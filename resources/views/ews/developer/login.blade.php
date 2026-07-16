<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-[#f3f4f6]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer Portal - Secure Access</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        .code-font {
            font-family: 'Fira Code', monospace;
        }
        .dev-glow {
            box-shadow: 0 10px 30px -10px rgba(14, 165, 233, 0.15);
        }
        .neon-border {
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
        .neon-border:focus-within {
            border-color: rgba(14, 165, 233, 0.7);
            box-shadow: 0 0 8px rgba(14, 165, 233, 0.2);
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-2 sm:p-4 overflow-hidden relative bg-gradient-to-tr from-slate-950 via-[#0f172a] to-slate-900">
    
    <!-- Tech grid background pattern -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:4rem_4rem] opacity-25 pointer-events-none"></div>

    <!-- Soft colored ambient background glows -->
    <div class="absolute top-1/4 left-1/4 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-650/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Container (Restricted to 85vh to prevent vertical scroll) -->
    <div class="w-full max-w-5xl h-full max-h-[85vh] bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl overflow-hidden flex flex-col md:flex-row shadow-2xl dev-glow z-10">
        
        <!-- Left Side: Generated Developer Banner (5/12 width) -->
        <div class="hidden md:flex md:w-5/12 h-full flex-col justify-between p-7 text-slate-350 relative overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[8000ms] hover:scale-110" 
                 style="background-image: url('{{ asset('developer_login_banner.png') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/85 to-transparent"></div>
            
            <!-- Branding Header -->
            <div class="relative z-10 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-sky-500/20 border border-sky-500/40 backdrop-blur-md flex items-center justify-center shadow-lg">
                    <i class="bi bi-terminal-fill text-sky-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black tracking-wider text-sky-400 uppercase">EWS Dev Hub</h4>
                    <div class="text-[8px] text-slate-400 tracking-widest font-extrabold uppercase font-mono">SECURED CONSOLE</div>
                </div>
            </div>

            <!-- Central Content Display -->
            <div class="relative z-10 space-y-3 my-auto">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-sky-500/20 border border-sky-500/30 text-[8px] font-extrabold text-sky-400 tracking-wider uppercase font-mono backdrop-blur-md">
                    <i class="bi bi-code-slash"></i> SANDBOX ENVIRONMENT
                </span>
                <h2 class="text-xl font-black leading-tight tracking-tight text-white uppercase">
                    EWS ALLOTMENT<br>SIMULATOR PORTAL
                </h2>
                <p class="text-slate-300 text-[10px] font-light leading-relaxed max-w-xs">
                    Run database diagnostics, sync PPP registry records, and verify physical possession triggers in simulated dev space.
                </p>
            </div>

            <!-- Left Footer guidelines -->
            <div class="relative z-10 flex items-center justify-between text-[8px] text-slate-400 border-t border-slate-800/80 pt-3">
                <span>Housing For All, Haryana.</span>
                <span class="font-mono text-sky-400">v2.4-stable</span>
            </div>
        </div>

        <!-- Right Side: Developer Login Form (7/12 width) -->
        <div class="w-full md:w-7/12 h-full flex flex-col justify-between p-6 sm:p-8 bg-slate-900/45 backdrop-blur-md relative overflow-y-auto custom-scroll">
            
            <!-- Mobile Header logo -->
            <div class="flex md:hidden items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded bg-sky-500/10 border border-sky-500/30 flex items-center justify-center">
                        <i class="bi bi-terminal-fill text-sky-400 text-xs"></i>
                    </div>
                    <span class="font-black text-slate-200 text-xs tracking-wider">EWS DEV AUTH</span>
                </div>
                <span class="text-[8px] text-sky-400 bg-sky-400/10 px-2 py-0.5 rounded border border-sky-400/20 font-bold font-mono">DEV GATEWAY</span>
            </div>

            <!-- Spacer -->
            <div class="hidden md:block h-1"></div>

            <!-- Developer Form Container (Centered & Clean) -->
            <div class="w-full max-w-sm mx-auto space-y-4 my-auto">
                <div class="text-center md:text-left space-y-1">
                    <h3 class="text-base font-black tracking-tight text-white uppercase">Developer Authorization</h3>
                    <p class="text-slate-405 text-[10px] font-light">Verify system keys to access developer sandbox dashboard.</p>
                </div>

                @if(session('error'))
                    <div class="bg-red-500/10 border border-red-500/25 text-red-400 p-2.5 rounded-lg text-xs flex items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-xs mt-0.5 shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 p-2.5 rounded-lg text-xs flex items-start gap-2">
                        <i class="bi bi-check-circle-fill text-xs mt-0.5 shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('ews.developer.login.send-otp') }}" class="space-y-3" id="devLoginForm">
                    @csrf

                    <!-- Mobile Number -->
                    <div class="space-y-1">
                        <label for="mobile" class="block text-[9px] font-black uppercase text-slate-400 tracking-wider">Registered Dev Mobile</label>
                        <div class="flex rounded-lg overflow-hidden neon-border border bg-slate-950 transition-all">
                            <span class="flex items-center justify-center px-2.5 text-slate-500 border-r border-slate-800 text-[11px] font-mono">+91</span>
                            <input type="text" id="mobile" name="mobile" maxlength="10" placeholder="9999999999" value="{{ old('mobile') }}"
                                class="w-full bg-transparent border-0 px-2.5 py-1.5 text-xs text-white focus:ring-0 focus:outline-none placeholder-slate-700 font-mono" required>
                        </div>
                    </div>

                    <!-- Captcha Grid Layout (Compact) -->
                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-1">
                            <label for="captcha" class="block text-[9px] font-black uppercase text-slate-400 tracking-wider">Captcha Code</label>
                            <input type="text" id="captcha" name="captcha" placeholder="Enter code" autocomplete="off"
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-white focus:border-sky-500 focus:ring-0 focus:outline-none placeholder-slate-650 font-mono text-center" required>
                        </div>
                        <div class="space-y-1 flex flex-col justify-end">
                            <div class="bg-slate-950/70 border border-slate-800 rounded-lg h-8 flex items-center justify-center font-mono font-bold tracking-widest text-sky-400 relative overflow-hidden select-none border-dashed border-sky-500/20 text-xs">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-sky-500/5 to-transparent animate-pulse"></div>
                                <span class="tracking-widest">{{ $captcha }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Authorization Button -->
                    <button type="submit"
                        class="w-full py-2 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-750 text-slate-950 font-black uppercase tracking-wider rounded-lg text-[10px] shadow-lg shadow-sky-500/10 hover:shadow-sky-500/20 transition-all flex items-center justify-center gap-1.5">
                        <span>Send Security OTP</span>
                        <i class="bi bi-shield-lock-fill text-[11px]"></i>
                    </button>
                </form>
            </div>

            <!-- Mobile/Desktop Footer guidelines -->
            <div class="flex items-center justify-between text-[8px] text-slate-500 pt-3 border-t border-slate-800">
                <span>© 2026 Housing For All, Haryana.</span>
                <span class="font-mono text-sky-500/50">ENV: LOCAL</span>
            </div>

            <div class="hidden md:block h-1"></div>
        </div>
    </div>

</body>
</html>
