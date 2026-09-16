<?php

namespace App\Filament\Resources\AppointmentRequests;

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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AppointmentRequestResource extends Resource
{
    protected static ?string $model = AppointmentRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $hasTitleCaseModelLabel = false;

    public static function getNavigationLabel(): string
    {
        return 'Randevu talepleri';
    }

    public static function getModelLabel(): string
    {
        return 'randevu talebi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'randevu talepleri';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Randevular';
    }

    public static function getNavigationBadge(): ?string
    {
        $unhandled = AppointmentRequest::unhandled()->count();

        return $unhandled > 0 ? (string) $unhandled : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): string|Htmlable|null
    {
        return 'Henüz ele alınmamış talepler';
    }

    /**
     * Requests arrive from the public form only.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record instanceof AppointmentRequest
            ? $record->name
            : parent::getRecordTitle($record);
    }

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
            'edit' => EditAppointmentRequest::route('/{record}/edit'),
        ];
    }
}
