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

        if (Auth::check()) {
            return redirect()->intended(Auth::user()->dashboardRoute());
        }

        $captcha = rand(1000, 9999);
        session(['captcha' => $captcha]);

        return view($config['login_view'], compact('captcha', 'context', 'config'));
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

        if (! $user->roleType || ! $user->roleType->role_id) {
            return back()->withInput()->with('error', 'Your account does not have a configured role mapping.');
        }
       

        if (isset($config['scheme']) && $user->scheme !== $config['scheme']) {
            return back()->withInput()->with('error', $config['not_registered_message']);
        }

         

        $userRole = $user->roleSlug();
        if ($context === 'citizen') {
            if ($userRole !== 'citizen') {
                return back()->withInput()->with('error', 'Mobile number is not registered as a citizen account.');
            }
        } elseif ($context === 'mmgav_villager') {
            if ($userRole !== 'villager') {
                return back()->withInput()->with('error', 'Mobile number is not registered as an MMGAV villager account.');
            }
        } elseif ($context === 'ews_citizen') {
            if ($userRole !== 'ews_user') {
                return back()->withInput()->with('error', 'Mobile number is not registered as an EWS citizen account.');
            }
        } elseif ($context === 'ews_developer') {
            if ($userRole !== 'ews_developer') {
                return back()->withInput()->with('error', 'Mobile number is not registered as an EWS developer account.');
            }
        } elseif ($context === 'department') {
            if (in_array($userRole, ['citizen', 'villager', 'mmgav_bdeo'], true)) {
                return back()->withInput()->with('error', 'Mobile number is not registered as a department officer account.');
            }
        }

        

        if ($context === 'mmgav_villager') {
            $owner = DB::table('ownermaster')->where('MobileNo', $mobile)->first();
            if (! $owner) {
                return back()->withInput()->with('error', 'You have not been allotted a flat yet.');
            }

            $flatExists = DB::table('flatmaster')->where('FlatId', $owner->FlatId)->exists();
            if (! $flatExists) {
                return back()->withInput()->with('error', 'You have not been allotted a flat yet.');
            }
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

        if (! $user->roleType || ! $user->roleType->role_id) {
            return redirect()->route($config['login_route'])->with('error', 'Your account does not have a configured role mapping.');
        }

        if (isset($config['scheme']) && $user->scheme !== $config['scheme']) {
            return redirect()->route($config['login_route'])->with('error', $config['not_registered_message']);
        }

        $userRole = $user->roleSlug();
        if ($context === 'citizen') {
            if ($userRole !== 'citizen') {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as a citizen account.');
            }
        } elseif ($context === 'mmgav_villager') {
            if ($userRole !== 'villager') {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as an MMGAV villager account.');
            }
        } elseif ($context === 'ews_citizen') {
            if ($userRole !== 'ews_user') {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as an EWS citizen account.');
            }
        } elseif ($context === 'ews_developer') {
            if ($userRole !== 'ews_developer') {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as an EWS developer account.');
            }
        } elseif ($context === 'department') {
            if (in_array($userRole, ['citizen', 'villager', 'mmgav_bdeo'], true)) {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as a department officer account.');
            }
        }

        if ($context === 'mmgav_villager') {
            $owner = DB::table('ownermaster')->where('MobileNo', $mobile)->first();
            if (! $owner) {
                return redirect()->route($config['login_route'])->with('error', 'You have not been allotted a flat yet.');
            }

            $flatExists = DB::table('flatmaster')->where('FlatId', $owner->FlatId)->exists();
            if (! $flatExists) {
                return redirect()->route($config['login_route'])->with('error', 'You have not been allotted a flat yet.');
            }
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

        $userRole = $user->roleSlug();
        if ($context === 'citizen') {
            if ($userRole !== 'citizen') {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as a citizen account.');
            }
        } elseif ($context === 'mmgav_villager') {
            if ($userRole !== 'villager') {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as an MMGAV villager account.');
            }
        } elseif ($context === 'ews_citizen') {
            if ($userRole !== 'ews_user') {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as an EWS citizen account.');
            }
        } elseif ($context === 'department') {
            if (in_array($userRole, ['citizen', 'villager', 'mmgav_bdeo'], true)) {
                return redirect()->route($config['login_route'])->with('error', 'Mobile number is not registered as a department officer account.');
            }
        }

        if ($context === 'mmgav_villager') {
            $owner = DB::table('ownermaster')->where('MobileNo', $mobile)->first();
            if (! $owner) {
                return redirect()->route($config['login_route'])->with('error', 'You have not been allotted a flat yet.');
            }

            $flatExists = DB::table('flatmaster')->where('FlatId', $owner->FlatId)->exists();
            if (! $flatExists) {
                return redirect()->route($config['login_route'])->with('error', 'You have not been allotted a flat yet.');
            }
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

        if (Auth::check()) {
            $userRole = Auth::user()->roleSlug();
            if ($userRole === 'villager') {
                $loginRoute = 'mmgav.villager.login';
            } elseif ($userRole === 'ews_user') {
                $loginRoute = 'ews.citizen.login';
            } elseif ($userRole === 'ews_developer') {
                $loginRoute = 'ews.developer.login';
            } elseif ($userRole === 'mmgay-dtp') {
                $loginRoute = 'pp.dtp.login';
            } elseif (!in_array($userRole, ['citizen', 'villager', 'mmgav_bdeo'], true)) {
                $loginRoute = 'pp.department.login';
            }
        }

        $referrer = $request->headers->get('referer');
        $redirectToHome = false;
        if ($referrer) {
            $parsedReferrer = parse_url($referrer, PHP_URL_PATH);
            if (in_array(rtrim($parsedReferrer, '/'), ['', '/hfa'], true)) {
                $redirectToHome = true;
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->has('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Logged out successfully.');
        }

        if ($redirectToHome) {
            return redirect()->route('home')->with('success', 'Logged out successfully.');
        }

        return redirect()->route($loginRoute)->with('success', 'Logged out successfully.');
    }

    public function showDtpLogin()
    {
        $user = Auth::user();
        if ($user) {
            return redirect($user->dashboardRoute());
        }

        $captcha = random_int(1000, 9999);
        session(['captcha' => $captcha]);

        return view('physical-possession.dtp_login', compact('captcha'));
    }

    public function dtpLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
            'captcha'  => ['required'],
        ]);

        if ((string) $credentials['captcha'] !== (string) session('captcha')) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', '❌ Invalid CAPTCHA');
        }

        $login = trim($credentials['email']);

        $user = User::where('email', $login)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', '❌ User does not exist');
        }

        if ($user->role !== 'mmgay-dtp') {
            return back()
                ->withInput($request->only('email'))
                ->with('error', '❌ Unauthorized role');
        }

        if (!\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', '❌ Invalid password');
        }

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->forget('captcha');

        return redirect($user->dashboardRoute())
            ->with('success', 'Login successful! Welcome back DTP.');
    }
}
