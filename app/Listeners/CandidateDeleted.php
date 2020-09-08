<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CandidateDeletedNotification;

class CandidateDeleted
{

    // @TODO - consider refactoring per https://themsaid.com/laravel-mailables-notfiications-as-event-listeners-20170516

    public function __construct()
    {
        //
    }

    public function handle($who, User $by)
    {
        if (config('site.notify_CandidateDeleted1')) {
            Notification::route('mail', config('site.notify_CandidateDeleted1'))
            ->notify(new CandidateDeletedNotification($who, $by));
        }

        if (config('site.notify_CandidateDeleted2')) {
            Notification::route('mail', config('site.notify_CandidateDeleted2'))
            ->notify(new CandidateDeletedNotification($who, $by));
        }
    }
}
