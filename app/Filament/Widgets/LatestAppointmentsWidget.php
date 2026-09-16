<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use App\Models\AppointmentRequest;
use App\Support\Localization\Locales;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestAppointmentsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Son randevu talepleri';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => AppointmentRequest::query()->latest()->limit(5))
            ->paginated(false)
            ->emptyStateHeading('Henüz randevu talebi yok')
            ->columns([
                TextColumn::make('name')
                    ->label('Ad soyad'),
                TextColumn::make('phone')
                    ->label('Telefon'),
                TextColumn::make('complaint')
                    ->label('Şikâyet')
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('locale')
                    ->label('Dil')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => Locales::short($state)),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Geliş zamanı')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Aç')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (AppointmentRequest $record): string => AppointmentRequestResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
