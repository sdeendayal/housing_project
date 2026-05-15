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
                    <span class="font-headline-md text-headline-md font-bold text-on-primary">Citizen Dashboard
                    </span>
                </div>


                <div class="flex items-center gap-2">
                    <a href="/mmsay-profile"
                        class="flex items-center gap-2 bg-[#0B5CAD] hover:bg-[#084B8A] text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200">

                        <span class="material-symbols-outlined text-[20px]">
                            account_circle
                        </span>

                        <span class="text-sm font-medium">
                            Profile
                        </span>

                    </a>

                    <a href="/mmsay-login"
                        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200">

                        <span class="material-symbols-outlined text-[20px]">
                            logout
                        </span>

                        <span class="text-sm font-medium">
                            Logout
                        </span>

                    </a>

                </div>
            </div>
        </header>
        <!-- Main Canvas -->
        <main class="flex-1 p-margin-mobile md:p-gutter max-w-container-max mx-auto w-full mt-16 md:mt-0 pb-stack-lg">





            <div class="mb-stack-lg">
                <h4><b>Welcome back, Anita Devi</b></h4>
                <p class="font-body-lg text-body-lg text-on-surface-variant">Application ID: <span
                        class="font-bold text-primary">HR-MMSAY-2023-8942</span></p>
            </div>
            <!-- Status Tracker -->
            <section
                class="bg-surface-container-lowest rounded-xl p-stack-md md:p-gutter shadow-sm border border-border-gray mb-stack-lg relative overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-primary-fixed/20 to-transparent pointer-events-none">
                </div>
                <h3 class="font-headline-md text-headline-md text-on-surface mb-stack-md relative z-10">Application
                    Status</h3>
                <div
                    class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center mt-stack-lg gap-6 md:gap-0">
                    <!-- Progress Line background -->
                    <div class="hidden md:block absolute top-6 left-8 right-8 h-1 bg-surface-variant z-0 rounded-full">
                    </div>
                    <!-- Progress Line active -->
                    <div class="hidden md:block absolute top-6 left-8 w-1/2 h-1 bg-success-green z-0 rounded-full">
                    </div>
                    <!-- Mobile Progress Line -->
                    <div class="md:hidden absolute left-[27px] top-12 bottom-4 w-1 bg-surface-variant z-0 rounded-full">
                    </div>
                    <div class="md:hidden absolute left-[27px] top-12 h-1/2 w-1 bg-success-green z-0 rounded-full">
                    </div>
                    <!-- Step 1 -->
                    <div class="flex md:flex-col items-center gap-4 md:gap-2 z-10 w-full md:w-auto">
                        <div
                            class="w-10 h-10 rounded-full bg-success-green text-on-primary flex items-center justify-center shadow-sm shrink-0">
                            <span class="material-symbols-outlined"
                                style="font-variation-settings: 'FILL' 1;">check</span>
                        </div>
                        <div class="md:text-center">
                            <p class="font-label-md text-label-md text-on-surface">Submitted</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">12 Oct 2023</p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="flex md:flex-col items-center gap-4 md:gap-2 z-10 w-full md:w-auto">
                        <div
                            class="w-10 h-10 rounded-full bg-success-green text-on-primary flex items-center justify-center shadow-sm shrink-0">
                            <span class="material-symbols-outlined"
                                style="font-variation-settings: 'FILL' 1;">check</span>
                        </div>
                        <div class="md:text-center">
                            <p class="font-label-md text-label-md text-on-surface">Verified</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">28 Oct 2023</p>
                        </div>
                    </div>
                    <!-- Step 3 (Current) -->
                    <div class="flex md:flex-col items-center gap-4 md:gap-2 z-10 w-full md:w-auto">
                        <div
                            class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-md ring-4 ring-primary/20 shrink-0 relative">
                            <span class="material-symbols-outlined">real_estate_agent</span>
                            <span class="absolute w-full h-full rounded-full animate-ping bg-primary opacity-20"></span>
                        </div>
                        <div class="md:text-center">
                            <p class="font-label-md text-label-md text-primary font-bold">Allotted</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">In Progress</p>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div class="flex md:flex-col items-center gap-4 md:gap-2 z-10 w-full md:w-auto">
                        <div
                            class="w-10 h-10 rounded-full bg-surface-variant text-outline flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined">hourglass_empty</span>
                        </div>
                        <div class="md:text-center">
                            <p class="font-label-md text-label-md text-outline">Pending</p>
                        </div>
                    </div>
                    <!-- Step 5 -->
                    <div class="flex md:flex-col items-center gap-4 md:gap-2 z-10 w-full md:w-auto">
                        <div
                            class="w-10 h-10 rounded-full bg-surface-variant text-outline flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined">verified</span>
                        </div>
                        <div class="md:text-center">
                            <p class="font-label-md text-label-md text-outline">Registered</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                <!-- Purchase Date -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

                    <p class="text-sm text-gray-500 mb-2">
                        Purchase Date
                    </p>

                    <h4 class="text-2xl font-bold text-[#0B3B66]">
                        12 Oct 2023
                    </h4>

                    <div class="mt-4 text-[#0B5CAD]">
                        <span class="material-symbols-outlined">
                            calendar_today
                        </span>
                    </div>

                </div>


                <!-- Total Paid -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

                    <p class="text-sm text-gray-500 mb-2">
                        Total Paid Amount
                    </p>

                    <h4 class="text-2xl font-bold text-green-700">
                        ₹ 14,50,000
                    </h4>

                    <div class="mt-5 w-full bg-gray-200 h-2 rounded-full overflow-hidden">

                        <div class="bg-green-700 h-full w-[65%] rounded-full"></div>

                    </div>

                </div>


                <!-- Outstanding -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

                    <p class="text-sm text-gray-500 mb-2">
                        Total Outstanding
                    </p>

                    <h4 class="text-2xl font-bold text-red-600">
                        ₹ 7,25,000
                    </h4>

                    <div class="mt-4 flex items-center gap-1 text-red-600 text-sm">

                        <span class="material-symbols-outlined text-[18px]">
                            warning
                        </span>

                        Due in 15 days

                    </div>

                </div>


                <!-- Status -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

                    <p class="text-sm text-gray-500 mb-2">
                        Flat/Plot Status
                    </p>

                    <h4 class="text-2xl font-bold text-[#0B3B66]">
                        Allotted
                    </h4>

                    <div class="mt-4">

                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">

                            Category A-1

                        </span>

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
