<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DistrictOfficerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('district_officer')->check()) {
            return redirect()->route('pp.department.login')
                ->with('error', 'Please login as District Officer first.');
        }

        $officer = Auth::guard('district_officer')->user();

        if (! $officer->is_active) {
            Auth::guard('district_officer')->logout();

            return redirect()->route('pp.department.login')
                ->with('error', 'Your officer account is inactive.');
        }

        return $next($request);
    }
}
