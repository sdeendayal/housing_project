<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>MMGAY BDO Portal Login</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&amp;display=swap" rel="stylesheet" />
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
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

        .login-bg-pattern {
            background-image: radial-gradient(circle, #0058bc10 1.5px, transparent 1.5px);
            background-size: 32px 32px;
        }

        body {
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-white font-body-md text-on-surface min-h-screen flex flex-col h-screen max-h-screen">

    <main class="flex-grow flex flex-col md:flex-row overflow-hidden">
        <!-- Left Side: Login Form -->
        <section class="w-full md:w-1/2 flex flex-col justify-center items-center relative login-bg-pattern px-12 py-4">
            <div class="absolute bottom-0 left-0 w-full opacity-10 pointer-events-none">
                <svg viewbox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0,192L48,197.3C96,203,192,213,288,197.3C384,181,480,139,576,138.7C672,139,768,181,864,197.3C960,213,1056,203,1152,186.7C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" fill="#0058bc" fill-opacity="1"></path>
                </svg>
            </div>
            <div class="w-full max-w-md z-10">
                <!-- Login Card -->
                <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_15px_45px_rgba(0,88,188,0.12)] border border-slate-100 p-6">
                    <!-- Branding Header (Inside Card) -->
                    <div class="flex items-center gap-3 mb-5 pb-5 border-b border-slate-100">
                        <div class="w-[44px] h-[44px] bg-[#0070eb] rounded-xl flex items-center justify-center shadow-md shadow-blue-500/25 flex-shrink-0">
                            <span class="material-symbols-outlined text-white text-[24px]" style="font-variation-settings: 'FILL' 1;">gavel</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#111c2d]">MMGAY BDPO Login</h3>
                            <p class="text-xs text-slate-500 font-medium">Block Development & Panchayat Officer</p>
                        </div>
                    </div>

                    @if (session('error'))
                        <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-xs font-semibold">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="bg-green-50 text-green-600 p-3 rounded-xl mb-4 text-xs font-semibold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('mmgay.bdo.login.submit') }}" class="space-y-4">
                        @csrf

                        {{-- Username/Email --}}
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-[#414755] uppercase tracking-wider">
                                BDPO Email Address
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">
                                    mail
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="bdo@mmgay.com" class="w-full h-[40px] pl-10 pr-3 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-[#0058bc]/20 focus:border-[#0058bc] focus:outline-none transition-all @error('email') border-red-500 @enderror" required>
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-[#414755] uppercase tracking-wider">
                                Password
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">
                                    lock
                                </span>
                                <input id="password" type="password" name="password" placeholder="••••••••" class="w-full h-[40px] pl-10 pr-10 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-[#0058bc]/20 focus:border-[#0058bc] focus:outline-none transition-all @error('password') border-red-500 @enderror" required>
                                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <span id="eyeIcon" class="material-symbols-outlined text-[18px]">
                                        visibility
                                    </span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Captcha --}}
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-[#414755]">
                                Security Verification
                            </label>
                            <div class="flex gap-2">
                                <div id="captchaBox" class="flex items-center justify-center w-[90px] h-[40px] rounded-lg bg-blue-50/50 border border-blue-100 font-bold tracking-widest text-sm text-[#0058bc]">
                                    {{ $captcha }}
                                </div>
                                <button type="button" id="refreshCaptcha" class="w-[40px] h-[40px] rounded-lg border border-slate-200 hover:bg-blue-50/50 hover:border-blue-200 transition-all flex items-center justify-center">
                                    <span id="refreshIcon" class="material-symbols-outlined text-[18px] text-slate-500">
                                        refresh
                                    </span>
                                </button>
                                <input type="text" name="captcha" placeholder="Enter Captcha" class="flex-1 min-w-0 h-[40px] px-3 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-[#0058bc]/20 focus:border-[#0058bc] focus:outline-none transition-all @error('captcha') border-red-500 @enderror" required>
                            </div>
                            @error('captcha')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="login-btn w-full h-[40px] bg-[#0058bc] text-white rounded-lg text-xs font-semibold hover:bg-blue-700 active:scale-[0.98] transition-all shadow-sm shadow-blue-500/10 mt-2">
                            Login
                        </button>
                    </form>

                    <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                        <p class="text-[11px] text-slate-400 mb-1.5">Are you a regular officer?</p>
                        <a href="{{ route('mmgay.login') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#0058bc] hover:underline">
                            <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                            Go to Officer Login
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Right Side: Visual Canvas -->
        <section class="hidden md:flex md:w-1/2 relative overflow-hidden bg-white">
            <div class="absolute inset-0 z-0">
                <img alt="Rural Haryana House" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBfibej-rHcQ0lm0QeIHkVkL7J1ZV_FLSs4MgxkJg5J7Ssz29lgtfRR0kpKAg1HbxrWnXavXlOSuzmVNlroYiKa6n9baf5_OG74Fso1TFts2fVpAEkrn92QcAkLpcGa0IRf4Iq5_FSe0K_ndwJ7TalA40H53iO_8fPzEMs57-kzLt_8k4sNfGn_rq3uhBTwGQ0gUu7h70O_N3xFFbzE4p9xWMh5QTCbdnM4jGgINXy1mLyqGgnrkr9jYU5Y0gbjpKREvqO6-6nlkNRG" />
                <div class="absolute inset-0 bg-[#0058bc]/20 mix-blend-multiply"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/40 to-transparent"></div>
            </div>
            <div class="relative z-10 w-full flex flex-col items-center justify-center text-center px-10">
                <div class="bg-white w-[110px] h-[110px] rounded-full shadow-2xl flex items-center justify-center mb-8 border-[6px] border-white/40">
                    <span class="material-symbols-outlined text-[#0058bc] text-[56px]" style="font-variation-settings: 'FILL' 1;">holiday_village</span>
                </div>
                <div class="max-w-md">
                    <h2 class="text-[#0058bc] text-[34px] font-extrabold leading-tight mb-4">Mukhyamantri Gramin Awas Yojana</h2>
                    <div class="h-[3px] w-20 bg-[#0058bc] mx-auto mb-6 rounded-full opacity-60"></div>
                    <p class="text-[#0058bc] text-[20px] font-bold mb-2">हरियाणा सरकार का है सपना, सबका घर हो अपना!</p>
                </div>
            </div>
        </section>
    </main>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
            });
        }

        const refreshCaptcha = document.querySelector('#refreshCaptcha');
        const captchaBox = document.querySelector('#captchaBox');

        if (refreshCaptcha) {
            refreshCaptcha.addEventListener('click', function() {
                fetch('{{ route("mmgay.bdo.refresh.captcha") }}')
                    .then(response => response.json())
                    .then(data => {
                        captchaBox.textContent = data.captcha;
                    });
            });
        }
    </script>
</body>

</html>
