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

    /**
     * Normalize string for comparison:
     * - Lowercase
     * - Remove municipality suffixes if comparing towns e.g. "(MUNICIPAL CORPORATION)"
     * - Remove all non-alphanumeric characters (spaces, hyphens, dots, underscores, brackets, etc.)
     */
    public static function normalizeForComparison($str)
    {
        if (empty($str)) {
            return '';
        }
        $str = strtolower(trim($str));
        // Remove municipality suffixes if comparing towns e.g. "(MUNICIPAL CORPORATION)"
        $str = preg_replace('/\s*\([^)]*\)/', '', $str);
        // Remove all non-alphanumeric characters
        return preg_replace('/[^a-z0-9]/', '', $str);
    }

    /**
     * Collapse repeated consecutive characters (e.g. "behaat" -> "behat", "aanand" -> "anand")
     */
    public static function collapseRepeatedChars($str)
    {
        if (empty($str)) {
            return '';
        }
        return preg_replace('/(.)\1+/', '$1', $str);
    }

    /**
     * Check if $input is duplicate or too similar to any name in $existingList.
     *
     * @param string $input User input (e.g. "behaat", "anandkamboj", "t01")
     * @param iterable|array $existingList List of strings or objects containing 'name'
     * @param float $threshold Similarity percentage (default 85%)
     * @return array|null ['match' => true, 'existing' => string, 'reason' => string, 'score' => float] or null
     */
    public static function findSimilarName($input, $existingList, $threshold = 85.0)
    {
        $inputTrimmed = trim($input);
        if (empty($inputTrimmed)) {
            return null;
        }

        $normInput = self::normalizeForComparison($inputTrimmed);
        if (empty($normInput)) {
            return null;
        }

        $collapsedInput = self::collapseRepeatedChars($normInput);
        $lenInput = strlen($normInput);

        foreach ($existingList as $item) {
            $existingName = is_object($item) ? ($item->name ?? '') : (is_array($item) ? ($item['name'] ?? '') : (string)$item);
            $existingTrimmed = trim($existingName);
            if (empty($existingTrimmed)) {
                continue;
            }

            // 1. Exact case-insensitive match
            if (strcasecmp($inputTrimmed, $existingTrimmed) === 0) {
                return [
                    'match' => true,
                    'existing' => $existingTrimmed,
                    'reason' => 'Exact match',
                    'score' => 100.0,
                ];
            }

            $normExisting = self::normalizeForComparison($existingTrimmed);
            if (empty($normExisting)) {
                continue;
            }

            // 2. Canonical Alphanumeric match (ignoring spaces, hyphens, case)
            // e.g. "Anand Kamboj" vs "anandkamboj", "T-01" vs "t01"
            if ($normInput === $normExisting) {
                return [
                    'match' => true,
                    'existing' => $existingTrimmed,
                    'reason' => 'Duplicate (ignoring spaces and punctuation)',
                    'score' => 100.0,
                ];
            }

            // 3. Repeated character collapsed match
            // e.g. "behaat" vs "behat", "aanandkamboj" vs "anandkamboj"
            $collapsedExisting = self::collapseRepeatedChars($normExisting);
            if ($collapsedInput === $collapsedExisting) {
                return [
                    'match' => true,
                    'existing' => $existingTrimmed,
                    'reason' => 'Duplicate with repeated characters',
                    'score' => 98.0,
                ];
            }

            // 4. Levenshtein edit distance check (with similarity guard)
            $lenExisting = strlen($normExisting);
            $minLen = min($lenInput, $lenExisting);

            // Calculate percentage similarity
            similar_text($normInput, $normExisting, $percent);
            $lev = levenshtein($normInput, $normExisting);

            // 1 letter typo/diff if similarity is >= 80% (prevents "panipat" vs "sonipat")
            if ($minLen >= 3 && $lev === 1 && $percent >= 80.0) {
                return [
                    'match' => true,
                    'existing' => $existingTrimmed,
                    'reason' => 'Almost identical spelling',
                    'score' => round($percent, 1),
                ];
            }

            // 2 letters typo/diff for long words (>= 8 chars) if similarity is >= 85%
            if ($minLen >= 8 && $lev <= 2 && $percent >= 85.0) {
                return [
                    'match' => true,
                    'existing' => $existingTrimmed,
                    'reason' => 'Almost identical spelling',
                    'score' => round($percent, 1),
                ];
            }

            // Percentage threshold check
            if ($percent >= $threshold && $minLen >= 4) {
                return [
                    'match' => true,
                    'existing' => $existingTrimmed,
                    'reason' => round($percent, 1) . '% similar name',
                    'score' => round($percent, 1),
                ];
            }
        }

        return null;
    }
}

