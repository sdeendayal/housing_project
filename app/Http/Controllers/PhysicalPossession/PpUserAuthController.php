<?php

namespace App\Http\Controllers\PhysicalPossession;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PpUserAuthController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;
    private const OTP_MAX_ATTEMPTS = 5;

    // Step 1: Mobile + Captcha page
    public function showLogin()
    {
        if (Auth::check() && $this->isPpLoginUser(Auth::user())) {
            return redirect()->to(Auth::user()->dashboardRoute());
        }

        $captcha = rand(1000, 9999);
        session(['captcha' => $captcha]);

        return view('physical-possession.auth.user-login', compact('captcha'));
    }

    // Step 2: Check mobile + captcha, send OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
            'captcha' => 'required|string|max:10',
        ], [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
            'mobile.regex' => 'Enter a valid Indian mobile number starting with 6, 7, 8, or 9.',
            'captcha.required' => 'Captcha is required.',
        ]);

        if ($request->captcha != session('captcha')) {
            return back()->withInput()->with('error', 'Invalid captcha. Please try again.');
        }

        $mobile = $request->mobile;

        $user = $this->findPpLoginUser($mobile);
        if (! $user) {
            return back()->withInput()->with('error', 'Mobile number is not registered for this portal.');
        }

        $latestOtp = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latestOtp && $this->isOtpResendOnCooldown($latestOtp)) {
            $waitSeconds = $this->remainingOtpCooldownSeconds($latestOtp);

            return back()->withInput()->with('error', "Please wait {$waitSeconds} seconds before requesting a new OTP.");
        }

        try {
            DB::transaction(function () use ($mobile) {
                Otp::where('mobile_number', $mobile)
                    ->whereNull('verified_at')
                    ->where('expires_at', '>', now())
                    ->update(['verified_at' => now()]);

                // Local = 111111, production = random 6 digit
                $otpCode = app()->environment('local')
                    ? '111111'
                    : str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                Otp::create([
                    'mobile_number' => $mobile,
                    'otp' => $otpCode,
                    'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                    'verified_at' => null,
                    'attempts' => 0,
                ]);

                Log::info('PP OTP generated', ['mobile' => $mobile]);

                if (app()->environment('local')) {
                    Log::info('PP local OTP for testing', ['mobile' => $mobile, 'otp' => $otpCode]);
                }
            });
        } catch (\Exception $e) {
            Log::error('PP OTP generation failed', ['mobile' => $mobile, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Unable to send OTP. Please try again.');
        }

        session(['pp_login_mobile' => $mobile]);

        return redirect()->route('pp.user.login.verify-page')
            ->with('success', 'OTP sent successfully to your mobile number.');
    }

    // Step 3: OTP verification page
    public function showVerifyOtp()
    {
        $mobile = session('pp_login_mobile');

        if (! $mobile) {
            return redirect()->route('pp.user.login')
                ->with('error', 'Please enter your mobile number first.');
        }

        return view('physical-possession.auth.otp-verify', compact('mobile'));
    }

    // Step 4: Verify OTP and login
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be exactly 6 digits.',
        ]);

        $mobile = session('pp_login_mobile');

        if (! $mobile) {
            return redirect()->route('pp.user.login')
                ->with('error', 'Session expired. Please start again.');
        }

        $user = $this->findPpLoginUser($mobile);
        if (! $user) {
            return redirect()->route('pp.user.login')
                ->with('error', 'Mobile number is not registered for this portal.');
        }

        $otpRecord = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otpRecord) {
            return back()->with('error', 'No active OTP found. Please request a new OTP.');
        }

        if ($otpRecord->isExpired()) {
            $otpRecord->update(['verified_at' => now()]);

            return back()->with('error', 'OTP has expired. Please request a new OTP.');
        }

        if ($otpRecord->attempts >= self::OTP_MAX_ATTEMPTS) {
            $otpRecord->update(['verified_at' => now()]);

            return back()->with('error', 'Too many failed attempts. Please request a new OTP.');
        }

        if ($request->otp !== $otpRecord->otp) {
            $otpRecord->increment('attempts');

            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        try {
            DB::transaction(function () use ($mobile, $user, $request) {
                Otp::where('mobile_number', $mobile)->delete();

                Auth::login($user);
                $request->session()->regenerate();

                session()->forget('pp_login_mobile');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Login failed. Please try again.');
        }

        return redirect()->to($user->dashboardRoute())
            ->with('success', 'Welcome, '.$user->name.'!');
    }

    // Resend OTP on verify page
    public function resendOtp(Request $request)
    {
        $mobile = session('pp_login_mobile');

        if (! $mobile) {
            return redirect()->route('pp.user.login')
                ->with('error', 'Session expired. Please start again.');
        }

        if (! $this->findPpLoginUser($mobile)) {
            return redirect()->route('pp.user.login')
                ->with('error', 'Mobile number is not registered for this portal.');
        }

        $latestOtp = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latestOtp && $this->isOtpResendOnCooldown($latestOtp)) {
            $waitSeconds = $this->remainingOtpCooldownSeconds($latestOtp);

            return back()->with('warning', "Please wait {$waitSeconds} seconds before resending OTP.");
        }

        try {
            DB::transaction(function () use ($mobile) {
                Otp::where('mobile_number', $mobile)
                    ->whereNull('verified_at')
                    ->where('expires_at', '>', now())
                    ->update(['verified_at' => now()]);

                $otpCode = app()->environment('local')
                    ? '111111'
                    : str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                Otp::create([
                    'mobile_number' => $mobile,
                    'otp' => $otpCode,
                    'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                    'verified_at' => null,
                    'attempts' => 0,
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to resend OTP. Please try again.');
        }

        return back()->with('success', 'A new OTP has been sent to your mobile number.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('citizen.login')
            ->with('success', 'Logged out successfully.');
    }

    private function findPpLoginUser(string $mobile): ?User
    {
        $user = User::where('mobile', $mobile)->first();

        return $this->isPpLoginUser($user) ? $user : null;
    }

    private function isPpLoginUser(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->belongsToRoleGroup('citizen') || $user->belongsToRoleGroup('district_officer')) {
            return true;
        }

        return in_array($user->role, ['citizen', 'district_officer'], true);
    }

    private function isOtpResendOnCooldown(Otp $latestOtp): bool
    {
        return $this->otpCooldownElapsedSeconds($latestOtp) < self::OTP_RESEND_COOLDOWN_SECONDS;
    }

    private function remainingOtpCooldownSeconds(Otp $latestOtp): int
    {
        $remaining = self::OTP_RESEND_COOLDOWN_SECONDS - $this->otpCooldownElapsedSeconds($latestOtp);

        return max(1, (int) ceil($remaining));
    }

    private function otpCooldownElapsedSeconds(Otp $latestOtp): int
    {
        return (int) $latestOtp->created_at->diffInSeconds(now());
    }
}
