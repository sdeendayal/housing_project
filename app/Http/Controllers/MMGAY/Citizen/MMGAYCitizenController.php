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

        return view('mmgay.citizen.dashboard', compact('user', 'ownerInfo'));
    }
}
