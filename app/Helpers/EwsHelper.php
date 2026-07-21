<?php

namespace App\Helpers;

class EwsHelper
{
    private static $salt = 'HARYANA_EWS_PROJECT_SECRET_SALT_2026';

    /**
     * Encode numeric ID to URL-safe secure_id string
     */
    public static function encodeSecureId($id)
    {
        if (empty($id)) {
            return '';
        }
        $hash = substr(md5(self::$salt . '_' . $id), 0, 8);
        return rtrim(strtr(base64_encode($id . '_' . $hash), '+/', '-_'), '=');
    }

    /**
     * Decode secure_id string back to numeric ID
     */
    public static function decodeSecureId($secureId)
    {
        if (empty($secureId)) {
            return null;
        }

        $decoded = base64_decode(strtr($secureId, '-_', '+/'));
        if ($decoded && str_contains($decoded, '_')) {
            list($id, $hash) = explode('_', $decoded, 2);
            if (substr(md5(self::$salt . '_' . $id), 0, 8) === $hash) {
                return (int) $id;
            }
        }

        // Fallback for numeric IDs if legacy links exist
        if (is_numeric($secureId)) {
            return (int) $secureId;
        }

        return null;
    }
}
