<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-955 text-[#f3f4f6]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer Portal - OTP Verification</title>
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
            box-shadow: 0 10px 30px -10px rgba(139, 92, 246, 0.15);
        }
        .neon-border {
            border-color: rgba(139, 92, 246, 0.2);
        }
        .neon-border:focus-within {
            border-color: rgba(139, 92, 246, 0.7);
            box-shadow: 0 0 8px rgba(139, 92, 246, 0.2);
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-2 sm:p-4 overflow-hidden relative bg-gradient-to-tr from-slate-950 via-[#0f172a] to-slate-900">
    
    <!-- Tech grid background pattern -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:4rem_4rem] opacity-25 pointer-events-none"></div>

    <!-- Soft colored ambient background glows -->
    <div class="absolute top-1/4 left-1/4 w-80 h-80 bg-violet-550/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Container (Restricted to 85vh to prevent vertical scroll) -->
    <div class="w-full max-w-5xl h-full max-h-[85vh] bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-2xl overflow-hidden flex flex-col md:flex-row shadow-2xl dev-glow z-10">
        
        <!-- Left Side: Generated Developer Banner (5/12 width) -->
        <div class="hidden md:flex md:w-5/12 h-full flex-col justify-between p-7 text-slate-355 relative overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[8000ms] hover:scale-110" 
                 style="background-image: url('{{ asset('developer_login_banner.png') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/85 to-transparent"></div>
            
            <!-- Branding Header -->
            <div class="relative z-10 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-violet-500/20 border border-violet-500/40 backdrop-blur-md flex items-center justify-center shadow-lg">
                    <i class="bi bi-shield-fill-check text-violet-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black tracking-wider text-violet-400 uppercase">EWS Dev Hub</h4>
                    <div class="text-[8px] text-slate-400 tracking-widest font-extrabold uppercase font-mono">AUTH CONSOLE</div>
                </div>
            </div>

            <!-- Central Content Display -->
            <div class="relative z-10 space-y-3 my-auto">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-violet-500/20 border border-violet-500/30 text-[8px] font-extrabold text-violet-405 tracking-wider uppercase font-mono backdrop-blur-md">
                    <i class="bi bi-key-fill"></i> MFA: OTP CHALLENGE
                </span>
                <h2 class="text-xl font-black leading-tight tracking-tight text-white uppercase">
                    MULTI-FACTOR IDENTITY<br>VERIFICATION PORT
                </h2>
                <p class="text-slate-300 text-[10px] font-light leading-relaxed max-w-xs">
                    Please provide the active verification code generated for your EWS administrative session to finalize access.
                </p>
            </div>

            <!-- Left Footer guidelines -->
            <div class="relative z-10 flex items-center justify-between text-[8px] text-slate-400 border-t border-slate-800/80 pt-3">
                <span>Housing For All, Haryana.</span>
                <span class="font-mono text-violet-450">v2.4-stable</span>
            </div>
        </div>

        <!-- Right Side: Developer OTP Verify Form (7/12 width) -->
        <div class="w-full md:w-7/12 h-full flex flex-col justify-between p-6 sm:p-8 bg-slate-900/45 backdrop-blur-md relative overflow-y-auto custom-scroll">
            
            <!-- Mobile Header logo -->
            <div class="flex md:hidden items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded bg-violet-500/10 border border-violet-500/30 flex items-center justify-center">
                        <i class="bi bi-shield-fill-check text-violet-400 text-xs"></i>
                    </div>
                    <span class="font-black text-slate-200 text-xs tracking-wider">EWS DEV MFA</span>
                </div>
                <span class="text-[8px] text-violet-400 bg-violet-400/10 px-2 py-0.5 rounded border border-violet-400/20 font-bold font-mono">VERIFY</span>
            </div>

            <!-- Spacer -->
            <div class="hidden md:block h-1"></div>

            <!-- Developer OTP Form Container (Centered & Clean) -->
            <div class="w-full max-w-sm mx-auto space-y-4 my-auto">
                <div class="text-center md:text-left space-y-1">
                    <h3 class="text-base font-black tracking-tight text-white uppercase">OTP Verification</h3>
                    <p class="text-slate-400 text-[10px] font-light">Enter 6-digit OTP code sent to +91 ******{{ substr($mobile, -4) }}.</p>
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

                <form method="POST" action="{{ route('ews.developer.login.verify') }}" class="space-y-3" id="devOtpForm">
                    @csrf

                    <!-- OTP Input -->
                    <div class="space-y-1">
                        <label for="otp" class="block text-[9px] font-black uppercase text-slate-400 tracking-wider">Verification OTP Code</label>
                        <input type="text" id="otp" name="otp" maxlength="6" placeholder="******" autocomplete="off"
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-white focus:border-violet-500 focus:ring-0 focus:outline-none placeholder-slate-650 font-mono text-center tracking-widest font-bold" required>
                    </div>

                    <!-- Sandbox Alert Banner -->
                    @if(app()->environment('local'))
                        <div class="bg-cyan-500/5 border border-cyan-500/25 rounded-lg p-2 text-[9px] text-cyan-400 font-mono">
                            <span class="font-bold uppercase">Sandbox:</span> Use test OTP <span class="bg-cyan-950 px-1 py-0.5 rounded text-white font-bold">111111</span> to proceed.
                        </div>
                    @else
                        <div class="bg-yellow-500/5 border border-yellow-500/25 rounded-lg p-2 text-[9px] text-yellow-500/80 font-mono">
                            <span class="font-bold uppercase">Production:</span> Enter code sent to your registered phone.
                        </div>
                    @endif

                    <!-- Buttons Group -->
                    <button type="submit"
                        class="w-full py-2 bg-gradient-to-r from-violet-500 to-violet-600 hover:from-violet-600 hover:to-violet-750 text-slate-950 font-black uppercase tracking-wider rounded-lg text-[10px] shadow-lg shadow-violet-500/10 hover:shadow-violet-500/20 transition-all flex items-center justify-center gap-1.5">
                        <span>Authorize Login</span>
                        <i class="bi bi-shield-check-fill text-[11px]"></i>
                    </button>
                </form>

                <!-- Resend OTP and Change Number -->
                <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 font-mono">
                    <form method="POST" action="{{ route('ews.developer.login.resend-otp') }}" class="m-0">
                        @csrf
                        <button type="submit" class="text-violet-400 hover:text-violet-300 font-bold transition">
                            <i class="bi bi-arrow-clockwise text-[11px]"></i> Resend OTP
                        </button>
                    </form>
                    <a href="{{ route('ews.developer.login') }}" class="text-slate-500 hover:text-slate-400 transition">
                        <i class="bi bi-arrow-left text-[11px]"></i> Change Mobile
                    </a>
                </div>
            </div>

            <!-- Mobile/Desktop Footer guidelines -->
            <div class="flex items-center justify-between text-[8px] text-slate-650 pt-3 border-t border-slate-800">
                <span>© 2026 Housing For All, Haryana.</span>
                <span class="font-mono text-violet-500/50">ENV: LOCAL</span>
            </div>

            <div class="hidden md:block h-1"></div>
        </div>
    </div>

</body>
</html>
