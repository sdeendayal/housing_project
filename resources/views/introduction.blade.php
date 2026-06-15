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
                <a class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="/">

                    <span class="material-symbols-outlined text-[18px]">
                        home
                    </span>

                    Home
                </a>

                <!-- About Us Dropdown -->
                <div class="relative group">

                    <!-- ACTIVE MENU -->
                    <button
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-civic-blue border-b-2 border-civic-accent bg-slate-50 rounded-md transition-colors">

                        <span class="material-symbols-outlined text-[18px]">
                            info
                        </span>

                        About Us

                        <span class="material-symbols-outlined text-[18px] transition-transform group-hover:rotate-180">
                            expand_more
                        </span>

                    </button>

                    <!-- Dropdown Menu -->
                    <div
                        class="absolute left-0 mt-1 w-64 bg-white border border-slate-200 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden">

                        <!-- Introduction -->
                        <a href="introduction"
                            class="flex items-center gap-3 px-5 py-3 text-sm font-medium bg-blue-50 text-civic-blue border-l-4 border-civic-accent">

                            <span class="material-symbols-outlined text-[18px]">
                                description
                            </span>

                            Introduction
                        </a>

                        <!-- Organisation Chart -->
                        <a href="organisation-chart"
                            class="flex items-center gap-3 px-5 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-civic-blue transition border-t border-slate-100">

                            <span class="material-symbols-outlined text-[18px]">
                                account_tree
                            </span>

                            Organisation Chart
                        </a>

                        <!-- Who's Who -->
                        <a href="whos-who"
                            class="flex items-center gap-3 px-5 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-civic-blue transition border-t border-slate-100">

                            <span class="material-symbols-outlined text-[18px]">
                                groups
                            </span>

                            Who's Who
                        </a>

                    </div>

                </div>

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
                    href="#">

                    <span class="material-symbols-outlined text-[18px]">
                        help
                    </span>

                    Help
                </a>

            </div>

        </div>
    </nav>
    <!-- BEGIN: Introduction Section -->
    <section class="bg-gradient-to-b from-slate-50 to-white py-10 border-b border-slate-200">

        <div class="max-w-7xl mx-auto px-4">

            <!-- Heading -->
            <div class="text-center mb-10">

                <div
                    class="inline-flex items-center gap-2 bg-blue-100 text-civic-blue px-4 py-1.5 rounded-full text-xs font-semibold mb-3">

                    <span class="material-symbols-outlined text-[16px]">
                        apartment
                    </span>

                    Introduction

                </div>

                <h2 class="text-2xl md:text-3xl font-bold text-civic-blue mb-4">
                    Department of Housing For All
                </h2>

                <p class="max-w-4xl mx-auto text-slate-600 text-base leading-7">
                    The State Government vide notification dated 15.12.2020 has created a new Department
                    <span class="font-semibold text-civic-blue">“Housing For All”</span>
                    with the objective to work as the Nodal Agency for promotion, development and facilitation
                    of housing requirements especially for socio-economically marginalized sections of society
                    in urban and rural areas of the State.
                </p>

            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Content -->
                <div class="lg:col-span-2">

                    <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">

                        <!-- Title -->
                        <div class="bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] px-5 py-4">

                            <h3 class="text-xl font-semibold text-white flex items-center gap-2">

                                <span class="material-symbols-outlined text-[20px]">
                                    policy
                                </span>

                                Mandate for the Department

                            </h3>

                        </div>

                        <!-- List -->
                        <div class="p-6 space-y-5 text-sm text-slate-700 leading-7">

                            <div class="flex gap-4">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                    1
                                </div>
                                <p>
                                    Administration of the Haryana Housing Board Act, 1971 (20 of the 1971)
                                    and rules made there under.
                                </p>
                            </div>

                            <div class="flex gap-4">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                    2
                                </div>
                                <p>
                                    Administration of the Haryana Housing Board.
                                </p>
                            </div>

                            <div class="flex gap-4">

                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                    3
                                </div>

                                <div>

                                    <p class="font-semibold text-civic-blue mb-2 text-sm">
                                        Implementation of Housing Schemes:
                                    </p>

                                    <ul class="space-y-1 ml-4 list-disc text-slate-600 text-sm">

                                        <li>Land acquisition and development Scheme.</li>
                                        <li>Low Income Group Housing Scheme.</li>
                                        <li>Middle Income Group Housing Scheme.</li>
                                        <li>Rental Housing Scheme.</li>
                                        <li>Rural Housing Scheme.</li>
                                        <li>Subsidized Industrial Housing Schemes.</li>

                                    </ul>

                                </div>

                            </div>

                            <div class="flex gap-4">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                    4
                                </div>
                                <p>
                                    Constitution of State Advisory Committee in respect of Housing Scheme(s).
                                </p>
                            </div>

                            <div class="flex gap-4">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                    5
                                </div>
                                <p>
                                    Implementation of Pradhan Mantri Awas Yojana-Urban.
                                </p>
                            </div>

                            <div class="flex gap-4">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                    6
                                </div>
                                <p>
                                    Implementation of Rajiv Awas Yojana.
                                </p>
                            </div>

                            <div class="flex gap-4">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                    7
                                </div>
                                <p>
                                    Implementation of any other housing schemes to be launched by GoI/Government of
                                    Haryana.
                                </p>
                            </div>

                            <div class="flex gap-4">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                    8
                                </div>
                                <p>
                                    All Housing Development schemes including formulation, proposal,
                                    planning, budget and their implementation in the state.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Right Side Cards -->
                <div class="space-y-5">

                    <!-- Vision Card -->
                    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-5">

                        <div class="flex items-center gap-3 mb-4">

                            <div
                                class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center text-civic-blue">

                                <span class="material-symbols-outlined text-2xl">
                                    visibility
                                </span>

                            </div>

                            <div>
                                <h4 class="text-lg font-bold text-civic-blue">
                                    Our Vision
                                </h4>

                                <p class="text-xs text-slate-500">
                                    Housing for Every Citizen
                                </p>
                            </div>

                        </div>

                        <p class="text-slate-600 leading-6 text-sm">
                            To ensure affordable, inclusive and sustainable housing for all citizens
                            through transparent governance and welfare-oriented policies.
                        </p>

                    </div>

                    <!-- Stats Card -->
                    <div
                        class="bg-[linear-gradient(135deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] rounded-2xl p-6 text-white shadow-lg">

                        <h4 class="text-xl font-bold mb-6">
                            Department Highlights
                        </h4>

                        <div class="space-y-5">

                            <div class="flex items-center justify-between border-b border-white/20 pb-3">

                                <span class="text-xs uppercase tracking-wide">
                                    Established
                                </span>

                                <span class="text-xl font-bold">
                                    2020
                                </span>

                            </div>

                            <div class="flex items-center justify-between border-b border-white/20 pb-3">

                                <span class="text-xs uppercase tracking-wide">
                                    Coverage
                                </span>

                                <span class="text-lg font-bold">
                                    Urban & Rural
                                </span>

                            </div>

                            <div class="flex items-center justify-between">

                                <span class="text-xs uppercase tracking-wide">
                                    Focus
                                </span>

                                <span class="text-lg font-bold">
                                    Housing For All
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

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
                            src="Haryana_emblem.png" />
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
</body>

</html>
