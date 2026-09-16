<?php

namespace App\Filament\Resources\Redirects\Schemas;

use App\Models\Redirect;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Yönlendirme')
                    ->description('Hiçbir sayfayla eşleşmeyen adresler burada aranır.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('from_path')
                            ->label('Eski adres')
                            ->helperText('Alan adı olmadan, başında eğik çizgiyle: /agri-tedavisi')
                            ->prefix(config('app.url'))
                            ->required()
                            ->maxLength(512)
                            ->unique(ignoreRecord: true),
                        TextInput::make('to_path')
                            ->label('Yeni adres')
                            ->helperText('Site içi bir yol veya tam bir bağlantı olabilir.')
                            ->required()
                            ->maxLength(512),
                        Select::make('status_code')
                            ->label('Yönlendirme türü')
                            ->options([
                                301 => '301 — Kalıcı',
                                302 => '302 — Geçici',
                                410 => '410 — Kaldırıldı',
                            ])
                            ->default(301)
                            ->native(false)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Etkin')
                            ->default(true),
                        Textarea::make('note')
                            ->label('Not')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Kullanım')
                    ->columnSpanFull()
                    ->columns(2)
                    ->visibleOn('edit')
                    ->schema([
                        Placeholder::make('hits')
                            ->label('Kullanım sayısı')
                            ->content(fn (?Redirect $record): string => (string) ($record?->hits ?? 0)),
                        Placeholder::make('last_hit_at')
                            ->label('Son kullanım')
                            ->content(fn (?Redirect $record): string => $record?->last_hit_at?->format('d.m.Y H:i') ?? '—'),
                    ]),
            ]);
    }
}
