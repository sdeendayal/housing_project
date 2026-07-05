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
        'secure_id',
        'owner_id',
        'scheme',
        'private_purchaser_id',
        'asset_id',
        'property_auction_id',
        'mmsay_application_no',
        'ppp_id',
        'member_id',
        'slip_id',
        'application_number',
        'district_id',
        'district_name',
        'branch_id',
        'city_id',
        'city_name',
        'sector_id',
        'sector_name',
        'flat_id',
        'asset_name',
        'asset_size',
        'asset_unit',
        'flat_cost',
        'received_amount',
        'balance_amount',
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
        'visit_slot_1',
        'visit_slot_2',
        'visit_slot_3',
        'visit_instructions',
        'created_by',
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
            'flat_cost' => 'decimal:2',
            'received_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
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

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function propertyRegistration(): BelongsTo
    {
        return $this->belongsTo(PropertyRegistration::class, 'asset_id', 'AssetId');
    }

    public function officerActions(): HasMany
    {
        return $this->hasMany(OfficerApplicationAction::class, 'application_id')->latest();
    }

    public function latestOfficerAction(): HasOne
    {
        return $this->hasOne(OfficerApplicationAction::class, 'application_id')->latestOfMany();
    }

    public function officerAction(): HasOne
    {
        return $this->latestOfficerAction();
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'returned' => 'info',
            default => 'warning',
        };
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function returnedDocuments()
    {
        return $this->documents()->where('review_status', PhysicalPossessionDocument::REVIEW_RETURNED);
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
