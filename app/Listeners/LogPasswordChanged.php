<?php

namespace App\Listeners;

class LogPasswordChanged
{
    public function handle($event)
    {
        activity('passwords')->performedOn($event->user)->causedBy($event->user)->log('Password changed.');
    }
}
