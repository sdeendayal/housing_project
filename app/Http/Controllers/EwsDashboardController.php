<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EwsDashboardController extends Controller
{
    /**
     * Display EWS User Dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        $ewsData = DB::table('all_ews_data_1')
            ->where('mobile_number', $user->mobile)
            ->first();

        if (!$ewsData) {
            $ewsData = DB::table('all_ews_data_1')
                ->where('full_name', $user->name)
                ->first();
        }

        return view('ews.dashboard', compact('ewsData'));
    }
}
