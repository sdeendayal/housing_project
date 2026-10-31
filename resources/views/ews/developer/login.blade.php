<!DOCTYPE html>
<html lang="en" class="h-full bg-[#090d16] text-slate-100 overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer Portal - Secure Access & Registration | Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        html, body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden !important;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
        }
        .code-font {
            font-family: 'Fira Code', monospace;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .luxury-shadow {
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }
        .orbital-spinner {
            border: 3px solid rgba(99, 102, 241, 0.15);
            border-top: 3px solid #6366f1;
            border-right: 3px solid #10b981;
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.55, 0.25, 0.25, 0.7) infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="h-screen max-h-screen w-screen overflow-hidden flex items-center justify-center p-2 sm:p-4 relative bg-[#090d16] select-none">
    
    <!-- Background Accents -->
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(99,102,241,0.22),rgba(255,255,255,0))] pointer-events-none"></div>
    <div class="fixed inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] opacity-20 pointer-events-none"></div>
    <div class="fixed top-1/4 left-8 w-80 h-80 bg-indigo-600/15 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="fixed bottom-8 right-8 w-80 h-80 bg-emerald-600/15 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- Main Window Container (Strictly Fitted to Viewport) -->
    <div class="w-full max-w-5xl h-full max-h-[92vh] rounded-[24px] overflow-hidden flex flex-col lg:flex-row luxury-shadow border border-slate-700/60 z-10 relative">
        
        <!-- LEFT: Architectural Visual Showcase (5/12) -->
        <div class="hidden lg:flex lg:w-5/12 h-full flex-col justify-between p-6 xl:p-7 text-white relative overflow-hidden bg-slate-950">
            <!-- Background Hero Rendering with Cinematic Overlay -->
            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[12000ms] hover:scale-105 opacity-75" 
                 style="background-image: url('{{ asset('developer_portal_hero.jpg') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/45"></div>
            
            <!-- Top Branding Header -->
            <div class="relative z-10 flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/10 border border-white/20 backdrop-blur-xl flex items-center justify-center text-emerald-400 shadow-md">
                    <i class="bi bi-buildings-fill text-base"></i>
                </div>
                <div>
                    <div class="text-[9px] tracking-[0.2em] font-extrabold uppercase text-emerald-400 font-mono">GOVERNMENT OF HARYANA</div>
                    <h4 class="text-xs font-black tracking-wide text-white uppercase brand-font">Housing For All Department</h4>
                </div>
            </div>

            <!-- Central Narrative Card -->
            <div class="relative z-10 space-y-3 my-auto">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-[9px] font-bold text-emerald-300 tracking-wider uppercase font-mono backdrop-blur-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>OFFICIAL DEVELOPER CONSOLE</span>
                </div>

                <h2 class="text-2xl xl:text-3xl font-black leading-tight tracking-tight text-white uppercase brand-font">
                    Builder & Developer<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-teal-200 to-indigo-300">Registry Portal</span>
                </h2>

                <p class="text-slate-300 text-[11px] font-normal leading-relaxed">
                    Integrated digital platform enabling real estate developers to register EWS flat inventories, allocate units, and maintain authenticated possession audit logs across Haryana.
                </p>

                <!-- Value Pillars -->
                <div class="space-y-2 pt-1 text-[11px] text-slate-200">
                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <i class="bi bi-shield-check text-emerald-400 text-sm shrink-0"></i>
                        <span class="font-medium text-slate-200">Instant Mobile OTP Enrollment & Dashboard Access</span>
                    </div>
                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <i class="bi bi-diagram-3-fill text-indigo-400 text-sm shrink-0"></i>
                        <span class="font-medium text-slate-200">End-to-End Unit Registry & Handover Audit Trail</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Compliance Tag -->
            <div class="relative z-10 flex items-center justify-between text-[9px] text-slate-400 border-t border-white/10 pt-2.5 font-mono">
                <span class="flex items-center gap-1.5 text-emerald-400 font-bold">
                    <i class="bi bi-lock-fill"></i> 256-Bit SSL Secured
                </span>
                <span>RERA & HSVP Compliant</span>
            </div>
        </div>

        <!-- RIGHT: Interactive Form Console (7/12) -->
        <div class="w-full lg:w-7/12 h-full glass-panel p-5 sm:p-7 xl:p-8 flex flex-col justify-between text-slate-800 relative overflow-hidden">
            
            <!-- Mobile Brand Bar -->
            <div class="flex lg:hidden items-center justify-between border-b border-slate-200 pb-2 mb-2 shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                        <i class="bi bi-buildings-fill"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-black text-slate-900 tracking-wider">DEVELOPER CONSOLE</div>
                        <div class="text-[8px] text-slate-500 font-mono">HOUSING FOR ALL HARYANA</div>
                    </div>
                </div>
                <span class="text-[8px] text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full font-bold font-mono">OFFICIAL</span>
            </div>

            <!-- Main Content Area -->
            <div class="w-full max-w-md mx-auto my-auto space-y-3.5">
                
                <!-- Section Header -->
                <div class="space-y-0.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black tracking-tight text-slate-900 brand-font">
                            Developer Gateway
                        </h3>
                        <span class="text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wider">
                            Official
                        </span>
                    </div>
                    <p class="text-slate-500 text-[11px]">
                        Sign in with your registered mobile or register a new development entity.
                    </p>
                </div>

                <!-- Modern Animated Sliding Tab Switcher -->
                <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 border border-slate-200 text-xs font-bold select-none shadow-inner">
                    <button type="button" id="tabBtnLogin" onclick="switchTab('login')"
                        class="flex-1 py-1.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all duration-200 shadow-sm bg-white text-indigo-700 font-extrabold cursor-pointer">
                        <i class="bi bi-box-arrow-in-right text-xs"></i>
                        <span>Sign In</span>
                    </button>
                    <button type="button" id="tabBtnRegister" onclick="switchTab('register')"
                        class="flex-1 py-1.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all duration-200 text-slate-500 hover:text-slate-900 font-semibold cursor-pointer">
                        <i class="bi bi-person-plus-fill text-xs"></i>
                        <span>New Registration</span>
                        <span class="text-[8px] px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 font-extrabold uppercase tracking-wide">Enroll</span>
                    </button>
                </div>

                <!-- Alerts -->
                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-2.5 rounded-xl text-[11px] flex items-start gap-2 shadow-sm">
                        <i class="bi bi-exclamation-circle-fill text-rose-600 text-sm shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <span class="font-medium">{{ session('error') }}</span>
                            @if(session('not_registered'))
                                <div class="mt-1 pt-1 border-t border-rose-200/70">
                                    <button type="button" onclick="switchTab('register', '{{ session('unregistered_mobile') }}')"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-900 hover:text-rose-950 underline decoration-rose-400 cursor-pointer">
                                        <i class="bi bi-arrow-right-circle-fill text-rose-600"></i> Register as a New Developer with this mobile
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-2.5 rounded-xl text-[11px] flex items-start gap-2 shadow-sm">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-sm shrink-0 mt-0.5"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- ============================================== -->
                <!-- TAB 1: SIGN IN (LOGIN) FORM                    -->
                <!-- ============================================== -->
                <div id="tabContentLogin" class="space-y-3">
                    <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-2.5 text-xs text-indigo-950 flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0">
                            <i class="bi bi-phone-vibrate text-xs"></i>
                        </div>
                        <div class="text-[11px] leading-tight">
                            Enter your registered <strong>10-digit mobile</strong> to receive an instant authentication OTP.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('ews.developer.login.send-otp') }}" class="space-y-3" id="devLoginForm" onsubmit="showThemeLoader('Authenticating developer credentials & generating OTP...')">
                        @csrf

                        <!-- Mobile Number Input -->
                        <div class="space-y-1">
                            <label for="login_mobile" class="block text-[10px] font-bold uppercase text-slate-700 tracking-wider">
                                Registered Mobile Number <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex rounded-xl overflow-hidden border border-slate-300 bg-white focus-within:border-indigo-600 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all shadow-sm">
                                <span class="flex items-center justify-center px-3 text-slate-600 border-r border-slate-200 text-xs font-mono font-bold bg-slate-50">+91</span>
                                <input type="text" id="login_mobile" name="mobile" maxlength="10" placeholder="Enter 10-digit mobile number" value="{{ old('mobile') }}"
                                    class="w-full bg-transparent border-0 px-3 py-2 text-xs text-slate-900 focus:ring-0 focus:outline-none placeholder-slate-400 font-mono font-bold tracking-wide" required autofocus>
                            </div>
                        </div>

                        <!-- Captcha Grid Layout -->
                        <div class="grid grid-cols-2 gap-2.5 items-end">
                            <div class="space-y-1">
                                <label for="login_captcha" class="block text-[10px] font-bold uppercase text-slate-700 tracking-wider">
                                    Captcha Verification <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="login_captcha" name="captcha" placeholder="Enter code" autocomplete="off"
                                    class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none rounded-xl px-3 py-2 text-xs text-slate-900 placeholder-slate-400 font-mono text-center font-black tracking-widest shadow-sm" required>
                            </div>
                            <div class="h-[36px] rounded-xl bg-gradient-to-r from-slate-100 to-indigo-50/70 border border-slate-250 flex items-center justify-center select-none shadow-inner border-dashed border-indigo-400/50">
                                <span class="font-mono font-black text-sm text-indigo-700 tracking-[0.25em]">{{ $captcha }}</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="loginSubmitBtn"
                            class="w-full mt-1 bg-gradient-to-r from-indigo-600 via-indigo-700 to-slate-900 hover:from-indigo-700 hover:via-indigo-800 hover:to-black text-white font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-wider group cursor-pointer">
                            <span>Request Login OTP</span>
                            <i class="bi bi-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <!-- Switch to Register Helper -->
                    <div class="text-center pt-2 border-t border-slate-100">
                        <p class="text-[11px] text-slate-500">
                            Don't have a registered developer profile? 
                            <button type="button" onclick="switchTab('register')" class="font-bold text-indigo-600 hover:text-indigo-800 underline decoration-indigo-300 ml-1 cursor-pointer">
                                Register New Entity
                            </button>
                        </p>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- TAB 2: NEW DEVELOPER REGISTRATION FORM         -->
                <!-- ============================================== -->
                <div id="tabContentRegister" class="hidden space-y-3">
                    <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-xl p-2.5 text-xs text-emerald-950 flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                            <i class="bi bi-patch-check-fill text-xs"></i>
                        </div>
                        <div class="text-[11px] leading-tight">
                            <strong>Instant Self-Registration:</strong> Provide your company credentials, verify via SMS OTP, and immediately launch your Developer Registry Console.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('ews.developer.register.send-otp') }}" class="space-y-2.5" id="devRegisterForm" onsubmit="showThemeLoader('Registering developer credentials & sending OTP code...')">
                        @csrf

                        <!-- Developer / Entity Name (Full width) -->
                        <div class="space-y-1">
                            <label for="reg_name" class="block text-[10px] font-bold uppercase text-slate-700 tracking-wider truncate">
                                Developer / Entity Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex rounded-xl overflow-hidden border border-slate-300 bg-white focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all shadow-sm">
                                <span class="flex items-center justify-center px-2.5 text-slate-400 bg-slate-50 border-r border-slate-200">
                                    <i class="bi bi-building text-xs"></i>
                                </span>
                                <input type="text" id="reg_name" name="name" placeholder="Company / Builder Name" value="{{ old('name') }}"
                                    class="w-full bg-transparent border-0 px-2.5 py-1.5 text-xs text-slate-900 focus:ring-0 focus:outline-none placeholder-slate-400 font-medium" required>
                            </div>
                        </div>

                        <!-- Row: Email & Mobile in 2 columns -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Official Work Email -->
                            <div class="space-y-1">
                                <label for="reg_email" class="block text-[10px] font-bold uppercase text-slate-700 tracking-wider truncate">
                                    Official Email <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex rounded-xl overflow-hidden border border-slate-300 bg-white focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all shadow-sm">
                                    <span class="flex items-center justify-center px-2.5 text-slate-400 bg-slate-50 border-r border-slate-200">
                                        <i class="bi bi-envelope-at text-xs"></i>
                                    </span>
                                    <input type="email" id="reg_email" name="email" placeholder="official@company.com" value="{{ old('email') }}"
                                        class="w-full bg-transparent border-0 px-2.5 py-1.5 text-xs text-slate-900 focus:ring-0 focus:outline-none placeholder-slate-400 font-medium" required>
                                </div>
                            </div>

                            <!-- Mobile Number -->
                            <div class="space-y-1">
                                <label for="reg_mobile" class="block text-[10px] font-bold uppercase text-slate-700 tracking-wider truncate">
                                    Mobile (For OTP) <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex rounded-xl overflow-hidden border border-slate-300 bg-white focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all shadow-sm">
                                    <span class="flex items-center justify-center px-2.5 text-slate-600 border-r border-slate-200 text-xs font-mono font-bold bg-slate-50">+91</span>
                                    <input type="text" id="reg_mobile" name="mobile" maxlength="10" placeholder="10-digit mobile" value="{{ old('mobile') }}"
                                        class="w-full bg-transparent border-0 px-2.5 py-1.5 text-xs text-slate-900 focus:ring-0 focus:outline-none placeholder-slate-400 font-mono font-bold" required>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Captcha -->
                        <div class="grid grid-cols-2 gap-2.5 items-end">
                            <div class="space-y-1">
                                <label for="reg_captcha" class="block text-[10px] font-bold uppercase text-slate-700 tracking-wider">
                                    Captcha <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="reg_captcha" name="captcha" placeholder="Enter code" autocomplete="off"
                                    class="w-full bg-white border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none rounded-xl px-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 font-mono text-center font-black tracking-widest shadow-sm" required>
                            </div>
                            <div class="h-[34px] rounded-xl bg-gradient-to-r from-slate-100 to-emerald-50/70 border border-slate-250 flex items-center justify-center select-none shadow-inner border-dashed border-emerald-400/50">
                                <span class="font-mono font-black text-xs text-emerald-700 tracking-[0.25em]">{{ $captcha }}</span>
                            </div>
                        </div>

                        <!-- Registration Button -->
                        <button type="submit" id="regSubmitBtn"
                            class="w-full mt-1 bg-gradient-to-r from-emerald-600 via-teal-600 to-slate-900 hover:from-emerald-700 hover:via-teal-700 hover:to-black text-white font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-wider group cursor-pointer">
                            <i class="bi bi-shield-lock-fill text-xs"></i>
                            <span>Send Verification OTP & Continue</span>
                            <i class="bi bi-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <!-- Switch to Login Helper -->
                    <div class="text-center pt-2 border-t border-slate-100">
                        <p class="text-[11px] text-slate-500">
                            Already registered with the department? 
                            <button type="button" onclick="switchTab('login')" class="font-bold text-indigo-600 hover:text-indigo-800 underline decoration-indigo-300 ml-1 cursor-pointer">
                                Sign In here
                            </button>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Bottom Disclaimer -->
            <div class="text-center pt-3 border-t border-slate-100 text-[10px] text-slate-400 flex items-center justify-between shrink-0">
                <span>Housing For All Department • Haryana</span>
                <a href="{{ route('home') }}" class="text-slate-600 hover:text-indigo-600 font-semibold inline-flex items-center gap-1 transition">
                    <span>Main Portal</span>
                    <i class="bi bi-box-arrow-up-right text-[9px]"></i>
                </a>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- HIGH-TECH THEME LOADER MODAL                                   -->
    <!-- ============================================================== -->
    <div id="themeLoader" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 hidden flex-col items-center justify-center transition-all duration-300">
        <div class="relative flex flex-col items-center max-w-sm text-center p-7 rounded-3xl bg-slate-900/95 border border-slate-700/60 shadow-2xl">
            <!-- Orbital Spinner Container -->
            <div class="relative w-16 h-16 mb-5">
                <div class="orbital-spinner w-16 h-16"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-500 to-emerald-400 flex items-center justify-center text-white shadow-lg">
                        <i class="bi bi-shield-lock-fill text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- Status Header -->
            <h4 class="text-base font-black text-white uppercase brand-font tracking-wide mb-1" id="loaderTitle">
                Authenticating Session
            </h4>
            <p class="text-[11px] text-slate-400 font-normal leading-relaxed" id="loaderSubtitle">
                Generating 256-bit encrypted security token and verifying gateway handshake...
            </p>

            <div class="w-full bg-slate-800 h-1 rounded-full mt-5 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 via-emerald-400 to-indigo-500 h-full rounded-full w-2/3 animate-[spin_2s_linear_infinite]" style="animation: shimmer 1.5s infinite;"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
    </style>

    <!-- Interactive Scripts -->
    <script>
        function switchTab(tab, prefillMobile = null) {
            const loginBtn = document.getElementById('tabBtnLogin');
            const registerBtn = document.getElementById('tabBtnRegister');
            const loginContent = document.getElementById('tabContentLogin');
            const registerContent = document.getElementById('tabContentRegister');

            if (tab === 'register') {
                loginContent.classList.add('hidden');
                registerContent.classList.remove('hidden');

                loginBtn.className = "flex-1 py-1.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all duration-200 text-slate-500 hover:text-slate-900 font-semibold cursor-pointer";
                registerBtn.className = "flex-1 py-1.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all duration-200 shadow-sm bg-white text-emerald-700 font-extrabold cursor-pointer";

                if (prefillMobile) {
                    const regMobileInput = document.getElementById('reg_mobile');
                    if (regMobileInput) {
                        regMobileInput.value = prefillMobile;
                    }
                }
                setTimeout(() => document.getElementById('reg_name')?.focus(), 30);
            } else {
                registerContent.classList.add('hidden');
                loginContent.classList.remove('hidden');

                registerBtn.className = "flex-1 py-1.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all duration-200 text-slate-500 hover:text-slate-900 font-semibold cursor-pointer";
                loginBtn.className = "flex-1 py-1.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all duration-200 shadow-sm bg-white text-indigo-700 font-extrabold cursor-pointer";

                setTimeout(() => document.getElementById('login_mobile')?.focus(), 30);
            }
        }

        function showThemeLoader(subtitleText = null) {
            const loader = document.getElementById('themeLoader');
            if (loader) {
                if (subtitleText) {
                    const sub = document.getElementById('loaderSubtitle');
                    if (sub) sub.innerText = subtitleText;
                }
                loader.classList.remove('hidden');
                loader.classList.add('flex');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initialTab = "{{ $activeTab ?? 'login' }}";
            switchTab(initialTab);
        });
    </script>
</body>
</html>
