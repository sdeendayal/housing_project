<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EwsDeveloperDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'ews_developer') {
            abort(403, 'Unauthorized access to Developer dashboard.');
        }

        return view('ews.developer.dashboard', compact('user'));
    }
}
