<?php

namespace App\Listeners;

class LogPasswordChanged
{
    public function handle($event)
    {
        activity()->performedOn($event->user)->causedBy($event->user)->log('Password changed.');
    }
}
