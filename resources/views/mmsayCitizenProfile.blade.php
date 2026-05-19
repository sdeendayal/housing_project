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
        }
    </style>
</head>

<body class="bg-background text-on-background min-h-screen flex flex-col md:flex-row">
    <!-- SideNavBar -->
    <nav
        class="hidden md:flex flex-col bg-surface text-primary font-label-md text-label-md border-r border-dotted border-outline-variant fixed left-0 top-0 h-full w-[260px] z-40">

        <!-- Logo Section -->
        <div class="px-gutter pt-6 pb-6 flex items-center gap-3 border-b border-outline-variant">

            <img alt="Haryana State Emblem" class="w-10 h-10 object-contain" src="Haryana_emblem.png" />

            <div>
                <h1 class="text-lg font-extrabold leading-tight text-primary">
                    Department of Housing For All
                </h1>

                <p class="text-sm text-on-surface-variant">
                    Government of Haryana
                </p>
            </div>
        </div>

        <!-- Menu -->
        <div class="flex-1 overflow-y-auto px-margin-mobile py-4">

            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold hover:bg-surface-container-high transition-all"
                href="/mmsay.citizen.dashboard">

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
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col md:ml-[260px] min-h-screen">
        <!-- TopAppBar -->
        <header
            class="bg-primary text-on-primary font-headline-md text-headline-md font-label-md text-label-md docked full-width top-0 sticky backdrop-blur-md bg-opacity-90 border-b border-outline-variant shadow-sm z-50">
            <div
                class="flex justify-between items-center w-full px-margin-mobile md:px-gutter max-w-container-max mx-auto h-16">
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button -->
                    <button
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
            <!-- Profile Page -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <!-- Compact Header -->
                <div class="bg-[#0B3B66] px-4 py-3 flex items-center justify-between">

                    <div class="flex items-center gap-3">

                        <!-- Small Avatar -->
                        <div class="w-12 h-12 rounded-full bg-white/15 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-white text-3xl">
                                account_circle
                            </span>
                        </div>

                        <!-- User Info -->
                        <div class="leading-tight">

                            <h2 class="text-base md:text-lg font-semibold text-white tracking-wide">
                                ANITA DEVI
                            </h2>

                            <p class="text-[11px] md:text-xs text-blue-100 mt-0.5">
                                App No : HR-MMSAY-274751
                            </p>

                        </div>
                    </div>

                    <!-- Status Badge -->
                    <span
                        class="hidden md:inline-flex items-center px-3 py-1 rounded-full bg-green-500/20 text-green-100 text-xs font-medium border border-green-300/20">
                        Active
                    </span>

                </div>


                <!-- Profile Content -->
                <div class="p-4">

                    <!-- Compact Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <!-- Item -->
                        <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                                Full Name
                            </p>

                            <h4 class="text-sm font-semibold text-[#0B3B66]">
                                ANITA DEVI
                            </h4>
                        </div>

                        <!-- Item -->
                        <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                                Father Name
                            </p>

                            <h4 class="text-sm font-semibold text-[#0B3B66]">
                                DALSINGHAR
                            </h4>
                        </div>

                        <!-- Mobile -->
                        <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                                Mobile
                            </p>

                            <h4 class="text-sm font-semibold text-[#0B3B66]">
                                8950886886
                            </h4>
                        </div>

                        <!-- Aadhaar -->
                        <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                                Aadhaar
                            </p>

                            <h4 class="text-sm font-semibold text-[#0B3B66]">
                                XXXX-XXXX-8081
                            </h4>
                        </div>

                        <!-- Category -->
                        <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                                Category
                            </p>

                            <span
                                class="inline-block bg-blue-100 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                Ghumantu
                            </span>
                        </div>

                        <!-- District -->
                        <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                                District
                            </p>

                            <h4 class="text-sm font-semibold text-[#0B3B66]">
                                ROHTAK MC
                            </h4>
                        </div>

                        <!-- Income -->
                        <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                                Annual Income
                            </p>

                            <h4 class="text-sm font-semibold text-green-700">
                                ₹ 1.40 - 1.80 Lakh
                            </h4>
                        </div>

                        <!-- App Number -->
                        <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                                Application No.
                            </p>

                            <h4 class="text-sm font-semibold text-[#0B3B66]">
                                274751
                            </h4>
                        </div>

                    </div>


                    <!-- Address -->
                    <div class="mt-3 border border-gray-200 rounded-lg p-3 bg-gray-50">

                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">
                            Residential Address
                        </p>

                        <p class="text-sm font-medium text-[#0B3B66] leading-5">
                            231 J P COLONY HISAR ROAD ROHTAK 124001
                        </p>

                    </div>

                </div>

            </div>









        </main>
        <!-- Footer -->
        <footer class="bg-tertiary text-on-tertiary border-t border-tertiary-container w-full mt-auto">

            <div
                class="py-4 px-4 md:px-6 flex flex-col md:flex-row justify-between items-center max-w-[1280px] mx-auto gap-2">

                <div class="text-center md:text-left leading-5">

                    <p class="text-xs text-on-tertiary-container">
                        Designed & Developed by <b>Citizen Resources Information Department, Haryana (CRID)</b>
                    </p>

                    <p class="text-xs text-on-tertiary-container">
                        Content Owned by <b>Department of Housing For All</b>
                    </p>

                    <p class="text-xs text-on-tertiary-container mt-1">
                        <b>© 2026 Department of Housing For All, Government of Haryana, India.</b>
                    </p>

                </div>

            </div>

        </footer>
    </div>
</body>

</html>
