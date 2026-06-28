<?php

use App\Http\Controllers\MMGAY\MMGAYAuthController;
use App\Http\Controllers\MMGAY\DistrictCEO\DistrictCEOController;
use App\Http\Controllers\MMGAY\Citizen\MMGAYCitizenController;
use App\Http\Controllers\OtpAuthController;
use Illuminate\Support\Facades\Route;

// MMGAY Admin/Officer routes
Route::get('/mmgay/login', [MMGAYAuthController::class, 'showLogin'])->name('mmgay.login');
Route::post('/mmgay/login', [MMGAYAuthController::class, 'login'])->name('mmgay.login.submit');
Route::get('/mmgay/refresh-captcha', [MMGAYAuthController::class, 'refreshCaptcha'])
    ->name('mmgay.refresh.captcha');
Route::post('/mmgay/logout', [MMGAYAuthController::class, 'logout'])->name('mmgay.logout');

// MMGAV/MMGAY Citizen Auth Routes (Mobile & OTP)
Route::get('/mmgav-citizen-login', [OtpAuthController::class, 'showLogin'])
    ->defaults('context', 'mmgay_citizen')
    ->name('mmgay.citizen.login');
Route::redirect('/mmgay-citizen-login', '/mmgav-citizen-login');

Route::post('/mmgav-citizen-login/send-otp', [OtpAuthController::class, 'sendOtp'])
    ->defaults('context', 'mmgay_citizen')
    ->middleware('throttle:5,1')
    ->name('mmgay.citizen.login.send-otp');

Route::get('/mmgav-citizen-login/verify', [OtpAuthController::class, 'showVerifyOtp'])
    ->defaults('context', 'mmgay_citizen')
    ->name('mmgay.citizen.login.verify-page');

Route::post('/mmgav-citizen-login/verify', [OtpAuthController::class, 'verifyOtp'])
    ->defaults('context', 'mmgay_citizen')
    ->name('mmgay.citizen.login.verify');

Route::post('/mmgav-citizen-login/resend-otp', [OtpAuthController::class, 'resendOtp'])
    ->defaults('context', 'mmgay_citizen')
    ->middleware('throttle:5,1')
    ->name('mmgay.citizen.login.resend-otp');

// MMGAY Officer Protected Routes
Route::middleware(['auth', 'mmgay'])->group(function () {

    Route::get('/district-ceo/dashboard', [DistrictCEOController::class, 'dashboard'])
        ->name('district.dashboard');

    Route::get('/district-ceo/dashboard/{phase}', [DistrictCEOController::class, 'getPhaseData'])
        ->name('district.dashboard.phase');
});

// MMGAV/MMGAY Citizen Protected Routes
Route::middleware(['auth', 'mmgay', 'role:citizen'])->group(function () {
    Route::get('/mmgav/citizen/dashboard', [MMGAYCitizenController::class, 'dashboard'])
        ->name('mmgay.citizen.dashboard');
});
?>