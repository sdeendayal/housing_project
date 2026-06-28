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

    <header class="text-on-primary docked full-width top-0 sticky border-b border-outline-variant shadow-md z-50 bg-no-repeat bg-right bg-cover" style="background-image: linear-gradient(90deg, rgba(6,127,208,1) 0%, rgba(0,51,88,1) 100%), url('{{ asset('header-tp-bg.png') }}');">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Placeholder for Department Logo -->
                <img alt="Department Logo" class="h-16 w-16 object-contain" src="{{ asset('Haryana_emblem.png') }}" />
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
                    href="/help">

                    <span class="material-symbols-outlined text-[18px]">
                        help
                    </span>

                    Help
                </a>

            </div>

        </div>
    </nav>

    <section class="bg-[#eef2f7] py-10 overflow-x-auto">
        <div class="min-w-[1250px] flex flex-col items-center text-[12px]">
            <div class="text-center mb-10">
                <div
                    class="inline-flex items-center gap-2 bg-blue-100 text-civic-blue px-4 py-1.5 rounded-full text-xs font-semibold mb-3">
                    <span class="material-symbols-outlined text-[16px]">
                        account_tree
                    </span>
                    Organisation Chart
                </div>
            </div>

            <!-- Department -->
            <div
                class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl shadow-md px-10 py-3 w-[420px] text-center whitespace-nowrap">

                <h2 class="font-semibold text-[18px]">
                    Department of Housing For All
                </h2>

            </div>

            <!-- Line -->
            <div class="w-[2px] h-8 bg-red-500"></div>
            <!-- Minister -->
            <!-- Minister -->
            <div
                class="bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-2xl shadow-lg px-5 py-4 w-[420px] text-center whitespace-nowrap">

                <h3 class="font-semibold text-[14px]">
                    Minister-In-Charge - Dr. Kamal Gupta
                </h3>

            </div>

            <!-- Line -->
            <div class="w-[2px] h-8 bg-blue-500"></div>
            <!-- Secretary -->
            <div
                class="bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-2xl shadow-lg px-5 py-4 w-[520px] text-center whitespace-nowrap">

                <h3 class="font-semibold text-[14px]">
                    Administrative Secretary - Dr. Raja Sekhar Vundru, I.A.S.
                </h3>

            </div>

            <!-- Connector -->
            <div class="relative w-[650px] h-[70px]">

                <!-- center line -->
                <div class="absolute left-1/2 top-0 w-[2px] h-[30px] bg-blue-500"></div>

                <!-- horizontal -->
                <div class="absolute top-[30px] left-[90px] w-[470px] h-[2px] bg-blue-500"></div>

                <!-- vertical left -->
                <div class="absolute left-[90px] top-[30px] w-[2px] h-[40px] bg-blue-500"></div>

                <!-- vertical right -->
                <div class="absolute right-[90px] top-[30px] w-[2px] h-[40px] bg-blue-500"></div>
            </div>

            <!-- Main Branches -->
            <div class="flex gap-28">

                <!-- LEFT SIDE -->
                <div class="flex flex-col items-center">

                    <!-- Heading -->
                    <div
                        class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-xl shadow-md px-8 py-3 w-[290px] text-center font-semibold text-[13px]">
                        Directorate of Housing For All
                    </div>

                    <div class="w-[2px] h-6 bg-blue-500"></div>

                    <!-- DG -->
                    <div class="bg-white border border-blue-100 rounded-2xl shadow-md px-5 py-4 text-center w-[290px]">
                        <h4 class="font-semibold text-[14px] text-blue-900">
                            Director General
                        </h4>
                        <p class="text-slate-600 text-[12px] mt-1">
                            Sh. Ajit Balaji Joshi, I.A.S
                        </p>
                    </div>

                    <div class="w-[2px] h-6 bg-blue-500"></div>

                    <!-- Joint Director -->
                    <div class="bg-white border border-blue-100 rounded-2xl shadow-md px-5 py-4 text-center w-[290px]">
                        <h4 class="font-semibold text-[14px] text-blue-900">
                            Joint Director
                        </h4>
                        <p class="text-slate-600 text-[12px] mt-1">
                            Sh. Rakesh Sandhu, H.C.S.
                        </p>
                    </div>

                    <!-- Bottom connector -->
                    <div class="relative w-[320px] h-[50px]">

                        <div class="absolute left-1/2 top-0 w-[2px] h-[20px] bg-blue-500"></div>

                        <div class="absolute top-[20px] left-[35px] w-[250px] h-[2px] bg-blue-500"></div>

                        <div class="absolute left-[35px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                        <div class="absolute left-1/2 top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                        <div class="absolute right-[35px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>

                    </div>

                    <!-- Officers -->
                    <div class="flex gap-4">

                        <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[110px] text-center">
                            <h5 class="font-semibold text-[13px] text-blue-900">
                                ATP
                            </h5>
                            <p class="text-[11px] text-slate-600 mt-1">
                                Sh. Aman Godara
                            </p>
                        </div>

                        <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[110px] text-center">
                            <h5 class="font-semibold text-[13px] text-blue-900">
                                Coordinator
                            </h5>
                            <p class="text-[11px] text-slate-600 mt-1">
                                Sh. Devender
                            </p>
                        </div>

                        <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[110px] text-center">
                            <h5 class="font-semibold text-[13px] text-blue-900">
                                A.O/Supdt.
                            </h5>
                            <p class="text-[11px] text-slate-600 mt-1">
                                Sh. Dev Kant Sharma
                            </p>
                        </div>

                    </div>

                </div>

                <!-- RIGHT SIDE -->
                <div class="flex flex-col items-center">

                    <div
                        class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-xl shadow-md px-8 py-3 w-[290px] text-center font-semibold text-[13px]">
                        Housing Board Haryana
                    </div>

                    <div class="w-[2px] h-6 bg-blue-500"></div>

                    <div class="bg-white border border-blue-100 rounded-2xl shadow-md px-5 py-4 text-center w-[290px]">
                        <h4 class="font-semibold text-[14px] text-blue-900">
                            Chief Administrator
                        </h4>
                        <p class="text-slate-600 text-[12px] mt-1">
                            Sh. Ajit Balaji Joshi, I.A.S
                        </p>
                    </div>

                    <div class="w-[2px] h-6 bg-blue-500"></div>

                    <div class="bg-white border border-blue-100 rounded-2xl shadow-md px-5 py-4 text-center w-[290px]">
                        <h4 class="font-semibold text-[14px] text-blue-900">
                            Secretary
                        </h4>
                        <p class="text-slate-600 text-[12px] mt-1">
                            Sh. Rakesh Sandhu, H.C.S.
                        </p>
                    </div>

                    <!-- Bottom connector -->
                    <div class="relative w-[470px] h-[50px]">

                        <div class="absolute left-1/2 top-0 w-[2px] h-[20px] bg-blue-500"></div>

                        <div class="absolute top-[20px] left-[25px] w-[420px] h-[2px] bg-blue-500"></div>

                        <div class="absolute left-[25px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                        <div class="absolute left-[155px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                        <div class="absolute right-[155px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                        <div class="absolute right-[25px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>

                    </div>

                    <!-- Officers -->
                    <div class="flex gap-3">

                        <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[105px] text-center">
                            <h5 class="font-semibold text-[13px] text-blue-900">Chief Engineer</h5>
                            <p class="text-[11px] text-slate-600">Sh. Kabul Singh</p>
                        </div>

                        <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[105px] text-center">
                            <h5 class="font-semibold text-[13px] text-blue-900">CRO (PM)</h5>
                            <p class="text-[11px] text-slate-600">Sh. Lalit</p>
                        </div>

                        <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[105px] text-center">
                            <h5 class="font-semibold text-[13px] text-blue-900">CAO</h5>
                            <p class="text-[11px] text-slate-600">Sh. Chander Mohan</p>
                        </div>

                        <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[105px] text-center">
                            <h5 class="font-semibold text-[13px] text-blue-900">STP</h5>
                            <p class="text-[11px] text-slate-600">Sh. Satish Punia</p>
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
