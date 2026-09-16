<?php

namespace App\Services\WpImport\Support;

/**
 * The playlist on the old video page mixes `watch?v=`, `shorts/` and `youtu.be`
 * links, some with tracking parameters. All of them carry the same id.
 */
class YouTubeUrl
{
    private const PATTERNS = [
        '#[?&]v=([A-Za-z0-9_-]{6,20})#',
        '#youtu\.be/([A-Za-z0-9_-]{6,20})#i',
        '#/shorts/([A-Za-z0-9_-]{6,20})#i',
        '#/embed/([A-Za-z0-9_-]{6,20})#i',
        '#/live/([A-Za-z0-9_-]{6,20})#i',
    ];

    public static function id(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        foreach (self::PATTERNS as $pattern) {
            if (preg_match($pattern, $url, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Seconds behind a `1:38` or `31:59` duration, used to sort and to spot
     * full length programmes.
     */
    public static function seconds(?string $duration): ?int
    {
        if (blank($duration) || preg_match('/^\s*(?:(\d+):)?(\d{1,2}):(\d{2})\s*$/', $duration, $m) !== 1) {
            return null;
        }

        return ((int) ($m[1] ?: 0)) * 3600 + ((int) $m[2]) * 60 + (int) $m[3];
    }
}
