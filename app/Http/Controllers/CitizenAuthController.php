<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CitizenAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required',
            'captcha' => 'required'
        ]);

        // Static OTP
        $staticOtp = '123456';

        // Captcha Check
        if ($request->captcha != session('captcha_code')) {

            return back()->with('error', 'Invalid Captcha');
        }

        // OTP Check
        if ($request->otp != $staticOtp) {

            return back()->with('error', 'Invalid OTP');
        }

        return redirect('/mmsay.citizen.dashboard');
    }
}
