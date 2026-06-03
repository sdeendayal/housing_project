<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // LOGIN PAGE SHOW
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect('/mmsay-department-dashboard');
        }

        $captcha = rand(1000, 9999);
        session(['captcha' => $captcha]);

        return view('mmsay.departmentLogin', compact('captcha'));
    }

    // LOGIN PROCESS
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required'
        ]);

        // CAPTCHA CHECK
        if ($request->captcha != session('captcha')) {
            return back()->with('error', '❌ Invalid CAPTCHA');
        }

        // USER CHECK (EXISTS OR NOT)
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', '❌ User does not exist');
        }

        // PASSWORD CHECK
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->with('error', '❌ Invalid password');
        }

        // LOGIN USER
        if (Auth::loginUsingId($user->id)) {
            $request->session()->regenerate();

            // ROLE CHECK (optional)
            if ($user->role == 'department') {

                return redirect('/mmsay-department-dashboard')
                    ->with('success', 'Login Successful');
            }

            return redirect('/dashboard');
        }

        return back()->with('error', '❌ Login failed');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/mmsay-department-login')->with('success', 'Logged out successfully');
    }
}