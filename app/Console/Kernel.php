<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

/**
 * Console Kernel — Application Task Scheduler
 *
 * Defines all scheduled Artisan commands and closures that run automatically.
 * The OS-level cron must call `php artisan schedule:run` every minute for this to work.
 *
 * ## Scheduled tasks summary
 *
 * | Time           | Task                                    | Purpose                            |
 * |----------------|-----------------------------------------|------------------------------------|
 * | Every 10 min   | SendPrayerWheelAcknowledgements job     | Acknowledge new prayer sign-ups    |
 * | Daily @ 16:00  | SendPrayerWheelReminderEmails job       | Daily reminder for prayer slots    |
 * | Daily @ 03:40  | backup:clean                            | Prune old backups                  |
 * | Daily @ 03:50  | backup:run                              | Create DB + file backup            |
 * | Daily          | activitylog:clean                       | Prune old activity log entries     |
 *
 * ## Notes for maintainers
 *
 * - `horizon:snapshot` is commented out. Un-comment it if you want Horizon to collect
 *   queue metric history (required for trend graphs in the Horizon dashboard).
 *
 * - The log-file purge closure is also commented out. If log rotation is needed,
 *   define `$logfile` with the correct storage path and uncomment the block.
 *
 * - The Prayer Wheel reminder job has a commented-out `everyMinute()` alternative
 *   schedule — this was used during development/testing.
 *
 * - All times are in the server's configured timezone (see config/app.php → timezone).
 *   The Prayer Wheel slot calculations depend on this matching local time.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Daily Backups. (Runs clean/prune first to free up space) (spatie/laravel-backup)
        $schedule->command('backup:clean')->daily()->at('03:40');
        $schedule->command('backup:run')->daily()->at('03:50');

        // Spatie Activity Log pruning - set number of days in config/activitylog.php file
        $schedule->command('activitylog:clean logout --days=15')->daily();
        $schedule->command('activitylog:clean login-success --days=90')->daily();
        $schedule->command('activitylog:clean login-failures --days=90')->daily();
        $schedule->command('activitylog:clean passwords --days=180')->daily();

        /**
         * Acknowledge new prayer wheel sign-ups.
         *
         * Runs every 10 minutes. Sends a consolidated email to any member who has
         * signed up for prayer slots since the last run. 
         * See SendPrayerWheelAcknowledgements.
         */
        $schedule->call(function () {
            \App\Jobs\SendPrayerWheelAcknowledgements::dispatch();
        })->everyTenMinutes();

        /**
         * Send daily prayer wheel reminder emails.
         *
         * Runs once daily at 4:00pm. Sends reminders to opted-in members with
         * upcoming prayer slots. See SendPrayerWheelReminderEmails for known issues
         * with the reminded_at flag.
         *
         * Note: ->everyMinute() alternative below is for development testing only.
         */
        $schedule->call(function () {
            \App\Jobs\SendPrayerWheelReminderEmails::dispatch();
        })->dailyAt('16:00:00');
//        })->everyMinute();

        // Horizon Queue statistics collection, if installed
        if (\Route::has('horizon.index')) {
            $schedule->command('horizon:snapshot')->everyFiveMinutes();
        }
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
