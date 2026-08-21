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
    private const PP_MIN_TOTAL_PAID = 60000;

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

        if ($ineligible = $this->ppPaymentEligibilityRedirect($user)) {
            return $ineligible;
        }

        $existing = $this->findSubmittedApplication($user);
        if ($existing) {
            return redirect()->route('pp.user.application.show', $existing)
                ->with('warning', 'You have already submitted an application. You cannot apply again.');
        }

        $returned = $this->findReturnedApplication($user);
        if ($returned) {
            return redirect()->route('pp.user.application.correct', $returned)
                ->with('info', 'Please correct the returned documents and resubmit.');
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
        ));
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
        $targetApplication = $this->resolveVerificationApplication($user, $profile);

        $existingCert = PhysicalPossessionDocument::where('user_id', $user->id)
            ->where('application_id', $targetApplication->id)
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
                'application_id' => $targetApplication->id,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_verified' => true,
                'verified_at' => now(),
                'review_status' => PhysicalPossessionDocument::REVIEW_PENDING,
                'officer_remarks' => null,
                'returned_at' => null,
                'returned_by' => null,
            ], $this->buildDocumentReferenceData($profile)));

            $certificate = $existingCert;
        } else {
            // Create new verified certificate record (no application yet)
            $certificate = PhysicalPossessionDocument::create(array_merge([
                'user_id' => $user->id,
                'application_id' => $targetApplication->id,
                'asset_id' => $assetId,
                'document_type' => $certField,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_verified' => true,
                'verified_at' => now(),
                'review_status' => PhysicalPossessionDocument::REVIEW_PENDING,
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

        $targetApplication = $this->resolveVerificationApplication($user, $profile);

        $existingDoc = PhysicalPossessionDocument::where('user_id', $user->id)
            ->where('application_id', $targetApplication->id)
            ->where('document_type', $docField)
            ->first();

        if ($existingDoc) {
            if ($existingDoc->file_path && Storage::disk('public')->exists($existingDoc->file_path)) {
                Storage::disk('public')->delete($existingDoc->file_path);
            }

            $existingDoc->update(array_merge([
                'asset_id' => $assetId,
                'application_id' => $targetApplication->id,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_verified' => true,
                'verified_at' => now(),
                'review_status' => PhysicalPossessionDocument::REVIEW_PENDING,
                'officer_remarks' => null,
                'returned_at' => null,
                'returned_by' => null,
            ], $this->buildDocumentReferenceData($profile)));

            $document = $existingDoc;
        } else {
            $document = PhysicalPossessionDocument::create(array_merge([
                'user_id' => $user->id,
                'application_id' => $targetApplication->id,
                'asset_id' => $assetId,
                'document_type' => $docField,
                'file_path' => $filePath,
                'original_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_verified' => true,
                'verified_at' => now(),
                'review_status' => PhysicalPossessionDocument::REVIEW_PENDING,
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

    // Allotment letter — citizen sidebar page
    public function viewAllotmentLetter()
    {
        $user = Auth::user();
        $profile = $this->getUserProfile($user);
        $letter = $this->getAllotmentLetterData($user);
        $verifyUrl = $letter ? route('pp.allotment.verify', $letter['application_number']) : null;
        $applicationId = $profile['application_no']
            ? 'HR-MMSAY-'.$profile['application_no']
            : '—';

        return view('mmsayCitizenAllotmentLetter', [
            'displayName' => $profile['name'],
            'applicationId' => $applicationId,
            'letter' => $letter,
            'verifyUrl' => $verifyUrl,
        ]);
    }

    // Allotment letter PDF download
    public function downloadAllotmentLetter()
    {
        $user = Auth::user();
        $letterData = $this->getAllotmentLetterData($user);

        if (! $letterData) {
            return redirect()->route('citizen.allotment-letter')
                ->with('error', 'Allotment letter data not found for your account.');
        }

        $verifyUrl = route('pp.allotment.verify', $letterData['application_number']);
        $letter = $letterData;
        $letter['verify_url'] = $verifyUrl;

        $pdf = Pdf::loadView('physical-possession.user.pdf.allotment-letter', compact('letter', 'verifyUrl'))
            ->setPaper('a4')
            ->setOption('enable_remote', true)
            ->setOption('default_font', 'noto sans devanagari');

        return $pdf->download('Allotment-Letter-'.$letterData['application_number'].'.pdf');
    }

    // Possession certificate form — citizen sidebar page
    public function viewPossessionCertificate()
    {
        $user = Auth::user();
        $profile = $this->getUserProfile($user);
        $applicationId = $profile['application_no']
            ? 'HR-MMSAY-'.$profile['application_no']
            : '—';

        return view('mmsayCitizenPossessionCertificate', [
            'displayName' => $profile['name'],
            'applicationId' => $applicationId,
            'user' => $user,
            'profile' => $profile,
        ]);
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

        if ($ineligible = $this->ppPaymentEligibilityRedirect($user)) {
            return $ineligible;
        }

        $draftApplication = $this->findDraftApplication($user);

        $verifiedPossessionCert = $this->findVerifiedDocument($user, PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE, $draftApplication);

        $verifiedAllotmentLetter = $this->findVerifiedDocument($user, PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER, $draftApplication);

        $maxKb = PhysicalPossessionDocument::MAX_UPLOAD_KB;
        $requiredFileRule = "required|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:{$maxKb}";
        $optionalFileRule = "nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:{$maxKb}";

        $rules = [];
        $messages = [];

        foreach (PhysicalPossessionDocument::applyFormFields() as $field => $meta) {
            if ($field === PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE && $verifiedPossessionCert) {
                $rules[$field] = "nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:{$maxKb}";

                continue;
            }

            if ($field === PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER && $verifiedAllotmentLetter) {
                $rules[$field] = "nullable|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:{$maxKb}";

                continue;
            }

            $rules[$field] = $meta['required'] ? $requiredFileRule : $optionalFileRule;
            $messages["{$field}.required"] = $meta['label'].' is required.';
            $messages["{$field}.mimes"] = $meta['label'].' must be PDF, JPG, JPEG, or PNG.';
            $messages["{$field}.mimetypes"] = $meta['label'].' must be PDF, JPG, JPEG, or PNG.';
            $messages["{$field}.max"] = $meta['label'].' must be less than 500 KB.';
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

        $isLegacyApproved = ($application->status === 'approved' && $application->citizen_visit_date);
        $isNewWorkflowScheduled = ($application->physical_possession_status !== null && $application->citizen_visit_date);

        if (! $isLegacyApproved && ! $isNewWorkflowScheduled) {
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

        $isLegacyApproved = ($application->status === 'approved' && $application->citizen_visit_date);
        $isNewWorkflowScheduled = ($application->physical_possession_status !== null && $application->citizen_visit_date);

        if (! $isLegacyApproved && ! $isNewWorkflowScheduled) {
            return back()->with('error', 'Visit performa is not available yet.');
        }

        return view('physical-possession.user.print-visit-performa', compact('application'));
    }

    // Single application detail
    public function showApplication(PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);

        if ($application->status === 'draft') {
            return redirect()->route('pp.user.apply')
                ->with('info', 'Please complete and submit your application.');
        }

        $application->load(['documents', 'statusLogs', 'officerAction.officer']);

        $purchaser = DB::table('property_private_purchasers as ppp')
            ->join('property_auction_detail as pad', 'ppp.PrivatePurchaserId', '=', 'pad.PurchaserID')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->leftJoin('cities as c', 'pr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pr.SectorId', '=', 's.SectorId')
            ->where('ppp.PrivatePurchaserId', $application->private_purchaser_id)
            ->where('pad.AssetId', $application->asset_id)
            ->select([
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.MobileNo',
                'ppp.Address',
                'ppp.PPPId',
                'ppp.MemberID',
                'ppp.ApplicationNo as purchaser_app_no',
                'ppp.CasteCategoryName as purchaser_category',
                'ppp.MaritalStatus as purchaser_marital_status',
                'ppp.CreateDate as purchaser_reg_date',
                'pr.AssetId',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'c.CityName',
                's.SectorName',
                'd.DistrictName',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pad.BalanceAmount',
            ])
            ->first();

        $initialDeposit = 0.0;
        $installmentPaid = 0.0;
        if ($purchaser) {
            $initialDeposit = (float) ($purchaser->ReceivedAmount ?? 0);
            $assetId = $purchaser->AssetId;
            if ($assetId) {
                $cashReceiptPaid = (float) DB::table('cash_receipt_details')
                    ->where('asset_number', $assetId)
                    ->where('IsDeleted', 0)
                    ->where('IsActive', 1)
                    ->sum('total_paid_amount');

                $installmentPaid = $cashReceiptPaid;
            }
        }
        $totalReceived = $initialDeposit + $installmentPaid;
        $balanceAmount = $purchaser ? (float) ($purchaser->FlatCost ?? 0) - $totalReceived : 0.0;

        return view('physical-possession.user.show-application', compact('application', 'purchaser', 'totalReceived', 'balanceAmount'));
    }

    // Citizen selects a visit slot from the three offered by the officer
    public function selectVisitSlot(Request $request, PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);

        if ($application->status !== 'approved') {
            return back()->with('error', 'Only approved applications can have meeting schedules selected.');
        }

        if ($application->citizen_visit_date) {
            return back()->with('error', 'You have already confirmed a visit schedule.');
        }

        $request->validate([
            'selected_slot' => 'required|date',
        ], [
            'selected_slot.required' => 'Please select one of the offered time slots.',
            'selected_slot.date' => 'Invalid slot date selected.',
        ]);

        $selectedSlot = \Carbon\Carbon::parse($request->selected_slot);

        // Verify the selected slot matches one of the three offered slots
        $offeredSlots = [
            $application->visit_slot_1 ? $application->visit_slot_1->toDateTimeString() : null,
            $application->visit_slot_2 ? $application->visit_slot_2->toDateTimeString() : null,
            $application->visit_slot_3 ? $application->visit_slot_3->toDateTimeString() : null,
        ];

        if (! in_array($selectedSlot->toDateTimeString(), $offeredSlots, true)) {
            return back()->with('error', 'The selected slot is not one of the offered options.');
        }

        // Verify slot is still valid and has capacity (max 10 approved visits per district per 1-hour slot)
        if ($selectedSlot->isPast()) {
            return back()->with('error', 'The selected slot is in the past. Please select another slot.');
        }

        $slotStart = $selectedSlot->copy()->startOfHour();
        $slotEnd = $slotStart->copy()->addHour();

        try {
            DB::transaction(function () use ($application, $selectedSlot, $slotStart, $slotEnd) {
                $locked = PhysicalPossessionApplication::query()
                    ->where('id', $application->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($locked->citizen_visit_date) {
                    throw new \RuntimeException('You have already confirmed a visit schedule.');
                }

                $query = PhysicalPossessionApplication::query()
                    ->where('status', 'approved')
                    ->whereNotNull('citizen_visit_date')
                    ->where('citizen_visit_date', '>=', $slotStart)
                    ->where('citizen_visit_date', '<', $slotEnd)
                    ->lockForUpdate();

                if ($locked->district_id) {
                    $query->where('district_id', $locked->district_id);
                } elseif ($locked->district_name) {
                    $query->where('district_name', 'like', '%'.$locked->district_name.'%');
                }

                if ($query->count() >= 10) {
                    throw new \RuntimeException(sprintf(
                        'This time slot (%s – %s on %s) has just become full. Please select a different slot.',
                        $slotStart->format('h:i A'),
                        $slotEnd->format('h:i A'),
                        $slotStart->format('d M Y')
                    ));
                }

                $locked->update([
                    'citizen_visit_date' => $selectedSlot,
                ]);

                ApplicationStatusLog::create([
                    'application_id' => $locked->id,
                    'asset_id' => $locked->asset_id,
                    'old_status' => 'approved',
                    'new_status' => 'approved',
                    'remarks' => 'Citizen confirmed visit schedule: ' . $selectedSlot->format('d M Y, h:i A'),
                    'changed_by_type' => 'user',
                    'changed_by_id' => Auth::id(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('pp.user.application.show', $application)
            ->with('success', 'Your visit schedule has been confirmed successfully!');
    }

    // Returned application — citizen re-uploads flagged documents
    public function correctDocuments(PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);

        if ($application->status !== 'returned') {
            return redirect()->route('pp.user.application.show', $application);
        }

        $user = Auth::user();
        $profile = $this->getUserProfile($user);
        $returnedDocuments = $application->documents()
            ->where('review_status', PhysicalPossessionDocument::REVIEW_RETURNED)
            ->get();

        if ($returnedDocuments->isEmpty()) {
            return redirect()->route('pp.user.application.show', $application)
                ->with('error', 'No documents are marked for correction.');
        }

        $allotmentLetter = $this->getAllotmentLetterData($user);
        $needsPossessionCert = $returnedDocuments->contains('document_type', PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE);
        $needsAllotmentLetter = $returnedDocuments->contains('document_type', PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER);

        return view('physical-possession.user.correct-documents', compact(
            'application',
            'user',
            'profile',
            'returnedDocuments',
            'allotmentLetter',
            'needsPossessionCert',
            'needsAllotmentLetter'
        ));
    }

    // Citizen resubmits after correcting returned documents
    public function resubmitApplication(Request $request, PhysicalPossessionApplication $application)
    {
        $this->ensureUserOwnsApplication($application);

        if ($application->status !== 'returned') {
            return redirect()->route('pp.user.application.show', $application);
        }

        $user = Auth::user();
        $profile = $this->getUserProfile($user);
        $returnedDocuments = $application->documents()
            ->where('review_status', PhysicalPossessionDocument::REVIEW_RETURNED)
            ->get();

        if ($returnedDocuments->isEmpty()) {
            return back()->with('error', 'No documents require correction.');
        }

        $maxKb = PhysicalPossessionDocument::MAX_UPLOAD_KB;
        $fileRule = "required|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:{$maxKb}";
        $rules = [];
        $messages = [];

        foreach ($returnedDocuments as $doc) {
            $type = $doc->document_type;

            if (in_array($type, [
                PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE,
                PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER,
            ], true)) {
                continue;
            }

            $rules[$type] = $fileRule;
            $messages["{$type}.required"] = $doc->typeLabel().' is required.';
            $messages["{$type}.mimes"] = $doc->typeLabel().' must be PDF, JPG, JPEG, or PNG.';
            $messages["{$type}.mimetypes"] = $doc->typeLabel().' must be PDF, JPG, JPEG, or PNG.';
            $messages["{$type}.max"] = $doc->typeLabel().' must be less than 500 KB.';
        }

        if (! empty($rules)) {
            $request->validate($rules, $messages);
        }

        foreach ($returnedDocuments as $doc) {
            $type = $doc->document_type;

            if (in_array($type, [
                PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE,
                PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER,
            ], true)) {
                if ($doc->fresh()->review_status === PhysicalPossessionDocument::REVIEW_RETURNED) {
                    return back()->with('error', 'Please re-verify '.$doc->typeLabel().' before resubmitting.');
                }

                continue;
            }

            $file = $request->file($type);
            if (! $file) {
                return back()->with('error', 'Please upload '.$doc->typeLabel().'.');
            }

            $this->replaceApplicationDocument($application, $profile, $doc, $file);
        }

        $stillReturned = $application->documents()
            ->where('review_status', PhysicalPossessionDocument::REVIEW_RETURNED)
            ->count();

        if ($stillReturned > 0) {
            return back()->with('error', 'Please correct all returned documents before resubmitting.');
        }

        DB::beginTransaction();

        try {
            $oldStatus = $application->status;

            $application->update([
                'status' => 'pending',
                'remarks' => null,
            ]);

            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'asset_id' => $application->asset_id,
                'old_status' => $oldStatus,
                'new_status' => 'pending',
                'remarks' => 'Application resubmitted after document correction',
                'changed_by_type' => 'user',
                'changed_by_id' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('pp.user.application.show', $application)
                ->with('success', 'Application resubmitted successfully. It is pending officer review again.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Unable to resubmit application. Please try again.');
        }
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
        $purchaser = $this->findPurchaserForUserWithProperty($user);

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

    /** @return array{initial_deposit: float, installment_paid: float, total_paid: float} */
    private function resolvePpTotalAmountPaid(User $user): array
    {
        $purchaser = $this->findPurchaserForUser($user);

        if (! $purchaser) {
            return [
                'initial_deposit' => 0.0,
                'installment_paid' => 0.0,
                'total_paid' => 0.0,
            ];
        }

        $auction = DB::table('property_auction_detail')
            ->where('PurchaserID', $purchaser->PrivatePurchaserId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderByDesc('CreatedDate')
            ->first();

        $initialDeposit = (float) ($auction?->ReceivedAmount ?? 0);
        $assetId = $auction?->AssetId ? (int) $auction->AssetId : null;
        $installmentPaid = 0.0;

        if ($assetId) {
            $cashReceiptPaid = (float) DB::table('cash_receipt_details')
                ->where('asset_number', $assetId)
                ->where('IsDeleted', 0)
                ->where('IsActive', 1)
                ->sum('total_paid_amount');

            $installmentPaid = $cashReceiptPaid;
        }

        return [
            'initial_deposit' => $initialDeposit,
            'installment_paid' => $installmentPaid,
            'total_paid' => $initialDeposit + $installmentPaid,
        ];
    }

    private function ppPaymentEligibilityRedirect(User $user): ?\Illuminate\Http\RedirectResponse
    {
        $payments = $this->resolvePpTotalAmountPaid($user);
        $purchaser = $this->findPurchaserForUser($user);
        $inEligibleTable = false;
        if ($purchaser && !empty($purchaser->ApplicationNo)) {
            $inEligibleTable = DB::table('mmsay_eligible_beneficiaries')
                ->where('application_number', $purchaser->ApplicationNo)
                ->exists();
        }

        if ($payments['total_paid'] >= self::PP_MIN_TOTAL_PAID && $inEligibleTable) {
            return null;
        }

        if ($payments['total_paid'] < self::PP_MIN_TOTAL_PAID) {
            return redirect()->route('citizen.dashboard')
                ->with('warning_title', 'Not Eligible')
                ->with(
                    'warning',
                    'To apply for Physical Possession, your total payments (initial registration deposit + installments) must be at least ₹'
                    .number_format(self::PP_MIN_TOTAL_PAID)
                    .'. Your total paid: ₹'.number_format($payments['total_paid'])
                    .' (registration deposit: ₹'.number_format($payments['initial_deposit'])
                    .', installments: ₹'.number_format($payments['installment_paid']).').'
                );
        }

        return redirect()->route('citizen.dashboard')
            ->with('warning_title', 'Not Eligible')
            ->with(
                'warning',
                'To apply for Physical Possession, your application number must be present in the MMSAY eligible beneficiaries list.'
            );
    }

    private function findPurchaserForUser(User $user): ?object
    {
        if ($user->private_purchaser_id) {
            return DB::table('property_private_purchasers as ppp')
                ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->where('ppp.IsDeleted', 0)
                ->select('ppp.*', 'd.DistrictName', 'd.DistrictId')
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
            ->where('ppp.IsDeleted', 0)
            ->where(function ($query) use ($variants, $mobile) {
                $query->whereIn('ppp.MobileNo', $variants)
                    ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
            })
            ->select('ppp.*', 'd.DistrictName', 'd.DistrictId')
            ->orderByDesc('ppp.PrivatePurchaserId')
            ->first();
    }

    private function findPurchaserForUserWithProperty(User $user): ?object
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
            return $this->purchaserBaseQuery(false)
                ->where('ppp.PrivatePurchaserId', $user->private_purchaser_id)
                ->select($select)
                ->orderByDesc('pad.PropertyAuctionId')
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

        return $this->purchaserBaseQuery()
            ->where(function ($query) use ($variants, $mobile) {
                $query->whereIn('ppp.MobileNo', $variants)
                    ->orWhereRaw('RIGHT(CAST(ppp.MobileNo AS CHAR), 10) = ?', [$mobile]);
            })
            ->select($select)
            ->orderByDesc('pad.PropertyAuctionId')
            ->first();
    }

    private function purchaserBaseQuery(bool $activeOnly = true)
    {
        $query = DB::table('property_private_purchasers as ppp')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'ppp.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'ppp.SectorId', '=', 's.SectorId')
            ->leftJoin('property_auction_detail as pad', function ($join) {
                $join->on('pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
                    ->where('pad.IsDeleted', 0)
                    ->where('pad.IsActive', 1);
            })
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->where('ppp.IsDeleted', 0);

        if ($activeOnly) {
            $query->where('ppp.IsActive', 1);
        }

        return $query;
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
            ->whereNotIn('status', ['draft', 'returned'])
            ->latest()
            ->first();
    }

    private function findReturnedApplication(User $user): ?PhysicalPossessionApplication
    {
        return PhysicalPossessionApplication::where('user_id', $user->id)
            ->where('status', 'returned')
            ->latest()
            ->first();
    }

    private function resolveVerificationApplication(User $user, array $profile): PhysicalPossessionApplication
    {
        $returned = $this->findReturnedApplication($user);
        if ($returned) {
            return $returned;
        }

        return $this->getOrCreateDraftApplication($user, $profile);
    }

    private function replaceApplicationDocument(
        PhysicalPossessionApplication $application,
        array $profile,
        PhysicalPossessionDocument $existingDoc,
        $file
    ): void {
        if ($existingDoc->file_path && Storage::disk('public')->exists($existingDoc->file_path)) {
            Storage::disk('public')->delete($existingDoc->file_path);
        }

        $path = $this->storeApplicationDocument($application, $profile, $existingDoc->document_type, $file);

        $existingDoc->update([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'review_status' => PhysicalPossessionDocument::REVIEW_PENDING,
            'officer_remarks' => null,
            'returned_at' => null,
            'returned_by' => null,
            'is_verified' => false,
            'verified_at' => null,
        ]);
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
