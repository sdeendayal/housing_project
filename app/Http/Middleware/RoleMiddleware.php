<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (empty($roles)) {
            $roles = ['default'];
        }

        if (! Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            $firstRole = $roles[0];
            $loginUrl = match ($firstRole) {
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
        foreach ($roles as $role) {
            if ($role === 'citizen') {
                if ($userRole === 'citizen') {
                    $authorized = true;
                    break;
                }
            } elseif ($role === 'villager') {
                if ($userRole === 'villager') {
                    $authorized = true;
                    break;
                }
            } elseif (in_array($role, ['department', 'departmental'], true)) {
                if (!in_array($userRole, ['citizen', 'villager', 'mmgav_bdeo'], true)) {
                    $authorized = true;
                    break;
                }
            } else {
                if ($userRole === $role) {
                    $authorized = true;
                    break;
                }
            }
        }

        if (!$authorized) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }
            Auth::logout();
            $firstRole = $roles[0];
            $loginUrl = match ($firstRole) {
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
