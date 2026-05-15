<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Department of Housing For All</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style data-purpose="custom-utilities">
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        civic: {
                            blue: '#1a365d',
                            light: '#f8fafc',
                            accent: '#3b82f6',
                            highlight: '#fbbf24'
                        },
                        "tertiary": "#002713",
                        "surface": "#f7fafc",
                        "inverse-primary": "#adc7f7",
                        "primary": "#002045",
                        "surface-dim": "#d7dadc",
                        "primary-container": "#1a365d",
                        "surface-container-low": "#f1f4f6",
                        "on-surface": "#181c1e",
                        "tertiary-container": "#003f23",
                        "on-tertiary-container": "#4bb278",
                        "inverse-on-surface": "#eef1f3",
                        "on-surface-variant": "#43474e",
                        "on-error-container": "#93000a",
                        "surface-container-high": "#e5e9eb",
                        "surface-variant": "#e0e3e5",
                        "surface-container-lowest": "#ffffff",
                        "outline": "#74777f",
                        "on-primary-fixed-variant": "#2d476f",
                        "secondary-fixed": "#d3e4ff",
                        "primary-fixed": "#d6e3ff",
                        "inverse-surface": "#2d3133",
                        "on-error": "#ffffff",
                        "surface-bright": "#f7fafc",
                        "on-background": "#181c1e",
                        "on-secondary-container": "#00477f",
                        "on-primary-container": "#86a0cd",
                        "on-tertiary-fixed": "#002110",
                        "on-tertiary": "#ffffff",
                        "surface-tint": "#455f88",
                        "on-secondary-fixed": "#001c38",
                        "secondary": "#1960a3",
                        "tertiary-fixed": "#91f8b8",
                        "secondary-fixed-dim": "#a2c9ff",
                        "on-secondary": "#ffffff",
                        "background": "#f7fafc",
                        "on-secondary-fixed-variant": "#004881",
                        "on-primary": "#ffffff",
                        "on-tertiary-fixed-variant": "#00522f",
                        "tertiary-fixed-dim": "#74db9d",
                        "secondary-container": "#7db6ff",
                        "error-container": "#ffdad6",
                        "surface-container-highest": "#e0e3e5",
                        "surface-container": "#ebeef0",
                        "primary-fixed-dim": "#adc7f7",
                        "error": "#ba1a1a",
                        "outline-variant": "#c4c6cf",
                        "on-primary-fixed": "#001b3c"
                    }
                }
            }
        }
    </script>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased">
    <!-- BEGIN: Top Utility Bar -->
    <div class="bg-civic-blue text-white text-xs py-1 px-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="hidden sm:block"></div>
            <div class="flex space-x-4 items-center">

                <a class="hover:text-civic-highlight" href="#">Skip to main content</a>
                <div class="flex space-x-2">
                    <button aria-label="Decrease font size" class="hover:text-civic-highlight">A-</button>
                    <button aria-label="Default font size" class="hover:text-civic-highlight">A</button>
                    <button aria-label="Increase font size" class="hover:text-civic-highlight">A+</button>
                    <a class="hover:text-civic-highlight" href="#">Site Map</a>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Top Utility Bar -->
    <!-- BEGIN: Header -->
    <header
        class="text-on-primary docked full-width top-0 sticky border-b border-outline-variant shadow-md z-50
bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] 
bg-[url('../header-tp-bg.png')] 
bg-no-repeat bg-right bg-cover">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Placeholder for Department Logo -->
                <img alt="Department Logo" class="h-16 w-16 object-contain" src="Haryana_emblem.png" />
                <div>
                    <h1 class="text-2xl font-bold leading-tight">Department of Housing For All</h1>
                    <p class="text-sm opacity-90">Government of Haryana</p>
                </div>
            </div>
            <!-- National Emblem Placeholder -->
            <img alt="National Emblem" class="h-16 w-auto object-contain hidden md:block" src="emblem-black.png" />
        </div>
    </header>
    <!-- END: Header -->
    <!-- BEGIN: Navigation -->
    <nav class="bg-white border-b border-slate-200 shadow-sm relative z-40">
        <div class="max-w-7xl mx-auto px-4">
            <div
                class="flex flex-wrap items-center justify-center md:justify-start space-x-1 md:space-x-6 py-2 text-center">
                <a class="px-3 py-2 text-sm font-medium text-civic-blue border-b-2 border-civic-accent" href="#">
                    Home</a>
                <a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">About Us</a>
                <a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">Our Vision</a>
                <a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">Gallery</a>
                <!-- Highlighted Button -->
                <a class="px-4 py-2 text-sm font-bold text-civic-blue bg-civic-highlight hover:bg-yellow-500 rounded-md shadow-sm transition-colors uppercase tracking-wide"
                    href="#">
                    Help
                </a>
                <!-- <a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors" href="#">Contact Us</a>
