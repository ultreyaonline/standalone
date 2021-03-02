<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \Illuminate\Auth\Events\Attempting::class => [\App\Listeners\LogAuthenticationAttempt::class],
        \Illuminate\Auth\Events\Login::class      => [\App\Listeners\LogSuccessfulLogin::class],
        \Illuminate\Auth\Events\Logout::class     => [\App\Listeners\LogSuccessfulLogout::class],
        \Illuminate\Auth\Events\Lockout::class    => [\App\Listeners\LogLockout::class],
        \Illuminate\Auth\Events\Failed::class     => [\App\Listeners\RecordFailedLoginAttempt::class],
        \Illuminate\Auth\Events\PasswordReset::class => [\App\Listeners\LogPasswordChanged::class],

        \Lab404\Impersonate\Events\TakeImpersonation::class => [\App\Listeners\LogSuccessfulLogin::class],
        \Lab404\Impersonate\Events\LeaveImpersonation::class => [\App\Listeners\LogSuccessfulLogout::class],

        \App\Events\UserAdded::class              => [\App\Listeners\UserAdded::class],
        \App\Events\UserDeleted::class            => [\App\Listeners\UserDeleted::class],
        \App\Events\CandidateDeleted::class       => [\App\Listeners\CandidateDeleted::class],
        \App\Events\CandidateAdded::class         => [\App\Listeners\CandidateAdded::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
