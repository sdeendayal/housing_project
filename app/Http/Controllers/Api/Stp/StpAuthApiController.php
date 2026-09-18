<?php

namespace App\Http\Controllers\Api\Stp;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StpAuthApiController extends Controller
{
    public function __construct(
        private OtpVerificationService $otpService
    ) {}

    /**
     * Send OTP to STP Mobile Number
     * POST /api/stp/login/send-otp or POST /api/stp/send-otp
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
        ], [
            'mobile.required' => 'Mobile number is required.',
            'mobile.regex' => 'Please enter a valid 10-digit mobile number.',
        ]);

        $mobile = trim($request->input('mobile'));
        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number is not registered as an EWS STP account.'
            ], 404);
        }

        // Validate STP Role
        $userRole = $user->roleSlug();
        if (!in_array($userRole, ['ews_developer', 'ews_stp', 'stp'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This account does not have STP access privileges.'
            ], 403);
        }

        if ($user->Is_Active === '0' || $user->Is_Active === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact administrator.'
            ], 403);
        }

        $purpose = 'ews_developer_login';
        $logLabel = 'EWS STP Mobile API';

        try {
            $result = $this->otpService->send(
                $mobile,
                $purpose,
                $user->id,
                $logLabel
            );
        } catch (\Exception $e) {
            Log::error("{$logLabel} OTP generation failed via API", [
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to send OTP at the moment. Please try again later.'
            ], 500);
        }

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        $response = [
            'success' => true,
            'message' => $result['message'] ?? 'OTP sent successfully to your registered mobile number.',
            'data' => [
                'mobile' => $mobile,
                'resend_after' => $result['resend_after'] ?? 60,
            ]
        ];

        // Include fixed test OTP in local/debug mode for developer ease
        if (OtpVerificationService::usesFixedTestOtp($mobile, $purpose)) {
            $response['data']['test_otp'] = '111111';
        }

        return response()->json($response);
    }

    /**
     * Verify OTP and return Sanctum Bearer Token
     * POST /api/stp/login/verify-otp or POST /api/stp/verify-otp
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'otp' => ['required', 'string', 'min:4', 'max:8'],
        ], [
            'mobile.required' => 'Mobile number is required.',
            'otp.required' => 'OTP is required.',
        ]);

        $mobile = trim($request->input('mobile'));
        $otp = trim($request->input('otp'));

        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number is not registered as an EWS STP account.'
            ], 404);
        }

        $userRole = $user->roleSlug();
        if (!in_array($userRole, ['ews_developer', 'ews_stp', 'stp'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This account does not have STP access privileges.'
            ], 403);
        }

        if ($user->Is_Active === '0' || $user->Is_Active === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact administrator.'
            ], 403);
        }

        $purpose = 'ews_developer_login';
        $result = $this->otpService->verify($mobile, $purpose, $otp);

        if (!$result['success']) {
            Log::warning("EWS STP Mobile API OTP verification failed", [
                'mobile' => $mobile,
                'purpose' => $purpose,
            ]);
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        // Generate Sanctum Bearer Token
        $token = $user->createToken('stp_mobile_token')->plainTextToken;

        // Resolve Zone and Districts info
        $zoneData = $this->resolveStpZoneData($user);

        Log::info("EWS STP logged in via Mobile API", [
            'user_id' => $user->id,
            'mobile' => $mobile,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful. Welcome to EWS STP Mobile Portal.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role' => $userRole,
                'scheme' => $user->scheme ?? 'EWS',
                'zone_id' => $zoneData['zone_id'],
                'zone_name' => $zoneData['zone_name'],
                'district_id' => $user->district_id,
                'district_name' => $user->district_name,
                'assigned_districts' => $zoneData['districts'],
            ]
        ]);
    }

    /**
     * Resend OTP
     * POST /api/stp/login/resend-otp or POST /api/stp/resend-otp
     */
    public function resendOtp(Request $request): JsonResponse
    {
        return $this->sendOtp($request);
    }

    /**
     * Direct Password Login fallback
     * POST /api/stp/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        $mobile = trim($request->input('mobile'));
        $user = User::where('mobile', $mobile)
            ->orWhere('email', $mobile)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found with provided credentials.'
            ], 404);
        }

        $userRole = $user->roleSlug();
        if (!in_array($userRole, ['ews_developer', 'ews_stp', 'stp'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: STP role privileges required.'
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password credentials.'
            ], 401);
        }

        $token = $user->createToken('stp_mobile_token')->plainTextToken;
        $zoneData = $this->resolveStpZoneData($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role' => $userRole,
                'scheme' => $user->scheme ?? 'EWS',
                'zone_id' => $zoneData['zone_id'],
                'zone_name' => $zoneData['zone_name'],
                'district_id' => $user->district_id,
                'district_name' => $user->district_name,
                'assigned_districts' => $zoneData['districts'],
            ]
        ]);
    }

    /**
     * Get Current Authenticated STP Profile
     * GET /api/stp/profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $zoneData = $this->resolveStpZoneData($user);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role' => $user->roleSlug(),
                'scheme' => $user->scheme ?? 'EWS',
                'zone_id' => $zoneData['zone_id'],
                'zone_name' => $zoneData['zone_name'],
                'district_id' => $user->district_id,
                'district_name' => $user->district_name,
                'assigned_districts' => $zoneData['districts'],
            ]
        ]);
    }

    /**
     * Logout STP user
     * POST /api/stp/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.'
        ]);
    }

    /**
     * Helper to resolve Zone and its child Districts for STP User
     */
    private function resolveStpZoneData($user): array
    {
        $zone = null;
        if (!empty($user->zone_id)) {
            $zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
        }
        if (!$zone && !empty($user->zone_name)) {
            $cleanZone = strtoupper(trim(str_ireplace(' ZONE', '', $user->zone_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanZone)->first();
        }
        if (!$zone && !empty($user->district_name)) {
            $cleanName = strtoupper(trim(str_ireplace(' ZONE', '', $user->district_name)));
            $zone = DB::table('ews_stp_districts')->where('name', $cleanName)->first();
            if (!$zone) {
                $dist = DB::table('ews_districts')->where('name', $cleanName)->first();
                if ($dist && $dist->zone_id) {
                    $zone = DB::table('ews_stp_districts')->where('id', $dist->zone_id)->first();
                }
            }
        }

        $zoneId = $zone ? $zone->id : ($user->zone_id ?? null);
        $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : (!empty($user->zone_name) ? strtoupper($user->zone_name) : 'ZONE');

        // Fetch districts mapped to this zone
        $districts = [];
        if ($zoneId) {
            $districts = DB::table('ews_districts')
                ->where('zone_id', $zoneId)
                ->orderBy('name', 'asc')
                ->get(['id', 'name'])
                ->toArray();
        } elseif (!empty($user->district_id)) {
            $districts = DB::table('ews_districts')
                ->where('id', $user->district_id)
                ->get(['id', 'name'])
                ->toArray();
        }

        return [
            'zone_id' => $zoneId,
            'zone_name' => $zoneName,
            'districts' => $districts,
        ];
    }
}
