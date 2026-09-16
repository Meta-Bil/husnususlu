<?php

namespace App\Filament\Resources\Redirects\Pages;

use App\Filament\Resources\Redirects\RedirectResource;
use App\Support\Http\RedirectResolver;
use Filament\Resources\Pages\CreateRecord;

class CreateRedirect extends CreateRecord
{
    protected static string $resource = RedirectResource::class;

    /**
     * The resolver caches the whole redirect map, so it has to be rebuilt.
     */
    protected function afterCreate(): void
    {
        RedirectResolver::flushCache();
    }
}
