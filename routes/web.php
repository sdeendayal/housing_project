<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

Route::get('/mmsay-citizen-login', function () {
    return view('mmsay.citizenLogin');
});



Route::get('/mmsay.citizen.dashboard', function () {
    return view('mmsayCitizenDashboard');
});

Route::get('/mmsay-profile', function () {
    return view('mmsayCitizenProfile');
});

Route::get('/mmsay-payment-status', function () {
    return view('mmsayPaymentStatus');
});

// Department Menu



Route::get('/mmsay-department-allotted-properties', function () {
    return view('mmsay.deptartmentPropertyAllotment');
});

// Deaptment Login Routes Working

Route::middleware('guest')->group(function () {
    Route::get('/mmsay-department-login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/mmsay-department-login', [AuthController::class, 'login'])->name('mmsay.login');
});

Route::post('/refresh-captcha', function () {
    $captcha = rand(1000, 9999);
    session(['captcha' => $captcha]);

    return response()->json(['captcha' => $captcha]);
});

Route::middleware(['auth', 'role:department'])->group(function () {
    Route::get('/mmsay-department-dashboard', function () {
        return view('mmsay.departmentDashboard');
    });

    // LIST PAGE
    Route::get('/mmsay-department-property-registration', [PropertyManagementController::class, 'index']);

    Route::get('/get-districts/{name}', [PropertyManagementController::class, 'getDistricts']);
    Route::get('/get-cities/{name}', [PropertyManagementController::class, 'getCities']);
    Route::get('/get-sectors/{name}', [PropertyManagementController::class, 'getSectors']);

    Route::get('/export-properties', [PropertyManagementController::class, 'export'])
    ->name('properties.export');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');






