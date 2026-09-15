<?php

namespace App\Support\Http;

use App\Models\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Resolves a request that matched no route against the redirect table, which
 * carries every old WordPress URL.
 */
class RedirectResolver
{
    private const CACHE_KEY = 'redirects.map';

    public function resolve(Request $request): ?RedirectResponse
    {
        $path = $this->normalize($request->getPathInfo());
        $target = $this->map()[$path] ?? null;

        if (! $target) {
            return null;
        }

        $this->recordHit($path);

        $url = $target['to'];

        if ($query = $request->getQueryString()) {
            $url .= (str_contains($url, '?') ? '&' : '?').$query;
        }

        return redirect()->to($url, $target['code']);
    }

    /**
     * @return array<string, array{to: string, code: int}>
     */
    public function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            return Redirect::query()
                ->where('is_active', true)
                ->get(['from_path', 'to_path', 'status_code'])
                ->mapWithKeys(fn (Redirect $redirect): array => [
                    $this->normalize($redirect->from_path) => [
                        'to' => $redirect->to_path,
                        'code' => $redirect->status_code,
                    ],
                ])
                ->all();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function recordHit(string $path): void
    {
        Redirect::query()
            ->where('from_path', $path)
            ->orWhere('from_path', $path.'/')
            ->update([
                'hits' => DB::raw('hits + 1'),
                'last_hit_at' => now(),
            ]);
    }

    /**
     * Lower-cased, without a trailing slash, always starting with one.
     */
    private function normalize(string $path): string
    {
        $path = '/'.trim(rawurldecode($path), '/');

        return mb_strtolower($path);
    }
}
