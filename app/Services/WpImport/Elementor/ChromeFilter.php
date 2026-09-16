<?php

namespace App\Services\WpImport\Elementor;

use App\Services\WpImport\Support\TurkishText;

/**
 * Elementor had no shared header or footer, so every page carries its own copy
 * of the logo, the menu, the contact rail and the disclaimer. This decides
 * what is furniture and what is content.
 *
 * Two rules do the work: a list of widget types that are never content, and a
 * count of how many pages a given piece of text appears on — anything repeated
 * across the site was part of the template.
 */
class ChromeFilter
{
    /** @var array<string, int> Fingerprint => number of documents it appears on. */
    private array $counts = [];

    /**
     * @param  array<int, string>  $chromeWidgets
     * @param  array<int, string>  $fingerprintWidgets
     * @param  array<int, string>  $contactLinkPatterns
     */
    public function __construct(
        private readonly array $chromeWidgets = [],
        private readonly array $fingerprintWidgets = [],
        private readonly array $contactLinkPatterns = [],
        private readonly int $threshold = 4,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (array) config('wp-import.chrome.widgets', []),
            (array) config('wp-import.chrome.fingerprint_widgets', []),
            (array) config('wp-import.chrome.contact_link_patterns', []),
            (int) config('wp-import.chrome.fingerprint_threshold', 4),
        );
    }

    /**
     * Counts how often each piece of text appears across the whole site. Must
     * run before `isChrome()` for the repetition rule to have anything to say.
     *
     * @param  iterable<ElementorDocument>  $documents
     */
    public function learn(iterable $documents): self
    {
        foreach ($documents as $document) {
            $seen = [];

            foreach ($document->widgets() as $widget) {
                $fingerprint = $widget->fingerprint();

                if ($fingerprint === null || isset($seen[$fingerprint])) {
                    continue;
                }

                $seen[$fingerprint] = true;
                $this->counts[$fingerprint] = ($this->counts[$fingerprint] ?? 0) + 1;
            }
        }

        return $this;
    }

    public function isChrome(ElementorNode $widget): bool
    {
        return $this->reasonFor($widget) !== null;
    }

    /**
     * Why a widget was dropped, in a form that can go into `import_notes`.
     */
    public function reasonFor(ElementorNode $widget): ?string
    {
        if (! $widget->isWidget()) {
            return null;
        }

        if (in_array((string) $widget->widgetType, $this->chromeWidgets, true)) {
            return 'sitenin her sayfasında tekrar eden öğe';
        }

        if ($widget->is('icon-list') && $this->isContactRail($widget)) {
            return 'iletişim / sosyal medya bağlantı şeridi';
        }

        if ($this->isScriptOnly($widget)) {
            return 'script veya stil bloğu';
        }

        if (! in_array((string) $widget->widgetType, $this->fingerprintWidgets, true)) {
            return null;
        }

        $fingerprint = $widget->fingerprint();

        if ($fingerprint !== null && ($this->counts[$fingerprint] ?? 0) >= $this->threshold) {
            return 'aynı metin '.$this->counts[$fingerprint].' sayfada tekrar ediyor';
        }

        return null;
    }

    /**
     * How many documents a text appeared on, for tests and diagnostics.
     */
    public function occurrences(ElementorNode $widget): int
    {
        $fingerprint = $widget->fingerprint();

        return $fingerprint === null ? 0 : ($this->counts[$fingerprint] ?? 0);
    }

    /**
     * A list of phone numbers, addresses and social profiles rather than of
     * content: every item either links out or carries no text at all.
     */
    private function isContactRail(ElementorNode $widget): bool
    {
        $items = $widget->rows('icon_list');

        if ($items === []) {
            return true;
        }

        foreach ($items as $item) {
            $text = TurkishText::normalize(strip_tags((string) ($item['text'] ?? '')));
            $url = TurkishText::lower((string) ($item['link']['url'] ?? ''));

            if ($text === '') {
                continue;
            }

            foreach ($this->contactLinkPatterns as $pattern) {
                if (str_contains($url, $pattern)) {
                    continue 2;
                }
            }

            if (preg_match('/^[\s+()\d\-.\/]+$/u', $text) === 1) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * An HTML widget holding only structured data or styling, which the new
     * site generates for itself.
     */
    private function isScriptOnly(ElementorNode $widget): bool
    {
        if (! $widget->is('html')) {
            return false;
        }

        $html = (string) $widget->stringSetting('html');
        $stripped = preg_replace('#<\s*(script|style)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? $html;

        return TurkishText::normalize(strip_tags($stripped)) === '';
    }
}
