<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsBanner;

class WebsiteController extends Controller
{
    public function index()
    {
        $banners = CmsBanner::where('status', 1)
            ->latest()
            ->get();

        return view('home.index', compact(
            'banners'
        ));
    }
}
