<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmgayPossessionBdoStatus extends Model
{
    protected $table = 'mmgay_possession_bdo_status';

    protected static function booted(): void
    {
        static::creating(function (self $status) {
            $application = $status->application;
            if ($application) {
                $status->possession_id = $application->possession_id;
                $status->ppp_id = $application->ppp_id;
                $status->member_id = $application->member_id;
                $status->mobile = $application->mobile;
                $status->flat_id = $application->flat_id;
            }
        });
    }

    protected $fillable = [
        'application_id',
        'possession_id',
        'ppp_id',
        'member_id',
        'mobile',
        'flat_id',
        'application_number',
        'bdo_user_id',
        'bdo_name',
        'bdo_email',
        'bdo_mobile',
        'status',
        'remarks',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(MmgayPossessionApplication::class, 'application_id');
    }

    public function bdoUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bdo_user_id');
    }
}
