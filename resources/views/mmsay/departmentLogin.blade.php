<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Icons -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
<script>
    document.addEventListener(...)
</script>

@extends('layouts.auth')
@section('title', 'MMSAY Login')
@section('content')
    <style>
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        /* HIDE DEFAULT HEADER & FOOTER */
        body.page-dept-login header {
            display: none !important;
        }
        body.page-dept-login footer {
            display: none !important;
        }
        body.page-dept-login main.flex-grow {
            padding: 0 !important;
            margin: 0 !important;
            height: 100vh !important;
            min-height: 100vh !important;
            overflow: hidden !important;
        }

        /* SCREEN LAYOUT */
        .mmsay-login-screen {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #f8fafc;
            font-family: 'Poppins', sans-serif !important;
        }

        /* 1. VISUAL PANEL (LEFT) */
        .mmsay-visual-panel {
            position: relative;
            width: 58%;
            height: 100%;
            background: url('{{ asset('images/citizen-login/sehri_bg.png') }}') center center / cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 30px !important;
            color: #ffffff;
            overflow: hidden;
        }
        .mmsay-visual-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(9, 21, 54, 0.94) 0%, rgba(15, 32, 67, 0.88) 100%);
            z-index: 1;
        }
        .panel-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Branding Header */
        .branding-header .emblem-img {
            height: 45px !important;
            width: auto;
            filter: drop-shadow(0 3px 6px rgba(0,0,0,0.3));
        }
        .branding-header .dept-title {
            font-size: 13.5px !important;
            font-weight: 800;
            letter-spacing: 0.8px;
            color: #ffffff;
            margin: 0;
        }
        .branding-header .dept-sub {
            font-size: 10.5px !important;
            color: #fbd38d;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
        }

        /* Hero presentation */
        .hero-section {
            max-width: 500px;
            margin-top: -10px;
        }
        .hero-title {
            font-size: 26px !important;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            margin-bottom: 12px !important;
            text-shadow: 0 3px 8px rgba(0,0,0,0.3);
        }
        .badge-scheme {
            font-size: 11px !important;
            background: rgba(251, 211, 141, 0.2);
            color: #fbd38d;
            border: 1px solid rgba(251, 211, 141, 0.4);
            padding: 1px 6px;
            border-radius: 20px;
            vertical-align: middle;
            margin-left: 8px;
            font-weight: 700;
        }
        .hero-desc {
            font-size: 13px !important;
            line-height: 1.5 !important;
            color: #cbd5e1;
            text-align: justify;
        }

        /* Scheme pills */
        .scheme-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 6px 12px !important;
            border-radius: 30px;
            font-size: 11.5px !important;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .scheme-pill span.material-symbols-outlined {
            color: #63b3ed;
            font-size: 16px !important;
        }
        .scheme-pill:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(99, 179, 237, 0.4);
            transform: translateY(-2px);
        }

        .left-footer p {
            font-size: 11px !important;
            color: #94a3b8;
            margin: 0;
        }

        /* 2. FORM PANEL (RIGHT) */
        .mmsay-form-panel {
            width: 42%;
            height: 100%;
            background: #ffffff;
            padding: 30px !important;
            box-shadow: -10px 0 30px rgba(0,0,0,0.02);
            z-index: 2;
        }
        .login-card-container {
            width: 100%;
            max-width: 340px;
            margin: auto;
        }

        .login-badge-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px;
            background: rgba(49, 130, 206, 0.06);
            border: 1.5px solid rgba(49, 130, 206, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px !important;
        }
        .login-badge-icon span {
            color: #2b6cb0;
            font-size: 22px !important;
        }
        .login-title {
            font-size: 18px !important;
            font-weight: 800;
            color: #1a202c;
            margin: 0 0 3px 0;
            letter-spacing: -0.5px;
        }
        .login-subtitle {
            font-size: 12.5px !important;
            color: #718096;
            margin: 0;
        }

        /* Form Controls */
        .input-label {
            font-size: 10px !important;
            font-weight: 700;
            text-transform: uppercase;
            color: #4a5568;
            margin-bottom: 3px !important;
            letter-spacing: 0.8px;
        }
        .input-container-premium {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .input-icon-pre {
            position: absolute;
            left: 12px !important;
            color: #a0aec0;
            font-size: 16px !important;
        }
        .form-input-premium {
            width: 100%;
            height: 34px !important;
            padding: 6px 12px 6px 36px !important;
            font-size: 12.5px !important;
            font-weight: 500;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            background-color: #f8fafc;
            color: #2d3748;
            outline: none !important;
            transition: all 0.2s ease;
        }
        .form-input-premium:focus {
            background-color: #ffffff;
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.12);
        }
        .input-container-premium:focus-within .input-icon-pre {
            color: #3182ce;
        }

        /* CAPTCHA Row */
        .captcha-row {
            width: 100%;
        }
        .captcha-badge {
            height: 34px !important;
            font-size: 18px !important;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 4px !important;
            padding: 0 12px !important;
            line-height: 34px !important;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            color: #2d3748;
            background: linear-gradient(45deg, #e2e8f0 25%, #f7fafc 25%, #f7fafc 50%, #e2e8f0 50%, #e2e8f0 75%, #f7fafc 75%, #f7fafc 100%) !important;
            background-size: 20px 20px !important;
            text-align: center;
            user-select: none;
            text-shadow: 1px 1px 1px rgba(255,255,255,0.8);
        }
        .captcha-spin-btn {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            border-radius: 6px;
            background: #48bb78;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 6px rgba(72, 187, 120, 0.2);
        }
        .captcha-spin-btn span {
            color: #ffffff;
            font-size: 16px !important;
        }
        .captcha-spin-btn:hover {
            background: #38a169;
            transform: rotate(180deg);
        }
        .captcha-input-wrapper {
            flex-grow: 1;
        }
        .captcha-input-wrapper .form-input-premium {
            padding-left: 10px !important;
        }

        /* Action Button */
        .login-submit-btn {
            width: 100%;
            height: 36px !important;
            background: linear-gradient(135deg, #2b6cb0 0%, #3182ce 100%);
            border: none;
            border-radius: 6px;
            color: #ffffff;
            font-size: 13px !important;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(49, 130, 206, 0.2);
            transition: all 0.2s ease;
            outline: none !important;
            margin-top: 8px !important;
        }
        .login-submit-btn:hover {
            background: linear-gradient(135deg, #1a365d 0%, #2b6cb0 100%);
            box-shadow: 0 6px 16px rgba(49, 130, 206, 0.35);
            transform: translateY(-1px);
        }

        /* Responsive Mobile Layer */
        @media (max-width: 991px) {
            .mmsay-login-screen {
                flex-direction: column;
                overflow-y: auto;
                height: 100vh;
            }
            .mmsay-visual-panel {
                display: none !important;
            }
            .mmsay-form-panel {
                width: 100% !important;
                height: auto !important;
                min-height: 100vh;
                padding: 40px 20px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .mobile-branding {
                display: flex !important;
                align-items: center;
                gap: 15px;
                margin-bottom: 30px;
                align-self: center;
                width: 100%;
                max-width: 360px;
                border-bottom: 2px solid #edf2f7;
                padding-bottom: 15px;
            }
            .emblem-img-mobile {
                height: 50px;
                width: auto;
            }
            .dept-title-mobile {
                font-size: 15px;
                font-weight: 800;
                color: #2d3748;
                margin: 0;
            }
            .dept-sub-mobile {
                font-size: 11px;
                color: #dd6b20;
                font-weight: 700;
                margin: 0;
                text-transform: uppercase;
            }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .panel-content {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .login-card-container {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
    </style>

    <div class="mmsay-login-screen">
        <!-- LEFT PANEL: Immersive Visuals -->
        <div class="mmsay-visual-panel">
            <div class="panel-overlay"></div>
            <div class="panel-content">
                <!-- Branding Header -->
                <div class="branding-header d-flex align-items-center gap-3">
                    <img src="{{ asset('Haryana_emblem.png') }}" alt="Haryana Emblem" class="emblem-img">
                    <div>
                        <h2 class="dept-title">DEPARTMENT OF HOUSING FOR ALL</h2>
                        <p class="dept-sub">Government of Haryana</p>
                    </div>
                </div>

                <!-- Hero Section -->
                <div class="hero-section">
                    <h1 class="hero-title">Mukhyamantri Shehri Awas Yojana <span class="badge-scheme">MMSAY</span></h1>
                    <p class="hero-desc">Building affordable, secure, and sustainable homes for every citizen of Haryana. Access official records, approve registrations, and manage scheme allotments.</p>
                    
                    <!-- Quick stats / badges -->
                    <div class="scheme-pill-container d-flex flex-wrap gap-2 mt-4">
                        <div class="scheme-pill">
                            <span class="material-symbols-outlined">analytics</span>
                            <span>1.38 Lakh+ Records Seeding</span>
                        </div>
                        <div class="scheme-pill">
                            <span class="material-symbols-outlined">apartment</span>
                            <span>Urban Development</span>
                        </div>
                        <div class="scheme-pill">
                            <span class="material-symbols-outlined">security</span>
                            <span>Secure Officer Portal</span>
                        </div>
                    </div>
                </div>

                <!-- Simple Left Footer -->
                <div class="left-footer">
                    <p>© 2026 Department of Housing For All, Haryana. Designed by CRID.</p>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Officer Login Form -->
        <div class="mmsay-form-panel d-flex align-items-center justify-content-center">
            <!-- Floating Mobile Branding -->
            <div class="mobile-branding d-none">
                <img src="{{ asset('Haryana_emblem.png') }}" alt="Haryana Emblem" class="emblem-img-mobile">
                <div>
                    <h2 class="dept-title-mobile">Department of Housing For All</h2>
                    <p class="dept-sub-mobile">Government of Haryana</p>
                </div>
            </div>

            <div class="login-card-container">
                <!-- Header -->
                <div class="login-header">
                    <div class="login-badge-icon">
                        <span class="material-symbols-outlined">admin_panel_settings</span>
                    </div>
                    <h3 class="login-title">Officer Login</h3>
                    <p class="login-subtitle">Access authorized departmental functions</p>
                </div>

                <!-- Form -->
                <form id="loginForm" action="{{ route('mmsay.login') }}" method="POST" class="mt-2">
                    @csrf

                    <!-- Input Group: Email -->
                    <div class="input-field-group mb-2">
                        <label class="input-label">Email Address / Mobile</label>
                        <div class="input-container-premium">
                            <span class="material-symbols-outlined input-icon-pre">mail</span>
                            <input type="text" class="form-input-premium" name="email" placeholder="Enter official email or mobile" required>
                        </div>
                    </div>

                    <!-- Input Group: Password -->
                    <div class="input-field-group mb-2">
                        <label class="input-label">Password</label>
                        <div class="input-container-premium">
                            <span class="material-symbols-outlined input-icon-pre">lock</span>
                            <input type="password" class="form-input-premium" name="password" placeholder="Enter account password" required>
                        </div>
                    </div>

                    <!-- Input Group: CAPTCHA -->
                    <div class="input-field-group mb-2">
                        <label class="input-label">Captcha Verification</label>
                        <div class="captcha-row d-flex align-items-center gap-2">
                            <div class="captcha-badge" id="captchaText">{{ $captcha }}</div>
                            <div role="button" tabindex="0" aria-label="Refresh captcha" onclick="refreshCaptcha(this)" class="captcha-spin-btn">
                                <span class="material-symbols-outlined">refresh</span>
                            </div>
                            <div class="captcha-input-wrapper">
                                <input type="text" class="form-input-premium" id="captchaInput" name="captcha" placeholder="Enter captcha" required>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="login-submit-btn d-flex align-items-center justify-content-center gap-2" id="loginBtn">
                        <span id="btnText" class="d-flex align-items-center justify-content-center gap-2">
                            Secure Access Portal
                            <span class="material-symbols-outlined font-bold" style="font-size: 18px;">arrow_forward</span>
                        </span>
                        <span id="btnLoader" style="display: none !important;" class="align-items-center justify-content-center gap-2">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Verifying Credentials...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div id="pageLoader" class="page-loader" style="display:none;">
        <div class="text-center">
            <div class="spinner-border text-success" role="status"></div>
            <div class="mt-3 fw-bold">Please wait...</div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('loginForm');

            if (form) {
                form.addEventListener('submit', function() {

                    document.getElementById('btnText').style.setProperty('display', 'none', 'important');
                    document.getElementById('btnLoader').style.setProperty('display', 'flex', 'important');
                    document.getElementById('loginBtn').disabled = true;
                    document.getElementById('pageLoader').style.display = 'flex';

                });
            }
        });
    </script>
    <script>
        document.body.classList.add('page-dept-login');
    </script>
@endpush
