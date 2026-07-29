<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Department Login | Housing for All Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-100 min-h-screen flex overflow-x-hidden">

    <!-- Split Screen Container -->
    <div class="w-full min-h-screen flex">
        
        <!-- Left Side: Hero Banner (Dusk Housing Background) -->
        <div class="hidden lg:flex lg:w-[55%] relative flex-col justify-between p-16 overflow-hidden">
            <!-- Background Image with Cover -->
            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[10000ms] hover:scale-105" style="background-image: url('<?php echo e(asset('images/ews_bg.png')); ?>');"></div>
            <!-- High-quality Dark Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-950/70 to-slate-900/35"></div>

            <!-- Top Brand -->
            <div class="relative z-10 flex items-center gap-3">
                <img src="<?php echo e(asset('Haryana_emblem.png')); ?>" class="w-10 h-10 object-contain invert brightness-200" alt="Haryana Govt Emblem">
                <div>
                    <h3 class="text-xs font-black tracking-widest uppercase text-orange-400">Department of Housing</h3>
                    <p class="text-[9px] uppercase tracking-wider text-slate-400 font-bold">Government of Haryana</p>
                </div>
            </div>

            <!-- Central Callout -->
            <div class="relative z-10 my-auto max-w-xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-[10px] font-black uppercase text-orange-400 tracking-wider mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                    EWS Housing Scheme
                </span>
                <h1 class="text-4xl lg:text-5xl font-black text-white leading-none tracking-tight">
                    Affordable Homes for <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-amber-300">Every Family.</span>
                </h1>
                <p class="text-xs text-slate-400 font-semibold uppercase mt-4 leading-relaxed tracking-wide">
                    Online tracking and registry system for Economically Weaker Section (EWS) residential allocations under Haryana's state housing framework.
                </p>
            </div>

            <!-- Footer Details -->
            <div class="relative z-10 flex items-center gap-6 border-t border-slate-800/60 pt-6">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-500">Platform Version</p>
                    <p class="text-xs font-extrabold text-slate-300 mt-0.5">v2.4.0 (Secure)</p>
                </div>
                <div class="h-6 w-[1px] bg-slate-800"></div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-500">Assistance</p>
                    <p class="text-xs font-extrabold text-slate-300 mt-0.5">support-housing@hry.gov.in</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-[45%] flex items-center justify-center p-8 bg-[#1e293b] relative">
            
            <!-- Ambient Glow Spotlights -->
            <div class="absolute right-10 top-10 w-72 h-72 bg-orange-600/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute left-10 bottom-10 w-72 h-72 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Form Wrapper -->
            <div class="w-full max-w-sm relative z-10">
                
                <!-- Central Emblem & Title -->
                <div class="text-center mb-8">
                    <img src="<?php echo e(asset('Haryana_emblem.png')); ?>" class="w-14 h-14 mx-auto mb-4 object-contain" alt="Haryana Govt Emblem">
                    <h2 class="text-lg font-black tracking-tight text-white uppercase">EWS Department Portal</h2>
                    <p class="text-[9px] uppercase tracking-widest text-slate-500 font-black mt-1">Authorized Administration Sign In</p>
                </div>

                <!-- Alert Messages -->
                <?php if(session('success')): ?>
                    <div class="bg-emerald-950/40 border border-emerald-900/50 text-emerald-400 text-xs font-bold p-3.5 rounded-lg mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">check_circle</span>
                        <span><?php echo e(session('success')); ?></span>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="bg-rose-950/40 border border-rose-900/50 text-rose-400 text-xs font-bold p-3.5 rounded-lg mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">error</span>
                        <span><?php echo e(session('error')); ?></span>
                    </div>
                <?php endif; ?>

                <?php if(isset($errors) && $errors->any()): ?>
                    <div class="bg-rose-950/40 border border-rose-900/50 text-rose-400 text-xs font-bold p-3.5 rounded-lg mb-5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="material-symbols-outlined text-base">warning</span>
                            <span>Validation errors:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5 font-medium">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form id="loginForm" action="<?php echo e(route('ews.department.login.submit')); ?>" method="POST" class="space-y-4" onsubmit="handleLoginSubmit(this)">
                    <?php echo csrf_field(); ?>

                    <!-- Email / Mobile Input -->
                    <div>
                        <label for="email" class="block text-[9px] font-black uppercase text-slate-400 tracking-widest mb-1.5">Email Address / Mobile Number</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-3 text-orange-500 text-base">person</span>
                            <input type="text" name="email" id="email" required value="<?php echo e(old('email', 'ews_department@gmail.com')); ?>" placeholder="Email address or 10-digit mobile" class="w-full text-xs bg-slate-900/50 border border-slate-800 focus:border-orange-500 rounded-lg pl-10 pr-3 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-orange-500 transition font-bold">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-[9px] font-black uppercase text-slate-400 tracking-widest mb-1.5">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-3 text-orange-500 text-base">lock</span>
                            <input type="password" name="password" id="password" required value="password123" placeholder="••••••••" class="w-full text-xs bg-slate-900/50 border border-slate-800 focus:border-orange-500 rounded-lg pl-10 pr-3 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-orange-500 transition font-bold">
                        </div>
                    </div>

                    <!-- Submit Button with Spinner -->
                    <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-black uppercase py-3.5 rounded-lg text-xs tracking-widest shadow-md flex items-center justify-center gap-2 transition-all mt-6 disabled:opacity-75 disabled:cursor-not-allowed">
                        <span id="btnIcon" class="material-symbols-outlined text-[15px] font-bold">login</span>
                        <svg id="btnSpinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btnText">Sign In</span>
                    </button>
                </form>

                <script>
                    function handleLoginSubmit(form) {
                        const btn = document.getElementById('submitBtn');
                        const icon = document.getElementById('btnIcon');
                        const spinner = document.getElementById('btnSpinner');
                        const btnText = document.getElementById('btnText');
                        if (btn) {
                            btn.disabled = true;
                            if (icon) icon.classList.add('hidden');
                            if (spinner) spinner.classList.remove('hidden');
                            if (btnText) btnText.innerText = 'Signing In...';
                        }
                    }
                </script>

                <!-- Back to Homepage -->
                <div class="mt-8 pt-6 border-t border-slate-800/60 text-center">
                    <a href="/" class="text-[9px] text-slate-500 hover:text-orange-400 uppercase tracking-widest font-black transition-all inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        <span>Back to Homepage</span>
                    </a>
                </div>

            </div>

        </div>

    </div>

    <?php echo $__env->make('partials.global-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH E:\sports\housing_project\resources\views/ews/department/login.blade.php ENDPATH**/ ?>