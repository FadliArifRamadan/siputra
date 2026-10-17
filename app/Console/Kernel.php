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
        // $schedule->command('inspire')->hourly();
        // ✅ Jalankan SLA check setiap jam
        $schedule->command('sla:check')->hourly();

        // ✅ Kirim pengingat setiap hari jam 08:00 dan 20:00
        $schedule->command('reminders:send')->dailyAt('08:00');
        $schedule->command('reminders:send')->dailyAt('20:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
