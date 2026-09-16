<?php

namespace App\Filament\Resources\Redirects\Tables;

use App\Support\Http\RedirectResolver;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RedirectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('from_path')
            ->columns([
                TextColumn::make('from_path')
                    ->label('Eski adres')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('to_path')
                    ->label('Yeni adres')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('status_code')
                    ->label('Tür')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Etkin')
                    ->boolean(),
                TextColumn::make('hits')
                    ->label('Kullanım')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_hit_at')
                    ->label('Son kullanım')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Etkin'),
                SelectFilter::make('status_code')
                    ->label('Tür')
                    ->options([
                        301 => '301 — Kalıcı',
                        302 => '302 — Geçici',
                        410 => '410 — Kaldırıldı',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->after(fn () => RedirectResolver::flushCache()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn () => RedirectResolver::flushCache()),
                ]),
            ]);
    }
}
