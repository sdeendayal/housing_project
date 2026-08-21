<?php

use App\Http\Controllers\Api\PpOfficerAuthApiController;
use App\Http\Controllers\Api\PpOfficerApiController;
use App\Http\Controllers\Api\MmgayBdoAuthApiController;
use App\Http\Controllers\Api\MmgayBdoApiController;
use App\Http\Controllers\Api\MmgayVillagerAuthApiController;
use App\Http\Controllers\Api\MmgayVillagerApiController;
use Illuminate\Support\Facades\Route;

// MMGAY Physical Possession APIs
Route::prefix('mmgay')->group(function () {

    // Refresh captcha
    Route::get('/refresh-captcha', [MmgayBdoAuthApiController::class, 'refreshCaptcha']);

    // BDO Login
    Route::post('/bdo/login', [MmgayBdoAuthApiController::class, 'login']);

    // Villager login (OTP base)
    Route::post('/villager/login/send-otp', [MmgayVillagerAuthApiController::class, 'sendOtp']);
    Route::post('/villager/login/verify', [MmgayVillagerAuthApiController::class, 'verifyOtp']);
    Route::post('/villager/login/resend-otp', [MmgayVillagerAuthApiController::class, 'resendOtp']);

    // BDO Authenticated APIs
    Route::middleware(['auth:sanctum', 'role:mmgav_bdeo'])->prefix('bdo')->group(function () {
        Route::post('/logout', [MmgayBdoAuthApiController::class, 'logout']);
        Route::get('/dashboard', [MmgayBdoApiController::class, 'dashboard']);
        Route::get('/eligibility-list', [MmgayBdoApiController::class, 'eligibilityList']);
        Route::get('/schedule/capacity/check', [MmgayBdoApiController::class, 'getSlotCapacityCheck']);
        Route::get('/schedule/{secure_id}', [MmgayBdoApiController::class, 'scheduleForm']);
        Route::post('/schedule/{secure_id}', [MmgayBdoApiController::class, 'scheduleSave']);
        Route::get('/possession-applications', [MmgayBdoApiController::class, 'applications']);
        Route::get('/verify/{secure_id}', [MmgayBdoApiController::class, 'verifyForm']);
        Route::post('/verify/{secure_id}', [MmgayBdoApiController::class, 'verifySave']);
        Route::get('/download-certificate/{secure_id}', [MmgayBdoApiController::class, 'downloadCertificate']);
        Route::get('/site-development', [MmgayBdoApiController::class, 'siteDevelopmentGet']);
        Route::post('/site-development', [MmgayBdoApiController::class, 'siteDevelopmentSave']);
        Route::get('/phase-report', [MmgayBdoApiController::class, 'phaseReport']);
    });

    // Villager Authenticated APIs
    Route::middleware(['auth:sanctum', 'role:villager'])->prefix('villager')->group(function () {
        Route::post('/logout', [MmgayVillagerAuthApiController::class, 'logout']);
        Route::get('/dashboard', [MmgayVillagerApiController::class, 'dashboard']);
        Route::get('/submit-possession', [MmgayVillagerApiController::class, 'submitPossessionForm']);
        Route::post('/submit-possession', [MmgayVillagerApiController::class, 'submitPossession']);
        Route::get('/download-slip', [MmgayVillagerApiController::class, 'downloadSlip']);
    });
});

// Original MMSAY Possession APIs
Route::prefix('possession')->group(function () {

    // Refresh captcha for APIs
    Route::get('/refresh-captcha', [PpOfficerAuthApiController::class, 'refreshCaptcha']);

    // BDO Login fallback (Support for /api/possession/bdo/login URL)
    Route::post('/bdo/login', [MmgayBdoAuthApiController::class, 'login']);

    // BDO Authenticated APIs fallback (Support for /api/possession/bdo/...)
    Route::middleware(['auth:sanctum', 'role:mmgav_bdeo'])->prefix('bdo')->group(function () {
        Route::post('/logout', [MmgayBdoAuthApiController::class, 'logout']);
        Route::get('/dashboard', [MmgayBdoApiController::class, 'dashboard']);
        Route::get('/eligibility-list', [MmgayBdoApiController::class, 'eligibilityList']);
        Route::get('/schedule/capacity/check', [MmgayBdoApiController::class, 'getSlotCapacityCheck']);
        Route::get('/schedule/{secure_id}', [MmgayBdoApiController::class, 'scheduleForm']);
        Route::post('/schedule/{secure_id}', [MmgayBdoApiController::class, 'scheduleSave']);
        Route::get('/possession-applications', [MmgayBdoApiController::class, 'applications']);
        Route::get('/verify/{secure_id}', [MmgayBdoApiController::class, 'verifyForm']);
        Route::post('/verify/{secure_id}', [MmgayBdoApiController::class, 'verifySave']);
        Route::get('/download-certificate/{secure_id}', [MmgayBdoApiController::class, 'downloadCertificate']);
        Route::get('/site-development', [MmgayBdoApiController::class, 'siteDevelopmentGet']);
        Route::post('/site-development', [MmgayBdoApiController::class, 'siteDevelopmentSave']);
        Route::get('/phase-report', [MmgayBdoApiController::class, 'phaseReport']);
    });

    // Department officer OTP Login (public)
    Route::post('/department/login/send-otp', [PpOfficerAuthApiController::class, 'sendOtp']);
    Route::post('/department/login/verify', [PpOfficerAuthApiController::class, 'verifyOtp']);
    Route::post('/department/login/resend-otp', [PpOfficerAuthApiController::class, 'resendOtp']);
    
    // Authenticated Department Officer APIs
    Route::middleware(['auth:sanctum', 'role:district_officer'])->prefix('officer')->group(function () {
        Route::post('/logout', [PpOfficerAuthApiController::class, 'logout']);
        
        Route::get('/dashboard', [PpOfficerApiController::class, 'dashboard']);
        Route::get('/draw-documents', [PpOfficerApiController::class, 'getDrawDocuments']);

        Route::get('/slots/capacity', [PpOfficerApiController::class, 'getSlotCapacity']);

        Route::get('/users', [PpOfficerApiController::class, 'users']);

        Route::get('/reports', [PpOfficerApiController::class, 'reports']);

        Route::get('/eligibility-list', [PpOfficerApiController::class, 'eligibilityList']);
        Route::get('/caste-eligibility', [PpOfficerApiController::class, 'casteEligibility']);
        
        Route::get('/schedule/capacity/check', [PpOfficerApiController::class, 'getSlotCapacityCheck']);
        
        Route::get('/schedule/{application}', [PpOfficerApiController::class, 'scheduleForm'])->where('application', '[a-f0-9]{32}');
        Route::post('/schedule/{secure_id}', [PpOfficerApiController::class, 'scheduleSave'])->where('secure_id', '[a-f0-9]{32}');

        Route::get('/possession-applications', [PpOfficerApiController::class, 'applications']);
        Route::get('/verify/{application}', [PpOfficerApiController::class, 'verifyForm'])->where('application', '[a-f0-9]{32}');
        Route::post('/verify/{application}', [PpOfficerApiController::class, 'verifySave'])->where('application', '[a-f0-9]{32}');
        Route::get('/download-certificate/{application}', [PpOfficerApiController::class, 'downloadCertificate'])->where('application', '[a-f0-9]{32}');
    });

});

// Phicommerce payment gateway callback route (outside web session scope)
Route::post('/mmsay/citizen/payment/callback', [App\Http\Controllers\PaymentController::class, 'payCallback'])
    ->name('citizen.payment.callback');

