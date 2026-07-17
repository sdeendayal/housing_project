<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmgaySiteDevelopmentLog extends Model
{
    protected $table = 'mmgay_site_development_logs';

    protected $fillable = [
        'site_development_id',
        'district_id',
        'block_id',
        'village_id',
        'road_status',
        'water_status',
        'electricity_status',
        'sewerage_status',
        'remarks',
        'updated_by',
        'updated_by_name',
    ];

    public function siteDevelopment(): BelongsTo
    {
        return $this->belongsTo(MmgaySiteDevelopment::class, 'site_development_id');
    }
}
