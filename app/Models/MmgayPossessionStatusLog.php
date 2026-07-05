<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmgayPossessionStatusLog extends Model
{
    protected $table = 'mmgay_possession_status_logs';

    protected $fillable = [
        'application_id',
        'asset_id',
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
