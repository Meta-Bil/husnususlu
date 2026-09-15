<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\TreatmentKind;
use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\HasLocalizedSlug;
use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Treatment extends Model implements HasMedia
{
    use HasFactory;
    use HasLocalizedAttributes;
    use HasLocalizedSlug;
    use HasPublishing;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $guarded = [];

    /** @var array<int, string> */
    protected array $localized = ['title', 'slug', 'summary', 'seo_title', 'seo_description'];

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    #[Scope]
    protected function painTypes(Builder $query): Builder
    {
        return $query->where('kind', TreatmentKind::PainType);
    }

    #[Scope]
    protected function procedures(Builder $query): Builder
    {
        return $query->where('kind', TreatmentKind::Procedure);
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
            'summary' => 'array',
            'blocks' => 'array',
            'seo_title' => 'array',
            'seo_description' => 'array',
            'locales_enabled' => 'array',
            'kind' => TreatmentKind::class,
            'status' => ContentStatus::class,
            'noindex' => 'boolean',
            'is_featured' => 'boolean',
            'needs_review' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
