<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MmgaySiteDevelopment extends Model
{
    protected $table = 'mmgay_site_developments';

    protected $fillable = [
        'district_id',
        'block_id',
        'village_id',
        'road_status',
        'water_status',
        'electricity_status',
        'sewerage_status',
        'remarks',
        'updated_by',
        'road_photo',
        'water_photo',
        'electricity_photo',
        'sewerage_photo',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(MmgaySiteDevelopmentPhoto::class, 'site_development_id');
    }

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MmgaySiteDevelopmentLog::class, 'site_development_id')->orderBy('created_at', 'desc');
    }
}
