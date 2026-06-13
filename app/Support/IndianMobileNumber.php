<?php

namespace App\Support;

class IndianMobileNumber
{
    public static function normalize(mixed $mobile): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $mobile);

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) !== 10 || ! preg_match('/^[6-9]/', $digits)) {
            return null;
        }

        return $digits;
    }
}
