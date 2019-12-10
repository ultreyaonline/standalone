<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class RedirectNonWwwUrls
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Str::startsWith($request->header('host'), 'www.')) {
            $host = str_replace('www.', '', $request->header('host'));
            $request->headers->set('host', $host);

            return redirect($request->fullUrl());
        }

        return $next($request);
    }
}
