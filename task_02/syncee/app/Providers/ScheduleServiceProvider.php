<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
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
    public function boot(Schedule $schedule): void
    {
        $schedule->command('sync:webshop')->withoutOverlapping()->everyThreeMinutes();
        $schedule->command('sync:connect')->withoutOverlapping()->everyThreeMinutes();
        $schedule->command('sync:pairing')->withoutOverlapping()->everyFiveMinutes();
    }
}
