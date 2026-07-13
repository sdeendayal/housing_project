<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MmgayBdoAuthApiController extends Controller
{
    /**
     * Generate a new captcha for stateless API consumers.
     */
    public function refreshCaptcha()
    {
        $captcha = rand(1000, 9999);
        $key = Str::uuid()->toString();
        
        // Store in cache for 5 minutes
        Cache::put('bdo_captcha_' . $key, $captcha, 300);

        return response()->json([
            'success' => true,
            'captcha_key' => $key,
            'captcha' => $captcha,
        ]);
    }

    /**
     * Handle BDO Login via API and return Sanctum Token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            // 'captcha' => 'required|string',
            // 'captcha_key' => 'nullable|string',
        ]);

        // Captcha Verification
        // $captcha = $request->input('captcha');
        // $captchaKey = $request->input('captcha_key');

        // if ($captchaKey) {
        //     $cachedCaptcha = Cache::get('bdo_captcha_' . $captchaKey);
        //     if (!$cachedCaptcha || $cachedCaptcha != $captcha) {
        //         return response()->json(['success' => false, 'message' => 'Invalid captcha. Please try again.'], 422);
        //     }
        //     Cache::forget('bdo_captcha_' . $captchaKey);
        // } else {
        //     if ($captcha != session('bdo_captcha')) {
        //         return response()->json(['success' => false, 'message' => 'Invalid captcha. Please try again.'], 422);
        //     }
        // }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'scheme' => 'MMGAY',
            'Is_Active' => '1',
            'Is_Deleted' => '0',
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid BDO login credentials.'], 401);
        }

        $user = Auth::user();

        if (!$user->hasRole('mmgav_bdeo')) {
            Auth::logout();
            return response()->json(['success' => false, 'message' => 'Unauthorized. Access is restricted to MMGAV BDO officers only.'], 403);
        }

        // Generate Sanctum Token
        $token = $user->createToken('bdo_api_token')->plainTextToken;

        Log::info("MMGAV BDO logged in via API", [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful! Welcome back BDO.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role' => 'mmgav_bdeo',
                'block_id' => $user->block_id,
                'block_name' => $user->block_name,
            ]
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
