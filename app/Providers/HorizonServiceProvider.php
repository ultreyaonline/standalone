<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

/**
 * Class HorizonServiceProvider
 *
 * Configures access control for the Laravel Horizon queue dashboard at /horizon.
 *
 * ## Dashboard access
 * Protected by the 'viewHorizon' Gate defined in gate() below.
 * Any user with the 'manage queues' Spatie permission is granted access.
 * In local environments, Laravel's default allows all authenticated users in.
 *
 * ## Alert notifications (currently disabled)
 * Horizon can send alerts when queues stall or workers crash.
 * Un-comment the relevant notification routing lines in boot() to enable:
 *   - routeMailNotificationsTo()   — email alerts
 *   - routeSlackNotificationsTo()  — Slack webhook alerts
 *   - routeSmsNotificationsTo()    — SMS alerts
 *
 * ## Dashboard URL
 * Accessible at /horizon (configured in config/horizon.php → 'path').
 * Protected by the 'web' middleware group (requires a logged-in session).
 *
 * @package App\Providers
 */
class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Route queue alert notifications — un-comment to enable:
        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('admin@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

        // Horizon::night(); // Un-comment to enable Horizon dark mode
    }

    /**
     * Register the Horizon gate.
     *
     * Controls who can view "/horizon" URL in non-local environments.
     * Grants access to users with the 'manage queues' Spatie permission.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewHorizon', function ($user) {
            return $user->can('manage queues');
        });
    }
}
