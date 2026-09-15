<?php

namespace App\Models;

use App\Enums\VideoCategory;
use App\Models\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Video extends Model implements HasMedia
{
    use HasFactory;
    use HasLocalizedAttributes;
    use InteractsWithMedia;

    protected $guarded = [];

    /** @var array<int, string> */
    protected array $localized = ['title', 'description', 'program'];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function watchUrl(): string
    {
        return "https://www.youtube.com/watch?v={$this->youtube_id}";
    }

    /**
     * The imported thumbnail, falling back to YouTube's own image.
     */
    public function thumbnailUrl(string $conversion = 'card'): string
    {
        $media = $this->getFirstMedia('thumbnail');

        if ($media) {
            return $media->hasGeneratedConversion($conversion)
                ? $media->getUrl($conversion)
                : $media->getUrl();
        }

        return "https://i.ytimg.com/vi/{$this->youtube_id}/maxresdefault.jpg";
    }

    #[Scope]
    protected function visible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')->width(640)->format('webp')->nonQueued();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'array',
            'description' => 'array',
            'program' => 'array',
            'category' => VideoCategory::class,
            'published_on' => 'date',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }
}
