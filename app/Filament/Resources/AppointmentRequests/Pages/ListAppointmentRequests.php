<?php

namespace App\Filament\Resources\AppointmentRequests\Pages;

use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListAppointmentRequests extends ListRecords
{
    protected static string $resource = AppointmentRequestResource::class;

    /**
     * Requests are only created by the public form, so there is nothing to add here.
     */
    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
