<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    public function handle($request, Closure $next, $guard = 'api')
    {
        Auth::setDefaultDriver($guard);
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['code' => 401,'message' => 'Unauthorized.'])->setStatusCode(401);
            } else {
                return response()->json(['code' => 403,'message' => '403'])->setStatusCode(403);
            }
        }
        return $next($request);
    }
}
