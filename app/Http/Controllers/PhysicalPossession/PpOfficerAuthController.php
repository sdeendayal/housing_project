<?php

namespace App\Http\Controllers\PhysicalPossession;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\LoginOtpSmsService;
use App\Services\OtpVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PpOfficerAuthController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;
    private const OTP_MAX_ATTEMPTS = 5;

    public function __construct(
        private LoginOtpSmsService $loginOtpSmsService
    ) {}

    public function showLogin()
    {
        if (Auth::check() && Auth::user()->hasRole('district_officer')) {
            return redirect()->route('pp.officer.dashboard');
        }

        $captcha = rand(1000, 9999);
        session(['captcha' => $captcha]);

        return view('physical-possession.auth.officer-login', compact('captcha'));
    }

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
        $user = $this->findOfficerUser($mobile);

        if (! $user) {
            return back()->withInput()->with('error', 'Mobile number is not registered as a site engineer account.');
        }

        $latestOtp = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latestOtp && $this->isOtpResendOnCooldown($latestOtp)) {
            $waitSeconds = $this->remainingOtpCooldownSeconds($latestOtp);

            return back()->withInput()->with('error', "Please wait {$waitSeconds} seconds before requesting a new OTP.");
        }

        $otpCode = null;

        try {
            DB::transaction(function () use ($mobile, &$otpCode) {
                Otp::where('mobile_number', $mobile)
                    ->whereNull('verified_at')
                    ->where('expires_at', '>', now())
                    ->update(['verified_at' => now()]);

                $otpCode = OtpVerificationService::generateLoginOtpCode();

                Otp::create([
                    'mobile_number' => $mobile,
                    'otp' => $otpCode,
                    'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                    'verified_at' => null,
                    'attempts' => 0,
                ]);

                Log::info('PP Officer OTP generated', ['mobile' => $mobile]);

                if (OtpVerificationService::usesFixedTestOtp($mobile, Otp::PURPOSE_CITIZEN_LOGIN)) {
                    Log::info('PP Officer local OTP for testing', ['mobile' => $mobile, 'otp' => $otpCode]);
                }
            });
        } catch (\Exception $e) {
            Log::error('PP Officer OTP generation failed', ['mobile' => $mobile, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Unable to send OTP. Please try again.');
        }

        if ($otpCode) {
            $this->loginOtpSmsService->send($mobile, $otpCode, 'PP Officer');
        }

        session(['pp_officer_login_mobile' => $mobile]);

        return redirect()->route('pp.officer.login.verify-page')
            ->with('success', 'OTP sent successfully to your mobile number.');
    }

    public function showVerifyOtp()
    {
        $mobile = session('pp_officer_login_mobile');

        if (! $mobile) {
            return redirect()->route('pp.officer.login')
                ->with('error', 'Please enter your mobile number first.');
        }

        return view('physical-possession.auth.officer-otp-verify', compact('mobile'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be exactly 6 digits.',
        ]);

        $mobile = session('pp_officer_login_mobile');

        if (! $mobile) {
            return redirect()->route('pp.officer.login')
                ->with('error', 'Session expired. Please start again.');
        }

        $user = $this->findOfficerUser($mobile);

        if (! $user) {
            return redirect()->route('pp.officer.login')
                ->with('error', 'Mobile number is not registered as a site engineer account.');
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

                session()->forget('pp_officer_login_mobile');
                session()->forget('captcha');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Login failed. Please try again.');
        }

        return redirect()->to($user->dashboardRoute())
            ->with('success', 'Welcome, '.$user->name.'!');
    }

    public function resendOtp(Request $request)
    {
        $mobile = session('pp_officer_login_mobile');

        if (! $mobile) {
            return redirect()->route('pp.officer.login')
                ->with('error', 'Session expired. Please start again.');
        }

        if (! $this->findOfficerUser($mobile)) {
            return redirect()->route('pp.officer.login')
                ->with('error', 'Mobile number is not registered as a site engineer account.');
        }

        $latestOtp = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latestOtp && $this->isOtpResendOnCooldown($latestOtp)) {
            $waitSeconds = $this->remainingOtpCooldownSeconds($latestOtp);

            return back()->with('warning', "Please wait {$waitSeconds} seconds before resending OTP.");
        }

        $otpCode = null;

        try {
            DB::transaction(function () use ($mobile, &$otpCode) {
                Otp::where('mobile_number', $mobile)
                    ->whereNull('verified_at')
                    ->where('expires_at', '>', now())
                    ->update(['verified_at' => now()]);

                $otpCode = OtpVerificationService::generateLoginOtpCode();

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

        if ($otpCode) {
            $this->loginOtpSmsService->send($mobile, $otpCode, 'PP Officer');
        }

        return back()->with('success', 'A new OTP has been sent to your mobile number.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pp.officer.login')
            ->with('success', 'Officer logout successful.');
    }

    private function findOfficerUser(string $mobile): ?User
    {
        $user = User::where('mobile', $mobile)->first();

        if (! $user || !$user->hasRole('district_officer')) {
            return null;
        }

        return $user;
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
