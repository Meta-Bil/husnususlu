<?php

namespace App\Filament\Resources\PostCategories\Tables;

use App\Models\PostCategory;
use App\Support\Localization\LocaleUrls;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('Ad')
                    ->state(fn (PostCategory $record): ?string => $record->localized('name', 'tr'))
                    ->description(fn (PostCategory $record): ?string => $record->slugFor('tr'))
                    ->searchable(['name', 'slug_tr']),
                TextColumn::make('posts_count')
                    ->label('Yazı sayısı')
                    ->counts('posts')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Güncellendi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('viewOnSite')
                    ->label('Sitede gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (PostCategory $record): ?string => LocaleUrls::category($record, 'tr'), shouldOpenInNewTab: true)
                    ->hidden(fn (PostCategory $record): bool => LocaleUrls::category($record, 'tr') === null),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
