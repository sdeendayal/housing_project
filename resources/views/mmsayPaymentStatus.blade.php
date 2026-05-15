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
    <nav
        class="hidden md:flex flex-col bg-surface text-primary font-label-md text-label-md border-r border-dotted border-outline-variant fixed left-0 top-0 h-full w-[260px] z-40">

        <!-- Logo Section -->
        <div class="px-gutter pt-6 pb-6 flex items-center gap-3 border-b border-outline-variant">

            <img alt="Haryana State Emblem" class="w-10 h-10 object-contain" src="Haryana_emblem.png" />

            <div>
                <h1 class="text-lg font-extrabold leading-tight text-primary">
                    Department of Housing For All
                </h1>

                <p class="text-xs text-on-surface-variant">
                    Government of Haryana
                </p>
            </div>
        </div>

        <!-- Menu -->
        <div class="flex-1 overflow-y-auto px-margin-mobile py-4">

            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2  text-on-primary-fixed border-l-4 border-primary font-bold hover:bg-surface-container-high transition-all"
                href="/mmsay.citizen.dashboard">

                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                    dashboard
                </span>

                Dashboard
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 bg-primary-fixed text-on-surface-variant hover:bg-surface-container-high transition-all"
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Purchase Date -->
                <div
                    class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300">

                    <p class="text-xs text-gray-500 mb-1">
                        Purchase Date
                    </p>

                    <h4 class="text-lg font-bold text-[#0B3B66]">
                        12 Oct 2023
                    </h4>

                    <div class="mt-3 text-[#0B5CAD]">
                        <span class="material-symbols-outlined text-[20px]">
                            calendar_today
                        </span>
                    </div>

                </div>
                <!-- Total Paid -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500 mb-2">
                        Total Paid Amount
                    </p>
                    <h4 class="text-lg font-bold text-green-700">
                        ₹ 14,50,000
                    </h4>
                    <div class="mt-5 w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                        <div class="bg-green-700 h-full w-[65%] rounded-full"></div>
                    </div>
                </div>
                <!-- Outstanding -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500 mb-2">
                        Total Outstanding
                    </p>
                    <h4 class="text-lg font-bold text-red-600">
                        ₹ 7,25,000
                    </h4>
                    <div class="mt-4 flex items-center gap-1 text-red-600 text-xs">
                        <span class="material-symbols-outlined text-[18px]">
                            warning
                        </span>
                        Due in 15 days
                    </div>
                </div>
                <!-- Status -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500 mb-2">
                        Flat/Plot Status
                    </p>
                    <h4 class="text-lg font-bold text-[#0B3B66]">
                        Allotted
                    </h4>
                    <div class="mt-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                            Category A-1
                        </span>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-8 flex flex-col gap-6">

                <!-- Payment Action Card -->
                <div
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 border-l-4 border-green-600">

                    <div>
                        <p class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">
                            Next Payment Due
                        </p>

                        <h2 class="text-2xl font-bold text-[#0B3B66]">
                            ₹ 1,20,000
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Due Date : 15 Nov 2024
                        </p>
                    </div>

                    <div class="flex gap-3 w-full md:w-auto">

                        <button
                            class="w-full md:w-auto px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold shadow-md transition-all duration-200">

                            Pay Now

                        </button>

                    </div>
                </div>


                <!-- Payment History -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">

                        <h3 class="text-lg font-bold text-[#0B3B66]">
                            Payment History
                        </h3>

                        <div class="flex gap-3">

                            <button class="p-2 rounded-lg hover:bg-gray-100 transition-all">

                                <span class="material-symbols-outlined text-gray-600">
                                    filter_list
                                </span>

                            </button>

                            <button class="p-2 rounded-lg hover:bg-gray-100 transition-all">

                                <span class="material-symbols-outlined text-gray-600">
                                    download
                                </span>

                            </button>

                        </div>
                    </div>


                    <!-- Table -->
                    <div class="overflow-x-auto">

                        <table class="w-full">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
                                        Receipt No.
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
                                        Date
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
                                        Amount
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
                                        Status
                                    </th>

                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-100">

                                <!-- Row -->
                                <tr class="hover:bg-gray-50 transition-all">

                                    <td class="px-4 py-3 font-medium text-[#0B3B66]">
                                        RCPT/2023/1029
                                    </td>

                                    <td class="px-4 py-3 text-xs text-gray-600">
                                        12 Oct 2023
                                    </td>

                                    <td class="px-4 py-3 font-semibold text-green-700">
                                        ₹ 5,00,000
                                    </td>

                                    <td class="px-4 py-3">

                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">

                                            Paid

                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        <button
                                            class="inline-flex items-center gap-1 text-[#0B5CAD] hover:underline text-xs font-medium">

                                            <span class="material-symbols-outlined text-[18px]">
                                                download
                                            </span>

                                            Receipt

                                        </button>

                                    </td>

                                </tr>


                                <!-- Row -->
                                <tr class="hover:bg-gray-50 transition-all">

                                    <td class="px-4 py-3 font-medium text-[#0B3B66]">
                                        RCPT/2024/0245
                                    </td>

                                    <td class="px-4 py-3 text-xs text-gray-600">
                                        15 Jan 2024
                                    </td>

                                    <td class="px-4 py-3 font-semibold text-green-700">
                                        ₹ 4,50,000
                                    </td>

                                    <td class="px-4 py-3">

                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">

                                            Paid

                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        <button
                                            class="inline-flex items-center gap-1 text-[#0B5CAD] hover:underline text-xs font-medium">

                                            <span class="material-symbols-outlined text-[18px]">
                                                download
                                            </span>

                                            Receipt

                                        </button>

                                    </td>

                                </tr>


                                <!-- Row -->
                                <tr class="hover:bg-gray-50 transition-all">

                                    <td class="px-4 py-3 font-medium text-[#0B3B66]">
                                        RCPT/2024/0591
                                    </td>

                                    <td class="px-4 py-3 text-xs text-gray-600">
                                        20 May 2024
                                    </td>

                                    <td class="px-4 py-3 font-semibold text-green-700">
                                        ₹ 5,00,000
                                    </td>

                                    <td class="px-4 py-3">

                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">

                                            Paid

                                        </span>

                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        <button
                                            class="inline-flex items-center gap-1 text-[#0B5CAD] hover:underline text-xs font-medium">

                                            <span class="material-symbols-outlined text-[18px]">
                                                download
                                            </span>

                                            Receipt

                                        </button>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- Notifications -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 border-l-4 border-[#0B5CAD]">

                    <h3 class="text-lg font-bold text-[#0B3B66] mb-5">
                        System Notifications
                    </h3>

                    <div class="flex gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 items-start">

                        <div class="w-10 h-10 rounded-full bg-[#0B5CAD] text-white flex items-center justify-center">

                            <span class="material-symbols-outlined text-[20px]">
                                info
                            </span>

                        </div>

                        <div>

                            <p class="font-semibold text-[#0B3B66]">
                                Allotment Certificate Available
                            </p>

                            <p class="text-xs text-gray-600 mt-1 leading-6">
                                Your allotment certificate for Sector 12 project is now available in the downloads
                                section.
                            </p>

                            <p class="text-xs text-gray-400 mt-2">
                                2 hours ago
                            </p>

                        </div>

                    </div>

                </div>

            </div>
        </main>
        <!-- Footer -->
        <footer class="bg-tertiary text-on-tertiary border-t border-tertiary-container w-full mt-auto">

            <div
                class="py-4 px-4 md:px-6 flex flex-col md:flex-row justify-between items-center max-w-[1280px] mx-auto gap-2">

                <div class="text-center md:text-left leading-5">

                    <p class="text-[11px] text-on-tertiary-container">
                        Designed & Developed by <b>Citizen Resources Information Department, Haryana (CRID)</b>
                    </p>

                    <p class="text-[11px] text-on-tertiary-container">
                        Content Owned by <b>Department of Housing For All</b>
                    </p>

                    <p class="text-[11px] text-on-tertiary-container mt-1">
                        <b>© 2026 Department of Housing For All, Government of Haryana, India.</b>
                    </p>

                </div>

            </div>

        </footer>
    </div>
</body>

</html>
