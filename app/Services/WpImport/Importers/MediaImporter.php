<?php

namespace App\Services\WpImport\Importers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Treatment;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

/**
 * Copies the images WordPress registered into the media library of the record
 * that uses them.
 *
 * The content steps attach covers as they go, so this step exists to re-run
 * the copy on its own — after a failed run, or once the uploads folder is in
 * place. Only the originals listed in `_wp_attached_file` are copied; the
 * `-300x200` variants WordPress generated, the plugin folders and anything
 * executable are left where they are.
 */
class MediaImporter extends Importer
{
    public static function key(): string
    {
        return 'media';
    }

    public function import(): void
    {
        $inventory = $this->context->media->inventory();

        $this->context->info(
            "{$inventory['available']} / {$inventory['total']} yüklenmiş görsel diskte bulundu.",
        );

        if ($inventory['available'] === 0) {
            $this->context->report->warn(
                self::key(),
                'Hiçbir görsel bulunamadı; WP_UPLOADS_PATH doğru mu? ('.config('wp-import.uploads_path').')',
            );

            return;
        }

        foreach ([['page', Page::class], ['treatment', Treatment::class], ['post', Post::class]] as [$wpType, $model]) {
            $this->attachCovers($wpType, $model);
        }

        foreach ($this->context->media->failures() as $failure) {
            $this->context->report->warn(self::key(), $failure);
        }
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function attachCovers(string $wpType, string $model): void
    {
        foreach ($this->context->mappedIds($wpType, $model) as $wpId => $recordId) {
            $record = $model::query()->find($recordId);

            if (! $record instanceof HasMedia || $record->getFirstMedia('cover')) {
                continue;
            }

            foreach ($this->candidates((int) $wpId) as $attachmentId) {
                if ($this->context->media->attach($record, $attachmentId, 'cover')) {
                    break;
                }
            }
        }
    }

    /**
     * The featured image first, then whatever images the page itself used.
     *
     * @return array<int, int>
     */
    private function candidates(int $wpId): array
    {
        $ids = [];

        if ($thumbnail = $this->context->wp->metaValue($wpId, '_thumbnail_id')) {
            $ids[] = (int) $thumbnail;
        }

        foreach ($this->context->document($wpId)->widgets() as $widget) {
            if ($id = $widget->imageId()) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }
}
