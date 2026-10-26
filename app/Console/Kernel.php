<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ProcessMonthlyRent::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run rent processing on the first day of every month shortly after midnight.
        // To activate this, make sure you have a cron entry running "php artisan schedule:run" every minute.
        $schedule->command('rent:process')->monthlyOn(1, '00:05');
        // Run late-payment interest check on the 28th of each month at 11:59 PM.
        $schedule->command('rent:process')->monthlyOn(28, '23:59');
        
        // Run automatic backup daily at 2:00 AM
        $schedule->command('backup:run')->dailyAt('02:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        // load commands automatically
        $this->load(__DIR__ . '/Commands');
    }
}
