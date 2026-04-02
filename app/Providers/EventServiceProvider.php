<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

/**
 * Class EventServiceProvider
 *
 * Maps application events to their listener classes.
 *
 * ## Auth events → audit listeners
 * All authentication lifecycle events are wired to listeners that write to the
 * activity log (spatie/laravel-activitylog) and the failed_login_attempts table.
 *
 * | Event                          | Listener                        | Action                     |
 * |--------------------------------|---------------------------------|----------------------------|
 * | Auth\Events\Attempting         | LogAuthenticationAttempt        | Logs the attempt            |
 * | Auth\Events\Login              | LogSuccessfulLogin              | Logs login, updates last_login_at |
 * | Auth\Events\Logout             | LogSuccessfulLogout             | Logs logout                 |
 * | Auth\Events\Lockout            | LogLockout                      | Logs lockout event          |
 * | Auth\Events\Failed             | RecordFailedLoginAttempt        | Writes to failed_login_attempts |
 * | Auth\Events\PasswordReset      | LogPasswordChanged              | Logs password reset         |
 *
 * ## Impersonation events
 * Admin impersonation start/end (lab404/laravel-impersonate) reuses the login/logout
 * listeners so impersonation sessions appear in the same audit trail as normal logins.
 *
 * ## Domain events → listeners
 * | Event                   | Listener           | Action                                        |
 * |-------------------------|--------------------|-----------------------------------------------|
 * | App\Events\UserAdded    | UserAdded          | Sends notification to configured admins       |
 * | App\Events\UserDeleted  | UserDeleted        | Sends notification, cleans up related data    |
 * | App\Events\CandidateDeleted | CandidateDeleted | Sends notification to pre-weekend team       |
 *
 * Notification recipients for UserAdded/UserDeleted/CandidateDeleted are configured
 * in config/site.php (notify_UserAdded1, notify_UserDeleted1, etc.).
 *
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Auth lifecycle — all feed the activity log
        \Illuminate\Auth\Events\Attempting::class => [\App\Listeners\LogAuthenticationAttempt::class],
        \Illuminate\Auth\Events\Login::class      => [\App\Listeners\LogSuccessfulLogin::class],
        \Illuminate\Auth\Events\Logout::class     => [\App\Listeners\LogSuccessfulLogout::class],
        \Illuminate\Auth\Events\Lockout::class    => [\App\Listeners\LogLockout::class],
        \Illuminate\Auth\Events\Failed::class     => [\App\Listeners\RecordFailedLoginAttempt::class],
        \Illuminate\Auth\Events\PasswordReset::class => [\App\Listeners\LogPasswordChanged::class],

        // Admin impersonation — reuses auth listeners for unified audit trail
        \Lab404\Impersonate\Events\TakeImpersonation::class => [\App\Listeners\LogSuccessfulLogin::class],
        \Lab404\Impersonate\Events\LeaveImpersonation::class => [\App\Listeners\LogSuccessfulLogout::class],

        // Domain events
        \App\Events\UserAdded::class              => [\App\Listeners\UserAdded::class],
        \App\Events\UserDeleted::class            => [\App\Listeners\UserDeleted::class],
        \App\Events\CandidateDeleted::class       => [\App\Listeners\CandidateDeleted::class],
        \App\Events\CandidateAdded::class         => [\App\Listeners\CandidateAdded::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
