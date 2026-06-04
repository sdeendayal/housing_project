<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Citizen routes — separate login flow
        if ($role === 'citizen') {
            if (! Auth::check()) {
                return redirect()->guest('/mmsay-citizen-login');
            }

            if (! Auth::user()->belongsToRoleGroup('citizen') && Auth::user()->role !== 'citizen') {
                Auth::logout();

                return redirect('/mmsay-citizen-login')->with('error', 'Unauthorized access.');
            }

            return $next($request);
        }

        // Department routes — original behavior unchanged
        if (! Auth::check()) {
            return redirect('/mmsay-department-login');
        }

        if (Auth::user()->role !== $role) {
            Auth::logout();

            return redirect('/mmsay-department-login')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}