<a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors" href="#">Suggestion Box</a>
<div class="relative ml-auto flex-1 md:flex-none mt-2 md:mt-0 w-full md:w-auto">
<input class="w-full md:w-48 pl-3 pr-8 py-1.5 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-civic-accent focus:border-civic-accent" placeholder="Search..." type="text"/>
<svg class="w-4 h-4 text-slate-400 absolute right-2.5 top-2" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg> -->
            </div>
        </div>
        </div>
    </nav>
    <!-- END: Navigation -->
    <!-- BEGIN: Scrolling Ticker -->
    <div class="bg-green-600 text-white py-1.5 px-4 overflow-hidden border-t-2 border-white relative z-30">
        <div class="whitespace-nowrap animate-[marquee_25s_linear_infinite] text-sm font-medium">
            <span class="bg-red-500 text-white px-1 py-0.5 rounded text-xs mr-2 animate-pulse">NEW</span>
            Immediate action required for MMSAY applications submitted before Q3. Final list for MMGAY phase 2 is now
            available for download.
        </div>
    </div>
    <style data-purpose="marquee-animation">
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }
    </style>
    <!-- END: Scrolling Ticker -->
    <!-- BEGIN: Main Content Layout -->
    <main class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- LEFT SIDEBAR -->
        <aside class="space-y-6 lg:col-span-1">
            <!-- Panel: Statutory Docs -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                <div class="bg-sky-500 text-white px-4 py-2 font-semibold text-sm">
                    Scheme Documents
                </div>
                <ul class="divide-y divide-slate-100 text-sm">
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue hover:underline">Notifications &amp;
                                Circulars</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Panel: Data Download -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                <div class="bg-green-500 text-white px-4 py-2 font-semibold text-sm">
                    Data Download
                </div>
                <ul class="divide-y divide-slate-100 text-sm">
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue hover:underline">List of Approved MMSAY
                                Beneficiaries</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue hover:underline">MMGAY Progress
                                Reports</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <div>
                                <span class="text-slate-700 hover:text-civic-blue hover:underline">Application Forms
                                    Download</span>
                                <span
                                    class="inline-block bg-red-500 text-white text-[10px] px-1 rounded ml-1">NEW</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Panel: Public Notice -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                <div class="bg-sky-400 text-white px-4 py-2 font-semibold text-sm">
                    Important Public Notice
                </div>
                <ul class="divide-y divide-slate-100 text-sm">
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700">ALERT: Beware of fraudulent calls regarding housing allotment
                                fees.</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700">Extension of deadline for document verification for Phase 1
                                applicants.</span>
                        </a>
                    </li>
                </ul>
                <div class="bg-slate-50 px-4 py-2 text-right border-t border-slate-100">
                    <a class="text-sm text-sky-600 hover:underline" href="#">View more</a>
                </div>
            </div>
        </aside>
        <!-- CENTER AREA -->
        <section class="lg:col-span-2 space-y-6">
            <!-- Scheme Compact Cards -->
            <div class="grid grid-cols-3 gap-4 mb-6">

                <!-- MMSAY -->
                <div class="relative group">

                    <a href="#"
                        class="bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-blue-50 transition-all duration-300">

                        <span
                            class="material-symbols-outlined text-primary text-4xl group-hover:scale-110 transition-transform duration-300"
                            style="font-variation-settings: 'FILL' 0;">
                            apartment
                        </span>

                        <div>
                            <div class="text-sm font-bold text-primary">
                                MMSAY
                            </div>
                        </div>
                    </a>

                    <!-- Dropdown -->
                    <div
                        class="absolute left-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-50 overflow-hidden">

                        <a href="/mmsay-login"
                            class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 text-sm font-medium text-gray-700 transition-all" target="_blank">

                            <span class="material-symbols-outlined text-[18px]">
                                person
                            </span>

                            Citizen Login
                        </a>

                        <a href="/mmsay-login"
                            class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100" target="_blank">

                            <span class="material-symbols-outlined text-[18px]">
                                business
                            </span>

                            Department Login
                        </a>

                    </div>

                </div>


                <!-- MMGAY -->
                <div class="relative group">

                    <a href="#"
                        class="bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-green-50 transition-all duration-300">

                        <span
                            class="material-symbols-outlined text-secondary text-4xl group-hover:scale-110 transition-transform duration-300"
                            style="font-variation-settings: 'FILL' 0;">
                            domain
                        </span>

                        <div>
                            <div class="text-sm font-bold text-primary">
                                MMGAY
                            </div>
                        </div>
                    </a>

                    <!-- Dropdown -->
                    <div
                        class="absolute left-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-50 overflow-hidden">

                        <a href="#"
                            class="flex items-center gap-2 px-4 py-3 hover:bg-green-50 text-sm font-medium text-gray-700 transition-all">

                            <span class="material-symbols-outlined text-[18px]">
                                person
                            </span>

                            Citizen Login
                        </a>

                        <a href="#"
                            class="flex items-center gap-2 px-4 py-3 hover:bg-green-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100">

                            <span class="material-symbols-outlined text-[18px]">
                                business
                            </span>

                            Department Login
                        </a>

                    </div>

                </div>


                <!-- EWS -->
                <div class="relative group">

                    <a href="#"
                        class="bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-orange-50 transition-all duration-300">

                        <span
                            class="material-symbols-outlined text-on-tertiary-container text-4xl group-hover:scale-110 transition-transform duration-300"
                            style="font-variation-settings: 'FILL' 0;">
                            foundation
                        </span>

                        <div>
                            <div class="text-sm font-bold text-primary">
                                EWS
                            </div>
                        </div>
                    </a>

                    <!-- Dropdown -->
                    <div
                        class="absolute left-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-50 overflow-hidden">

                        <a href="#"
                            class="flex items-center gap-2 px-4 py-3 hover:bg-orange-50 text-sm font-medium text-gray-700 transition-all">

                            <span class="material-symbols-outlined text-[18px]">
                                person
                            </span>

                            Citizen Login
                        </a>

                        <a href="#"
                            class="flex items-center gap-2 px-4 py-3 hover:bg-orange-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100">

                            <span class="material-symbols-outlined text-[18px]">
                                business
                            </span>

                            Department Login
                        </a>

                    </div>

                </div>

            </div>
            <!-- Main Visual / Carousel Area -->
            <div
                class="bg-white rounded-lg shadow-sm border border-surface-container-highest p-6 mb-6 flex flex-col items-center">
                {{-- <h2 class="text-lg font-bold text-primary mb-4">HOUSING DEVELOPMENT MAP</h2> --}}
                <div
                    class="relative w-full aspect-video bg-surface-container flex items-center justify-center rounded overflow-hidden group">
                    <img alt="Map"
                        class="w-full h-full object-contain group-hover:scale-105 transition-transform"
                        src="banner.jpeg" />
                    <div
                        class="absolute inset-0 flex items-center justify-between px-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span
                            class="material-symbols-outlined text-white bg-black/30 rounded-full p-1">chevron_left</span><span
                            class="material-symbols-outlined text-white bg-black/30 rounded-full p-1">chevron_right</span>
                    </div>
                </div>
                {{-- <p class="mt-4 text-secondary text-sm font-medium hover:underline cursor-pointer">Click to enlarge</p> --}}
            </div>
            <!-- What's New Section -->
            <div class="bg-white rounded-lg shadow-sm border border-surface-container-highest overflow-hidden">
                <div
                    class="px-4 py-2 bg-surface-container-low border-b border-surface-container-highest flex justify-between items-center">
                    <h3 class="text-sm font-bold text-primary uppercase">What's New</h3><a
                        class="text-xs text-secondary font-medium hover:underline" href="#">View All</a>
                </div>
                <div class="divide-y divide-surface-container-low max-h-48 overflow-y-auto custom-scroll">
                    <div class="p-3 flex items-start space-x-3"><span
                            class="material-symbols-outlined text-secondary-container text-sm">calendar_today</span>
                        <div>
                            <div class="text-[10px] text-on-surface-variant mb-0.5">05 May, 2024</div>
                            <p class="text-xs text-on-surface font-medium">Sector 12 EWS draw results announced.</p>
                        </div>
                    </div>
                    <div class="p-3 flex items-start space-x-3"><span
                            class="material-symbols-outlined text-secondary-container text-sm">calendar_today</span>
                        <div>
                            <div class="text-[10px] text-on-surface-variant mb-0.5">28 Apr, 2024</div>
                            <p class="text-xs text-on-surface font-medium">MMGAY Phase 3 online applications open.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- RIGHT SIDEBAR -->
        <aside class="space-y-6 lg:col-span-1">
            <!-- Panel: e-Governance -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                <div class="bg-sky-500 text-white px-4 py-2 font-semibold text-sm">
                    e-Services
                </div>
                <ul class="divide-y divide-slate-100 text-sm">
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-center space-x-3" href="#">
                            <span class="bg-green-100 text-green-600 p-1.5 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 font-medium hover:text-civic-blue">New Beneficiary? Register
                                Here</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-center space-x-3" href="#">
                            <span class="bg-blue-100 text-blue-600 p-1.5 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 font-medium hover:text-civic-blue">Applicant Login</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50 bg-blue-50/50">
                        <div class="flex items-start space-x-3">
                            <span class="text-sky-500 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <div>
                                <span class="text-slate-800 font-medium block mb-1">Track Application Status</span>
                                <ul class="text-xs text-slate-500 space-y-1 list-disc pl-4">
                                    <li>Help Desk support</li>
                                    <li>Call at: 1800-123-4567</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-center space-x-3" href="#">
                            <span class="text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 hover:text-civic-blue hover:underline">List of
                                e-Services</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-center space-x-3" href="#">
                            <span class="text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 hover:text-civic-blue hover:underline">Verify Payment</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-center space-x-3" href="#">
                            <span class="text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 hover:text-civic-blue hover:underline">Department Login</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Panel: Citizen Services -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                <div class="bg-green-500 text-white px-4 py-2 font-semibold text-sm">
                    Citizen Services: Docs
                </div>
                <ul class="divide-y divide-slate-100 text-sm">
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue">Citizen Charter</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue">Housing Scheme Dashboard</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue"><span
                                    class="text-red-500 font-semibold">PROCEDURE</span> for Application</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue"><span
                                    class="text-red-500 font-semibold">CHECKLIST</span> of required documents</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
    </main>
    <!-- END: Main Content Layout -->
    <!-- BEGIN: Bottom Grid Navigation -->
    <section class="max-w-7xl mx-auto px-4 pb-12">
        <div
            class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-0.5 bg-surface-container-highest border border-surface-container-highest rounded-lg overflow-hidden">
            <a class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                href="#">RERA Portal</a><a
                class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                href="#">Building Code</a><a
                class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                href="#">Act/Rules</a><a
                class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                href="#">Fees &amp; Charges</a><a
                class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                href="#">TDR Calculator</a><a
                class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                href="#">Employee Corner</a>
        </div>
    </section>
    <!-- END: Bottom Grid Navigation -->
    <!-- BEGIN: Partner Logos -->
    <section class="bg-white border-t border-slate-200 py-6">
        <div
            class="max-w-7xl mx-auto px-4 flex flex-wrap justify-center gap-4 md:gap-8 opacity-80 hover:opacity-100 transition-opacity">
            <img alt="MyGov" class="h-12 object-contain border border-slate-200 rounded" src="saral-logo.png" />
            <img alt="Web Directory" class="h-12 object-contain border border-slate-200 rounded"
                src="data-gov.png" />
            <img alt="India.gov.in" class="h-12 object-contain border border-slate-200 rounded"
                src="digital-india.png" />
            <img alt="data.gov.in" class="h-12 object-contain border border-slate-200 rounded"
                src="govt-of-haryana.png" />
            <img alt="Digital India" class="h-12 object-contain border border-slate-200 rounded"
                src="pmay-logo.jpg" />
            <img alt="Make in India" class="h-12 object-contain border border-slate-200 rounded"
                src="nhb-logo.png" />
            <img alt="Make in India" class="h-12 object-contain border border-slate-200 rounded"
                src="hsvp-logo.jpg" />
            <img alt="Make in India" class="h-12 object-contain border border-slate-200 rounded"
                src="hbh-logo.png" />
            <img alt="Make in India" class="h-12 object-contain border border-slate-200 rounded" src="my-gov.png" />
        </div>
    </section>
    <!-- END: Partner Logos -->
    <!-- BEGIN: Footer -->
    <footer class="bg-slate-800 text-slate-300 py-8 text-sm">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>© 2026 Department of Housing For All, Government of Haryana, India.</p>
            <p class="mt-2 text-slate-500">Designed & Developed by Citizen Resources Information Department, Haryana (CRID)</p>
        </div>
    </footer>
    <!-- END: Footer -->
</body>

</html>
