<?php

namespace App\Listeners;

// Illuminate\Auth\Events\Logout
use Illuminate\Support\Facades\Cache;

class LogSuccessfulLogout
{
    public function handle($event)
    {
        $user = null;
        if (!empty($event->impersonated)) {
            $user = $event->impersonated;
            activity()->performedOn($event->impersonated)->causedBy($event->impersonator)->log('Ended Impersonation of ' . $event->impersonated->name);
        } elseif ($event->user) {
            $user = $event->user;
            activity()->performedOn($event->user)->causedBy($event->user)->log('Logout');
        }

        // update whos-online-cache
        if (!empty($user)) {
            Cache::forget($user->getWhosOnlineKey());
        }
    }
}
