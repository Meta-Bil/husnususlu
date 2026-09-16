<?php

namespace App\Services\WpImport\Support;

/**
 * Turkish needs its own case rules: `I` lowercases to `ı` and `i` uppercases
 * to `İ`, which `mb_strtolower()` gets wrong. Getting this right matters for
 * every shouty title and slug the import touches.
 */
class TurkishText
{
    private const LOWER_MAP = ['I' => 'ı', 'İ' => 'i', 'Ş' => 'ş', 'Ğ' => 'ğ', 'Ü' => 'ü', 'Ö' => 'ö', 'Ç' => 'ç'];

    private const UPPER_MAP = ['i' => 'İ', 'ı' => 'I', 'ş' => 'Ş', 'ğ' => 'Ğ', 'ü' => 'Ü', 'ö' => 'Ö', 'ç' => 'Ç'];

    private const SLUG_MAP = [
        'ı' => 'i', 'İ' => 'i', 'ş' => 's', 'Ş' => 's', 'ğ' => 'g', 'Ğ' => 'g',
        'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c',
        'â' => 'a', 'Â' => 'a', 'î' => 'i', 'Î' => 'i', 'û' => 'u', 'Û' => 'u',
    ];

    public static function lower(string $text): string
    {
        return mb_strtolower(strtr($text, self::LOWER_MAP), 'UTF-8');
    }

    public static function upper(string $text): string
    {
        return mb_strtoupper(strtr($text, self::UPPER_MAP), 'UTF-8');
    }

    public static function ucfirst(string $text): string
    {
        if ($text === '') {
            return $text;
        }

        return self::upper(mb_substr($text, 0, 1, 'UTF-8')).mb_substr($text, 1, null, 'UTF-8');
    }

    /**
     * A URL slug that keeps Turkish words readable: `Ağrı Türleri` becomes
     * `agri-turleri` rather than `ari-trleri`.
     */
    public static function slug(string $text): string
    {
        $text = self::lower(strtr($text, self::SLUG_MAP));
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? '';

        return trim($text, '-');
    }

    /**
     * Collapses whitespace, decodes entities and removes zero-width characters.
     */
    public static function normalize(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\u{00a0}", "\u{200b}", "\u{feff}"], [' ', '', ''], $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    /**
     * Whether a string is shouting, meaning it is worth recasing.
     */
    public static function isShouting(string $text): bool
    {
        $letters = preg_replace('/[^\p{L}]/u', '', $text) ?? '';

        if (mb_strlen($letters, 'UTF-8') < 4) {
            return false;
        }

        $upper = 0;

        foreach (preg_split('//u', $letters, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $character) {
            if (self::upper($character) === $character) {
                $upper++;
            }
        }

        return $upper / mb_strlen($letters, 'UTF-8') >= 0.7;
    }

    /**
     * Turns a shouted string into a sentence, leaving acronyms and numbers as
     * they are: `DOÇ. DR. I BACAK AGRILARI` becomes `Bacak ağrıları`.
     *
     * @param  array<int, string>  $keepAsIs  Words that must not be recased.
     */
    public static function sentenceCase(string $text, array $keepAsIs = []): string
    {
        if (! self::isShouting($text)) {
            return $text;
        }

        $keep = array_map(self::upper(...), $keepAsIs);

        $words = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $result = '';
        $isFirstWord = true;

        foreach ($words as $word) {
            if (trim($word) === '') {
                $result .= $word;

                continue;
            }

            $bare = preg_replace('/[^\p{L}\p{N}]/u', '', $word) ?? $word;

            if ($bare !== '' && in_array(self::upper($bare), $keep, true)) {
                $result .= $word;
                $isFirstWord = false;

                continue;
            }

            $lowered = self::lower($word);
            $result .= $isFirstWord ? self::ucfirst($lowered) : $lowered;
            $isFirstWord = false;
        }

        return $result;
    }
}
