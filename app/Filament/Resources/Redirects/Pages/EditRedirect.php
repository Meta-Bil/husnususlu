<?php

namespace App\Filament\Resources\Redirects\Pages;

use App\Filament\Resources\Redirects\RedirectResource;
use App\Support\Http\RedirectResolver;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRedirect extends EditRecord
{
    protected static string $resource = RedirectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(fn () => RedirectResolver::flushCache()),
        ];
    }

    /**
     * The resolver caches the whole redirect map, so it has to be rebuilt.
     */
    protected function afterSave(): void
    {
        RedirectResolver::flushCache();
    }
}
