<?php

namespace App\Http\Controllers;

use App\Models\PhysicalPossessionApplication;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
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
                $assetName = $auction->AssetName;
            }
        }

        $purchaseDate = $this->resolvePurchaseDate($auction, $purchaser);

        $paymentDetails = $this->getPaymentDetails(
            $auction?->AssetId ?? null,
            $flatCost,
            (float) ($auction?->ReceivedAmount ?? 0)
        );
        $paymentSummary = $this->resolvePaymentSummary(
            $flatCost,
            (float) ($auction?->ReceivedAmount ?? 0),
            (float) ($auction?->BalanceAmount ?? 0),
            $paymentDetails
        );

        $totalPaid = $paymentSummary['totalPaid'];
        $outstanding = $paymentSummary['outstanding'];
        $paymentProgress = $paymentSummary['paymentProgress'];

        // Clear names for dashboard payment banner
        $totalDue = $flatCost;
        $remainingBalance = $outstanding;

        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;

        $isAllotted = $auction !== null;
        $isFullyPaid = $isAllotted && $remainingBalance <= 0 && $totalDue > 0;
        $hasOutstanding = $isAllotted && $remainingBalance > 0;

        $applicationNo = $purchaser?->ApplicationNo;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');

        $displayName = $user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen');
        $flatStatus = $isAllotted
            ? ($isFullyPaid ? 'Registered' : 'Allotted')
            : ($purchaser ? 'Application Submitted' : '—');
        $category = $purchaser?->CasteCategoryName ?? '—';

        $latestPpApplication = PhysicalPossessionApplication::where('user_id', $user->id)
            ->where('status', '!=', 'draft')
            ->latest()
            ->first();

        $ppHasApplication = $latestPpApplication !== null;
        $ppHasDraftApplication = PhysicalPossessionApplication::where('user_id', $user->id)->where('status', 'draft')->exists();

        $ppMinTotalPaidRequired = 40000;
        $isPpEligible = $totalPaid >= $ppMinTotalPaidRequired;

        $applicationSections = $this->buildApplicationDetailSections(
            $user,
            $purchaser,
            $auction,
            $applicationId,
            $flatStatus,
            $purchaseDate,
            $submittedAt,
            $flatCost
        );

        return view('mmsayCitizenDashboard', [
            'displayName' => $displayName,
            'applicationId' => $applicationId,
            'purchaseDate' => $purchaseDate?->format('d M Y') ?? '—',
            'basicDetails' => $applicationSections['basicDetails'],
            'propertyDetails' => $applicationSections['propertyDetails'],
            'totalDue' => $totalDue,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance,
            'isFullyPaid' => $isFullyPaid,
            'totalAmountFormatted' => $this->formatIndianCurrency($flatCost),
            'totalPaidFormatted' => $this->formatIndianCurrency($totalPaid),
            'outstandingFormatted' => $this->formatIndianCurrency($outstanding),
            'paymentProgress' => $paymentProgress,
            'flatStatus' => $flatStatus,
            'category' => $category,
            'hasOutstanding' => $hasOutstanding,
            'assetName' => $assetName,
            'ppHasApplication' => $ppHasApplication,
            'ppHasDraftApplication' => $ppHasDraftApplication,
            'latestPpApplication' => $latestPpApplication,
            'isPpEligible' => $isPpEligible,
            'ppTotalPaid' => $totalPaid,
            'ppMinTotalPaidRequired' => $ppMinTotalPaidRequired,
            'ppTotalPaidFormatted' => $this->formatIndianCurrency($totalPaid),
            'ppMinTotalPaidRequiredFormatted' => $this->formatIndianCurrency($ppMinTotalPaidRequired),
            'installments' => $paymentDetails['installments'],
            'paymentReceipts' => $paymentDetails['receipts'],
            'installmentStats' => $paymentDetails['installmentStats'],
        ]);
    }

    // Payment status page — same data as dashboard payment section
    public function paymentStatus(): View
    {
        $user = Auth::user();
        $purchaser = $this->findPurchaserForUser($user);

        $auction = null;
        $totalPaid = 0.0;
        $outstanding = 0.0;
        $flatCost = 0.0;
        $purchaseDate = null;
        $assetName = null;

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
                $assetName = $auction->AssetName;
            }
        }

        $purchaseDate = $this->resolvePurchaseDate($auction, $purchaser);

        $paymentDetails = $this->getPaymentDetails(
            $auction?->AssetId ?? null,
            $flatCost,
            (float) ($auction?->ReceivedAmount ?? 0)
        );
        $paymentSummary = $this->resolvePaymentSummary(
            $flatCost,
            (float) ($auction?->ReceivedAmount ?? 0),
            (float) ($auction?->BalanceAmount ?? 0),
            $paymentDetails
        );
        $totalPaid = $paymentSummary['totalPaid'];
        $outstanding = $paymentSummary['outstanding'];
        $paymentProgress = $paymentSummary['paymentProgress'];

        $hasOutstanding = $auction !== null && $outstanding > 0;
        $isFullyPaid = $auction !== null && $outstanding <= 0 && $flatCost > 0;
        $applicationNo = $purchaser?->ApplicationNo;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($purchaseDate?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');

        return view('mmsayPaymentStatus', [
            'displayName' => $user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen'),
            'applicationId' => $applicationId,
            'assetName' => $assetName,
            'purchaseDate' => $purchaseDate?->format('d M Y') ?? '—',
            'totalAmountFormatted' => $this->formatIndianCurrency($flatCost),
            'totalPaidFormatted' => $this->formatIndianCurrency($totalPaid),
            'outstandingFormatted' => $this->formatIndianCurrency($outstanding),
            'paymentProgress' => $paymentProgress,
            'hasOutstanding' => $hasOutstanding,
            'isFullyPaid' => $isFullyPaid,
            'installments' => $paymentDetails['installments'],
            'paymentReceipts' => $paymentDetails['receipts'],
            'installmentStats' => $paymentDetails['installmentStats'],
        ]);
    }

    // Property details page — allotted flat / location / cost
    public function propertyDetails(): View
    {
        $user = Auth::user();
        $purchaser = $this->findPurchaserForUser($user);

        $auction = null;
        $flatCost = 0.0;

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
            }
        }

        $purchaseDate = $this->resolvePurchaseDate($auction, $purchaser);
        $submittedAt = $purchaser?->CreateDate ? Carbon::parse($purchaser->CreateDate) : null;

        $paymentDetails = $this->getPaymentDetails(
            $auction?->AssetId ?? null,
            $flatCost,
            (float) ($auction?->ReceivedAmount ?? 0)
        );
        $paymentSummary = $this->resolvePaymentSummary(
            $flatCost,
            (float) ($auction?->ReceivedAmount ?? 0),
            (float) ($auction?->BalanceAmount ?? 0),
            $paymentDetails
        );
        $outstanding = $paymentSummary['outstanding'];

        $isAllotted = $auction !== null;
        $isFullyPaid = $isAllotted && $outstanding <= 0 && $flatCost > 0;
        $flatStatus = $isAllotted
            ? ($isFullyPaid ? 'Registered' : 'Allotted')
            : ($purchaser ? 'Application Submitted' : '—');

        $applicationNo = $purchaser?->ApplicationNo;
        $applicationId = $applicationNo
            ? 'HR-MMSAY-'.($submittedAt?->format('Y') ?? now()->format('Y')).'-'.$applicationNo
            : ($purchaser?->PPPId ?? '—');

        $applicationSections = $this->buildApplicationDetailSections(
            $user,
            $purchaser,
            $auction,
            $applicationId,
            $flatStatus,
            $purchaseDate,
            $submittedAt,
            $flatCost
        );

        return view('mmsayCitizenPropertyDetails', [
            'applicationId' => $applicationId,
            'flatStatus' => $flatStatus,
            'propertyDetails' => $applicationSections['propertyDetails'],
            'hasProperty' => $auction !== null,
            'isFullyPaid' => $isFullyPaid,
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
        $idLabel = $purchaser?->MemberID ? 'Member ID' : 'Family ID';
        $idValue = $this->detailValue($purchaser?->MemberID ?? $purchaser?->PPPId);
        $category = $purchaser?->CasteCategoryName ?? '—';
        $district = $purchaser?->DistrictName ?? '—';
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
            'idLabel' => $idLabel,
            'idValue' => $idValue,
            'category' => $category,
            'district' => strtoupper($district),
            'address' => $address,
        ]);
    }

    public function downloadCashReceipt(int $receiptId): Response
    {
        $user = Auth::user();
        $receipt = $this->findCashReceiptForUser($user, $receiptId);

        if (! $receipt) {
            abort(404);
        }

        $purchaser = $this->findPurchaserForUser($user);
        $receiptNumber = $this->formatReceiptNumber($receipt->receipt_number);

        $receiptData = [
            'receipt_number' => $receiptNumber,
            'amount' => $this->formatIndianCurrency((float) $receipt->total_paid_amount),
            'amount_numeric' => number_format((float) $receipt->total_paid_amount, 2),
            'payment_date' => $receipt->created_date
                ? Carbon::parse($receipt->created_date)->format('d M Y')
                : '—',
            'purchaser_name' => strtoupper(trim($user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Citizen'))),
            'father_name' => $purchaser?->PurchaserFatherName
                ? strtoupper(trim($purchaser->PurchaserFatherName))
                : '—',
            'application_no' => $purchaser?->ApplicationNo ? (string) $purchaser->ApplicationNo : '—',
            'mobile' => $user->mobile ?? ($purchaser?->MobileNo ? (string) $purchaser->MobileNo : '—'),
            'asset_number' => (string) $receipt->asset_number,
            'asset_name' => $receipt->asset_name ?? '—',
            'em_office' => $receipt->em_office ?? '—',
            'district' => $receipt->district_name ?? '—',
            'city' => $receipt->city_name ?? '—',
            'sector' => $receipt->sector_name ?? '—',
            'mode' => 'Cash Receipt',
        ];

        $pdf = Pdf::loadView('mmsay.citizen.pdf.cash-receipt', ['receipt' => $receiptData])
            ->setPaper('a4');

        $safeNumber = preg_replace('/[^\w\-]/', '_', $receiptNumber) ?: 'receipt';

        return $pdf->download('Cash-Receipt-'.$safeNumber.'.pdf');
    }

    private function findPurchaserForUser(User $user): ?object
    {
        if ($user->private_purchaser_id) {
            return DB::table('property_private_purchasers as ppp')
                ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->select('ppp.*', 'd.DistrictName')
                ->first();
        }

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

    private function findCashReceiptForUser(User $user, int $receiptId): ?object
    {
        $purchaser = $this->findPurchaserForUser($user);

        if (! $purchaser) {
            return null;
        }

        $auction = DB::table('property_auction_detail')
            ->where('PurchaserID', $purchaser->PrivatePurchaserId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderByDesc('CreatedDate')
            ->first();

        if (! $auction) {
            return null;
        }

        return DB::table('cash_receipt_details as cr')
            ->leftJoin('em_offices as eo', 'cr.BranchId', '=', 'eo.BranchId')
            ->leftJoin('districts as d', 'cr.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'cr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'cr.SectorId', '=', 's.SectorId')
            ->leftJoin('property_registration as pr', 'cr.asset_number', '=', 'pr.AssetId')
            ->where('cr.id', $receiptId)
            ->where('cr.asset_number', $auction->AssetId)
            ->where('cr.IsDeleted', 0)
            ->where('cr.IsActive', 1)
            ->select(
                'cr.*',
                'eo.BranchName as em_office',
                'd.DistrictName as district_name',
                'c.CityName as city_name',
                's.SectorName as sector_name',
                'pr.AssetName as asset_name'
            )
            ->first();
    }

    /** @return array{basicDetails: array<int, array{label: string, value: string, full?: bool}>, propertyDetails: array<int, array{label: string, value: string, full?: bool}>} */
    private function buildApplicationDetailSections(
        User $user,
        ?object $purchaser,
        ?object $auction,
        string $applicationId,
        string $flatStatus,
        ?Carbon $purchaseDate,
        ?Carbon $submittedAt,
        float $flatCost
    ): array {
        if (! $purchaser) {
            return ['basicDetails' => [], 'propertyDetails' => []];
        }

        $displayName = $user->name ?: ($purchaser->PrivatePurchaserName ?? 'Citizen');
        $mobile = $user->mobile ?? ($purchaser->MobileNo ? (string) $purchaser->MobileNo : null);

        $basicDetails = [
            ['label' => 'Name', 'value' => $this->detailValue($displayName)],
            ['label' => "Father's Name", 'value' => $this->detailValue($purchaser->PurchaserFatherName)],
            ['label' => 'Family ID', 'value' => $this->detailValue($purchaser->PPPId)],
            ['label' => 'Member ID', 'value' => $this->detailValue($purchaser->MemberID)],
            ['label' => 'Mobile', 'value' => $this->detailValue($mobile)],
            ['label' => 'Application ID', 'value' => $this->detailValue($applicationId)],
            ['label' => 'Application No.', 'value' => $this->detailValue($purchaser->ApplicationNo)],
            ['label' => 'Category', 'value' => $this->detailValue($purchaser->CasteCategoryName)],
            ['label' => 'Marital Status', 'value' => $this->detailValue($purchaser->MaritalStatus)],
            ['label' => 'Application Date', 'value' => $submittedAt?->format('d M Y') ?? '—'],
            ['label' => 'Flat / Unit Status', 'value' => $this->detailValue($flatStatus)],
            ['label' => 'Purchase Date', 'value' => $purchaseDate?->format('d M Y') ?? '—'],
            ['label' => 'Residential Address', 'value' => $this->detailValue($purchaser->Address), 'full' => true],
        ];

        $property = $auction?->AssetId
            ? $this->findPropertyRegistration((int) $auction->AssetId)
            : null;

        $purchaserLocation = DB::table('property_private_purchasers as ppp')
            ->leftJoin('em_offices as eo', 'ppp.BranchId', '=', 'eo.BranchId')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'ppp.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'ppp.SectorId', '=', 's.SectorId')
            ->where('ppp.PrivatePurchaserId', $purchaser->PrivatePurchaserId)
            ->select('eo.BranchName', 'd.DistrictName', 'c.CityName', 's.SectorName')
            ->first();

        $location = $property ?? $purchaserLocation;
        $sectorName = $location->SectorName ?? null;
        $areaType = $this->resolveAreaType($sectorName);

        $propertyDetails = [
            ['label' => 'Flat / Unit Status', 'value' => $this->detailValue($flatStatus)],
            ['label' => 'Allotted Asset', 'value' => $this->detailValue($property->AssetName ?? $auction->AssetName ?? null)],
            ['label' => 'Asset ID', 'value' => $this->detailValue($auction->AssetId ?? null)],
            ['label' => 'Flat / Plot ID', 'value' => $this->detailValue($purchaser->Flat_Id)],
            ['label' => 'Property Size', 'value' => $this->detailValue($property->AssetSize ?? null)],
            ['label' => 'Unit', 'value' => $this->detailValue($property->Unit ?? null)],
            ['label' => 'EM Office', 'value' => $this->detailValue($location->BranchName ?? null)],
            ['label' => 'District', 'value' => $this->detailValue($location->DistrictName ?? $purchaser->DistrictName ?? null)],
            ['label' => 'City', 'value' => $this->detailValue($location->CityName ?? null)],
            ['label' => 'Area Type', 'value' => $this->detailValue($areaType)],
            ['label' => $areaType === '—' ? 'Ward / Sector / Area' : $areaType, 'value' => $this->detailValue($sectorName)],
            ['label' => 'Property Auction ID', 'value' => $this->detailValue($auction->PropertyAuctionId ?? null)],
            ['label' => 'Purchase Date', 'value' => $purchaseDate?->format('d M Y') ?? '—'],
            ['label' => 'Total Property Cost', 'value' => $flatCost > 0 ? $this->formatIndianCurrency($flatCost) : '—'],
            ['label' => 'Amount Received', 'value' => $auction && (float) $auction->ReceivedAmount > 0
                ? $this->formatIndianCurrency((float) $auction->ReceivedAmount)
                : '—'],
            ['label' => 'Balance Amount', 'value' => $auction
                ? $this->formatIndianCurrency((float) $auction->BalanceAmount)
                : '—'],
        ];

        return [
            'basicDetails' => $basicDetails,
            'propertyDetails' => $propertyDetails,
        ];
    }

    private function findPropertyRegistration(int $assetId): ?object
    {
        return DB::table('property_registration as pr')
            ->leftJoin('em_offices as eo', 'pr.BranchId', '=', 'eo.BranchId')
            ->leftJoin('districts as d', 'pr.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pr.SectorId', '=', 's.SectorId')
            ->where('pr.AssetId', $assetId)
            ->where('pr.IsDeleted', 0)
            ->select(
                'pr.*',
                'eo.BranchName',
                'd.DistrictName',
                'c.CityName',
                's.SectorName'
            )
            ->first();
    }

    private function detailValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return trim((string) $value) !== '' ? trim((string) $value) : '—';
    }

    private function resolveAreaType(?string $sectorName): string
    {
        if (! $sectorName || trim($sectorName) === '') {
            return '—';
        }

        if (preg_match('/^Ward\s/i', $sectorName)) {
            return 'Ward';
        }

        if (preg_match('/^sector\s/i', $sectorName)) {
            return 'Sector';
        }

        return 'Locality';
    }

    private function resolvePurchaseDate(?object $auction, ?object $purchaser): ?Carbon
    {
        if ($auction?->CreatedDate) {
            return Carbon::parse($auction->CreatedDate);
        }

        $assetId = $auction?->AssetId ?? null;

        if ($assetId) {
            $offerDate = DB::table('installment_due')
                ->where('AssetId', $assetId)
                ->where('IsDeleted', 0)
                ->where('IsActive', 1)
                ->whereNotNull('OfferOfPossessionDate')
                ->orderBy('InstallmentNumber')
                ->value('OfferOfPossessionDate');

            if ($offerDate) {
                return Carbon::parse($offerDate);
            }
        }

        if ($purchaser?->CreateDate) {
            return Carbon::parse($purchaser->CreateDate);
        }

        return null;
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

    /** Lump-sum / advance EMI pool: ledger total, else cash receipts. */
    private function resolveInstallmentPaymentPool(int $assetId, Collection $ledgerByNumber): array
    {
        $ledgerTotal = (float) $ledgerByNumber->sum(fn ($row) => (float) $row->Payment);

        $receiptRows = DB::table('cash_receipt_details')
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderBy('created_date')
            ->get(['total_paid_amount', 'created_date']);

        $receiptTotal = (float) $receiptRows->sum(fn ($row) => (float) $row->total_paid_amount);
        $pool = $ledgerTotal > 0 ? $ledgerTotal : $receiptTotal;

        $bulkPaymentDate = null;
        $firstReceipt = $receiptRows->first();
        if ($firstReceipt?->created_date) {
            $bulkPaymentDate = Carbon::parse($firstReceipt->created_date);
        }

        return [
            'pool' => $pool,
            'bulkPaymentDate' => $bulkPaymentDate,
        ];
    }

    private function resolveInstallmentStatus(
        float $allocated,
        float $dueAmount,
        Carbon $dueDate,
        Carbon $today
    ): string {
        if ($dueAmount > 0 && $allocated >= ($dueAmount - 0.01)) {
            return 'paid';
        }

        if ($dueDate->lt($today)) {
            return $allocated > 0 ? 'partial' : 'overdue';
        }

        return 'upcoming';
    }

    private function resolveInstallmentPaidOn(
        string $status,
        ?object $ledger,
        object $installmentRow,
        ?Carbon $bulkPaymentDate,
        float $dueAmount
    ): ?Carbon {
        if ($status !== 'paid') {
            return null;
        }

        $ledgerPayment = $ledger ? (float) $ledger->Payment : 0.0;
        $isIndividualLedgerPayment = $ledgerPayment > 0
            && $ledgerPayment <= ($dueAmount + 0.01);

        if ($isIndividualLedgerPayment) {
            if ($ledger?->CreateDate) {
                return Carbon::parse($ledger->CreateDate);
            }

            if ($installmentRow->LastSettledDate) {
                return Carbon::parse($installmentRow->LastSettledDate);
            }
        }

        if ($bulkPaymentDate) {
            return $bulkPaymentDate;
        }

        if ($installmentRow->LastSettledDate) {
            return Carbon::parse($installmentRow->LastSettledDate);
        }

        if ($ledger?->CreateDate) {
            return Carbon::parse($ledger->CreateDate);
        }

        return null;
    }

    /** @return array{totalPaid: float, outstanding: float, paymentProgress: int} */
    private function resolvePaymentSummary(
        float $flatCost,
        float $fallbackPaid,
        float $fallbackOutstanding,
        array $paymentDetails
    ): array {
        $installments = $paymentDetails['installments'];

        // dd($installments);

        if ($installments->isEmpty()) {
            $totalPaid = $fallbackPaid;
            $outstanding = $fallbackOutstanding;
        } else {
            $installmentPayments = (float) ($paymentDetails['installmentPaidTotal'] ?? 0);
            $totalPaid = $fallbackPaid + $installmentPayments;
            $outstanding = $flatCost > 0 ? max(0.0, $flatCost - $totalPaid) : 0.0;
        }

        $paymentProgress = $flatCost > 0
            ? (int) min(100, round(($totalPaid / $flatCost) * 100))
            : 0;



        return [
            'totalPaid' => $totalPaid,
            'outstanding' => $outstanding,
            'paymentProgress' => $paymentProgress,
        ];
    }

    /** Bacha hua balance: FlatCost minus initial deposit minus actual ledger payments. */
    private function remainingFlatBalance(float $flatCost, float $receivedAmount, $ledgerRows): float
    {
        if ($flatCost <= 0) {
            return 0.0;
        }

        $installmentPayments = (float) collect($ledgerRows)->sum(fn ($row) => (float) $row->Payment);

        return max(0.0, round($flatCost - $receivedAmount - $installmentPayments, 2));
    }

    /** 36vi kist = poora bacha hua amount; kam jama nahi — closing balance to reach FlatCost. */
    private function emiAmountForInstallment(
        $row,
        $ledger,
        float $remainingBalance,
        int $lastInstallmentNumber,
        bool $isPaid
    ): float {
        if ((int) $row->InstallmentNumber === $lastInstallmentNumber) {
            if ($isPaid && $ledger) {
                return (float) $ledger->Payment;
            }

            return $remainingBalance;
        }

        return (float) $row->EMIAmount;
    }

    /** @return array{installments: Collection, receipts: Collection, installmentStats: array{total: int, paid: int, overdue: int, upcoming: int}} */
    private function getPaymentDetails(?int $assetId, float $flatCost = 0.0, float $receivedAmount = 0.0): array
    {
        if (! $assetId) {
            return [
                'installments' => collect(),
                'receipts' => collect(),
                'installmentStats' => ['total' => 0, 'paid' => 0, 'overdue' => 0, 'upcoming' => 0],
                'installmentPaidTotal' => 0.0,
            ];
        }

        $ledgerByNumber = DB::table('ledger')
            ->where('AssetId', $assetId)
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->get()
            ->keyBy('InstallmentNumber');

        $installmentRows = DB::table('installment_due')
            ->where('AssetId', $assetId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderBy('InstallmentNumber')
            ->get();

        $lastInstallmentNumber = (int) $installmentRows->max('InstallmentNumber');
        $paymentPool = $this->resolveInstallmentPaymentPool($assetId, $ledgerByNumber);
        $installmentPaidTotal = $paymentPool['pool'];
        $bulkPaymentDate = $paymentPool['bulkPaymentDate'];
        $remainingPool = $installmentPaidTotal;
        $remainingBalance = $this->remainingFlatBalance($flatCost, $receivedAmount, $ledgerByNumber);

        $installments = $installmentRows->map(function ($row) use (
            $ledgerByNumber,
            $remainingBalance,
            $lastInstallmentNumber,
            $bulkPaymentDate,
            &$remainingPool
        ) {
                $ledger = $ledgerByNumber->get($row->InstallmentNumber);
                $dueDate = Carbon::parse($row->DueDate);
                $today = Carbon::today();

                $dueAmount = (float) $row->DueAmount;

                $allocated = min($remainingPool, max(0.0, $dueAmount));
                $remainingPool -= $allocated;

                $status = $this->resolveInstallmentStatus($allocated, $dueAmount, $dueDate, $today);

                $paidOn = $this->resolveInstallmentPaidOn(
                    $status,
                    $ledger,
                    $row,
                    $bulkPaymentDate,
                    $dueAmount
                );

                $emiAmount = $this->emiAmountForInstallment(
                    $row,
                    $ledger,
                    $remainingBalance,
                    $lastInstallmentNumber,
                    $status === 'paid'
                );
                $totalDue = (int) $row->InstallmentNumber === $lastInstallmentNumber
                    ? $emiAmount
                    : (float) $row->DueAmount;

                return (object) [
                    'installment_number' => (int) $row->InstallmentNumber,
                    'due_date_formatted' => $dueDate->format('d M Y'),
                    'emi_amount' => $emiAmount,
                    'emi_formatted' => $this->formatIndianCurrency($emiAmount),
                    'principal' => (float) $row->PrincipleAmount,
                    'interest' => (float) $row->InterestAmount,
                    'gst' => (float) $row->GSTAmount,
                    'total_due' => $totalDue,
                    'total_due_formatted' => $this->formatIndianCurrency($totalDue),
                    'balance_after' => (float) $row->RunningClosingBalance,
                    'balance_after_formatted' => $this->formatIndianCurrency((float) $row->RunningClosingBalance),
                    'paid_on_formatted' => $paidOn?->format('d M Y') ?? '—',
                    'status' => $status,
                    'status_label' => match ($status) {
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'partial' => 'Partial',
                        default => 'Upcoming',
                    },
                ];
            });

        $installmentStats = [
            'total' => $installments->count(),
            'paid' => $installments->where('status', 'paid')->count(),
            'overdue' => $installments->whereIn('status', ['overdue', 'partial'])->count(),
            'upcoming' => $installments->where('status', 'upcoming')->count(),
        ];

        $receipts = DB::table('cash_receipt_details')
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderBy('created_date')
            ->get()
            ->map(function ($row) {
                $date = $row->created_date ? Carbon::parse($row->created_date) : null;

                return (object) [
                    'id' => (int) $row->id,
                    'receipt_number' => $this->formatReceiptNumber($row->receipt_number),
                    'date_formatted' => $date?->format('d M Y') ?? '—',
                    'amount_formatted' => $this->formatIndianCurrency((float) $row->total_paid_amount),
                    'mode' => 'Cash Receipt',
                ];
            });

        return [
            'installments' => $installments,
            'receipts' => $receipts,
            'installmentStats' => $installmentStats,
            'installmentPaidTotal' => $installmentPaidTotal,
        ];
    }

    private function formatReceiptNumber(?string $value): string
    {
        if (! $value || trim($value) === '') {
            return '—';
        }

        $clean = trim($value);

        // Only expand Excel-style scientific notation (e.g. 1.23E+11), not text like "RC-EMI-1"
        if (preg_match('/^\d+(\.\d+)?[eE][+\-]?\d+$/', $clean)) {
            return number_format((float) $clean, 0, '.', '');
        }

        return $clean;
    }

}
