<?php

namespace App\Enums;

use App\Models\Page;
use App\Models\PostCategory;
use App\Models\Treatment;
use Filament\Support\Contracts\HasLabel;

enum MenuItemType: string implements HasLabel
{
    case Page = 'page';
    case Treatment = 'treatment';
    case PostCategory = 'post_category';
    case Route = 'route';
    case Url = 'url';

    public function getLabel(): string
    {
        return match ($this) {
            self::Page => 'Sayfa',
            self::Treatment => 'Tedavi / ağrı türü',
            self::PostCategory => 'Blog kategorisi',
            self::Route => 'Site bölümü',
            self::Url => 'Dış bağlantı',
        };
    }

    /**
     * @return class-string|null
     */
    public function modelClass(): ?string
    {
        return match ($this) {
            self::Page => Page::class,
            self::Treatment => Treatment::class,
            self::PostCategory => PostCategory::class,
            default => null,
        };
    }
}
