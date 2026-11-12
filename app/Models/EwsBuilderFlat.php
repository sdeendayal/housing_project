<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EwsBuilderFlat extends Model
{
    protected $table = 'ews_builder_flats';

    protected $fillable = [
        'register_flat',
        'zone_id',
        'zone_name',
        'district_id',
        'district_name',
        'town_name',
        'town_id',
        'developer_name',
        'project_name',
        'project_id',
        'sector_no',
        'address_of_project',
        'area_of_project',
        'developer_plan',
        'approved_building_plan',
        'building_plan_approval_date',
        'building_plan_copy',
        'oc_status_yn',
        'block_tower_number',
        'block_id',
        'floor',
        'flat_number',
        'flat_code',
        'created_by',
        'secure_id',
        'license_no',
        'oc_status',
        'oc_date',
        'oc_copy_file',
        'total_flats_in_project',
        'total_ews_flats_in_project',
        'flats_offered_for_draw',
        'remaining_flats_for_oc',
        'is_approved_by_dtp',
        'dtp_approved_at',
        'dtp_approved_by',
        'dtp_user_name',
        'dtp_approval_remarks',
        'tcp_licence_data',
        'tcp_licence_verified_at',
        'tcp_licence_status',
    ];

    protected $casts = [
        'is_approved_by_dtp' => 'integer',
        'dtp_approved_at' => 'datetime',
        'building_plan_approval_date' => 'date',
        'oc_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($flat) {
            if (empty($flat->secure_id)) {
                $flat->secure_id = md5(uniqid(microtime() . rand(), true));
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(EwsStpDistrict::class, 'zone_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(EwsDistrict::class, 'district_id');
    }

    public function town(): BelongsTo
    {
        return $this->belongsTo(EwsTown::class, 'town_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(EwsProject::class, 'project_id');
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(EwsBlock::class, 'block_id');
    }

    public function stpApprovals(): HasMany
    {
        return $this->hasMany(EwsStpApproval::class, 'flat_id')->orderBy('id', 'desc');
    }

    public function latestStpApproval(): HasOne
    {
        return $this->hasOne(EwsStpApproval::class, 'flat_id')->latestOfMany();
    }

    public function isApprovedByDtp(): bool
    {
        return (int)($this->is_approved_by_dtp ?? 0) === 1;
    }

    /**
     * Backward-compatibility accessors and mutators for unified columns
     */
    public function getDistIdAttribute()
    {
        return $this->district_id;
    }

    public function setDistIdAttribute($value)
    {
        $this->attributes['district_id'] = $value;
    }

    public function getDistNameAttribute()
    {
        return $this->district_name;
    }

    public function setDistNameAttribute($value)
    {
        $this->attributes['district_name'] = $value;
    }

    public function getIsAprovedByDtpAttribute()
    {
        return $this->is_approved_by_dtp;
    }

    public function setIsAprovedByDtpAttribute($value)
    {
        $this->attributes['is_approved_by_dtp'] = $value;
    }
}
