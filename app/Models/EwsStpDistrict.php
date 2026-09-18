<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EwsStpDistrict extends Model
{
    protected $table = 'ews_stp_districts';

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    /**
     * The 5 allowed STP district names in uppercase.
     */
    public const ALLOWED_DISTRICTS = [
        'FARIDABAD',
        'GURUGRAM',
        'HISAR',
        'PANIPAT',
        'ROHTAK',
    ];

    /**
     * Check if a given district is a valid STP district.
     */
    public static function isValidDistrict(?string $name): bool
    {
        if (empty($name)) {
            return false;
        }
        return in_array(strtoupper(trim($name)), self::ALLOWED_DISTRICTS, true);
    }
}
