<?php

namespace App\Filament\Resources\AppointmentRequests\Schemas;

use App\Enums\AppointmentStatus;
use App\Models\AppointmentRequest;
use App\Support\Localization\Locales;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppointmentRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Talep')
                    ->description('Bu bilgiler ziyaretçinin doldurduğu formdan gelir ve değiştirilemez.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Placeholder::make('name')
                            ->label('Ad soyad')
                            ->content(fn (AppointmentRequest $record): string => $record->name),
                        Placeholder::make('phone')
                            ->label('Telefon')
                            ->content(fn (AppointmentRequest $record): string => $record->phone),
                        Placeholder::make('email')
                            ->label('E-posta')
                            ->content(fn (AppointmentRequest $record): string => $record->email ?: '—'),
                        Placeholder::make('complaint')
                            ->label('Şikâyet')
                            ->content(fn (AppointmentRequest $record): string => $record->complaint ?: '—'),
                        Placeholder::make('preferred_date')
                            ->label('Tercih edilen gün')
                            ->content(fn (AppointmentRequest $record): string => $record->preferred_date?->format('d.m.Y') ?? '—'),
                        Placeholder::make('preferred_time')
                            ->label('Tercih edilen saat')
                            ->content(fn (AppointmentRequest $record): string => $record->preferred_time ?: '—'),
                        Placeholder::make('locale')
                            ->label('Dil')
                            ->content(fn (AppointmentRequest $record): string => Locales::native((string) $record->locale)),
                        Placeholder::make('created_at')
                            ->label('Geliş zamanı')
                            ->content(fn (AppointmentRequest $record): string => $record->created_at?->format('d.m.Y H:i') ?? '—'),
                        Placeholder::make('message')
                            ->label('Mesaj')
                            ->content(fn (AppointmentRequest $record): string => $record->message ?: '—')
                            ->columnSpanFull(),
                        Placeholder::make('source_url')
                            ->label('Geldiği sayfa')
                            ->content(fn (AppointmentRequest $record): string => $record->source_url ?: '—')
                            ->columnSpanFull(),
                    ]),

                Section::make('Takip')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Durum')
                            ->options(AppointmentStatus::class)
                            ->default(AppointmentStatus::New->value)
                            ->native(false)
                            ->required(),
                        Textarea::make('admin_notes')
                            ->label('Klinik notu')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
