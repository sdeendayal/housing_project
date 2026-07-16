<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EwsBuilderFlat extends Model
{
    protected $table = 'ews_builder_flats';

    protected $fillable = [
        'district_id',
        'district_name',
        'town_name',
        'project_name',
        'block_tower_number',
        'floor',
        'flat_number',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
