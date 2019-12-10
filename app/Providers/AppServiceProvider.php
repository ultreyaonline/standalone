<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // dev utilities
        if ($this->app->isLocal() || $this->app->environment('test')) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('currency', function ($attribute, $value, $parameters) {
            return preg_match("/^[\d.,]+$/", $value);
        });

        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)$/', $value);
        });

        // @TODO - verify that this always handles special CSV cases such as quoted-apostrophe strings and commas in strings
        // CSV Macro: allow exporting a collection to CSV with <a download="sales.csv" href="{{ $sales->toInlineCsv(['Heading1', 'Heading2']) }}" >Download</a>
        // ref https://stefanzweifel.io/posts/convert-a-collection-to-a-downloadable-csv
        Collection::macro('toInlineCsv', function (array $headers = null) {
            $csvString = $this->map(function ($value, $key) {
                return is_array($value) ? implode(',', $value) : implode(',', [$key, $value]);
            });

            if (!empty($headers)) {
                $csvString = $csvString->prepend(implode(',', $headers));
            }

            $csvString = $csvString->implode("\n");

            $encodedCsvString = strtr(
                rawurlencode($csvString),
                ['%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')', '%22%22' => '"']
            );

            return 'data:attachment/csv;charset=utf-8,' . $encodedCsvString;
        });
    }
}
