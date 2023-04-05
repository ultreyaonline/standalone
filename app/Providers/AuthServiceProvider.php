<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Admin" role all permissions (assuming they are verified using gate-related functions):
        Gate::before(function ($user, $ability) {
            if (in_array($ability, ['can delete admins', 'can delete super-admins'])) return null;
            if ($user->hasRole('Admin') || $user->hasRole('Super-Admin')) {
                return true;
            }
        });

        Gate::define('make SD assignments', function ($user) {
            if (config('site.community_SA_may_assign_SDs') && $user->can('assign SD to teams')) {
                return true;
            }
        });

        Gate::define('add community member', function ($user) {
            if (config('site.rectors_can_add_new_members') && $user->isAnActiveRector($roverOrHeadToo = 'head')) {
                return true;
            }
        });

        Gate::define('use rectors tools', function ($user) {
            if ($user->isAnActiveRector($roverOrHeadToo = 'head')) {
                return true;
            }
        });

        Gate::define('email entire community', function ($user) {
            if ($user->isAnActiveRector($roverOrHeadToo = 'rector')) {
                return true;
            }
            if (optional($user->spouse)->isAnActiveRector($roverOrHeadToo = 'rector')) {
                return true;
            }
        });

        Gate::define('see weekend team roster', function ($user, $weekend) {
            if ($weekend->visibility_flag >= \App\Enums\WeekendVisibleTo::Community) {
                return true;
            }

            if ($user->hasAnyRole(['President', 'Admin', 'Super-Admin', 'Emerging Community Liaison'])) {
                return true;
            }

            if ($user->hasAnyRole(['Mens Leader', 'Rector Selection']) && $weekend->weekend_MF === 'M' && $user->gender === 'M') {
                return true;
            }

            if ($user->hasAnyRole(['Womens Leader', 'Rector Selection']) && $weekend->weekend_MF === 'W' && $user->gender === 'W') {
                return true;
            }

            $my_assignments = $user->rolesForWeekend($weekend->id, $includeWeekendsNotMadeVisibleToCommunityYet = true);

            if (! \count($my_assignments)) {
                return false;
            }

            if ($weekend->visibility_flag == \App\Enums\WeekendVisibleTo::HeadChas) {
                foreach ($my_assignments as $position) {
                    if (\in_array($position->role->id, [1,2])) {
                        return true;
                    }
                }
            }
            if ($weekend->visibility_flag == \App\Enums\WeekendVisibleTo::SectionHeads) {
                foreach ($my_assignments as $position) {
                    if ($position->role->isDeptHead) {
                        return true;
                    }
                }
            }

            return false;
        });

        Gate::define('see section heads tools', function ($user, $weekend) {

            if ($user->id === $weekend->rectorID) {
                return true;
            }

            $assignments = $user->rolesForWeekend($weekend->id, $ignoreVisibleOnly = true);

            if (! \count($assignments)) {
                return false;
            }
            if ($weekend->visibility_flag >= \App\Enums\WeekendVisibleTo::ThemeVisible) {
                foreach ($assignments as $position) {
                    if ($position->role->isDeptHead) {
                        return true;
                    }
                }
            }
            return false;
        });

        // @TODO - Rector overrides
//        if ($user->isAnActiveRector($roverOrHeadToo = 'rector')) {
//            $user->can('email entire community');
//            $user->can('view past community service');
//            $user->can('add community member');
//            $user->can('use leaders worksheet');
//        }


        Gate::define('see reports about team fees', function ($user, $weekend) {
            if ($weekend->visibility_flag < \App\Enums\WeekendVisibleTo::Community) {
                return false;
            }

            if ($user->hasAnyRole(['Mens Leader', 'Womens Leader']) && $weekend->weekend_MF === $user->gender) {
                return true;
            }

            if ($weekend->mayTrackTeamPayments || $user->can('record team fees paid')) {
                return true;
            }

            return false;
        });



    }
}
