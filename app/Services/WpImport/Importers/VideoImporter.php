<?php

namespace App\Services\WpImport\Importers;

use App\Enums\VideoCategory;
use App\Models\Video;
use App\Services\WpImport\Elementor\ElementorNode;
use App\Services\WpImport\Support\TurkishText;
use App\Services\WpImport\Support\YouTubeUrl;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * The video gallery.
 *
 * Every film lived in the `video-playlist` widget of the old gallery page,
 * with the doctor's name in front of each title and a few entries listed
 * twice. Thumbnails were served straight from YouTube and are pulled down so
 * the new site does not depend on them.
 */
class VideoImporter extends Importer
{
    public static function key(): string
    {
        return 'videos';
    }

    public function import(): void
    {
        $entries = $this->entries();

        if ($entries === []) {
            $this->context->report->warn(self::key(), 'Video listesi bulunamadı.');

            return;
        }

        $order = 0;

        foreach ($entries as $entry) {
            $this->importOne($entry, ++$order);
        }

        $this->context->rememberVideos($this->index());
    }

    /**
     * YouTube id => record id, so the pages that embed a film can point at the
     * gallery entry instead of repeating the link.
     *
     * @return array<string, int>
     */
    public function index(): array
    {
        return Video::query()
            ->pluck('id', 'youtube_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    public function fresh(): void
    {
        if ($this->context->dryRun) {
            return;
        }

        Video::query()->each(fn (Video $video) => $video->delete());
    }

    /**
     * The playlist, with the duplicate YouTube ids folded together.
     *
     * @return array<string, array{youtube_id: string, title: string, raw_title: string, duration: string|null}>
     */
    public function entries(): array
    {
        $document = $this->context->document((int) config('wp-import.videos.source_page'));
        $entries = [];
        $duplicates = 0;

        foreach ($document->widgets() as $widget) {
            if (! $widget->is('video-playlist')) {
                continue;
            }

            foreach ($this->tabsOf($widget) as $tab) {
                $id = YouTubeUrl::id((string) ($tab['youtube_url'] ?? ''));
                $rawTitle = TurkishText::normalize(strip_tags((string) ($tab['title'] ?? '')));

                if ($id === null || $rawTitle === '') {
                    continue;
                }

                if (isset($entries[$id])) {
                    $duplicates++;

                    continue;
                }

                $entries[$id] = [
                    'youtube_id' => $id,
                    'title' => $this->context->titles->clean($rawTitle),
                    'raw_title' => $rawTitle,
                    'duration' => filled($tab['duration'] ?? null) ? (string) $tab['duration'] : null,
                ];
            }
        }

        if ($duplicates > 0) {
            $this->context->report->note(self::key(), "Aynı videonun {$duplicates} tekrarı atlandı.");
            $this->context->report->skipped('videos', $duplicates);
        }

        return $entries;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function tabsOf(ElementorNode $widget): array
    {
        return $widget->rows('tabs');
    }

    /**
     * @param  array{youtube_id: string, title: string, raw_title: string, duration: string|null}  $entry
     */
    private function importOne(array $entry, int $order): void
    {
        $category = $this->categoryFor($entry['raw_title']);

        $attributes = [
            'title' => ['tr' => $entry['title']],
            'category' => $category,
            'channel' => $this->channelFor($entry['raw_title']),
            'duration' => $entry['duration'],
            'is_visible' => true,
            'is_featured' => $order === 1,
            'sort_order' => $order,
        ];

        if ($this->context->dryRun) {
            $this->context->report->created('videos');

            return;
        }

        $video = Video::query()->firstOrNew(['youtube_id' => $entry['youtube_id']]);
        $exists = $video->exists;

        /* Keep any title an editor has already corrected. */
        if ($exists && filled($video->title)) {
            unset($attributes['title']);
        }

        $video->fill($attributes)->save();

        $this->attachThumbnail($video);

        $exists ? $this->context->report->updated('videos') : $this->context->report->created('videos');
    }

    /**
     * A title naming a channel or a live broadcast came from television.
     */
    private function categoryFor(string $title): VideoCategory
    {
        $haystack = TurkishText::lower($title);

        foreach ((array) config('wp-import.videos.tv_keywords', []) as $keyword) {
            if (str_contains($haystack, TurkishText::lower((string) $keyword))) {
                return VideoCategory::Tv;
            }
        }

        return VideoCategory::Info;
    }

    /**
     * The broadcaster, when the title names one.
     */
    private function channelFor(string $title): ?string
    {
        $haystack = TurkishText::lower($title);

        foreach (['tv100' => 'TV100', 'ntv' => 'NTV', 'beyaz' => 'Beyaz TV', 'konuştukça' => 'Konuştukça'] as $needle => $channel) {
            if (str_contains($haystack, $needle)) {
                return $channel;
            }
        }

        return null;
    }

    /**
     * Pulls the poster frame down from YouTube. `maxresdefault` does not exist
     * for every film, so `hqdefault` is the fallback.
     */
    private function attachThumbnail(Video $video): void
    {
        if ($video->getFirstMedia('thumbnail')) {
            return;
        }

        $timeout = (int) config('wp-import.videos.thumbnail_timeout', 15);

        foreach ((array) config('wp-import.videos.thumbnail_urls', []) as $template) {
            $url = str_replace(':id', $video->youtube_id, (string) $template);
            $file = $this->download($url, $timeout);

            if ($file === null) {
                continue;
            }

            $this->context->media->attachFile($video, $file, 'thumbnail', $video->localized('title') ?: $video->youtube_id);
            @unlink($file);

            return;
        }

        $this->context->report->warn(self::key(), "Kapak görseli indirilemedi: {$video->youtube_id}");
    }

    private function download(string $url, int $timeout): ?string
    {
        try {
            $response = Http::timeout($timeout)->get($url);
        } catch (Throwable) {
            return null;
        }

        /* YouTube answers a missing size with a 120x90 placeholder, not a 404. */
        if (! $response->successful() || strlen($response->body()) < 6000) {
            return null;
        }

        $file = tempnam(sys_get_temp_dir(), 'yt').'.jpg';
        file_put_contents($file, $response->body());

        return $file;
    }
}
