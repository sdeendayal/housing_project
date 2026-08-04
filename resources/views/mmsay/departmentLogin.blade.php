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
@section('title', 'MMSAY - Department Login')
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
            background: url('{{ asset('images/citizen-login/mmsay_login_bg_v2.png') }}') center center / cover no-repeat;
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
            background: linear-gradient(115deg, rgba(6, 24, 58, 0.94) 0%, rgb(7 38 82 / 39%) 55%, rgb(8 47 96 / 0%) 100%);
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
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.3));
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
            width: 100%;
            max-width: 720px;
            margin-top: -10px;
            transform: translateY(-24px);
        }

        .hero-title {
            font-size: 26px !important;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            margin-bottom: 12px !important;
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
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
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 82% 14%, rgba(96, 165, 250, 0.2), transparent 30%),
                radial-gradient(circle at 12% 88%, rgba(14, 116, 214, 0.12), transparent 34%),
                linear-gradient(145deg, #dceeff 0%, #eef7ff 48%, #cfe7fb 100%);
            padding: 30px !important;
            box-shadow: -12px 0 35px rgba(8, 48, 94, 0.09);
            z-index: 2;
            gap: 0;
        }

        .mmsay-form-panel::before {
            content: '';
            position: absolute;
            width: 290px;
            height: 290px;
            top: -120px;
            right: -105px;
            border-radius: 999px;
            border: 52px solid rgba(255, 255, 255, 0.2);
            pointer-events: none;
        }

        .mmsay-form-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(24, 91, 157, 0.11) 1px, transparent 1px);
            background-size: 24px 24px;
            mask-image: linear-gradient(to bottom right, transparent 15%, #000 80%);
            pointer-events: none;
        }

        .login-card-container {
            width: 100%;
            max-width: 390px;
            flex: 0 1 390px;
            margin: auto;
            position: relative;
            z-index: 2;
            padding: 26px;
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.52);
            box-shadow:
                0 24px 60px rgba(20, 74, 128, 0.16),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Other portal logins */
        .portal-links-panel {
            width: 100%;
            max-width: 250px;
            flex: 0 1 250px;
            padding-left: 22px;
            border-left: 1px solid #e2e8f0;
        }

        .portal-links-heading {
            margin: 0;
            color: #1e293b;
            font-size: 13px !important;
            font-weight: 800;
        }

        .portal-links-subtitle {
            margin: 3px 0 12px;
            color: #94a3b8;
            font-size: 10px !important;
            line-height: 1.4;
        }

        .portal-link-group+.portal-link-group {
            margin-top: 12px;
        }

        .portal-group-label {
            margin: 0 0 6px;
            color: #64748b;
            font-size: 9px !important;
            font-weight: 800;
            letter-spacing: 0.9px;
            text-transform: uppercase;
        }

        .portal-link-list {
            display: grid;
            gap: 6px;
        }

        .portal-login-link {
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 39px;
            padding: 7px 9px;
            color: #334155;
            text-decoration: none !important;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .portal-login-link:hover {
            color: #1d4ed8;
            border-color: #bfdbfe;
            background: #eff6ff;
            transform: translateX(2px);
            box-shadow: 0 5px 12px rgba(15, 23, 42, 0.06);
        }

        .portal-link-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 27px;
            height: 27px;
            flex: 0 0 27px;
            color: #2563eb;
            border-radius: 7px;
            background: #eff6ff;
        }

        .portal-link-icon .material-symbols-outlined {
            font-size: 16px !important;
        }

        .portal-link-copy {
            min-width: 0;
            flex: 1;
        }

        .portal-link-name {
            display: block;
            overflow: hidden;
            color: inherit;
            font-size: 10.5px !important;
            font-weight: 700;
            line-height: 1.25;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .portal-link-scheme {
            display: block;
            margin-top: 1px;
            color: #94a3b8;
            font-size: 8.5px !important;
            font-weight: 500;
        }

        .portal-arrow {
            color: #cbd5e1;
            font-size: 15px !important;
            transition: transform 0.2s ease;
        }

        .portal-login-link:hover .portal-arrow {
            color: #3b82f6;
            transform: translateX(2px);
        }

        /* Left hero portal grid */
        .mmsay-visual-panel .portal-links-panel {
            display: block;
            width: 100%;
            max-width: 720px;
            margin-top: 14px;
            padding: 12px 14px 13px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 14px;
            background: rgba(8, 20, 48, 0.58);
            box-shadow: 0 16px 35px rgba(2, 6, 23, 0.22);
            backdrop-filter: blur(14px);
        }

        .mmsay-visual-panel .portal-links-heading {
            color: #ffffff;
            font-size: 12px !important;
        }

        .mmsay-visual-panel .portal-links-subtitle {
            margin: 2px 0 8px;
            color: #aebdd2;
        }

        .mmsay-visual-panel .portal-link-group+.portal-link-group {
            margin-top: 9px;
        }

        .mmsay-visual-panel .portal-group-label {
            margin-bottom: 5px;
            color: #fbd38d;
        }

        .mmsay-visual-panel .portal-link-list {
            display: grid;
            gap: 7px;
        }

        .mmsay-visual-panel .portal-link-group--mmsay .portal-link-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mmsay-visual-panel .portal-link-group--mmgay .portal-link-list,
        .mmsay-visual-panel .portal-link-group--ews .portal-link-list {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mmsay-visual-panel .portal-login-link {
            min-height: 35px;
            padding: 5px 7px;
            color: #f8fafc;
            border-color: rgba(255, 255, 255, 0.13);
            background: rgba(255, 255, 255, 0.07);
        }

        .mmsay-visual-panel .portal-login-link:hover {
            color: #ffffff;
            border-color: rgba(147, 197, 253, 0.65);
            background: rgba(59, 130, 246, 0.22);
            transform: translateY(-1px);
        }

        .mmsay-visual-panel .portal-link-icon {
            color: #bfdbfe;
            background: rgba(59, 130, 246, 0.2);
        }

        .mmsay-visual-panel .portal-link-scheme {
            color: #94a3b8;
        }

        .mmsay-visual-panel .portal-arrow {
            color: #7f91aa;
        }

        .login-badge-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.55);
            border: 1.5px solid rgba(43, 108, 176, 0.18);
            box-shadow: 0 8px 20px rgba(43, 108, 176, 0.1);
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
            color: #12345b;
            margin: 0 0 3px 0;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 12.5px !important;
            color: #5d7692;
            margin: 0;
        }

        /* Form Controls */
        .input-label {
            font-size: 10px !important;
            font-weight: 700;
            text-transform: uppercase;
            color: #36536f;
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
            color: #7893ad;
            font-size: 16px !important;
        }

        .form-input-premium {
            width: 100%;
            height: 34px !important;
            padding: 6px 12px 6px 36px !important;
            font-size: 12.5px !important;
            font-weight: 500;
            border: 1.5px solid rgba(117, 154, 191, 0.38);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.56);
            color: #173754;
            outline: none !important;
            transition: all 0.2s ease;
        }

        .form-input-premium:focus {
            background-color: rgba(255, 255, 255, 0.88);
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
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.8);
        }

        .captcha-spin-btn {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            border-radius: 6px;
            background: linear-gradient(135deg, #1681d9, #2563b8);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 12px rgba(37, 99, 184, 0.2);
        }

        .captcha-spin-btn span {
            color: #ffffff;
            font-size: 16px !important;
        }

        .captcha-spin-btn:hover {
            background: linear-gradient(135deg, #0f6fbe, #1e4f98);
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
            border-radius: 9px;
            color: #ffffff;
            font-size: 13px !important;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(49, 130, 206, 0.24);
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
                gap: 24px;
            }

            .login-card-container,
            .portal-links-panel {
                width: 100%;
                max-width: 430px;
                flex-basis: auto;
            }

            .portal-links-panel {
                padding-top: 20px;
                padding-left: 0;
                border-top: 1px solid #e2e8f0;
                border-left: 0;
            }

            .portal-link-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
                        <h2 class="dept-title">Housing For All</h2>
                        <p class="dept-sub">Government of Haryana</p>
                    </div>
                </div>

                <!-- Hero Section -->
                <div class="hero-section">
                    <h1 class="hero-title">Mukhyamantri Shehri Awas Yojana <span class="badge-scheme">MMSAY</span></h1>
                    <p class="hero-desc">Building affordable, secure, and sustainable homes for every citizen of Haryana.
                        Access official records, approve registrations, and manage scheme allotments.</p>

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
                    <!-- Other authorized login portals -->
                    <aside class="portal-links-panel" aria-label="Other login portals">
                        <h4 class="portal-links-heading">Other Login Portals</h4>
                        <p class="portal-links-subtitle">Choose the portal applicable to your role.</p>

                        <div class="portal-link-group portal-link-group--mmsay">
                            <p class="portal-group-label">MMSAY</p>
                            <div class="portal-link-list">
                                <a href="{{ url('mmsay-citizen-login') }}" class="portal-login-link">
                                    <span class="portal-link-icon"><span
                                            class="material-symbols-outlined">person</span></span>
                                    <span class="portal-link-copy">
                                        <span class="portal-link-name">Citizen Login</span>
                                        <span class="portal-link-scheme">MMSAY Portal</span>
                                    </span>
                                    <span class="material-symbols-outlined portal-arrow">arrow_forward</span>
                                </a>

                                <a href="{{ url('physical-possession/department/login') }}" class="portal-login-link">
                                    <span class="portal-link-icon"><span
                                            class="material-symbols-outlined">engineering</span></span>
                                    <span class="portal-link-copy">
                                        <span class="portal-link-name">Site Engineer Login</span>
                                        <span class="portal-link-scheme">Physical Possession</span>
                                    </span>
                                    <span class="material-symbols-outlined portal-arrow">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                        <div class="portal-link-group portal-link-group--mmgay">
                            <p class="portal-group-label">MMGAY</p>
                            <div class="portal-link-list">
                                <a href="{{ url('mmgav/login') }}" class="portal-login-link">
                                    <span class="portal-link-icon"><span
                                            class="material-symbols-outlined">how_to_reg</span></span>
                                    <span class="portal-link-copy"><span class="portal-link-name">Applicant
                                            Login</span><span class="portal-link-scheme">MMGAY Portal</span></span>
                                    <span class="material-symbols-outlined portal-arrow">arrow_forward</span>
                                </a>

                                <a href="{{ url('mmgay/login') }}" class="portal-login-link">
                                    <span class="portal-link-icon"><span
                                            class="material-symbols-outlined">badge</span></span>
                                    <span class="portal-link-copy"><span class="portal-link-name">Officer Login</span><span
                                            class="portal-link-scheme">MMGAY Portal</span></span>
                                    <span class="material-symbols-outlined portal-arrow">arrow_forward</span>
                                </a>

                                <a href="{{ url('mmgay/bdo/login') }}" class="portal-login-link">
                                    <span class="portal-link-icon"><span
                                            class="material-symbols-outlined">admin_panel_settings</span></span>
                                    <span class="portal-link-copy"><span class="portal-link-name">BDPO Login</span><span
                                            class="portal-link-scheme">MMGAY Portal</span></span>
                                    <span class="material-symbols-outlined portal-arrow">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                        <div class="portal-link-group portal-link-group--ews">
                            <p class="portal-group-label">EWS</p>
                            <div class="portal-link-list">
                                <a href="{{ url('ews/citizen/login') }}" class="portal-login-link">
                                    <span class="portal-link-icon"><span
                                            class="material-symbols-outlined">person</span></span>
                                    <span class="portal-link-copy"><span class="portal-link-name">Citizen Login</span><span
                                            class="portal-link-scheme">EWS Portal</span></span>
                                    <span class="material-symbols-outlined portal-arrow">arrow_forward</span>
                                </a>

                                <a href="{{ url('ews/developer/login') }}" class="portal-login-link">
                                    <span class="portal-link-icon"><span
                                            class="material-symbols-outlined">developer_mode</span></span>
                                    <span class="portal-link-copy"><span class="portal-link-name">Developer
                                            Login</span><span class="portal-link-scheme">EWS Portal</span></span>
                                    <span class="material-symbols-outlined portal-arrow">arrow_forward</span>
                                </a>

                                <a href="{{ url('ews/department/login') }}" class="portal-login-link">
                                    <span class="portal-link-icon"><span
                                            class="material-symbols-outlined">apartment</span></span>
                                    <span class="portal-link-copy"><span class="portal-link-name">Department
                                            Login</span><span class="portal-link-scheme">EWS Portal</span></span>
                                    <span class="material-symbols-outlined portal-arrow">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </aside>
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
                    <h3 class="login-title">Department Login</h3>
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
                            <input type="text" class="form-input-premium" name="email"
                                placeholder="Enter official email or mobile" required>
                        </div>
                    </div>

                    <!-- Input Group: Password -->
                    <div class="input-field-group mb-2">
                        <label class="input-label">Password</label>
                        <div class="input-container-premium">
                            <span class="material-symbols-outlined input-icon-pre">lock</span>
                            <input type="password" class="form-input-premium" name="password"
                                placeholder="Enter account password" required>
                        </div>
                    </div>

                    <!-- Input Group: CAPTCHA -->
                    <div class="input-field-group mb-2">
                        <label class="input-label">
                            Captcha Verification
                        </label>

                        {{-- Captcha and refresh row --}}
                        <div class="captcha-row d-flex align-items-center gap-2">
                            <div id="captchaText" class="captcha-badge flex-grow-1 w-100">
                                {{ $captcha }}
                            </div>

                            <button type="button" aria-label="Refresh captcha" onclick="refreshCaptcha(this)"
                                class="captcha-spin-btn flex-shrink-0"
                                style="width: 44px !important; min-width: 44px !important; height: 34px !important;">

                                <span class="material-symbols-outlined">
                                    refresh
                                </span>
                            </button>
                        </div>

                        {{-- Captcha input on separate row --}}
                        <div class="captcha-input-wrapper mt-2 w-100">
                            <input type="text" class="form-input-premium" id="captchaInput" name="captcha"
                                placeholder="Enter captcha" autocomplete="off" required>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="login-submit-btn d-flex align-items-center justify-content-center gap-2"
                        id="loginBtn">
                        <span id="btnText" class="d-flex align-items-center justify-content-center gap-2">
                            Secure Access Portal
                            <span class="material-symbols-outlined font-bold"
                                style="font-size: 18px;">arrow_forward</span>
                        </span>
                        <span id="btnLoader" style="display: none !important;"
                            class="align-items-center justify-content-center gap-2">
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
