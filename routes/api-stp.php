<?php

use App\Http\Controllers\Api\Stp\StpAuthApiController;
use App\Http\Controllers\Api\Stp\StpApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EWS STP Mobile API Routes
|--------------------------------------------------------------------------
|
| Base URL Prefix: /api/stp
|
*/

// Public Authentication APIs for Mobile App
Route::prefix('login')->group(function () {
    Route::post('/send-otp', [StpAuthApiController::class, 'sendOtp'])->middleware('throttle:10,1');
    Route::post('/verify-otp', [StpAuthApiController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::post('/resend-otp', [StpAuthApiController::class, 'resendOtp'])->middleware('throttle:5,1');
    Route::post('/', [StpAuthApiController::class, 'login'])->middleware('throttle:10,1');
});

// Shorthand aliases for mobile apps
Route::post('/send-otp', [StpAuthApiController::class, 'sendOtp'])->middleware('throttle:10,1');
Route::post('/verify-otp', [StpAuthApiController::class, 'verifyOtp'])->middleware('throttle:10,1');
Route::post('/resend-otp', [StpAuthApiController::class, 'resendOtp'])->middleware('throttle:5,1');
Route::post('/login', [StpAuthApiController::class, 'login'])->middleware('throttle:10,1');

// Protected STP APIs (Requires Bearer Token & STP Role)
Route::middleware(['auth:sanctum', 'role:ews_developer,ews_stp,stp'])->group(function () {
    Route::get('/profile', [StpAuthApiController::class, 'profile']);
    Route::post('/logout', [StpAuthApiController::class, 'logout']);

    // STP Zone & Assigned Districts
    Route::get('/districts', [StpApiController::class, 'getZoneDistricts']);
    Route::get('/zone-districts', [StpApiController::class, 'getZoneDistricts']);

    // Master Dropdown Data (Towns, Projects, Blocks)
    Route::get('/towns', [StpApiController::class, 'getTowns']);
    Route::get('/projects', [StpApiController::class, 'getProjects']);
    Route::get('/blocks', [StpApiController::class, 'getBlocks']);
});
