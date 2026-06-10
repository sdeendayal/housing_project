<?php

namespace App\Http\Controllers;

use App\Models\PhysicalPossessionApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CitizenAuthController extends Controller
{
    // Citizen dashboard — logged-in user data from DB (no helpers / view composers)
    public function dashboard(): View
    {
        $user = Auth::user();
        $purchaser = $this->findPurchaserForUser($user);

        $auction = null;
        $assetName = null;
        $totalPaid = 0.0;
        $outstanding = 0.0;
        $flatCost = 0.0;
        $purchaseDate = null;

        if ($purchaser) {
            $auction = DB::table('property_auction_detail as pad')
                ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
                ->where('pad.PurchaserID', $purchaser->PrivatePurchaserId)
                ->where('pad.IsDeleted', 0)
                ->where('pad.IsActive', 1)
                ->select('pad.*', 'pr.AssetName')
                ->orderByDesc('pad.CreatedDate')
                ->first();

            if ($auction) {
                $flatCost = (float) $auction->FlatCost;
                $totalPaid = (float) $auction->ReceivedAmount;
                $outstanding = (float) $auction->BalanceAmount;
                $assetName = $auction->AssetName;
                $purchaseDate = $auction->CreatedDate
                    ? Carbon::parse($auction->CreatedDate)
                    : null;
            }
        }

        // dd($auction);

        $paymentProgress = $flatCost > 0
            ? (int) min(100, round(($totalPaid / $flatCost) * 100))
            : 0;

        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;

        $isAllotted = $auction !== null;
        $isFullyPaid = $isAllotted && $outstanding <= 0 && $flatCost > 0;
        $hasOutstanding = $isAllotted && $outstanding > 0;

        $applicationNo = $purchaser?->ApplicationNo;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');

        $displayName = $user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen');
        $flatStatus = $isAllotted
            ? ($isFullyPaid ? 'Registered' : 'Allotted')
            : ($purchaser ? 'Application Submitted' : '—');
        $category = $purchaser?->CasteCategoryName ?? '—';

        $ppStats = [
            'total' => PhysicalPossessionApplication::where('user_id', $user->id)->count(),
            'pending' => PhysicalPossessionApplication::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => PhysicalPossessionApplication::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => PhysicalPossessionApplication::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        $ppRecentApplications = PhysicalPossessionApplication::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        $ppHasApplication = $ppStats['total'] > 0;
        $latestPpApplication = $ppHasApplication ? $ppRecentApplications->first() : null;

        return view('mmsayCitizenDashboard', [
            'displayName' => $displayName,
            'applicationId' => $applicationId,
            'purchaseDate' => $purchaseDate?->format('d M Y') ?? '—',
            'totalPaidFormatted' => $this->formatIndianCurrency($totalPaid),
            'outstandingFormatted' => $this->formatIndianCurrency($outstanding),
            'paymentProgress' => $paymentProgress,
            'flatStatus' => $flatStatus,
            'category' => $category,
            'hasOutstanding' => $hasOutstanding,
            'assetName' => $assetName,
            'ppStats' => $ppStats,
            'ppRecentApplications' => $ppRecentApplications,
            'ppHasApplication' => $ppHasApplication,
            'latestPpApplication' => $latestPpApplication,
        ]);
    }

    // Citizen profile — logged-in user data from DB
    public function profile(): View
    {
        $user = Auth::user();
        $purchaser = $this->findPurchaserForUser($user);

        $applicationNo = $purchaser?->ApplicationNo;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');

        $fullName = strtoupper(trim($user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen')));
        $fatherName = $purchaser?->PurchaserFatherName
            ? strtoupper(trim($purchaser->PurchaserFatherName))
            : '—';
        $mobile = $user->mobile ?? ($purchaser?->MobileNo ? (string) $purchaser->MobileNo : '—');
        $aadhaarMasked = $this->maskIdentifier($purchaser?->MemberID ?? $purchaser?->PPPId);
        $category = $purchaser?->CasteCategoryName ?? '—';
        $district = $purchaser?->DistrictName ?? '—';
        $annualIncome = '—';
        $address = $purchaser?->Address ? strtoupper(trim($purchaser->Address)) : '—';

        $isActive = $purchaser
            && (int) $purchaser->IsActive === 1
            && (int) $purchaser->IsDeleted === 0
            && (int) ($purchaser->Is_UserLogin_Active ?? 1) === 1
            && (int) ($purchaser->Is_UserLogin_Deleted ?? 0) === 0;

        return view('mmsayCitizenProfile', [
            'fullName' => $fullName,
            'applicationId' => $applicationId,
            'applicationNo' => $applicationNo ? (string) $applicationNo : '—',
            'accountStatus' => $isActive ? 'Active' : 'Inactive',
            'fatherName' => $fatherName,
            'mobile' => $mobile,
            'aadhaarMasked' => $aadhaarMasked,
            'category' => $category,
            'district' => strtoupper($district),
            'annualIncome' => $annualIncome,
            'address' => $address,
        ]);
    }

    private function findPurchaserForUser(User $user): ?object
    {
        $mobile = $user->mobile;

        if (! $mobile) {
            return null;
        }

        $variants = array_unique([
            $mobile,
            '91'.$mobile,
            (int) $mobile,
        ]);

        return DB::table('property_private_purchasers as ppp')
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

    private function maskIdentifier(?string $value): string
    {
        if (! $value || trim($value) === '') {
            return '—';
        }

        $clean = preg_replace('/\s+/', '', $value);
        $lastFour = strlen($clean) >= 4 ? substr($clean, -4) : $clean;

        return 'XXXX-XXXX-'.$lastFour;
    }

    private function formatIndianCurrency(float $amount): string
    {
        if ($amount <= 0) {
            return '₹ 0';
        }

        $rounded = (int) round($amount);
        $lastThree = substr((string) $rounded, -3);
        $rest = substr((string) $rounded, 0, -3);
        if ($rest !== '') {
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        }

        return '₹ '.($rest ? $rest.',' : '').$lastThree;
    }

}
