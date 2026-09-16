<?php

namespace App\Filament\Resources\Videos\Tables;

use App\Enums\VideoCategory;
use App\Models\Video;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Görsel')
                    ->state(fn (Video $record): string => $record->thumbnailUrl())
                    ->height(40),
                TextColumn::make('title')
                    ->label('Başlık')
                    ->state(fn (Video $record): ?string => $record->localized('title', 'tr'))
                    ->description(fn (Video $record): ?string => $record->localized('program', 'tr'))
                    ->searchable(['title', 'youtube_id', 'channel']),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('channel')
                    ->label('Kanal')
                    ->toggleable(),
                TextColumn::make('duration')
                    ->label('Süre')
                    ->toggleable(),
                TextColumn::make('published_on')
                    ->label('Yayın tarihi')
                    ->date('d.m.Y')
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Öne çıkan')
                    ->boolean(),
                IconColumn::make('is_visible')
                    ->label('Görünür')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(VideoCategory::class),
                TernaryFilter::make('is_visible')
                    ->label('Görünür'),
                TernaryFilter::make('is_featured')
                    ->label('Öne çıkan'),
            ])
            ->recordActions([
                Action::make('watchOnYoutube')
                    ->label('YouTube’da aç')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Video $record): string => $record->watchUrl(), shouldOpenInNewTab: true),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
