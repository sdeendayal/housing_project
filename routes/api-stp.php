<?php

use App\Http\Controllers\Api\Stp\StpAuthApiController;
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
});
