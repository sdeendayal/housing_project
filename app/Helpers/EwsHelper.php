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

    public static function getTownAbbreviation($townName)
    {
        $clean = strtoupper(trim($townName));
        $mapping = [
            'SONIPAT' => 'SNP',
            'GURUGRAM' => 'GGM',
            'FARIDABAD' => 'FBD',
            'ROHTAK' => 'RTK',
            'PANIPAT' => 'PNP',
            'KUNDLI' => 'KND',
        ];
        if (isset($mapping[$clean])) {
            return $mapping[$clean];
        }
        $consonants = preg_replace('/[AEIOU]/', '', $clean);
        $consonants = preg_replace('/[^A-Z]/', '', $consonants);
        if (strlen($consonants) >= 3) {
            return substr($consonants, 0, 3);
        }
        return substr($clean, 0, 3);
    }

    public static function getDeveloperAbbreviation($developerName)
    {
        $clean = strtoupper(trim($developerName));
        $mapping = [
            'PARKER INFRA PRIVATE LTD.' => 'PIPD',
            'PARKER INFRA PRIVATE LTD' => 'PIPD',
            'PARKER INFRA' => 'PIPD',
            'AAKARSHAK RELATORS PVT. LTD.' => 'ARPL',
            'AAKARSHAK RELATORS' => 'ARPL',
            'PARDESI DEVELOPERS PVT. LTD.' => 'PDPL',
            'PARDESI DEVELOPERS' => 'PDPL',
            'INDIAN RAILWAY WELFARE ORGANIZATION' => 'IRWO',
            'JBB EVEREST BUILDTECH PVT. LTD.' => 'JEBPL',
            'JBB EVEREST BUILDTECH' => 'JEBPL',
            'DEVELOPER LOGIN' => 'DEV',
        ];
        if (isset($mapping[$clean])) {
            return $mapping[$clean];
        }
        $words = preg_split('/\s+/', $clean);
        $initials = '';
        foreach ($words as $w) {
            if (strlen($w) > 0) {
                $initials .= $w[0];
            }
        }
        $initials = preg_replace('/[^A-Z]/', '', $initials);
        if (strlen($initials) > 0) {
            return substr($initials, 0, 4);
        }
        return 'DEV';
    }

    public static function getFloorAbbreviation($floorName)
    {
        $clean = strtolower(trim($floorName));
        if (str_contains($clean, 'ground')) {
            return 'GF';
        }
        preg_match('/\d+/', $clean, $matches);
        if (!empty($matches[0])) {
            return $matches[0] . 'F';
        }
        $words = [
            'first' => '1F', 'second' => '2F', 'third' => '3F', 'fourth' => '4F',
            'fifth' => '5F', 'sixth' => '6F', 'seventh' => '7F', 'eighth' => '8F',
            'ninth' => '9F', 'tenth' => '10F'
        ];
        foreach ($words as $word => $abbr) {
            if (str_contains($clean, $word)) {
                return $abbr;
            }
        }
        return strtoupper(substr($floorName, 0, 2));
    }

    public static function generateFlatCode($townName, $developerName, $floorName, $blockTowerNo, $flatNumber)
    {
        $parts = [];
        $parts[] = self::getTownAbbreviation($townName);
        $parts[] = self::getDeveloperAbbreviation($developerName);
        $parts[] = self::getFloorAbbreviation($floorName);
        
        if (!empty($blockTowerNo)) {
            $blockClean = strtoupper(trim($blockTowerNo));
            if (strlen($blockClean) === 1 && ctype_alpha($blockClean)) {
                $parts[] = 'B' . $blockClean;
            } else {
                $parts[] = preg_replace('/\s+/', '', $blockClean);
            }
        }
        
        $flatClean = strtoupper(trim($flatNumber));
        $flatClean = preg_replace('/^[A-Z][-]?/', '', $flatClean);
        $parts[] = $flatClean;

        return implode('-', $parts);
    }
}
