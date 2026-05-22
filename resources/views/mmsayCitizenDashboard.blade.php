<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>MMSAY Citizen Dashboard - Haryana Housing For All</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
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
                        "headline-lg": [
                            "Inter"
                        ],
                        "body-lg": [
                            "Inter"
                        ],
                        "label-sm": [
                            "Inter"
                        ],
                        "body-md": [
                            "Inter"
                        ],
                        "body-sm": [
                            "Inter"
                        ],
                        "headline-md": [
                            "Inter"
                        ],
                        "headline-xl": [
                            "Inter"
                        ],
                        "label-md": [
                            "Inter"
                        ]
                    },
                    "fontSize": {
                        "headline-lg": [
                            "32px",
                            {
                                "lineHeight": "40px",
                                "letterSpacing": "-0.01em",
                                "fontWeight": "700"
                            }
                        ],
                        "body-lg": [
                            "18px",
                            {
                                "lineHeight": "28px",
                                "fontWeight": "400"
                            }
                        ],
                        "label-sm": [
                            "12px",
                            {
                                "lineHeight": "16px",
                                "fontWeight": "500"
                            }
                        ],
                        "body-md": [
                            "16px",
                            {
                                "lineHeight": "24px",
                                "fontWeight": "400"
                            }
                        ],
                        "body-sm": [
                            "14px",
                            {
                                "lineHeight": "20px",
                                "fontWeight": "400"
                            }
                        ],
                        "headline-md": [
                            "24px",
                            {
                                "lineHeight": "32px",
                                "fontWeight": "600"
                            }
                        ],
                        "headline-xl": [
                            "40px",
                            {
                                "lineHeight": "48px",
                                "letterSpacing": "-0.02em",
                                "fontWeight": "700"
                            }
                        ],
                        "label-md": [
                            "14px",
                            {
                                "lineHeight": "16px",
                                "fontWeight": "600"
                            }
                        ]
                    }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }

        .material-symbols-outlined {
            font-size: 20px;
        }
    </style>
</head>

<body class="bg-background text-on-background min-h-screen flex flex-col md:flex-row">
    <!-- SideNavBar -->
   <nav id="sidebar"
    class="fixed md:flex flex-col bg-surface text-primary font-label-md text-label-md border-r border-dotted border-outline-variant left-0 top-0 h-full w-[260px] z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

      <!-- Premium Logo Section -->
