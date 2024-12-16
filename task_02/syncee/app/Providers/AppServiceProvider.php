<?php

namespace App\Providers;

use App\Contracts\SyncLoggerContract;
use App\Services\Logging\SyncLogger;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SyncLoggerContract::class, SyncLogger::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
