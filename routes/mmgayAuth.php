<?php

use App\Http\Controllers\MMGAY\MMGAYAuthController;
use App\Http\Controllers\MMGAY\DistrictCEO\DistrictCEOController;
use App\Http\Controllers\MMGAY\Citizen\MMGAYCitizenController;
use App\Http\Controllers\OtpAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MMGAY\DC\DCController;
use App\Http\Controllers\MMGAY\SuperAdmin\SuperAdminController;

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

    Route::get(
        '/district-ceo/dashboard/{phase?}',
        [DistrictCEOController::class, 'dashboard']
    )
        ->where('phase', 'all|1|2|3')
        ->name('district.dashboard');

    Route::get(
        '/district-ceo/dashboard/site-development/{villageId}',
        [DistrictCEOController::class, 'siteDevelopment']
    )
        ->whereNumber('villageId')
        ->name('district.site-development.show');

    Route::get(
        '/district-ceo/possession/{filter?}',
        [DistrictCEOController::class, 'possessionList']
    )
        ->where(
            'filter',
            'all|schedule_pending|awaiting_citizen|field_visit_pending|possession_pending|verified'
        )
        ->name('district.possession.list');

    /*
    |--------------------------------------------------------------------------
    | Existing possession detail route
    |--------------------------------------------------------------------------
    | Point this route to your existing detail method. That method and its
    | verified/non-verified Blade design do not need to be changed.
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/district-ceo/possession-export/csv',
        [DistrictCEOController::class, 'exportPossessionCsv']
    )->name('district.possession.export.csv');

    Route::get(
        '/district-ceo/physical-possession/{secure_id}',
        [DistrictCEOController::class, 'physicalPossessionView']
    )->name('district.possession.show');

    /*
    |--------------------------------------------------------------------------
    | District CEO Reports
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/district-ceo/dashboard/report/{type}',
        [DistrictCEOController::class, 'report']
    )->name('district.dashboard.report');

    Route::get(
        '/report/applicants',
        [DistrictCeoController::class, 'applicantReport']
    )->name('district.dashboard.applicants');

    Route::get(
        '/report/applicants/print',
        [DistrictCeoController::class, 'printApplicantReport']
    )->name('district.dashboard.applicants.print');

    Route::get(
        '/report/applicants/excel',
        [DistrictCeoController::class, 'exportApplicantReportExcel']
    )->name('district.dashboard.applicants.excel');

    Route::get(
        '/report/applicants/csv',
        [DistrictCEOController::class, 'exportApplicantReportCsv']
    )->name('district.dashboard.applicants.csv');
    /*
    |--------------------------------------------------------------------------
    | Village Wise Summary
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/district-ceo/dashboard/village-summary',
        [DistrictCEOController::class, 'villageSummary']
    )->name('district.dashboard.village-summary');

    Route::get(
        '/district-ceo/dashboard/village-summary/pdf/{phase?}',
        [DistrictCEOController::class, 'exportVillageSummaryPdf']
    )->name('district.dashboard.village-summary.pdf');

    Route::get(
        '/district-ceo/dashboard/village-summary/excel/{phase?}',
        [DistrictCEOController::class, 'exportVillageSummaryExcel']
    )->name('district.dashboard.village-summary.excel');


    /*
    |--------------------------------------------------------------------------
    | District CEO Dashboard
    |--------------------------------------------------------------------------
    | Dynamic optional phase route हमेशा fixed routes के बाद रखें।
    */

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
        ->name('district.owner.action_v2');

    Route::get(
        '/district-ceo/physical-possession',
        [DistrictCEOController::class, 'physicalPossessionDashboard']
    )->name('district.possession.dashboard');


    Route::get(
        '/district-ceo/physical-possession/view/{secure_id}',
        [DistrictCEOController::class, 'physicalPossessionView']
    )->name('district.possession.view');

    Route::get(
        '/district-ceo/physical-possession/view/{id}',
        [DistrictCEOController::class, 'viewPossession']
    )->name('district.possession.view');

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

// BDO Login
Route::get('/mmgay/bdo/login', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'showLogin'])->name('mmgay.bdo.login');
Route::post('/mmgay/bdo/login', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'login'])->name('mmgay.bdo.login.submit');
Route::post('/mmgay/bdo/logout', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'logout'])->name('mmgay.bdo.logout');
Route::get('/mmgay/bdo/refresh-captcha', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'refreshCaptcha'])->name('mmgay.bdo.refresh.captcha');

