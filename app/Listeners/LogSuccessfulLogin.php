<?php

namespace App\Listeners;

use App\Notifications\UserLoggedIn;
use Illuminate\Support\Carbon;
//use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle($event)
    {
        if (! empty($event->impersonated)) {
            activity('admin')->performedOn($event->impersonated)->causedBy($event->impersonator)->log('Begin Impersonation as ' . $event->impersonated->name);
        } else {
            // add to activity log
            activity('login-success')->log('Login successful.');

            // log last login date without updating other timestamps, nor firing other events
            $event->user->last_login_at = Carbon::now();
            $event->user->timestamps = false;
            $event->user->saveQuietly();

            // also send to configured notifications (such as Slack etc)
            $event->user->notify(new UserLoggedIn($event->user));
        }
    }
}
