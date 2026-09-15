<?php

namespace App\Models\Concerns;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasPublishing
{
    public function isPublished(): bool
    {
        return $this->status === ContentStatus::Published
            && (is_null($this->published_at) || $this->published_at->isPast());
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::Published)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Published and available in the given locale — what a visitor may open.
     */
    #[Scope]
    protected function live(Builder $query, ?string $locale = null): Builder
    {
        return $query->published()->enabledIn($locale);
    }
}
