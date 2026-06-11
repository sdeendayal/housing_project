<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PhysicalPossessionApplication extends Model
{
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
        'private_purchaser_id',
        'ppp_id',
        'member_id',
        'slip_id',
        'application_number',
        'district_id',
        'district_name',
        'mobile',
        'applicant_name',
        'father_name',
        'address',
        'registration_details',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
        'citizen_visit_date',
        'visit_instructions',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'citizen_visit_date' => 'datetime',
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

    public function documents(): HasMany
    {
        return $this->hasMany(PhysicalPossessionDocument::class, 'application_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ApplicationStatusLog::class, 'application_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function officerAction(): HasOne
    {
        return $this->hasOne(OfficerApplicationAction::class, 'application_id');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'warning',
        };
    }

    public function getRouteKeyName(): string
    {
        return 'secure_id';
    }

    public static function generateSecureId(): string
    {
        do {
            $secureId = bin2hex(random_bytes(16));
        } while (self::where('secure_id', $secureId)->exists());

        return $secureId;
    }
}
