<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GrievanceController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if ($user->private_purchaser_id) {
            $purchaser = DB::table('property_private_purchasers as ppp')
                ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->select('ppp.*', 'd.DistrictName')
                ->first();
        } else {
            $mobile = $user->mobile;
            $purchaser = null;

            if ($mobile) {
                $variants = array_unique([$mobile, '91'.$mobile, (int) $mobile]);

                $purchaser = DB::table('property_private_purchasers as ppp')
                    ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                    ->where('ppp.IsActive', 1)
                    ->where('ppp.IsDeleted', 0)
                    ->where(function ($query) use ($variants, $mobile) {
                        $query->whereIn('ppp.MobileNo', $variants)
                            ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
                    })
                    ->select('ppp.*', 'd.DistrictName')
                    ->orderBy('ppp.PrivatePurchaserId')
                    ->first();
            }
        }

        $applicationNo = $purchaser?->ApplicationNo;
        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');
        $displayName = trim($user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen'));
        $mobileNumber = (string) ($user->mobile ?? ($purchaser?->MobileNo ?? ''));

        $grievances = Grievance::query()
            ->where('mobile_number', $mobileNumber)
            ->latest()
            ->get();

        return view('grievances.index', [
            'grievances' => $grievances,
            'displayName' => $displayName,
            'applicationId' => $applicationId,
        ]);
    }

    public function create(): View
    {
        $user = Auth::user();

        if ($user->private_purchaser_id) {
            $purchaser = DB::table('property_private_purchasers as ppp')
                ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->select('ppp.*', 'd.DistrictName')
                ->first();
        } else {
            $mobile = $user->mobile;
            $purchaser = null;

            if ($mobile) {
                $variants = array_unique([$mobile, '91'.$mobile, (int) $mobile]);

                $purchaser = DB::table('property_private_purchasers as ppp')
                    ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                    ->where('ppp.IsActive', 1)
                    ->where('ppp.IsDeleted', 0)
                    ->where(function ($query) use ($variants, $mobile) {
                        $query->whereIn('ppp.MobileNo', $variants)
                            ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
                    })
                    ->select('ppp.*', 'd.DistrictName')
                    ->orderBy('ppp.PrivatePurchaserId')
                    ->first();
            }
        }

        $applicationNo = $purchaser?->ApplicationNo;
        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');
        $displayName = trim($user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen'));

        return view('grievances.create', [
            'displayName' => $displayName,
            'applicationId' => $applicationId,
            'subjectOptions' => Grievance::SUBJECT_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'grievance_subject' => ['required', 'string', Rule::in(Grievance::SUBJECT_OPTIONS)],
            'grievance_description' => 'required|string|max:2000',
        ], [
            'grievance_subject.required' => 'Please select a grievance subject.',
            'grievance_subject.in' => 'Please select a valid grievance subject.',
            'grievance_description.required' => 'Please enter grievance description.',
        ]);

        $user = Auth::user();

        if ($user->private_purchaser_id) {
            $purchaser = DB::table('property_private_purchasers as ppp')
                ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->select('ppp.*', 'd.DistrictName')
                ->first();
        } else {
            $mobile = $user->mobile;
            $purchaser = null;

            if ($mobile) {
                $variants = array_unique([$mobile, '91'.$mobile, (int) $mobile]);

                $purchaser = DB::table('property_private_purchasers as ppp')
                    ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                    ->where('ppp.IsActive', 1)
                    ->where('ppp.IsDeleted', 0)
                    ->where(function ($query) use ($variants, $mobile) {
                        $query->whereIn('ppp.MobileNo', $variants)
                            ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
                    })
                    ->select('ppp.*', 'd.DistrictName')
                    ->orderBy('ppp.PrivatePurchaserId')
                    ->first();
            }
        }

        $applicationNo = $purchaser?->ApplicationNo;
        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');
        $applicantName = trim($user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen'));
        $mobileNumber = (string) ($user->mobile ?? ($purchaser?->MobileNo ?? ''));
        $assetId = null;

        if ($purchaser) {
            $auction = DB::table('property_auction_detail')
                ->where('PurchaserID', $purchaser->PrivatePurchaserId)
                ->where('IsDeleted', 0)
                ->where('IsActive', 1)
                ->orderByDesc('CreatedDate')
                ->first();

            $assetId = $auction?->AssetId ? (int) $auction->AssetId : null;
        }

        $grievance = Grievance::create([
            'application_id' => $applicationId,
            'applicant_name' => $applicantName,
            'mobile_number' => $mobileNumber,
            'asset_id' => $assetId,
            'district_id' => $purchaser?->DistrictId ? (int) $purchaser->DistrictId : null,
            'district' => $purchaser?->DistrictName ?? null,
            'grievance_subject' => $validated['grievance_subject'],
            'grievance_description' => $validated['grievance_description'],
            'grievance_status' => Grievance::STATUS_PENDING,
        ]);

        return redirect()
            ->route('citizen.grievances.show', $grievance)
            ->with('success', 'Your grievance has been submitted successfully. Ticket No: '.$grievance->ticket_number);
    }

    public function show(Grievance $grievance): View
    {
        $user = Auth::user();

        if ($user->private_purchaser_id) {
            $purchaser = DB::table('property_private_purchasers as ppp')
                ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->select('ppp.*', 'd.DistrictName')
                ->first();
        } else {
            $mobile = $user->mobile;
            $purchaser = null;

            if ($mobile) {
                $variants = array_unique([$mobile, '91'.$mobile, (int) $mobile]);

                $purchaser = DB::table('property_private_purchasers as ppp')
                    ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                    ->where('ppp.IsActive', 1)
                    ->where('ppp.IsDeleted', 0)
                    ->where(function ($query) use ($variants, $mobile) {
                        $query->whereIn('ppp.MobileNo', $variants)
                            ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
                    })
                    ->select('ppp.*', 'd.DistrictName')
                    ->orderBy('ppp.PrivatePurchaserId')
                    ->first();
            }
        }

        $applicationNo = $purchaser?->ApplicationNo;
        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');
        $displayName = trim($user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen'));
        $mobileNumber = (string) ($user->mobile ?? ($purchaser?->MobileNo ?? ''));

        if ($grievance->mobile_number !== $mobileNumber) {
            abort(404);
        }

        return view('grievances.show', [
            'grievance' => $grievance,
            'displayName' => $displayName,
            'applicationId' => $applicationId,
        ]);
    }
}
