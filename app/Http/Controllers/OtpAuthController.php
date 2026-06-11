<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OtpAuthController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;
    private const OTP_MAX_ATTEMPTS = 5;

    public function showLogin(string $context): View|RedirectResponse
    {
        $config = config("otp-login.contexts.{$context}");

        if (! $config) {
            abort(404);
        }

        if (Auth::check() && Auth::user()->belongsToRoleGroup($config['role_group'])) {
            return redirect()->intended(Auth::user()->dashboardRoute());
        }

        $captcha = rand(1000, 9999);
        session(['captcha' => $captcha]);

        return view($config['login_view'], compact('captcha'));
    }

    public function sendOtp(SendOtpRequest $request, string $context): RedirectResponse
    {
        $config = config("otp-login.contexts.{$context}");

        if (! $config) {
            abort(404);
        }

        if ($request->captcha != session('captcha')) {
            return back()->withInput()->with('error', 'Invalid captcha. Please try again.');
        }

        $mobile = $request->mobile;
        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return back()->withInput()->with('error', $config['not_registered_message']);
        }

        if ($user->belongsToRoleGroup($config['wrong_group_slug'])) {
            return back()->withInput()->with('error', $config['wrong_group_message']);
        }

        if (! $user->belongsToRoleGroup($config['role_group'])) {
            return back()->withInput()->with('error', $config['not_registered_message']);
        }

        $latestOtp = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latestOtp) {
            $elapsedSeconds = (int) $latestOtp->created_at->diffInSeconds(now());

            if ($elapsedSeconds < self::OTP_RESEND_COOLDOWN_SECONDS) {
                $waitSeconds = max(1, (int) ceil(self::OTP_RESEND_COOLDOWN_SECONDS - $elapsedSeconds));

                return back()->withInput()->with('error', "Please wait {$waitSeconds} seconds before requesting a new OTP.");
            }
        }

        try {
            DB::transaction(function () use ($mobile, $config) {
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

                Log::info("{$config['log_label']} OTP generated", ['mobile' => $mobile]);

                if (app()->environment('local')) {
                    Log::info("{$config['log_label']} local OTP for testing", ['mobile' => $mobile, 'otp' => $otpCode]);
                }
            });
        } catch (\Exception $e) {
            Log::error("{$config['log_label']} OTP generation failed", ['mobile' => $mobile, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Unable to send OTP. Please try again.');
        }

        session([
            config('otp-login.session_context_key') => $context,
            config('otp-login.session_mobile_key') => $mobile,
        ]);

        return redirect()->route($config['verify_page_route'])
            ->with('success', 'OTP sent successfully to your mobile number.');
    }

    public function showVerifyOtp(string $context): View|RedirectResponse
    {
        $config = config("otp-login.contexts.{$context}");

        if (! $config) {
            abort(404);
        }

        $sessionContext = session(config('otp-login.session_context_key'));
        $mobile = session(config('otp-login.session_mobile_key'));

        if (! $mobile || $sessionContext !== $context) {
            return redirect()->route($config['login_route'])
                ->with('error', 'Please enter your mobile number first.');
        }

        return view($config['verify_view'], compact('mobile'));
    }

    public function verifyOtp(VerifyOtpRequest $request, string $context): RedirectResponse
    {
        $config = config("otp-login.contexts.{$context}");

        if (! $config) {
            abort(404);
        }

        $sessionContext = session(config('otp-login.session_context_key'));
        $mobile = session(config('otp-login.session_mobile_key'));

        if (! $mobile || $sessionContext !== $context) {
            return redirect()->route($config['login_route'])
                ->with('error', 'Session expired. Please start again.');
        }

        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
        }

        if ($user->belongsToRoleGroup($config['wrong_group_slug'])) {
            return redirect()->route($config['login_route'])->with('error', $config['wrong_group_message']);
        }

        if (! $user->belongsToRoleGroup($config['role_group'])) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
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

            Log::warning("{$config['log_label']} OTP verification failed", [
                'mobile' => $mobile,
                'attempts' => $otpRecord->attempts,
            ]);

            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        try {
            DB::transaction(function () use ($mobile, $user, $request, $config) {
                Otp::where('mobile_number', $mobile)->delete();

                Auth::login($user);
                $request->session()->regenerate();

                session()->forget([
                    config('otp-login.session_context_key'),
                    config('otp-login.session_mobile_key'),
                ]);
            });

            Log::info("{$config['log_label']} logged in via OTP", ['user_id' => $user->id, 'mobile' => $mobile]);
        } catch (\Exception $e) {
            Log::error("{$config['log_label']} login failed", ['mobile' => $mobile, 'error' => $e->getMessage()]);

            return back()->with('error', 'Login failed. Please try again.');
        }

        return redirect()->intended($user->dashboardRoute())
            ->with('success', 'Login successful! Welcome back.');
    }

    public function resendOtp(Request $request, string $context): RedirectResponse
    {
        $config = config("otp-login.contexts.{$context}");

        if (! $config) {
            abort(404);
        }

        $sessionContext = session(config('otp-login.session_context_key'));
        $mobile = session(config('otp-login.session_mobile_key'));

        if (! $mobile || $sessionContext !== $context) {
            return redirect()->route($config['login_route'])
                ->with('error', 'Session expired. Please start again.');
        }

        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
        }

        if ($user->belongsToRoleGroup($config['wrong_group_slug'])) {
            return redirect()->route($config['login_route'])->with('error', $config['wrong_group_message']);
        }

        if (! $user->belongsToRoleGroup($config['role_group'])) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
        }

        $latestOtp = Otp::where('mobile_number', $mobile)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latestOtp) {
            $elapsedSeconds = (int) $latestOtp->created_at->diffInSeconds(now());

            if ($elapsedSeconds < self::OTP_RESEND_COOLDOWN_SECONDS) {
                $waitSeconds = max(1, (int) ceil(self::OTP_RESEND_COOLDOWN_SECONDS - $elapsedSeconds));

                return back()->with('warning', "Please wait {$waitSeconds} seconds before resending OTP.");
            }
        }

        try {
            DB::transaction(function () use ($mobile, $config) {
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

                Log::info("{$config['log_label']} OTP resent", ['mobile' => $mobile]);

                if (app()->environment('local')) {
                    Log::info("{$config['log_label']} local OTP for testing", ['mobile' => $mobile, 'otp' => $otpCode]);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to resend OTP. Please try again.');
        }

        return back()->with('success', 'A new OTP has been sent to your mobile number.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $loginRoute = 'citizen.login';

        if (Auth::check() && Auth::user()->belongsToRoleGroup('department')) {
            $loginRoute = 'pp.department.login';
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($loginRoute)->with('success', 'Logged out successfully.');
    }
}
