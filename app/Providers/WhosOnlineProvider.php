<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class WhosOnlineProvider extends ServiceProvider
{
    public function boot()
    {
        $this->getOnlineUsersCount();
    }

    public function register()
    {
        //
    }

    /**
     * Inspired by comments in article: https://erikbelusic.com/tracking-if-a-user-is-online-in-laravel/
     *
     * See also \App\Http\Middleware\LogLastUserActivity
     */
    private function getOnlineUsersCount()
    {
//        if (Auth::check() && Auth::user()->can('view whosonline statistics')) {
            // register to a specific view
            view()->composer('members.dashboard', function ($view) {
                /**
                 * Reduces server load by limiting to recently-logged-in users
                 * Requires a timestamp field called 'last_login_at' on the Users table+model
                 */
                $users = User::where('last_login_at', '>', Carbon::now()->addDays('-3'))
//                ->remember(15)// cache for 15 minutes
                    ->get();

                $onlineUsers = $users->filter(function ($user) {
                    if (Cache::has($user->getWhosOnlineKey())) {
                        return $user;
                    }
                });

                $view->with(['onlineUsers' => $onlineUsers]);
            });
//        }
    }
}
