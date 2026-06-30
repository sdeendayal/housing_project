<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OtpAuthController extends Controller
{
    public function __construct(
        private OtpVerificationService $otpService
    ) {}

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

        if (! $user->roleType || ! $user->roleType->role_group_id) {
            return back()->withInput()->with('error', 'Your account does not have a configured role group mapping.');
        }

        if (isset($config['scheme']) && $user->scheme !== $config['scheme']) {
            return back()->withInput()->with('error', $config['not_registered_message']);
        }

        if ($user->belongsToRoleGroup($config['wrong_group_slug'])) {
            return back()->withInput()->with('error', $config['wrong_group_message']);
        }

        if (! $user->belongsToRoleGroup($config['role_group'])) {
            return back()->withInput()->with('error', $config['not_registered_message']);
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
            Log::error("{$config['log_label']} OTP generation failed", ['mobile' => $mobile, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Unable to send OTP. Please try again.');
        }

        if (! $result['success']) {
            return back()->withInput()->with('error', $result['message']);
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

        return view($config['verify_view'], [
            'mobile' => $mobile,
            'usesFixedOtp' => OtpVerificationService::usesFixedTestOtp($mobile, $config['otp_purpose']),
        ]);
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

        if (! $user->roleType || ! $user->roleType->role_group_id) {
            return redirect()->route($config['login_route'])->with('error', 'Your account does not have a configured role group mapping.');
        }

        if (isset($config['scheme']) && $user->scheme !== $config['scheme']) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
        }

        if ($user->belongsToRoleGroup($config['wrong_group_slug'])) {
            return redirect()->route($config['login_route'])->with('error', $config['wrong_group_message']);
        }

        if (! $user->belongsToRoleGroup($config['role_group'])) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
        }

        $purpose = $config['otp_purpose'];
        $result = $this->otpService->verify($mobile, $purpose, $request->otp);

        if (! $result['success']) {
            Log::warning("{$config['log_label']} OTP verification failed", [
                'mobile' => $mobile,
                'purpose' => $purpose,
            ]);

            return back()->with('error', $result['message']);
        }

        try {
            DB::transaction(function () use ($mobile, $user, $request, $config, $purpose) {
                Auth::login($user);
                $request->session()->regenerate();

                session()->forget([
                    config('otp-login.session_context_key'),
                    config('otp-login.session_mobile_key'),
                ]);
            });

            Log::info("{$config['log_label']} logged in via OTP", [
                'user_id' => $user->id,
                'mobile' => $mobile,
                'purpose' => $purpose,
            ]);
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

        if (isset($config['scheme']) && $user->scheme !== $config['scheme']) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
        }

        if ($user->belongsToRoleGroup($config['wrong_group_slug'])) {
            return redirect()->route($config['login_route'])->with('error', $config['wrong_group_message']);
        }

        if (! $user->belongsToRoleGroup($config['role_group'])) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
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
            return back()->with('error', 'Unable to resend OTP. Please try again.');
        }

        if (! $result['success']) {
            return back()->with('warning', $result['message']);
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
