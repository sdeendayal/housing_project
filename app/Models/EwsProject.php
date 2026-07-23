<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EwsProject extends Model
{
    protected $table = 'ews_projects';

    protected $fillable = [
        'district_id',
        'name',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(EwsDistrict::class, 'district_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(EwsBlock::class, 'project_id');
    }
}
