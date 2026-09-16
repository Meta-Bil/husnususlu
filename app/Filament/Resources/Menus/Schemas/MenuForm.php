<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menü')
                    ->description('Menü öğeleri, menü kaydedildikten sonra alttaki listeden düzenlenir.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('key')
                            ->label('Anahtar')
                            ->helperText('Şablonda bu menüyü çağırmak için kullanılır, örneğin "main" veya "footer".')
                            ->required()
                            ->maxLength(255)
                            ->alphaDash()
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }
}
