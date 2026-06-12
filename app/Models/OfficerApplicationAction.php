<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficerApplicationAction extends Model
{
    protected $fillable = [
        'application_id',
        'asset_id',
        'private_purchaser_id',
        'user_id',
        'secure_id',
        'officer_id',
        'action',
        'remarks',
        'previous_status',
        'new_status',
        'application_number',
        'district_id',
        'district_name',
        'citizen_visit_date',
        'visit_instructions',
    ];

    protected function casts(): array
    {
        return [
            'citizen_visit_date' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(PhysicalPossessionApplication::class, 'application_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function propertyRegistration(): BelongsTo
    {
        return $this->belongsTo(PropertyRegistration::class, 'asset_id', 'AssetId');
    }
}
