<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('message-confirmation:run')->dailyAt('06:30');
        $schedule->command('patient-photos:update')->dailyAt('12:37');
        $schedule->command('backup:database')->daily()->at('02:00');
        $schedule->command('patient-photos:update')->everyFifteenDays()->at('02:00');
        $schedule->command('campaign:run --campaignId=19 --batch=30 --minDelay=10 --maxDelay=15')
            ->everyFourMinutes()
            ->between('07:00', '22:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
