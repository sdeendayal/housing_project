<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MMGAY\MMGAYAuthController;
use App\Http\Controllers\MMGAY\DistrictCEO\DistrictCEOController;

Route::get('/mmgay/login', [MMGAYAuthController::class, 'showLogin'])->name('mmgay.login');
Route::post('/mmgay/login', [MMGAYAuthController::class, 'login'])->name('mmgay.login.submit');
Route::get('/mmgay/refresh-captcha', [MMGAYAuthController::class, 'refreshCaptcha'])
    ->name('mmgay.refresh.captcha');
Route::post('/mmgay/logout', [MMGAYAuthController::class, 'logout'])->name('mmgay.logout');

Route::middleware(['auth', 'mmgay'])->group(function () {

    Route::get('/district-ceo/dashboard', [DistrictCEOController::class, 'dashboard'])
        ->name('district.dashboard');

    Route::get('/district-ceo/dashboard/{phase}', [DistrictCEOController::class, 'getPhaseData'])
        ->name('district.dashboard.phase');
});
?>