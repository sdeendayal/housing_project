<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmgayPossessionApplication extends Model
{
    protected $table = 'mmgay_possession_applications';

    protected static function booted(): void
    {
        static::creating(function (self $application) {
            if (empty($application->secure_id)) {
                $application->secure_id = self::generateSecureId();
            }
        });
    }

    protected $fillable = [
        'user_id',
        'secure_id',
        'owner_id',
        'slip_id',
        'application_number',
        'district_id',
        'district_name',
        'block_id',
        'block_name',
        'mobile',
        'applicant_name',
        'father_name',
        'address',
        'status',
        'remarks',
        'citizen_visit_date',
        'visit_slot_1',
        'visit_slot_2',
        'visit_slot_3',
        'visit_instructions',
        'physical_possession_status',
        'possession_date',
        'meeting_slot',
        'plot_image',
        'latitude',
        'longitude',
        'image_capture_datetime',
        'possession_certificate',
        'site_engineer_file',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'citizen_visit_date' => 'datetime',
            'visit_slot_1' => 'datetime',
            'visit_slot_2' => 'datetime',
            'visit_slot_3' => 'datetime',
            'possession_date' => 'date',
            'image_capture_datetime' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'DistrictId');
    }

    public static function generateSecureId(): string
    {
        return md5(uniqid(rand(), true));
    }
}
