<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>MMGAY Admin Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9f9ff;
        }

        /* Custom Scrollbar for administrative density */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    <!-- Shared Configuration -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container": "#e7eeff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f0f3ff",
                        "secondary-fixed-dim": "#b7c8e1",
                        "on-primary-fixed": "#001a41",
                        "tertiary": "#595c5e",
                        "surface-dim": "#cfdaf2",
                        "outline": "#717786",
                        "secondary-fixed": "#d3e4fe",
                        "outline-variant": "#c1c6d7",
                        "on-secondary-fixed": "#0b1c30",
                        "inverse-on-surface": "#ecf1ff",
                        "surface-container-high": "#dee8ff",
                        "surface": "#f9f9ff",
                        "on-surface-variant": "#414755",
                        "on-primary": "#ffffff",
                        "tertiary-fixed-dim": "#c4c7c9",
                        "secondary": "#505f76",
                        "on-primary-container": "#fefcff",
                        "surface-variant": "#d8e3fb",
                        "on-secondary-fixed-variant": "#38485d",
                        "background": "#f9f9ff",
                        "surface-container-highest": "#d8e3fb",
                        "on-background": "#111c2d",
                        "on-primary-fixed-variant": "#004493",
                        "surface-tint": "#005bc1",
                        "primary": "#0058bc",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#e0e3e5",
                        "on-tertiary-fixed": "#191c1e",
                        "error": "#ba1a1a",
                        "primary-fixed-dim": "#adc6ff",
                        "inverse-primary": "#adc6ff",
                        "primary-container": "#0070eb",
                        "on-secondary-container": "#54647a",
                        "secondary-container": "#d0e1fb",
                        "on-tertiary-container": "#fbfdff",
                        "on-tertiary-fixed-variant": "#444749",
                        "on-secondary": "#ffffff",
                        "inverse-surface": "#263143",
                        "on-error": "#ffffff",
                        "on-surface": "#111c2d",
                        "tertiary-container": "#727577",
                        "primary-fixed": "#d8e2ff",
                        "surface-bright": "#f9f9ff",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "md": "16px",
                        "xs": "4px",
                        "xl": "32px",
                        "gutter": "20px",
                        "container-max": "1440px",
                        "sm": "8px",
                        "base": "4px",
                        "lg": "24px"
                    },
                    "fontFamily": {
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "headline-lg": ["Inter"],
                        "headline-xl": ["Inter"],
                        "headline-lg-mobile": ["Inter"],
                        "stat-value": ["Inter"],
                        "headline-md": ["Inter"],
                        "label-md": ["Inter"]
                    },
                    "fontSize": {
                        "body-lg": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "400"
                        }],
                        "body-md": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
                        }],
                        "headline-lg": ["24px", {
                            "lineHeight": "32px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }],
                        "headline-xl": ["30px", {
                            "lineHeight": "38px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "headline-lg-mobile": ["20px", {
                            "lineHeight": "28px",
                            "fontWeight": "600"
                        }],
                        "stat-value": ["28px", {
                            "lineHeight": "34px",
                            "fontWeight": "700"
                        }],
                        "headline-md": ["20px", {
                            "lineHeight": "28px",
                            "fontWeight": "600"
                        }],
                        "label-md": ["12px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.05em",
                            "fontWeight": "600"
                        }]
                    }
                },
            },
        }
    </script>
</head>

