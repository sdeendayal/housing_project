<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EwsBlock extends Model
{
    protected $table = 'ews_blocks';

    protected $fillable = [
        'zone_id',
        'zone_name',
        'district_id',
        'district_name',
        'town_id',
        'town_name',
        'project_id',
        'project_name',
        'name',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(EwsProject::class, 'project_id');
    }
}
