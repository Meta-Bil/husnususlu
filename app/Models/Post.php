<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\HasLocalizedSlug;
use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use HasLocalizedAttributes;
    use HasLocalizedSlug;
    use HasPublishing;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $guarded = [];

    /** @var array<int, string> */
    protected array $localized = ['title', 'slug', 'excerpt', 'body', 'seo_title', 'seo_description'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    /**
     * Reading time in minutes, calculated from the body of the current locale.
     */
    public function readingTime(?string $locale = null): int
    {
        if ($this->reading_time) {
            return $this->reading_time;
        }

        $words = str_word_count(strip_tags((string) $this->localized('body', $locale)));

        return max(1, (int) ceil($words / 200));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('og_image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')->width(900)->format('webp')->nonQueued();
        $this->addMediaConversion('hero')->width(1600)->format('webp')->nonQueued();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'array',
            'slug' => 'array',
            'excerpt' => 'array',
            'body' => 'array',
            'faq' => 'array',
            'seo_title' => 'array',
            'seo_description' => 'array',
            'locales_enabled' => 'array',
            'status' => ContentStatus::class,
            'noindex' => 'boolean',
            'needs_review' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
