<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Department of Housing For All</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
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
    <style>
        /* Contact Card */
        .contact-card {
            transition: all 0.35s ease;
        }

        .contact-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 30px rgba(0, 82, 168, 0.12);
        }

        /* Small Icon Circle */
        .contact-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #0f75c8, #0b3c74);
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 18px rgba(15, 117, 200, 0.25);
            transition: all 0.35s ease;
        }

        .contact-icon span {
            font-size: 32px !important;
            color: #fff;
            transition: all 0.35s ease;
        }

        /* Hover effect */
        .contact-card:hover .contact-icon {
            transform: scale(1.08) rotate(4deg);
        }

        .contact-card:hover .contact-icon span {
            transform: scale(1.12);
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
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

    <section class="bg-[#eef3f9] py-14">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Heading -->
            <div class="text-center mb-10">

                <h2 class="text-[42px] font-bold text-[#0b3c74]">
                    Get in Touch
                </h2>

                <div class="flex justify-center items-center gap-3 mt-3">
                    <div class="w-16 h-[2px] bg-[#0f75c8] rounded"></div>
                    <div class="w-3 h-3 bg-[#0f75c8] rounded-full"></div>
                    <div class="w-16 h-[2px] bg-[#0f75c8] rounded"></div>
                </div>

                <p class="text-slate-600 text-[15px] mt-4">
                    We are here to help you
                </p>

            </div>

            <!-- Cards -->
            <div class="bg-white rounded-[35px] shadow-lg border border-slate-200 p-8">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">

                    <!-- Address -->
                    <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                        <div class="flex justify-center mb-5">
                            <div class="contact-icon">
                                <span class="material-symbols-outlined">
                                    location_on
                                </span>
                            </div>
                        </div>

                        <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                            Address
                        </h3>

                        <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                        <p class="text-[13px] text-slate-600 leading-7">
                            C-15, Awas Bhawan,<br>
                            Sector 6,<br>
                            Panchkula, Haryana
                        </p>

                    </div>

                    <!-- Email -->
                    <!-- Email -->
                    <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                        <div class="flex justify-center mb-5">
                            <div class="contact-icon">
                                <span class="material-symbols-outlined">
                                    mail
                                </span>
                            </div>
                        </div>

                        <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                            Email ID
                        </h3>

                        <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                        <p class="text-[13px] text-slate-600 leading-6 mb-5">
                            admin-hfa[at]<br>
                            hry[dot]gov[dot]in
                        </p>

                        <!-- Button -->
                        <a href="mailto:admin-hfa@hry.gov.in"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white px-5 py-2 rounded-full text-[13px] font-medium shadow-md hover:scale-105 transition duration-300">

                            <span class="material-symbols-outlined text-[18px]">
                                send
                            </span>

                            Email Us
                        </a>

                    </div>

                    <!-- Phone 1 -->
                    <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                        <div class="flex justify-center mb-5">
                            <div class="contact-icon">
                                <span class="material-symbols-outlined">
                                    call
                                </span>
                            </div>
                        </div>

                        <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                            Phone
                        </h3>

                        <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                        <p class="text-[16px] font-medium text-slate-700 mb-5">
                            0172-2585852
                        </p>

                        <a href="tel:01722585852"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white px-5 py-2 rounded-full text-[13px] font-medium shadow-md hover:scale-105 transition duration-300">

                            <span class="material-symbols-outlined text-[18px]">
                                call
                            </span>

                            Call Now
                        </a>

                    </div>

                    <!-- Phone 2 -->
                    <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                        <div class="flex justify-center mb-5">
                            <div class="contact-icon">
                                <span class="material-symbols-outlined">
                                    call
                                </span>
                            </div>
                        </div>

                        <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                            Phone
                        </h3>

                        <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                        <p class="text-[16px] font-medium text-slate-700 mb-5">
                            0172-2568687
                        </p>

                        <a href="tel:01722568687"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white px-5 py-2 rounded-full text-[13px] font-medium shadow-md hover:scale-105 transition duration-300">

                            <span class="material-symbols-outlined text-[18px]">
                                call
                            </span>

                            Call Now
                        </a>

                    </div>

                    <!-- Phone 3 -->
                    <!-- Phone 3 -->
                    <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                        <div class="flex justify-center mb-5">
                            <div class="contact-icon">
                                <span class="material-symbols-outlined">
                                    call
                                </span>
                            </div>
                        </div>

                        <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                            Phone
                        </h3>

                        <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                        <p class="text-[16px] font-medium text-slate-700 mb-5">
                            0172-2567233
                        </p>

                        <a href="tel:01722567233"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white px-5 py-2 rounded-full text-[13px] font-medium shadow-md hover:scale-105 transition duration-300">

                            <span class="material-symbols-outlined text-[18px]">
                                call
                            </span>

                            Call Now
                        </a>

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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {

            if ($.fn.DataTable.isDataTable('#whoTable')) {
                $('#whoTable').DataTable().destroy();
            }

            $('#whoTable').DataTable({
                responsive: true,
                pageLength: 10,
                ordering: true,
                autoWidth: false,
                lengthMenu: [10, 25, 50, 100],

                language: {
                    search: "Search Officer:",
                    lengthMenu: "Show _MENU_ entries",
                }
            });

        });
    </script>
</body>

</html>
