<?php

namespace App\Http\Controllers\MMGAY;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MMGAYAuthController extends Controller
{
    // LOGIN PAGE
    public function showLogin()
    {
        if (Auth::check()) {

            $user = Auth::user();

            if ($user->role == 'district_ceo') {
                return redirect()->route('district.dashboard');
            }

            if ($user->role == 'dc') {
                return redirect()->route('mmgay.dc.dashboard');
            }

            if ($user->role == 'admin') {
                return redirect()->route('mmgay.admin.dashboard');
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
            'captcha' => $captcha
        ]);
    }

    // LOGIN PROCESS
    public function login(Request $request)
    {
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
            'email' => $request->email,
            'password' => $request->password,
            'scheme' => 'MMGAY',
            'Is_Active' => 1,
            'Is_Deleted' => 0,
        ];

        if (!Auth::attempt($credentials)) {
            return back()
                ->withInput()
                ->with('error', 'Invalid login credentials.');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // District CEO
        if ($user->role === 'district_ceo') {
            return redirect()
                ->route('district.dashboard')
                ->with('success', 'Welcome ' . $user->name);
        }

        // DC
        if ($user->role === 'dc') {
            return redirect()
                ->route('mmgay.dc.dashboard')
                ->with('success', 'Welcome ' . $user->name);
        }

        // Admin
        if ($user->role === 'admin') {
            return redirect()
                ->route('mmgay.admin.dashboard')
                ->with('success', 'Welcome ' . $user->name);
        }

        Auth::logout();

        return redirect()
            ->route('mmgay.login')
            ->with('error', 'Unauthorized access.');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mmgay.login')
            ->with('success', 'Logged out successfully.');
    }
}