<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Cache;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIPWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Cache::store('memcached')->has($request->ip())) {
            return $next($request);
        } else {
            return response()->json(['error' => __('error.Unauthorized. IP not whitelisted')]);
        }

    }
}
