<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EwsDeveloperLog extends Model
{
    protected $table = 'ews_developer_logs';

    protected $fillable = [
        'user_id',
        'action',
        'details',
        'ip_address',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
