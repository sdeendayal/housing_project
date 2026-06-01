<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PropertyService
{
    public function getAllProperties()
    {
        return DB::table('property_registration')
            ->where('IsDeleted', 0)
            ->get();
    }
}