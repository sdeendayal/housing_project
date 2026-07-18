<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitizenAuthController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhysicalPossession\PpUserController;
use App\Http\Controllers\PropertyManagementController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\EwsDashboardController;


Route::get('/', [WebsiteController::class, 'index'])->name('home');

Route::get('/help', function () {
    return view('home.help');
})->name('help');

Route::get('/introduction', function () {
    return view('home.introduction');
})->name('introduction');


Route::get('/organisation-chart', function () {
    return view('home.organisationChart');
})->name('organisation.chart');

Route::get('/whos-who', function () {
    return view('home.whosWho');
})->name('whos.who');

Route::get('/vision', function () {
    return "Vision page is under construction. Please check back later.";
})->name('vision');

Route::get('/gallery', function () {
    return "Gallery page is under construction. Please check back later.";
})->name('gallery');

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

// ─── EWS Citizen Login (OTP — shared OtpAuthController) ─────────────────────────
Route::middleware('')->group(function () {
    Route::get('/ews/citizen/login', [OtpAuthController::class, 'showLogin'])
        ->defaults('context', 'ews_citizen')
        ->name('ews.citizen.login');

    Route::post('/ews/citizen/login/send-otp', [OtpAuthController::class, 'sendOtp'])
        ->defaults('context', 'ews_citizen')
        ->middleware('throttle:5,1')
        ->name('ews.citizen.login.send-otp');

    Route::get('/ews/citizen/login/verify', [OtpAuthController::class, 'showVerifyOtp'])
        ->defaults('context', 'ews_citizen')
        ->name('ews.citizen.login.verify-page');

    Route::post('/ews/citizen/login/verify', [OtpAuthController::class, 'verifyOtp'])
        ->defaults('context', 'ews_citizen')
        ->name('ews.citizen.login.verify');

    Route::post('/ews/citizen/login/resend-otp', [OtpAuthController::class, 'resendOtp'])
        ->defaults('context', 'ews_citizen')
        ->middleware('throttle:5,1')
        ->name('ews.citizen.login.resend-otp');
});

// EWS Citizen Protected Routes
Route::middleware(['auth', 'role:ews_user'])->group(function () {
    Route::get('/ews/dashboard', [EwsDashboardController::class, 'index'])
        ->name('ews.dashboard');
    Route::get('/ews/logout', [OtpAuthController::class, 'logout'])
        ->name('ews.logout');
});

// ─── EWS Developer Login (OTP — shared OtpAuthController) ─────────────────────────
Route::middleware('')->group(function () {
    Route::get('/ews/developer/login', [OtpAuthController::class, 'showLogin'])
        ->defaults('context', 'ews_developer')
        ->name('ews.developer.login');

    Route::post('/ews/developer/login/send-otp', [OtpAuthController::class, 'sendOtp'])
        ->defaults('context', 'ews_developer')
        ->middleware('throttle:5,1')
        ->name('ews.developer.login.send-otp');

    Route::get('/ews/developer/login/verify', [OtpAuthController::class, 'showVerifyOtp'])
        ->defaults('context', 'ews_developer')
        ->name('ews.developer.login.verify-page');

    Route::post('/ews/developer/login/verify', [OtpAuthController::class, 'verifyOtp'])
        ->defaults('context', 'ews_developer')
        ->name('ews.developer.login.verify');

    Route::post('/ews/developer/login/resend-otp', [OtpAuthController::class, 'resendOtp'])
        ->defaults('context', 'ews_developer')
        ->middleware('throttle:5,1')
        ->name('ews.developer.login.resend-otp');
});

// EWS Developer Protected Routes
Route::middleware(['auth', 'role:ews_developer'])->group(function () {
    Route::get('/ews/developer/dashboard', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'index'])
        ->name('ews.developer.dashboard');
    Route::get('/ews/developer/flats/data', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'getFlatsData'])
        ->name('ews.developer.flats.data');
    Route::get('/ews/developer/flats/create', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'create'])
        ->name('ews.developer.flats.create');
    Route::post('/ews/developer/flats', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'store'])
        ->name('ews.developer.flats.store');
    Route::get('/ews/developer/flats/{secure_id}/edit', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'edit'])
        ->name('ews.developer.flats.edit');
    Route::put('/ews/developer/flats/{secure_id}', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'update'])
        ->name('ews.developer.flats.update');
    Route::delete('/ews/developer/flats/{secure_id}', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'destroy'])
        ->name('ews.developer.flats.destroy');
    Route::get('/ews/developer/flats/export/csv', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'exportCsv'])
        ->name('ews.developer.flats.export.csv');
    Route::get('/ews/developer/flats/export/pdf', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'exportPdf'])
        ->name('ews.developer.flats.export.pdf');
    Route::get('/ews/developer/logs', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'logs'])
        ->name('ews.developer.logs');
    Route::get('/ews/developer/districts-stats', [\App\Http\Controllers\EwsDeveloperDashboardController::class, 'districtStats'])
        ->name('ews.developer.districts-stats');
    Route::get('/ews/developer/logout', [OtpAuthController::class, 'logout'])
        ->name('ews.developer.logout');
});

