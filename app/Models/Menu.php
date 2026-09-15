<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    /**
     * Top level items with their children, ready to render.
     */
    public function rootItems(): HasMany
    {
        return $this->items()->whereNull('parent_id')->with(['children.linkable', 'linkable']);
    }
}
