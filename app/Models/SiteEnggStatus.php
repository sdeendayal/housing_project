<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteEnggStatus extends Model
{
    protected $table = 'site_engg_status';

    protected static function booted(): void
    {
        static::creating(function (self $status) {
            $application = $status->application;
            if ($application) {
                $status->possession_id = $application->possession_id;
                $status->private_purchaser_id = $application->private_purchaser_id;
                $status->ppp_id = $application->ppp_id;
                $status->member_id = $application->member_id;
                $status->mobile = $application->mobile;
                $status->flat_id = $application->flat_id;
                $status->asset_id = $application->asset_id;
            }
        });
    }

    protected $fillable = [
        'application_id',
        'possession_id',
        'asset_id',
        'private_purchaser_id',
        'ppp_id',
        'member_id',
        'mobile',
        'flat_id',
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