// Citizen protected routes
Route::middleware(['auth', 'role:citizen'])->group(function () {
    Route::get('/mmsay/citizen/dashboard', [CitizenAuthController::class, 'dashboard'])
        ->name('citizen.dashboard');

    Route::get('/mmsay-profile', [CitizenAuthController::class, 'profile'])
        ->name('citizen.profile');

    Route::get('/mmsay-payment-status', [CitizenAuthController::class, 'paymentStatus'])
        ->name('citizen.payment-status');

    Route::get('/mmsay/citizen/payment', fn() => redirect()->route('citizen.payment-status'))
        ->name('citizen.payment');

    Route::get('/mmsay/citizen/payment/pay', [PaymentController::class, 'payForm'])
        ->name('citizen.payment.pay');

    Route::post('/mmsay/citizen/payment/pay', [PaymentController::class, 'paySubmit'])
        ->name('citizen.payment.pay.submit');

    Route::get('/mmsay/citizen/payment/reconcile/{id}', [PaymentController::class, 'reconcile'])
        ->name('citizen.payment.reconcile')
        ->where('id', '[0-9]+');



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

    Route::get('/mmsay/citizen/grievances', [GrievanceController::class, 'index'])
        ->name('citizen.grievances.index');

    Route::get('/mmsay/citizen/grievances/create', [GrievanceController::class, 'create'])
        ->name('citizen.grievances.create');

    Route::post('/mmsay/citizen/grievances', [GrievanceController::class, 'store'])
        ->name('citizen.grievances.store');

    Route::get('/mmsay/citizen/grievances/{grievance}', [GrievanceController::class, 'show'])
        ->name('citizen.grievances.show')
        ->where('grievance', '[a-f0-9]{32}');

    Route::get('/citizen-logout', [OtpAuthController::class, 'logout'])
        ->name('citizen.logout');
});

// Public payment result page (to prevent SameSite/Host session issues on redirect)
Route::get('/mmsay/citizen/payment/result', [PaymentController::class, 'result'])
    ->name('citizen.payment.result');

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

    Route::get('/mmsay-department-allotted-properties', [PropertyManagementController::class, 'mmsayDepartmentAllottedProperties']);

    Route::get('/mmsay-department-draw', [PropertyManagementController::class, 'mmsayDepartmentDraw']);

    Route::get('/mmsay-department-draw/details/{id}', [PropertyManagementController::class, 'districtDetails']);

    Route::get('/mmsay-department-emi-payments', [PropertyManagementController::class, 'departmentEmiPayments'])
        ->name('mmsay.department.emi.payments');

    Route::get('/mmsay-emi-status/{assetId}', [PropertyManagementController::class, 'emiStatus'])
        ->name('mmsay.emi.status');

    Route::get('/mmsay-department-physical-letter', [PropertyManagementController::class, 'departmentPhysicalLetter'])->name('mmsay.department.physical.letter');

    Route::get(
        '/allotment-letter/{id}',
        [PropertyManagementController::class, 'downloadAllotmentLetter']
    )->name('allotment.letter');

    Route::get(
        '/allotment-letter-pdf/{id}',
        [PropertyManagementController::class, 'exportAllotmentLetterPdf']
    )->name('allotment.letter.pdf');

    Route::get(
        'mmsay-department-property-emi-calculation',
        [PropertyManagementController::class, 'departmentPropertyEmiCalculation']
    );

    Route::get(
        '/emi-get-cities',
        [PropertyManagementController::class, 'emiGetCities']
    );

    Route::get(
        '/emi-get-sectors',
        [PropertyManagementController::class, 'emiGetSectors']
    );

    Route::get(
        '/emi-get-assets',
        [PropertyManagementController::class, 'emiGetAssets']
    );

    Route::get(
        '/emi-get-asset-details',
        [PropertyManagementController::class, 'emiGetAssetDetails']
    );

    Route::get(
        '/physical-possession/view/{assetId}',
        [PropertyManagementController::class, 'view']
    )
        ->name('physical-possession.view');

    Route::get(
        'full-paid-properties',
        [PropertyManagementController::class, 'fullPaidProperties']
    )->name('full-paid-properties');

    Route::get(
        'pending-properties',
        [PropertyManagementController::class, 'pendingProperties']
    )->name('pending-properties');



    // CMS Routes

    Route::get('/mmsay-department-add-banner', [CmsController::class, 'addBanner']);

    Route::post('/department-banners-store', [CmsController::class, 'saveBanner'])
        ->name('department-banners-store');

    Route::get('/banner-delete/{id}', [CmsController::class, 'deleteBanner'])
        ->name('banner-delete');

    Route::get('/banner-deactivate/{id}', [CmsController::class, 'deactivateBanner'])
        ->name('banner-deactivate');

    Route::get('/banner-activate/{id}', [CmsController::class, 'activateBanner'])
        ->name('banner-activate');

    Route::get('/mmsay-department-add-news', [CmsController::class, 'addNews']);

    Route::post('/department-news-store', [CmsController::class, 'saveNews'])->name('department-news-store');

    Route::put('news-update/{id}', [CmsController::class, 'updateNews'])->name('news-update');

    Route::get('/mmsay-department-add-district-officer', [CmsController::class, 'departmentAddDistrictOfficer'])
        ->name('mmsay-department-add-district-officer');

    Route::post('/officers/store', [CmsController::class, 'storeOfficer'])->name('officers.store');

    Route::get('/mmsay-officers-list', [CmsController::class, 'listOfficers'])->name('mmsay.officers.list');

    Route::post('/mmsay-officer-update', [CmsController::class, 'updateOfficer'])
        ->name('mmsay.officer.update');

    Route::post('/mmsay-transfer-officer', [CmsController::class, 'transferOfficer'])->name('mmsay.officer.transfer');

    Route::post('/mmsay-officer-delete', [CmsController::class, 'deleteOfficer'])
        ->name('mmsay.officer.delete');


});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── EWS Department Login & Dashboard ─────────────────────────
Route::get('/ews/department/login', [\App\Http\Controllers\EwsDepartmentController::class, 'showLogin'])->name('ews.department.login');
Route::post('/ews/department/login', [\App\Http\Controllers\EwsDepartmentController::class, 'login'])->name('ews.department.login.submit');

