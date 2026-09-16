<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Enums\MenuItemType;
use App\Filament\Support\LocaleTabs;
use App\Models\MenuItem;
use App\Models\PostCategory;
use App\Support\Localization\Locales;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Menü öğeleri';

    protected static ?string $modelLabel = 'menü öğesi';

    protected static ?string $pluralModelLabel = 'menü öğeleri';

    /**
     * Named routes an item may point at, keyed by the part after the locale.
     *
     * @return array<string, string>
     */
    public static function routeOptions(): array
    {
        return [
            'home' => 'Anasayfa',
            'pain-types.index' => 'Ağrı türleri listesi',
            'procedures.index' => 'Girişimsel tedaviler listesi',
            'blog.index' => 'Blog listesi',
            'videos' => 'Video galeri',
        ];
    }

    public static function typeFrom(mixed $state): ?MenuItemType
    {
        if ($state instanceof MenuItemType) {
            return $state;
        }

        return blank($state) ? null : MenuItemType::tryFrom((string) $state);
    }

    /**
     * Records of the model a given item type links to.
     *
     * @return array<int, string>
     */
    public static function linkableOptions(mixed $type): array
    {
        $model = static::typeFrom($type)?->modelClass();

        if (! $model) {
            return [];
        }

        $titleField = $model === PostCategory::class ? 'name' : 'title';

        return $model::query()
            ->get()
            ->mapWithKeys(fn (Model $record): array => [
                $record->getKey() => $record->localized($titleField, 'tr') ?? "#{$record->getKey()}",
            ])
            ->all();
    }

    public function form(Schema $schema): Schema
    {
        $default = Locales::default();
        $menuId = $this->getOwnerRecord()->getKey();

        return $schema
            ->components([
                Section::make('Bağlantı')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Bağlantı türü')
                            ->options(MenuItemType::class)
                            ->default(MenuItemType::Page->value)
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                $set('linkable_type', static::typeFrom($state)?->modelClass());
                                $set('linkable_id', null);
                            }),
                        Select::make('linkable_id')
                            ->label('Bağlanacak kayıt')
                            ->options(fn (Get $get): array => static::linkableOptions($get('type')))
                            ->searchable()
                            ->required(fn (Get $get): bool => static::typeFrom($get('type'))?->modelClass() !== null)
                            ->visible(fn (Get $get): bool => static::typeFrom($get('type'))?->modelClass() !== null)
                            ->dehydratedWhenHidden()
                            ->dehydrateStateUsing(fn (Get $get, mixed $state): mixed => static::typeFrom($get('type'))?->modelClass() ? $state : null),
                        Hidden::make('linkable_type')
                            ->dehydrateStateUsing(fn (Get $get): ?string => static::typeFrom($get('type'))?->modelClass()),
                        Select::make('route_name')
                            ->label('Site bölümü')
                            ->options(static::routeOptions())
                            ->native(false)
                            ->required(fn (Get $get): bool => static::typeFrom($get('type')) === MenuItemType::Route)
                            ->visible(fn (Get $get): bool => static::typeFrom($get('type')) === MenuItemType::Route)
                            ->dehydratedWhenHidden()
                            ->dehydrateStateUsing(fn (Get $get, mixed $state): mixed => static::typeFrom($get('type')) === MenuItemType::Route ? $state : null),
                        LocaleTabs::make(fn (string $locale): array => [
                            TextInput::make("url.{$locale}")
                                ->label('Bağlantı adresi')
                                ->url()
                                ->maxLength(255)
                                ->required(fn (Get $get): bool => $locale === $default && static::typeFrom($get('type')) === MenuItemType::Url)
                                ->dehydratedWhenHidden()
                                ->dehydrateStateUsing(fn (Get $get, mixed $state): mixed => static::typeFrom($get('type')) === MenuItemType::Url ? $state : null),
                        ], 'Dış bağlantı')
                            ->visible(fn (Get $get): bool => static::typeFrom($get('type')) === MenuItemType::Url),
                    ]),

                Section::make('Görünüm')
                    ->columns(2)
                    ->schema([
                        LocaleTabs::make(fn (string $locale): array => [
                            TextInput::make("label.{$locale}")
                                ->label('Menüde görünen ad')
                                ->maxLength(255)
                                ->required($locale === $default),
                        ], 'Menü adı'),
                        Select::make('parent_id')
                            ->label('Üst öğe')
                            ->options(fn (?MenuItem $record): array => $this->parentOptions($menuId, $record))
                            ->searchable()
                            ->placeholder('Üst seviye'),
                        Select::make('target')
                            ->label('Açılış şekli')
                            ->options([
                                '_self' => 'Aynı sekmede',
                                '_blank' => 'Yeni sekmede',
                            ])
                            ->default('_self')
                            ->native(false)
                            ->required(),
                        TextInput::make('sort_order')
                            ->label('Sıra')
                            ->integer()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_visible')
                            ->label('Menüde göster')
                            ->default(true),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('label')
                    ->label('Ad')
                    ->state(fn (MenuItem $record): string => ($record->parent_id ? '— ' : '').($record->localized('label', 'tr') ?? '')),
                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('parent_id')
                    ->label('Üst öğe')
                    ->state(fn (MenuItem $record): ?string => $record->parent?->localized('label', 'tr'))
                    ->placeholder('Üst seviye'),
                IconColumn::make('is_visible')
                    ->label('Görünür')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tür')
                    ->options(MenuItemType::class),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Menü öğesi ekle'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Items of the same menu that may become a parent: top level items only,
     * and never the record being edited.
     *
     * @return array<int, string>
     */
    private function parentOptions(int $menuId, ?MenuItem $record): array
    {
        return MenuItem::query()
            ->where('menu_id', $menuId)
            ->whereNull('parent_id')
            ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (MenuItem $item): array => [
                $item->getKey() => $item->localized('label', 'tr') ?? "#{$item->getKey()}",
            ])
            ->all();
    }
}
