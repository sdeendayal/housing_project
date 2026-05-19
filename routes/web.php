<?php
use Illuminate\Support\Facades\Route;

// web routes 
Route::get('/', function () {
    return view('index');
});

Route::get('/help', function () {
    return view('help');
});

Route::get('introduction', function () {
    return view('introduction');
});

Route::get('organisation-chart', function () {
    return view('organisationChart');
});

Route::get('whos-who', function () {
    return view('whosWho');
});

Route::get('/mmsay-login', function () {
    return view('mmsayLogin');
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

