<?php

namespace App\Observers;

use App\Models\Menu;
use App\Support\Navigation\Navigation;

class MenuObserver
{
    public function saved(Menu $menu): void
    {
        Navigation::flushCache();
    }

    public function deleted(Menu $menu): void
    {
        Navigation::flushCache();
    }
}
