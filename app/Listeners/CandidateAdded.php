<?php

namespace App\Listeners;

use App\User;
use App\Notifications\CandidateAddedNotification;
use Illuminate\Support\Facades\Notification;

class CandidateAdded
{

    // @TODO - consider refactoring per https://themsaid.com/laravel-mailables-notfiications-as-event-listeners-20170516

    public function __construct()
    {
        //
    }

    public function handle($who, User $by)
    {
        if (config('site.notify_CandidateAdded1')) {
            Notification::route('mail', config('site.notify_CandidateAdded1'))
                ->notify(new CandidateAddedNotification($who, $by));
        }
        if (config('site.notify_CandidateAdded2')) {
            Notification::route('mail', config('site.notify_CandidateAdded2'))
            ->notify(new CandidateAddedNotification($who, $by));
        }
    }
}
