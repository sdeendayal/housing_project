<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmgaySiteDevelopmentPhoto extends Model
{
    protected $table = 'mmgay_site_development_photos';

    protected $fillable = [
        'site_development_id',
        'photo_path',
    ];

    public function siteDevelopment(): BelongsTo
    {
        return $this->belongsTo(MmgaySiteDevelopment::class, 'site_development_id');
    }
}
