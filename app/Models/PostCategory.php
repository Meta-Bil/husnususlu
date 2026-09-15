<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\HasLocalizedSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostCategory extends Model
{
    use HasFactory;
    use HasLocalizedAttributes;
    use HasLocalizedSlug;

    protected $guarded = [];

    /** @var array<int, string> */
    protected array $localized = ['name', 'slug', 'description'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'slug' => 'array',
            'description' => 'array',
        ];
    }
}
