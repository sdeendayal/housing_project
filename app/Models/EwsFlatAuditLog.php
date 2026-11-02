<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EwsFlatAuditLog extends Model
{
    protected $table = 'ews_flat_audit_logs';

    protected $fillable = [
        'flat_id',
        'flat_code',
        'user_id',
        'user_name',
        'user_role',
        'action',
        'summary',
        'changed_fields',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(EwsBuilderFlat::class, 'flat_id');
    }
}
