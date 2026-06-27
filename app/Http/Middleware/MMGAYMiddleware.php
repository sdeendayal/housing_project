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
        if (!Auth::check()) {
            return redirect()->route('mmgay.login');
        }

        if (Auth::user()->scheme !== 'MMGAY') {
            abort(403);
        }

        return $next($request);
    }
}