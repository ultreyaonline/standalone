<?php

namespace App\Listeners;

use App\Notifications\UserLoggedIn;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle($event)
    {
        if (! empty($event->impersonated)) {
            activity()->performedOn($event->impersonated)->causedBy($event->impersonator)->log('Begin Impersonation as ' . $event->impersonated->name);
        } else {
            // add to activity log
            activity()->log('Login successful.');

            // also send to Slack
            $event->user->notify(new UserLoggedIn($event->user));

            // log to db, without hitting the User model, since we don't want to update other timestamps, nor fire other events.
            DB::update('UPDATE ' . $event->user->getTable() . ' SET last_login_at = "' . Carbon::now()->toDateTimeString() . '" where id =' . (int)$event->user->id);
//            $event->user->last_login_at = Carbon::now();
//            $event->user->timestamps = false;
//            $event->user->save();
        }
    }
}
