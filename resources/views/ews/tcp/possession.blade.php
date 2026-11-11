<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Physical Possession Pool (4,211 Allotted Flats) | TCP Haryana</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts & Bootstrap Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- jQuery & DataTables CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif; font-size: 13px; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        table.dataTable { border-bottom: 1px solid #e2e8f0 !important; margin: 0 !important; width: 100% !important; }
        table.dataTable thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 9.5px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 8px 10px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none !important;
            white-space: nowrap !important;
        }
        table.dataTable tbody td {
            padding: 7px 10px !important;
            font-size: 11px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }
        table.dataTable tbody tr:hover { background-color: #f8fafc !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0284c7 !important;
            color: white !important;
            border: none !important;
            border-radius: 6px;
            font-weight: 700;
            font-size: 10.5px;
            padding: 2px 8px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: 10.5px !important;
            padding: 2px 8px !important;
            border-radius: 6px;
        }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_length {
            font-size: 10.5px !important;
            color: #64748b !important;
        }
        .dataTables_filter { display: none; }

        @keyframes indeterminate {
            0% { transform: translateX(-100%) scaleX(0.2); }
            50% { transform: translateX(0%) scaleX(0.6); }
            100% { transform: translateX(100%) scaleX(0.2); }
        }
        .animate-indeterminate {
            animation: indeterminate 1.4s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
            transform-origin: left;
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.08); }
        }
        .animate-pulse-glow {
            animation: pulseGlow 2.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-slate-50 select-text">

    <!-- LEFT SIDEBAR -->
    @include('ews.tcp.partials.sidebar')

    <!-- RIGHT MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden">
        
        <!-- HEADER TOP BAR -->
        <header class="h-12 bg-white border-b border-slate-200 px-3.5 flex items-center justify-between shrink-0 shadow-2xs z-30">
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="{{ route('ews.tcp.dashboard') }}" title="Back to Builder Flats Dashboard"
                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-xs transition border border-slate-200">
                    <i class="bi bi-arrow-left"></i>
                </a>

                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white text-xs shadow-xs shrink-0">
                    <i class="bi bi-houses-fill"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 leading-none">
                        <h1 class="text-xs font-black uppercase tracking-tight text-slate-900 truncate">
                            Physical Possession Management Module
                        </h1>
                        <span class="px-1.5 py-0.2 rounded text-[7.5px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-800 border border-indigo-200 font-mono shrink-0">
                            4,211 ALLOTTED POOL
                        </span>
                        <span class="px-1.5 py-0.2 rounded text-[7.5px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 font-mono shrink-0">
                            READ-ONLY TCP OVERSIGHT
                        </span>
                    </div>
                    <p class="text-[8px] text-slate-400 font-medium truncate mt-0.5">
                        Department of Town & Country Planning • Government of Haryana • Allotted Beneficiaries Physical Possession Ledger
                    </p>
                </div>
            </div>

            <!-- Header Right Profile Context -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('ews.tcp.dashboard') }}"
                    class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-250 rounded-lg text-[9px] font-black uppercase tracking-wider transition flex items-center gap-1.5">
                    <i class="bi bi-speedometer2 text-cyan-600"></i>
                    <span class="hidden sm:inline">Builder Flats Dashboard</span>
                </a>

                <div class="h-4 w-px bg-slate-200"></div>

                <div class="text-right hidden sm:block">
                    <span class="block text-[9.5px] font-black uppercase text-slate-700 leading-tight">{{ $user->name ?? 'TCP Officer' }}</span>
                    <span class="text-[7.5px] font-mono text-cyan-700 uppercase font-bold tracking-wider">HARYANA STATE</span>
                </div>
                <div class="w-7 h-7 rounded-lg bg-slate-900 text-cyan-400 flex items-center justify-center text-xs font-bold shadow-2xs border border-slate-800">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
        </header>

        <!-- MAIN SCROLLABLE CONTENT BODY -->
        <main class="flex-1 overflow-y-auto custom-scroll p-3 sm:p-4 space-y-3 min-w-0">

            <!-- Hidden Filter Input for AJAX DataTable Scope -->
            <input type="hidden" id="possession-scope" value="{{ $initialScope }}">

            <!-- SECTION 1: THE 3 TELEMETRY KPI CARDS (Matching Requested Physical Possession Pool) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                
                <!-- Card 1: Total Allotted Pool (4,211 Flats) -->
                <div id="card-possession-all" onclick="setPossessionFilter('all')"
                    class="cursor-pointer {{ $initialScope === 'all' ? 'border-2 border-indigo-500 bg-indigo-50/60 shadow-2xs ring-2 ring-indigo-400/20' : 'border border-slate-200 bg-white' }} rounded-xl px-3.5 py-2.5 shadow-2xs hover:border-indigo-300 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-base shrink-0 shadow-inner">
                            <i class="bi bi-houses-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">Total Allotted Pool</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl font-black font-mono text-indigo-700 leading-none" id="counter-total">{{ number_format($stats['total_allotted'] ?? 4211) }}</span>
                                <span class="text-[8.5px] text-slate-400 font-bold">Flats</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-800 border border-indigo-200 font-mono">4,211 POOL</span>
                        <i class="bi bi-chevron-right text-[10px] text-indigo-600"></i>
                    </div>
                </div>

                <!-- Card 2: Possession Given (1 Units) -->
                <div id="card-possession-given" onclick="setPossessionFilter('possession_given')"
                    class="cursor-pointer {{ $initialScope === 'possession_given' ? 'border-2 border-emerald-500 bg-emerald-50/60 shadow-2xs ring-2 ring-emerald-400/20' : 'border border-slate-200 bg-white' }} rounded-xl px-3.5 py-2.5 shadow-2xs hover:border-emerald-300 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base shrink-0 shadow-inner">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">Possession Given</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl font-black font-mono text-emerald-700 leading-none" id="counter-given">{{ number_format($stats['possession_given'] ?? 1) }}</span>
                                <span class="text-[8.5px] text-slate-400 font-bold">Units</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 font-mono">GIVEN</span>
                        <i class="bi bi-chevron-right text-[10px] text-emerald-600"></i>
                    </div>
                </div>

                <!-- Card 3: Pending Possession (4,210 Pending) -->
                <div id="card-possession-pending" onclick="setPossessionFilter('possession_pending')"
                    class="cursor-pointer {{ $initialScope === 'possession_pending' ? 'border-2 border-rose-500 bg-rose-50/60 shadow-2xs ring-2 ring-rose-400/20' : 'border border-slate-200 bg-white' }} rounded-xl px-3.5 py-2.5 shadow-2xs hover:border-rose-300 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base shrink-0 shadow-inner">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">Pending Possession</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl font-black font-mono text-rose-700 leading-none" id="counter-pending">{{ number_format($stats['possession_pending'] ?? 4210) }}</span>
                                <span class="text-[8.5px] text-slate-400 font-bold">Pending</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200 font-mono">PENDING</span>
                        <i class="bi bi-chevron-right text-[10px] text-rose-600"></i>
                    </div>
                </div>

            </div>

            <!-- SECTION 2: Master Possession Table Container -->
            <section class="bg-white border border-slate-200 rounded-xl shadow-2xs overflow-hidden relative">
                
                <!-- Table Top Animated Progress Line -->
                <div id="table-loading-bar" class="h-0.5 w-full bg-slate-100 overflow-hidden hidden">
                    <div class="h-full bg-gradient-to-r from-indigo-600 via-cyan-400 to-emerald-500 rounded-full animate-indeterminate"></div>
                </div>
                
                <!-- Table Header Bar -->
                <div class="px-3.5 py-2 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center text-xs shrink-0">
                            <i class="bi bi-table"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 leading-none">
                                <h2 class="text-xs font-black uppercase tracking-wider text-slate-900" id="table-heading-title">
                                    All Haryana State Allotted Pool (Physical Possession)
                                </h2>
                                <span class="px-1.5 py-0.2 bg-indigo-100 text-indigo-800 text-[7.5px] font-black uppercase rounded font-mono">
                                    4,211 Records Master
                                </span>
                            </div>
                            <p class="text-[8px] text-slate-400 font-mono uppercase">
                                Real-time verification of physical possession, allotment keys & handed-over flats
                            </p>
                        </div>
                    </div>

                    <!-- Export Toolbar -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[8.5px] font-black uppercase text-slate-400 tracking-wider">Export:</span>
                        <a id="btn-export-csv" href="{{ route('ews.tcp.possession.export', ['format' => 'csv']) }}"
                            class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded-lg text-[9px] font-black uppercase tracking-wider flex items-center gap-1 shadow-2xs transition">
                            <i class="bi bi-filetype-csv text-emerald-600 text-xs"></i>
                            <span>CSV Export</span>
                        </a>
                    </div>
                </div>

                <!-- FILTER BAR (District, Search, Reset) -->
                <div class="p-2.5 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center gap-2">
                    
                    <!-- District Filter -->
                    <div class="flex items-center gap-1">
                        <span class="text-[8.5px] font-black uppercase text-slate-500 font-mono">1 District:</span>
                        <select id="filter-district" onchange="applyFilters()"
                            class="bg-white border border-slate-300 rounded-lg px-2.5 py-1 text-[10px] font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                            <option value="">All Haryana Districts</option>
                            @foreach($allottedDistricts as $dist)
                                <option value="{{ $dist }}">{{ strtoupper($dist) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="flex-1 min-w-[200px] max-w-sm relative">
                        <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="custom-search-input" placeholder="Search application no, beneficiary name, flat no..."
                            class="w-full pl-7 pr-3 py-1 bg-white border border-slate-300 rounded-lg text-[10.5px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                    </div>

                    <!-- Reset Filters Button -->
                    <button type="button" onclick="resetAllFilters()"
                        class="px-2 py-1 bg-white hover:bg-slate-100 text-slate-600 border border-slate-250 rounded-lg text-[9px] font-black uppercase tracking-wider flex items-center gap-1 transition shadow-2xs cursor-pointer">
                        <i class="bi bi-arrow-counterclockwise text-indigo-600"></i>
                        <span>Reset</span>
                    </button>
                </div>

                <!-- THE DATATABLE -->
                <div class="p-2 overflow-x-auto">
                    <table id="possession-matrix-table" class="w-full display nowrap text-left">
                        <thead>
                            <tr>
                                <th class="w-8 text-center">S.No.</th>
                                <th>District</th>
                                <th>Application #</th>
                                <th>Beneficiary Name</th>
                                <th>Mobile (Masked)</th>
                                <th>Allotted Flat No.</th>
                                <th>Possession Status</th>
                                <th>Handover Date</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Server-side populated by AJAX -->
                        </tbody>
                    </table>
                </div>

            </section>

        </main>

        <!-- FOOTER -->
        <footer class="bg-white border-t border-slate-200 py-1 px-3 text-center shrink-0">
            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">
                Town & Country Planning Department • Government of Haryana • EWS Physical Possession Ledger
            </p>
        </footer>

    </div>

    <!-- FULL-PAGE ULTRA-PREMIUM LOADER OVERLAY -->
    <div id="full-page-loader" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-xs transition-all duration-300 opacity-0 pointer-events-none select-none">
        <div class="relative bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl p-6 shadow-2xl shadow-indigo-950/20 flex flex-col items-center justify-center max-w-xs w-full mx-4 text-center transform scale-95 transition-all duration-300 ease-out" id="loader-card">
            
            <!-- Ambient Glowing Neon Halos -->
            <div class="absolute -top-8 -left-8 w-28 h-28 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none animate-pulse-glow"></div>
            <div class="absolute -bottom-8 -right-8 w-28 h-28 bg-cyan-400/20 rounded-full blur-2xl pointer-events-none animate-pulse-glow" style="animation-delay: 1.2s;"></div>

            <!-- Real-time Telemetry Pill -->
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[8px] font-mono font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 mb-3 shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span>STATE POSSESSION REGISTRY</span>
            </div>

            <!-- Multi-Ring Gyroscope Spinner with Haryana State Emblem -->
            <div class="relative w-16 h-16 flex items-center justify-center mb-3">
                <!-- Outer Spinning Ring -->
                <div class="absolute inset-0 rounded-full border-3 border-transparent border-t-indigo-600 border-r-cyan-400 animate-spin" style="animation-duration: 0.9s;"></div>
                <!-- Inner Reverse Spinning Ring -->
                <div class="absolute inset-1.5 rounded-full border-2 border-transparent border-b-emerald-500 border-l-violet-500 animate-spin" style="animation-direction: reverse; animation-duration: 1.3s;"></div>
                <!-- Center Core with Emblem -->
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30 p-2 border border-slate-700/50">
                    <img src="{{ asset('Haryana_emblem.png') }}" class="w-6 h-6 object-contain brightness-0 invert drop-shadow" alt="Haryana Emblem">
                </div>
            </div>

            <!-- Dynamic Loading Heading & Description -->
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900" id="loader-title">
                Querying Possession Ledger...
            </h3>
            <p class="text-[9.5px] text-slate-500 font-medium mt-0.5 leading-snug px-1" id="loader-desc">
                Retrieving 4,211 allotted beneficiary records from master registry
            </p>

            <!-- Animated Indeterminate Gradient Progress Bar -->
            <div class="w-full bg-slate-100 rounded-full h-1 mt-4 overflow-hidden border border-slate-200/60 shadow-inner">
                <div class="h-full bg-gradient-to-r from-indigo-600 via-cyan-400 to-emerald-500 rounded-full animate-indeterminate"></div>
            </div>

            <!-- Bottom Live Audit Stamp -->
            <div class="mt-3 flex items-center justify-between w-full text-[7.5px] font-mono text-slate-400 tracking-wider">
                <span>TCP HARYANA OVERSIGHT</span>
                <span class="text-emerald-600 font-bold flex items-center gap-0.5"><i class="bi bi-shield-check"></i> SECURE SYNC</span>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        let table = null;
        let loaderTimer = null;

        function showLoader(title = 'Querying Physical Possession Ledger...', desc = 'Retrieving records from Haryana master database...') {
            $('#loader-title').text(title);
            $('#loader-desc').text(desc);
            $('#table-loading-bar').removeClass('hidden');

            const loader = $('#full-page-loader');
            const card = $('#loader-card');

            loader.removeClass('hidden pointer-events-none').addClass('flex');
            
            setTimeout(() => {
                loader.removeClass('opacity-0').addClass('opacity-100');
                card.removeClass('scale-95').addClass('scale-100');
            }, 10);
        }

        function hideLoader() {
            $('#table-loading-bar').addClass('hidden');

            const loader = $('#full-page-loader');
            const card = $('#loader-card');

            loader.removeClass('opacity-100').addClass('opacity-0');
            card.removeClass('scale-100').addClass('scale-95');

            clearTimeout(loaderTimer);
            loaderTimer = setTimeout(() => {
                loader.removeClass('flex').addClass('hidden pointer-events-none');
            }, 250);
        }

        $(document).ready(function() {
            // Initialize DataTables
            table = $('#possession-matrix-table').DataTable({
                processing: false,
                serverSide: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                ajax: {
                    url: "{{ route('ews.tcp.possession.data') }}",
                    type: "GET",
                    data: function(d) {
                        d.scope = $('#possession-scope').val();
                        d.district = $('#filter-district').val();
                        d.search = { value: $('#custom-search-input').val() };
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'w-8 text-center font-mono text-slate-400 text-[10px]' },
                    { data: 'dist_name', name: 'dist_name' },
                    { data: 'application_number', name: 'application_number' },
                    { data: 'full_name', name: 'full_name' },
                    { data: 'mobile_number', name: 'mobile_number' },
                    { data: 'flat_no', name: 'flat_no' },
                    { data: 'possession_status', name: 'possession_status', orderable: false, searchable: false },
                    { data: 'possession_date', name: 'possession_date' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-right' },
                ],
                order: [[1, 'asc']],
                language: {
                    emptyTable: '<div class="py-8 text-center text-slate-400 font-mono"><i class="bi bi-folder-x text-2xl text-slate-300 block mb-1"></i>No beneficiary physical possession records found matching the filter criteria.</div>',
                    zeroRecords: '<div class="py-8 text-center text-slate-400 font-mono"><i class="bi bi-search text-2xl text-slate-300 block mb-1"></i>No records matching your search query.</div>',
                },
                dom: '<"flex items-center justify-between p-2 border-b border-slate-100 text-[10.5px]"l<"table-telemetry">>rt<"flex items-center justify-between p-2.5 border-t border-slate-100 text-[10.5px]"ip>'
            });

            table.on('preXhr.dt', function () {
                showLoader('Querying Physical Possession Ledger...', 'Please wait while state records are retrieved...');
            });
            table.on('xhr.dt', function () {
                hideLoader();
            });
            table.on('error.dt', function () {
                hideLoader();
            });

            // Live Custom Search Input with debounce
            let searchTimeout = null;
            $('#custom-search-input').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const q = $('#custom-search-input').val();
                    if (q && q.trim().length > 0) {
                        showLoader('Searching Master Ledger...', 'Scanning application numbers, names and flat codes for "' + q.trim() + '"...');
                    }
                    table.draw();
                }, 350);
            });

            // Sync Export CSV link with current filters
            updateExportLink();
        });

        // Interactive Card Filter Trigger
        function setPossessionFilter(scope) {
            $('#possession-scope').val(scope);

            // Reset all 3 card active styling
            $('#card-possession-all')
                .removeClass('border-2 border-indigo-500 bg-indigo-50/60 shadow-2xs ring-2 ring-indigo-400/20')
                .addClass('border border-slate-200 bg-white');
            $('#card-possession-given')
                .removeClass('border-2 border-emerald-500 bg-emerald-50/60 shadow-2xs ring-2 ring-emerald-400/20')
                .addClass('border border-slate-200 bg-white');
            $('#card-possession-pending')
                .removeClass('border-2 border-rose-500 bg-rose-50/60 shadow-2xs ring-2 ring-rose-400/20')
                .addClass('border border-slate-200 bg-white');

            if (scope === 'possession_given') {
                showLoader('Filtering Handed-Over Units...', 'Retrieving certified physical possession records...');
                $('#card-possession-given')
                    .addClass('border-2 border-emerald-500 bg-emerald-50/60 shadow-2xs ring-2 ring-emerald-400/20')
                    .removeClass('border border-slate-200 bg-white');
                $('#table-heading-title').text('Physical Possession Handed Over (Certified)');
            } else if (scope === 'possession_pending') {
                showLoader('Filtering Pending Possession...', 'Retrieving records awaiting physical site handover...');
                $('#card-possession-pending')
                    .addClass('border-2 border-rose-500 bg-rose-50/60 shadow-2xs ring-2 ring-rose-400/20')
                    .removeClass('border border-slate-200 bg-white');
                $('#table-heading-title').text('Physical Possession Awaiting Handover (Pending)');
            } else {
                showLoader('Loading All Haryana Pool...', 'Retrieving 4,211 allotted beneficiary master records...');
                $('#card-possession-all')
                    .addClass('border-2 border-indigo-500 bg-indigo-50/60 shadow-2xs ring-2 ring-indigo-400/20')
                    .removeClass('border border-slate-200 bg-white');
                $('#table-heading-title').text('All Haryana State Allotted Pool (Physical Possession - 4,211 Records)');
            }

            // Sync sidebar
            $('.tcp-nav-item')
                .removeClass('bg-slate-800 text-white font-bold shadow-xs ring-1 ring-indigo-500/30 ring-emerald-500/30 ring-rose-500/30')
                .addClass('text-slate-300 hover:bg-slate-800 hover:text-white font-medium');
            $('.tcp-nav-dot').addClass('hidden');

            let sidebarId = '#sidebar-nav-possession-all';
            let ringColor = 'ring-indigo-500/30';
            if (scope === 'possession_given') {
                sidebarId = '#sidebar-nav-possession-given';
                ringColor = 'ring-emerald-500/30';
            } else if (scope === 'possession_pending') {
                sidebarId = '#sidebar-nav-possession-pending';
                ringColor = 'ring-rose-500/30';
            }

            const activeNav = $(sidebarId);
            if (activeNav.length) {
                activeNav
                    .removeClass('text-slate-300 hover:bg-slate-800 hover:text-white font-medium')
                    .addClass('bg-slate-800 text-white font-bold shadow-xs ring-1 ' + ringColor);
                activeNav.find('.tcp-nav-dot').removeClass('hidden');
            }

            updateExportLink();
            if (table) table.draw();
        }

        function applyFilters() {
            const dist = $('#filter-district').val();
            const label = dist ? dist.toUpperCase() : 'All Haryana';
            showLoader('Filtering District: ' + label + '...', 'Narrowing records for ' + label + ' jurisdiction...');
            updateExportLink();
            if (table) table.draw();
        }

        function resetAllFilters() {
            showLoader('Resetting Possession Filters...', 'Reloading 4,211 master pool records...');
            $('#filter-district').val('');
            $('#custom-search-input').val('');
            setPossessionFilter('all');
        }

        function updateExportLink() {
            let baseUrl = "{{ route('ews.tcp.possession.export', ['format' => 'csv']) }}";
            let scope = $('#possession-scope').val() || 'all';
            let district = $('#filter-district').val() || '';
            let url = baseUrl + '?scope=' + encodeURIComponent(scope);
            if (district) {
                url += '&district=' + encodeURIComponent(district);
            }
            $('#btn-export-csv').attr('href', url);
        }
    </script>
</body>
</html>

