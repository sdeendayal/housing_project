<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TcpLicenceApiService
{
    /**
     * Official Haryana TCP EODB API endpoint for retrieving licence details.
     */
    protected const API_URL = 'https://tcpharyana.gov.in/EODB/api/CIS/GetTCPLicenceDetails';

    /**
     * Checks if current environment is local.
     */
    public static function isLocalEnvironment(): bool
    {
        if (app()->environment('local')) {
            return true;
        }

        try {
            $host = request()->getHost();
            if (in_array($host, ['localhost', '127.0.0.1', '::1']) || str_contains($host, '127.0.0.1') || str_contains($host, 'localhost')) {
                return true;
            }
        } catch (\Throwable $e) {}

        return false;
    }

    /**
     * Normalizes the licence number according to TCP Haryana API guidelines:
     * - Trim leading/trailing whitespace
     * - Convert 'of' to uppercase 'OF'
     * - Collapse repeated whitespace to a single space
     *
     * Example: " 242 of 2007 " -> "242 OF 2007"
     */
    public static function normalizeLicenceNo(?string $licenceNo): string
    {
        if (empty($licenceNo)) {
            return '';
        }

        // Trim whitespace
        $clean = trim($licenceNo);

        // Collapse multiple spaces into single space
        $clean = preg_replace('/\s+/', ' ', $clean);

        // Convert lowercase 'of' to uppercase 'OF' surrounded by spaces or boundaries
        $clean = preg_replace('/\bof\b/i', 'OF', $clean);

        return $clean;
    }

    /**
     * Fetch licence details from TCP Haryana API.
     *
     * Rule:
     * - STRICTLY call real TCP Haryana API only.
     * - If valid licence found, return official data.
     * - If no record or invalid, return no data (no dummy/mock response).
     *
     * @param string|null $licenceNo
     * @return array
     */
    public function fetchLicenceDetails(?string $licenceNo): array
    {
        $normalized = self::normalizeLicenceNo($licenceNo);

        if (empty($normalized)) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'लाइसेंस नंबर दर्ज करना अनिवार्य है।',
                'licence_no' => '',
                'data' => null,
                'all_records' => [],
                'source' => 'VALIDATION_ERROR',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post(self::API_URL, [
                    'LicenceNo' => $normalized
                ]);

            if ($response->successful()) {
                $body = $response->json();

                if (isset($body['code']) && $body['code'] == 200 && !empty($body['LicenceDetails'])) {
                    $records = $body['LicenceDetails'];
                    $primary = $records[0] ?? null;

                    if ($primary) {
                        foreach ($primary as $k => $v) {
                            if (is_string($v)) {
                                $primary[$k] = trim($v);
                            }
                        }
                    }

                    return [
                        'success' => true,
                        'code' => 200,
                        'message' => $body['message'] ?? 'Licence details retrieved successfully from TCP Haryana Portal.',
                        'licence_no' => $normalized,
                        'data' => $primary,
                        'all_records' => $records,
                        'source' => 'LIVE_TCP_API',
                    ];
                }

                // If no record found in real API
                return [
                    'success' => false,
                    'code' => 404,
                    'message' => 'इस लाइसेंस नंबर (' . $normalized . ') के अगेंस्ट कोई डेटा प्राप्त नहीं हुआ।',
                    'licence_no' => $normalized,
                    'data' => null,
                    'all_records' => [],
                    'source' => 'LIVE_TCP_API',
                ];
            }

            return [
                'success' => false,
                'code' => $response->status(),
                'message' => 'इस लाइसेंस नंबर (' . $normalized . ') के अगेंस्ट कोई डेटा प्राप्त नहीं हुआ।',
                'licence_no' => $normalized,
                'data' => null,
                'all_records' => [],
                'source' => 'LIVE_TCP_API',
            ];
        } catch (\Throwable $e) {
            Log::warning('TCP Licence API Exception: ' . $e->getMessage(), [
                'licence_no' => $normalized
            ]);

            return [
                'success' => false,
                'code' => 500,
                'message' => 'इस लाइसेंस नंबर (' . $normalized . ') के अगेंस्ट कोई डेटा प्राप्त नहीं हुआ। (TCP Haryana Server Timeout/Unreachable)',
                'licence_no' => $normalized,
                'data' => null,
                'all_records' => [],
                'source' => 'LIVE_TCP_API_ERROR',
            ];
        }
    }
}
