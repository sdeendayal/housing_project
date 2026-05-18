<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login - Haryana Housing For All</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-secondary-fixed-variant": "#005312",
                        "primary": "#003358",
                        "on-primary-fixed-variant": "#00497b",
                        "on-primary": "#ffffff",
                        "on-tertiary-fixed": "#0f1d25",
                        "on-error": "#ffffff",
                        "secondary-container": "#a0f399",
                        "surface-container": "#eeeeee",
                        "on-secondary-fixed": "#002204",
                        "surface-tint": "#296195",
                        "inverse-on-surface": "#f1f1f1",
                        "primary-fixed": "#d0e4ff",
                        "on-surface": "#1a1c1c",
                        "success-green": "#2e7d32",
                        "on-surface-variant": "#42474f",
                        "on-secondary-container": "#217128",
                        "on-tertiary-container": "#a9b8c2",
                        "secondary-fixed-dim": "#88d982",
                        "inverse-surface": "#2f3131",
                        "surface-bright": "#f9f9f9",
                        "outline": "#727780",
                        "on-error-container": "#93000a",
                        "surface": "#f9f9f9",
                        "outline-variant": "#c1c7d0",
                        "on-background": "#1a1c1c",
                        "secondary": "#1b6d24",
                        "primary-container": "#004a7c",
                        "status-blue": "#004a7c",
                        "tertiary-fixed": "#d6e5ef",
                        "on-primary-fixed": "#001d35",
                        "background": "#f9f9f9",
                        "surface-variant": "#e2e2e2",
                        "error-container": "#ffdad6",
                        "secondary-fixed": "#a3f69c",
                        "on-primary-container": "#87baf3",
                        "tertiary-container": "#3b4952",
                        "surface-container-low": "#f3f3f3",
                        "tertiary-fixed-dim": "#bac9d3",
                        "surface-container-highest": "#e2e2e2",
                        "on-tertiary": "#ffffff",
                        "tertiary": "#25333b",
                        "glass-surface": "rgba(255, 255, 255, 0.7)",
                        "bg-subtle": "#f8f9fa",
                        "on-secondary": "#ffffff",
                        "on-tertiary-fixed-variant": "#3b4951",
                        "primary-fixed-dim": "#9ccaff",
                        "border-gray": "#e0e0e0",
                        "inverse-primary": "#9ccaff",
                        "surface-dim": "#dadada",
                        "error": "#ba1a1a",
                        "surface-container-high": "#e8e8e8",
                        "surface-container-lowest": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "container-max": "1280px",
                        "stack-lg": "32px",
                        "margin-mobile": "16px",
                        "gutter": "24px",
                        "stack-sm": "8px",
                        "stack-md": "16px"
                    },
                    "fontFamily": {
                        "headline-lg": ["Inter"],
                        "body-lg": ["Inter"],
                        "label-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "headline-md": ["Inter"],
                        "headline-xl": ["Inter"],
                        "label-md": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg": ["32px", {
                            "lineHeight": "40px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "700"
                        }],
                        "body-lg": ["18px", {
                            "lineHeight": "28px",
                            "fontWeight": "400"
                        }],
                        "label-sm": ["12px", {
                            "lineHeight": "16px",
                            "fontWeight": "500"
                        }],
                        "body-md": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "400"
                        }],
                        "body-sm": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
                        }],
                        "headline-md": ["24px", {
                            "lineHeight": "32px",
                            "fontWeight": "600"
                        }],
                        "headline-xl": ["40px", {
                            "lineHeight": "48px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "label-md": ["14px", {
                            "lineHeight": "16px",
                            "fontWeight": "600"
                        }]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-bg-subtle text-on-background font-body-md flex flex-col min-h-screen">
    <!-- TopAppBar Component -->
    <header
        class="text-on-primary docked full-width top-0 sticky border-b border-outline-variant shadow-md z-50 bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] bg-[url('../header-tp-bg.png')] bg-no-repeat bg-right bg-cover">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Placeholder for Department Logo -->
                <img alt="Department Logo" class="h-16 w-16 object-contain" src="Haryana_emblem.png">
                <div>
                    <h1 class="text-2xl font-bold leading-tight">Department of Housing For All</h1>
                    <p class="text-sm opacity-90">Government of Haryana</p>
                </div>
            </div>
            <!-- National Emblem Placeholder -->
            <img alt="National Emblem" class="h-16 w-auto object-contain hidden md:block" src="emblem-black.png">
        </div>
    </header>
    <!-- Main Content Canvas -->
    <main class="flex-grow flex items-center justify-center py-stack-lg px-margin-mobile md:px-gutter relative">
        <!-- Subtle background decorative element -->
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-primary-fixed/20 to-transparent pointer-events-none">
        </div>
        <div class="w-full max-w-[1000px] z-10">

            <!-- Bento Grid / Side-by-Side Cards -->
            <!-- Login Cards Wrapper -->
            <!-- Login Cards Wrapper -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Citizen Login -->
                <form action="/mmsay.citizen.dashboard" method="GET"
                    class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0_4px_20px_rgba(0,0,0,0.05)] flex flex-col overflow-hidden h-full">

                    <!-- Header -->
                    <div class="bg-surface-container-low p-4 border-b border-outline-variant flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-full bg-primary-fixed text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                                person
                            </span>
                        </div>

                        <div>
                            <h2 class="font-semibold text-xl text-on-surface">
                                Citizen Login
                            </h2>

                            <p class="text-xs text-on-surface-variant">
                                OTP based secure login
                            </p>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 flex flex-col gap-4">

                        @csrf

                        <!-- Mobile -->
                        <div>
                            <label class="text-xs text-on-surface-variant block mb-1">
                                Mobile Number
                            </label>

                            <div class="flex">
                                <span
                                    class="px-3 flex items-center bg-surface-container border border-outline-variant border-r-0 rounded-l-lg text-sm text-on-surface-variant">
                                    +91
                                </span>

                                <input name="mobile" required
                                    class="w-full bg-surface border border-outline-variant rounded-r-lg px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none"
                                    placeholder="Enter Mobile Number" type="text">
                            </div>
                        </div>

                        <!-- OTP -->
                        <div>
                            <label class="text-xs text-on-surface-variant block mb-1">
                                OTP Verification
                            </label>

                            <div class="flex gap-2">

                                <input name="otp" required
                                    class="w-full bg-surface border border-outline-variant rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none"
                                    placeholder="Enter OTP" type="text">

                                <button type="button"
                                    class="bg-secondary hover:bg-secondary/90 text-white px-4 rounded-lg text-sm whitespace-nowrap">
                                    Send OTP
                                </button>

                            </div>
                        </div>

                        <!-- Info -->
                        <div
                            class="bg-primary-fixed/20 border border-primary-fixed rounded-lg p-3 flex gap-2 items-start">

                            <span class="material-symbols-outlined text-primary text-base">
                                verified_user
                            </span>

                            <p class="text-xs text-on-surface-variant">
                                OTP will be sent to your registered mobile number.
                            </p>
                        </div>

                        <!-- Login -->
                        <button type="submit"
                            class="w-full bg-primary hover:bg-primary-container text-on-primary font-medium py-2.5 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">

                            <span>Verify & Login</span>

                            <span class="material-symbols-outlined text-sm">
                                arrow_forward
                            </span>
                        </button>

                    </div>
                </form>



                <!-- Department Login -->
                <form action="/mmsay.department.dashboard" method="GET"
                    class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0_4px_20px_rgba(0,0,0,0.05)] flex flex-col overflow-hidden h-full">

                    <!-- Header -->
                    <div class="bg-surface-container-low p-4 border-b border-outline-variant flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-full bg-tertiary-fixed text-tertiary flex items-center justify-center">

                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                                admin_panel_settings
                            </span>
                        </div>

                        <div>
                            <h2 class="font-semibold text-xl text-on-surface">
                                Department Login
                            </h2>

                            <p class="text-xs text-on-surface-variant">
                                Official OTP verification login
                            </p>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 flex flex-col gap-4">

                        @csrf

                        <!-- Department ID -->
                        <div>
                            <label class="text-xs text-on-surface-variant block mb-1">
                                Department ID
                            </label>

                            <input name="department_id" required
                                class="w-full bg-surface border border-outline-variant rounded-lg px-3 py-2 text-sm focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 outline-none"
                                placeholder="Enter Official ID" type="text">
                        </div>

                        <!-- Mobile -->
                        <div>
                            <label class="text-xs text-on-surface-variant block mb-1">
                                Registered Mobile Number
                            </label>

                            <div class="flex">
                                <span
                                    class="px-3 flex items-center bg-surface-container border border-outline-variant border-r-0 rounded-l-lg text-sm text-on-surface-variant">
                                    +91
                                </span>

                                <input name="mobile" required
                                    class="w-full bg-surface border border-outline-variant rounded-r-lg px-3 py-2 text-sm focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 outline-none"
                                    placeholder="Enter Mobile Number" type="text">
                            </div>
                        </div>

                        <!-- OTP -->
                        <div>
                            <label class="text-xs text-on-surface-variant block mb-1">
                                OTP Verification
                            </label>

                            <div class="flex gap-2">

                                <input name="otp" required
                                    class="w-full bg-surface border border-outline-variant rounded-lg px-3 py-2 text-sm focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 outline-none"
                                    placeholder="Enter OTP" type="text">

                                <button type="button"
                                    class="bg-secondary hover:bg-secondary/90 text-white px-4 rounded-lg text-sm whitespace-nowrap">
                                    Send OTP
                                </button>

                            </div>
                        </div>

                        <!-- Notice -->
                        <div
                            class="bg-tertiary-fixed/20 border border-tertiary-fixed rounded-lg p-3 flex gap-2 items-start">

                            <span class="material-symbols-outlined text-tertiary text-base">
                                security
                            </span>

                            <p class="text-xs text-on-surface-variant">
                                Restricted access for authorized Haryana Government officials only.
                            </p>
                        </div>

                        <!-- Login -->
                        <button type="submit"
                            class="w-full bg-tertiary hover:bg-tertiary-container text-white font-medium py-2.5 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">

                            <span>Verify & Secure Login</span>

                            <span class="material-symbols-outlined text-sm">
                                lock
                            </span>
                        </button>

                    </div>
                </form>

            </div>
        </div>
    </main>
    <!-- Footer Component -->
    <footer class="bg-slate-800 text-slate-300 py-8 text-sm">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>© 2026 Department of Housing For All, Government of Haryana, India.</p>
            <p class="mt-2 text-slate-500">Designed & Developed by Citizen Resources Information Department, Haryana (CRID)</p>
        </div>
    </footer>
</body>

</html>
