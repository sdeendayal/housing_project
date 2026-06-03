<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Icons -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@extends('layouts.auth')
@section('title', 'MMSAY Login')
@section('content')

    <div class="body-div">
        <div class="main-wrapper">
            <div class="premium-login-card">
                <div class="row g-0">
                    <!-- LEFT SECTION -->
                    <div class="col-lg-5">
                        <div class="left-panel h-100 d-flex flex-column justify-content-center">
                            <div>
                                <!-- LOGO -->
                                <div class="d-flex align-items-start gap-4">
                                    <div class="logo-box">
                                        <span class="material-symbols-outlined">
                                            apartment
                                        </span>
                                    </div>
                                    <div>
                                        <div class="portal-heading">
                                            Haryana Housing
                                        </div>
                                        <div class="portal-subtitle">
                                            Department Portal
                                        </div>
                                    </div>
                                </div>
                                <!-- DESCRIPTION -->
                                <div class="description">
                                    Secure login portal for authorized Haryana Government Department officials.
                                    Access housing schemes, applications and internal services.
                                </div>
                                <!-- IMAGE -->
                                <!-- <div class="housing-image">
                                                                <img
                                                                    src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1200&auto=format&fit=crop"
                                                                    alt="Housing">
                                                                
                                                                </div> -->
                            </div>
                            <!-- FEATURES -->
                            <div>
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        <span class="material-symbols-outlined">
                                            verified_user
                                        </span>
                                    </div>
                                    <div>
                                        <div class="feature-title">
                                            Secure Login
                                        </div>
                                        <div class="feature-text">
                                            Protected citizen access
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        <span class="material-symbols-outlined">
                                            sms
                                        </span>
                                    </div>
                                    <div>
                                        <div class="feature-title">
                                            OTP Verification
                                        </div>
                                        <div class="feature-text">
                                            Mobile OTP verification system
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- RIGHT SECTION -->
                    <div class="col-lg-7">
                        <div class="right-panel h-100 d-flex flex-column justify-content-center">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <!-- TOP -->
                            <div class="login-top">
                                <div class="login-icon">
                                    <span class="material-symbols-outlined">
                                        admin_panel_settings
                                    </span>
                                </div>
                                <div>
                                    <div class="login-title">
                                        Department Login
                                    </div>
                                    <div class="login-subtitle">
                                        Official secure login portal
                                    </div>
                                </div>
                            </div>
                            <!-- FORM -->
                            <form action="{{ route('mmsay.login') }}" method="POST">
                                @csrf

                                <!-- EMAIL -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Email Address
                                    </label>
                                    <div class="input-group-custom d-flex">
                                        <input type="email" class="form-control premium-input" name="email"
                                            placeholder="Enter Official Email" required>
                                    </div>
                                </div>

                                <!-- PASSWORD -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Password
                                    </label>
                                    <div class="input-group-custom d-flex">
                                        <input type="password" class="form-control premium-input" name="password"
                                            placeholder="Enter Password" required>
                                    </div>
                                </div>

                                <!-- CAPTCHA -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Captcha Verification
                                    </label>

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="captcha-box" id="captchaText">
                                            {{ $captcha }}
                                        </div>

                                        <div
                                            role="button"
                                            tabindex="0"
                                            aria-label="Refresh captcha"
                                            onclick="refreshCaptcha(this)"
                                            class="captcha-refresh-btn w-10 h-10 flex items-center justify-center rounded-full bg-green-500 hover:bg-green-600 cursor-pointer transition shadow">

                                            <span class="material-symbols-outlined text-white text-xl">
                                                refresh
                                            </span>
                                        </div>

                                        <input type="text" class="form-control premium-input" id="captchaInput"
                                            name="captcha" placeholder="Enter Captcha" required>
                                    </div>
                                </div>

                                <!-- BUTTON -->
                                <button type="submit" class="login-btn">
                                    Secure Login
                                    <span class="material-symbols-outlined align-middle ms-2">
                                        arrow_forward
                                    </span>
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
