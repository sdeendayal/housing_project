<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Citizen Portal - Secure Login</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        .login-bg {
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.85)), 
                        url('https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?q=80&w=1600&auto=format&fit=crop') center / cover no-repeat;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center login-bg p-4 md:p-8 overflow-hidden">

    <!-- Split Glass Container -->
    <div class="w-full max-w-5xl h-full md:h-[85vh] bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl overflow-hidden flex flex-col md:flex-row shadow-2xl">
        
        <!-- Left Side: Content & Metrics (7/12 width) -->
        <div class="hidden md:flex md:w-7/12 h-full flex-col justify-between p-8 text-white border-r border-white/10 relative">
            
            <!-- Branding Header -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center shadow-lg">
                    <i class="bi bi-houses-fill text-[#0f172a] text-base"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black tracking-wider text-amber-400 uppercase">EWS Digital Registry</h4>
                    <div class="text-[9px] text-slate-400 tracking-widest font-extrabold uppercase">Housing Board Haryana</div>
                </div>
            </div>

            <!-- Central Content Display -->
            <div class="space-y-4 my-auto">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-[9px] font-extrabold text-amber-400 tracking-wider uppercase">
                    <i class="bi bi-shield-check-fill"></i> Secure Government Gate
                </span>
                <h2 class="text-3xl font-black leading-tight tracking-tight text-white uppercase">
                    EWS Housing Survey & <br>Allotment Registry Console
                </h2>
                <p class="text-slate-300 text-xs font-light leading-relaxed max-w-md">
                    Official database for citizen registrations, eligibility checklists, and physical possession clearance under the Economically Weaker Section (EWS) scheme.
                </p>

                <!-- Stats compact cards -->
                <div class="grid grid-cols-3 gap-3 pt-3">
                    <div class="bg-white/5 border border-white/10 p-2.5 rounded-xl">
                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Total Records</div>
                        <div class="text-sm font-black text-white mt-0.5">2,731 Files</div>
                    </div>
                    <div class="bg-white/5 border border-white/10 p-2.5 rounded-xl">
                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Income Status</div>
                        <div class="text-sm font-black text-white mt-0.5">100% PPP</div>
                    </div>
                    <div class="bg-white/5 border border-white/10 p-2.5 rounded-xl">
                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">SMS Gateway</div>
                        <div class="text-sm font-black text-emerald-400 mt-0.5">Connected</div>
                    </div>
                </div>
            </div>

            <!-- Left Footer guidelines -->
            <div class="flex items-center justify-between text-[10px] text-slate-400 border-t border-white/10 pt-4">
                <span>© 2026 Housing For All, Haryana. Protected by NIC.</span>
                <span class="font-mono text-[9px] text-amber-400">v4.8.x-ews</span>
            </div>
        </div>

        <!-- Right Side: Centered Login Card (5/12 width) -->
        <div class="w-full md:w-5/12 h-full flex flex-col justify-between p-6 sm:p-8 bg-[#0a0f24]/30 backdrop-blur-md relative">
            
            <!-- Mobile Header logo -->
            <div class="flex md:hidden items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-500 flex items-center justify-center">
                        <i class="bi bi-houses-fill text-[#0f172a] text-xs"></i>
                    </div>
                    <span class="font-black text-slate-200 text-xs tracking-wider">EWS PORTAL</span>
                </div>
                <span class="text-[9px] text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20 font-bold">LIVE LINK</span>
            </div>

            <!-- Spacer -->
            <div class="hidden md:block h-2"></div>

            <!-- Centered Floating Login Card -->
            <div class="w-full max-w-sm mx-auto space-y-4 my-auto">
                <div class="bg-white rounded-2xl overflow-hidden shadow-2xl border border-slate-100">
                    
                    <!-- Card brand header -->
                    <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-blue-900 to-slate-900 text-white">
                        <div class="w-8.5 h-8.5 rounded-lg bg-white/10 flex items-center justify-center">
                            <i class="bi bi-houses text-base"></i>
                        </div>
                        <div>
                            <h2 class="text-xs font-extrabold tracking-wide uppercase leading-tight">EWS Citizen Gate</h2>
                            <div class="text-[9px] text-orange-400 font-bold uppercase tracking-wider">Housing Board Haryana</div>
                        </div>
                    </div>

                    <!-- Card Form body -->
                    <div class="p-4 space-y-3.5">
                        
                        <form id="sendOtpForm" action="{{ route('ews.citizen.login.send-otp') }}" method="POST" class="space-y-3.5">
                            @csrf

                            <!-- Mobile -->
                            <div class="space-y-1">
                                <label for="mobileInput" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                                    <span>Mobile Number</span>
                                    <span class="text-red-500 font-bold">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 text-xs font-bold text-slate-400 border-r border-slate-200 pr-3">+91</span>
                                    <input 
                                        type="text" 
                                        id="mobileInput"
                                        name="mobile"
                                        maxlength="10"
                                        inputmode="numeric"
                                        autocomplete="tel"
                                        placeholder="10-digit number"
                                        value="{{ old('mobile') }}"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-14 pr-4 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 transition-all font-semibold"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Captcha -->
                            <div class="space-y-1">
                                <label for="captchaInput" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    Captcha Code <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-12 gap-2">
                                    <!-- Box -->
                                    <div class="col-span-8 flex items-center bg-slate-50 border border-slate-200 rounded-xl overflow-hidden px-1 py-1">
                                        <div id="captchaText" class="w-full text-center text-xs font-mono font-bold tracking-[0.2em] text-slate-700 italic select-none">
                                            {{ $captcha }}
                                        </div>
                                        <button 
                                            type="button" 
                                            onclick="refreshCaptcha(this)"
                                            class="w-7 h-7 rounded-lg bg-white hover:bg-slate-100 text-blue-900 border border-slate-200 flex items-center justify-center transition-colors shadow-sm"
                                            title="Refresh"
                                        >
                                            <i class="bi bi-arrow-clockwise text-xs"></i>
                                        </button>
                                    </div>
                                    <!-- Input -->
                                    <div class="col-span-4">
                                        <input 
                                            type="text" 
                                            id="captchaInput"
                                            name="captcha"
                                            placeholder="Code"
                                            maxlength="4"
                                            autocomplete="off"
                                            class="w-full text-center bg-slate-50 border border-slate-200 rounded-xl py-2.5 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 transition-all font-bold"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <button 
                                type="submit" 
                                id="sendOtpBtn"
                                class="w-full py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs active:scale-[0.98] transition-all flex items-center justify-center gap-1.5 uppercase tracking-wider shadow"
                            >
                                <span>Send Verification OTP</span>
                                <i class="bi bi-arrow-right text-[10px]"></i>
                            </button>
                        </form>

                        <!-- Guidelines PDF list -->
                        <div class="p-2.5 bg-slate-50 border border-slate-200/60 rounded-xl space-y-1.5 text-[9px] font-semibold">
                            <div class="font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1">
                                <i class="bi bi-file-earmark-arrow-down-fill text-blue-900"></i>
                                <span>Guidelines & Downloads</span>
                            </div>
                            <div class="grid grid-cols-2 gap-1">
                                <a href="#" onclick="alert('Downloading guidelines...')" class="text-slate-655 hover:text-blue-900 transition-colors truncate">
                                    <i class="bi bi-file-pdf"></i> EWS_Guidelines.pdf
                                </a>
                                <a href="#" onclick="alert('Downloading rules...')" class="text-slate-655 hover:text-blue-900 transition-colors truncate">
                                    <i class="bi bi-file-pdf"></i> Exclusion_Rules.pdf
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Desk Details -->
                <div class="flex justify-between items-center text-[10px] text-slate-400 px-1 pt-1">
                    <span>Helpdesk: 1800-180-2122</span>
                    <a href="{{ route('home') }}" class="hover:text-slate-200 transition-colors inline-flex items-center gap-0.5 font-bold">
                        <i class="bi bi-arrow-left"></i> Home
                    </a>
                </div>
            </div>

            <!-- Spacer -->
            <div class="hidden md:block h-2"></div>

            <!-- Footer compliant text -->
            <div class="w-full flex items-center justify-between text-[8px] text-slate-500 border-t border-white/10 pt-4 tracking-wider uppercase font-semibold">
                <span>SECURED DLT GATEWAY</span>
                <span>NIC Haryana Compliant</span>
            </div>
        </div>

    </div>

    <!-- Page Fullscreen Loader -->
    <div id="pageLoader" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden">
        <div class="bg-white border border-slate-100 p-5 rounded-2xl flex flex-col items-center gap-3 max-w-xs shadow-2xl">
            <div class="w-8 h-8 border-4 border-slate-200 border-t-blue-900 rounded-full animate-spin"></div>
            <p class="text-xs font-semibold text-slate-700">Requesting OTP, please wait...</p>
        </div>
    </div>

    <!-- Captcha Refreshing Script and Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // CAPTCHA REFRESH
        function refreshCaptcha(btn) {
            if (btn.classList.contains('pointer-events-none')) return;
            btn.classList.add('pointer-events-none');
            
            var box = document.getElementById('captchaText');
            box.style.opacity = '0.5';

            fetch('/refresh-captcha', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                box.textContent = data.captcha;
                box.style.opacity = '1';
                btn.classList.remove('pointer-events-none');
            })
            .catch(function(err) {
                console.error(err);
                box.style.opacity = '1';
                btn.classList.remove('pointer-events-none');
            });
        }

        // MOBILE INPUT SANITIZER
        var mobileInput = document.getElementById('mobileInput');
        if (mobileInput) {
            mobileInput.addEventListener('input', function() {
                var value = this.value.replace(/\D/g, '');
                if (value.length > 10) {
                    value = value.slice(-10);
                }
                this.value = value;
            });
        }

        // FORM SUBMIT HANDLER
        var form = document.getElementById('sendOtpForm');
        var loader = document.getElementById('pageLoader');
        if (form && loader) {
            form.addEventListener('submit', function() {
                loader.classList.remove('hidden');
            });
        }

        // SWEETALERT POPUPS FOR ERRORS
        function showAlert(icon, title, text) {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonColor: '#1e3a8a',
                background: '#ffffff',
                color: '#0f172a',
            });
        }

        @if ($errors->any())
            showAlert('error', 'Validation Error', @json($errors->first()));
        @elseif (session('error'))
            showAlert('error', 'Unable to Send OTP', @json(session('error')));
        @endif
    </script>
</body>
</html>