<div
    class="relative overflow-hidden px-5 py-5 border-b border-gray-200/70 bg-gradient-to-br from-white via-[#f8fbff] to-[#eef4ff]">

    <!-- Decorative Blur -->
    <div
        class="absolute -top-10 -right-10 w-28 h-28 bg-blue-200/30 rounded-full blur-3xl">
    </div>

    <div
        class="absolute -bottom-10 -left-10 w-24 h-24 bg-indigo-200/20 rounded-full blur-3xl">
    </div>

    <!-- Content -->
    <div class="relative z-10 flex items-center gap-3">

        <!-- Logo -->
        <div
            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0B5CAD] to-[#003358] flex items-center justify-center shadow-lg shadow-blue-200/40 border border-white/30 shrink-0">

            <img alt="Haryana State Emblem"
                class="w-8 h-8 object-contain drop-shadow-sm"
                src="Haryana_emblem.png" />

        </div>

        <!-- Text -->
        <div>

            <h1
                class="text-sm font-extrabold leading-tight text-[#0B2C4D] tracking-wide">
                Department of Housing For All
            </h1>

            <div class="flex items-center gap-1 mt-1">

                <span
                    class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse">
                </span>

                <p class="text-[11px] font-medium text-gray-500 tracking-wide">
                    Government of Haryana
                </p>

            </div>
        </div>
    </div>
</div>

        <!-- Menu -->
        <div class="flex-1 overflow-y-auto px-margin-mobile py-4">

            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold hover:bg-surface-container-high transition-all"
                href="#">

                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                    dashboard
                </span>

                Dashboard
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
                href="/mmsay-payment-status">

                <span class="material-symbols-outlined">
                    payments
                </span>

                Payment Status

            </a>

            {{-- <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
                href="#">

                <span class="material-symbols-outlined">
                    description
                </span>

                Statutory Docs
            </a> --}}

            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
                href="#">

                <span class="material-symbols-outlined">
                    bolt
                </span>

                Quick Services
            </a>

            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
                href="#">

                <span class="material-symbols-outlined">
                    track_changes
                </span>

                Application Status
            </a>

            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
                href="#">

                <span class="material-symbols-outlined">
                    support_agent
                </span>

                Grievances
            </a>

        </div>

    </nav>

    <!-- Mobile Overlay -->
<div id="sidebarOverlay"
    class="fixed inset-0 bg-black/40 z-40 hidden md:hidden">
</div>
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col md:ml-[260px] min-h-screen">
        <!-- TopAppBar -->
        <header
            class="bg-primary text-on-primary font-headline-md text-headline-md font-label-md text-label-md docked full-width top-0 sticky backdrop-blur-md bg-opacity-90 border-b border-outline-variant shadow-sm z-50">
            <div
                class="flex justify-between items-center w-full px-margin-mobile md:px-gutter max-w-container-max mx-auto h-16">
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button -->
                   <button id="menuToggle"
    class="md:hidden text-on-primary hover:bg-on-primary-fixed-variant/20 p-2 rounded-full transition-colors">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <span class="text-lg md:text-lg font-semibold text-on-primary"> Citizen Dashboard</span>
                </div>


                <div class="flex items-center gap-2">
                    <a href="/mmsay-profile"
                        class="flex items-center gap-2 bg-[#0B5CAD] hover:bg-[#084B8A] text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200">

                        <span class="material-symbols-outlined text-[20px]">
                            account_circle
                        </span>

                        <span class="text-xs font-medium">
                            Profile
                        </span>

                    </a>

                    <a href="/mmsay-login"
                        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200">

                        <span class="material-symbols-outlined text-[20px]">
                            logout
                        </span>

                        <span class="text-xs font-medium">
                            Logout
                        </span>

                    </a>

                </div>
            </div>
        </header>
        <!-- Main Canvas -->
        <main class="flex-1 p-margin-mobile md:p-gutter max-w-container-max mx-auto w-full mt-16 md:mt-0 pb-stack-lg">





           <!-- Premium Welcome Bar -->
<div
    class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#ffffff] via-[#f8fbff] to-[#eef4ff] border border-white/30 shadow-[0_8px_30px_rgba(0,0,0,0.06)] px-4 md:px-6 py-4 mb-6 backdrop-blur-xl">

    <!-- Decorative Blur -->
    <div
        class="absolute -top-10 -right-10 w-32 h-32 bg-blue-200/30 rounded-full blur-3xl">
    </div>

    <div
        class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-200/20 rounded-full blur-3xl">
    </div>

    <!-- Content -->
    <div
        class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <!-- Left Side -->
        <div class="flex items-center gap-3">

            <!-- Avatar -->
            <div
                class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center shadow-lg shrink-0">

                <span class="material-symbols-outlined text-[20px]">
                    person
                </span>

            </div>

            <!-- Welcome Text -->
            <div>

                <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">
                    Welcome Back
                </p>

                <h3 class="text-sm md:text-base font-bold text-gray-800">
                    Anita Devi
                </h3>

            </div>
        </div>

        <!-- Right Side -->
        <div
            class="flex items-center gap-3 bg-white/70 border border-gray-100 px-4 py-3 rounded-xl shadow-sm">

            <div
                class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">

                <span class="material-symbols-outlined text-[18px]">
                    badge
                </span>

            </div>

            <div>

                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">
                    Application ID
                </p>

                <h4 class="text-sm font-bold text-primary tracking-wide">
                    HR-MMSAY-2023-8942
                </h4>

            </div>
        </div>
    </div>
</div>
           <!-- Compact Premium Application Status Tracker -->
<section
    class="relative overflow-hidden rounded-2xl border border-white/20 bg-gradient-to-br from-[#ffffff] via-[#f8fbff] to-[#eef4ff] p-4 md:p-5 shadow-[0_8px_30px_rgba(0,0,0,0.06)] backdrop-blur-xl mb-5">

    <!-- Blur Effects -->
    <div
        class="absolute -top-16 -right-16 w-52 h-52 bg-blue-200/20 rounded-full blur-3xl pointer-events-none">
    </div>

    <div
        class="absolute -bottom-16 -left-16 w-52 h-52 bg-indigo-200/20 rounded-full blur-3xl pointer-events-none">
    </div>

    <!-- Header -->
    <div class="relative z-10 flex items-center justify-between mb-7">

        <div>
            <h2 class="text-lg md:text-xl font-bold text-gray-800">
                Application Status
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Track your application progress
            </p>
        </div>

        <div
            class="hidden md:flex items-center gap-2 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">

            <span class="w-2 h-2 rounded-full bg-green-600 animate-pulse"></span>
            Live

        </div>
    </div>

    <!-- Timeline -->
    <div
        class="relative z-10 flex flex-col lg:flex-row justify-between gap-7 lg:gap-0">

        <!-- Desktop Line -->
        <div
            class="hidden lg:block absolute top-5 left-0 right-0 h-[4px] bg-gray-200 rounded-full">
        </div>

        <div
            class="hidden lg:block absolute top-5 left-0 w-[58%] h-[4px] bg-gradient-to-r from-green-500 to-blue-600 rounded-full">
        </div>

        <!-- Mobile Line -->
        <div
            class="lg:hidden absolute left-[15px] top-0 bottom-0 w-[4px] bg-gray-200 rounded-full">
        </div>

        <div
            class="lg:hidden absolute left-[15px] top-0 h-[55%] w-[4px] bg-gradient-to-b from-green-500 to-blue-600 rounded-full">
        </div>

        <!-- Step -->
        <div class="relative flex lg:flex-col items-start lg:items-center gap-3 z-10">

            <div
                class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white flex items-center justify-center shadow-md shrink-0">

                <span class="material-symbols-outlined text-[18px]"
                    style="font-variation-settings:'FILL' 1;">
                    check
                </span>

            </div>

            <div class="lg:text-center">

                <h4 class="font-semibold text-sm text-gray-800">
                    Submitted
                </h4>

                <p class="text-[11px] text-gray-500">
                    12 Oct 2023
                </p>

            </div>
        </div>

        <!-- Step -->
        <div class="relative flex lg:flex-col items-start lg:items-center gap-3 z-10">

            <div
                class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white flex items-center justify-center shadow-md shrink-0">

                <span class="material-symbols-outlined text-[18px]"
                    style="font-variation-settings:'FILL' 1;">
                    verified
                </span>

            </div>

            <div class="lg:text-center">

                <h4 class="font-semibold text-sm text-gray-800">
                    Verified
                </h4>

                <p class="text-[11px] text-gray-500">
                    28 Oct 2023
                </p>

            </div>
        </div>

        <!-- Active Step -->
        <div class="relative flex lg:flex-col items-start lg:items-center gap-3 z-10">

            <div class="relative">

                <div
                    class="absolute inset-0 rounded-xl bg-blue-500 blur-lg opacity-30 animate-pulse">
                </div>

                <div
                    class="relative w-10 h-10 md:w-12 md:h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center shadow-xl ring-2 ring-blue-100 shrink-0">

                    <span class="material-symbols-outlined text-[20px]">
                        real_estate_agent
                    </span>

                </div>
            </div>

            <div class="lg:text-center">

                <h4 class="font-bold text-sm text-blue-700">
                    Allotted
                </h4>

                <p class="text-[11px] text-blue-500">
                    In Progress
                </p>

            </div>
        </div>

        <!-- Step -->
        <div class="relative flex lg:flex-col items-start lg:items-center gap-3 z-10">

            <div
                class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white border border-gray-200 text-gray-400 flex items-center justify-center shadow-sm shrink-0">

                <span class="material-symbols-outlined text-[18px]">
                    pending
                </span>

            </div>

            <div class="lg:text-center">

                <h4 class="font-medium text-sm text-gray-400">
                    Pending
                </h4>

            </div>
        </div>

        <!-- Step -->
        <div class="relative flex lg:flex-col items-start lg:items-center gap-3 z-10">

            <div
                class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white border border-gray-200 text-gray-400 flex items-center justify-center shadow-sm shrink-0">

                <span class="material-symbols-outlined text-[18px]">
                    task_alt
                </span>

            </div>

            <div class="lg:text-center">

                <h4 class="font-medium text-sm text-gray-400">
                    Registered
                </h4>

            </div>
        </div>
    </div>
</section>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

                <!-- Purchase Date -->
                <div
                    class="bg-gradient-to-br from-white to-gray-50 border border-white/40 rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-300/30 hover:-translate-y-2 transition-all duration-500 backdrop-blur-md">

                    <p class="text-sm font-medium text-gray-500 mb-2">
                        Purchase Date
                    </p>

                    <h4 class="text-xl font-bold text-[#0B3B66]">
                        12 Oct 2023
                    </h4>

                    <div class="mt-2 text-[#0B5CAD]">

                        <span class="material-symbols-outlined text-[28px]">
                            calendar_today
                        </span>

                    </div>
                </div>

                <!-- Total Paid -->
                <div
                    class="bg-gradient-to-br from-white to-gray-50 border border-white/40 rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-300/30 hover:-translate-y-2 transition-all duration-500 backdrop-blur-md">

                    <p class="text-sm font-medium text-gray-500 mb-2">
                        Total Paid Amount
                    </p>

                    <h4 class="text-xl font-bold text-green-700">
                        ₹ 14,50,000
                    </h4>

                    <div class="mt-2 w-full bg-gray-200 h-2 rounded-full overflow-hidden">

                        <div class="bg-green-700 h-full w-[65%] rounded-full"></div>

                    </div>
                </div>

                <!-- Outstanding -->
                <div
                   class="bg-gradient-to-br from-white to-gray-50 border border-white/40 rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-300/30 hover:-translate-y-2 transition-all duration-500 backdrop-blur-md">

                    <p class="text-sm font-medium text-gray-500 mb-2">
                        Total Outstanding
                    </p>

                    <h4 class="text-xl font-bold text-red-600">
                        ₹ 7,25,000
                    </h4>

                    <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">

                        <span class="material-symbols-outlined text-[18px]">
                            warning
                        </span>

                        Due in 15 days

                    </div>
                </div>

                <!-- Status -->
                <div
                    class="bg-gradient-to-br from-white to-gray-50 border border-white/40 rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-300/30 hover:-translate-y-2 transition-all duration-500 backdrop-blur-md">

                    <p class="text-sm font-medium text-gray-500 mb-2">
                        Flat/Plot Status
                    </p>

                    <h4 class="text-xl font-bold text-[#0B3B66]">
                        Allotted
                    </h4>

                    <div class="mt-2">

                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">

                            Category A-1

                        </span>

                    </div>
                </div>
            </div>
        </main>
        <!-- Premium Compact Footer -->
<footer
    class="relative overflow-hidden bg-gradient-to-r from-[#0B2C4D] via-[#123B63] to-[#0B2C4D] text-white border-t border-white/10 mt-auto">

    <!-- Blur Effect -->
    <div
        class="absolute -top-10 right-0 w-40 h-40 bg-blue-400/10 rounded-full blur-3xl">
    </div>

    <div
        class="max-w-[1280px] mx-auto px-4 md:px-6 py-3 relative z-10">

        <div
            class="flex flex-col md:flex-row items-center justify-between gap-3">

            <!-- Left Side -->
            <div class="text-center md:text-left">

                <h4 class="text-sm font-semibold tracking-wide text-white">
                    Department of Housing For All
                </h4>

                <p class="text-[11px] text-blue-100 mt-1 leading-5">
                    Government of Haryana, India
                </p>

            </div>

            <!-- Center -->
            <div
                class="hidden md:block w-px h-10 bg-white/10">
            </div>

            <!-- Right Side -->
            <div class="text-center md:text-right leading-5">

                <p class="text-[11px] text-blue-100">
                    Designed & Developed by
                    <span class="font-semibold text-white">
                        CRID Haryana
                    </span>
                </p>

                <p class="text-[11px] text-blue-100">
                    © 2026 All Rights Reserved
                </p>

            </div>
        </div>
    </div>
</footer>
    </div>
</body>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>

</html>
