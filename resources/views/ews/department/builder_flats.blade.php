<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offered Builder Flats Master Matrix | EWS Department Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery, DataTables & SweetAlert2 CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .code-font { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* DataTables Custom Styling (Ultra Compact, High-Density) */
        table.dataTable { border-bottom: 1px solid #cbd5e1 !important; margin-top: 4px !important; margin-bottom: 4px !important; }
        table.dataTable thead th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            font-size: 8.5px !important;
            letter-spacing: 0.04em !important;
            border-bottom: 1px solid #cbd5e1 !important;
            padding: 4px 6px !important;
            white-space: nowrap !important;
        }
        table.dataTable tbody td {
            font-size: 10px !important;
            padding: 4px 6px !important;
            color: #334155 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            line-height: 1.25 !important;
            vertical-align: middle !important;
        }
        table.dataTable tbody tr:hover { background-color: #f0fdf4 !important; }
        
        .dataTables_wrapper .dataTables_length {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            margin-bottom: 2px !important;
        }
        .dataTables_wrapper .dataTables_length select {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 1px 16px 1px 6px;
            font-size: 9.5px;
            font-weight: bold;
            outline: none;
            color: #334155;
            height: 24px;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 2px !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 9.5px;
            font-weight: 500;
            outline: none;
            height: 24px;
            margin-left: 6px;
            color: #334155;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 9px !important;
            color: #64748b !important;
            padding-top: 4px !important;
            font-weight: 600 !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 4px !important;
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
            background: #059669 !important;
            color: #ffffff !important;
            border-color: #059669 !important;
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
<body class="bg-[#f1f8f4] text-slate-800 h-screen flex overflow-hidden">

    <!-- 1. Left Sidebar -->
    @include('ews.department.partials.sidebar')

    <!-- 2. Main Page Workspace Area -->
    <div class="flex-1 min-w-0 flex flex-col ml-[260px] h-screen overflow-hidden">
        
        <!-- Top Sticky Header (Ultra Compact, Height: 44px) -->
        <header class="sticky top-0 w-full z-40 h-11 flex justify-between items-center px-3 sm:px-4 bg-white shadow-2xs border-b border-emerald-100 shrink-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('ews.department.dashboard') }}" class="flex items-center gap-1 text-slate-600 hover:text-emerald-700 transition px-2 py-0.5 rounded-md hover:bg-slate-100 text-[11px] font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[15px]">arrow_back</span>
                    <span>Dashboard</span>
                </a>
                <div class="h-4 w-[1px] bg-slate-200"></div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-slate-800 uppercase tracking-wide">EWS Builder Flats Compliance Registry</span>
                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-[8px] font-black uppercase tracking-wider">State Monitoring</span>
                    <span class="px-1.5 py-0.5 bg-cyan-100 text-cyan-800 border border-cyan-200 rounded text-[8px] font-bold uppercase tracking-wider hidden sm:inline-block">Read Only</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-2 px-2 py-0.5 bg-emerald-50/80 rounded-lg border border-emerald-200/60">
                    <div class="w-5 h-5 rounded-md bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shadow-2xs">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="text-left leading-tight">
                        <span class="block text-[9px] font-black uppercase text-slate-800 truncate max-w-[120px]">{{ $user->name }}</span>
                        <span class="block text-[7.5px] text-emerald-700 font-bold uppercase tracking-wider font-mono">Department Admin</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Workspace (Ultra High Density, Viewport Optimized) -->
        <main class="flex-1 overflow-y-auto px-3 sm:px-4 py-2 space-y-2 custom-scroll flex flex-col min-w-0">

            <!-- Hidden Filter Inputs for AJAX DataTables -->
            <input type="hidden" id="filter-ownership" value="all">

            <!-- SECTION 1: Telemetry KPI Cards (Builder Flats Oversight) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 shrink-0">
                
                <!-- Card 1: All Haryana Offered Builder Flats -->
                <div id="card-scope-all" onclick="setOwnershipFilter('all')"
                    class="cursor-pointer bg-sky-50/60 border-2 border-sky-500 rounded-xl px-3 py-2 shadow-2xs ring-2 ring-sky-400/20 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm shrink-0">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">All Haryana Offered</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-lg font-black font-mono text-sky-700 leading-none">{{ number_format($stats['total_flats'] ?? 0) }}</span>
                                <span class="text-[8px] text-slate-400 font-bold">Flats</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-sky-100 text-sky-800">State</span>
                        <i class="bi bi-chevron-right text-[10px] text-sky-600"></i>
                    </div>
                </div>

                <!-- Card 2: Approved by STP -->
                <div id="card-scope-dtp-approved" onclick="setOwnershipFilter('dtp_approved')"
                    class="cursor-pointer bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-2xs hover:border-emerald-300 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">Approved By STP</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-lg font-black font-mono text-emerald-600 leading-none">{{ number_format($stats['dtp_approved_flats'] ?? 0) }}</span>
                                <span class="text-[8px] text-slate-400 font-bold">Units</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">Certified</span>
                        <i class="bi bi-chevron-right text-[10px] text-emerald-600"></i>
                    </div>
                </div>

                <!-- Card 3: Pending STP Approval -->
                <div id="card-scope-dtp-pending" onclick="setOwnershipFilter('dtp_pending')"
                    class="cursor-pointer bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-2xs hover:border-amber-300 transition-all hover:shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm shrink-0">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider truncate">Pending STP Approval</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-lg font-black font-mono text-amber-600 leading-none">{{ number_format($stats['dtp_pending_flats'] ?? 0) }}</span>
                                <span class="text-[8px] text-slate-400 font-bold">Awaiting</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">Pending</span>
                        <i class="bi bi-chevron-right text-[10px] text-amber-600"></i>
                    </div>
                </div>

            </div>

            <!-- SECTION 2 & 3: Master Data Matrix Panel -->
            <section class="bg-white border border-slate-200 rounded-xl shadow-2xs overflow-hidden flex flex-col flex-1 min-h-0">
                
                <!-- Table Header Bar with Export Controls -->
                <div class="px-3 sm:px-4 py-1.5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                            <i class="bi bi-file-earmark-text-fill text-xs"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xs font-black uppercase tracking-wider text-slate-900" id="table-heading-title">
                                    <span>All Haryana State Offered Builder Flats</span>
                                </h2>
                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-800 text-[7.5px] font-black uppercase rounded flex items-center gap-1">
                                    <i class="bi bi-geo-alt-fill text-[7.5px]"></i>
                                    <span>Haryana State</span>
                                </span>
                            </div>
                            <p class="text-[8px] text-slate-400 font-mono uppercase">EWS Builder Housing Master Matrix • Department Oversight</p>
                        </div>
                    </div>

                    <!-- Export Toolbar -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[8.5px] font-black uppercase text-slate-400 tracking-wider">Export:</span>
                        <button type="button" onclick="triggerExport('csv')" class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded-md text-[8.5px] font-bold flex items-center gap-1 shadow-2xs cursor-pointer transition-all">
                            <i class="bi bi-filetype-csv text-emerald-600"></i>
                            <span>CSV</span>
                        </button>
                        <button type="button" onclick="triggerExport('excel')" class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded-md text-[8.5px] font-bold flex items-center gap-1 shadow-2xs cursor-pointer transition-all">
                            <i class="bi bi-file-earmark-excel text-emerald-700"></i>
                            <span>Excel</span>
                        </button>
                        <button type="button" onclick="triggerExport('pdf')" class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded-md text-[8.5px] font-bold flex items-center gap-1 shadow-2xs cursor-pointer transition-all">
                            <i class="bi bi-file-earmark-pdf text-rose-600"></i>
                            <span>PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Cascading Filter Console (Zone -> District -> Town -> Project -> License No -> Reset) -->
                <div class="px-3 sm:px-4 py-1.5 bg-slate-50/80 border-b border-slate-200 shrink-0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2 items-end">
                        
                        <!-- Step 1: Select Zone -->
                        <div id="filter-zone-box" class="lg:col-span-2 space-y-0.5">
                            <label for="filter-zone" class="block text-[8px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between whitespace-nowrap">
                                <span class="flex items-center gap-1">
                                    <span class="px-1 py-0.2 rounded text-[7.5px] bg-cyan-100 text-cyan-800 font-black">1</span>
                                    <span>Select Zone</span>
                                </span>
                                <span class="text-[7.5px] text-cyan-700 font-mono font-bold">{{ count($zonesList ?? []) }} Zones</span>
                            </label>
                            <select id="filter-zone" onchange="onZoneFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-[10px] text-slate-800 font-bold focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none shadow-2xs h-7">
                                <option value="">All Zones</option>
                                @foreach($zonesList ?? [] as $z)
                                    <option value="{{ $z->id }}">{{ strtoupper($z->name) }} ZONE</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Select District -->
                        <div id="filter-district-box" class="lg:col-span-2 space-y-0.5">
                            <label for="filter-district" class="block text-[8px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between whitespace-nowrap">
                                <span class="flex items-center gap-1">
                                    <span class="px-1 py-0.2 rounded text-[7.5px] bg-sky-100 text-sky-800 font-black">2</span>
                                    <span>Select District</span>
                                </span>
                                <span class="text-[7.5px] text-slate-400 font-mono font-bold" id="district-count-badge">{{ count($districts ?? []) }} Districts</span>
                            </label>
                            <select id="filter-district" onchange="onDistrictFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-[10px] text-slate-800 font-bold focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none shadow-2xs h-7">
                                <option value="">All Haryana Districts</option>
                                @foreach($districts ?? [] as $d)
                                    <option value="{{ $d->id }}" data-zone="{{ $d->zone_id }}">{{ strtoupper($d->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 3: Name of Town -->
                        <div id="filter-town-box" class="lg:col-span-2 space-y-0.5">
                            <label for="filter-town" class="block text-[8px] font-black uppercase text-slate-500 tracking-wider flex items-center gap-1 whitespace-nowrap">
                                <span class="px-1 py-0.2 rounded text-[7.5px] bg-violet-100 text-violet-800 font-black">3</span>
                                <span>Name of Town</span>
                            </label>
                            <select id="filter-town" onchange="onTownFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-[10px] text-slate-800 font-bold focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none shadow-2xs h-7">
                                <option value="">All Towns</option>
                                @foreach($townsList ?? [] as $t)
                                    <option value="{{ $t->id }}">{{ strtoupper($t->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 4: Name of Project -->
                        <div id="filter-project-box" class="lg:col-span-3 space-y-0.5">
                            <label for="filter-project" class="block text-[8px] font-black uppercase text-slate-500 tracking-wider flex items-center gap-1 whitespace-nowrap">
                                <span class="px-1 py-0.2 rounded text-[7.5px] bg-indigo-100 text-indigo-800 font-black">4</span>
                                <span>Name of Project</span>
                            </label>
                            <select id="filter-project" onchange="onProjectFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-[10px] text-slate-800 font-bold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-2xs h-7">
                                <option value="">All Projects</option>
                                @foreach($projectsList ?? [] as $p)
                                    <option value="{{ $p->id }}" data-license="{{ $p->license_no ?? '' }}">{{ strtoupper($p->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 5: License No. -->
                        <div id="filter-license-box" class="lg:col-span-2 space-y-0.5">
                            <label for="filter-license" class="block text-[8px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between whitespace-nowrap">
                                <span class="flex items-center gap-1">
                                    <span class="px-1 py-0.2 rounded text-[7.5px] bg-emerald-100 text-emerald-800 font-black">5</span>
                                    <span>License No.</span>
                                </span>
                                <span class="text-[7.5px] text-emerald-700 font-mono font-bold shrink-0" id="license-count-badge">{{ count($licensesList ?? []) }} Active</span>
                            </label>
                            <select id="filter-license" onchange="onLicenseFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-md px-2 py-0.5 text-[10px] text-slate-800 font-bold focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none shadow-2xs font-mono h-7">
                                <option value="">All Licenses</option>
                                @foreach($licensesList ?? [] as $lic)
                                    <option value="{{ $lic }}">{{ $lic }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="sm:col-span-2 lg:col-span-1 flex items-center">
                            <button type="button" onclick="resetAllFilters()" title="Reset All Filters"
                                class="w-full h-7 px-2 bg-slate-200 hover:bg-slate-300 text-slate-700 hover:text-slate-900 rounded-md text-[9px] font-black uppercase tracking-wider transition-all shadow-2xs flex items-center justify-center gap-1 cursor-pointer whitespace-nowrap">
                                <i class="bi bi-arrow-counterclockwise text-xs"></i>
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content (Dynamic Server-Side AJAX) -->
                <div class="px-3 py-1 flex-1 overflow-x-auto min-h-0">
                    <table class="w-full text-left border-collapse" id="flats-table">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[9px] text-slate-500 uppercase font-black tracking-wider">
                                <th style="width: 4%;">S.No.</th>
                                <th id="th-col-1">District Name</th>
                                <th id="th-col-2">Town Name</th>
                                <th id="th-col-3">Project Name</th>
                                <th id="th-col-license">License No.</th>
                                <th id="th-col-4">Block / Tower No.</th>
                                <th id="th-col-5">Floor Details</th>
                                <th id="th-col-6">Flat No.</th>
                                <th id="th-col-8">STP Status</th>
                                <th style="text-align: right; width: 6%;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-[11px] font-medium text-slate-700">
                            <!-- Populated dynamically via DataTables Ajax -->
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

    <!-- SECTION 4: Comprehensive Flat Bio-Data Modal (Read-Only) -->
    <div id="flat-bio-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="flat-bio-modal-content">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                        <i class="bi bi-file-earmark-person-fill text-sm"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">EWS Flat Comprehensive Dossier</h3>
                            <span class="px-2 py-0.2 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-[8.5px] font-black uppercase tracking-wider">Read Only</span>
                        </div>
                        <p class="text-[9px] text-slate-400 font-mono uppercase" id="bio-flat-code">—</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="printBioModal()" class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded-lg text-[9.5px] font-bold flex items-center gap-1 shadow-2xs">
                        <i class="bi bi-printer"></i>
                        <span>Print</span>
                    </button>
                    <button type="button" onclick="closeBioModal()" class="w-7 h-7 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Scrollable Content -->
            <div class="p-6 overflow-y-auto space-y-4 custom-scroll" id="printable-bio-content">
                
                <!-- 1. Architectural & Identification Card -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between text-[9px] font-black uppercase text-slate-500 tracking-wider pb-1.5 border-b border-slate-200">
                        <span class="flex items-center gap-1 text-emerald-700"><i class="bi bi-grid-fill"></i> Flat Architectural Identity</span>
                        <span class="font-mono text-emerald-700 font-bold" id="bio-dtp-badge">Status</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Flat Number</span>
                            <span class="block text-sm font-black font-mono text-violet-700" id="bio-flat-no">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Floor Level</span>
                            <span class="block text-xs font-bold text-slate-800" id="bio-floor">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Block / Tower</span>
                            <span class="block text-xs font-bold font-mono text-indigo-700" id="bio-block">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Unique Code</span>
                            <span class="block text-[11px] font-bold font-mono text-emerald-700 truncate" id="bio-code">—</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Project & Colonizer Details -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between text-[9px] font-black uppercase text-slate-500 tracking-wider pb-1.5 border-b border-slate-200">
                        <span class="flex items-center gap-1 text-indigo-700"><i class="bi bi-building"></i> Project & Colonizer Profile</span>
                        <span class="font-mono text-slate-500" id="bio-license-badge">License</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-1">
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Project Name</span>
                            <span class="block text-xs font-bold text-slate-900 uppercase" id="bio-proj-name">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Colonizer / Developer</span>
                            <span class="block text-xs font-semibold text-slate-800 uppercase" id="bio-colonizer">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">License Number</span>
                            <span class="block text-xs font-bold font-mono text-emerald-700" id="bio-license">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">District</span>
                            <span class="block text-xs font-bold text-slate-800 uppercase" id="bio-dist-name">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Town</span>
                            <span class="block text-xs font-semibold text-slate-800 uppercase" id="bio-town-name">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Sector / Location</span>
                            <span class="block text-xs font-semibold text-slate-800" id="bio-sector">—</span>
                        </div>
                    </div>
                </div>

                <!-- 2.5 Official TCP Haryana Grant Specifications -->
                <div class="bg-slate-50 border border-sky-200/90 rounded-xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between text-[9px] font-black uppercase text-slate-600 tracking-wider pb-1.5 border-b border-sky-100">
                        <span class="flex items-center gap-1 text-sky-700"><i class="bi bi-patch-check-fill text-sky-600"></i> Official TCP Grant Specifications</span>
                        <span class="font-mono text-sky-600 text-[8.5px]">Statutory Licensing Data</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-1">
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Master Development Plan</span>
                            <span class="block text-xs font-bold text-slate-900 uppercase" id="bio-devplan">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Approved Scheme / Purpose</span>
                            <span class="block text-xs font-bold text-slate-800" id="bio-purpose">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Licence Validity & Status</span>
                            <span class="block text-xs font-bold text-emerald-700" id="bio-validity-status">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Govt Case Number</span>
                            <span class="block text-xs font-mono font-bold text-slate-800 uppercase" id="bio-case-no">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Govt Application Number</span>
                            <span class="block text-xs font-mono font-bold text-slate-800 uppercase" id="bio-app-no">—</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Statutory Sanction & Building Plan Compliance (Step 10) -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between text-[9px] font-black uppercase text-slate-500 tracking-wider pb-1.5 border-b border-slate-200">
                        <span class="flex items-center gap-1 text-emerald-700"><i class="bi bi-shield-check"></i> Statutory Sanctions & Building Plan</span>
                        <span class="font-mono text-slate-400 text-[8.5px]">Step 10 Compliance</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Approved of Building Plan</span>
                            <span class="block text-xs font-bold text-slate-800" id="bio-bp-status">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Date of Approved Plan</span>
                            <span class="block text-xs font-mono font-bold text-slate-800" id="bio-bp-date">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Building Plan Copy (PDF)</span>
                            <div id="bio-bp-pdf-box" class="mt-0.5">
                                <span class="text-xs text-slate-400 font-mono">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Occupation Certificate (OC) Compliance & Quotas (Step 11) -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between text-[9px] font-black uppercase text-slate-500 tracking-wider pb-1.5 border-b border-slate-200">
                        <span class="flex items-center gap-1 text-amber-700"><i class="bi bi-file-earmark-check"></i> Occupation Certificate & Quota Compliance</span>
                        <span class="font-mono text-slate-400 text-[8.5px]">Step 11 Clearance</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1">
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Status of OC</span>
                            <span class="block text-xs font-bold text-slate-800" id="bio-oc-status">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">OC Category</span>
                            <span class="block text-xs font-bold text-slate-800" id="bio-oc-cat">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Date of OC</span>
                            <span class="block text-xs font-mono font-bold text-slate-800" id="bio-oc-date">—</span>
                        </div>
                        <div>
                            <span class="block text-[8.5px] font-bold uppercase text-slate-400">Copy Attached OC (PDF)</span>
                            <div id="bio-oc-pdf-box" class="mt-0.5">
                                <span class="text-xs text-slate-400 font-mono">—</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-200/60 text-center">
                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                            <span class="block text-[8px] font-black uppercase text-slate-400">Total Flats</span>
                            <span class="text-sm font-black font-mono text-slate-800" id="bio-total-flats">—</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                            <span class="block text-[8px] font-black uppercase text-slate-400">Total EWS Flats</span>
                            <span class="text-sm font-black font-mono text-emerald-700" id="bio-total-ews">—</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                            <span class="block text-[8px] font-black uppercase text-slate-400">Offered For Draw</span>
                            <span class="text-sm font-black font-mono text-sky-700" id="bio-offered-draw">—</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                            <span class="block text-[8px] font-black uppercase text-slate-400">Remaining For OC</span>
                            <span class="text-sm font-black font-mono text-amber-700" id="bio-remaining-oc">—</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Verification & Audit Trail Metadata -->
                <div class="bg-slate-100/70 border border-slate-200 rounded-xl p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[9.5px]">
                    <div>
                        <span class="text-slate-400 uppercase font-bold">Registered By:</span>
                        <span class="font-bold text-slate-700" id="bio-created-by">—</span>
                        <span class="text-slate-400 ml-1" id="bio-created-at">—</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold">STP Approval:</span>
                        <span class="font-bold font-mono text-emerald-700" id="bio-dtp-info">Pending Certification</span>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 border-t border-slate-150 bg-slate-50 flex items-center justify-between">
                <span class="text-[9.5px] font-bold text-slate-400 uppercase font-mono">EWS Department Haryana • Read-Only Bio-Data</span>
                <button type="button" onclick="closeBioModal()" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-bold transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Full-Page Ultra-Premium Loader Overlay -->
    <div id="full-page-loader" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs transition-all duration-300 opacity-100">
        <div class="relative bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-2xl p-6 shadow-2xl flex flex-col items-center justify-center max-w-xs w-full mx-4 text-center transform scale-100 transition-all duration-300" id="loader-card">
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
                Filtering official Haryana State compliance records
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

        // Step 0: Cascading Zone Filter Change
        function onZoneFilterChange() {
            const zoneId = $('#filter-zone').val();
            showFullPageLoader('Filtering Zone...', 'Updating districts and project matrix...');

            // 1. Fetch cascading districts for selected zone
            let distUrl = "{{ route('ews.department.builder-flats.districts') }}";
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
            let townUrl = "{{ route('ews.department.builder-flats.towns') }}";
            if (zoneId) townUrl += "?zone_id=" + zoneId;

            $.getJSON(townUrl, function(data) {
                let options = '<option value="">All Towns</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-town').html(options);
            });

            // 3. Fetch cascading projects for selected zone
            let projUrl = "{{ route('ews.department.builder-flats.projects') }}";
            if (zoneId) projUrl += "?zone_id=" + zoneId;

            $.getJSON(projUrl, function(data) {
                let options = '<option value="">All Projects</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '" data-license="' + (item.license_no || '') + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-project').html(options);
            });

            // 4. Fetch cascading licenses for selected zone
            let licUrl = "{{ route('ews.department.builder-flats.licenses') }}";
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

        // Step 1: Cascading District Filter Change
        function onDistrictFilterChange() {
            const zoneId = $('#filter-zone').val();
            const districtId = $('#filter-district').val();
            showFullPageLoader('Filtering District...', 'Narrowing towns, projects and licenses...');

            // Fetch cascading towns
            let townUrl = "{{ route('ews.department.builder-flats.towns') }}";
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
            let projUrl = "{{ route('ews.department.builder-flats.projects') }}";
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
            let licUrl = "{{ route('ews.department.builder-flats.licenses') }}";
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

        // Step 2: Cascading Town Filter Change
        function onTownFilterChange() {
            const zoneId = $('#filter-zone').val();
            const districtId = $('#filter-district').val();
            const townId = $('#filter-town').val();

            let projUrl = "{{ route('ews.department.builder-flats.projects') }}";
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

            let licUrl = "{{ route('ews.department.builder-flats.licenses') }}";
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

        // Step 3: Cascading Project Filter Change
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

        // Step 4: License Filter Change
        function onLicenseFilterChange() {
            if (table) table.draw();
        }

        // Reset All Cascading Filters
        function resetAllFilters() {
            $('#filter-zone').val('');
            $('#filter-district').val('');
            $('#filter-town').val('');
            $('#filter-project').val('');
            $('#filter-license').val('');
            onZoneFilterChange();
        }

        // Top 3 Builder Flat Telemetry Card Filter Trigger
        function setOwnershipFilter(scope) {
            $('#filter-ownership').val(scope);

            // Reset all 3 card active states
            $('#card-scope-all')
                .removeClass('border-2 border-sky-500 bg-sky-50/60 shadow-2xs ring-2 ring-sky-400/20')
                .addClass('border border-slate-200 bg-white');
            $('#card-scope-dtp-approved')
                .removeClass('border-2 border-emerald-500 bg-emerald-50/60 shadow-2xs ring-2 ring-emerald-400/20')
                .addClass('border border-slate-200 bg-white');
            $('#card-scope-dtp-pending')
                .removeClass('border-2 border-amber-500 bg-amber-50/60 shadow-2xs ring-2 ring-amber-400/20')
                .addClass('border border-slate-200 bg-white');

            if (scope === 'dtp_approved') {
                $('#card-scope-dtp-approved')
                    .addClass('border-2 border-emerald-500 bg-emerald-50/60 shadow-2xs ring-2 ring-emerald-400/20')
                    .removeClass('border border-slate-200 bg-white');
                restoreStandardHeaders();
                $('#table-heading-title').find('span:first').text('Approved EWS Flats (Certified by STP)');
            } else if (scope === 'dtp_pending') {
                $('#card-scope-dtp-pending')
                    .addClass('border-2 border-amber-500 bg-amber-50/60 shadow-2xs ring-2 ring-amber-400/20')
                    .removeClass('border border-slate-200 bg-white');
                restoreStandardHeaders();
                $('#table-heading-title').find('span:first').text('Pending EWS Flats (Awaiting STP Clearance)');
            } else {
                $('#card-scope-all')
                    .addClass('border-2 border-sky-500 bg-sky-50/60 shadow-2xs ring-2 ring-sky-400/20')
                    .removeClass('border border-slate-200 bg-white');
                restoreStandardHeaders();
                $('#table-heading-title').find('span:first').text('All Haryana State Offered Builder Flats');
            }

            if (table) table.draw();
        }

        function restoreStandardHeaders() {
            $('#th-col-1').text('District Name');
            $('#th-col-2').text('Town Name');
            $('#th-col-3').text('Project Name');
            $('#th-col-license').text('License No.');
            $('#th-col-4').text('Block / Tower No.');
            $('#th-col-5').text('Floor Details');
            $('#th-col-6').text('Flat No.');
            $('#th-col-8').text('STP Status');
            $('#filter-town-box, #filter-project-box, #filter-license-box').show();
        }

        // Export Functionality
        function triggerExport(format) {
            let url = new URL("{{ route('ews.department.builder-flats.export', ['format' => ':fmt']) }}".replace(':fmt', format));
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

        // Open Bio-Data Modal
        function viewFlatBioData(id) {
            let url = "{{ route('ews.department.builder-flats.details', ['id' => ':id']) }}".replace(':id', id);

            $.getJSON(url, function(resp) {
                if (resp && resp.success) {
                    const d = resp.data;
                    $('#bio-flat-code').text(d.flat_code || d.flat_number);
                    $('#bio-flat-no').text(d.flat_number || '—');
                    $('#bio-floor').text(d.floor || '—');
                    $('#bio-block').text(d.block_tower_number || '—');
                    $('#bio-code').text(d.flat_code || '—');

                    // STP Badge
                    if (d.is_aproved_by_dtp) {
                        $('#bio-dtp-badge').html('<span class="px-2 py-0.5 rounded text-[8.5px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300"><i class="bi bi-patch-check-fill text-emerald-600"></i> STP Approved</span>');
                        $('#bio-dtp-info').html('<span class="text-emerald-700 font-bold">' + (d.dtp_approved_at || 'Certified') + '</span> by ' + (d.dtp_user_name || 'STP'));
                    } else {
                        $('#bio-dtp-badge').html('<span class="px-2 py-0.5 rounded text-[8.5px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-300"><i class="bi bi-hourglass-split text-amber-600"></i> Pending STP</span>');
                        $('#bio-dtp-info').html('<span class="text-amber-700 font-bold">Awaiting Clearance</span>');
                    }

                    // Project Profile
                    $('#bio-license-badge').text(d.license_no ? 'Lic #' + d.license_no : 'No License');
                    $('#bio-proj-name').text(d.project_name || '—');
                    $('#bio-colonizer').text(d.colonizer_name || '—');
                    $('#bio-license').text(d.license_no || '—');
                    $('#bio-dist-name').text(d.district_name || '—');
                    $('#bio-town-name').text(d.town_name || '—');
                    $('#bio-sector').text(d.sector || '—');

                    // Official TCP Grant Specifications
                    $('#bio-devplan').text(d.master_development_plan || '—');
                    $('#bio-purpose').text(d.approved_purpose || '—');
                    const validityStr = (d.licence_validity_date && d.licence_validity_date !== '—') 
                        ? d.licence_validity_date + (d.grant_status ? ' (' + d.grant_status + ')' : '')
                        : (d.grant_status || '—');
                    $('#bio-validity-status').text(validityStr);
                    $('#bio-case-no').text(d.case_no || '—');
                    $('#bio-app-no').text(d.application_no || '—');

                    // Statutory Sanction
                    $('#bio-bp-status').text(d.is_approved_of_building_plan || '—');
                    $('#bio-bp-date').text(d.date_of_approved_building_plan || '—');
                    if (d.building_plan_pdf_url) {
                        $('#bio-bp-pdf-box').html('<a href="' + d.building_plan_pdf_url + '" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-bold bg-sky-100 text-sky-800 hover:bg-sky-200 border border-sky-300"><i class="bi bi-file-earmark-pdf-fill text-rose-600"></i> View PDF</a>');
                    } else {
                        $('#bio-bp-pdf-box').html('<span class="text-xs text-slate-400 font-mono italic">Not Attached</span>');
                    }

                    // OC Compliance
                    $('#bio-oc-status').text(d.status_of_oc || '—');
                    $('#bio-oc-cat').text(d.oc_category || '—');
                    $('#bio-oc-date').text(d.date_of_oc || '—');
                    if (d.oc_pdf_url) {
                        $('#bio-oc-pdf-box').html('<a href="' + d.oc_pdf_url + '" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 border border-emerald-300"><i class="bi bi-file-earmark-pdf-fill text-rose-600"></i> View OC PDF</a>');
                    } else {
                        $('#bio-oc-pdf-box').html('<span class="text-xs text-slate-400 font-mono italic">Not Attached</span>');
                    }

                    $('#bio-total-flats').text(d.total_flats_in_project || '—');
                    $('#bio-total-ews').text(d.total_ews_flats_in_project || '—');
                    $('#bio-offered-draw').text(d.flats_offered_for_draw || '—');
                    $('#bio-remaining-oc').text(d.remaining_flats_for_oc || '—');

                    $('#bio-created-by').text(d.created_by_name || 'Authorized Officer');
                    $('#bio-created-at').text('(' + (d.created_at || '—') + ')');

                    // Show modal
                    $('#flat-bio-modal').removeClass('hidden').addClass('flex');
                    setTimeout(() => {
                        $('#flat-bio-modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
                    }, 20);
                } else {
                    Swal.fire('Error', 'Unable to retrieve flat dossier.', 'error');
                }
            }).fail(function() {
                Swal.fire('Error', 'Network or server error while retrieving flat dossier.', 'error');
            });
        }

        function closeBioModal() {
            $('#flat-bio-modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
            setTimeout(() => {
                $('#flat-bio-modal').removeClass('flex').addClass('hidden');
            }, 200);
        }

        function printBioModal() {
            window.print();
        }

        // Initialize Yajra Server-side DataTables
        $(document).ready(function() {
            // Safety timeout: ensure loader never remains permanently stuck
            const loaderSafetyTimer = setTimeout(function() {
                hideFullPageLoader();
            }, 5000);

            // Hook DataTables AJAX Lifecycle BEFORE initialization so the very first request is handled
            $('#flats-table').on('preXhr.dt', function () {
                showFullPageLoader('Syncing EWS Matrix...', 'Fetching official Haryana compliance dataset...');
            });
            $('#flats-table').on('xhr.dt error.dt draw.dt', function () {
                clearTimeout(loaderSafetyTimer);
                hideFullPageLoader();
            });

            table = $('#flats-table').DataTable({
                processing: false, // We use the premium full-page overlay instead (prevents duplicate loader)
                serverSide: true,
                searchDelay: 350,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                ajax: {
                    url: "{{ route('ews.department.builder-flats.data') }}",
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
                    { data: 'dtp_status', name: 'dtp_status', orderable: false, searchable: false, class: 'whitespace-nowrap' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-right whitespace-nowrap' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search matrix...",
                    emptyTable: '<div class="py-12 text-center text-slate-400 font-medium"><i class="bi bi-inbox text-3xl text-slate-300 block mb-2"></i>No offered flats found in Haryana registry matching the criteria.</div>',
                    zeroRecords: '<div class="py-12 text-center text-slate-400 font-medium"><i class="bi bi-search text-3xl text-slate-300 block mb-2"></i>No records matching your search query.</div>'
                },
                order: []
            });

            // Auto-activate scope if passed in URL query param
            const urlParams = new URLSearchParams(window.location.search);
            const initialScope = urlParams.get('scope') || urlParams.get('view');
            if (initialScope && ['all', 'dtp_approved', 'dtp_pending', 'allotted', 'possession_given', 'possession_pending'].includes(initialScope)) {
                setOwnershipFilter(initialScope);
            }
        });
    </script>
</body>
</html>

