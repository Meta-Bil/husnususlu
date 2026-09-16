<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Redirect;
use App\Observers\MenuItemObserver;
use App\Observers\MenuObserver;
use App\Observers\RedirectObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Menu::observe(MenuObserver::class);
        MenuItem::observe(MenuItemObserver::class);
        Redirect::observe(RedirectObserver::class);
    }
}
