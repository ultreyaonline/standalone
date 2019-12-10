<?php

namespace App\Listeners;

use App\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserDeletedNotification;

class UserDeleted
{

    // @TODO - consider refactoring per https://themsaid.com/laravel-mailables-notfiications-as-event-listeners-20170516

    public function __construct()
    {
        //
    }

    public function handle($who, $by)
    {
        if (config('site.notify_UserDeleted1')) {
            Notification::route('mail', config('site.notify_UserDeleted1'))
            ->notify(new UserDeletedNotification($who, $by));
        }

        if (config('site.notify_UserDeleted2')) {
            Notification::route('mail', config('site.notify_UserDeleted2'))
            ->notify(new UserDeletedNotification($who, $by));
        }
    }
}
