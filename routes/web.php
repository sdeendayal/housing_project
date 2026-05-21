<?php
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('index');
// });

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

Route::get('/mmsay-department-login', function () {
    return view('mmsay.departmentLogin');
});

Route::get('/mmsay.citizen.dashboard', function () {
    return view('mmsayCitizenDashboard');
});

Route::get('/mmsay.department.dashboard', function () {
    return view('mmsayDepartmentDashboard');
});

Route::get('/mmsay-profile', function () {
    return view('mmsayCitizenProfile');
});

Route::get('/mmsay-payment-status', function () {
    return view('mmsayPaymentStatus');
});

