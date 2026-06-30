<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MMGAYMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            if ($request->is('mmgav/*')) {
                return redirect()->route('mmgav.villager.login');
            }

            return redirect()->route('mmgay.login');
        }

        if (Auth::user()->scheme !== 'MMGAY') {
            if ($request->is('mmgav/*')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('mmgav.villager.login')->with('error', 'Unauthorized access.');
            }

            abort(403);
        }

        return $next($request);
    }
}