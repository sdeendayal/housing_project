<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EwsBeneficiaryPossession extends Model
{
    use HasFactory;

    protected $table = 'ews_beneficiary_possessions';

    protected $fillable = [
        'beneficiary_id',
        'application_number',
        'citizen_name',
        'citizen_mobile',
        'flat_no',
        'district_name',
        'project_name',
        'stp_user_id',
        'possession_status',
        'possession_letter_path',
        'possession_letter_original_name',
        'beneficiary_flat_photo_path',
        'latitude',
        'longitude',
        'remarks',
        'app_version',
        'device_info',
        'possession_given_at',
        'verified_by_user_id',
        'verified_at',
    ];

    protected $casts = [
        'possession_given_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected $appends = [
        'possession_letter_url',
        'beneficiary_flat_photo_url',
    ];

    public function getPossessionLetterUrlAttribute()
    {
        if ($this->possession_letter_path) {
            return '/storage/' . ltrim($this->possession_letter_path, '/');
        }
        return null;
    }

    public function getBeneficiaryFlatPhotoUrlAttribute()
    {
        if ($this->beneficiary_flat_photo_path) {
            return '/storage/' . ltrim($this->beneficiary_flat_photo_path, '/');
        }
        return null;
    }

    public function auditLogs()
    {
        return $this->hasMany(EwsPossessionAuditLog::class, 'possession_id')->orderBy('id', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'stp_user_id');
    }
}
