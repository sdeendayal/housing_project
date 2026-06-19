<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\Otp;
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

        $otpRecord = Otp::where('mobile_number', $mobile)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($otpRecord) {
            $this->sendLoginOtpSms($mobile, $otpRecord->otp, $config['log_label']);
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

        $otpRecord = Otp::where('mobile_number', $mobile)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($otpRecord) {
            $this->sendLoginOtpSms($mobile, $otpRecord->otp, $config['log_label']);
        }

        return back()->with('success', 'A new OTP has been sent to your mobile number.');
    }

    private function sendLoginOtpSms(string $mobile, string $otpCode, string $logLabel): void
    {
        if (app()->environment('local')) {
            return;
        }

        $tem_id = '1007056441918679505';
        $message = 'Dear User, '.$otpCode.' is OTP for Login, Cash Award Management System. Sports Department, Haryana';

        try {
            $response = $this->sendSMS($mobile, $message, $tem_id);
            Log::info("{$logLabel} OTP SMS sent", ['mobile' => $mobile, 'response' => $response]);
        } catch (\Throwable $e) {
            Log::error("{$logLabel} OTP SMS failed", ['mobile' => $mobile, 'error' => $e->getMessage()]);
        }
    }

    private function sendSMS(string $mobile, string $message, string $temp_id): mixed
    {
        $username = 'haryanait-sport';
        $password = 'sports@1234';
        $senderid = 'GOVHRY';
        $dept_key = 'dca7fc77-9e28-4765-bbaa-07bd43197b2e';
        $encryp_password = sha1(trim($password));

        return $this->sendSingleSMS($username, $encryp_password, $senderid, $message, $mobile, $dept_key, $temp_id);
    }

    private function sendSingleSMS(
        string $username,
        string $encryp_password,
        string $senderid,
        string $message,
        string $mobileno,
        string $deptSecureKey,
        string $temp_id
    ): mixed {
        $key = hash('sha512', trim($username).trim($senderid).trim($message).trim($deptSecureKey));

        $data = [
            'username' => trim($username),
            'password' => trim($encryp_password),
            'senderid' => trim($senderid),
            'content' => trim($message),
            'smsservicetype' => 'otpmsg',
            'mobileno' => trim($mobileno),
            'key' => trim($key),
            'templateid' => trim($temp_id),
        ];

        return $this->postToUrl('https://msdgweb.mgov.gov.in/esms/sendsmsrequestDLT', $data);
    }

    private function postToUrl(string $url, array $data): mixed
    {
        $fields = '';

        foreach ($data as $key => $value) {
            $fields .= $key.'='.$value.'&';
        }

        rtrim($fields, '&');
        $post = curl_init();
        curl_setopt($post, CURLOPT_SSLVERSION, 6);
        curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($post);
        curl_close($post);

        return $result;
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
