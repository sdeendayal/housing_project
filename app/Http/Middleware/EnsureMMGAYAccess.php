<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureMMGAYAccess
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('mmgay.login')
                ->with('error', 'Please login first.');
        }

        $user = Auth::user();

        if ($user->scheme !== 'MMGAY') {
            Auth::logout();
            return redirect()->route('mmgay.login')
                ->with('error', 'Unauthorized scheme access.');
        }

        if ($user->Is_Active != 1 || $user->Is_Deleted != 0) {
            Auth::logout();
            return redirect()->route('mmgay.login')
                ->with('error', 'Account inactive.');
        }

        return $next($request);
    }
}