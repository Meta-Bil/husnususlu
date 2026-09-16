<?php

namespace App\Services\WpImport\Support;

/**
 * Video titles on the old site carried the doctor's name, a channel separator
 * and a hashtag, and were usually shouted. This puts them back into shape.
 */
class TitleCleaner
{
    /** Acronyms and brand names that survive sentence casing. */
    private const KEEP_AS_IS = [
        'NTV', 'TV', 'TV100', 'MR', 'BT', 'RF', 'EMG', 'PRP', 'TENS', 'COVID',
        'COVID-19', 'HIV', 'ABD', 'AB', 'I', 'II', 'III', 'IV', 'SGK', 'USG',
    ];

    /**
     * @param  array<int, string>  $prefixes  Speaker names to strip.
     * @param  array<int, string>  $noise  Hashtags and other litter.
     * @param  array<string, string>  $typos
     */
    public function __construct(
        private readonly array $prefixes = [],
        private readonly array $noise = [],
        private readonly array $typos = [],
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (array) config('wp-import.title_prefixes', []),
            (array) config('wp-import.title_noise', []),
            (array) config('wp-import.typos', []),
        );
    }

    public function clean(string $title): string
    {
        $original = TurkishText::normalize(strip_tags($title));
        $cleaned = $this->removeNoise($original);
        $cleaned = $this->removeSpeaker($cleaned);
        $cleaned = $this->fixTypos($cleaned);
        $cleaned = TurkishText::sentenceCase($cleaned, self::KEEP_AS_IS);
        $cleaned = $this->tidy($cleaned);

        /* A few films were titled with nothing but the doctor's name. Stripping
           it would leave them nameless, so they keep the name they had. */
        return $cleaned !== '' ? $cleaned : $this->tidy($this->fixTypos($original));
    }

    /**
     * Applies the typo table on its own, for titles that need no other work.
     */
    public function fixTypos(string $text): string
    {
        return strtr($text, $this->typos);
    }

    private function removeNoise(string $title): string
    {
        foreach ($this->noise as $noise) {
            $title = str_ireplace($noise, '', $title);
        }

        return $title;
    }

    /**
     * Removes the doctor's name wherever it sits, with the separator that
     * followed or preceded it.
     */
    private function removeSpeaker(string $title): string
    {
        foreach ($this->prefixes as $prefix) {
            $quoted = preg_quote($prefix, '/');
            $title = preg_replace('/^\s*'.$quoted.'\s*[-–—|I:•]*\s*/iu', '', $title) ?? $title;
            $title = preg_replace('/\s*[-–—|:•]+\s*'.$quoted.'\s*$/iu', '', $title) ?? $title;
            $title = preg_replace('/\s+'.$quoted.'\s*$/iu', '', $title) ?? $title;
        }

        return $title;
    }

    private function tidy(string $title): string
    {
        $title = preg_replace('/\s+/u', ' ', $title) ?? $title;
        $title = trim($title, " \t\n\r\0\x0B-–—|:•");

        return TurkishText::ucfirst(trim($title));
    }
}
