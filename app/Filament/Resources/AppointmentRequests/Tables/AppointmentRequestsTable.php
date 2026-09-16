<?php

namespace App\Filament\Resources\AppointmentRequests\Tables;

use App\Enums\AppointmentStatus;
use App\Models\AppointmentRequest;
use App\Support\Localization\Locales;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Ad soyad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('complaint')
                    ->label('Şikâyet')
                    ->limit(40)
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('preferred_date')
                    ->label('Tercih edilen gün')
                    ->date('d.m.Y')
                    ->description(fn (AppointmentRequest $record): ?string => $record->preferred_time)
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('locale')
                    ->label('Dil')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => Locales::short($state)),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Geliş zamanı')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(AppointmentStatus::class),
                Filter::make('created_at')
                    ->label('Geliş tarihi')
                    ->schema([
                        DatePicker::make('from')->label('Başlangıç'),
                        DatePicker::make('until')->label('Bitiş'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date))),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Aç'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
