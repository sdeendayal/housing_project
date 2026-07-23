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
        'town_id',
        'project_name',
        'project_id',
        'block_tower_number',
        'block_id',
        'floor',
        'flat_number',
        'flat_code',
        'created_by',
        'secure_id',
    ];

    protected static function booted()
    {
        static::creating(function ($flat) {
            if (empty($flat->secure_id)) {
                $flat->secure_id = md5(uniqid(microtime() . rand(), true));
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(EwsDistrict::class, 'district_id');
    }

    public function town(): BelongsTo
    {
        return $this->belongsTo(EwsTown::class, 'town_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(EwsProject::class, 'project_id');
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(EwsBlock::class, 'block_id');
    }
}