<body class="flex min-h-screen overflow-hidden">
    <!-- SIDE NAVIGATION (SideNavBar) -->
    <aside
        class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-lg z-40 bg-surface-container shadow-none border-r border-outline-variant">
        <!-- Logo/Header -->
        <div class="px-md mb-xl flex items-center gap-sm">
            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-on-primary">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">home_work</span>
            </div>
            <div>
                <h1 class="text-headline-md font-headline-md text-on-surface font-bold leading-tight">MMGAY Admin</h1>
                <p class="text-[10px] uppercase tracking-wider text-on-surface-variant font-bold">Management Portal</p>
            </div>
        </div>
        <!-- Navigation Links -->
        <nav class="flex-1 px-sm space-y-base">
            <!-- Dashboard (Active) -->
            <a class="flex items-center gap-md bg-secondary-container text-on-secondary-container rounded-lg px-md py-sm border-l-4 border-primary transition-all duration-200 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-md text-label-md">Dashboard</span>
            </a>
            <!-- Phases -->
            <a class="flex items-center gap-md text-on-surface-variant px-md py-sm hover:bg-surface-variant hover:text-on-surface transition-all duration-200 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">layers</span>
                <span class="font-label-md text-label-md">Phases</span>
            </a>
            <!-- Reports -->
            <a class="flex items-center gap-md text-on-surface-variant px-md py-sm hover:bg-surface-variant hover:text-on-surface transition-all duration-200 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">assessment</span>
                <span class="font-label-md text-label-md">Reports</span>
            </a>
            <!-- Settings -->
            <a class="flex items-center gap-md text-on-surface-variant px-md py-sm hover:bg-surface-variant hover:text-on-surface transition-all duration-200 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-label-md text-label-md">Settings</span>
            </a>
        </nav>
        <!-- Footer / Support -->
        <div class="mt-auto px-md pt-lg border-t border-outline-variant">

            <form action="{{ route('mmgay.logout') }}" method="POST">
                @csrf

                <button type="submit"
                    class="w-full flex items-center gap-md px-md py-sm rounded-lg
                   text-red-600 hover:bg-red-50 hover:text-red-700
                   transition-all duration-200">

                    <span class="material-symbols-outlined">
                        logout
                    </span>

                    <span class="font-semibold">
                        Logout
                    </span>

                </button>

            </form>

        </div>
    </aside>
    <!-- TOP HEADER (TopNavBar Mapping) -->
    <header
        class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-lg bg-surface-container-lowest shadow-sm border-b border-outline-variant">
        <div class="flex items-center gap-md">
            <h2 class="text-headline-md font-headline-md font-bold text-primary">Dashboard</h2>
            <div class="h-6 w-[1px] bg-outline-variant"></div>

        </div>
        <div class="flex items-center gap-md">
            <!-- Branch Selector -->


            <!-- User Profile -->
            <div class="flex items-center gap-sm pl-md border-l border-outline-variant">
                <div class="text-right hidden xl:block">
                    <p class="text-body-md font-body-md font-bold text-on-surface">Rajesh Kumar</p>
                    <p class="text-[11px] text-on-surface-variant">Admin Coordinator</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-secondary-container overflow-hidden">
                    <img class="w-full h-full object-cover"
                        data-alt="A professional headshot of a middle-aged South Asian male government official wearing a crisp white formal shirt, set against a blurred office background with subtle blue and gray tones. The lighting is soft and even, creating a professional and trustworthy executive portrait suitable for a corporate dashboard interface."
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuACVV_IXWKJ1auij7WlDx2Ex2mYP4fpdH_lLbCuY7QFjqZqybcecm2GZIuMi9ahEgHjUqK2SV6eHcoVhusuQPUOFX1y_3YFV1qG8JV2MOZsftHgMkvU8xHDahtIrIyWvDpiUF6Wy4Fkm_dqbGD6hCWz6AGejtIafkyD_6vbUd5_xxARDOLlWCDiDWYrta5gDp01MQ6anCIhzaKkjlcZ7sITn7X-s5ZRJ00WD93g48JZ5mHJh-uk8DoEo3_pjz51z5iFRKNBLAGODtar" />
                </div>
            </div>
        </div>
    </header>
    <!-- MAIN CONTENT AREA -->
    <main class="ml-[260px] mt-16 flex-1 h-[calc(100vh-64px)] overflow-y-auto p-lg bg-background">
        <!-- Tab Sub-Navigation -->
        <div class="mb-lg border-b border-outline-variant flex items-center gap-xl">

            <button class="phase-tab px-xs pb-sm text-primary font-bold border-b-2 border-primary transition-colors"
                data-phase="1">
                <span class="text-body-md">Phase 1</span>
            </button>

            <button class="phase-tab px-xs pb-sm text-on-surface-variant hover:text-primary transition-colors"
                data-phase="2">
                <span class="text-body-md">Phase 2</span>
            </button>

            <button class="phase-tab px-xs pb-sm text-on-surface-variant hover:text-primary transition-colors"
                data-phase="3">
                <span class="text-body-md">Phase 3</span>
            </button>

        </div>

        <!-- KPI Grid (Bento Style) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

            <!-- KPI 1 -->
            <div
                class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-blue-50 hover:border-blue-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer">
                <div
                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                        inventory_2
                    </span>
                </div>

                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Total Plots
                </h4>

                <p class="text-2xl font-bold text-blue-700 mt-1" id="total">
                    0
                </p>

                <div class="mt-2 px-2 py-1 rounded-full bg-blue-100">
                    <span class="text-[10px] font-medium text-blue-700">
                        +12% Last Month
                    </span>
                </div>
            </div>

            <!-- KPI 2 -->
            <div
                class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-green-50 hover:border-green-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer">
                <div
                    class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                        payments
                    </span>
                </div>

                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Paid
                </h4>

                <p class="text-2xl font-bold text-green-700 mt-1" id="paid">
                    0
                </p>

                <div class="w-full mt-2 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-green-500 h-full w-[19%]"></div>
                </div>

                <span class="text-[10px] text-gray-500 mt-2">
                    19.1% Complete
                </span>
            </div>

            <!-- KPI 3 -->
            <div
                class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-blue-50 hover:border-blue-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer">
                <div
                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                        verified
                    </span>
                </div>

                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Approved
                </h4>

                <p class="text-2xl font-bold text-blue-700 mt-1" id="approved">
                    0
                </p>

                <span class="mt-2 text-[10px] px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                    Verified
                </span>
            </div>

            <!-- KPI 4 -->
            <div
                class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-yellow-50 hover:border-yellow-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer">
                <div
                    class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                        cycle
                    </span>
                </div>

                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    In Process
                </h4>

                <p class="text-2xl font-bold text-yellow-700 mt-1" id="inprocess">
                    0
                </p>

                <div class="flex items-center gap-1 mt-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                    <span class="text-[10px] text-gray-500">
                        Processing
                    </span>
                </div>
            </div>

            <!-- KPI 5 -->
            <div
                class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-red-50 hover:border-red-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer">
                <div
                    class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                        cancel
                    </span>
                </div>

                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Rejected
                </h4>

                <p class="text-2xl font-bold text-red-600 mt-1" id="rejected">
                    0
                </p>

                <button class="mt-2 text-[10px] font-medium text-red-600 hover:underline">
                    View List
                </button>
            </div>

            <!-- KPI 6 -->
            <div
                class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-orange-50 hover:border-orange-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer">
                <div
                    class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                        pending_actions
                    </span>
                </div>

                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Pending Approval
                </h4>

                <p class="text-2xl font-bold text-orange-600 mt-1" id="pending">
                    0
                </p>

                <div class="mt-2 px-2 py-1 rounded-full bg-orange-100">
                    <span class="text-[10px] font-medium text-orange-700">
                        Needs Review
                    </span>
                </div>
            </div>

        </div>
        <!-- Data Visualization Area (Asymmetric Component) -->

    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Micro-interactions Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Simple logic for branch selector toggle simulation
            const branchBtn = document.querySelector('button[class*="Branch Selector"]'); // Placeholder selector
            // Since we built the HTML manually based on guidelines, let's use the actual button
            const realBranchBtn = document.querySelector('header .relative button');

            if (realBranchBtn) {
                realBranchBtn.addEventListener('click', () => {
                    console.log('Branch selector toggled');
                });
            }

            // Tab logic
            const tabButtons = document.querySelectorAll('.mb-lg button');
            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabButtons.forEach(b => {
                        b.classList.remove('text-primary', 'font-bold', 'border-b-2',
                            'border-primary');
                        b.classList.add('text-on-surface-variant');
                    });
                    btn.classList.add('text-primary', 'font-bold', 'border-b-2', 'border-primary');
                    btn.classList.remove('text-on-surface-variant');
                });
            });
        });
    </script>
    <script>
        $(function() {

            loadPhase(1);

            $('.phase-tab').click(function() {

                $('.phase-tab')
                    .removeClass('text-primary font-bold border-b-2 border-primary')
                    .addClass('text-on-surface-variant');

                $(this)
                    .removeClass('text-on-surface-variant')
                    .addClass('text-primary font-bold border-b-2 border-primary');

                loadPhase($(this).data('phase'));

            });

        });
    </script>
    <script>
        function loadPhase(phase) {

            $.ajax({
                url: "{{ route('district.dashboard') }}/" + phase,
                type: "GET",
                dataType: "json",

                success: function(res) {

                    console.log(res);

                    $('#total').text(res.Total ?? 0);
                    $('#paid').text(res.Paid ?? 0);
                    $('#approved').text(res.Approved ?? 0);
                    $('#rejected').text(res.Rejected ?? 0);
                    $('#inprocess').text(res.InProcess ?? 0);
                    $('#pending').text(res.Pending ?? 0);
                },

                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });

        }
    </script>
</body>

</html>
