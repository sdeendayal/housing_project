<?php

namespace App\Http\Controllers\PhysicalPossession;

use App\Http\Controllers\Controller;

class PpLandingController extends Controller
{
    // Landing page dikhana
    public function index()
    {
        return view('physical-possession.landing');
    }
}