// BDO Protected Routes
Route::middleware(['auth', 'mmgay', 'role:mmgav_bdeo'])->prefix('mmgay/bdo')->name('mmgay.bdo.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'dashboard'])->name('dashboard');
    Route::get('/phase-report', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'phaseReport'])->name('phase-report');
    Route::get('/villages-report', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'villagesReport'])->name('villages-report');
    Route::get('/eligibility-list', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'eligibilityList'])->name('eligibility-list');
    Route::get('/schedule/capacity/check', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'getSlotCapacityCheck'])->name('schedule.capacity-check');
    Route::get('/schedule/{secure_id}', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'scheduleForm'])->name('schedule-form');
    Route::post('/schedule/{secure_id}', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'scheduleSave'])->name('schedule-save');
    Route::get('/possession-applications', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'applications'])->name('possession-applications');
    Route::get('/verify/{secure_id}', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'verifyForm'])->name('verify-form');
    Route::post('/verify/{secure_id}', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'verifySave'])->name('verify-save');
    Route::get('/download-certificate/{secure_id}', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'downloadCertificate'])->name('download-certificate');
    Route::get('/site-development', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'siteDevelopmentForm'])->name('site-development');
    Route::post('/site-development', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'siteDevelopmentSave'])->name('site-development.save');
    Route::get('/owner-status-report', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'ownerStatusReport'])->name('owner-status-report');
    Route::get('/owner-registry-details/{mobile}', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'getOwnerRegistryDetails'])->name('owner-registry-details');
    Route::get('/owner-status-report/export/csv', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'ownerStatusReportExportCsv'])->name('owner-status-report.export.csv');
    Route::get('/owner-status-report/export/pdf', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'ownerStatusReportExportPdf'])->name('owner-status-report.export.pdf');
    Route::get('/profile', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'profile'])->name('profile');
    Route::post('/profile/change-password', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'changePassword'])->name('profile.change-password');

    // HFA API Test Tool
    Route::get('/hfa-api-test', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'hfaApiTestForm'])->name('hfa-api-test');
    Route::post('/hfa-api-test', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'hfaApiTestSubmit'])->name('hfa-api-test.submit');
});

// Villager Possession Routes
Route::middleware(['auth', 'mmgay', 'role:villager'])->prefix('mmgav/villager')->name('mmgay.villager.')->group(function () {
    Route::get('/submit-possession', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'submitPossessionForm'])->name('submit');
    Route::post('/submit-possession', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'submitPossession'])->name('submit.post');
    Route::get('/download-slip', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'downloadSlip'])->name('download-slip');
    Route::get('/download-certificate/{secure_id}', [App\Http\Controllers\MMGAY\Bdo\MMGAYBdoPossessionController::class, 'downloadCertificate'])->name('download-certificate');
});

