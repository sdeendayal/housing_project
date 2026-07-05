<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteEnggStatus extends Model
{
    protected $table = 'site_engg_status';

    protected $fillable = [
        'application_id',
        'application_number',
        'site_engg_user_id',
        'site_engg_name',
        'site_engg_email',
        'site_engg_mobile',
        'status',
        'remarks',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(\App\Models\PhysicalPossessionApplication::class, 'application_id'); // Let's check model name for physical_possession_applications
    }

    public function siteEnggUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'site_engg_user_id');
    }
}
