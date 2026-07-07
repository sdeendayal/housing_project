<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusLog extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $log) {
            $application = $log->application;
            if ($application) {
                $log->possession_id = $application->possession_id;
            }
        });
    }

    protected $fillable = [
        'application_id',
        'possession_id',
        'asset_id',
        'old_status',
        'new_status',
        'remarks',
        'changed_by_type',
        'changed_by_id',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(PhysicalPossessionApplication::class, 'application_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_id');
    }
}
