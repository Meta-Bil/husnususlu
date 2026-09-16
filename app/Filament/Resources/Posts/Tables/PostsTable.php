<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Enums\ContentStatus;
use App\Models\Post;
use App\Models\PostCategory;
use App\Support\Localization\Locales;
use App\Support\Localization\LocaleUrls;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Görsel')
                    ->collection('cover')
                    ->height(40),
                TextColumn::make('title')
                    ->label('Başlık')
                    ->state(fn (Post $record): ?string => $record->localized('title', 'tr'))
                    ->description(fn (Post $record): ?string => $record->slugFor('tr'))
                    ->searchable(['title', 'slug_tr']),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->state(fn (Post $record): ?string => $record->category?->localized('name', 'tr'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge(),
                TextColumn::make('locales_enabled')
                    ->label('Diller')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => Locales::short($state)),
                TextColumn::make('published_at')
                    ->label('Yayın tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(ContentStatus::class),
                SelectFilter::make('post_category_id')
                    ->label('Kategori')
                    ->options(fn (): array => PostCategory::query()
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (PostCategory $category): array => [
                            $category->getKey() => $category->localized('name', 'tr') ?? "#{$category->getKey()}",
                        ])
                        ->all()),
                TernaryFilter::make('needs_review')
                    ->label('Gözden geçirilmeli'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('viewOnSite')
                    ->label('Sitede gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Post $record): ?string => LocaleUrls::post($record, 'tr'), shouldOpenInNewTab: true)
                    ->hidden(fn (Post $record): bool => LocaleUrls::post($record, 'tr') === null),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
