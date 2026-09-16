<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Enums\ContentStatus;
use App\Enums\PageTemplate;
use App\Models\Page;
use App\Support\Localization\Locales;
use App\Support\Localization\LocaleUrls;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->state(fn (Page $record): ?string => $record->localized('title', 'tr'))
                    ->description(fn (Page $record): ?string => $record->slugFor('tr'))
                    ->searchable(['title', 'slug_tr']),
                TextColumn::make('template')
                    ->label('Şablon')
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
                TextColumn::make('updated_at')
                    ->label('Güncellendi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(ContentStatus::class),
                SelectFilter::make('template')
                    ->label('Şablon')
                    ->options(PageTemplate::class),
                TernaryFilter::make('needs_review')
                    ->label('Gözden geçirilmeli'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('viewOnSite')
                    ->label('Sitede gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Page $record): ?string => LocaleUrls::page($record, 'tr'), shouldOpenInNewTab: true)
                    ->hidden(fn (Page $record): bool => LocaleUrls::page($record, 'tr') === null),
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
