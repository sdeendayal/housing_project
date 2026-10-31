<?php

namespace App\Http\Controllers;

use App\Models\EwsDeveloperLog;
use App\Models\Otp;
use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use App\Services\OtpVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EwsDeveloperAuthController extends Controller
{
    public function __construct(
        private OtpVerificationService $otpService
    ) {}

    /**
     * Show combined Developer Login / Register Portal.
     */
    public function showAuth(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->dashboardRoute());
        }

        $activeTab = $request->query('tab', session('active_tab', 'login'));
        $captcha = rand(1000, 9999);
        session(['captcha' => $captcha]);

        return view('ews.developer.login', compact('captcha', 'activeTab'));
    }

    /**
     * Send OTP for Developer Login.
     */
    public function sendLoginOtp(Request $request): RedirectResponse
    {
        if ($request->captcha != session('captcha')) {
            return back()->withInput()->with('error', 'Invalid captcha code. Please try again.')->with('active_tab', 'login');
        }

        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $mobile = trim($request->mobile);
        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('error', 'This mobile number is not registered as a Developer. Please register your Developer account.')
                ->with('not_registered', true)
                ->with('unregistered_mobile', $mobile)
                ->with('active_tab', 'register');
        }

        $userRole = $user->roleSlug();
        if ($userRole !== 'ews_developer') {
            return back()
                ->withInput()
                ->with('error', "This mobile number is not registered as a Developer account (Current role: {$userRole}).")
                ->with('active_tab', 'login');
        }

        if ($user->Is_Active != '1') {
            return back()
                ->withInput()
                ->with('error', 'Your Developer account is currently inactive. Please contact the Department Administrator.')
                ->with('active_tab', 'login');
        }

        $result = $this->otpService->send(
            $mobile,
            Otp::PURPOSE_EWS_DEVELOPER_LOGIN,
            $user->id,
            'EWS Developer Login'
        );

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message'])->with('active_tab', 'login');
        }

        session([
            'ews_dev_login_mobile' => $mobile,
            'ews_dev_login_user_id' => $user->id,
        ]);

        return redirect()->route('ews.developer.login.verify-page')
            ->with('success', 'A 6-digit OTP has been dispatched to your registered mobile number.');
    }

    /**
     * Show Login OTP Verification Page.
     */
    public function showLoginVerify(): View|RedirectResponse
    {
        $mobile = session('ews_dev_login_mobile');
        if (!$mobile) {
            return redirect()->route('ews.developer.login')->with('error', 'Session expired. Please enter your mobile number again.');
        }

        return view('ews.developer.otp-verify', compact('mobile'));
    }

    /**
     * Verify Login OTP.
     */
    public function verifyLoginOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $mobile = session('ews_dev_login_mobile');
        $userId = session('ews_dev_login_user_id');

        if (!$mobile || !$userId) {
            return redirect()->route('ews.developer.login')->with('error', 'Session expired. Please try again.');
        }

        $user = User::find($userId);
        if (!$user || $user->roleSlug() !== 'ews_developer') {
            return redirect()->route('ews.developer.login')->with('error', 'Unauthorized developer account.');
        }

        $result = $this->otpService->verify($mobile, Otp::PURPOSE_EWS_DEVELOPER_LOGIN, $request->otp);

        if (!$result['success']) {
            return back()->with('error', $result['message'] ?? 'Invalid OTP code. Please enter the correct code.');
        }

        session()->forget(['ews_dev_login_mobile', 'ews_dev_login_user_id']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('ews.developer.dashboard'))->with('success', 'Welcome back! Logged in successfully.');
    }

    /**
     * Resend Login OTP.
     */
    public function resendLoginOtp(): RedirectResponse
    {
        $mobile = session('ews_dev_login_mobile');
        $userId = session('ews_dev_login_user_id');

        if (!$mobile) {
            return redirect()->route('ews.developer.login')->with('error', 'Session expired. Please start again.');
        }

        $result = $this->otpService->resend(
            $mobile,
            Otp::PURPOSE_EWS_DEVELOPER_LOGIN,
            $userId,
            'EWS Developer Login Resend'
        );

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Send OTP for New Developer Registration.
     */
    public function sendRegisterOtp(Request $request): RedirectResponse
    {
        if ($request->captcha != session('captcha')) {
            return back()->withInput()->with('error', 'Invalid captcha code. Please try again.')->with('active_tab', 'register');
        }

        $request->validate([
            'name' => 'required|string|min:3|max:150',
            'email' => 'required|email|max:150',
            'mobile' => 'required|digits:10',
        ], [
            'name.required' => 'Developer / Entity name is required.',
            'name.min' => 'Developer / Entity name must be at least 3 characters.',
            'email.required' => 'Official email address is required.',
            'email.email' => 'Please provide a valid official email address.',
            'mobile.required' => '10-digit mobile number is required.',
            'mobile.digits' => 'Mobile number must be exactly 10 numeric digits.',
        ]);

        $mobile = trim($request->mobile);
        $email = strtolower(trim($request->email));
        $name = trim($request->name);

        // Check if mobile already exists
        $existingUserByMobile = User::where('mobile', $mobile)->first();
        if ($existingUserByMobile) {
            if ($existingUserByMobile->role === 'ews_developer') {
                return redirect()->route('ews.developer.login', ['tab' => 'login'])
                    ->withInput()
                    ->with('error', "This mobile number is already registered under '{$existingUserByMobile->name}'. Please Sign In.");
            }
            return back()->withInput()
                ->with('error', "This mobile number is already registered with another account role.")
                ->with('active_tab', 'register');
        }

        // Check if email already exists
        $existingUserByEmail = User::where('email', $email)->first();
        if ($existingUserByEmail) {
            return back()->withInput()
                ->with('error', 'This official email address is already registered in the system.')
                ->with('active_tab', 'register');
        }

        // Store registration payload in session
        session([
            'ews_dev_reg_name' => $name,
            'ews_dev_reg_email' => $email,
            'ews_dev_reg_mobile' => $mobile,
        ]);

        // Send OTP
        $result = $this->otpService->send(
            $mobile,
            Otp::PURPOSE_EWS_DEVELOPER_REGISTRATION,
            null,
            'EWS Developer Registration'
        );

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message'])->with('active_tab', 'register');
        }

        return redirect()->route('ews.developer.register.verify-page')
            ->with('success', "Registration OTP has been dispatched to +91 ******" . substr($mobile, -4) . ".");
    }

    /**
     * Show Registration OTP Verification Page.
     */
    public function showRegisterVerify(): View|RedirectResponse
    {
        $mobile = session('ews_dev_reg_mobile');
        $name = session('ews_dev_reg_name');
        $email = session('ews_dev_reg_email');

        if (!$mobile || !$name) {
            return redirect()->route('ews.developer.login', ['tab' => 'register'])
                ->with('error', 'Registration session expired. Please enter your details again.');
        }

        return view('ews.developer.register-otp-verify', compact('mobile', 'name', 'email'));
    }

    /**
     * Verify Registration OTP & Create Developer Account.
     */
    public function verifyRegisterOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $mobile = session('ews_dev_reg_mobile');
        $name = session('ews_dev_reg_name');
        $email = session('ews_dev_reg_email');

        if (!$mobile || !$name || !$email) {
            return redirect()->route('ews.developer.login', ['tab' => 'register'])
                ->with('error', 'Registration session expired. Please restart the registration process.');
        }

        // Verify OTP
        $result = $this->otpService->verify($mobile, Otp::PURPOSE_EWS_DEVELOPER_REGISTRATION, $request->otp);

        if (!$result['success']) {
            return back()->with('error', $result['message'] ?? 'Invalid verification OTP. Please try again.');
        }

        // Ensure role exists
        $devRole = Role::firstOrCreate(
            ['slug' => 'ews_developer'],
            [
                'name' => 'EWS Developer',
                'dashboard_route' => 'ews.developer.dashboard',
                'is_active' => true,
            ]
        );

        $secureId = md5(uniqid("dev_" . microtime() . rand(), true));

        // Create the Developer User - purely state-level entity with NO zone assignment
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'password' => Hash::make(Str::random(16)),
            'role' => 'ews_developer',
            'scheme' => 'EWS',
            'Is_Active' => '1',
            'Is_Deleted' => '0',
            'zone_id' => null,
            'zone_name' => null,
            'district_name' => null,
            'secure_id' => $secureId,
        ]);

        // Map Role
        RoleType::updateOrCreate(
            ['user_id' => $user->id],
            ['role_id' => $devRole->id, 'Is_Active' => '1', 'Is_Deleted' => '0']
        );

        // Activity Log
        EwsDeveloperLog::create([
            'user_id' => $user->id,
            'action' => 'DEVELOPER_SELF_REGISTERED',
            'details' => "Developer '{$user->name}' (Mobile: {$user->mobile}, Email: {$user->email}) successfully self-registered via OTP.",
            'ip_address' => $request->ip(),
        ]);

        // Clear registration session
        session()->forget(['ews_dev_reg_name', 'ews_dev_reg_email', 'ews_dev_reg_mobile']);

        // Auto-login newly registered developer
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('ews.developer.dashboard')
            ->with('success', "Account verified successfully! Welcome to your Developer Dashboard.");
    }

    /**
     * Resend Registration OTP.
     */
    public function resendRegisterOtp(): RedirectResponse
    {
        $mobile = session('ews_dev_reg_mobile');

        if (!$mobile) {
            return redirect()->route('ews.developer.login', ['tab' => 'register'])
                ->with('error', 'Session expired. Please restart the registration process.');
        }

        $result = $this->otpService->resend(
            $mobile,
            Otp::PURPOSE_EWS_DEVELOPER_REGISTRATION,
            null,
            'EWS Developer Registration Resend'
        );

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }
}
