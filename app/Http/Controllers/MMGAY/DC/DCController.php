<?php

namespace App\Http\Controllers\MMGAY\DC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DCController extends Controller
{
    public function dashboard($phase = 1)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('mmgay.login');
        }

        // Get District Id
        $districtId = DB::table('DistrictMaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            abort(404, 'District not found.');
        }

        $query = DB::table('OwnerMaster')
            ->where('DistrictId', $districtId)
            ->where('Phase', $phase);

        $total = (clone $query)->count();

        $approved = (clone $query)
            ->where('IsApproved', 1)
            ->count();

        $rejected = (clone $query)
            ->where('IsRejected', 1)
            ->count();

        $pending = (clone $query)
            ->where('IsApproved', 0)
            ->where('IsRejected', 0)
            ->count();

        $paid = (clone $query)
            ->where('IsPaid', 1)
            ->count();

        $reconsidered = (clone $query)
            ->where('IsDcReconsidered', 1)
            ->count();

        return view('mmgay.dc.dashboard', compact(
            'phase',
            'total',
            'pending',
            'approved',
            'rejected',
            'paid',
            'reconsidered'
        ));
    }

    public function ownerList()
    {
        return view('mmgay.dc.owner-list');
    }

    public function ownerView($id)
    {
        return view('mmgay.dc.owner-view');
    }
}
