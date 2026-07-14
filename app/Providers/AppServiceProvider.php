<?php

namespace App\Providers;

use App\Support\Favorites;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(Favorites::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $favorites = $this->app->make(Favorites::class);

            $view->with('favoriteIds', $favorites->all())
                ->with('favoriteCount', $favorites->count());
        });
    }
}
