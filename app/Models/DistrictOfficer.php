<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DistrictOfficer extends Authenticatable
{
    protected $fillable = [
        'user_id',
        'name',
        'username',
        'mobile',
        'password',
        'district_id',
        'district_name',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
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

    public function approvedApplications(): HasMany
    {
        return $this->hasMany(PhysicalPossessionApplication::class, 'approved_by');
    }
}
