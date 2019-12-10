<?php

namespace App\Http\Middleware;

use Closure;

class RoutePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return auth()->user()->hasPermissionForRoute($request->route())
            ? $next($request)
            : redirect()->back()->withErrors(["You don't have access to the requested page."]);
    }
}
