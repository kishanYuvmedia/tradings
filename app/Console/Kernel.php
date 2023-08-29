<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
$startHour = 9.3; // Change this to your desired start hour
$endHour = 15.3;  // Change this to your desired end hour

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('websocket:api-call')
          ->weekdays()
          ->everyFiveMinutes()
          ->timezone('Asia/Kolkata')
          ->between('9:30', '15:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
