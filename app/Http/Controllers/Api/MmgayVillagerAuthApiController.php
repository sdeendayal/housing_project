<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MmgayVillagerAuthApiController extends Controller
{
    public function __construct(
        private OtpVerificationService $otpService
    ) {}

    /**
     * Send OTP to MMGAV Villager.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'captcha' => 'required|string',
            'captcha_key' => 'nullable|string',
        ]);

        // Captcha Verification
        $captcha = $request->input('captcha');
        $captchaKey = $request->input('captcha_key');

        if ($captchaKey) {
            $cachedCaptcha = Cache::get('bdo_captcha_' . $captchaKey);
            if (!$cachedCaptcha || $cachedCaptcha != $captcha) {
                return response()->json(['success' => false, 'message' => 'Invalid captcha. Please try again.'], 422);
            }
            Cache::forget('bdo_captcha_' . $captchaKey);
        } else {
            if ($captcha != session('bdo_captcha')) {
                return response()->json(['success' => false, 'message' => 'Invalid captcha. Please try again.'], 422);
            }
        }

        $config = config("otp-login.contexts.mmgav_villager");
        $mobile = $request->mobile;
        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if (isset($config['scheme']) && $user->scheme !== $config['scheme']) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if ($user->roleSlug() !== 'villager') {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        // Additional physical possession villager check (must have ownermaster and flatMaster entry)
        $owner = DB::table('ownermaster')->where('MobileNo', $mobile)->first();
        if (! $owner) {
            return response()->json(['success' => false, 'message' => 'You have not been allotted a flat yet.'], 400);
        }

        $flatExists = DB::table('flatmaster')->where('FlatId', $owner->FlatId)->exists();
        if (! $flatExists) {
            return response()->json(['success' => false, 'message' => 'You have not been allotted a flat yet.'], 400);
        }

        $purpose = $config['otp_purpose'];

        try {
            $result = $this->otpService->send(
                $mobile,
                $purpose,
                $user->id,
                $config['log_label']
            );
        } catch (\Exception $e) {
            Log::error("{$config['log_label']} OTP generation failed via API", ['mobile' => $mobile, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Unable to send OTP. Please try again.'], 500);
        }

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'resend_after' => $result['resend_after'] ?? 60
        ]);
    }

    /**
     * Verify OTP and return Sanctum Token.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'otp' => 'required|string',
        ]);

        $config = config("otp-login.contexts.mmgav_villager");
        $mobile = $request->input('mobile');
        $otp = $request->input('otp');

        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if (isset($config['scheme']) && $user->scheme !== $config['scheme']) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if ($user->roleSlug() !== 'villager') {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        $purpose = $config['otp_purpose'];
        $result = $this->otpService->verify($mobile, $purpose, $otp);

        if (! $result['success']) {
            Log::warning("{$config['log_label']} OTP verification failed via API", [
                'mobile' => $mobile,
                'purpose' => $purpose,
            ]);
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        // Generate Sanctum Bearer Token
        $token = $user->createToken('villager_api_token')->plainTextToken;

        Log::info("{$config['log_label']} logged in via API OTP", [
            'user_id' => $user->id,
            'mobile' => $mobile,
            'purpose' => $purpose,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful! Welcome to MMGAY Portal.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role' => $user->roleSlug(),
                'district_id' => $user->district_id,
                'district_name' => $user->district_name,
                'block_id' => $user->block_id,
                'block_name' => $user->block_name,
            ]
        ]);
    }

    /**
     * Resend login OTP.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        $config = config("otp-login.contexts.mmgav_villager");
        $mobile = $request->input('mobile');

        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if (isset($config['scheme']) && $user->scheme !== $config['scheme']) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if ($user->roleSlug() !== 'villager') {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        $purpose = $config['otp_purpose'];

        try {
            $result = $this->otpService->resend(
                $mobile,
                $purpose,
                $user->id,
                $config['log_label']
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to resend OTP. Please try again.'], 500);
        }

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'A new OTP has been sent to your mobile number.'
        ]);
    }

    /**
     * Revoke authenticated token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }
}
