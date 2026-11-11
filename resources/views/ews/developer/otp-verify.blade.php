<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer Portal - Login OTP Verification | Haryana</title>
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
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
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
        .otp-box:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            background-color: #ffffff;
        }
    </style>
</head>
<body class="h-screen max-h-screen w-screen overflow-hidden flex items-center justify-center p-2 sm:p-4 bg-[#080c14] select-none relative">
    
    <!-- Background Accents -->
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_75%_75%_at_50%_-15%,rgba(99,102,241,0.18),rgba(255,255,255,0))] pointer-events-none"></div>
    <div class="fixed inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] opacity-20 pointer-events-none"></div>

    <!-- Main Container: Compact, High-Density Enterprise Layout -->
    <div class="w-full max-w-5xl max-h-[92vh] rounded-[24px] overflow-hidden flex flex-col lg:flex-row luxury-shadow border border-slate-700/60 z-10 relative bg-slate-950">
        
        <!-- LEFT PANEL: Multi-Factor Authentication Dossier (42% width) -->
        <div class="hidden lg:flex lg:w-5/12 flex-col justify-between p-6 text-white relative overflow-hidden bg-slate-950 border-r border-slate-800">
            <!-- Architectural Background -->
            <div class="absolute inset-0 bg-cover bg-center opacity-40" 
                 style="background-image: url('{{ asset('developer_portal_hero.jpg') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/92 to-slate-950/70"></div>
            
            <!-- Header Branding -->
            <div class="relative z-10 flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/15 border border-indigo-400/30 backdrop-blur-xl flex items-center justify-center text-indigo-400 shrink-0">
                    <i class="bi bi-shield-lock-fill text-base"></i>
                </div>
                <div>
                    <div class="text-[9px] tracking-[0.2em] font-extrabold uppercase text-indigo-400 font-mono">GOVERNMENT OF HARYANA</div>
                    <h4 class="text-xs font-black tracking-wide text-white uppercase brand-font">Housing For All • Developer Security Gateway</h4>
                </div>
            </div>

            <!-- Central Content: Auth Context & Security Snapshot -->
            <div class="relative z-10 space-y-3.5 my-auto py-2">
                
                <!-- Status Badge -->
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-[9px] font-bold text-indigo-300 tracking-wider uppercase font-mono">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    <span>MULTI-FACTOR SESSION AUTHENTICATION</span>
                </div>

                <div>
                    <h2 class="text-xl font-extrabold leading-tight text-white uppercase brand-font">
                        Authorize Your<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-teal-200 to-emerald-300">
                            Developer Session
                        </span>
                    </h2>
                    <p class="text-slate-400 text-[11px] leading-relaxed mt-1">
                        High-security access terminal. Enter the 6-digit one-time authorization code sent to your registered mobile number to proceed.
                    </p>
                </div>

                <!-- Session Security Dossier -->
                <div class="p-3 rounded-xl bg-white/[0.06] border border-white/10 backdrop-blur-md space-y-2 text-[11px]">
                    <div class="flex items-center justify-between border-b border-white/10 pb-1.5">
                        <span class="text-[9px] uppercase font-bold text-indigo-300 font-mono tracking-wider flex items-center gap-1">
                            <i class="bi bi-person-check-fill"></i> Authorized Session Target
                        </span>
                        <span class="text-[9px] text-slate-400 font-mono">ENCRYPTED SESSION</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <div class="text-[9px] text-slate-400 uppercase font-mono">Target Mobile</div>
                            <div class="font-mono font-bold text-indigo-300 text-xs">+91 ******{{ substr($mobile, -4) }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-400 uppercase font-mono">Challenge Protocol</div>
                            <div class="text-slate-300 font-mono text-[10px]">SMS Dual-Factor (2FA)</div>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-400 uppercase font-mono">Security Tier</div>
                            <div class="text-emerald-400 font-bold text-xs flex items-center gap-1">
                                <i class="bi bi-shield-fill-check text-[10px]"></i> Enterprise 256-Bit
                            </div>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-400 uppercase font-mono">Passcode Validity</div>
                            <div class="text-slate-300 font-mono text-[10px]">10 Minutes Remaining</div>
                        </div>
                    </div>
                </div>

                <!-- Security Checklist -->
                <div class="space-y-1.5 bg-slate-900/80 p-2.5 rounded-xl border border-white/10 text-[10px]">
                    <div class="flex items-center gap-2 text-slate-300">
                        <i class="bi bi-check2-circle text-emerald-400 text-xs"></i>
                        <span>Registered Developer credential verified in state registry</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-300">
                        <i class="bi bi-shield-check text-indigo-400 text-xs"></i>
                        <span>End-to-end encrypted session dispatch via Govt Gateway</span>
                    </div>
                </div>

                <!-- Legal / Compliance Notice -->
                <div class="p-2 rounded-lg bg-indigo-950/30 border border-indigo-500/20 text-[9px] text-slate-300 leading-tight flex items-start gap-1.5">
                    <i class="bi bi-info-circle text-indigo-400 text-xs shrink-0 mt-0.5"></i>
                    <span>All sessions are logged with IP & timestamp. Unauthorized access attempts are monitored under the IT Act 2000.</span>
                </div>

            </div>

            <!-- Footer Badge -->
            <div class="relative z-10 flex items-center justify-between text-[9px] text-slate-400 border-t border-white/10 pt-2 font-mono">
                <span>Haryana EWS Portal v2.6</span>
                <span class="text-indigo-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span> Gateway Secured
                </span>
            </div>
        </div>

        <!-- RIGHT PANEL: Compact, High-Information OTP Verification Console (58% width) -->
        <div class="w-full lg:w-7/12 glass-panel p-5 sm:p-7 flex flex-col justify-between text-slate-800 relative bg-white">
            
            <div class="space-y-4 max-w-lg mx-auto w-full">
                
                <!-- Header with Breadcrumb & Timer Badge -->
                <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-900 text-[10px] font-bold uppercase tracking-wider">
                        <i class="bi bi-shield-lock-fill text-xs text-indigo-700"></i> Session Verification
                    </div>
                    <div class="flex items-center gap-1 text-[11px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                        <i class="bi bi-clock-history text-slate-600"></i>
                        <span>Validity: <span class="text-indigo-700">10 Mins</span></span>
                    </div>
                </div>

                <!-- Title & Context -->
                <div class="space-y-1">
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 brand-font uppercase tracking-tight">
                        Enter Passcode Code
                    </h3>
                    <p class="text-slate-600 text-xs leading-relaxed">
                        A secure 6-digit one-time authorization code has been dispatched via SMS to 
                        <strong class="text-slate-900 font-mono bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">+91 ******{{ substr($mobile, -4) }}</strong>.
                    </p>
                </div>

                <!-- Flash Messages -->
                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-2.5 rounded-xl text-xs flex items-center gap-2 shadow-sm">
                        <i class="bi bi-exclamation-circle-fill text-rose-600 text-sm shrink-0"></i>
                        <span class="font-medium text-[11px]">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-2.5 rounded-xl text-xs flex items-center gap-2 shadow-sm">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-sm shrink-0"></i>
                        <span class="font-medium text-[11px]">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Form: Segmented 6-Digit Code Inputs -->
                <form id="otpForm" method="POST" action="{{ route('ews.developer.login.verify') }}" class="space-y-4" onsubmit="showThemeLoader('Authorizing Developer Session & Launching Dashboard...')">
                    @csrf
                    
                    <!-- Hidden real input containing the 6 digits -->
                    <input type="hidden" id="otp" name="otp" required>

                    <!-- Segmented Inputs Container -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-[11px] font-bold uppercase text-slate-700 tracking-wider">
                                6-Digit Passcode <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-slate-400 font-mono">Auto-advances on type</span>
                        </div>

                        <!-- 6 Boxes -->
                        <div class="flex items-center justify-between gap-2 sm:gap-2.5" id="otpBoxContainer">
                            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-box w-11 h-12 sm:w-12 sm:h-12 bg-slate-50 border-2 border-slate-200 rounded-xl text-center text-xl font-mono font-black text-slate-900 transition-all cursor-pointer" data-index="0" autofocus autocomplete="off">
                            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-box w-11 h-12 sm:w-12 sm:h-12 bg-slate-50 border-2 border-slate-200 rounded-xl text-center text-xl font-mono font-black text-slate-900 transition-all cursor-pointer" data-index="1" autocomplete="off">
                            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-box w-11 h-12 sm:w-12 sm:h-12 bg-slate-50 border-2 border-slate-200 rounded-xl text-center text-xl font-mono font-black text-slate-900 transition-all cursor-pointer" data-index="2" autocomplete="off">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-box w-11 h-12 sm:w-12 sm:h-12 bg-slate-50 border-2 border-slate-200 rounded-xl text-center text-xl font-mono font-black text-slate-900 transition-all cursor-pointer" data-index="3" autocomplete="off">
                            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-box w-11 h-12 sm:w-12 sm:h-12 bg-slate-50 border-2 border-slate-200 rounded-xl text-center text-xl font-mono font-black text-slate-900 transition-all cursor-pointer" data-index="4" autocomplete="off">
                            <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="otp-box w-11 h-12 sm:w-12 sm:h-12 bg-slate-50 border-2 border-slate-200 rounded-xl text-center text-xl font-mono font-black text-slate-900 transition-all cursor-pointer" data-index="5" autocomplete="off">
                        </div>
                    </div>

                    <!-- Compact Enterprise Advisory Guidelines (Kam Jgha me Jyada Shabad) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[10px] text-slate-600">
                        <div class="flex items-start gap-1.5">
                            <i class="bi bi-shield-shaded text-indigo-600 text-xs shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800 block font-semibold">Strict Privacy</strong>
                                <span>Never share OTP with anyone, including staff.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-1.5">
                            <i class="bi bi-hourglass-split text-rose-600 text-xs shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800 block font-semibold">Max 5 Attempts</strong>
                                <span>Exceeding attempts locks session for 15m.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-1.5">
                            <i class="bi bi-speedometer2 text-emerald-600 text-xs shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800 block font-semibold">Instant Access</strong>
                                <span>Direct authorization to developer dashboard.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-indigo-600 via-indigo-700 to-slate-900 hover:from-indigo-700 hover:via-indigo-800 hover:to-black text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/35 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-wider group cursor-pointer">
                        <i class="bi bi-unlock-fill text-sm"></i>
                        <span>Authorize Login & Access Dashboard</span>
                        <i class="bi bi-arrow-right text-xs group-hover:translate-x-1.5 transition-transform duration-200"></i>
                    </button>
                </form>

                <!-- Resend & Change Number Options -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-3 text-xs">
                    <form method="POST" action="{{ route('ews.developer.login.resend-otp') }}">
                        @csrf
                        <button type="submit" id="resendBtn" class="text-indigo-600 hover:text-indigo-900 font-bold flex items-center gap-1.5 text-xs cursor-pointer">
                            <i class="bi bi-arrow-clockwise"></i>
                            <span>Resend Passcode</span>
                            <span id="countdown" class="text-slate-400 font-mono font-normal"></span>
                        </button>
                    </form>

                    <a href="{{ route('ews.developer.login') }}" class="text-slate-500 hover:text-slate-800 font-semibold flex items-center gap-1.5 text-xs transition">
                        <i class="bi bi-arrow-left"></i>
                        <span>Change Mobile Number</span>
                    </a>
                </div>

                <!-- Support & Helpdesk Row (High information density) -->
                <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-[10px] text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <i class="bi bi-headset text-slate-700 text-xs"></i>
                        <span>Support: <strong>1800-180-2022</strong> (Toll Free, 9AM-5PM)</span>
                    </div>
                    <div class="hidden sm:flex items-center gap-1 text-slate-400 font-mono">
                        <i class="bi bi-envelope"></i> support-hfa@hry.gov.in
                    </div>
                </div>

            </div>

            <!-- Footer Compliance -->
            <div class="text-center pt-2 text-[10px] text-slate-400 border-t border-slate-100 flex items-center justify-between">
                <span>Department of Housing For All, Haryana</span>
                <span>Protected by IT Act 2000 & Cloudflare SSL</span>
            </div>

        </div>

    </div>

    <!-- Theme Loader Modal -->
    <div id="themeLoader" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 hidden flex-col items-center justify-center">
        <div class="flex flex-col items-center max-w-sm text-center p-6 rounded-2xl bg-slate-900/95 border border-slate-700/60 shadow-2xl">
            <div class="relative w-16 h-16 mb-4">
                <div class="orbital-spinner w-16 h-16"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-emerald-400 flex items-center justify-center text-white shadow-lg">
                        <i class="bi bi-shield-check text-sm"></i>
                    </div>
                </div>
            </div>
            <h4 class="text-base font-black text-white uppercase brand-font tracking-wide mb-1" id="loaderTitle">
                Authorizing Session
            </h4>
            <p class="text-[11px] text-slate-400 font-normal leading-relaxed" id="loaderSubtitle">
                Validating security OTP & opening your Developer Registry space...
            </p>
        </div>
    </div>

    <script>
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

        // Countdown timer for Resend button
        let secondsLeft = 60;
        const countdownEl = document.getElementById('countdown');
        const resendBtn = document.getElementById('resendBtn');
        const timer = setInterval(() => {
            if (secondsLeft > 0) {
                countdownEl.innerText = `(${secondsLeft}s)`;
                secondsLeft--;
            } else {
                clearInterval(timer);
                countdownEl.innerText = '';
            }
        }, 1000);

        // 6-digit segmented OTP Box Handler
        const boxes = document.querySelectorAll('.otp-box');
        const hiddenOtpInput = document.getElementById('otp');
        const form = document.getElementById('otpForm');

        function updateHiddenOtp() {
            let code = '';
            boxes.forEach(box => {
                code += box.value.trim();
            });
            hiddenOtpInput.value = code;
        }

        boxes.forEach((box, index) => {
            box.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val ? val[0] : '';
                updateHiddenOtp();

                if (val && index < boxes.length - 1) {
                    boxes[index + 1].focus();
                    boxes[index + 1].select();
                }
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    if (!box.value && index > 0) {
                        boxes[index - 1].focus();
                        boxes[index - 1].select();
                    } else {
                        box.value = '';
                        updateHiddenOtp();
                    }
                } else if (e.key === 'ArrowLeft' && index > 0) {
                    boxes[index - 1].focus();
                } else if (e.key === 'ArrowRight' && index < boxes.length - 1) {
                    boxes[index + 1].focus();
                }
            });

            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                if (pastedData) {
                    for (let i = 0; i < boxes.length; i++) {
                        if (pastedData[i]) {
                            boxes[i].value = pastedData[i];
                        }
                    }
                    updateHiddenOtp();
                    const nextFocusIndex = Math.min(pastedData.length, boxes.length - 1);
                    boxes[nextFocusIndex].focus();
                }
            });
        });

        // Ensure OTP is populated on form submit
        form.addEventListener('submit', function(e) {
            updateHiddenOtp();
            if (hiddenOtpInput.value.length !== 6) {
                e.preventDefault();
                const loader = document.getElementById('themeLoader');
                if (loader) {
                    loader.classList.add('hidden');
                    loader.classList.remove('flex');
                }
                alert('Please enter all 6 digits of the verification passcode.');
                for (let i = 0; i < boxes.length; i++) {
                    if (!boxes[i].value) {
                        boxes[i].focus();
                        break;
                    }
                }
            }
        });
    </script>
</body>
</html>
