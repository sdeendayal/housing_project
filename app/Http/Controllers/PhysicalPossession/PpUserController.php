<?php

namespace App\Http\Controllers\PhysicalPossession;

use App\Http\Controllers\Controller;
use App\Services\PhysicalPossessionAssetService;
use App\Services\OtpVerificationService;
use App\Models\ApplicationStatusLog;
use App\Models\AllotmentTable2;
use App\Models\Otp;
use App\Models\PhysicalPossessionApplication;
use App\Models\PhysicalPossessionDocument;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PpUserController extends Controller
{
    public function __construct(
        private PhysicalPossessionAssetService $assetService,
        private OtpVerificationService $otpService
    ) {}

    // User dashboard
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $this->getUserProfile($user);

        $applications = PhysicalPossessionApplication::where('user_id', $user->id)
            ->where('status', '!=', 'draft')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total' => PhysicalPossessionApplication::where('user_id', $user->id)->where('status', '!=', 'draft')->count(),
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

        // Full payment must be completed before applying (same tables as citizen dashboard)
        $isFullyPaid = false;
        $purchaserId = $user->private_purchaser_id;
        if (! $purchaserId && $user->mobile) {
            $mobileVariants = array_unique([$user->mobile, '91'.$user->mobile, (int) $user->mobile]);
            $purchaserId = DB::table('property_private_purchasers')
                ->where('IsActive', 1)
                ->where('IsDeleted', 0)
                ->whereIn('MobileNo', $mobileVariants)
                ->value('PrivatePurchaserId');
        }
        if ($purchaserId) {
            $auction = DB::table('property_auction_detail')
                ->where('PurchaserID', $purchaserId)
                ->where('IsDeleted', 0)
                ->where('IsActive', 1)
                ->orderByDesc('CreatedDate')
                ->first();
            if ($auction) {
                $flatCost = (float) $auction->FlatCost;
                $receivedAmount = (float) $auction->ReceivedAmount;
                $balanceAmount = (float) $auction->BalanceAmount;
                $installmentRows = DB::table('installment_due')
                    ->where('AssetId', $auction->AssetId)
                    ->where('IsDeleted', 0)
                    ->where('IsActive', 1)
                    ->get();
                if ($installmentRows->isEmpty()) {
                    $remainingBalance = $balanceAmount;
                } else {
                    $ledgerByNumber = DB::table('ledger')
                        ->where('AssetId', $auction->AssetId)
                        ->where('Is_Deleted', 0)
                        ->where('Is_Active', 1)
                        ->get()
                        ->keyBy('InstallmentNumber');
                    $emiPaid = 0.0;
                    foreach ($installmentRows as $row) {
                        $ledger = $ledgerByNumber->get($row->InstallmentNumber);
                        if ($ledger && (int) $ledger->RemainingBalance === 0 && (int) $ledger->Payable_amount === 0) {
                            $emiPaid += (float) $row->EMIAmount;
                        }
                    }
                    $totalPaid = $receivedAmount + $emiPaid;
                    $remainingBalance = $flatCost > 0 ? max(0.0, $flatCost - $totalPaid) : 0.0;
                }
                $isFullyPaid = $flatCost > 0 && $remainingBalance <= 0;
            }
        }
        if (! $isFullyPaid) {
            return redirect()->route('citizen.dashboard')
                ->with('warning', 'Your full payment has not been completed yet. Please complete your payment to become eligible for the Physical Possession process.');
        }

        $existing = $this->findSubmittedApplication($user);
        if ($existing) {
            return redirect()->route('pp.user.application.show', $existing)
                ->with('warning', 'You have already submitted an application. You cannot apply again.');
        }

        $profile = $this->getUserProfile($user);

        // Backfill: link orphan verified documents to a draft application
        if (PhysicalPossessionDocument::where('user_id', $user->id)->whereNull('application_id')->where('is_verified', true)->exists()) {
            $this->getOrCreateDraftApplication($user, $profile);
        }

        $draftApplication = $this->findDraftApplication($user);

        // Check if possession certificate is already verified for this user
        $verifiedPossessionCert = $this->findVerifiedDocument($user, PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE, $draftApplication);

        $verifiedAllotmentLetter = $this->findVerifiedDocument($user, PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER, $draftApplication);

        $allotmentLetter = $this->getAllotmentLetterData($user);

        return view('physical-possession.user.apply', compact(
            'user',
            'profile',
            'verifiedPossessionCert',
            'verifiedAllotmentLetter',
            'allotmentLetter'
        ) + ['isFullyPaid' => true]);
    }

    /**
     * Certificate verification — single method for OTP send + OTP verify + file save.
     * Step 1: request without "otp" → generate and send OTP.
     * Step 2: request with "otp" → verify OTP, upload file, mark certificate verified.
     */
    public function verifyCertificate(Request $request): JsonResponse
    {
        // Get logged-in user and mobile number
        $user = Auth::user();
        $mobile = $user->mobile;

        if (empty($mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not found on your profile.',
            ], 422);
        }

        $certField = PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE;
        $purpose = Otp::PURPOSE_POSSESSION_CERTIFICATE;

        $otpResponse = $this->handleDocumentOtpStep($request, $user, $purpose);

        if ($otpResponse) {
            return $otpResponse;
        }

        // OTP is correct — auto-generate pre-filled possession certificate PDF and store it
        $profile = $this->getUserProfile($user);

        $pdf = Pdf::loadView('physical-possession.user.pdf.prefilled-form', compact('user', 'profile'))
            ->setPaper('a4');

        $memberFolder = $this->sanitizeStorageSegment(
            $profile['member_id'] ?? $profile['ppp_id'] ?? 'purchaser_'.$profile['private_purchaser_id']
        );

        $basePath = $memberFolder.'/verified_certificates/'.$user->id;
        $storedName = $certField.'_'.now()->format('YmdHis').'.pdf';
        $filePath = $basePath.'/'.$storedName;

        Storage::disk('public')->put($filePath, $pdf->output());

        $originalName = 'Possession-Certificate-'.$user->mobile.'.pdf';
        $fileSize = Storage::disk('public')->size($filePath);
        $mimeType = 'application/pdf';

        $assetId = $profile['asset_id'] ?? $this->assetService->resolveFromPurchaserId($profile['private_purchaser_id']);

        // Create draft application so application_id is saved on the document
        $draftApplication = $this->getOrCreateDraftApplication($user, $profile);

        $existingCert = PhysicalPossessionDocument::where('user_id', $user->id)
            ->where('application_id', $draftApplication->id)
            ->where('document_type', $certField)
            ->first();

        if ($existingCert) {
            // Delete old file from storage
            if ($existingCert->file_path && Storage::disk('public')->exists($existingCert->file_path)) {
                Storage::disk('public')->delete($existingCert->file_path);
            }

            // Update existing certificate record
            $existingCert->update(array_merge([
                'asset_id' => $assetId,
                'application_id' => $draftApplication->id,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_verified' => true,
                'verified_at' => now(),
            ], $this->buildDocumentReferenceData($profile)));

            $certificate = $existingCert;
        } else {
            // Create new verified certificate record (no application yet)
            $certificate = PhysicalPossessionDocument::create(array_merge([
                'user_id' => $user->id,
                'application_id' => $draftApplication->id,
                'asset_id' => $assetId,
                'document_type' => $certField,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_verified' => true,
                'verified_at' => now(),
            ], $this->buildDocumentReferenceData($profile)));
        }

        return response()->json([
            'success' => true,
            'step' => 'verified',
            'message' => 'Possession Certificate verified successfully!',
            'verified_at' => $certificate->verified_at->format('d M Y, h:i A'),
            'file_name' => $certificate->original_name,
        ]);
    }

    /**
     * Allotment letter verification — OTP send + verify + auto-save PDF from allotment_table2.
     */
    public function verifyAllotmentLetter(Request $request): JsonResponse
    {
        $user = Auth::user();
        $mobile = $user->mobile;

        if (empty($mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not found on your profile.',
            ], 422);
        }

        $letterData = $this->getAllotmentLetterData($user);

        if (! $letterData) {
            return response()->json([
                'success' => false,
                'message' => 'Allotment letter data not found for your application.',
            ], 422);
        }

        $docField = PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER;
        $purpose = Otp::PURPOSE_ALLOTMENT_LETTER;

        $otpResponse = $this->handleDocumentOtpStep($request, $user, $purpose);

        if ($otpResponse) {
            return $otpResponse;
        }

        $profile = $this->getUserProfile($user);
        $verifyUrl = route('pp.allotment.verify', $letterData['application_number']);
        $letter = $letterData;
        $letter['verify_url'] = $verifyUrl;

        $pdf = Pdf::loadView('physical-possession.user.pdf.allotment-letter', compact('letter', 'verifyUrl'))
            ->setPaper('a4')
            ->setOption('enable_remote', true)
            ->setOption('default_font', 'noto sans devanagari');

        $memberFolder = $this->sanitizeStorageSegment(
            $profile['member_id'] ?? $profile['ppp_id'] ?? 'purchaser_'.$profile['private_purchaser_id']
        );

        $basePath = $memberFolder.'/verified_certificates/'.$user->id;
        $storedName = $docField.'_'.now()->format('YmdHis').'.pdf';
        $filePath = $basePath.'/'.$storedName;

        Storage::disk('public')->put($filePath, $pdf->output());

        $originalName = 'Allotment-Letter-'.$letterData['application_number'].'.pdf';
        $fileSize = Storage::disk('public')->size($filePath);
        $mimeType = 'application/pdf';
        $assetId = $profile['asset_id'] ?? $this->assetService->resolveFromPurchaserId($profile['private_purchaser_id']);

        $draftApplication = $this->getOrCreateDraftApplication($user, $profile);

        $existingDoc = PhysicalPossessionDocument::where('user_id', $user->id)
            ->where('application_id', $draftApplication->id)
            ->where('document_type', $docField)
            ->first();

        if ($existingDoc) {
            if ($existingDoc->file_path && Storage::disk('public')->exists($existingDoc->file_path)) {
                Storage::disk('public')->delete($existingDoc->file_path);
            }

            $existingDoc->update(array_merge([
                'asset_id' => $assetId,
                'application_id' => $draftApplication->id,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_verified' => true,
                'verified_at' => now(),
            ], $this->buildDocumentReferenceData($profile)));

            $document = $existingDoc;
        } else {
            $document = PhysicalPossessionDocument::create(array_merge([
                'user_id' => $user->id,
                'application_id' => $draftApplication->id,
                'asset_id' => $assetId,
                'document_type' => $docField,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_verified' => true,
                'verified_at' => now(),
            ], $this->buildDocumentReferenceData($profile)));
        }

        return response()->json([
            'success' => true,
            'step' => 'verified',
            'message' => 'Allotment Letter verified and saved successfully!',
            'verified_at' => $document->verified_at->format('d M Y, h:i A'),
            'file_name' => $document->original_name,
        ]);
    }

    // Public QR verification page for allotment letter
    public function publicVerifyAllotment(int $applicationNumber)
    {
        $allotment = AllotmentTable2::where('application_number', $applicationNumber)->first();

        if (! $allotment) {
            abort(404, 'Allotment record not found.');
        }

        $letter = [
            'application_number' => $allotment->application_number,
            'family_id' => null,
            'beneficiary_name' => $allotment->name,
            'father_name' => $allotment->fathers_or_husband_name,
            'plot' => $allotment->plot,
            'sector' => $allotment->Sector,
            'town_name' => $allotment->town,
            'district_name' => $allotment->district,
        ];

        $purchaser = DB::table('property_private_purchasers')
            ->where('ApplicationNo', $applicationNumber)
            ->where('IsActive', 1)
            ->where('IsDeleted', 0)
            ->first();

        if ($purchaser) {
            $letter['family_id'] = $purchaser->PPPId;
            $district = DB::table('districts')->where('DistrictId', $purchaser->DistrictId)->value('DistrictName');
            $city = DB::table('cities')->where('CityId', $purchaser->CityId)->value('CityName');
            $letter['district_name'] = $district ?: $letter['district_name'];
            $letter['town_name'] = $city ?: $letter['town_name'];
        }

        return view('physical-possession.user.allotment-verify', compact('letter'));
    }

    // Pre-filled form — view in browser first
    public function viewPrefilledForm()
    {
        $user = Auth::user();

        if ($this->findSubmittedApplication($user)) {
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

        if ($this->findSubmittedApplication($user)) {
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
        $user = Auth::user();
        $draftApplication = $this->findDraftApplication($user);

        $verifiedPossessionCert = $this->findVerifiedDocument($user, PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE, $draftApplication);

        $verifiedAllotmentLetter = $this->findVerifiedDocument($user, PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER, $draftApplication);

        $requiredFileRule = 'required|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:10240';
        $optionalFileRule = 'nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:10240';

        $rules = [];
        $messages = [];

        foreach (PhysicalPossessionDocument::applyFormFields() as $field => $meta) {
            if ($field === PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE && $verifiedPossessionCert) {
                $rules[$field] = 'nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:10240';

                continue;
            }

            if ($field === PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER && $verifiedAllotmentLetter) {
                $rules[$field] = 'nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:10240';

                continue;
            }

            $rules[$field] = $meta['required'] ? $requiredFileRule : $optionalFileRule;
            $messages["{$field}.required"] = $meta['label'].' is required.';
            $messages["{$field}.mimes"] = $meta['label'].' must be PDF, JPG, JPEG, or PNG.';
            $messages["{$field}.mimetypes"] = $meta['label'].' must be PDF, JPG, JPEG, or PNG.';
            $messages["{$field}.max"] = $meta['label'].' must be less than 10 MB.';
            $messages["{$field}.file"] = $meta['label'].' must be a valid uploaded file.';
        }

        $request->validate($rules, $messages);

        if ($this->findSubmittedApplication($user)) {
            return back()->with('error', 'You have already submitted an application.');
        }

        $missingDocuments = $this->missingSubmitDocuments(
            $request,
            $verifiedPossessionCert,
            $verifiedAllotmentLetter
        );

        if (! empty($missingDocuments)) {
            return back()
                ->withInput()
                ->with('error', 'Please complete all 5 documents before submitting: '.implode(', ', $missingDocuments).'.');
        }

        $profile = $this->getUserProfile($user);

        if (empty($profile['district_id']) || $profile['district'] === '—') {
            return back()->with('error', 'Your district details are missing. Please contact support before applying.');
        }

        $applicationNumber = $this->generateApplicationNumber();
        $slipId = 'SLIP-'.$applicationNumber;

        DB::beginTransaction();

        try {
            $assetId = $profile['asset_id'] ?? $this->assetService->resolveFromPurchaserId($profile['private_purchaser_id']);

            if ($draftApplication) {
                // Finalize existing draft application
                $draftApplication->update(array_merge([
                    'slip_id' => $slipId,
                    'application_number' => $applicationNumber,
                    'mobile' => $user->mobile,
                    'applicant_name' => $profile['name'],
                    'father_name' => $profile['father_name'],
                    'address' => $profile['address'],
                    'registration_details' => $profile['registration_details'],
                    'status' => 'pending',
                ], $this->buildApplicationReferenceData($profile)));

                $application = $draftApplication->fresh();
            } else {
                // Create new application (no OTP-verified draft existed)
                $application = PhysicalPossessionApplication::create(array_merge([
                    'user_id' => $user->id,
                    'slip_id' => $slipId,
                    'application_number' => $applicationNumber,
                    'mobile' => $user->mobile,
                    'applicant_name' => $profile['name'],
                    'father_name' => $profile['father_name'],
                    'address' => $profile['address'],
                    'registration_details' => $profile['registration_details'],
                    'status' => 'pending',
                    'created_by' => $user->id,
                ], $this->buildApplicationReferenceData($profile)));
            }

            // Documents upload aur save
            foreach (PhysicalPossessionDocument::applyFormFields() as $type => $meta) {
                if ($type === PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE && $verifiedPossessionCert) {
                    $newPath = $this->copyVerifiedCertToApplication($verifiedPossessionCert, $application, $profile, $type);

                    $verifiedPossessionCert->update([
                        'application_id' => $application->id,
                        'file_path' => $newPath,
                    ]);

                    continue;
                }

                if ($type === PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER && $verifiedAllotmentLetter) {
                    $newPath = $this->copyVerifiedCertToApplication($verifiedAllotmentLetter, $application, $profile, $type);

                    $verifiedAllotmentLetter->update([
                        'application_id' => $application->id,
                        'file_path' => $newPath,
                    ]);

                    continue;
                }

                $file = $request->file($type);

                if (! $file) {
                    continue;
                }

                $path = $this->storeApplicationDocument($application, $profile, $type, $file);

                PhysicalPossessionDocument::create(array_merge([
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                    'asset_id' => $assetId,
                    'document_type' => $type,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ], $this->buildDocumentReferenceData($profile)));
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
            ->where('status', '!=', 'draft')
            ->latest()
            ->get();

        $ppHasApplication = $applications->isNotEmpty();
        $hasDraftApplication = PhysicalPossessionApplication::where('user_id', $user->id)->where('status', 'draft')->exists();

        return view('physical-possession.user.applications', compact('applications', 'ppHasApplication', 'hasDraftApplication'));
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

        // Draft application — send user back to the apply form to continue
        if ($application->status === 'draft') {
            return redirect()->route('pp.user.apply')
                ->with('info', 'Please complete and submit your application.');
        }

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
        $purchaser = $this->findPurchaserForUser($user);

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
        $cityName = $purchaser?->CityName ?? '—';
        $urbanEstate = strtoupper(trim($cityName !== '—' ? $cityName : ($district !== '—' ? $district : '—')));
        $officeLocation = $urbanEstate !== '—' ? $urbanEstate : strtoupper(trim($district));

        return [
            'name' => $name,
            'father_name' => $fatherName,
            'mobile' => $user->mobile ?? '—',
            'district' => $district,
            'district_id' => $districtId,
            'city_id' => $purchaser?->CityId ?? null,
            'city_name' => $cityName,
            'sector_id' => $purchaser?->SectorId ?? null,
            'sector_name' => $sectorName,
            'branch_id' => $purchaser?->BranchId ?? null,
            'flat_id' => $purchaser?->Flat_Id ?? null,
            'address' => $address,
            'registration_details' => $registrationDetails,
            'application_no' => $purchaser?->ApplicationNo ?? null,
            'private_purchaser_id' => $purchaser?->PrivatePurchaserId ?? null,
            'property_auction_id' => $purchaser?->PropertyAuctionId ?? null,
            'asset_id' => $purchaser?->AssetId ? (int) $purchaser->AssetId : null,
            'asset_name' => $purchaser?->AssetName ?? '—',
            'asset_size' => $purchaser?->AssetSize ?? null,
            'asset_unit' => $purchaser?->Unit ?? null,
            'flat_cost' => $purchaser?->FlatCost ?? null,
            'received_amount' => $purchaser?->ReceivedAmount ?? null,
            'balance_amount' => $purchaser?->BalanceAmount ?? null,
            'ppp_id' => $purchaser?->PPPId ?? null,
            'member_id' => $purchaser?->MemberID ?? null,
            'category' => $purchaser?->CasteCategoryName ?? '—',
            'plot_no' => $plotNo,
            'sector' => $sectorName,
            'urban_estate' => $urbanEstate,
            'office_location' => $officeLocation,
        ];
    }

    private function findPurchaserForUser(User $user): ?object
    {
        $select = [
            'ppp.*',
            'd.DistrictName',
            'd.DistrictId',
            'c.CityName',
            's.SectorName',
            'pad.PropertyAuctionId',
            'pad.FlatCost',
            'pad.ReceivedAmount',
            'pad.BalanceAmount',
            'pad.AssetId',
            'pr.AssetName',
            'pr.AssetSize',
            'pr.Unit',
            'ppp.ApplicationNo',
        ];

        if ($user->private_purchaser_id) {
            return $this->purchaserBaseQuery()
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->select($select)
                ->orderByDesc('pad.PropertyAuctionId')
                ->first();
        }

        $mobile = $user->mobile;

        if (! $mobile) {
            return null;
        }

        return $this->purchaserBaseQuery()
            ->where(function ($query) use ($mobile) {
                $query->where('ppp.MobileNo', $mobile)
                    ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
            })
            ->select($select)
            ->orderByDesc('pad.PropertyAuctionId')
            ->first();
    }

    private function purchaserBaseQuery()
    {
        return DB::table('property_private_purchasers as ppp')
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
            ->where('ppp.IsDeleted', 0);
    }

    private function buildApplicationReferenceData(array $profile): array
    {
        return [
            'private_purchaser_id' => $profile['private_purchaser_id'],
            'asset_id' => $profile['asset_id'],
            'property_auction_id' => $profile['property_auction_id'],
            'mmsay_application_no' => $profile['application_no'],
            'ppp_id' => $profile['ppp_id'],
            'member_id' => $profile['member_id'],
            'district_id' => $profile['district_id'],
            'district_name' => $profile['district'],
            'branch_id' => $profile['branch_id'],
            'city_id' => $profile['city_id'],
            'city_name' => $profile['city_name'],
            'sector_id' => $profile['sector_id'],
            'sector_name' => $profile['sector_name'],
            'flat_id' => $profile['flat_id'],
            'asset_name' => $profile['asset_name'] !== '—' ? $profile['asset_name'] : null,
            'asset_size' => $profile['asset_size'],
            'asset_unit' => $profile['asset_unit'],
            'flat_cost' => $profile['flat_cost'],
            'received_amount' => $profile['received_amount'],
            'balance_amount' => $profile['balance_amount'],
        ];
    }

    private function buildDocumentReferenceData(array $profile): array
    {
        return [
            'private_purchaser_id' => $profile['private_purchaser_id'],
            'property_auction_id' => $profile['property_auction_id'],
            'mmsay_application_no' => $profile['application_no'],
            'asset_id' => $profile['asset_id'],
        ];
    }

    /**
     * @return list<string> Human-readable labels for documents still missing at submit time.
     */
    private function missingSubmitDocuments(
        Request $request,
        ?PhysicalPossessionDocument $verifiedPossessionCert,
        ?PhysicalPossessionDocument $verifiedAllotmentLetter
    ): array {
        $missing = [];

        foreach (PhysicalPossessionDocument::applyFormFields() as $type => $meta) {
            if (! $meta['required']) {
                continue;
            }

            if ($type === PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE) {
                if (! $verifiedPossessionCert) {
                    $missing[] = $meta['label'];
                }

                continue;
            }

            if ($type === PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER) {
                if (! $verifiedAllotmentLetter && ! $request->hasFile($type)) {
                    $missing[] = $meta['label'];
                }

                continue;
            }

            if (! $request->hasFile($type)) {
                $missing[] = $meta['label'];
            }
        }

        return $missing;
    }

    private function handleDocumentOtpStep(Request $request, User $user, string $purpose): ?JsonResponse
    {
        $mobile = $user->mobile;

        if (empty($mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not found on your profile.',
            ], 422);
        }

        if (! $request->filled('otp')) {
            $result = $request->boolean('resend')
                ? $this->otpService->resend($mobile, $purpose, $user->id, 'PP Document')
                : $this->otpService->send($mobile, $purpose, $user->id, 'PP Document');

            return response()->json($result, $result['success'] ? 200 : 422);
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Please enter the OTP.',
            'otp.digits' => 'OTP must be exactly 6 digits.',
        ]);

        $result = $this->otpService->verify($mobile, $purpose, $request->otp);

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        return null;
    }

    private function findSubmittedApplication(User $user): ?PhysicalPossessionApplication
    {
        return PhysicalPossessionApplication::where('user_id', $user->id)
            ->where('status', '!=', 'draft')
            ->latest()
            ->first();
    }

    private function findDraftApplication(User $user): ?PhysicalPossessionApplication
    {
        return PhysicalPossessionApplication::where('user_id', $user->id)
            ->where('status', 'draft')
            ->latest()
            ->first();
    }

    private function getOrCreateDraftApplication(User $user, array $profile): PhysicalPossessionApplication
    {
        $draft = $this->findDraftApplication($user);

        if ($draft) {
            $draft->update($this->buildApplicationReferenceData($profile));
            $this->linkOrphanVerifiedDocuments($user, $draft);

            return $draft->fresh();
        }

        $suffix = 'U'.$user->id.'-'.now()->format('YmdHis');

        $draft = PhysicalPossessionApplication::create(array_merge([
            'user_id' => $user->id,
            'slip_id' => 'SLIP-DRAFT-'.$suffix,
            'application_number' => 'DRAFT-'.$suffix,
            'mobile' => $user->mobile,
            'applicant_name' => $profile['name'],
            'father_name' => $profile['father_name'],
            'address' => $profile['address'],
            'registration_details' => $profile['registration_details'],
            'status' => 'draft',
            'created_by' => $user->id,
        ], $this->buildApplicationReferenceData($profile)));

        $this->linkOrphanVerifiedDocuments($user, $draft);

        return $draft;
    }

    private function linkOrphanVerifiedDocuments(User $user, PhysicalPossessionApplication $application): void
    {
        PhysicalPossessionDocument::where('user_id', $user->id)
            ->whereNull('application_id')
            ->where('is_verified', true)
            ->update(['application_id' => $application->id]);
    }

    private function findVerifiedDocument(
        User $user,
        string $documentType,
        ?PhysicalPossessionApplication $draftApplication
    ): ?PhysicalPossessionDocument {
        $query = PhysicalPossessionDocument::where('user_id', $user->id)
            ->where('document_type', $documentType)
            ->where('is_verified', true);

        if ($draftApplication) {
            $query->where('application_id', $draftApplication->id);
        } else {
            $query->whereNull('application_id');
        }

        return $query->first();
    }

    private function findUserApplication(User $user): ?PhysicalPossessionApplication
    {
        return $this->findSubmittedApplication($user);
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

    // Copy already-verified certificate file into the application folder
    private function copyVerifiedCertToApplication(
        PhysicalPossessionDocument $verifiedCert,
        PhysicalPossessionApplication $application,
        array $profile,
        string $type
    ): string {
        $memberFolder = $this->sanitizeStorageSegment(
            $profile['member_id'] ?? $profile['ppp_id'] ?? 'purchaser_'.$profile['private_purchaser_id']
        );

        $basePath = $memberFolder.'/physical_possession_applications/'.$application->secure_id;
        $extension = pathinfo($verifiedCert->file_path, PATHINFO_EXTENSION);
        $storedName = $type.'_'.substr($application->secure_id, 0, 8).'.'.$extension;
        $newPath = $basePath.'/'.$storedName;

        Storage::disk('public')->copy($verifiedCert->file_path, $newPath);

        return $newPath;
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

    // Allotment letter data from allotment_table2 + user profile
    private function getAllotmentLetterData(User $user): ?array
    {
        $profile = $this->getUserProfile($user);

        if (empty($profile['application_no'])) {
            return null;
        }

        $allotment = AllotmentTable2::where('application_number', $profile['application_no'])->first();

        if (! $allotment) {
            return null;
        }

        $districtName = $this->resolveAllotmentDistrictName($allotment, $profile);
        $townName = $this->resolveAllotmentTownName($allotment, $profile);

        return [
            'application_number' => $allotment->application_number,
            'family_id' => $profile['ppp_id'],
            // Name from purchaser profile (old template: $data->ppt['fullNameLL'])
            'beneficiary_name' => $profile['name'] ?: $allotment->name,
            'father_name' => $profile['father_name'] !== '—' ? $profile['father_name'] : $allotment->fathers_or_husband_name,
            // Plot details from allotment_table2 (old template: $alot->plot, $alot->Sector)
            'plot' => $allotment->plot,
            'sector' => $allotment->Sector,
            // Town/District (old template: PptMembers btNameLL / districtNameLL)
            'town_name' => $townName,
            'district_name' => $districtName,
        ];
    }

    // Town name — profile city first, then cities table lookup by district/town code
    private function resolveAllotmentTownName(AllotmentTable2 $allotment, array $profile): string
    {
        if ($profile['urban_estate'] !== '—' && ! empty($profile['urban_estate'])) {
            return $profile['urban_estate'];
        }

        $townCode = trim((string) $allotment->town);
        $districtCode = trim((string) $allotment->district);

        if ($townCode !== '' && $districtCode !== '') {
            $city = DB::table('cities')
                ->where('DistrictId', (int) $profile['district_id'])
                ->where('CityId', 'like', '%'.$townCode)
                ->value('CityName');

            if ($city) {
                return $city;
            }
        }

        return $townCode;
    }

    // District name — profile district first
    private function resolveAllotmentDistrictName(AllotmentTable2 $allotment, array $profile): string
    {
        if ($profile['district'] !== '—' && ! empty($profile['district'])) {
            return $profile['district'];
        }

        $districtCode = trim((string) $allotment->district);

        if ($districtCode !== '' && ! empty($profile['district_id'])) {
            $district = DB::table('districts')
                ->where('DistrictId', $profile['district_id'])
                ->value('DistrictName');

            if ($district) {
                return $district;
            }
        }

        return $districtCode;
    }
}
