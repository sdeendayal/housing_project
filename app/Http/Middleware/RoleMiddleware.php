<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if ($role === 'citizen') {
            return $this->guardRoleGroup($request, $next, 'citizen', route('citizen.login'));
        }

        if (in_array($role, ['department', 'departmental'], true)) {
            return $this->guardRoleGroup($request, $next, 'department', route('pp.department.login'));
        }

        if ($role === 'district_officer') {
            return $this->guardRoleSlug($request, $next, 'district_officer', route('pp.department.login'));
        }

        // Legacy department email/password routes
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role && ! Auth::user()->hasRole($role)) {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }

    private function guardRoleGroup(Request $request, Closure $next, string $groupSlug, string $loginUrl)
    {
        if (! Auth::check()) {
            return redirect()->guest($loginUrl);
        }

        $user = Auth::user();

        if (! $user->belongsToRoleGroup($groupSlug)) {
            Auth::logout();

            return redirect($loginUrl)->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }

    private function guardRoleSlug(Request $request, Closure $next, string $roleSlug, string $loginUrl)
    {
        if (! Auth::check()) {
            return redirect()->guest($loginUrl);
        }

        $user = Auth::user();

        if (! $user->hasRole($roleSlug)) {
            Auth::logout();

            return redirect($loginUrl)->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
