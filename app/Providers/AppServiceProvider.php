<?php

namespace App\Providers;

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Repositories\ApiTokenRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ApiTokenRepositoryInterface::class,
            ApiTokenRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || app()->environment('development')) {
            URL::forceScheme('https');
        }
    }
}
