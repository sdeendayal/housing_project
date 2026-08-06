<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>MMGAY - Department Login</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&amp;display=swap" rel="stylesheet" />
    <!-- Material Symbols Outlined -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#0058bc",
                        "on-surface": "#111c2d",
                        "on-surface-variant": "#414755",
                        "surface": "#f9f9ff",
                        "outline-variant": "#c1c6d7",
                        "surface-container-highest": "#d8e3fb"
                    },
                    "fontFamily": {
                        "body-md": ["Inter"],
                        "headline-xl": ["Inter"],
                        "headline-lg": ["Inter"]
                    },
                    "fontSize": {
                        "body-md": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
                        }],
                        "label-md": ["12px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.05em",
                            "fontWeight": "600"
                        }],
                        "headline-xl": ["30px", {
                            "lineHeight": "38px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .captcha-box {
            background-color: #e7eeff;
            border: 1px solid #adc6ff;
        }

        /* Background pattern from reference Image 4 */
        .login-bg-pattern {
            background-image: radial-gradient(circle, #0562cb22 1.5px, transparent 1.5px);
            background-size: 32px 32px;
        }

        body {
            overflow: hidden;
        }

        /* Department-login inspired dark glass treatment for the visual panel. */
        .dark-login-portals a {
            background: rgba(249, 248, 248, 0.09) !important;
            border-color: rgba(255, 255, 255, 0.16) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }

        .dark-login-portals a:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }

        .dark-login-portals .bg-blue-50 {
            background: rgba(96, 165, 250, 0.16) !important;
            color: #bfdbfe !important;
        }

        .dark-login-portals .text-slate-800 {
            color: #ffffff !important;
        }

        .dark-login-portals .text-slate-500,
        .dark-login-portals .text-slate-400 {
            color: rgba(219, 234, 254, 0.72) !important;
        }

        .dark-login-portals .text-slate-300 {
            color: rgba(219, 234, 254, 0.58) !important;
        }

        .dark-login-portals a:hover .material-symbols-outlined:last-child {
            color: #ffffff !important;
        }
    </style>
</head>

<body class="bg-white font-body-md text-on-surface min-h-screen flex flex-col h-screen max-h-screen">

    <!-- Main Content Canvas: 50/50 Split Screen -->
    <main class="flex-grow flex flex-col md:flex-row overflow-hidden">
        <!-- Left Side: Login Form -->
        <section
            class="w-full md:w-1/2 flex flex-col justify-center items-center relative login-bg-pattern px-6 md:px-12 py-4 overflow-hidden h-full">
            <!-- Subtle wave background at bottom as seen in Image 4 -->
            <div class="absolute bottom-0 left-0 w-full opacity-10 pointer-events-none">
                <svg viewbox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M0,192L48,197.3C96,203,192,213,288,197.3C384,181,480,139,576,138.7C672,139,768,181,864,197.3C960,213,1056,203,1152,186.7C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"
                        fill="#0058bc" fill-opacity="1"></path>
                </svg>
            </div>
            <div class="w-full max-w-md z-10">
                <!-- Branding Header -->
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-[42px] h-[42px] bg-[#0070eb] rounded-[10px] flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <span class="material-symbols-outlined text-white text-[24px]"
                            style="font-variation-settings: 'FILL' 1;">shield_person</span>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-bold text-[#111c2d]">MMGAY Portal Login</h3>
                        <p class="text-[#505f76] text-[12px]">Officer sign in — villagers use Mobile OTP</p>
                    </div>
                </div>
                <!-- Form -->
                <!-- Login Card -->
                <div
                    class="rounded-2xl border border-blue-100/80 bg-white/95 p-5 shadow-[0_18px_50px_rgba(0,88,188,0.13)] backdrop-blur-xl">

                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#0058bc]">
                                Secure Officer Access
                            </p>
                            <p class="mt-0.5 text-[10px] text-slate-400">
                                Enter your registered credentials to continue.
                            </p>
                        </div>

                        <span
                            class="material-symbols-outlined flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[19px] text-[#0058bc]">
                            verified_user
                        </span>
                    </div>

                    <form method="POST" action="{{ route('mmgay.login.submit') }}" class="space-y-3.5">
                        @csrf

                        {{-- Username --}}
                        <div class="space-y-1.5">
                            <label for="email"
                                class="block text-[10px] font-bold uppercase tracking-wider text-[#414755]">
                                Username or Email
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                    person
                                </span>

                                <input id="email" type="text" name="email" value="{{ old('email') }}"
                                    placeholder="Username or Email" autocomplete="username" required
                                    class="h-11 w-full rounded-xl border bg-slate-50/70 pl-10 pr-4 text-xs text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#0058bc] focus:bg-white focus:ring-2 focus:ring-blue-100
                                    @error('email') border-red-400 bg-red-50/40 @else border-slate-200 @enderror">
                            </div>

                            @error('email')
                                <p class="flex items-center gap-1 text-[10px] font-medium text-red-500">
                                    <span class="material-symbols-outlined text-[13px]">error</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1.5">
                            <label for="password"
                                class="block text-[10px] font-bold uppercase tracking-wider text-[#414755]">
                                Password
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                    lock
                                </span>

                                <input id="password" type="password" name="password" placeholder="Password"
                                    autocomplete="current-password" required
                                    class="h-11 w-full rounded-xl border bg-slate-50/70 pl-10 pr-11 text-xs text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#0058bc] focus:bg-white focus:ring-2 focus:ring-blue-100
                                    @error('password') border-red-400 bg-red-50/40 @else border-slate-200 @enderror">

                                <button type="button" id="togglePassword" aria-label="Show or hide password"
                                    class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 transition hover:bg-blue-50 hover:text-[#0058bc]">
                                    <span id="eyeIcon" class="material-symbols-outlined text-[18px]">
                                        visibility
                                    </span>
                                </button>
                            </div>

                            @error('password')
                                <p class="flex items-center gap-1 text-[10px] font-medium text-red-500">
                                    <span class="material-symbols-outlined text-[13px]">error</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Captcha --}}
                        <div class="space-y-1.5">
                            <label for="captchaBox"
                                class="block text-[10px] font-bold uppercase tracking-wider text-[#414755]">
                                Security Verification
                            </label>

                            <div class="grid w-full grid-cols-[minmax(0,1fr)_44px] gap-2">
                                {{-- Captcha --}}
                                <div id="captchaBox"
                                    class="flex h-11 w-full select-none items-center justify-center rounded-xl border border-blue-100
                   bg-[repeating-linear-gradient(135deg,#eff6ff,#eff6ff_8px,#dbeafe_8px,#dbeafe_9px)]
                   px-4 font-mono text-base font-extrabold tracking-[0.28em] text-[#0058bc]">
                                    {{ session('captcha') }}
                                </div>

                                {{-- Refresh --}}
                                <button type="button" id="refreshCaptcha" aria-label="Refresh captcha"
                                    class="flex h-11 w-full items-center justify-center rounded-xl border border-slate-200
                   bg-white text-slate-500 transition
                   hover:border-blue-200 hover:bg-blue-50 hover:text-[#0058bc]">

                                    <span id="refreshIcon" class="material-symbols-outlined text-[18px]">
                                        refresh
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="email"
                                class="block text-[10px] font-bold uppercase tracking-wider text-[#414755]">

                            </label>

                            <div class="relative">
                                <input id="captcha" type="text" name="captcha" placeholder="Enter captcha"
                                    autocomplete="off" required
                                    class="col-span-2 h-11 w-full rounded-xl border bg-slate-50/70 px-3 text-xs text-slate-800 uppercase outline-none transition placeholder:normal-case placeholder:text-slate-400 focus:border-[#0058bc] focus:bg-white focus:ring-2 focus:ring-blue-100 sm:col-span-1
                                    @error('captcha') border-red-400 bg-red-50/40 @else border-slate-200 @enderror">
                            </div>

                            @error('captcha')
                                <p class="flex items-center gap-1 text-[10px] font-medium text-red-500">
                                    <span class="material-symbols-outlined text-[13px]">error</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="login-btn group flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#0058bc] to-[#0878df] text-xs font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-blue-600/25 active:translate-y-0">
                            <span>Secure Login</span>
                            <span
                                class="material-symbols-outlined text-[18px] transition group-hover:translate-x-0.5">arrow_forward</span>
                        </button>
                    </form>

                </div>
            </div>
        </section>
        <!-- Right Side: Visual Canvas (Updated to IMAGE_4) -->
        <section class="hidden md:flex md:w-1/2 relative overflow-hidden bg-[#071a3d]">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img alt="Rural Haryana House" class="w-full h-full object-cover"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBfibej-rHcQ0lm0QeIHkVkL7J1ZV_FLSs4MgxkJg5J7Ssz29lgtfRR0kpKAg1HbxrWnXavXlOSuzmVNlroYiKa6n9baf5_OG74Fso1TFts2fVpAEkrn92QcAkLpcGa0IRf4Iq5_FSe0K_ndwJ7TalA40H53iO_8fPzEMs57-kzLt_8k4sNfGn_rq3uhBTwGQ0gUu7h70O_N3xFFbzE4p9xWMh5QTCbdnM4jGgINXy1mLyqGgnrkr9jYU5Y0gbjpKREvqO6-6nlkNRG" />
                <!-- Department-login inspired navy overlay -->
                <div class="absolute inset-0 bg-[#071a3d]/72"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-[#0b2148]/90 via-[#0d3b76]/68 to-[#0878df]/28">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#06152f]/65 via-transparent to-[#0b2148]/30"></div>
            </div>
            <!-- Content Overlay -->
            <div class="relative z-10 w-full flex flex-col items-center justify-center text-center px-8 py-6">
                <!-- White Circular House Icon -->
                <div
                    class="bg-white w-[92px] h-[92px] rounded-full shadow-2xl flex items-center justify-center mb-5 border-[6px] border-white/40">
                    <span class="material-symbols-outlined text-[#0058bc] text-[56px]"
                        style="font-variation-settings: 'FILL' 1;">holiday_village</span>
                </div>
                <div class="w-full max-w-[760px]">
                    <h2 class="text-white text-[32px] font-extrabold leading-tight mb-3 drop-shadow-sm">Mukhyamantri
                        Gramin Awas
                        Yojana</h2>
                    <div class="h-[3px] w-20 bg-amber-300 mx-auto mb-4 rounded-full opacity-90"></div>
                    <p class="text-white text-[20px] font-bold mb-2 drop-shadow-sm">हरियाणा सरकार का है सपना, सबका घर
                        हो अपना!</p>
                    <p class="text-blue-100/80 text-[15px] italic">
                        (Providing sustainable housing solutions for every rural family in Haryana)
                    </p>

                    {{-- Other portals: current MMGAY Officer Login intentionally excluded --}}
                    <div
                        class="dark-login-portals mt-5 rounded-2xl border border-white/20 bg-[#081d41]/72 p-4 text-left shadow-[0_18px_55px_rgba(2,12,32,0.32)] backdrop-blur-xl">
                        <div class="mb-3 flex items-end justify-between gap-4">
                            <div>
                                <h3 class="text-[15px] font-extrabold text-white">Other Login Portals</h3>
                                <p class="mt-0.5 text-[10px] text-blue-100/70">Choose the portal applicable to your
                                    role.</p>
                            </div>
                            <span
                                class="rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-blue-100">
                                Quick Access
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            {{-- MMSAY --}}
                            <div>
                                <p class="mb-1.5 text-[9px] font-extrabold uppercase tracking-[0.14em] text-amber-300">
                                    MMSAY
                                </p>
                                <div class="space-y-1.5">
                                    <a href="{{ url('mmsay-citizen-login') }}"
                                        class="group flex items-center gap-2 rounded-xl border border-slate-200 bg-white/90 p-2 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-sm">
                                        <span
                                            class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[17px] text-[#0058bc]">person</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-[10px] font-bold text-slate-800">Citizen
                                                Login</span>
                                            <span class="block truncate text-[8px] text-slate-400">MMSAY Portal</span>
                                        </span>
                                        <span
                                            class="material-symbols-outlined text-[15px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#0058bc]">arrow_forward</span>
                                    </a>

                                    <a href="{{ url('physical-possession/department/login') }}"
                                        class="group flex items-center gap-2 rounded-xl border border-slate-200 bg-white/90 p-2 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-sm">
                                        <span
                                            class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[17px] text-[#0058bc]">engineering</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-[10px] font-bold text-slate-800">Site
                                                Engineer</span>
                                            <span class="block truncate text-[8px] text-slate-400">Physical
                                                Possession</span>
                                        </span>
                                        <span
                                            class="material-symbols-outlined text-[15px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#0058bc]">arrow_forward</span>
                                    </a>

                                    <a href="{{ url('mmsay-department-login') }}"
                                        class="group flex items-center gap-2 rounded-xl border border-slate-200 bg-white/90 p-2 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-sm">
                                        <span
                                            class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[17px] text-[#0058bc]">apartment</span>
                                        <span class="min-w-0 flex-1">
                                            <span
                                                class="block truncate text-[10px] font-bold text-slate-800">Department
                                                Login</span>
                                            <span class="block truncate text-[8px] text-slate-400">MMSAY Portal</span>
                                        </span>
                                        <span
                                            class="material-symbols-outlined text-[15px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#0058bc]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>

                            {{-- MMGAY: Officer Login is the current page, so it is not listed --}}
                            <div>
                                <p class="mb-1.5 text-[9px] font-extrabold uppercase tracking-[0.14em] text-amber-300">
                                    MMGAY
                                </p>
                                <div class="space-y-1.5">
                                    <a href="{{ url('mmgav/login') }}"
                                        class="group flex items-center gap-2 rounded-xl border border-slate-200 bg-white/90 p-2 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-sm">
                                        <span
                                            class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[17px] text-[#0058bc]">how_to_reg</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-[10px] font-bold text-slate-800">Applicant
                                                Login</span>
                                            <span class="block truncate text-[8px] text-slate-400">MMGAY Portal</span>
                                        </span>
                                        <span
                                            class="material-symbols-outlined text-[15px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#0058bc]">arrow_forward</span>
                                    </a>

                                    <a href="{{ url('mmgay/bdo/login') }}"
                                        class="group flex items-center gap-2 rounded-xl border border-slate-200 bg-white/90 p-2 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-sm">
                                        <span
                                            class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[17px] text-[#0058bc]">shield_person</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-[10px] font-bold text-slate-800">BDPO
                                                Login</span>
                                            <span class="block truncate text-[8px] text-slate-400">MMGAY Portal</span>
                                        </span>
                                        <span
                                            class="material-symbols-outlined text-[15px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#0058bc]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>

                            {{-- EWS --}}
                            <div>
                                <p class="mb-1.5 text-[9px] font-extrabold uppercase tracking-[0.14em] text-amber-300">
                                    EWS
                                </p>
                                <div class="space-y-1.5">
                                    @foreach ([['ews/citizen/login', 'person', 'Citizen Login'], ['ews/developer/login', 'developer_mode', 'Developer Login'], ['ews/department/login', 'apartment', 'Department Login']] as [$portalUrl, $portalIcon, $portalLabel])
                                        <a href="{{ url($portalUrl) }}"
                                            class="group flex items-center gap-2 rounded-xl border border-slate-200 bg-white/90 p-2 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-sm">
                                            <span
                                                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[17px] text-[#0058bc]">{{ $portalIcon }}</span>
                                            <span class="min-w-0 flex-1">
                                                <span
                                                    class="block truncate text-[10px] font-bold text-slate-800">{{ $portalLabel }}</span>
                                                <span class="block truncate text-[8px] text-slate-400">EWS
                                                    Portal</span>
                                            </span>
                                            <span
                                                class="material-symbols-outlined text-[15px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#0058bc]">arrow_forward</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Password Visibility Toggle
            const eyeBtn = document.querySelector('.password-toggle');
            if (eyeBtn) {
                eyeBtn.addEventListener('click', () => {
                    const input = eyeBtn.parentElement.querySelector('input');
                    const icon = eyeBtn.querySelector('.material-symbols-outlined');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.textContent = 'visibility_off';
                    } else {
                        input.type = 'password';
                        icon.textContent = 'visibility';
                    }
                });
            }

            // Simple login button state
            const loginBtn = document.querySelector('.login-btn');
            if (loginBtn) {
                loginBtn.addEventListener('click', () => {
                    const originalText = loginBtn.textContent;
                    loginBtn.innerHTML =
                        '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>';
                    setTimeout(() => {
                        loginBtn.textContent = originalText;
                    }, 1500);
                });
            }
        });

        const toggle = document.getElementById('togglePassword');

        toggle.addEventListener('click', function() {

            let password = document.getElementById('password');
            let icon = document.getElementById('eyeIcon');

            if (password.type === 'password') {
                password.type = 'text';
                icon.innerHTML = 'visibility_off';
            } else {
                password.type = 'password';
                icon.innerHTML = 'visibility';
            }

        });

        const refreshBtn = document.getElementById('refreshCaptcha');

        refreshBtn.addEventListener('click', function() {

            const icon = document.getElementById('refreshIcon');

            icon.classList.add('animate-spin');

            fetch("{{ route('mmgay.refresh.captcha') }}")
                .then(response => response.json())
                .then(data => {

                    document.getElementById('captchaBox').innerHTML = data.captcha;

                    icon.classList.remove('animate-spin');

                })
                .catch(() => {

                    icon.classList.remove('animate-spin');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to refresh captcha.'
                    });

                });

        });
    </script>
    @if (session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Villager Login',
                    text: '{{ session('info') }}'
                });
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}'
            });
        </script>
    @endif
</body>

</html>
