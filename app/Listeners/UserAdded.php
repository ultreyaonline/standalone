<?php

namespace App\Listeners;

use App\User;
use App\Notifications\UserAddedNotification;
use Illuminate\Support\Facades\Notification;

class UserAdded
{

    // @TODO - consider refactoring per https://themsaid.com/laravel-mailables-notfiications-as-event-listeners-20170516

    public function __construct()
    {
        //
    }

    public function handle(User $user, string $by)
    {
        if (config('site.notify_UserAdded1')) {
            Notification::route('mail', config('site.notify_UserAdded1'))
                ->notify(new UserAddedNotification($user, $by));
        }
        if (config('site.notify_UserAdded2')) {
            Notification::route('mail', config('site.notify_UserAdded2'))
            ->notify(new UserAddedNotification($user, $by));
        }
    }
}
