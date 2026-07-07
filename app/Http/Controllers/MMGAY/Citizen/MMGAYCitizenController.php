<?php

namespace App\Http\Controllers\MMGAY\Citizen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MMGAYCitizenController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('mmgav.villager.login');
        }

        $ownerInfo = null;

        if (Schema::hasTable('ownermaster')) {
            $ownerInfo = DB::table('ownermaster')
                ->leftJoin('blockmaster', 'ownermaster.BlockId', '=', 'blockmaster.BlockId')
                ->leftJoin('villagemaster', 'ownermaster.VillageId', '=', 'villagemaster.VillageId')
                ->leftJoin('districtmaster', 'ownermaster.DistrictId', '=', 'districtmaster.DistrictId')
                ->leftJoin('flatmaster', 'ownermaster.FlatId', '=', 'flatmaster.FlatId')
                ->select(
                    'ownermaster.*',
                    'blockmaster.BlockName',
                    'villagemaster.VillageName',
                    'districtmaster.DistrictName',
                    'flatmaster.FlatNo'
                )
                ->where('ownermaster.MobileNo', $user->mobile)
                ->first();
        }

        $possessionApplication = null;
        $logs = [];
        if ($ownerInfo) {
            $possessionApplication = DB::table('mmgay_possession_applications')
                ->where('owner_id', $ownerInfo->OwnerId)
                ->first();

            if ($possessionApplication) {
                $logs = DB::table('mmgay_possession_status_logs')
                    ->where('application_id', $possessionApplication->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('mmgay.citizen.dashboard', compact('user', 'ownerInfo', 'possessionApplication', 'logs'));
    }
}
