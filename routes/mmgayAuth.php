<?php

use App\Http\Controllers\MMGAY\MMGAYAuthController;
use App\Http\Controllers\MMGAY\DistrictCEO\DistrictCEOController;
use App\Http\Controllers\MMGAY\Citizen\MMGAYCitizenController;
use App\Http\Controllers\OtpAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MMGAY\DC\DCController;

// MMGAY Admin/Officer routes
Route::get('/mmgay/login', [MMGAYAuthController::class, 'showLogin'])->name('mmgay.login');
Route::post('/mmgay/login', [MMGAYAuthController::class, 'login'])->name('mmgay.login.submit');
Route::get('/mmgay/refresh-captcha', [MMGAYAuthController::class, 'refreshCaptcha'])
    ->name('mmgay.refresh.captcha');
Route::post('/mmgay/logout', [MMGAYAuthController::class, 'logout'])->name('mmgay.logout');

// MMGAV Villager Auth Routes (Mobile & OTP)
Route::get('/mmgav/login', [OtpAuthController::class, 'showLogin'])
    ->defaults('context', 'mmgav_villager')
    ->name('mmgav.villager.login');

Route::post('/mmgav/login/send-otp', [OtpAuthController::class, 'sendOtp'])
    ->defaults('context', 'mmgav_villager')
    ->middleware('throttle:5,1')
    ->name('mmgav.villager.login.send-otp');

Route::get('/mmgav/login/verify', [OtpAuthController::class, 'showVerifyOtp'])
    ->defaults('context', 'mmgav_villager')
    ->name('mmgav.villager.login.verify-page');

Route::post('/mmgav/login/verify', [OtpAuthController::class, 'verifyOtp'])
    ->defaults('context', 'mmgav_villager')
    ->name('mmgav.villager.login.verify');

Route::post('/mmgav/login/resend-otp', [OtpAuthController::class, 'resendOtp'])
    ->defaults('context', 'mmgav_villager')
    ->middleware('throttle:5,1')
    ->name('mmgav.villager.login.resend-otp');

Route::post('/mmgav/logout', [OtpAuthController::class, 'logout'])->name('mmgav.villager.logout');

// Legacy MMGAV citizen URLs (redirect to villager login)
Route::redirect('/mmgav-citizen-login', '/mmgav/login');
Route::redirect('/mmgay-citizen-login', '/mmgav/login');
Route::redirect('/mmgav-citizen-login/verify', '/mmgav/login/verify');

// MMGAY Officer Protected Routes
Route::middleware(['auth', 'mmgay'])->group(function () {

    Route::get('/district-ceo/dashboard', [DistrictCEOController::class, 'dashboard'])
        ->name('district.dashboard');

    Route::get('/district-ceo/dashboard/{phase}', [DistrictCEOController::class, 'getPhaseData'])
        ->name('district.dashboard.phase');

    Route::get('/district-ceo/list/{phase}/{status}', [DistrictCEOController::class, 'list'])
        ->name('district.list');

    Route::get('/owner/{id}', [DistrictCEOController::class, 'viewOwner'])
        ->name('owner.view');

    Route::post(
        '/district-ceo/owner/action/{id}',
        [DistrictCEOController::class, 'ownerAction']
    )
        ->name('district.owner.action');

    Route::post('/district/owner/{id}/grievance', [DistrictCEOController::class, 'submitGrievance'])
        ->name('district.owner.grievance.submit');

    Route::post('/district/owner/{id}/action', [DistrictCEOController::class, 'ownerAction'])
        ->name('district.owner.action');

});

Route::prefix('dc')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/dashboard/{phase?}', [DCController::class, 'dashboard'])
            ->name('dc.dashboard');

        Route::get('/owner-list', [DCController::class, 'ownerList'])
            ->name('dc.owner.list');

        Route::get('/owner/{id}', [DCController::class, 'ownerView'])
            ->name('dc.owner.view');

        Route::post('/owner/action/{id}', [DCController::class, 'ownerAction'])
            ->name('dc.owner.action');

        Route::post('/owner/grievance/{id}', [DCController::class, 'submitGrievance'])
            ->name('dc.owner.grievance.submit');

        Route::post('/logout', [MMGAYAuthController::class, 'logout'])
            ->name('dc.logout');
    });

// MMGAV Villager Protected Routes
Route::middleware(['auth', 'mmgay', 'role:villager'])->group(function () {
    Route::get('/mmgav/villager/dashboard', [MMGAYCitizenController::class, 'dashboard'])
        ->name('mmgav.villager.dashboard');

    // Legacy URL and route name for existing bookmarks/DB records
    Route::get('/mmgav/citizen/dashboard', [MMGAYCitizenController::class, 'dashboard'])
        ->name('mmgay.citizen.dashboard');
});
