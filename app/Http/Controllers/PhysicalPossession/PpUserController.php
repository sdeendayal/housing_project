<?php

namespace App\Http\Controllers\PhysicalPossession;

use App\Http\Controllers\Controller;
use App\Services\PhysicalPossessionAssetService;
use App\Models\ApplicationStatusLog;
use App\Models\PhysicalPossessionApplication;
use App\Models\PhysicalPossessionDocument;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PpUserController extends Controller
{
    public function __construct(
        private PhysicalPossessionAssetService $assetService
    ) {}

    // User dashboard
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $this->getUserProfile($user);

        $applications = PhysicalPossessionApplication::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total' => PhysicalPossessionApplication::where('user_id', $user->id)->count(),
            'pending' => PhysicalPossessionApplication::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => PhysicalPossessionApplication::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => PhysicalPossessionApplication::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('physical-possession.user.dashboard', compact('user', 'profile', 'applications', 'stats'));
    }

    // Apply form - user details auto fill
    public function applyForm()
    {
        $user = Auth::user();

        $existing = $this->findUserApplication($user);
        if ($existing) {
            return redirect()->route('pp.user.application.show', $existing)
                ->with('warning', 'You have already submitted an application. You cannot apply again.');
        }

        $profile = $this->getUserProfile($user);

        return view('physical-possession.user.apply', compact('user', 'profile'));
    }

    // Pre-filled form — view in browser first
    public function viewPrefilledForm()
    {
        $user = Auth::user();

        if ($this->findUserApplication($user)) {
            return redirect()->route('pp.user.applications')
                ->with('error', 'Form is not available after application submission.');
        }

        $profile = $this->getUserProfile($user);

        return view('physical-possession.user.view-prefilled-form', compact('user', 'profile'));
    }

    // Pre-filled PDF download (after view)
    public function downloadPrefilledForm()
    {
        $user = Auth::user();

        if ($this->findUserApplication($user)) {
            return redirect()->route('pp.user.applications')
                ->with('error', 'Form download is not available after application submission.');
        }

        $profile = $this->getUserProfile($user);

        $pdf = Pdf::loadView('physical-possession.user.pdf.prefilled-form', compact('user', 'profile'))
            ->setPaper('a4');

        return $pdf->download('Possession-Certificate-Request-'.$user->mobile.'.pdf');
    }

    // Application submit karna
    public function submitApplication(Request $request)
    {
        $requiredFileRule = 'required|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:10240';
        $optionalFileRule = 'nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:10240';

        $rules = [];
        $messages = [];

        foreach (PhysicalPossessionDocument::applyFormFields() as $field => $meta) {
            $rules[$field] = $meta['required'] ? $requiredFileRule : $optionalFileRule;
            $messages["{$field}.required"] = $meta['label'].' is required.';
            $messages["{$field}.mimes"] = $meta['label'].' must be PDF, JPG, JPEG, or PNG.';
            $messages["{$field}.mimetypes"] = $meta['label'].' must be PDF, JPG, JPEG, or PNG.';
            $messages["{$field}.max"] = $meta['label'].' must be less than 10 MB.';
            $messages["{$field}.file"] = $meta['label'].' must be a valid uploaded file.';
        }

        $request->validate($rules, $messages);

        $user = Auth::user();

        if ($this->findUserApplication($user)) {
            return back()->with('error', 'You have already submitted an application.');
        }

        $profile = $this->getUserProfile($user);

        if (empty($profile['district_id']) || $profile['district'] === '—') {
            return back()->with('error', 'Your district details are missing. Please contact support before applying.');
        }

        $applicationNumber = $this->generateApplicationNumber();
        $slipId = 'SLIP-'.$applicationNumber;

        DB::beginTransaction();

        try {
            // Application save
            $assetId = $profile['asset_id'] ?? $this->assetService->resolveFromPurchaserId($profile['private_purchaser_id']);

            $application = PhysicalPossessionApplication::create([
                'user_id' => $user->id,
                'private_purchaser_id' => $profile['private_purchaser_id'],
                'asset_id' => $assetId,
                'ppp_id' => $profile['ppp_id'],
                'member_id' => $profile['member_id'],
                'slip_id' => $slipId,
                'application_number' => $applicationNumber,
                'district_id' => $profile['district_id'],
                'district_name' => $profile['district'],
                'mobile' => $user->mobile,
                'applicant_name' => $profile['name'],
                'father_name' => $profile['father_name'],
                'address' => $profile['address'],
                'registration_details' => $profile['registration_details'],
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            // Documents upload aur save
            foreach (PhysicalPossessionDocument::applyFormFields() as $type => $meta) {
                $file = $request->file($type);

                if (! $file) {
                    continue;
                }

                $path = $this->storeApplicationDocument($application, $profile, $type, $file);

                PhysicalPossessionDocument::create([
                    'application_id' => $application->id,
                    'asset_id' => $assetId,
                    'document_type' => $type,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }

            // Status log
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'asset_id' => $assetId,
                'old_status' => null,
                'new_status' => 'pending',
                'remarks' => 'Application submitted',
                'changed_by_type' => 'user',
                'changed_by_id' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('pp.user.success', $application)
                ->with('success', 'Application submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Unable to submit application. Please try again.');
        }
    }

    // Success page - acknowledgement slip
    public function success(PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);

        return view('physical-possession.user.success', compact('application'));
    }

    // Acknowledgement PDF download
    public function downloadSlip(PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);

        $pdf = Pdf::loadView('physical-possession.user.pdf.acknowledgement', compact('application'))
            ->setPaper('a4');

        return $pdf->download('Acknowledgement-'.$application->application_number.'.pdf');
    }

    // Print slip page
    public function printSlip(PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);

        return view('physical-possession.user.print-slip', compact('application'));
    }

    // Meri applications list
    public function myApplications()
    {
        $user = Auth::user();
        $applications = PhysicalPossessionApplication::where('user_id', $user->id)
            ->latest()
            ->get();

        $ppHasApplication = $applications->isNotEmpty();

        return view('physical-possession.user.applications', compact('applications', 'ppHasApplication'));
    }

    // Visit performa PDF
    public function downloadVisitPerforma(PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);
        $application->load('officerAction.officer');

        if ($application->status !== 'approved' || ! $application->citizen_visit_date) {
            return back()->with('error', 'Visit performa is not available yet.');
        }

        $pdf = Pdf::loadView('physical-possession.user.pdf.visit-performa', compact('application'))
            ->setPaper('a4');

        return $pdf->download('Visit-Performa-'.$application->application_number.'.pdf');
    }

    // Visit performa print page
    public function printVisitPerforma(PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);
        $application->load('officerAction.officer');

        if ($application->status !== 'approved' || ! $application->citizen_visit_date) {
            return back()->with('error', 'Visit performa is not available yet.');
        }

        return view('physical-possession.user.print-visit-performa', compact('application'));
    }

    // Single application detail
    public function showApplication(PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);
        $application->load(['documents', 'statusLogs', 'officerAction.officer']);

        return view('physical-possession.user.show-application', compact('application'));
    }

    // View uploaded document in browser
    public function viewDocument(PhysicalPossessionApplication $application, PhysicalPossessionDocument $document)
    {
        $this->ensureUserOwnsApplication($application);
        if ($document->application_id !== $application->id) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'Document file not found.');
        }

        return Storage::disk('public')->response($document->file_path, $document->original_name, [
            'Content-Type' => $document->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$document->original_name.'"',
        ]);
    }

    // Profile page
    public function profile()
    {
        $user = Auth::user();
        $profile = $this->getUserProfile($user);

        return view('physical-possession.user.profile', compact('user', 'profile'));
    }

    // User ka profile data database se laana
    private function getUserProfile(User $user): array
    {
        $mobile = $user->mobile;
        $purchaser = null;

        if ($mobile) {
            $purchaser = DB::table('property_private_purchasers as ppp')
                ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                ->leftJoin('cities as c', 'ppp.CityId', '=', 'c.CityId')
                ->leftJoin('sectors as s', 'ppp.SectorId', '=', 's.SectorId')
                ->leftJoin('property_auction_detail as pad', function ($join) {
                    $join->on('pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
                        ->where('pad.IsDeleted', 0)
                        ->where('pad.IsActive', 1);
                })
                ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
                ->where('ppp.IsActive', 1)
                ->where('ppp.IsDeleted', 0)
                ->where(function ($query) use ($mobile) {
                    $query->where('ppp.MobileNo', $mobile)
                        ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
                })
                ->select(
                    'ppp.*',
                    'd.DistrictName',
                    'd.DistrictId',
                    'c.CityName',
                    's.SectorName',
                    'pad.FlatCost',
                    'pad.ReceivedAmount',
                    'pad.BalanceAmount',
                    'pad.AssetId',
                    'pr.AssetName',
                    'ppp.ApplicationNo'
                )
                ->orderBy('ppp.PrivatePurchaserId')
                ->first();
        }

        $name = $user->name ?: ($purchaser?->PrivatePurchaserName ?? 'Applicant');
        $fatherName = $purchaser?->PurchaserFatherName ?? '—';
        $district = $purchaser?->DistrictName ?? '—';
        $districtId = $purchaser?->DistrictId ?? null;
        $address = $purchaser?->Address ?? '—';

        $registrationDetails = '—';
        if ($purchaser) {
            $parts = [];
            if ($purchaser->ApplicationNo) {
                $parts[] = 'Application No: '.$purchaser->ApplicationNo;
            }
            if ($purchaser->PPPId) {
                $parts[] = 'PPP ID: '.$purchaser->PPPId;
            }
            if ($purchaser->AssetName ?? null) {
                $parts[] = 'Property: '.$purchaser->AssetName;
            }
            if ($purchaser->CasteCategoryName ?? null) {
                $parts[] = 'Category: '.$purchaser->CasteCategoryName;
            }
            $registrationDetails = ! empty($parts) ? implode(' | ', $parts) : '—';
        }

        $plotNo = '—';
        if ($purchaser) {
            if (! empty($purchaser->Flat_Id)) {
                $plotNo = (string) $purchaser->Flat_Id;
            } elseif (! empty($purchaser->AssetId)) {
                $plotNo = (string) $purchaser->AssetId;
            } elseif (! empty($purchaser->AssetName)) {
                $plotNo = $purchaser->AssetName;
            }
        }

        $sectorName = $purchaser?->SectorName ?? '—';
        $urbanEstate = strtoupper(trim($purchaser?->CityName ?? $purchaser?->DistrictName ?? '—'));
        $officeLocation = $urbanEstate !== '—' ? $urbanEstate : strtoupper(trim($district));

        return [
            'name' => $name,
            'father_name' => $fatherName,
            'mobile' => $user->mobile ?? '—',
            'district' => $district,
            'district_id' => $districtId,
            'address' => $address,
            'registration_details' => $registrationDetails,
            'application_no' => $purchaser?->ApplicationNo ?? null,
            'private_purchaser_id' => $purchaser?->PrivatePurchaserId ?? null,
            'asset_id' => $purchaser?->AssetId ? (int) $purchaser->AssetId : null,
            'ppp_id' => $purchaser?->PPPId ?? null,
            'member_id' => $purchaser?->MemberID ?? null,
            'category' => $purchaser?->CasteCategoryName ?? '—',
            'plot_no' => $plotNo,
            'sector' => $sectorName,
            'urban_estate' => $urbanEstate,
            'office_location' => $officeLocation,
            'asset_name' => $purchaser?->AssetName ?? '—',
        ];
    }

    private function findUserApplication(User $user): ?PhysicalPossessionApplication
    {
        return PhysicalPossessionApplication::where('user_id', $user->id)->latest()->first();
    }

    private function ensureUserOwnsApplication(PhysicalPossessionApplication $application): void
    {
        if ($application->user_id !== Auth::id()) {
            abort(404);
        }
    }

    private function storeApplicationDocument(
        PhysicalPossessionApplication $application,
        array $profile,
        string $type,
        $file
    ): string {
        $memberFolder = $this->sanitizeStorageSegment(
            $profile['member_id'] ?? $profile['ppp_id'] ?? 'purchaser_'.$profile['private_purchaser_id']
        );

        $basePath = $memberFolder.'/physical_possession_applications/'.$application->secure_id;
        $storedName = $type.'_'.substr($application->secure_id, 0, 8).'.'.$file->getClientOriginalExtension();

        return $file->storeAs($basePath, $storedName, 'public');
    }

    private function sanitizeStorageSegment(?string $value): string
    {
        $clean = preg_replace('/[^A-Za-z0-9_-]/', '_', trim((string) $value));

        return $clean !== '' ? $clean : 'unknown_member';
    }

    private function generateApplicationNumber(): string
    {
        $year = now()->format('Y');

        do {
            $randomSuffix = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $applicationNumber = 'PP'.$year.'-'.$randomSuffix;
        } while (PhysicalPossessionApplication::where('application_number', $applicationNumber)->exists());

        return $applicationNumber;
    }
}
