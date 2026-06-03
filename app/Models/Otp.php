<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'mobile_number',
        'otp',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // Check if OTP has expired
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    // Check if OTP is still active (not verified and not expired)
    public function isActive(): bool
    {
        return is_null($this->verified_at) && ! $this->isExpired();
    }
}
