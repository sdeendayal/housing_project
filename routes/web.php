<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitizenAuthController;
use App\Http\Controllers\PropertyManagementController;

Route::get('/', function () {
    return view('home.index');
})->name('home');

Route::get('/help', function () {
    return view('home.help');
});

Route::get('introduction', function () {
    return view('home.introduction');
});

Route::get('organisation-chart', function () {
    return view('home.organisationChart');
});

Route::get('whos-who', function () {
    return view('home.whosWho');
});

// ─── Citizen Login (OTP flow — two steps) ───────────────────────────────────
// guest
Route::middleware('')->group(function () {
    // Step 1: Mobile + Captcha page
    Route::get('/mmsay-citizen-login', [CitizenAuthController::class, 'showLogin'])
        ->name('citizen.login');

    Route::post('/mmsay-citizen-login/send-otp', [CitizenAuthController::class, 'sendOtp'])
        ->middleware('throttle:5,1')
        ->name('citizen.login.send-otp');

    // Step 2: OTP verification page
    Route::get('/mmsay-citizen-login/verify', [CitizenAuthController::class, 'showVerifyOtp'])
        ->name('citizen.login.verify-page');

    Route::post('/mmsay-citizen-login/verify', [CitizenAuthController::class, 'verifyOtp'])
        ->name('citizen.login.verify');

    Route::post('/mmsay-citizen-login/resend-otp', [CitizenAuthController::class, 'resendOtp'])
        ->middleware('throttle:5,1')
        ->name('citizen.login.resend-otp');
});

// Citizen protected routes
Route::middleware(['role:citizen'])->group(function () {
    Route::get('/mmsay/citizen/dashboard', [CitizenAuthController::class, 'dashboard'])
        ->name('citizen.dashboard');

    Route::get('/mmsay-profile', [CitizenAuthController::class, 'profile'])
        ->name('citizen.profile');

    Route::get('/mmsay-payment-status', function () {
        return view('mmsayPaymentStatus');
    })->name('citizen.payment-status');

    Route::get('/citizen-logout', [CitizenAuthController::class, 'logout'])
        ->name('citizen.logout');
});

// Captcha refresh (shared by citizen and department login pages)
Route::post('/refresh-captcha', function () {
    $captcha = rand(1000, 9999);
    session(['captcha' => $captcha]);

    return response()->json(['captcha' => $captcha]);
});

// ─── Department Login (unchanged — email/password) ──────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/mmsay-department-login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/mmsay-department-login', [AuthController::class, 'login'])->name('mmsay.login');
});

Route::middleware(['auth', 'role:department'])->group(function () {

    Route::get(
        '/mmsay-department-dashboard',
        [PropertyManagementController::class, 'dashboard']
    )->name('mmsay.dashboard');

    Route::get('/mmsay-department-property-registration', [PropertyManagementController::class, 'index']);

    Route::get('/get-districts/{name}', [PropertyManagementController::class, 'getDistricts']);
    Route::get('/get-cities/{name}', [PropertyManagementController::class, 'getCities']);
    Route::get('/get-sectors/{name}', [PropertyManagementController::class, 'getSectors']);

    Route::get('/export-properties', [PropertyManagementController::class, 'export'])
        ->name('properties.export');

    Route::get('/mmsay-department-cash-receipt', [PropertyManagementController::class, 'mmsayDepartmentCashReceipt']);

    Route::get('/mmsay-department-allotted-properties', function () {
        return view('mmsay.deptartmentPropertyAllotment');
    });
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
