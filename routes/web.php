<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitizenAuthController;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\PhysicalPossession\PpUserController;
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

// ─── Citizen Login (OTP — shared OtpAuthController) ─────────────────────────
Route::middleware('')->group(function () {
    Route::get('/mmsay-citizen-login', [OtpAuthController::class, 'showLogin'])
        ->defaults('context', 'citizen')
        ->name('citizen.login');

    Route::post('/mmsay-citizen-login/send-otp', [OtpAuthController::class, 'sendOtp'])
        ->defaults('context', 'citizen')
        ->middleware('throttle:5,1')
        ->name('citizen.login.send-otp');

    Route::get('/mmsay-citizen-login/verify', [OtpAuthController::class, 'showVerifyOtp'])
        ->defaults('context', 'citizen')
        ->name('citizen.login.verify-page');

    Route::post('/mmsay-citizen-login/verify', [OtpAuthController::class, 'verifyOtp'])
        ->defaults('context', 'citizen')
        ->name('citizen.login.verify');

    Route::post('/mmsay-citizen-login/resend-otp', [OtpAuthController::class, 'resendOtp'])
        ->defaults('context', 'citizen')
        ->middleware('throttle:5,1')
        ->name('citizen.login.resend-otp');
});

// Citizen protected routes
Route::middleware(['auth', 'role:citizen'])->group(function () {
    Route::get('/mmsay/citizen/dashboard', [CitizenAuthController::class, 'dashboard'])
        ->name('citizen.dashboard');

    Route::get('/mmsay-profile', [CitizenAuthController::class, 'profile'])
        ->name('citizen.profile');

    Route::get('/mmsay-payment-status', [CitizenAuthController::class, 'paymentStatus'])
        ->name('citizen.payment-status');

    Route::get('/mmsay/citizen/cash-receipt/{receipt}/download', [CitizenAuthController::class, 'downloadCashReceipt'])
        ->where('receipt', '[0-9]+')
        ->name('citizen.cash-receipt.download');

    Route::get('/mmsay/citizen/property-details', [CitizenAuthController::class, 'propertyDetails'])
        ->name('citizen.property-details');

    Route::get('/mmsay-allotment-letter', [PpUserController::class, 'viewAllotmentLetter'])
        ->name('citizen.allotment-letter');

    Route::get('/mmsay-allotment-letter/download', [PpUserController::class, 'downloadAllotmentLetter'])
        ->name('citizen.allotment-letter.download');

    Route::get('/mmsay-possession-certificate', [PpUserController::class, 'viewPossessionCertificate'])
        ->name('citizen.possession-certificate');

    Route::get('/citizen-logout', [OtpAuthController::class, 'logout'])
        ->name('citizen.logout');
});

// Captcha refresh (shared by citizen and department login pages)
Route::post('/refresh-captcha', function () {
    $captcha = rand(1000, 9999);
    session(['captcha' => $captcha]);

    return response()->json(['captcha' => $captcha]);
});

Route::get('/mmsay-department-allotted-properties', function () {
    return view('mmsay.deptartmentPropertyAllotment');
});

// ─── Department Login (unchanged — email/password) ──────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/mmsay-department-login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/mmsay-department-login', [AuthController::class, 'login'])->name('mmsay.login');
});

Route::middleware(['auth', 'role:department'])->group(function () {
    Route::get('/mmsay-department-dashboard', function () {
        return view('mmsay.departmentDashboard');
    })->name('department.dashboard');

    Route::get('/mmsay-department-property-registration', [PropertyManagementController::class, 'index']);

    Route::get('/get-districts/{name}', [PropertyManagementController::class, 'getDistricts']);
    Route::get('/get-cities/{name}', [PropertyManagementController::class, 'getCities']);
    Route::get('/get-sectors/{name}', [PropertyManagementController::class, 'getSectors']);

    Route::get('/export-properties', [PropertyManagementController::class, 'export'])
        ->name('properties.export');

    Route::get('/mmsay-department-cash-receipt', [PropertyManagementController::class, 'mmsayDepartmentCashReceipt']);
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
