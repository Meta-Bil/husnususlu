<?php

namespace App\Services\WpImport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Throwable;

/**
 * Copies the images WordPress registered into the media library of the record
 * that uses them.
 *
 * Only the originals listed in `_wp_attached_file` are copied: WordPress also
 * wrote a `-300x200` variant of every upload, and the plugin folders hold
 * cached and generated files that are not content.
 */
class MediaLibrary
{
    /** Upload folders that hold generated or third-party files, not content. */
    private const SKIPPED_FOLDERS = ['elementor', 'complianz', 'cache', 'et_temp', 'wpforms', 'litespeed'];

    /** @var array<int, array{file: string, alt: string|null, mime: string}> */
    private array $attachments;

    /** @var array<string, int> Relative upload path => attachment id. */
    private array $byPath;

    private int $copied = 0;

    private int $skipped = 0;

    /** @var array<int, string> */
    private array $failures = [];

    public function __construct(
        private readonly WpDatabase $wp,
        private readonly string $uploadsPath,
        private readonly bool $dryRun = false,
    ) {
        $this->attachments = $this->wp->attachments();
        $this->byPath = [];

        foreach ($this->attachments as $id => $attachment) {
            $this->byPath[strtolower($attachment['file'])] = $id;
        }
    }

    public static function make(WpDatabase $wp, bool $dryRun = false): self
    {
        return new self($wp, (string) config('wp-import.uploads_path'), $dryRun);
    }

    public function copied(): int
    {
        return $this->copied;
    }

    public function skipped(): int
    {
        return $this->skipped;
    }

    /**
     * @return array<int, string>
     */
    public function failures(): array
    {
        return $this->failures;
    }

    /**
     * Attaches an attachment to a record's collection, replacing whatever was
     * there so a re-run does not pile up copies.
     */
    public function attach(Model&HasMedia $model, int $attachmentId, string $collection = 'cover'): bool
    {
        $path = $this->pathFor($attachmentId);

        if ($path === null) {
            return false;
        }

        if ($this->dryRun) {
            $this->copied++;

            return true;
        }

        /* Already the file this record carries: a re-run has nothing to do. */
        $current = $model->getFirstMedia($collection);

        if ($current && (int) $current->getCustomProperty('wp_attachment_id') === $attachmentId) {
            return true;
        }

        try {
            $model->clearMediaCollection($collection);
            $model->addMedia($path)
                ->preservingOriginal()
                ->withCustomProperties(['wp_attachment_id' => $attachmentId, 'alt' => $this->altFor($attachmentId)])
                ->usingName($this->altFor($attachmentId) ?: pathinfo($path, PATHINFO_FILENAME))
                ->toMediaCollection($collection);

            $this->copied++;

            return true;
        } catch (Throwable $exception) {
            $this->failures[] = "#{$attachmentId} ({$path}): ".$exception->getMessage();

            return false;
        }
    }

    /**
     * Attaches a file downloaded from a URL, used for the video thumbnails
     * that never lived in the WordPress uploads folder.
     */
    public function attachFile(Model&HasMedia $model, string $file, string $collection, ?string $name = null): bool
    {
        if ($this->dryRun) {
            $this->copied++;

            return true;
        }

        try {
            $model->clearMediaCollection($collection);
            $model->addMedia($file)
                ->usingName($name ?: pathinfo($file, PATHINFO_FILENAME))
                ->toMediaCollection($collection);

            $this->copied++;

            return true;
        } catch (Throwable $exception) {
            $this->failures[] = basename($file).': '.$exception->getMessage();

            return false;
        }
    }

    /**
     * Copies an upload onto the public disk for the blocks that store a plain
     * path rather than a media record, and returns that path.
     */
    public function copyToPublicDisk(int $attachmentId, string $directory): ?string
    {
        $source = $this->pathFor($attachmentId);

        if ($source === null) {
            return null;
        }

        $target = trim($directory, '/').'/'.basename($source);

        if ($this->dryRun) {
            return $target;
        }

        if (! Storage::disk('public')->exists($target)) {
            $contents = @file_get_contents($source);

            if ($contents === false) {
                $this->failures[] = "#{$attachmentId}: okunamadı";

                return null;
            }

            Storage::disk('public')->put($target, $contents);
            $this->copied++;
        }

        return $target;
    }

    /**
     * The absolute path of an attachment's original file, or null when it is
     * a generated variant, a plugin file or simply missing from disk.
     */
    public function pathFor(int $attachmentId): ?string
    {
        $attachment = $this->attachments[$attachmentId] ?? null;

        if ($attachment === null) {
            return null;
        }

        return $this->pathForFile($attachment['file']);
    }

    public function altFor(int $attachmentId): ?string
    {
        return $this->attachments[$attachmentId]['alt'] ?? null;
    }

    /**
     * Resolves an image URL from the old site back to its attachment id.
     */
    public function idForUrl(?string $url): ?int
    {
        if (blank($url)) {
            return null;
        }

        $relative = $this->relativeUploadPath($url);

        if ($relative === null) {
            return null;
        }

        /* Strip the `-1024x768` WordPress adds to every generated size. */
        $original = preg_replace('/-\d+x\d+(\.[A-Za-z0-9]+)$/', '$1', $relative) ?? $relative;

        return $this->byPath[strtolower($original)] ?? $this->byPath[strtolower($relative)] ?? null;
    }

    /**
     * How many of the registered uploads can actually be copied.
     *
     * @return array{total: int, available: int}
     */
    public function inventory(): array
    {
        $available = 0;

        foreach ($this->attachments as $attachment) {
            if ($this->pathForFile($attachment['file']) !== null) {
                $available++;
            }
        }

        return ['total' => count($this->attachments), 'available' => $available];
    }

    private function pathForFile(string $file): ?string
    {
        if (! $this->isImportable($file)) {
            $this->skipped++;

            return null;
        }

        $path = rtrim($this->uploadsPath, '\\/').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $file);

        return is_file($path) ? $path : null;
    }

    private function isImportable(string $file): bool
    {
        $folder = strtolower(explode('/', $file)[0] ?? '');

        if (in_array($folder, self::SKIPPED_FOLDERS, true)) {
            return false;
        }

        $name = basename($file);

        /* `photo-300x200.jpg` is a thumbnail WordPress generated. */
        if (preg_match('/-\d+x\d+\.[A-Za-z0-9]+$/', $name) === 1) {
            return false;
        }

        foreach (explode('.', strtolower($name)) as $extension) {
            if (in_array($extension, ['php', 'phtml', 'phar', 'html', 'htm', 'js'], true)) {
                return false;
            }
        }

        return true;
    }

    private function relativeUploadPath(string $url): ?string
    {
        $url = str_replace('\\/', '/', $url);

        if (preg_match('#/wp-content/uploads/(.+)$#i', $url, $matches) !== 1) {
            return null;
        }

        return ltrim(explode('?', $matches[1])[0], '/');
    }
}
