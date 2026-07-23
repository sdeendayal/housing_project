<?php

use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\PhysicalPossession\PpLandingController;
use App\Http\Controllers\PhysicalPossession\PpOfficerController;
use App\Http\Controllers\PhysicalPossession\PpUserController;
use App\Http\Controllers\PhysicalPossession\PhysicalPossessionWorkflowController;
use Illuminate\Support\Facades\Route;

// ─── Physical Possession Module ─────────────────────────────────────────────

Route::prefix('physical-possession')->name('pp.')->group(function () {

    Route::get('/', [PpLandingController::class, 'index'])->name('landing');

    // Public allotment letter QR verification (no login)
    Route::get('/allotment/verify/{applicationNumber}', [PpUserController::class, 'publicVerifyAllotment'])
        ->name('allotment.verify')
        ->where('applicationNumber', '[0-9]+');

    // Public download certificate (no login required, directly open in browser)
    Route::get('/public/download-certificate/{application}', [PhysicalPossessionWorkflowController::class, 'publicDownloadCertificate'])
        ->name('public.download-certificate')
        ->where('application', '[a-f0-9]{32}');

    // Legacy PP login URLs → existing citizen login
    Route::redirect('/login', '/mmsay-citizen-login')->name('user.login');
    Route::redirect('/officer/login', '/physical-possession/department/login')->name('officer.login');

    // Department Officer Login (OTP — shared OtpAuthController)
    Route::get('/department/login', [OtpAuthController::class, 'showLogin'])
        ->defaults('context', 'department')
        ->name('department.login');
    Route::post('/department/login/send-otp', [OtpAuthController::class, 'sendOtp'])
        ->defaults('context', 'department')
        ->middleware('throttle:5,1')
        ->name('department.login.send-otp');
    Route::get('/department/login/verify', [OtpAuthController::class, 'showVerifyOtp'])
        ->defaults('context', 'department')
        ->name('department.login.verify-page');
    Route::post('/department/login/verify', [OtpAuthController::class, 'verifyOtp'])
        ->defaults('context', 'department')
        ->name('department.login.verify');
    Route::post('/department/login/resend-otp', [OtpAuthController::class, 'resendOtp'])
        ->defaults('context', 'department')
        ->middleware('throttle:5,1')
        ->name('department.login.resend-otp');

    // Citizen PP features (uses existing citizen session from /mmsay-citizen-login)
    Route::middleware(['auth', 'role:citizen'])->group(function () {
        Route::get('/dashboard', fn () => redirect()->route('citizen.dashboard'))->name('user.dashboard');
        
        // Disable original apply routes by redirecting or showing a message
        Route::get('/apply', function() {
            return redirect()->route('citizen.dashboard')->with('error', 'Direct citizen applications are disabled. Physical Possession must be initiated by the Site Engineer only.');
        })->name('user.apply');
        
        Route::post('/apply', [PpUserController::class, 'submitApplication'])->name('user.apply.submit');
        Route::post('/verify-certificate', [PpUserController::class, 'verifyCertificate'])->name('user.certificate.verify');
        Route::post('/verify-allotment-letter', [PpUserController::class, 'verifyAllotmentLetter'])->name('user.allotment.verify');
        Route::get('/view-form', [PpUserController::class, 'viewPrefilledForm'])->name('user.view-form');
        Route::get('/download-form', [PpUserController::class, 'downloadPrefilledForm'])->name('user.download-form');
        Route::get('/my-applications', [PpUserController::class, 'myApplications'])->name('user.applications');
        Route::get('/application/{application}', [PpUserController::class, 'showApplication'])->name('user.application.show')->where('application', '[a-f0-9]{32}');
        Route::post('/application/{application}/select-slot', [PpUserController::class, 'selectVisitSlot'])->name('user.application.select-slot')->where('application', '[a-f0-9]{32}');
        Route::get('/application/{application}/correct', [PpUserController::class, 'correctDocuments'])->name('user.application.correct')->where('application', '[a-f0-9]{32}');
        Route::post('/application/{application}/resubmit', [PpUserController::class, 'resubmitApplication'])->name('user.application.resubmit')->where('application', '[a-f0-9]{32}');
        Route::get('/application/{application}/document/{document}/view', [PpUserController::class, 'viewDocument'])->name('user.document.view')->where('application', '[a-f0-9]{32}');
        Route::get('/success/{application}', [PpUserController::class, 'success'])->name('user.success')->where('application', '[a-f0-9]{32}');
        Route::get('/slip/{application}/download', [PpUserController::class, 'downloadSlip'])->name('user.slip.download')->where('application', '[a-f0-9]{32}');
        Route::get('/slip/{application}/print', [PpUserController::class, 'printSlip'])->name('user.slip.print')->where('application', '[a-f0-9]{32}');
        Route::get('/visit-performa/{application}/download', [PpUserController::class, 'downloadVisitPerforma'])->name('user.visit-performa.download')->where('application', '[a-f0-9]{32}');
        Route::get('/visit-performa/{application}/print', [PpUserController::class, 'printVisitPerforma'])->name('user.visit-performa.print')->where('application', '[a-f0-9]{32}');
        Route::get('/profile', fn () => redirect()->route('citizen.profile'))->name('user.profile');
        Route::get('/logout', fn () => redirect()->route('citizen.logout'))->name('user.logout');

        // New citizen submit routes
        Route::get('/submit-possession', [PhysicalPossessionWorkflowController::class, 'citizenSubmitForm'])->name('citizen.submit');
        Route::post('/submit-possession', [PhysicalPossessionWorkflowController::class, 'citizenSubmit'])->name('citizen.submit.post');
    });

    // Department officer PP panel (district_officer / department role)
    Route::middleware(['auth', 'role:district_officer,department'])->prefix('officer')->name('officer.')->group(function () {
        Route::get('/dashboard', [PpOfficerController::class, 'dashboard'])->name('dashboard');
        Route::get('/slots/capacity', [PpOfficerController::class, 'getSlotCapacity'])->name('slots.capacity');
        Route::get('/users', [PpOfficerController::class, 'users'])->name('users');
        Route::get('/reports', [PpOfficerController::class, 'reports'])->name('reports');
        Route::get('/logout', [OtpAuthController::class, 'logout'])->name('logout');

        // New workflow officer routes
        Route::get('/eligibility-list', [PhysicalPossessionWorkflowController::class, 'officerEligibilityList'])->name('eligibility-list');
        Route::get('/schedule/capacity/check', [PhysicalPossessionWorkflowController::class, 'getSlotCapacityCheck'])->name('schedule.capacity-check');
        Route::get('/schedule/{application}', [PhysicalPossessionWorkflowController::class, 'officerScheduleForm'])->name('schedule-form');
        Route::post('/schedule/{application}', [PhysicalPossessionWorkflowController::class, 'officerScheduleSave'])->name('schedule-save');
        Route::get('/possession-applications', [PhysicalPossessionWorkflowController::class, 'officerApplications'])->name('possession-applications');
        Route::get('/verify/{application}', [PhysicalPossessionWorkflowController::class, 'officerVerifyForm'])->name('verify-form')->where('application', '[a-f0-9]{32}');
        Route::post('/verify/{application}', [PhysicalPossessionWorkflowController::class, 'officerVerifySave'])->name('verify-save')->where('application', '[a-f0-9]{32}');
        Route::get('/download-certificate/{application}', [PhysicalPossessionWorkflowController::class, 'officerDownloadCertificate'])->name('download-certificate')->where('application', '[a-f0-9]{32}');
    });
});
