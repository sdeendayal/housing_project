<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EwsProject extends Model
{
    protected $table = 'ews_projects';

    protected $fillable = [
        'zone_id',
        'zone_name',
        'district_id',
        'district_name',
        'town_id',
        'town_name',
        'name',
        'register_flat',
        'project_abbr',
        'address',
        'address_of_project',
        'sector_no',
        'developer_name',
        'license_no',
        'area_of_project',
        'developer_plan',
        'approved_building_plan',
        'building_plan_approval_date',
        'building_plan_copy',
        'oc_status_yn',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(EwsDistrict::class, 'district_id');
    }

    public function town(): BelongsTo
    {
        return $this->belongsTo(EwsTown::class, 'town_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(EwsBlock::class, 'project_id');
    }
}
