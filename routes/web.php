<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
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

