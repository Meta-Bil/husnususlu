<?php

namespace App\Observers;

use App\Models\MenuItem;
use App\Support\Navigation\Navigation;

class MenuItemObserver
{
    public function saved(MenuItem $menuItem): void
    {
        Navigation::flushCache();
    }

    public function deleted(MenuItem $menuItem): void
    {
        Navigation::flushCache();
    }
}
