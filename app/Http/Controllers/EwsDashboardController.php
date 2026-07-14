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
        $flatDetails = null;

        // Extract EWS ID from the user email (seeded as ews_{id}@gmail.com)
        if (preg_match('/ews_(\d+)/', $user->email, $matches)) {
            $ewsId = $matches[1];
            $flatDetails = DB::table('aws_flats_crid')->where('Id', $ewsId)->first();
        }

        // Fallback: search by mobile number/name if email did not match
        if (!$flatDetails) {
            $flatDetails = DB::table('aws_flats_crid')
                ->where('Member_ID', $user->role)
                ->orWhere('membername', $user->name)
                ->first();
        }

        return view('ews.dashboard', compact('flatDetails'));
    }
}
