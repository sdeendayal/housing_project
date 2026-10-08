<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EwsFlatAbbreviation extends Model
{
    protected $table = 'ews_flat_abbreviations';

    protected $fillable = [
        'dist_id',
        'dist_name',
        'town_id',
        'town_name',
        'zone_id',
        'zone_name',
        'town_abbr',
        'project_name',
        'project_abbr',
        'floor',
        'floor_abbr',
        'block_tower',
        'block_abbr',
        'flat_no',
        'flat_abbr',
        'final_no',
    ];
}
