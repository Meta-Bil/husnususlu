<?php

namespace App\Filament\Resources\PostCategories\Schemas;

use App\Filament\Support\LocaleTabs;
use App\Support\Localization\Locales;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $default = Locales::default();

        return $schema
            ->components([
                Section::make('Kategori')
                    ->columnSpanFull()
                    ->schema([
                        LocaleTabs::make(fn (string $locale): array => [
                            TextInput::make("name.{$locale}")
                                ->label('Ad')
                                ->maxLength(255)
                                ->required($locale === $default)
                                ->live(onBlur: $locale === $default)
                                ->afterStateUpdated(function (string $operation, ?string $state, Set $set) use ($locale, $default): void {
                                    if ($locale !== $default || $operation !== 'create' || blank($state)) {
                                        return;
                                    }

                                    $set("slug.{$default}", Str::slug($state));
                                }),
                            TextInput::make("slug.{$locale}")
                                ->label('URL adı')
                                ->helperText('Adres çubuğunda görünen kısım. Türkçe addan otomatik önerilir.')
                                ->maxLength(255)
                                ->required($locale === $default),
                            Textarea::make("description.{$locale}")
                                ->label('Açıklama')
                                ->rows(3),
                        ]),
                        TextInput::make('sort_order')
                            ->label('Sıra')
                            ->integer()
                            ->default(0)
                            ->required(),
                    ]),
            ]);
    }
}
