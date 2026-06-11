<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusLog extends Model
{
    protected $fillable = [
        'application_id',
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
}
