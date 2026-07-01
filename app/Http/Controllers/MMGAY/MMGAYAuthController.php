<?php

namespace App\Http\Controllers\MMGAY;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\IndianMobileNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MMGAYAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->scheme === 'MMGAY' && $user->belongsToRoleGroup('villager')) {
                return redirect()->route('mmgav.villager.dashboard');
            }

            if ($user->scheme === 'MMGAY') {
                return redirect()->intended($user->dashboardRoute());
            }
        }

        $captcha = rand(1000, 9999);
        session(['captcha' => $captcha]);

        return view('mmgay.authLogin', compact('captcha'));
    }

    public function refreshCaptcha()
    {
        $captcha = rand(1000, 9999);

        session(['captcha' => $captcha]);

        return response()->json([
            'captcha' => $captcha,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $login = trim((string) $request->input('email', ''));

        if ($redirect = $this->redirectVillagerToOtpPortal($login)) {
            return $redirect;
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required',
        ]);

        if ($request->captcha != session('captcha')) {
            return back()
                ->withInput()
                ->with('error', 'Invalid captcha.');
        }

        $credentials = [
            'email' => $login,
            'password' => $request->password,
            'scheme' => 'MMGAY',
            'Is_Active' => '1',
            'Is_Deleted' => '0',
        ];

        if (!Auth::attempt($credentials)) {
            if ($redirect = $this->redirectVillagerToOtpPortal($login)) {
                return $redirect;
            }

            return back()
                ->withInput()
                ->with('error', 'Invalid login credentials.');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->belongsToRoleGroup('villager') || $user->roleSlug() === 'villager') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('mmgav.villager.login')
                ->with('info', 'Villager accounts must use Mobile OTP login.');
        }

        $roleSlug = $user->roleSlug();
        // District CEO (Existing - Don't Change)
        if ($roleSlug === 'district_ceo') {
            return redirect()
                ->route('district.dashboard')
                ->with('success', 'Welcome ' . $user->name);
        }

        // DC Dashboard
        if ($roleSlug === 'dc') {
            return redirect()
                ->route('dc.dashboard')
                ->with('success', 'Welcome ' . $user->name);
        }

        // Admin Dashboard
        if ($roleSlug === 'admin') {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Welcome ' . $user->name);
        }

        // Fallback
        return redirect()
            ->intended($user->dashboardRoute())
            ->with('success', 'Welcome ' . $user->name);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mmgay.login')
            ->with('success', 'Logged out successfully.');
    }

    private function redirectVillagerToOtpPortal(string $login): ?RedirectResponse
    {
        $mobile = IndianMobileNumber::normalize($login);

        if ($mobile) {
            return redirect()
                ->route('mmgav.villager.login')
                ->with('info', 'Villagers must log in with their registered mobile number and OTP.');
        }

        $villager = User::query()
            ->where('scheme', 'MMGAY')
            ->where('email', $login)
            ->first();

        if ($villager && $villager->belongsToRoleGroup('villager')) {
            return redirect()
                ->route('mmgav.villager.login')
                ->with('info', 'Villager accounts use Mobile OTP login, not email/password.');
        }

        return null;
    }
}
