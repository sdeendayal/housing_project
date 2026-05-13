<?php

use Illuminate\Support\Facades\Route;

// web routes 
Route::get('/', function () {
    return view('welcome');
});
