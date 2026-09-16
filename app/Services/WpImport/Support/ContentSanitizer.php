<?php

namespace App\Services\WpImport\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerAction;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Everything coming out of the old database is untrusted markup: Elementor
 * widgets hold hand-pasted HTML, inline styles, tracking scripts and JSON-LD.
 * Post bodies are reduced to the small set of elements the new site renders.
 */
class ContentSanitizer
{
    private readonly HtmlSanitizer $sanitizer;

    /**
     * @param  array<string, array<int, string>>  $elements  Tag => allowed attributes.
     * @param  array<int, string>  $droppedElements  Removed with their content.
     */
    public function __construct(array $elements, array $droppedElements = [], int $maxInputLength = 500_000)
    {
        /*
         * Block rather than drop by default: the old pages wrap their text in
         * `section` and `div`, and dropping those would take the article with
         * them. Blocking unwraps the tag and keeps what it held. Anything that
         * must go with its content is listed in `dropped_elements`.
         */
        $config = (new HtmlSanitizerConfig)
            ->defaultAction(HtmlSanitizerAction::Block)
            ->withMaxInputLength($maxInputLength)
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            ->allowMediaSchemes(['https', 'http', 'data'])
            ->allowRelativeLinks()
            ->allowRelativeMedias();

        foreach ($droppedElements as $element) {
            $config = $config->dropElement($element);
        }

        foreach ($elements as $element => $attributes) {
            $config = $config->allowElement($element, $attributes);
        }

        $this->sanitizer = new HtmlSanitizer($config);
    }

    public static function fromConfig(): self
    {
        return new self(
            (array) config('wp-import.sanitizer.elements', []),
            (array) config('wp-import.sanitizer.dropped_elements', []),
            (int) config('wp-import.sanitizer.max_input_length', 500_000),
        );
    }

    /**
     * Sanitized body HTML, with headings normalised and empty wrappers gone.
     */
    public function clean(string $html): string
    {
        $html = $this->stripCodeBlocks($html);
        $html = $this->demoteHeadings($html);
        $html = $this->sanitizer->sanitize($html);

        return $this->tidy($html);
    }

    /**
     * The plain text of a fragment, for excerpts and fingerprints.
     */
    public function text(string $html): string
    {
        $html = $this->stripCodeBlocks($html);
        $html = preg_replace('#<(br|/p|/li|/h[1-6]|/div)\s*/?>#i', ' ', $html) ?? $html;

        return TurkishText::normalize(strip_tags($html));
    }

    /**
     * Scripts and stylesheets go before parsing. The sanitizer would also drop
     * them, but an HTML parser hoists a stray `style` element out of the body
     * and its rules can then survive as text.
     */
    private function stripCodeBlocks(string $html): string
    {
        return preg_replace('#<\s*(script|style)\b[^>]*>.*?(?:<\s*/\s*\1\s*>|$)#is', '', $html) ?? $html;
    }

    /**
     * The new site owns the h1, so a body h1 becomes an h2 and everything
     * below it shifts down one level rather than being dropped outright.
     */
    private function demoteHeadings(string $html): string
    {
        if (! preg_match('#<h1\b#i', $html)) {
            return $html;
        }

        foreach ([5 => 6, 4 => 5, 3 => 4, 2 => 3, 1 => 2] as $from => $to) {
            $html = preg_replace('#<(/?)h'.$from.'\b([^>]*)>#i', '<$1h'.$to.'$2>', $html) ?? $html;
        }

        return $html;
    }

    private function tidy(string $html): string
    {
        /* h5/h6 have no styling of their own; fold them into h4. */
        $html = preg_replace('#<(/?)h[56]\b[^>]*>#i', '<$1h4>', $html) ?? $html;
        $html = preg_replace('#<p>\s*(?:&nbsp;|\x{00a0}|\s)*</p>#iu', '', $html) ?? $html;
        $html = preg_replace('#<(h[2-4]|li|td|th)>\s*</\1>#i', '', $html) ?? $html;
        $html = preg_replace('#\s{2,}#', ' ', $html) ?? $html;

        return trim($html);
    }
}
