@php
    $rawZone = $displayZoneName ?? (!empty($user->district_name) ? $user->district_name : (!empty($user->zone_name) ? $user->zone_name : 'Zone'));
    $zoneTitle = str_contains(strtoupper($rawZone), 'ZONE') ? strtoupper($rawZone) : strtoupper($rawZone) . ' ZONE';
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STP {{ $zoneTitle }} - EWS Builder Flats Master Matrix | Haryana Government</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery, DataTables & SweetAlert2 CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Compact DataTables Styling (Single Viewport Fit) */
        table.dataTable {
            border-bottom: 1px solid #e2e8f0 !important;
            margin-top: 6px !important;
            margin-bottom: 6px !important;
        }
        table.dataTable thead th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            font-size: 8.5px !important;
            letter-spacing: 0.05em !important;
            border-bottom: 1px solid #cbd5e1 !important;
            padding: 6px 8px !important;
            white-space: nowrap !important;
        }
        table.dataTable tbody td {
            font-size: 10.5px !important;
            padding: 5px 8px !important;
            color: #334155 !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        table.dataTable tbody tr:hover { background-color: #f8fafc !important; }
        .dataTables_wrapper .dataTables_length select {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 2px 16px 2px 6px;
            font-size: 9.5px;
            font-weight: bold;
            outline: none;
            color: #334155;
            height: 24px;
        }
        .dataTables_wrapper .dataTables_filter input {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 9.5px;
            font-weight: 500;
            outline: none;
            margin-left: 6px;
            color: #334155;
            height: 24px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 2px 7px !important;
            margin-left: 2px !important;
            border-radius: 4px !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            font-size: 9.5px !important;
            font-weight: bold !important;
            color: #334155 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0284c7 !important;
            color: #ffffff !important;
            border-color: #0284c7 !important;
        }

        /* Full Page Indeterminate Progress Animation */
        @keyframes indeterminate {
            0% { transform: translateX(-100%) scaleX(0.2); }
            50% { transform: translateX(0%) scaleX(0.6); }
            100% { transform: translateX(100%) scaleX(0.2); }
        }
        .animate-indeterminate {
            animation: indeterminate 1.4s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
            transform-origin: left;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa] text-slate-800">

    <!-- 1. Left Sidebar -->
    @include('ews.stp.partials.sidebar')

    <!-- 2. Right Workspace Container -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">

        <!-- Top Navigation Header (Ultra Compact, Height: 44px) -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs shrink-0">
            <div class="w-full px-3 sm:px-4 flex items-center justify-between h-11">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('Haryana_emblem.png') }}" class="w-7 h-7 object-contain" alt="Haryana Emblem">
                    <div>
                        <div class="flex items-center gap-1.5 leading-none">
                            <h1 class="text-xs font-black uppercase tracking-wider text-slate-900">Special Town Planning</h1>
                            <span class="px-1.5 py-0.2 bg-cyan-100 text-cyan-800 border border-cyan-200 rounded text-[7.5px] font-black uppercase tracking-wider">STP Portal</span>
                            <span class="px-1.5 py-0.2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[7.5px] font-bold uppercase tracking-wider hidden sm:inline-block">Zone Oversight</span>
                        </div>
                        <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider hidden md:block">Government of Haryana • EWS Colony & Builder Compliance Matrix</p>
                    </div>
                </div>

                <!-- Right Side User & Session -->
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-2 px-2 py-0.5 bg-slate-100/90 rounded-lg border border-slate-200">
                        <div class="w-5 h-5 rounded-md bg-cyan-600 text-white flex items-center justify-center text-[10px] font-bold shadow-2xs">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="text-left leading-tight">
                            <span class="block text-[9px] font-black uppercase text-slate-800 truncate max-w-[130px]">{{ $user->name ?? 'STP Officer' }}</span>
                            <span class="block text-[7.5px] text-cyan-700 font-bold uppercase tracking-wider font-mono">{{ $zoneTitle }}</span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <a href="{{ route('ews.stp.logout') }}"
                        class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 rounded-md text-[9.5px] font-black uppercase tracking-wider transition-all flex items-center gap-1 shadow-2xs">
                        <i class="bi bi-box-arrow-right text-[10px]"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content Workspace (Ultra High Density, Viewport Optimized) -->
        <main class="flex-1 overflow-y-auto px-3 sm:px-4 py-2 space-y-2 custom-scroll flex flex-col">

            <!-- Hidden Filter Inputs for AJAX DataTables -->
            <input type="hidden" id="filter-ownership" value="all">

            <!-- SECTION 1: Top 4 Telemetry KPI Cards (Single Sleek Row) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 shrink-0">
                
                <!-- Card 1: Total Allotted Flats (Physical Possession Module Navigation) -->
                <a href="{{ route('ews.stp.possession.index') }}" id="card-physical-possession"
                    class="cursor-pointer bg-white hover:bg-amber-50/50 border border-slate-200 hover:border-amber-400 rounded-xl px-3 py-1.5 shadow-2xs transition-all hover:shadow-xs flex items-center justify-between group">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm shrink-0 group-hover:scale-105 transition-transform">
                            <i class="bi bi-houses-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">Total Allotted Flats</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-lg font-black font-mono text-amber-600 leading-none">{{ number_format($stats['total_allotted'] ?? 639) }}</span>
                                <span class="text-[8px] text-slate-400 font-bold">Possession</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">Module</span>
                        <i class="bi bi-arrow-right text-[10px] text-amber-600 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </a>

                <!-- Card 2: All Zone Offered Builder Flats -->
                <div id="card-scope-all" onclick="setOwnershipFilter('all')"
                    class="cursor-pointer bg-sky-50/60 border-2 border-sky-500 rounded-xl px-3 py-1.5 shadow-2xs ring-2 ring-sky-400/20 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm shrink-0">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">All {{ strtoupper(trim(str_ireplace('ZONE', '', $displayZoneName ?? 'Rohtak'))) }} Offered</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-lg font-black font-mono text-sky-700 leading-none">{{ number_format($stats['total_flats'] ?? 0) }}</span>
                                <span class="text-[8px] text-slate-400 font-bold">Flats</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-sky-100 text-sky-800">Zone</span>
                        <i class="bi bi-chevron-right text-[10px] text-sky-600"></i>
                    </div>
                </div>

                <!-- Card 3: Approved by DTP -->
                <div id="card-scope-dtp-approved" onclick="setOwnershipFilter('dtp_approved')"
                    class="cursor-pointer bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-2xs hover:border-emerald-300 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">Approved By DTP</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-lg font-black font-mono text-emerald-600 leading-none">{{ number_format($stats['dtp_approved_flats'] ?? 0) }}</span>
                                <span class="text-[8px] text-slate-400 font-bold">Units</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">Certified</span>
                        <i class="bi bi-chevron-right text-[10px] text-emerald-600"></i>
                    </div>
                </div>

                <!-- Card 4: Pending DTP Approval -->
                <div id="card-scope-dtp-pending" onclick="setOwnershipFilter('dtp_pending')"
                    class="cursor-pointer bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-2xs hover:border-amber-300 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm shrink-0">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">Pending DTP Approval</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-lg font-black font-mono text-amber-600 leading-none">{{ number_format($stats['dtp_pending_flats'] ?? 0) }}</span>
                                <span class="text-[8px] text-slate-400 font-bold">Awaiting</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">Pending</span>
                        <i class="bi bi-chevron-right text-[10px] text-amber-600"></i>
                    </div>
                </div>

            </div>

            <!-- SECTION 2 & 3: Master Data Matrix Panel (High Density) -->
            <section class="bg-white border border-slate-200 rounded-xl shadow-2xs overflow-hidden flex flex-col flex-1 min-h-0">
                
                <!-- Table Header Bar with Export Controls -->
                <div class="px-3.5 py-1.5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 bg-white shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-sky-50 text-sky-700 border border-sky-200 flex items-center justify-center text-xs shrink-0">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 leading-none">
                                <h2 class="text-xs font-black uppercase tracking-wider text-slate-900" id="table-heading-title">
                                    <span>All {{ $zoneTitle }} Offered Builder Flats</span>
                                </h2>
                                <span class="px-1.5 py-0.5 bg-sky-100 text-sky-800 text-[8px] font-black uppercase rounded flex items-center gap-1 border border-sky-200">
                                    <i class="bi bi-shield-lock-fill text-[8px] text-sky-600"></i>
                                    <span>{{ $zoneTitle }} (LOCKED)</span>
                                </span>
                            </div>
                            <p class="text-[8px] text-slate-400 font-mono uppercase">EWS Builder Housing Master Matrix • STP Oversight</p>
                        </div>
                    </div>

                    <!-- Export Toolbar -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[8.5px] font-black uppercase text-slate-400 tracking-wider">Export:</span>
                        <button type="button" onclick="triggerExport('csv')" class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded text-[9px] font-bold flex items-center gap-1 shadow-2xs cursor-pointer transition-all">
                            <i class="bi bi-filetype-csv text-emerald-600"></i>
                            <span>CSV</span>
                        </button>
                        <button type="button" onclick="triggerExport('excel')" class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded text-[9px] font-bold flex items-center gap-1 shadow-2xs cursor-pointer transition-all">
                            <i class="bi bi-file-earmark-excel text-emerald-700"></i>
                            <span>Excel</span>
                        </button>
                        <button type="button" onclick="triggerExport('pdf')" class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded text-[9px] font-bold flex items-center gap-1 shadow-2xs cursor-pointer transition-all">
                            <i class="bi bi-file-earmark-pdf text-rose-600"></i>
                            <span>PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Cascading Filter Console (Locked Zone -> 2 District -> 3 Town -> 4 Project -> 5 License No -> Reset) -->
                <div class="px-3 py-1.5 bg-slate-50/90 border-b border-slate-200 shrink-0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2 items-end">
                        
                        <!-- Step 1: Assigned Zone (Permanently Locked) -->
                        <div id="filter-zone-box" class="lg:col-span-2 space-y-0.5">
                            <label class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between whitespace-nowrap">
                                <span class="flex items-center gap-1">
                                    <span class="px-1 py-0.2 rounded text-[7.5px] bg-slate-200 text-slate-700 font-black">1</span>
                                    <span>Assigned Zone</span>
                                </span>
                                <span class="text-[7.5px] text-cyan-600 font-mono font-bold">LOCKED</span>
                            </label>
                            <div class="w-full bg-slate-100 border border-slate-300 rounded-md px-2 py-0.5 text-xs text-slate-700 font-bold flex items-center gap-1.5 h-[28px] cursor-not-allowed select-none shadow-2xs">
                                <i class="bi bi-lock-fill text-slate-400 text-xs"></i>
                                <span class="truncate">{{ $zoneTitle }}</span>
                            </div>
                            <input type="hidden" id="filter-zone" value="{{ $user->zone_id }}">
                        </div>

                        <!-- Step 2: Select District (Zone-Scoped) -->
                        <div id="filter-district-box" class="lg:col-span-2 space-y-0.5">
                            <label for="filter-district" class="block text-[8.5px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between whitespace-nowrap">
                                <span class="flex items-center gap-1">
                                    <span class="px-1 py-0.2 rounded text-[7.5px] bg-sky-100 text-sky-800 font-black">2</span>
                                    <span>Select District</span>
                                </span>
                                <span class="text-[7.5px] text-slate-400 font-mono font-bold" id="district-count-badge">{{ count($districts ?? []) }} In Zone</span>
                            </label>
                            <select id="filter-district" onchange="onDistrictFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none shadow-2xs h-[28px]">
                                <option value="">All {{ $zoneTitle }} Districts</option>
                                @foreach($districts ?? [] as $d)
                                    <option value="{{ $d->id }}" data-zone="{{ $d->zone_id }}">{{ strtoupper($d->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 3: Name of Town -->
                        <div id="filter-town-box" class="lg:col-span-2 space-y-0.5">
                            <label for="filter-town" class="block text-[8.5px] font-black uppercase text-slate-600 tracking-wider flex items-center gap-1 whitespace-nowrap">
                                <span class="px-1 py-0.2 rounded text-[7.5px] bg-violet-100 text-violet-800 font-black">3</span>
                                <span>Name of Town</span>
                            </label>
                            <select id="filter-town" onchange="onTownFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-xs text-slate-800 font-bold focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none shadow-2xs h-[28px]">
                                <option value="">All Towns</option>
                                @foreach($townsList ?? [] as $t)
                                    <option value="{{ $t->id }}">{{ strtoupper($t->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 4: Name of Project -->
                        <div id="filter-project-box" class="lg:col-span-3 space-y-0.5">
                            <label for="filter-project" class="block text-[8.5px] font-black uppercase text-slate-600 tracking-wider flex items-center gap-1 whitespace-nowrap">
                                <span class="px-1 py-0.2 rounded text-[7.5px] bg-indigo-100 text-indigo-800 font-black">4</span>
                                <span>Name of Project</span>
                            </label>
                            <select id="filter-project" onchange="onProjectFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-xs text-slate-800 font-bold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-2xs h-[28px]">
                                <option value="">All Projects</option>
                                @foreach($projectsList ?? [] as $p)
                                    <option value="{{ $p->id }}" data-license="{{ $p->license_no ?? '' }}">{{ strtoupper($p->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 5: License Number -->
                        <div id="filter-license-box" class="lg:col-span-2 space-y-0.5">
                            <label for="filter-license" class="block text-[8.5px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between whitespace-nowrap">
                                <span class="flex items-center gap-1">
                                    <span class="px-1 py-0.2 rounded text-[7.5px] bg-emerald-100 text-emerald-800 font-black">5</span>
                                    <span>License No.</span>
                                </span>
                                <span class="text-[7.5px] text-emerald-700 font-mono font-bold" id="license-count-badge">{{ count($licensesList ?? []) }} Active</span>
                            </label>
                            <select id="filter-license" onchange="onLicenseFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-xs text-slate-800 font-bold focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none shadow-2xs h-[28px]">
                                <option value="">All Licenses</option>
                                @foreach($licensesList ?? [] as $lic)
                                    <option value="{{ $lic }}">{{ $lic }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="lg:col-span-1">
                            <button type="button" onclick="resetAllFilters()" title="Reset All Filters"
                                class="w-full h-[28px] bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md text-[9px] font-black uppercase tracking-wider transition-all flex items-center justify-center gap-1 shadow-2xs cursor-pointer">
                                <i class="bi bi-arrow-counterclockwise text-xs"></i>
                                <span>Reset</span>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Server-Side Yajra DataTable Container -->
                <div class="px-3 py-1 flex-1 overflow-x-auto min-h-0">
                    <table id="flats-table" class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="w-8 text-center">S.No.</th>
                                <th id="th-col-1">District Name</th>
                                <th id="th-col-2">Town Name</th>
                                <th id="th-col-3">Project Name</th>
                                <th id="th-col-license">License No.</th>
                                <th id="th-col-4">Block / Tower</th>
                                <th id="th-col-5">Floor</th>
                                <th id="th-col-6">Flat No.</th>
                                <th id="th-col-7">Unique Flat Code</th>
                                <th id="th-col-8">DTP Status</th>
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

        <!-- Footer (Ultra Compact) -->
        <footer class="bg-white border-t border-slate-200 py-1 shrink-0">
            <div class="w-full px-3 text-center">
                <p class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider">
                    Special Town Planning Department • Government of Haryana • EWS Regulatory Cell
                </p>
            </div>
        </footer>

    </div><!-- End Right Workspace Container -->

    <!-- Full-Page Ultra-Premium Loader Overlay -->
    <div id="full-page-loader" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-xs transition-all duration-300 opacity-0 pointer-events-none">
        <div class="relative bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-2xl p-6 shadow-2xl flex flex-col items-center justify-center max-w-xs w-full mx-4 text-center transform scale-95 transition-all duration-300" id="loader-card">
            <!-- Ambient Background Glow -->
            <div class="absolute -top-6 -left-6 w-24 h-24 bg-cyan-400/20 rounded-full blur-xl pointer-events-none"></div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-500/20 rounded-full blur-xl pointer-events-none"></div>

            <!-- Multi-ring Spinner with Emblem -->
            <div class="relative w-16 h-16 flex items-center justify-center mb-3">
                <div class="absolute inset-0 rounded-full border-3 border-transparent border-t-cyan-500 border-r-blue-600 animate-spin"></div>
                <div class="absolute inset-1.5 rounded-full border-2 border-transparent border-b-emerald-500 border-l-cyan-400 animate-spin" style="animation-direction: reverse; animation-duration: 1.2s;"></div>
                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-md shadow-cyan-500/30">
                    <img src="{{ asset('Haryana_emblem.png') }}" class="w-6 h-6 object-contain brightness-0 invert" alt="Haryana">
                </div>
            </div>

            <!-- Loading Text -->
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900" id="loader-title">
                Syncing EWS Matrix...
            </h3>
            <p class="text-[9px] text-slate-500 font-semibold mt-0.5" id="loader-subtitle">
                Filtering official {{ $zoneTitle }} compliance records
            </p>

            <!-- Animated Gradient Progress Bar -->
            <div class="w-full bg-slate-100 rounded-full h-1 mt-3.5 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-cyan-500 via-blue-500 to-emerald-500 rounded-full animate-indeterminate"></div>
            </div>
        </div>
    </div>

    <!-- JavaScript Application Logic -->
    <script>
        let table;

        // Full Page High-Density Loader Overlay Control
        function showFullPageLoader(title = 'Syncing EWS Matrix...', subtitle = 'Filtering official Haryana State compliance records') {
            $('#loader-title').text(title);
            $('#loader-subtitle').text(subtitle);
            $('#full-page-loader').removeClass('hidden pointer-events-none').addClass('flex');
            setTimeout(() => {
                $('#full-page-loader').removeClass('opacity-0').addClass('opacity-100');
                $('#loader-card').removeClass('scale-95').addClass('scale-100');
            }, 10);
        }

        function hideFullPageLoader() {
            $('#full-page-loader').removeClass('opacity-100').addClass('opacity-0');
            $('#loader-card').removeClass('scale-100').addClass('scale-95');
            setTimeout(() => {
                $('#full-page-loader').removeClass('flex').addClass('hidden pointer-events-none');
            }, 250);
        }

        // Step 1: Cascading Zone Filter Change
        function onZoneFilterChange() {
            const zoneId = $('#filter-zone').val();
            showFullPageLoader('Filtering Zone...', 'Updating districts and project matrix...');

            // 1. Fetch cascading districts for selected zone
            let distUrl = "{{ route('ews.stp.districts') }}";
            if (zoneId) distUrl += "?zone_id=" + zoneId;

            $.getJSON(distUrl, function(data) {
                let options = '<option value="">' + (zoneId ? 'All Zone Districts' : 'All Districts') + '</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '" data-zone="' + item.zone_id + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-district').html(options);
                $('#district-count-badge').text(data.length + ' Districts');
            });

            // 2. Fetch cascading towns for selected zone
            let townUrl = "{{ route('ews.stp.towns') }}";
            if (zoneId) townUrl += "?zone_id=" + zoneId;

            $.getJSON(townUrl, function(data) {
                let options = '<option value="">All Towns</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-town').html(options);
            });

            // 3. Fetch cascading projects for selected zone
            let projUrl = "{{ route('ews.stp.projects') }}";
            if (zoneId) projUrl += "?zone_id=" + zoneId;

            $.getJSON(projUrl, function(data) {
                let options = '<option value="">All Projects</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '" data-license="' + (item.license_no || '') + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-project').html(options);
            });

            // 4. Fetch cascading licenses for selected zone
            let licUrl = "{{ route('ews.stp.licenses') }}";
            if (zoneId) licUrl += "?zone_id=" + zoneId;

            $.getJSON(licUrl, function(data) {
                let options = '<option value="">All Licenses</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item + '">' + item + '</option>';
                });
                $('#filter-license').html(options);
                $('#license-count-badge').text(data.length + ' Active');
            });

            if (table) table.draw();
        }

        // Step 2: Cascading District Filter Change
        function onDistrictFilterChange() {
            const zoneId = $('#filter-zone').val();
            const districtId = $('#filter-district').val();
            showFullPageLoader('Filtering District...', 'Narrowing towns, projects and licenses...');

            // Fetch cascading towns
            let townUrl = "{{ route('ews.stp.towns') }}";
            let params = [];
            if (districtId) params.push("district_id=" + districtId);
            else if (zoneId) params.push("zone_id=" + zoneId);
            if (params.length) townUrl += "?" + params.join("&");

            $.getJSON(townUrl, function(data) {
                let options = '<option value="">All Towns</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-town').html(options);
            });

            // Fetch cascading projects
            let projUrl = "{{ route('ews.stp.projects') }}";
            let projParams = [];
            if (districtId) projParams.push("district_id=" + districtId);
            else if (zoneId) projParams.push("zone_id=" + zoneId);
            if (projParams.length) projUrl += "?" + projParams.join("&");

            $.getJSON(projUrl, function(data) {
                let options = '<option value="">All Projects</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '" data-license="' + (item.license_no || '') + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-project').html(options);
            });

            // Fetch cascading licenses
            let licUrl = "{{ route('ews.stp.licenses') }}";
            let licParams = [];
            if (districtId) licParams.push("district_id=" + districtId);
            else if (zoneId) licParams.push("zone_id=" + zoneId);
            if (licParams.length) licUrl += "?" + licParams.join("&");

            $.getJSON(licUrl, function(data) {
                let options = '<option value="">All Licenses</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item + '">' + item + '</option>';
                });
                $('#filter-license').html(options);
                $('#license-count-badge').text(data.length + ' Active');
            });

            if (table) table.draw();
        }

        // Step 3: Cascading Town Filter Change
        function onTownFilterChange() {
            const zoneId = $('#filter-zone').val();
            const districtId = $('#filter-district').val();
            const townId = $('#filter-town').val();

            let projUrl = "{{ route('ews.stp.projects') }}";
            let params = [];
            if (districtId) params.push("district_id=" + districtId);
            else if (zoneId) params.push("zone_id=" + zoneId);
            if (townId) params.push("town_id=" + townId);
            if (params.length) projUrl += "?" + params.join("&");

            $.getJSON(projUrl, function(data) {
                let options = '<option value="">All Projects</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '" data-license="' + (item.license_no || '') + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-project').html(options);
            });

            let licUrl = "{{ route('ews.stp.licenses') }}";
            let licParams = [];
            if (districtId) licParams.push("district_id=" + districtId);
            else if (zoneId) licParams.push("zone_id=" + zoneId);
            if (townId) licParams.push("town_id=" + townId);
            if (licParams.length) licUrl += "?" + licParams.join("&");

            $.getJSON(licUrl, function(data) {
                let options = '<option value="">All Licenses</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item + '">' + item + '</option>';
                });
                $('#filter-license').html(options);
                $('#license-count-badge').text(data.length + ' Active');
            });

            if (table) table.draw();
        }

        // Step 4: Cascading Project Filter Change
        function onProjectFilterChange() {
            const selectedOpt = $('#filter-project option:selected');
            const lic = selectedOpt.data('license');
            if (lic) {
                if ($('#filter-license option[value="' + lic + '"]').length) {
                    $('#filter-license').val(lic);
                }
            }
            if (table) table.draw();
        }

        // Step 5: License Filter Change
        function onLicenseFilterChange() {
            if (table) table.draw();
        }

        // Reset All Cascading Filters
        function resetAllFilters() {
            $('#filter-district').val('');
            $('#filter-town').val('');
            $('#filter-project').val('');
            $('#filter-license').val('');
            onDistrictFilterChange();
        }

        // Active Sidebar Navigation Handler
        function activateStpNav(target) {
            let label = 'Dashboard';
            if (target === 'all') label = 'Total Offered Flats';
            if (target === 'dtp_approved') label = 'Approved (DTP) Units';
            if (target === 'dtp_pending') label = 'Pending Approval Units';
            showFullPageLoader('Loading ' + label + '...', 'Retrieving filtered records from master database...');

            // 1. Reset all sidebar links to inactive
            $('.stp-nav-item')
                .removeClass('bg-slate-800 text-white font-bold shadow-xs ring-1 ring-cyan-500/30 ring-sky-500/30 ring-emerald-500/30 ring-amber-500/30')
                .addClass('text-slate-300 hover:bg-slate-800 hover:text-white font-medium');
            
            // Hide all dots
            $('.stp-nav-dot').addClass('hidden');

            // 2. Activate only the selected link
            let ringColor = 'ring-cyan-500/30';
            if (target === 'dtp_approved') ringColor = 'ring-emerald-500/30';
            if (target === 'dtp_pending') ringColor = 'ring-amber-500/30';
            if (target === 'all') ringColor = 'ring-sky-500/30';

            const activeEl = $('#sidebar-nav-' + target);
            if (activeEl.length) {
                activeEl
                    .removeClass('text-slate-300 hover:bg-slate-800 hover:text-white font-medium')
                    .addClass('bg-slate-800 text-white font-bold shadow-xs ring-1 ' + ringColor);
                activeEl.find('.stp-nav-dot').removeClass('hidden');
            }

            // 3. Trigger ownership filter
            if (target === 'dashboard') {
                setOwnershipFilter('all', false);
                $('main').animate({ scrollTop: 0 }, 150);
            } else {
                setOwnershipFilter(target, false);
            }
        }

        // Top 3 Telemetry Card Filter Trigger
        function setOwnershipFilter(scope, updateSidebar = true) {
            $('#filter-ownership').val(scope);

            $('#card-scope-all')
                .removeClass('border-sky-500 bg-sky-50/60 shadow-2xs ring-2 ring-sky-400/20')
                .addClass('border-slate-200 bg-white');
            $('#card-scope-dtp-approved')
                .removeClass('border-emerald-500 bg-emerald-50/60 shadow-2xs ring-2 ring-emerald-400/20')
                .addClass('border-slate-200 bg-white');
            $('#card-scope-dtp-pending')
                .removeClass('border-amber-500 bg-amber-50/60 shadow-2xs ring-2 ring-amber-400/20')
                .addClass('border-slate-200 bg-white');

            if (scope === 'dtp_approved') {
                $('#card-scope-dtp-approved')
                    .addClass('border-emerald-500 bg-emerald-50/60 shadow-2xs ring-2 ring-emerald-400/20')
                    .removeClass('border-slate-200 bg-white');
                $('#table-heading-title').find('span:first').text('Approved EWS Flats (Certified by DTP)');
            } else if (scope === 'dtp_pending') {
                $('#card-scope-dtp-pending')
                    .addClass('border-amber-500 bg-amber-50/60 shadow-2xs ring-2 ring-amber-400/20')
                    .removeClass('border-slate-200 bg-white');
                $('#table-heading-title').find('span:first').text('Pending EWS Flats (Awaiting DTP Clearance)');
            } else {
                $('#card-scope-all')
                    .addClass('border-sky-500 bg-sky-50/60 shadow-2xs ring-2 ring-sky-400/20')
                    .removeClass('border-slate-200 bg-white');
                $('#table-heading-title').find('span:first').text('All {{ $zoneTitle }} Offered Builder Flats');
            }

            // Sync sidebar link active state when filter is changed from cards
            if (updateSidebar) {
                $('.stp-nav-item')
                    .removeClass('bg-slate-800 text-white font-bold shadow-xs ring-1 ring-cyan-500/30 ring-sky-500/30 ring-emerald-500/30 ring-amber-500/30')
                    .addClass('text-slate-300 hover:bg-slate-800 hover:text-white font-medium');
                $('.stp-nav-dot').addClass('hidden');

                let navKey = (scope === 'all') ? 'all' : scope;
                let ringColor = (scope === 'dtp_approved') ? 'ring-emerald-500/30' : ((scope === 'dtp_pending') ? 'ring-amber-500/30' : 'ring-sky-500/30');
                const activeNav = $('#sidebar-nav-' + navKey);
                if (activeNav.length) {
                    activeNav
                        .removeClass('text-slate-300 hover:bg-slate-800 hover:text-white font-medium')
                        .addClass('bg-slate-800 text-white font-bold shadow-xs ring-1 ' + ringColor);
                    activeNav.find('.stp-nav-dot').removeClass('hidden');
                }
            }

            if (table) table.draw();
        }

        // Export Functionality
        function triggerExport(format) {
            let url = new URL("{{ route('ews.stp.flats.export', ['format' => ':fmt']) }}".replace(':fmt', format));
            url.searchParams.set('ownership_scope', $('#filter-ownership').val());
            url.searchParams.set('zone_id', $('#filter-zone').val());
            url.searchParams.set('district_id', $('#filter-district').val());
            url.searchParams.set('town_id', $('#filter-town').val());
            url.searchParams.set('project_id', $('#filter-project').val());
            url.searchParams.set('license_no', $('#filter-license').val());
            if (table) {
                let searchVal = table.search();
                if (searchVal) url.searchParams.set('search', searchVal);
            }

            Swal.fire({
                icon: 'info',
                title: 'EXPORT MATRIX',
                text: 'Your ' + format.toUpperCase() + ' download is starting natively...',
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });

            window.location.href = url.toString();
        }

        // Initialize Yajra Server-side DataTables
        $(document).ready(function() {
            table = $('#flats-table').DataTable({
                processing: false, // We use the custom full-page overlay instead
                serverSide: true,
                searchDelay: 350,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                ajax: {
                    url: "{{ route('ews.stp.flats.data') }}",
                    data: function (d) {
                        d.ownership_scope = $('#filter-ownership').val();
                        d.zone_id = $('#filter-zone').val();
                        d.district_id = $('#filter-district').val();
                        d.town_id = $('#filter-town').val();
                        d.project_id = $('#filter-project').val();
                        d.license_no = $('#filter-license').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center font-mono text-[9px] text-slate-500' },
                    { data: 'district_name', name: 'district_name', class: 'font-bold text-slate-900 uppercase whitespace-nowrap' },
                    { data: 'town_name', name: 'town_name', class: 'whitespace-nowrap text-slate-700' },
                    { data: 'project_name', name: 'project_name', class: 'text-slate-800 font-bold whitespace-nowrap uppercase' },
                    { data: 'license_no', name: 'license_no', class: 'font-mono text-slate-700 whitespace-nowrap' },
                    { data: 'block_tower_number', name: 'block_tower_number', class: 'text-indigo-650 font-mono font-bold whitespace-nowrap' },
                    { data: 'floor', name: 'floor', class: 'font-bold text-slate-800 whitespace-nowrap' },
                    { data: 'flat_number', name: 'flat_number', class: 'text-violet-655 font-black font-mono whitespace-nowrap' },
                    { data: 'flat_code', name: 'flat_code', class: 'text-emerald-600 font-bold font-mono whitespace-nowrap' },
                    { data: 'dtp_status', name: 'dtp_status', orderable: false, searchable: false, class: 'whitespace-nowrap' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-right whitespace-nowrap' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search matrix...",
                },
                order: []
            });

            // Hook DataTables AJAX Lifecycle to Full-Page Loader Overlay
            table.on('preXhr.dt', function () {
                showFullPageLoader('Syncing EWS Matrix...', 'Fetching official {{ $zoneTitle }} compliance dataset...');
            });
            table.on('xhr.dt', function () {
                hideFullPageLoader();
            });
            table.on('error.dt', function () {
                hideFullPageLoader();
            });

            // Auto-activate scope if passed in URL query param
            const urlParams = new URLSearchParams(window.location.search);
            const initialScope = urlParams.get('scope') || urlParams.get('view');
            if (initialScope && ['all', 'dtp_approved', 'dtp_pending', 'dashboard'].includes(initialScope)) {
                activateStpNav(initialScope);
            }
        });
    </script>
</body>
</html>