Route::middleware(['auth', 'role:ews_department'])->group(function () {
    Route::get('/ews/department/dashboard', [\App\Http\Controllers\EwsDepartmentController::class, 'dashboard'])->name('ews.department.dashboard');
    Route::get('/ews/department/list', [\App\Http\Controllers\EwsDepartmentController::class, 'list'])->name('ews.department.list');
    Route::get('/ews/department/beneficiary/data', [\App\Http\Controllers\EwsDepartmentController::class, 'getBeneficiaryData'])->name('ews.department.beneficiary.data');
    Route::get('/ews/department/beneficiary/{type}/{secure_id}', [\App\Http\Controllers\EwsDepartmentController::class, 'showBeneficiary'])->name('ews.department.beneficiary.show');

    // ─── EWS Developer Management Routes for Department Panel ─────────
    Route::get('/ews/department/developers', [\App\Http\Controllers\EwsDepartmentController::class, 'developersIndex'])->name('ews.department.developers.index');
    Route::get('/ews/department/developers/data', [\App\Http\Controllers\EwsDepartmentController::class, 'getDevelopersData'])->name('ews.department.developers.data');
    Route::post('/ews/department/developers', [\App\Http\Controllers\EwsDepartmentController::class, 'storeDeveloper'])->name('ews.department.developers.store');
    Route::put('/ews/department/developers/{secure_id}', [\App\Http\Controllers\EwsDepartmentController::class, 'updateDeveloper'])->name('ews.department.developers.update');
    Route::delete('/ews/department/developers/{secure_id}', [\App\Http\Controllers\EwsDepartmentController::class, 'destroyDeveloper'])->name('ews.department.developers.destroy');

    Route::get('/ews/department/developer-flats', [\App\Http\Controllers\EwsDepartmentController::class, 'developerFlatsIndex'])->name('ews.department.developer-flats.index');
    Route::get('/ews/department/developer-flats/data', [\App\Http\Controllers\EwsDepartmentController::class, 'getDeveloperFlatsData'])->name('ews.department.developer-flats.data');

    Route::get('/ews/department/developer-logs', [\App\Http\Controllers\EwsDepartmentController::class, 'developerLogsIndex'])->name('ews.department.developer-logs.index');
    Route::get('/ews/department/developer-logs/data', [\App\Http\Controllers\EwsDepartmentController::class, 'getDeveloperLogsData'])->name('ews.department.developer-logs.data');

    // Export Routes (CSV, Excel, PDF)
    Route::get('/ews/department/export/beneficiaries', [\App\Http\Controllers\EwsDepartmentController::class, 'exportBeneficiaries'])->name('ews.department.export.beneficiaries');
    Route::get('/ews/department/export/developers', [\App\Http\Controllers\EwsDepartmentController::class, 'exportDevelopers'])->name('ews.department.export.developers');
    Route::get('/ews/department/export/developer-flats', [\App\Http\Controllers\EwsDepartmentController::class, 'exportDeveloperFlats'])->name('ews.department.export.developer-flats');
    Route::get('/ews/department/export/developer-logs', [\App\Http\Controllers\EwsDepartmentController::class, 'exportDeveloperLogs'])->name('ews.department.export.developer-logs');

    Route::get('/ews/department/logout', [\App\Http\Controllers\EwsDepartmentController::class, 'logout'])->name('ews.department.logout');
});


