<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\PageTemplate;
use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\HasLocalizedSlug;
use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Page extends Model implements HasMedia
{
    use HasFactory;
    use HasLocalizedAttributes;
    use HasLocalizedSlug;
    use HasPublishing;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $guarded = [];

    /** @var array<int, string> */
    protected array $localized = ['title', 'slug', 'excerpt', 'seo_title', 'seo_description'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
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
            'blocks' => 'array',
            'seo_title' => 'array',
            'seo_description' => 'array',
            'locales_enabled' => 'array',
            'status' => ContentStatus::class,
            'template' => PageTemplate::class,
            'noindex' => 'boolean',
            'needs_review' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
