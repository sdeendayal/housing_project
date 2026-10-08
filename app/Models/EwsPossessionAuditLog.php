<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EwsPossessionAuditLog extends Model
{
    use HasFactory;

    protected $table = 'ews_possession_audit_logs';

    protected $fillable = [
        'possession_id',
        'beneficiary_id',
        'application_number',
        'action',
        'old_status',
        'new_status',
        'stp_user_id',
        'stp_user_name',
        'latitude',
        'longitude',
        'ip_address',
        'user_agent',
        'app_version',
        'device_info',
        'payload_snapshot',
    ];

    protected $casts = [
        'payload_snapshot' => 'array',
    ];

    public function possession()
    {
        return $this->belongsTo(EwsBeneficiaryPossession::class, 'possession_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'stp_user_id');
    }
}
