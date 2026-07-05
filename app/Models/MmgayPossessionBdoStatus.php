<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmgayPossessionBdoStatus extends Model
{
    protected $table = 'mmgay_possession_bdo_status';

    protected $fillable = [
        'application_id',
        'application_number',
        'bdo_user_id',
        'bdo_name',
        'bdo_email',
        'bdo_mobile',
        'status',
        'remarks',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(MmgayPossessionApplication::class, 'application_id');
    }

    public function bdoUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bdo_user_id');
    }
}
