<?php

namespace App\Filament\Resources\AppointmentRequests;

use App\Filament\Resources\AppointmentRequests\Pages\CreateAppointmentRequest;
use App\Filament\Resources\AppointmentRequests\Pages\EditAppointmentRequest;
use App\Filament\Resources\AppointmentRequests\Pages\ListAppointmentRequests;
use App\Filament\Resources\AppointmentRequests\Schemas\AppointmentRequestForm;
use App\Filament\Resources\AppointmentRequests\Tables\AppointmentRequestsTable;
use App\Models\AppointmentRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AppointmentRequestResource extends Resource
{
    protected static ?string $model = AppointmentRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AppointmentRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointmentRequests::route('/'),
            'create' => CreateAppointmentRequest::route('/create'),
            'edit' => EditAppointmentRequest::route('/{record}/edit'),
        ];
    }
}
