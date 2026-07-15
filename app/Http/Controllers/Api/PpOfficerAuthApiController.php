<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PpOfficerAuthApiController extends Controller
{
    public function __construct(
        private OtpVerificationService $otpService
    ) {}

    /**
     * Generate a new captcha for stateless API consumers.
     */
    public function refreshCaptcha()
    {
        $captcha = rand(1000, 9999);
        $key = Str::uuid()->toString();
        
        // Store in cache for 5 minutes
        Cache::put('captcha_' . $key, $captcha, 300);

        return response()->json([
            'success' => true,
            'captcha_key' => $key,
            'captcha' => $captcha,
        ]);
    }

    /**
     * Send OTP to Department/District Officer.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        /*
        // Captcha Verification
        $captcha = $request->input('captcha');
        $captchaKey = $request->input('captcha_key');

        if ($captchaKey) {
            $cachedCaptcha = Cache::get('captcha_' . $captchaKey);
            if (!$cachedCaptcha || $cachedCaptcha != $captcha) {
                return response()->json(['success' => false, 'message' => 'Invalid captcha. Please try again.'], 422);
            }
            Cache::forget('captcha_' . $captchaKey);
        } else {
            if ($captcha != session('captcha')) {
                return response()->json(['success' => false, 'message' => 'Invalid captcha. Please try again.'], 422);
            }
        }
        */

        $config = config("otp-login.contexts.department");
        $mobile = $request->mobile;
        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if (in_array($user->roleSlug(), ['citizen', 'villager', 'mmgav_bdeo'], true)) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
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

        $config = config("otp-login.contexts.department");
        $mobile = $request->input('mobile');
        $otp = $request->input('otp');

        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if (in_array($user->roleSlug(), ['citizen', 'villager', 'mmgav_bdeo'], true)) {
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
        $token = $user->createToken('officer_api_token')->plainTextToken;

        Log::info("{$config['log_label']} logged in via API OTP", [
            'user_id' => $user->id,
            'mobile' => $mobile,
            'purpose' => $purpose,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful! Welcome back.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role' => $user->roleSlug(),
                'district_id' => $user->district_id,
                'district_name' => $user->district_name,
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

        $config = config("otp-login.contexts.department");
        $mobile = $request->input('mobile');

        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => $config['not_registered_message']], 404);
        }

        if (in_array($user->roleSlug(), ['citizen', 'villager', 'mmgav_bdeo'], true)) {
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
