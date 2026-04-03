<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web([
            /**
             * LogLastUserActivity — updates users.last_login_at and Redis online-presence key
             */
            \App\Http\Middleware\LogLastUserActivity::class,
        ]);

        $middleware->api([
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // aliases
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'roleOrPermission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withCommands([
        __DIR__ . '/../app/Console/Commands',
    ])
    ->withSchedule(function (Schedule $schedule) {
        /**
         * Acknowledge new prayer wheel sign-ups.
         * Runs every 10 minutes.
         * Sends a consolidated email to any member who has
         * signed up for prayer slots since the last run.
         */
        $schedule->call(function () {
            \App\Jobs\SendPrayerWheelAcknowledgements::dispatch();
        })->everyTenMinutes();

        /**
         * Send daily prayer wheel reminder emails.
         * Runs once daily at 4:00pm.
         * Sends reminders to opted-in members with upcoming prayer slots.
         */
        $schedule->call(function () {
            \App\Jobs\SendPrayerWheelReminderEmails::dispatch();
        })->dailyAt('16:00:00');

        // Daily Backups
        $schedule->command('backup:clean')->daily()->at('03:40');
        $schedule->command('backup:run')->daily()->at('03:50');

        // Cleanup
        $schedule->command('activitylog:clean logout --days=15')->daily();
        $schedule->command('activitylog:clean login-success --days=90')->daily();
        $schedule->command('activitylog:clean login-failures --days=90')->daily();
        $schedule->command('activitylog:clean passwords --days=180')->daily();

        // Horizon Queue statistics collection, if installed
        if (\Route::has('horizon.index')) {
            $schedule->command('horizon:snapshot')->everyFiveMinutes();
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
