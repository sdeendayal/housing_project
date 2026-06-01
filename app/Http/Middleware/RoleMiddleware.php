<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/mmsay-department-login');
        }

        if (Auth::user()->role !== $role) {
            Auth::logout();
            return redirect('/mmsay-department-login')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}