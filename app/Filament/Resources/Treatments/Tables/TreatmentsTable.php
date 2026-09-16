<?php

namespace App\Filament\Resources\Treatments\Tables;

use App\Enums\ContentStatus;
use App\Enums\TreatmentKind;
use App\Models\Treatment;
use App\Support\Localization\Locales;
use App\Support\Localization\LocaleUrls;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TreatmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->state(fn (Treatment $record): ?string => $record->localized('title', 'tr'))
                    ->description(fn (Treatment $record): ?string => $record->slugFor('tr'))
                    ->searchable(['title', 'slug_tr']),
                TextColumn::make('kind')
                    ->label('Tür')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge(),
                IconColumn::make('is_featured')
                    ->label('Öne çıkan')
                    ->boolean(),
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
                SelectFilter::make('kind')
                    ->label('Tür')
                    ->options(TreatmentKind::class),
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(ContentStatus::class),
                TernaryFilter::make('is_featured')
                    ->label('Öne çıkan'),
                TernaryFilter::make('needs_review')
                    ->label('Gözden geçirilmeli'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('viewOnSite')
                    ->label('Sitede gör')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Treatment $record): ?string => LocaleUrls::treatment($record, 'tr'), shouldOpenInNewTab: true)
                    ->hidden(fn (Treatment $record): bool => LocaleUrls::treatment($record, 'tr') === null),
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
