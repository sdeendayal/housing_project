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

        $mobile = $ewsData ? ($ewsData->mobile_number ?? $user->mobile) : $user->mobile;

        // Query status checks across all EWS tables
        $pppExclusion = DB::table('ews_reject_ppp_exclusion_2')->where('mobile_number', $mobile)->first();
        $propertyReject = DB::table('ews_reject_property_in_india_3')->where('mobile_number', $mobile)->first();
        $houseReject = DB::table('ews_house_ownership_reject_4')->where('mobile_number', $mobile)->first();
        
        $eligibleDraw = DB::table('ews_eligible_draw_list_5')->where('mobile_number', $mobile)->first();
        $booking = DB::table('all_ews_data_544')
            ->where('Paid', 'Paid')
            ->where(function($q) use ($mobile) {
                $q->where('MobileNo', $mobile)
                  ->orWhere('MobileNo_2', $mobile);
            })
            ->first();
        $eligibleFinal = DB::table('ews_eligible_6')->where('mobile_number', $mobile)->first();
        $allotted = DB::table('all_ews_data_544')
            ->where('Allotment', 'alloted')
            ->where(function($q) use ($mobile) {
                $q->where('MobileNo', $mobile)
                  ->orWhere('MobileNo_2', $mobile);
            })
            ->first();
        if ($allotted) {
            $allotted->flat_no = $allotted->Flat_PlotNo ?? $allotted->Flat_plotno_2 ?? null;
        }
        $waiting = DB::table('ews_waiting_list_9')->where('mobile_number', $mobile)->first();

        return view('ews.dashboard', compact(
            'ewsData',
            'pppExclusion',
            'propertyReject',
            'houseReject',
            'eligibleDraw',
            'booking',
            'eligibleFinal',
            'allotted',
            'waiting'
        ));
    }
}
