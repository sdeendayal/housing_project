<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        $user = Auth::user();

        if ($user) {
            if ($user->role === 'ews_department') {
                return redirect('/ews/department/dashboard');
            }

            return redirect('/mmsay-department-dashboard');
        }

        $captcha = random_int(1000, 9999);
        session(['captcha' => $captcha]);

        return view('mmsay.departmentLogin', compact('captcha'));
    }

    public function login(Request $request)
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

        $user = User::query()
            ->where('email', $login)
            ->orWhere('mobile', $login)
            ->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', '❌ User does not exist');
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', '❌ Invalid password');
        }

        // Existing user directly login hoga; loginUsingId ki extra DB query bachegi.
        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->forget('captcha');

        if ($user->role === 'department') {
            return redirect('/mmsay-department-dashboard')
                ->with('success', 'Login Successful');
        }

        if ($user->role === 'ews_department') {
            return redirect('/ews/department/dashboard')
                ->with('success', 'Login Successful');
        }

        try {
            return redirect($user->dashboardRoute())
                ->with('success', 'Login Successful');
        } catch (\Throwable $e) {
            return redirect('/mmsay-department-dashboard')
                ->with('success', 'Login Successful');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Logged out successfully.');
    }
}