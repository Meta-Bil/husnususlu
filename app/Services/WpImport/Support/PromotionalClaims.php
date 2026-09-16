<?php

namespace App\Services\WpImport\Support;

/**
 * Turkish health advertising rules forbid guarantees, superlatives and success
 * rates. The old site is full of them, so anything matching is taken out of
 * the text and reported, never silently kept.
 */
class PromotionalClaims
{
    /** @var array<int, string> */
    private array $removed = [];

    /**
     * @param  array<int, string>  $patterns
     */
    public function __construct(private readonly array $patterns = []) {}

    public static function fromConfig(): self
    {
        return new self((array) config('wp-import.promotional_claims', []));
    }

    /**
     * Whether a whole fragment is nothing but a promotional claim, in which
     * case the caller should drop it rather than leave an empty bullet.
     */
    public function rejects(string $text): bool
    {
        $stripped = TurkishText::normalize(strip_tags($text));

        if ($stripped === '') {
            return false;
        }

        $lowered = TurkishText::lower($stripped);

        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $lowered) !== 1) {
                continue;
            }

            $remainder = TurkishText::normalize(preg_replace($pattern, '', $lowered) ?? '');

            /* Only a few stray words left: the claim was the whole point. */
            if (mb_strlen($remainder, 'UTF-8') <= max(12, (int) (mb_strlen($stripped, 'UTF-8') * 0.4))) {
                $this->remember($stripped);

                return true;
            }
        }

        return false;
    }

    /**
     * Removes the offending sentences from a longer text.
     */
    public function scrub(string $text): string
    {
        foreach ($this->patterns as $pattern) {
            if (! $this->matches($pattern, $text)) {
                continue;
            }

            $text = $this->removeSentences($text, $pattern);
        }

        return $text;
    }

    /**
     * Turkish has to be lower-cased before matching: PCRE's caseless mode does
     * not fold the dotless `ı` onto `I`, so `%98 BAŞARI` would slip past a
     * pattern written in lower case.
     */
    private function matches(string $pattern, string $text): bool
    {
        return preg_match($pattern, TurkishText::lower(strip_tags($text))) === 1;
    }

    /**
     * @return array<int, string>
     */
    public function removed(): array
    {
        return array_values(array_unique($this->removed));
    }

    public function flush(): void
    {
        $this->removed = [];
    }

    /**
     * Drops the sentence or list item a claim sits in, so the text still reads.
     */
    private function removeSentences(string $text, string $pattern): string
    {
        $parts = preg_split('/(<\/li>|<\/p>|<\/h[1-6]>|(?<=[.!?…])\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $result = '';

        for ($i = 0; $i < count($parts); $i += 2) {
            $sentence = $parts[$i];
            $delimiter = $parts[$i + 1] ?? '';

            if ($this->matches($pattern, TurkishText::normalize($sentence))) {
                $this->remember(TurkishText::normalize(strip_tags($sentence)));

                /* Keep the closing tag so the markup stays balanced. */
                $result .= str_starts_with($delimiter, '</') ? $this->openingTagsOf($sentence).$delimiter : '';

                continue;
            }

            $result .= $sentence.$delimiter;
        }

        return $result;
    }

    private function openingTagsOf(string $fragment): string
    {
        preg_match_all('#<(?!/)[a-z][^>]*>#i', $fragment, $matches);

        return implode('', $matches[0]);
    }

    private function remember(string $claim): void
    {
        if ($claim !== '') {
            $this->removed[] = mb_substr($claim, 0, 160, 'UTF-8');
        }
    }
}
