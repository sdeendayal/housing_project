<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Department of Housing For All</title>
    <link rel="icon" type="image/png" href="favicon.png">
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body class="bg-slate-50 text-slate-800 antialiased font-[Poppins]">
    <!-- BEGIN: Top Utility Bar -->
    {{-- <div class="bg-civic-blue text-white text-xs py-1 px-4">
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
    </div> --}}
    <!-- END: Top Utility Bar -->
    <!-- BEGIN: Header -->
    <header
        class="text-on-primary docked full-width top-0 sticky border-b border-outline-variant shadow-md z-50 bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] bg-[url('../header-tp-bg.png')] bg-no-repeat bg-right bg-cover">
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
            <div class="flex items-center space-x-4 bg-white/5 p-2 rounded-lg border border-white/10">
                <img alt="Sh. Nayab Singh Saini"
                    class="h-16 w-16 rounded-full object-cover border-2 border-white/20 shadow-sm"
                    src="cm-picture-new1.jpg" />
                <div class="text-left">
                    <p class="text-sm font-bold text-white leading-tight">Sh. Nayab Singh Saini</p>
                    <p class="text-[10px] text-slate-300">Hon'ble Chief Minister of Haryana</p>
                </div>
            </div>
        </div>
    </header>
    <!-- END: Header -->
    <!-- BEGIN: Navigation -->
    <nav class="bg-white border-b border-slate-200 shadow-sm relative z-40">
        <div class="max-w-7xl mx-auto px-4">

            <div
                class="flex flex-wrap items-center justify-center md:justify-start space-x-1 md:space-x-4 py-2 text-center">

                <!-- Home -->
                <a class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-civic-blue border-b-2 border-civic-accent"
                    href="/">

                    <span class="material-symbols-outlined text-[18px]">
                        home
                    </span>

                    Home
                </a>

                <!-- About Us -->
                <a class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">

                    <span class="material-symbols-outlined text-[18px]">
                        info
                    </span>

                    About Us
                </a>

                <!-- Vision -->
                <a class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">

                    <span class="material-symbols-outlined text-[18px]">
                        visibility
                    </span>

                    Our Vision
                </a>

                <!-- Gallery -->
                <a class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">

                    <span class="material-symbols-outlined text-[18px]">
                        photo_library
                    </span>

                    Gallery
                </a>

                <!-- Help -->
                <a class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-civic-blue bg-civic-highlight hover:bg-yellow-500 rounded-md shadow-sm transition-colors uppercase tracking-wide"
                    href="/help">

                    <span class="material-symbols-outlined text-[18px]">
                        help
                    </span>

                    Help
                </a>

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

                <!-- Header -->
                <div class="bg-sky-500 text-white px-4 py-2 font-semibold text-sm flex items-center justify-between">

                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">
                            campaign
                        </span>
                        <span>Latest News</span>
                    </div>

                    <span class="animate-pulse text-xs bg-white/20 px-2 py-1 rounded-full">
                        LIVE
                    </span>

                </div>

                <!-- Scroll Area -->
                <div class="h-[360px] overflow-hidden relative">

                    <ul id="newsScroller"
                        class="divide-y divide-slate-100 text-sm absolute w-full animate-news-scroll hover:[animation-play-state:paused]">

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Strategy to implement AHP-PMAY(U) in Haryana-comments/suggestions thereof
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Draw Results of Charki Dadri under MMSAY
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Draw Results of Fatehabad under MMSAY
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Draw Results of Gohana under MMSAY
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Draw Results of Jagadhri under MMSAY
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Draw Results of Jhajjar under MMSAY
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Draw Results of Rohtak under MMSAY
                                </span>
                            </a>
                        </li>

                    </ul>

                </div>

            </div>
            <!-- Panel: Data Download -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                <div class="bg-green-500 text-white px-4 py-3 font-semibold text-sm flex items-center justify-between">

                    <!-- Left Side -->
                    <div class="flex items-center gap-2">

                        <span class="material-symbols-outlined text-[20px]">
                            download
                        </span>

                        <span class="tracking-wide">
                            Data Download
                        </span>

                    </div>

                    <!-- Right Icon -->
                    <span class="material-symbols-outlined text-[18px] opacity-90">
                        cloud_download
                    </span>

                </div>
                <ul class="divide-y divide-slate-100 text-sm">
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">List of
                                Approved MMSAY
                                Beneficiaries</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">MMGAY
                                Progress
                                Reports</span>
                        </a>
                    </li>
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <div>
                                <span
                                    class="text-slate-700 hover:text-civic-blue transition-colors duration-200">Application
                                    Forms
                                    Download</span>
                                <span
                                    class="inline-block bg-red-500 text-white text-[10px] px-1 rounded ml-1">NEW</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Panel: Public Notice -->
            {{-- <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                <div class="bg-sky-400 text-white px-4 py-2 font-semibold text-sm">
                    Important Public Notice
                </div>
                <ul class="divide-y divide-slate-100 text-sm">
                    <li class="p-3 hover:bg-slate-50">
                        <a class="flex items-start space-x-2" href="#">
                            <span class="text-green-500 mt-0.5">●</span>
                            <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">ALERT:
                                Beware of fraudulent calls regarding housing allotment
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
            </div> --}}
        </aside>
        <!-- CENTER AREA -->
        <section class="lg:col-span-2 space-y-6">
            <!-- Scheme Compact Cards -->
            <div class="grid grid-cols-3 gap-4 mb-6">

                <!-- MMSAY -->
                <div class="relative group">

                    <a href="#"
                        class="relative bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-blue-50 transition-all duration-300 overflow-hidden">

                        <!-- Bottom Line -->
                        <span
                            class="absolute bottom-0 left-0 w-full h-1 bg-blue-200 group-hover:bg-blue-600 transition-all duration-300">
                        </span>

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
                            class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 text-sm font-medium text-gray-700 transition-all"
                            target="_blank">

                            <span class="material-symbols-outlined text-[18px]">
                                person
                            </span>

                            Citizen Login
                        </a>

                        <a href="/mmsay-login"
                            class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100"
                            target="_blank">

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
                        class="relative bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-green-50 transition-all duration-300 overflow-hidden">

                        <!-- Bottom Line -->
                        <span
                            class="absolute bottom-0 left-0 w-full h-1 bg-green-200 group-hover:bg-green-600 transition-all duration-300">
                        </span>

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
                        class="relative bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-orange-50 transition-all duration-300 overflow-hidden">

                        <!-- Bottom Line -->
                        <span
                            class="absolute bottom-0 left-0 w-full h-1 bg-orange-200 group-hover:bg-orange-600 transition-all duration-300">
                        </span>

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
                class="bg-white rounded-lg shadow-sm border border-surface-container-highest p-4 mb-6 overflow-hidden">

                <div class="relative w-full overflow-hidden rounded-xl group">

                    <!-- Slider Wrapper -->
                    <div id="slider" class="flex gap-4 transition-transform duration-700 ease-in-out">

                        <!-- Slide 1 -->
                        <div class="min-w-[calc(100%-16px)] flex justify-center">
                            <img src="banner.jpeg"
                                class="w-full rounded-xl object-cover transition-transform duration-500 hover:scale-[1.01]"
                                alt="Banner 1">
                        </div>

                        <!-- Slide 2 -->
                        {{-- <div class="min-w-[calc(100%-16px)] flex justify-center">
                            <img src="banner8.jpg"
                                class="w-full rounded-xl object-cover transition-transform duration-500 hover:scale-[1.01]"
                                alt="Banner 2">
                        </div> --}}

                        <!-- Slide 3 -->
                        {{-- <div class="min-w-[calc(100%-16px)] flex justify-center">
                            <img src="banner2.jpg"
                                class="w-full rounded-xl object-cover transition-transform duration-500 hover:scale-[1.01]"
                                alt="Banner 3">
                        </div> --}}

                    </div>

                    <!-- Left Button -->
                    <button id="prevBtn"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full z-10">

                        <span class="material-symbols-outlined">
                            chevron_left
                        </span>

                    </button>

                    <!-- Right Button -->
                    <button id="nextBtn"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full z-10">

                        <span class="material-symbols-outlined">
                            chevron_right
                        </span>

                    </button>

                </div>

            </div>
            <!-- What's New Section -->
            <div class="bg-white rounded-lg shadow-sm border border-surface-container-highest overflow-hidden">
                <div
                    class="px-4 py-3 bg-surface-container-low border-b border-surface-container-highest flex justify-between items-center">

                    <!-- Left Side -->
                    <div class="flex items-center gap-2">

                        <span class="material-symbols-outlined text-secondary text-[20px]">
                            notifications_active
                        </span>

                        <h3 class="text-sm font-bold text-primary uppercase tracking-wide">
                            What's New
                        </h3>

                    </div>

                    <!-- Right Side -->
                    <a class="flex items-center gap-1 text-sm font-semibold text-sky-600 hover:text-sky-800 transition-all duration-200 hover:underline"
                        href="#">

                        View All

                        <span class="material-symbols-outlined text-[18px]">
                            arrow_forward
                        </span>

                    </a>

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

                <div class="bg-sky-500 text-white px-4 py-2 font-semibold text-sm flex items-center justify-between">

                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">
                            policy
                        </span>
                        <span>Policy / Guidelines</span>
                    </div>

                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
                        Docs
                    </span>

                </div>

                <!-- Scroll Container -->
                <div class="h-[320px] overflow-hidden relative">

                    <ul class="divide-y divide-slate-100 text-sm animate-news-scroll">

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-center space-x-3 no-underline" href="#">
                                <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                    description
                                </span>

                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    ARHC Scheme Guideline
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-center space-x-3 no-underline" href="#">
                                <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                    description
                                </span>

                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Redesignation of Department of Housing for All
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-center space-x-3 no-underline" href="#">
                                <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                    description
                                </span>

                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Pradhan Mantri Awaas Yojana (Gramin)
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-center space-x-3 no-underline" href="#">
                                <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                    description
                                </span>

                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Affordable Rental Housing Complexes
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-center space-x-3 no-underline" href="#">
                                <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                    description
                                </span>

                                </span>

                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Pradhan Mantri Awaas Yojana (Urban)
                                </span>
                            </a>
                        </li>

                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-center space-x-3 no-underline" href="#">
                                <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                    description
                                </span>

                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                    Mukhya Mantri Gramin Awas Yojana
                                </span>
                            </a>
                        </li>

                    </ul>

                </div>

            </div>
            <!-- Panel: Citizen Services -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                <div class="bg-green-500 text-white px-4 py-3 font-semibold text-sm flex items-center justify-between">

                    <div class="flex items-center gap-2">

                        <!-- Left Icon -->
                        <span class="material-symbols-outlined text-[20px]">
                            support_agent
                        </span>

                        <span class="tracking-wide">
                            Citizen Services: Docs
                        </span>

                    </div>

                    <!-- Right Icon -->
                    <span class="material-symbols-outlined text-[18px] opacity-90">
                        description
                    </span>

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
    <section class="w-full bg-white py-10">

        <div class="max-w-6xl mx-auto px-4">

            <!-- Center Grid Wrapper -->
            <div class="grid place-items-center">

                <!-- Card -->
                <div
                    class="w-full bg-white border border-slate-200 rounded-2xl shadow-sm p-8 
                        transition-all duration-300 hover:shadow-xl hover:-translate-y-1">

                    <!-- Title -->
                    <h2 class="text-2xl font-bold text-slate-800 text-center mb-5 relative">

                        ABOUT US

                        <span class="block w-20 h-[3px] bg-slate-900 mx-auto mt-2 rounded-full"></span>

                    </h2>

                    <!-- Content -->
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed text-justify">

                        Hon’ble Chief Minister-cum-Finance Minister, Haryana in the Budget Speech of the financial year
                        2020-2021 on 28.02.2020 had stated that there will be a department namely ‘Department of Housing
                        for All’
                        by subsuming various housing schemes currently undertaken by several departments like housing
                        scheme for
                        BPL/EWS by Housing Board Haryana, Pradhan Mantri Awas Yojna-Urban, Rajiv Awas Yojna by
                        Department of
                        Urban Local Bodies, Pradhan Mantri Awas Yojna-Gramin by Department of Rural Development, Housing
                        Advance
                        Scheme for registered construction worker by Haryana Building and other Construction Worker
                        Welfare Board,
                        Ashiana Scheme by Haryana Shehri Vikas Pradhikaran, Dr. B. R. Ambedkar Awas Navinikaran Yojna
                        for house
                        repair by Department of SC & BC Welfare. The Legislative Assembly has accorded consent in this
                        regard.

                    </p>

                </div>

            </div>

        </div>

    </section>

    <section class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Heading -->
            <div class="text-center mb-10">

                <h2 class="text-3xl font-bold text-civic-blue uppercase tracking-tight">
                    Gallery
                </h2>

                <!-- Underline -->
                <div class="w-24 h-1 bg-sky-500 mx-auto mt-3 rounded-full"></div>

            </div>

            <!-- Gallery -->
            <div class="flex flex-wrap justify-center gap-6">

                <!-- Card 1 -->
                <div class="flex-none w-64 rounded-xl overflow-hidden shadow-lg">
                    <img alt="Project Alpha"
                        class="w-full h-44 object-cover hover:scale-105 transition-transform duration-500"
                        src="1654693046606798243.jpg" />
                    {{-- <div class="p-4 bg-white border-t border-slate-100">
                        <p class="font-semibold text-civic-blue">
                            Sector 42 Housing Complex
                        </p>
                    </div> --}}
                </div>

                <!-- Card 2 -->
                <div class="flex-none w-64 rounded-xl overflow-hidden shadow-lg">
                    <img alt="Project Beta"
                        class="w-full h-44 object-cover hover:scale-105 transition-transform duration-500"
                        src="16546930511289561504.jpg" />
                    {{-- <div class="p-4 bg-white border-t border-slate-100">
                        <p class="font-semibold text-civic-blue">
                            Green Valley Apartments
                        </p>
                    </div> --}}
                </div>

                <!-- Card 3 -->
                <div class="flex-none w-64 rounded-xl overflow-hidden shadow-lg">
                    <img alt="Project Gamma"
                        class="w-full h-44 object-cover hover:scale-105 transition-transform duration-500"
                        src="16546930602025626684.jpg" />
                    {{-- <div class="p-4 bg-white border-t border-slate-100">
                        <p class="font-semibold text-civic-blue">
                            Urban Residency Phase II
                        </p>
                    </div> --}}
                </div>

                <!-- Card 4 -->
                <div class="flex-none w-64 rounded-xl overflow-hidden shadow-lg">
                    <img alt="Project Delta"
                        class="w-full h-44 object-cover hover:scale-105 transition-transform duration-500"
                        src="1654693080605359150.jpg" />
                    {{-- <div class="p-4 bg-white border-t border-slate-100">
                        <p class="font-semibold text-civic-blue">
                            Skyview Towers
                        </p>
                    </div> --}}
                </div>



            </div>

            <!-- Button -->
            <div class="mt-10 text-center">
                <a class="inline-block px-8 py-3 bg-civic-blue text-white font-bold rounded-lg shadow hover:bg-slate-700 transition-colors uppercase tracking-wider text-sm"
                    href="#">
                    View All
                </a>
            </div>

        </div>
    </section>
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

        <div class="max-w-7xl mx-auto px-4 relative">

            <!-- Left Button -->
            <button id="logoPrev"
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white shadow-md border border-slate-200 rounded-full p-2 hover:bg-slate-100 transition">

                <span class="material-symbols-outlined text-slate-700">
                    chevron_left
                </span>

            </button>

            <!-- Slider Wrapper -->
            <div class="overflow-hidden mx-12">

                <div id="logoSlider" class="flex items-center gap-6 transition-transform duration-500 ease-in-out">

                    <a href="https://saralharyana.gov.in/" target="_blank">
                        <img alt="Saral"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="saral-logo.png" />
                    </a>

                    <a href="https://www.data.gov.in/" target="_blank">
                        <img alt="Data Gov"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="data-gov.png" />
                    </a>

                    <a href="https://www.digitalindia.gov.in/" target="_blank">
                        <img alt="Digital India"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="digital-india.png" />
                    </a>

                    <a href="https://haryana.gov.in/" target="_blank">
                        <img alt="Govt of Haryana"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="govt-of-haryana.png" />
                    </a>

                    <a href="https://pmaymis.gov.in/" target="_blank">
                        <img alt="PMAY"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="pmay-logo.jpg" />
                    </a>

                    <a href="https://nhb.org.in/" target="_blank">
                        <img alt="NHB"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="nhb-logo.png" />
                    </a>

                    <a href="https://hsvphry.org.in/" target="_blank">
                        <img alt="HSVP"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="hsvp-logo.jpg" />
                    </a>

                    <a href="#" target="_blank">
                        <img alt="HBH"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="hbh-logo.png" />
                    </a>

                    <a href="https://www.mygov.in/" target="_blank">
                        <img alt="My Gov"
                            class="h-20 min-w-[170px] object-contain border border-slate-200 rounded-xl p-3 bg-white shadow-sm hover:shadow-md transition"
                            src="my-gov.png" />
                    </a>

                </div>

            </div>

            <!-- Right Button -->
            <button id="logoNext"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white shadow-md border border-slate-200 rounded-full p-2 hover:bg-slate-100 transition">

                <span class="material-symbols-outlined text-slate-700">
                    chevron_right
                </span>

            </button>

        </div>

    </section>
    <!-- END: Partner Logos -->
    <!-- BEGIN: Footer -->
    <footer class="bg-slate-900 border-t border-slate-700 text-slate-300 py-5">

        <div class="max-w-7xl mx-auto px-4">

            <!-- TOP: Menu (Centered) -->
            <div class="pb-4 border-b border-slate-700 flex flex-wrap justify-center gap-4 text-sm text-slate-400">

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">language</span>
                    Web Information Manager
                </a>

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">feedback</span>
                    Feedback
                </a>

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">privacy_tip</span>
                    Privacy Policy
                </a>

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">copyright</span>
                    Copyright Policy
                </a>

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">gavel</span>
                    Terms & Conditions
                </a>

            </div>

            <!-- CENTER: Logo + Department -->
            <div class="flex flex-col items-center justify-center gap-4 py-5 text-center">

                <div>

                    <p class="text-base font-semibold text-white">
                        © 2026 Department of Housing For All, Government of Haryana
                    </p>

                    <p class="text-sm text-slate-400 mt-1">
                        Designed & Developed by Citizen Resources Information Department, Haryana (CRID)
                    </p>

                </div>

            </div>

            <!-- BOTTOM: Visitor Counter + Image (Right Side) -->
            <div class="flex justify-center items-center gap-6 mt-4">

                <!-- Visitor Counter -->
                <div class="flex items-center gap-3">

                    <span class="material-symbols-outlined text-slate-300 text-[26px]">
                        monitoring
                    </span>

                    <div class="flex flex-col leading-tight text-left">
                        <span class="text-xs uppercase tracking-wider text-slate-500">
                            Visitors
                        </span>
                        <span class="text-lg font-bold text-white tracking-wide">
                            12,45,892
                        </span>
                    </div>

                </div>

                <!-- Image -->
                <img src="emblem-black.png" alt="Haryana Logo"
                    class="h-14 w-14 object-contain opacity-95 hover:scale-105 transition-transform duration-300">

            </div>

        </div>

    </footer>
    <!-- END: Footer -->
    <script>
        const slider = document.getElementById('slider');
        const slides = document.querySelectorAll('#slider > div');

        let index = 0;
        const totalSlides = slides.length;

        function showSlide(i) {
            slider.style.transform = `translateX(calc(-${i * 100}% - ${i * 16}px))`;
        }

        // Next Slide
        function nextSlide() {
            index = (index + 1) % totalSlides;
            showSlide(index);
        }

        // Previous Slide
        function prevSlide() {
            index = (index - 1 + totalSlides) % totalSlides;
            showSlide(index);
        }

        // Auto Slide
        let autoSlide = setInterval(nextSlide, 3000);

        // Button Events
        document.getElementById('nextBtn').addEventListener('click', () => {
            nextSlide();
            resetAutoSlide();
        });

        document.getElementById('prevBtn').addEventListener('click', () => {
            prevSlide();
            resetAutoSlide();
        });

        // Reset Auto Timer
        function resetAutoSlide() {
            clearInterval(autoSlide);
            autoSlide = setInterval(nextSlide, 3000);
        }
    </script>
    <style>
        @keyframes newsScroll {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-50%);
            }
        }

        .animate-news-scroll {
            animation: newsScroll 18s linear infinite;
        }
    </style>
    <style>
        @keyframes newsScroll {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-50%);
            }
        }

        .animate-news-scroll {
            animation: newsScroll 18s linear infinite;
        }

        .animate-news-scroll:hover {
            animation-play-state: paused;
        }

        a {
            text-decoration: none !important;
        }
    </style>
    <script>
        const logoSlider = document.getElementById('logoSlider');

        let logoIndex = 0;

        function moveLogos() {
            logoSlider.style.transform = `translateX(-${logoIndex * 140}px)`;
        }

        document.getElementById('logoNext').addEventListener('click', () => {
            const maxScroll = logoSlider.children.length - 4;

            if (logoIndex < maxScroll) {
                logoIndex++;
                moveLogos();
            }
        });

        document.getElementById('logoPrev').addEventListener('click', () => {
            if (logoIndex > 0) {
                logoIndex--;
                moveLogos();
            }
        });

        // Auto Slide
        setInterval(() => {
            const maxScroll = logoSlider.children.length - 4;

            if (logoIndex >= maxScroll) {
                logoIndex = 0;
            } else {
                logoIndex++;
            }

            moveLogos();
        }, 2500);
    </script>
</body>

</html>
