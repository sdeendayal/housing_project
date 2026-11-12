<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EwsStpApproval extends Model
{
    protected $table = 'ews_stp_approvals';

    protected $fillable = [
        'flat_id',
        'flat_code',
        'stp_user_id',
        'stp_user_name',
        'stp_role',
        'zone_id',
        'zone_name',
        'district_id',
        'district_name',
        'action',
        'status',
        'remarks',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flat(): BelongsTo
    {
        return $this->belongsTo(EwsBuilderFlat::class, 'flat_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'stp_user_id');
    }
}

