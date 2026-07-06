<?php

use App\Http\Controllers\Api\PpOfficerAuthApiController;
use App\Http\Controllers\Api\PpOfficerApiController;
use Illuminate\Support\Facades\Route;


Route::prefix('possession')->group(function () {

    // Refresh captcha for APIs
    Route::get('/refresh-captcha', [PpOfficerAuthApiController::class, 'refreshCaptcha']);

    // Department officer OTP Login (public)
    Route::post('/department/login/send-otp', [PpOfficerAuthApiController::class, 'sendOtp']);
    Route::post('/department/login/verify', [PpOfficerAuthApiController::class, 'verifyOtp']);
    Route::post('/department/login/resend-otp', [PpOfficerAuthApiController::class, 'resendOtp']);
    
    // Authenticated Department Officer APIs
    Route::middleware(['auth:sanctum', 'role:district_officer'])->prefix('officer')->group(function () {
        Route::post('/logout', [PpOfficerAuthApiController::class, 'logout']);
        
        Route::get('/dashboard', [PpOfficerApiController::class, 'dashboard']);

        Route::get('/slots/capacity', [PpOfficerApiController::class, 'getSlotCapacity']);

        Route::get('/users', [PpOfficerApiController::class, 'users']);

        Route::get('/reports', [PpOfficerApiController::class, 'reports']);

        Route::get('/eligibility-list', [PpOfficerApiController::class, 'eligibilityList']);
        
        Route::get('/schedule/capacity/check', [PpOfficerApiController::class, 'getSlotCapacityCheck']);
        
        Route::get('/schedule/{application}', [PpOfficerApiController::class, 'scheduleForm'])->where('application', '[a-f0-9]{32}');
        Route::post('/schedule/{application}', [PpOfficerApiController::class, 'scheduleSave'])->where('application', '[a-f0-9]{32}');

        Route::get('/possession-applications', [PpOfficerApiController::class, 'applications']);
        Route::get('/verify/{application}', [PpOfficerApiController::class, 'verifyForm'])->where('application', '[a-f0-9]{32}');
        Route::post('/verify/{application}', [PpOfficerApiController::class, 'verifySave'])->where('application', '[a-f0-9]{32}');
        Route::get('/download-certificate/{application}', [PpOfficerApiController::class, 'downloadCertificate'])->where('application', '[a-f0-9]{32}');
    });

});

// Phicommerce payment gateway callback route (outside web session scope)
Route::post('/mmsay/citizen/payment/callback', [App\Http\Controllers\PaymentController::class, 'payCallback'])
    ->name('citizen.payment.callback');

