<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LogLastUserActivity
{
    /**
     * Cache User Login activities, to know whether someone is online or not
     * See also \App\Providers\WhosOnlineProvider
     *
     * Inspired by article and comments at https://erikbelusic.com/tracking-if-a-user-is-online-in-laravel/
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(15);
            $user = $request->user();
            Cache::put($user->getWhosOnlineKey(), true, $expiresAt);
        }

        return $next($request);
    }
}