Route::prefix('super-admin')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/dashboard/pdf', [SuperAdminController::class, 'dashboardPdf'])
            ->name('admin.dashboard.pdf');

        Route::get('/get-districts/{phase?}', [SuperAdminController::class, 'getDistricts'])
            ->name('admin.getDistricts');

        Route::get('/get-blocks/{districtId}/{phase?}', [SuperAdminController::class, 'getBlocks'])
            ->name('admin.getBlocks');

        Route::get('/get-villages/{blockId}/{phase?}', [SuperAdminController::class, 'getVillages'])
            ->name('admin.getVillages');

        Route::get('/dashboard-data', [SuperAdminController::class, 'dashboardData'])
            ->name('admin.dashboard.data');

        Route::get('/super-admin/export/excel', [SuperAdminController::class, 'exportExcel'])->name('admin.export.excel');
        Route::get('/super-admin/export/pdf', [SuperAdminController::class, 'exportPDF'])->name('admin.export.pdf');

        // Badal kar ye kar dein:
        Route::get('/district-report', [SuperAdminController::class, 'districtWiseReport'])
            ->name('admin.district.report');

        Route::get(
            '/district-report/export/csv',
            [SuperAdminController::class, 'districtReportCsv']
        )->name('admin.district.report.csv');

        Route::get(
            '/district-report/print',
            [SuperAdminController::class, 'districtReportPrint']
        )->name('admin.district.report.print');

        Route::get(
            '/district-report/districts',
            [
                SuperAdminController::class,
                'districtReportDistricts',
            ]
        )->name('admin.district.report.districts');

        //  Village Report Routes Start
    
        Route::get('/village-report', [SuperAdminController::class, 'villageWiseReport'])->name('admin.village.report');

        Route::get('/village-report/csv', [SuperAdminController::class, 'villageReportCsv'])->name('admin.village.report.csv');

        Route::get('/village-report/print', [SuperAdminController::class, 'villageReportPrint'])->name('admin.village.report.print');

        Route::get(
            '/village-report/site-development/{villageId}',
            [SuperAdminController::class, 'villageSiteDevelopment']
        )
            ->whereNumber('villageId')
            ->name('admin.village.site-development');

        Route::get(
            '/village-report/filters/districts',
            [
                SuperAdminController::class,
                'villageReportFilterDistricts',
            ]
        )->name('admin.village.report.filters.districts');

        Route::get(
            '/village-report/filters/villages',
            [
                SuperAdminController::class,
                'villageReportFilterVillages',
            ]
        )->name('admin.village.report.filters.villages');

        //  Village Report Routes End    
    
        Route::get('/applicants', [SuperAdminController::class, 'applicants'])
            ->name('superadmin.applicants.index');



        Route::get(
            '/applicants/export/csv',
            [
                SuperAdminController::class,
                'applicantsCsv'
            ]
        )->name('superadmin.applicants.csv');

        Route::get(
            '/applicants/print',
            [
                SuperAdminController::class,
                'applicantsPrint'
            ]
        )->name('superadmin.applicants.print');

        Route::get(
            '/applicants/{secureId}',
            [SuperAdminController::class, 'applicantView']
        )->name('superadmin.applicants.show');

        Route::get('/allotment-report', [SuperAdminController::class, 'allotmentReport'])
            ->name('admin.allotment.report');

        Route::get(
            '/allotment-report/export/csv',
            [SuperAdminController::class, 'allotmentReportCsv']
        )->name('admin.allotment.export.csv');

        Route::get(
            '/allotment-report/print',
            [SuperAdminController::class, 'allotmentReportPrint']
        )->name('admin.allotment.print');

        Route::get('/allotment-report/excel', [SuperAdminController::class, 'exportAllotmentExcel'])
            ->name('admin.allotment.export.excel');

        Route::get('/allotment-report/pdf', [SuperAdminController::class, 'exportAllotmentPdf'])
            ->name('admin.allotment.export.pdf');

        Route::get('/registration', [SuperAdminController::class, 'registration'])
            ->name('admin.registration');

        Route::get('/registration/export/excel', [SuperAdminController::class, 'exportRegistrationExcel'])
            ->name('admin.registration.export.excel');

        Route::get('/registration/export/pdf', [SuperAdminController::class, 'exportRegistrationPdf'])
            ->name('admin.registration.export.pdf');

        // Possession Routes Start
    
        Route::get(
            '/possession/stats',
            [SuperAdminController::class, 'possessionStats']
        )->name('admin.possession.stats');

        Route::get(
            '/possession/{filter?}',
            [SuperAdminController::class, 'possessionList']
        )
            ->where(
                'filter',
                'all|schedule_pending|awaiting_citizen|field_visit_pending|document_verification|verified'
            )
            ->name('admin.possession.list');

        Route::get(
            '/possession-print/{filter?}',
            [SuperAdminController::class, 'possessionPrint']
        )
            ->where(
                'filter',
                'all|schedule_pending|awaiting_citizen|field_visit_pending|document_verification|verified'
            )
            ->name('admin.possession.print');

        Route::get(
            '/possession/application/{secureId}',
            [SuperAdminController::class, 'possessionView']
        )
            ->name('admin.possession.show');

        Route::get(
            '/possession-export/csv',
            [SuperAdminController::class, 'possessionExportCsv']
        )
            ->name('admin.possession.export.csv');

        Route::get(
            '/possession/filters/districts',
            [
                SuperAdminController::class,
                'possessionFilterDistricts',
            ]
        )->name('admin.possession.filters.districts');

        Route::get(
            '/possession/filters/blocks',
            [
                SuperAdminController::class,
                'possessionFilterBlocks',
            ]
        )->name('admin.possession.filters.blocks');

        Route::get(
            '/possession/filters/villages',
            [
                SuperAdminController::class,
                'possessionFilterVillages',
            ]
        )->name('admin.possession.filters.villages');

        Route::post('/logout', [MMGAYAuthController::class, 'logout'])
            ->name('admin.logout');
    });
