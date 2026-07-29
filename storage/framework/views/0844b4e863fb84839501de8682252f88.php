<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>MMGAY Admin Portal Login</title>
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
            background-image: radial-gradient(circle, #0058bc10 1.5px, transparent 1.5px);
            background-size: 32px 32px;
        }

        body {
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-white font-body-md text-on-surface min-h-screen flex flex-col h-screen max-h-screen">

    <!-- Main Content Canvas: 50/50 Split Screen -->
    <main class="flex-grow flex flex-col md:flex-row overflow-hidden">
        <!-- Left Side: Login Form -->
        <section class="w-full md:w-1/2 flex flex-col justify-center items-center relative login-bg-pattern px-6 md:px-12 py-4 overflow-hidden h-full">
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
                    class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_15px_40px_rgba(0,88,188,0.12)] border border-blue-100 p-5">

                    <form method="POST" action="<?php echo e(route('mmgay.login.submit')); ?>" class="space-y-3">
                        <?php echo csrf_field(); ?>

                        
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-[#414755] uppercase tracking-wider">
                                Username or Email
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#414755] text-[18px]">
                                    person
                                </span>

                                <input type="text" name="email" value="<?php echo e(old('email')); ?>"
                                    placeholder="Username or Email"
                                    class="w-full h-[38px] pl-9 pr-4 text-xs border rounded-lg <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> focus:ring-1 focus:ring-[#0058bc] focus:border-[#0058bc]">

                            </div>

                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-[10px] mt-0.5"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="space-y-1">

                            <label class="text-[10px] font-bold text-[#414755] uppercase tracking-wider">
                                Password
                            </label>

                            <div class="relative">

                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#414755] text-[18px]">
                                    lock
                                </span>

                                <input id="password" type="password" name="password" placeholder="Password"
                                    class="w-full h-[38px] pl-9 pr-9 text-xs border rounded-lg <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> focus:ring-1 focus:ring-[#0058bc] focus:border-[#0058bc]">

                                <button type="button" id="togglePassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#414755]">

                                    <span id="eyeIcon" class="material-symbols-outlined text-[18px]">
                                        visibility
                                    </span>

                                </button>

                            </div>

                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-[10px] mt-0.5"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="space-y-1">

                            <label class="text-[10px] font-bold uppercase tracking-wider text-[#414755]">
                                Security Verification
                            </label>

                            <div class="flex gap-2">

                                <div id="captchaBox"
                                    class="flex items-center justify-center w-[90px] h-[38px] rounded-lg bg-blue-50 border text-xs font-semibold tracking-widest text-[#0058bc]">
                                    <?php echo e(session('captcha')); ?>

                                </div>

                                <button type="button" id="refreshCaptcha"
                                    class="w-[38px] h-[38px] rounded-lg border border-[#c1c6d7] hover:bg-blue-50 transition flex items-center justify-center">

                                    <span id="refreshIcon" class="material-symbols-outlined text-[18px]">
                                        refresh
                                    </span>

                                </button>

                                <input type="text" name="captcha" placeholder="Enter Captcha"
                                    class="flex-1 h-[38px] text-xs border rounded-lg <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> focus:ring-1 focus:ring-[#0058bc] focus:border-[#0058bc]">

                            </div>

                            <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-[10px] mt-0.5"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        </div>

                        <button type="submit" class="login-btn w-full h-[38px] bg-[#0058bc] text-white rounded-lg text-xs font-semibold hover:bg-[#004bb4] transition">

                            Login

                        </button>

                    </form>

                    
                    <div class="mt-3.5 pt-3.5 border-t border-slate-100 flex flex-col gap-2">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">Villager / Beneficiary?</span>
                            <a href="<?php echo e(route('mmgav.villager.login')); ?>"
                                class="font-semibold text-[#0058bc] hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">sms</span>
                                Applicant  Login
                            </a>
                        </div>
                        <div class="flex items-center justify-between text-[11px] border-t border-dashed border-slate-100 pt-2">
                            <span class="text-slate-500">Block Development Officer?</span>
                            <a href="<?php echo e(route('mmgay.bdo.login')); ?>"
                                class="font-semibold text-[#0058bc] hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">login</span>
                                BDPO Login
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <!-- Right Side: Visual Canvas (Updated to IMAGE_4) -->
        <section class="hidden md:flex md:w-1/2 relative overflow-hidden bg-white">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img alt="Rural Haryana House" class="w-full h-full object-cover"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBfibej-rHcQ0lm0QeIHkVkL7J1ZV_FLSs4MgxkJg5J7Ssz29lgtfRR0kpKAg1HbxrWnXavXlOSuzmVNlroYiKa6n9baf5_OG74Fso1TFts2fVpAEkrn92QcAkLpcGa0IRf4Iq5_FSe0K_ndwJ7TalA40H53iO_8fPzEMs57-kzLt_8k4sNfGn_rq3uhBTwGQ0gUu7h70O_N3xFFbzE4p9xWMh5QTCbdnM4jGgINXy1mLyqGgnrkr9jYU5Y0gbjpKREvqO6-6nlkNRG" />
                <!-- High-contrast overlay to make text readable over the bright landscape -->
                <div class="absolute inset-0 bg-[#0058bc]/20 mix-blend-multiply"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/40 to-transparent"></div>
            </div>
            <!-- Content Overlay -->
            <div class="relative z-10 w-full flex flex-col items-center justify-center text-center px-10">
                <!-- White Circular House Icon -->
                <div
                    class="bg-white w-[110px] h-[110px] rounded-full shadow-2xl flex items-center justify-center mb-8 border-[6px] border-white/40">
                    <span class="material-symbols-outlined text-[#0058bc] text-[56px]"
                        style="font-variation-settings: 'FILL' 1;">holiday_village</span>
                </div>
                <div class="max-w-md">
                    <h2 class="text-[#0058bc] text-[34px] font-extrabold leading-tight mb-4">Mukhyamantri Gramin Awas
                        Yojana</h2>
                    <div class="h-[3px] w-20 bg-[#0058bc] mx-auto mb-6 rounded-full opacity-60"></div>
                    <p class="text-[#0058bc] text-[20px] font-bold mb-2">हरियाणा सरकार का है सपना, सबका घर हो अपना!</p>
                    <p class="text-[#0058bc]/80 text-[15px] italic">
                        (Providing sustainable housing solutions for every rural family in Haryana)
                    </p>
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

            fetch("<?php echo e(route('mmgay.refresh.captcha')); ?>")
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
    <?php if(session('info')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'info',
                    title: 'Villager Login',
                    text: '<?php echo e(session('info')); ?>'
                });
            });
        </script>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo e(session('success')); ?>'
            });
        </script>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo e(session('error')); ?>'
            });
        </script>
    <?php endif; ?>
</body>

</html>
<?php /**PATH E:\sports\housing_project\resources\views/mmgay/authLogin.blade.php ENDPATH**/ ?>