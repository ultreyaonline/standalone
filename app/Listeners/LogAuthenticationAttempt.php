<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogAuthenticationAttempt
{
    public function handle(Attempting $event)
    {
        if (config('site.log_login_attempts', false)) {
            activity()->withProperty('username', $event->credentials['username'])
                ->log('Login attempted: ' . $event->credentials['username']);
        }
    }
}
