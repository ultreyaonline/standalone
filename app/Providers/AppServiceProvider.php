<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Class AppServiceProvider
 *
 * The primary application service provider. Handles:
 * - Conditional registration of third-party service providers (Debugbar)
 * - Custom Validator rules
 * - Pagination theme configuration
 * - Collection macro extensions
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Debugbar and IDE Helper are registered in local/test environments only, never production.
     *
     */
    public function register(): void
    {
        // dev utilities — never loaded in production
        if ($this->app->isLocal() || $this->app->environment('test')) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * Registered here:
     * - 'currency' validator: accepts digits, commas, and periods (e.g. "1,250.00")
     * - 'slug' validator: lowercase alphanumeric with hyphens
     * - Bootstrap 4 pagination
     * - Collection::toInlineCsv() macro for generating data-URI CSV download links
     *
     */
    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(7);
        });

        // Validates that a value contains only currency-safe characters: digits, commas, periods

        Validator::extend('currency', function ($attribute, $value, $parameters) {
            return preg_match("/^[\d.,]+$/", $value);
        });

        // Validates that a value is a URL-safe slug: lowercase alphanumeric with a single hyphen separator
        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)$/', $value);
        });

        // Use Bootstrap pagination markup
        // @TODO: Switch to useBootstrapFive() when upgrading to Bootstrap 5
        Paginator::useBootstrapFour();

        /**
         * Collection macro: toInlineCsv
         *
         * Converts a Collection to a base64-encoded CSV data URI, suitable for use
         * directly in an HTML <a href="..."> download link without a server round-trip.
         *
         * Usage in Blade:
         *   <a download="export.csv" href="{{ $collection->toInlineCsv(['Heading1', 'Heading2']) }}">Download</a>
         *
         * The collection items should be arrays or key-value pairs. The $headers array
         * provides the CSV column header row.
         *
         * Reference: https://stefanzweifel.io/posts/convert-a-collection-to-a-downloadable-csv
         */
        Collection::macro('toInlineCsv', function (array $headers = []) {
            $csvString = $this->map(function ($value, $key) {
                return is_array($value) ? implode(',', $value) : implode(',', [$key, $value]);
            })
                ->prepend(implode(',', $headers))
                ->implode("\n");

            $encodedCsvString = strtr(
                rawurlencode($csvString),
                ['%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')', '%22%22' => '"']
            );

            return 'data:attachment/csv;charset=utf-8,' . $encodedCsvString;
        });


    }
}
