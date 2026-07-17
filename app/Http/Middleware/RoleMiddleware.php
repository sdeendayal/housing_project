<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (! Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            $loginUrl = match ($role) {
                'citizen' => route('citizen.login'),
                'villager' => route('mmgav.villager.login'),
                'ews_user' => route('ews.citizen.login'),
                'ews_developer' => route('ews.developer.login'),
                'ews_department' => route('ews.department.login'),
                default => route('pp.department.login'),
            };
            return redirect()->guest($loginUrl);
        }

        $user = Auth::user();
        $userRole = $user->roleSlug();

        $authorized = false;
        if ($role === 'citizen') {
            $authorized = ($userRole === 'citizen');
        } elseif ($role === 'villager') {
            $authorized = ($userRole === 'villager');
        } elseif (in_array($role, ['department', 'departmental'], true)) {
            $authorized = !in_array($userRole, ['citizen', 'villager', 'mmgav_bdeo'], true);
        } else {
            $authorized = ($userRole === $role);
        }

        if (!$authorized) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }
            Auth::logout();
            $loginUrl = match ($role) {
                'citizen' => route('citizen.login'),
                'villager' => route('mmgav.villager.login'),
                'ews_user' => route('ews.citizen.login'),
                'ews_developer' => route('ews.developer.login'),
                'ews_department' => route('ews.department.login'),
                default => route('pp.department.login'),
            };
            return redirect($loginUrl)->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
