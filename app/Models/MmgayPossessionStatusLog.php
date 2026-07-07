<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmgayPossessionStatusLog extends Model
{
    protected $table = 'mmgay_possession_status_logs';

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            $application = $log->application;
            if ($application) {
                $log->possession_id = $application->possession_id;
                $log->ppp_id = $application->ppp_id;
                $log->member_id = $application->member_id;
                $log->mobile = $application->mobile;
                $log->flat_id = $application->flat_id;
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
        'old_status',
        'new_status',
        'remarks',
        'changed_by_type',
        'changed_by_id',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(MmgayPossessionApplication::class, 'application_id');
    }
}
