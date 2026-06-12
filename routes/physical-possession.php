<?php

use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\PhysicalPossession\PpLandingController;
use App\Http\Controllers\PhysicalPossession\PpOfficerController;
use App\Http\Controllers\PhysicalPossession\PpUserController;
use Illuminate\Support\Facades\Route;

// ─── Physical Possession Module ─────────────────────────────────────────────

Route::prefix('physical-possession')->name('pp.')->group(function () {

    Route::get('/', [PpLandingController::class, 'index'])->name('landing');

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
        Route::get('/apply', [PpUserController::class, 'applyForm'])->name('user.apply');
        Route::post('/apply', [PpUserController::class, 'submitApplication'])->name('user.apply.submit');
        Route::get('/view-form', [PpUserController::class, 'viewPrefilledForm'])->name('user.view-form');
        Route::get('/download-form', [PpUserController::class, 'downloadPrefilledForm'])->name('user.download-form');
        Route::get('/my-applications', [PpUserController::class, 'myApplications'])->name('user.applications');
        Route::get('/application/{application}', [PpUserController::class, 'showApplication'])->name('user.application.show')->where('application', '[a-f0-9]{32}');
        Route::get('/application/{application}/document/{document}/view', [PpUserController::class, 'viewDocument'])->name('user.document.view')->where('application', '[a-f0-9]{32}');
        Route::get('/success/{application}', [PpUserController::class, 'success'])->name('user.success')->where('application', '[a-f0-9]{32}');
        Route::get('/slip/{application}/download', [PpUserController::class, 'downloadSlip'])->name('user.slip.download')->where('application', '[a-f0-9]{32}');
        Route::get('/slip/{application}/print', [PpUserController::class, 'printSlip'])->name('user.slip.print')->where('application', '[a-f0-9]{32}');
        Route::get('/visit-performa/{application}/download', [PpUserController::class, 'downloadVisitPerforma'])->name('user.visit-performa.download')->where('application', '[a-f0-9]{32}');
        Route::get('/visit-performa/{application}/print', [PpUserController::class, 'printVisitPerforma'])->name('user.visit-performa.print')->where('application', '[a-f0-9]{32}');
        Route::get('/profile', fn () => redirect()->route('citizen.profile'))->name('user.profile');
        Route::get('/logout', fn () => redirect()->route('citizen.logout'))->name('user.logout');
    });

    // Department officer PP panel (district_officer role)
    Route::middleware(['auth', 'role:district_officer'])->prefix('officer')->name('officer.')->group(function () {
        Route::get('/dashboard', [PpOfficerController::class, 'dashboard'])->name('dashboard');
        Route::get('/applications', [PpOfficerController::class, 'applications'])->name('applications');
        Route::get('/applications/approved', [PpOfficerController::class, 'approvedApplications'])->name('applications.approved');
        Route::get('/applications/rejected', [PpOfficerController::class, 'rejectedApplications'])->name('applications.rejected');
        Route::get('/application/{application}', [PpOfficerController::class, 'showApplication'])->name('application.show')->where('application', '[a-f0-9]{32}');
        Route::post('/application/{application}/decide', [PpOfficerController::class, 'decide'])->name('application.decide')->where('application', '[a-f0-9]{32}');
        Route::post('/application/{application}/approve', [PpOfficerController::class, 'approve'])->name('application.approve')->where('application', '[a-f0-9]{32}');
        Route::post('/application/{application}/reject', [PpOfficerController::class, 'reject'])->name('application.reject')->where('application', '[a-f0-9]{32}');
        Route::get('/application/{application}/document/{document}', [PpOfficerController::class, 'downloadDocument'])->name('document.download')->where('application', '[a-f0-9]{32}');
        Route::get('/users', [PpOfficerController::class, 'users'])->name('users');
        Route::get('/reports', [PpOfficerController::class, 'reports'])->name('reports');
        Route::get('/logout', [OtpAuthController::class, 'logout'])->name('logout');
    });
});
