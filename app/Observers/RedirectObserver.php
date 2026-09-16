<?php

namespace App\Observers;

use App\Models\Redirect;
use App\Support\Http\RedirectResolver;

class RedirectObserver
{
    public function saved(Redirect $redirect): void
    {
        RedirectResolver::flushCache();
    }

    public function deleted(Redirect $redirect): void
    {
        RedirectResolver::flushCache();
    }
}
