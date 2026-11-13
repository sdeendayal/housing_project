<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS STP - License & Flat Verification Console</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .code-font { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dev-shadow { box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08); }

        /* Custom Select2 Tailwind Styling */
        .select2-container { width: 100% !important; }
        .select2-container--default .select2-selection--single {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important;
            height: 38px !important;
            padding: 5px 12px !important;
            display: flex;
            align-items: center;
            outline: none !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            font-family: 'Fira Code', monospace;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #0284c7 !important;
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15) !important;
        }
        .select2-dropdown {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
        }
        .select2-results__option {
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            padding: 8px 12px !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0284c7 !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    @include('ews.stp.partials.sidebar')

    <!-- RIGHT CONTAINER WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                    <i class="bi bi-patch-check-fill text-base"></i>
                </div>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase">
                        STP Verification Console
                    </h2>
                    <p class="text-[8px] text-slate-455 font-mono uppercase">EWS License & Multi-Record Audit Matrix</p>
                </div>
            </div>

            <!-- Profile Context Details -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-[10px] text-slate-700 font-bold flex items-center gap-1 justify-end">
                        <span>{{ $user->name }}</span>
                        @if(!empty($displayZoneName))
                            <span class="text-[9px] bg-emerald-100 text-emerald-800 font-extrabold uppercase px-1.5 py-0.5 rounded border border-emerald-200">({{ $displayZoneName }})</span>
                        @endif
                        <i class="bi bi-person-circle text-emerald-600"></i>
                    </div>
                    <div class="text-[8.5px] text-slate-500 font-mono">
                        Zone: <span class="font-bold text-slate-700 uppercase">{{ $displayZoneName ?? 'N/A' }}</span> | Mobile: {{ $user->mobile }}
                    </div>
                </div>
                <a href="{{ route('ews.stp.logout') }}" class="md:hidden px-3 py-1.5 bg-red-50 text-red-650 rounded-lg text-[9px] font-black uppercase border border-red-100">
                    Logout
                </a>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3.5 custom-scroll">
            
            <!-- Sleek Compact Welcome Ribbon -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 rounded-xl px-4 py-2.5 text-white shadow-sm flex items-center justify-between gap-2.5 relative overflow-hidden">
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 shrink-0">
                        <i class="bi bi-shield-check text-sm"></i>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-2.5 gap-y-0.5">
                        <h2 class="text-xs font-black tracking-tight text-white uppercase">License & EWS Flat Verification</h2>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded text-[8.5px] font-black uppercase">
                            <i class="bi bi-geo-alt-fill text-[8px]"></i> {{ $displayZoneName ?? 'ZONE' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-[9px] text-slate-300 font-mono">
                    <span class="hidden sm:inline">Active Licenses:</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 font-mono" id="badge-total-licenses">{{ count($licenses) }} In Zone</span>
                </div>
            </div>

            <!-- Compact Inline Verification Form Console -->
            <section class="bg-white border border-slate-200/90 rounded-xl shadow-xs p-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                    
                    <!-- 1. STP Assigned Zone (Locked Field) -->
                    <div class="lg:col-span-3 space-y-1">
                        <label class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <span class="px-1 py-0.2 rounded text-[8px] bg-slate-200 text-slate-800 font-black">1</span>
                                <span>STP Assigned Zone</span>
                            </span>
                            <span class="text-[8px] font-mono text-slate-400 uppercase">Fixed</span>
                        </label>
                        <div class="w-full bg-slate-100/90 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-black uppercase tracking-wider flex items-center justify-between shadow-2xs">
                            <div class="flex items-center gap-2 truncate">
                                <i class="bi bi-shield-lock-fill text-sky-600 text-sm"></i>
                                <span class="truncate">{{ $displayZoneName ?? 'ZONE' }}</span>
                            </div>
                            <span class="px-1.5 py-0.2 rounded text-[8px] bg-slate-200 text-slate-700 font-mono font-bold shrink-0">ZONE {{ $zoneId }}</span>
                        </div>
                    </div>

                    <!-- 2. Select District (Cascading) -->
                    <div class="lg:col-span-3 space-y-1">
                        <label for="filter-district" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <span class="px-1 py-0.2 rounded text-[8px] bg-sky-100 text-sky-800 font-black">2</span>
                                <span>Name of District</span>
                            </span>
                            <span class="text-[8px] font-mono text-sky-600 font-bold uppercase">{{ count($districts) }} in Zone</span>
                        </label>
                        <select id="filter-district" onchange="onDistrictChange()"
                            class="w-full bg-white border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none shadow-2xs">
                            <option value="">All {{ $displayZoneName }} Districts</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Select Town (Cascading) -->
                    <div class="lg:col-span-2 space-y-1">
                        <label for="filter-town" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center gap-1">
                            <span class="px-1 py-0.2 rounded text-[8px] bg-violet-100 text-violet-800 font-black">3</span>
                            <span>Name of Town</span>
                        </label>
                        <select id="filter-town" onchange="onTownChange()"
                            class="w-full bg-white border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none shadow-2xs">
                            <option value="">All Towns</option>
                        </select>
                    </div>

                    <!-- 4. License Number Dropdown with Select2 Search -->
                    <div class="lg:col-span-3 space-y-1">
                        <label for="filter-license" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <span class="px-1 py-0.2 rounded text-[8px] bg-emerald-100 text-emerald-800 font-black">4</span>
                                <span>License Number</span>
                            </span>
                            <span class="text-[8px] font-mono text-emerald-700 font-bold uppercase flex items-center gap-0.5">
                                <i class="bi bi-search text-[7.5px]"></i> Select2
                            </span>
                        </label>
                        <select id="filter-license" class="select2-license w-full">
                            <option value="">-- Select License No --</option>
                            @foreach($licenses as $lic)
                                <option value="{{ $lic }}">{{ $lic }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 5. Reset Filter Button -->
                    <div class="lg:col-span-1 flex items-center">
                        <button type="button" onclick="resetVerificationFilters()" title="Reset Filters"
                            class="w-full h-[38px] bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-2xs flex items-center justify-center gap-1 border border-slate-200">
                            <i class="bi bi-arrow-counterclockwise text-sm"></i>
                            <span class="hidden sm:inline lg:hidden">Reset</span>
                        </button>
                    </div>

                </div>
            </section>

            <!-- DYNAMIC RESULTS CONTAINER -->
            <div id="verify-results-container" class="space-y-3.5">
                
                <!-- State 1: Awaiting Selection Prompt (Default) -->
                <div id="prompt-state" class="bg-white border border-slate-200/90 rounded-2xl p-8 text-center shadow-xs">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xl shadow-xs mb-2">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-600">Please Select License Number Above</h3>
                </div>

                <!-- State 2: Loading Spinner -->
                <div id="loading-state" class="hidden bg-white border border-slate-200/90 rounded-2xl p-12 text-center shadow-xs">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 mb-3 animate-spin">
                        <i class="bi bi-arrow-repeat text-2xl"></i>
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Querying Database</h3>
                    <p class="text-[11px] font-mono text-slate-500 mt-1">Retrieving matching flat units...</p>
                </div>

                <!-- State 3: Live Retrieved Records (Flats Matrix Directly Below Filters) -->
                <div id="results-content-state" class="hidden space-y-3.5">

                    <!-- Telemetry KPI Dashboard Strip -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="telemetry-kpi-grid">
                        <!-- Card 1: Total Flats -->
                        <div onclick="setApprovalCardFilter('all')" id="card-filter-all"
                            class="group relative bg-white border-2 border-sky-500 rounded-xl p-3.5 shadow-xs hover:shadow-md cursor-pointer transition-all duration-200 ring-2 ring-sky-500/20">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase text-slate-500 tracking-wider">Total Retrieved</span>
                                <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-xs">
                                    <i class="bi bi-houses-fill"></i>
                                </div>
                            </div>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-xl font-black font-mono text-slate-800" id="card-val-total">0</span>
                                <span class="text-[9px] font-bold text-sky-700 font-mono">Flats</span>
                            </div>
                        </div>

                        <!-- Card 2: Approved by STP -->
                        <div onclick="setApprovalCardFilter('approved')" id="card-filter-approved"
                            class="group relative bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs hover:shadow-md hover:border-emerald-300 cursor-pointer transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase text-emerald-800 tracking-wider flex items-center gap-1">
                                    <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                    <span>Approved by STP</span>
                                </span>
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                            </div>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-xl font-black font-mono text-emerald-700" id="card-val-approved">0</span>
                                <span class="text-[9px] font-black text-emerald-700 font-mono px-1.5 py-0.2 rounded bg-emerald-50 border border-emerald-200" id="card-badge-approved-pct">0%</span>
                            </div>
                        </div>

                        <!-- Card 3: Pending STP Approval -->
                        <div onclick="setApprovalCardFilter('pending')" id="card-filter-pending"
                            class="group relative bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs hover:shadow-md hover:border-amber-300 cursor-pointer transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase text-amber-800 tracking-wider flex items-center gap-1">
                                    <i class="bi bi-hourglass-split text-amber-600"></i>
                                    <span>Pending Approval</span>
                                </span>
                                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                            </div>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-xl font-black font-mono text-amber-700" id="card-val-pending">0</span>
                                <span class="text-[9px] font-black text-amber-700 font-mono px-1.5 py-0.2 rounded bg-amber-50 border border-amber-200" id="card-badge-pending-pct">0%</span>
                            </div>
                        </div>

                        <!-- Card 4: Clearance Ratio Gauge -->
                        <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-xl p-3.5 shadow-xs flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase text-slate-300 tracking-wider">Clearance Ratio</span>
                                <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-white/10 text-emerald-300 border border-white/15" id="card-clearance-status">STP Quota</span>
                            </div>
                            <div class="mt-2">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xl font-black font-mono text-emerald-400" id="card-val-rate">0%</span>
                                    <span class="text-[8px] font-mono text-slate-400">Certified for Draw</span>
                                </div>
                                <div class="w-full bg-white/10 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                    <div id="card-progress-bar" class="bg-gradient-to-r from-emerald-400 to-teal-400 h-1.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="mt-1 text-[8px] font-mono text-slate-400 flex items-center justify-between">
                                <span>Authority: STP</span>
                                <span class="text-emerald-300 font-bold">100% Certified</span>
                            </div>
                        </div>
                    </div>

                    <!-- Flats Records Database Matrix (Clean, High-Density Table) -->
                    <div class="bg-white border border-slate-200/90 rounded-xl shadow-xs overflow-hidden" id="flats-table-card">
                        
                        <!-- Table Top Bar -->
                        <div class="px-5 py-3 border-b border-slate-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/70">
                            
                            <!-- Left: Project Switcher (Visible if multiple records exist) -->
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <!-- Multi-Project Dropdown Switcher (Visible if multiple records exist) -->
                                <div id="table-project-filter-container" class="hidden items-center gap-1.5">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase font-mono">Project:</span>
                                    <select id="table-project-filter" onchange="onTableProjectFilterChange()"
                                        class="text-xs py-1 px-2.5 bg-white border border-slate-250 rounded-lg font-bold text-slate-800 focus:outline-none focus:border-sky-500 shadow-2xs">
                                    </select>
                                </div>
                            </div>

                            <!-- Right: Quick Search Input & Export Actions -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="relative">
                                    <i class="bi bi-search absolute left-2.5 top-2 text-slate-400 text-xs"></i>
                                    <input type="text" id="flats-search-input" onkeyup="filterFlatsTable()" placeholder="Quick filter flats..."
                                        class="text-xs pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-sky-500 font-medium text-slate-800 shadow-2xs w-48 sm:w-56">
                                </div>
                                <a id="btn-full-matrix-view" href="#" target="_blank" title="Open Entire Verified Table in Dedicated Full Page View"
                                    class="px-2.5 py-1.5 bg-gradient-to-r from-sky-600 via-indigo-600 to-indigo-700 hover:from-sky-700 hover:to-indigo-800 text-white rounded-lg text-xs font-black uppercase shadow-xs flex items-center gap-1.5 transition">
                                    <i class="bi bi-box-arrow-up-right text-[11px]"></i> <span class="hidden md:inline">Full Page View</span>
                                </a>
                                <button type="button" onclick="exportFlatsToCSV()" title="Export to CSV"
                                    class="px-2.5 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 shadow-2xs flex items-center gap-1.5 transition">
                                    <i class="bi bi-file-earmark-excel text-emerald-600"></i> <span class="hidden md:inline">CSV</span>
                                </button>
                                <button type="button" onclick="window.print()" title="Print"
                                    class="px-2.5 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 shadow-2xs flex items-center gap-1.5 transition">
                                    <i class="bi bi-printer text-slate-600"></i> <span class="hidden md:inline">Print</span>
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto custom-scroll">
                            <table class="w-full text-left border-collapse" id="flats-matrix-table">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[9px] text-slate-500 uppercase font-black tracking-wider">
                                        <th class="px-4 py-3" style="width: 4%;">#</th>
                                        <th class="px-4 py-3">Project & Sector</th>
                                        <th class="px-4 py-3">Flat No.</th>
                                        <th class="px-4 py-3">Tower & Floor</th>
                                        <th class="px-4 py-3">OC Status</th>
                                        <th class="px-4 py-3 text-center">STP Status</th>
                                        <th class="px-4 py-3 text-right" style="width: 8%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-150 text-xs text-slate-700" id="flats-table-body">
                                    <!-- Rows populated dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Pagination & Page Size Controls -->
                        <div id="flats-pagination-bar" class="px-5 py-3 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                            <!-- Left: Page Size Selector & Entries Info -->
                            <div class="flex items-center gap-3 text-slate-600 font-medium flex-wrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-500">Show</span>
                                    <select id="flats-page-size" onchange="changeFlatsPageSize(this.value)"
                                        class="bg-white border border-slate-300 text-slate-800 text-xs font-bold rounded-lg px-2.5 py-1 focus:ring-1 focus:ring-sky-500 focus:outline-none shadow-2xs cursor-pointer">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="text-slate-500">entries</span>
                                </div>
                                <span class="text-slate-300 hidden sm:inline">|</span>
                                <div id="flats-pagination-info" class="text-slate-600 font-mono text-[11px] font-semibold">
                                    Showing 0 to 0 of 0 entries
                                </div>
                            </div>

                            <!-- Right: Pagination Buttons -->
                            <div id="flats-pagination-buttons" class="flex items-center gap-1 flex-wrap justify-end">
                                <!-- Populated dynamically -->
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Footer -->
            <footer class="bg-white border border-slate-200/90 py-2 shrink-0 rounded-xl mt-4">
                <div class="w-full px-3 text-center">
                    <p class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider">
                        Department of Housing for All • Developed by DITECH (CRID)
                    </p>
                </div>
            </footer>

        </div>
    </div>

    <!-- Quick Flat Inspect Modal -->
    <div id="inspect-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden" id="inspect-modal-content">
            <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2 text-slate-800">
                    <i class="bi bi-building-check text-sky-600 text-lg"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider" id="modal-flat-title">Flat Specification</h3>
                </div>
                <button onclick="closeInspectModal()" class="w-7 h-7 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scroll text-xs">
                <div class="grid grid-cols-2 gap-3" id="modal-flat-grid">
                    <!-- Populated dynamically -->
                </div>
            </div>
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-150 flex items-center justify-between">
                <div class="flex items-center gap-2" id="modal-flat-actions"></div>
                <button onclick="closeInspectModal()" class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold uppercase rounded-lg text-xs transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Global State
        const flatShowBaseUrl = "{{ url('ews/stp/flats') }}";
        let currentVerifyData = null;
        let activeRecordId = 'all';
        let currentApprovalFilter = 'all';
        let flatsCurrentPage = 1;
        let flatsPageSize = 10;

        $(document).ready(function() {
            // Initialize Select2 on License field
            $('#filter-license').select2({
                placeholder: "-- Search / Select License --",
                allowClear: true,
                width: '100%'
            });

            // Auto-trigger fetch when license changes
            $('#filter-license').on('select2:select', function(e) {
                const licenseNo = e.params.data.id;
                if (licenseNo) {
                    fetchRecordsByLicense(licenseNo);
                }
            });

            $('#filter-license').on('select2:clear', function() {
                showPromptState();
            });
        });

        // Quick click on suggested license chip
        function triggerQuickLicense(licenseNo) {
            $('#filter-license').val(licenseNo).trigger('change.select2');
            fetchRecordsByLicense(licenseNo);
        }

        // District change -> Fetch Towns & Filtered Licenses
        function onDistrictChange() {
            const districtId = $('#filter-district').val();

            // 1. Fetch Towns for District
            let townUrl = "{{ route('ews.stp.towns') }}";
            if (districtId) townUrl += "?district_id=" + districtId;

            $.getJSON(townUrl, function(data) {
                let options = '<option value="">All Towns</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-town').html(options);
            });

            // 2. Fetch Licenses for District
            refreshLicensesDropdown(districtId, '');
        }

        // Town change -> Fetch Filtered Licenses
        function onTownChange() {
            const districtId = $('#filter-district').val();
            const townId = $('#filter-town').val();
            refreshLicensesDropdown(districtId, townId);
        }

        // AJAX update of Select2 Licenses list
        function refreshLicensesDropdown(districtId, townId) {
            let licUrl = "{{ route('ews.stp.verify.licenses') }}";
            let params = [];
            if (districtId) params.push("district_id=" + districtId);
            if (townId) params.push("town_id=" + townId);
            if (params.length) licUrl += "?" + params.join("&");

            $.getJSON(licUrl, function(licenses) {
                let currentVal = $('#filter-license').val();
                let options = '<option value="">-- Select License No --</option>';
                let found = false;

                $.each(licenses, function(i, lic) {
                    let selected = (lic === currentVal) ? 'selected' : '';
                    if (selected) found = true;
                    options += `<option value="${lic}" ${selected}>${lic}</option>`;
                });

                $('#filter-license').html(options).trigger('change.select2');
                $('#badge-total-licenses').text(licenses.length + ' Available');

                if (currentVal && found) {
                    fetchRecordsByLicense(currentVal);
                } else if (!currentVal) {
                    showPromptState();
                }
            });
        }

        // Reset Filters
        function resetVerificationFilters() {
            $('#filter-district').val('');
            $('#filter-town').html('<option value="">All Towns</option>');
            $('#filter-license').val('').trigger('change.select2');
            refreshLicensesDropdown('', '');
            showPromptState();
        }

        function showPromptState() {
            $('#prompt-state').removeClass('hidden');
            $('#loading-state').addClass('hidden');
            $('#results-content-state').addClass('hidden');
            currentVerifyData = null;
            flatsCurrentPage = 1;
        }

        // Main AJAX Query to Local Database
        function fetchRecordsByLicense(licenseNo) {
            if (!licenseNo) return;

            const districtId = $('#filter-district').val();
            const townId = $('#filter-town').val();

            $('#prompt-state').addClass('hidden');
            $('#loading-state').removeClass('hidden');
            $('#results-content-state').addClass('hidden');

            let url = "{{ route('ews.stp.verify.records') }}?license_no=" + encodeURIComponent(licenseNo);
            if (districtId) url += "&district_id=" + districtId;
            if (townId) url += "&town_id=" + townId;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    $('#loading-state').addClass('hidden');

                    if (!resp.success || !resp.records || resp.records.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'NO RECORDS FOUND',
                            text: resp.message || 'No local records found for this license number.',
                            confirmButtonColor: '#0284c7'
                        });
                        showPromptState();
                        return;
                    }

                    currentVerifyData = resp;
                    activeRecordId = 'all';
                    currentApprovalFilter = 'all';
                    flatsCurrentPage = 1;
                    $('#flats-search-input').val('');
                    renderCompleteView();
                },
                error: function(err) {
                    $('#loading-state').addClass('hidden');
                    Swal.fire({
                        icon: 'error',
                        title: 'QUERY ERROR',
                        text: 'Failed to retrieve records from local database.',
                        confirmButtonColor: '#ef4444'
                    });
                    showPromptState();
                }
            });
        }

        // Distinct Badge Colors for Multiple Records
        const recordColorThemes = [
            { bg: 'bg-sky-50', text: 'text-sky-700', border: 'border-sky-200' },
            { bg: 'bg-violet-50', text: 'text-violet-700', border: 'border-violet-200' },
            { bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200' },
            { bg: 'bg-amber-50', text: 'text-amber-700', border: 'border-amber-200' },
            { bg: 'bg-indigo-50', text: 'text-indigo-700', border: 'border-indigo-200' },
            { bg: 'bg-rose-50', text: 'text-rose-700', border: 'border-rose-200' }
        ];

        function getRecordTheme(index) {
            return recordColorThemes[(index - 1) % recordColorThemes.length];
        }

        // Card Click Quick Filter Handler
        function setApprovalCardFilter(filter) {
            currentApprovalFilter = filter;
            flatsCurrentPage = 1;
            $('#card-filter-all, #card-filter-approved, #card-filter-pending')
                .removeClass('border-sky-500 border-emerald-500 border-amber-500 ring-2 ring-sky-500/20 ring-emerald-500/20 ring-amber-500/20 border-2')
                .addClass('border-slate-200');

            if (filter === 'all') {
                $('#card-filter-all').removeClass('border-slate-200').addClass('border-2 border-sky-500 ring-2 ring-sky-500/20');
            } else if (filter === 'approved') {
                $('#card-filter-approved').removeClass('border-slate-200').addClass('border-2 border-emerald-500 ring-2 ring-emerald-500/20');
            } else if (filter === 'pending') {
                $('#card-filter-pending').removeClass('border-slate-200').addClass('border-2 border-amber-500 ring-2 ring-amber-500/20');
            }
            renderFlatsTable();
        }

        // Render Master View Directly to Table
        function renderCompleteView() {
            if (!currentVerifyData) return;

            const records = currentVerifyData.records || [];
            const allFlats = currentVerifyData.all_flats || [];
            const totalFlats = allFlats.length;

            // Update Telemetry KPI Cards
            const approvedFlats = allFlats.filter(f => (f.is_aproved_by_dtp == 1)).length;
            const pendingFlats = totalFlats - approvedFlats;
            const approvalRate = totalFlats > 0 ? Math.round((approvedFlats / totalFlats) * 100) : 0;

            $('#card-val-total').text(totalFlats);
            $('#card-val-approved').text(approvedFlats);
            $('#card-badge-approved-pct').text(approvalRate + '%');
            $('#card-val-pending').text(pendingFlats);
            $('#card-badge-pending-pct').text((100 - approvalRate) + '%');
            $('#card-val-rate').text(approvalRate + '%');
            $('#card-progress-bar').css('width', approvalRate + '%');
            $('#card-clearance-status').text(approvalRate === 100 ? '100% Cleared' : (approvedFlats > 0 ? `${approvedFlats}/${totalFlats} Cleared` : 'Pending STP'));

            // 1. Table Header License & Count
            $('#res-table-license-badge').text(currentVerifyData.license_no);
            $('#res-flats-count-badge').text(`${totalFlats} Flats Retrieved`);

            // Update Full Page View Link
            const distId = $('#filter-district').val() || '';
            const townId = $('#filter-town').val() || '';
            const matrixUrl = `{{ route('ews.stp.verify.matrix') }}?license_no=${encodeURIComponent(currentVerifyData.license_no || '')}&district_id=${distId}&town_id=${townId}`;
            $('#btn-full-matrix-view').attr('href', matrixUrl);

            // 2. Multi-Project Switcher Dropdown (Shown if > 1 project record)
            if (records.length > 1) {
                $('#table-project-filter-container').removeClass('hidden').addClass('flex');
                let optHtml = `<option value="all">All Projects (${totalFlats} Flats)</option>`;
                $.each(records, function(i, r) {
                    optHtml += `<option value="${r.record_id}">[#${r.record_index}] ${r.project_name || 'Scheme'} (${r.flats_count} Flats)</option>`;
                });
                $('#table-project-filter').html(optHtml).val(activeRecordId);
            } else {
                $('#table-project-filter-container').addClass('hidden').removeClass('flex');
            }

            // 3. Render Flats Table
            renderFlatsTable();

            $('#results-content-state').removeClass('hidden');
        }

        // Project filter change in table header
        function onTableProjectFilterChange() {
            activeRecordId = $('#table-project-filter').val();
            flatsCurrentPage = 1;
            renderFlatsTable();
        }

        // Render Flats Table according to Active Record, Approval Filter, Search & 10-Item Pagination
        function renderFlatsTable() {
            if (!currentVerifyData) return;

            let flats = [];
            if (activeRecordId === 'all') {
                flats = currentVerifyData.all_flats || [];
            } else {
                const rec = (currentVerifyData.records || []).find(r => r.record_id === activeRecordId);
                flats = rec ? (rec.flats || []) : [];
            }

            // Apply Approval Filter
            if (currentApprovalFilter === 'approved') {
                flats = flats.filter(f => (f.is_aproved_by_dtp == 1));
            } else if (currentApprovalFilter === 'pending') {
                flats = flats.filter(f => (f.is_aproved_by_dtp != 1));
            }

            const totalRetrieved = flats.length;
            const filterLabel = currentApprovalFilter === 'approved' ? ' (Approved Only)' : (currentApprovalFilter === 'pending' ? ' (Pending Only)' : '');
            $('#res-flats-count-badge').text(`${totalRetrieved} Flats Retrieved${filterLabel}`);

            // Apply Text Search Filter (Quick filter across all flats)
            const searchVal = ($('#flats-search-input').val() || '').toLowerCase().trim();
            if (searchVal) {
                flats = flats.filter(f => {
                    const projectName = (f.record_project_name || f.project_name || '').toLowerCase();
                    const developerName = (f.developer_name || '').toLowerCase();
                    const flatNo = String(f.flat_number || '').toLowerCase();
                    const tower = String(f.block_tower_number || '').toLowerCase();
                    const floor = String(f.floor || '').toLowerCase();
                    const loc = [f.town_name, f.district_name, f.sector_no].filter(Boolean).join(' ').toLowerCase();
                    const ocStatus = String(f.oc_status || '').toLowerCase();
                    const combined = `${flatNo} ${tower} ${floor} ${projectName} ${developerName} ${loc} ${ocStatus}`;
                    return combined.indexOf(searchVal) > -1;
                });
            }

            const totalFiltered = flats.length;
            const totalPages = Math.ceil(totalFiltered / flatsPageSize) || 1;

            if (flatsCurrentPage > totalPages) {
                flatsCurrentPage = totalPages;
            }
            if (flatsCurrentPage < 1) {
                flatsCurrentPage = 1;
            }

            const startIndex = (flatsCurrentPage - 1) * flatsPageSize;
            const endIndex = Math.min(startIndex + flatsPageSize, totalFiltered);
            const pageFlats = flats.slice(startIndex, endIndex);

            let rows = '';
            if (totalFiltered === 0) {
                rows = `
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400 font-mono text-xs">
                            <i class="bi bi-inbox text-2xl block text-slate-300 mb-1"></i>
                            ${searchVal ? `No flats found matching "${searchVal}".` : `No flats matching filter "${currentApprovalFilter.toUpperCase()}" under this record.`}
                        </td>
                    </tr>
                `;
            } else {
                $.each(pageFlats, function(idx, f) {
                    const globalSerial = startIndex + idx + 1;
                    const theme = getRecordTheme(f.record_index || 1);
                    const recordLabel = f.record_index ? `#${f.record_index}` : '#1';
                    const projectName = f.record_project_name || f.project_name || 'Project';
                    const secureIdentifier = f.secure_id || f.id;

                    const isDtpApproved = (f.is_aproved_by_dtp == 1);
                    const dtpBadge = isDtpApproved
                        ? `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 font-mono shadow-2xs" title="${f.dtp_approved_at ? 'Approved: ' + f.dtp_approved_at : 'Approved by STP'}"><i class="bi bi-shield-check text-emerald-600"></i> Approved</span>`
                        : `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-300 font-mono shadow-2xs"><i class="bi bi-hourglass-split text-amber-600"></i> Pending</span>`;

                    rows += `
                        <tr class="hover:bg-slate-50 transition-colors flat-row" data-record-id="${f.record_id || ''}">
                            <td class="px-4 py-2.5 font-mono text-slate-400 text-[11px]">${globalSerial}</td>
                            <td class="px-4 py-2.5">
                                <div class="font-extrabold text-slate-900 text-xs">${projectName}</div>
                                ${f.sector_no ? `<span class="block text-[10px] text-slate-400 font-semibold mt-0.5">Sector ${f.sector_no}</span>` : ''}
                            </td>
                            <td class="px-4 py-2.5 font-black text-violet-700 font-mono text-sm">${f.flat_number || '—'}</td>
                            <td class="px-4 py-2.5">
                                <span class="font-bold text-slate-800 text-xs">${f.block_tower_number ? 'Tower ' + f.block_tower_number : '—'}</span>
                                <span class="block text-[10px] text-slate-500 font-medium">${f.floor || '—'}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-black uppercase font-mono ${f.oc_status && f.oc_status.toLowerCase() !== 'pending' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600'}">
                                    ${f.oc_status || 'Pending'}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                ${dtpBadge}
                            </td>
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end">
                                    <a href="${flatShowBaseUrl}/${secureIdentifier}" target="_blank"
                                        class="px-2.5 py-1 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-200 hover:border-sky-600 rounded-lg text-[10px] font-black uppercase transition flex items-center gap-1 shadow-2xs" title="Official TCP Verification & View">
                                        <i class="bi bi-eye text-xs"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#flats-table-body').html(rows);

            // Update Pagination UI Bar
            renderFlatsPagination(totalFiltered, totalPages, startIndex, endIndex);
        }

        // Render Pagination Controls & Entry Range
        function renderFlatsPagination(totalFiltered, totalPages, startIndex, endIndex) {
            // Keep Page size dropdown in sync
            $('#flats-page-size').val(flatsPageSize);

            // 1. Info Label
            if (totalFiltered === 0) {
                $('#flats-pagination-info').text('Showing 0 to 0 of 0 entries');
                $('#flats-pagination-buttons').html('');
                return;
            }

            const infoStr = `Showing ${startIndex + 1} to ${endIndex} of ${totalFiltered} entries`;
            $('#flats-pagination-info').text(infoStr);

            // 2. Pagination Navigation Buttons
            let btnsHtml = '';

            // Previous Button
            const prevDisabled = (flatsCurrentPage <= 1);
            btnsHtml += `
                <button type="button" ${prevDisabled ? 'disabled' : `onclick="goToFlatsPage(${flatsCurrentPage - 1})"`}
                    class="px-2.5 py-1 text-xs font-bold rounded-lg border transition-all flex items-center gap-1 ${prevDisabled ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-50' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-sky-700 border-slate-300 shadow-2xs cursor-pointer'}">
                    <i class="bi bi-chevron-left text-[10px]"></i> Previous
                </button>
            `;

            // Page numbers
            let pageNumbers = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pageNumbers.push(i);
            } else {
                pageNumbers.push(1);
                if (flatsCurrentPage > 3) {
                    pageNumbers.push('...');
                }
                const start = Math.max(2, flatsCurrentPage - 1);
                const end = Math.min(totalPages - 1, flatsCurrentPage + 1);
                for (let i = start; i <= end; i++) {
                    pageNumbers.push(i);
                }
                if (flatsCurrentPage < totalPages - 2) {
                    pageNumbers.push('...');
                }
                pageNumbers.push(totalPages);
            }

            $.each(pageNumbers, function(idx, p) {
                if (p === '...') {
                    btnsHtml += `<span class="px-2 py-1 text-xs font-bold text-slate-400 font-mono">...</span>`;
                } else if (p === flatsCurrentPage) {
                    btnsHtml += `
                        <button type="button" class="min-w-[30px] h-[28px] px-2 text-xs font-black rounded-lg bg-sky-600 text-white border border-sky-600 shadow-xs cursor-default">
                            ${p}
                        </button>
                    `;
                } else {
                    btnsHtml += `
                        <button type="button" onclick="goToFlatsPage(${p})"
                            class="min-w-[30px] h-[28px] px-2 text-xs font-bold rounded-lg bg-white text-slate-700 hover:bg-slate-100 hover:text-sky-700 border border-slate-300 transition-all shadow-2xs cursor-pointer">
                            ${p}
                        </button>
                    `;
                }
            });

            // Next Button
            const nextDisabled = (flatsCurrentPage >= totalPages);
            btnsHtml += `
                <button type="button" ${nextDisabled ? 'disabled' : `onclick="goToFlatsPage(${flatsCurrentPage + 1})"`}
                    class="px-2.5 py-1 text-xs font-bold rounded-lg border transition-all flex items-center gap-1 ${nextDisabled ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-50' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-sky-700 border-slate-300 shadow-2xs cursor-pointer'}">
                    Next <i class="bi bi-chevron-right text-[10px]"></i>
                </button>
            `;

            $('#flats-pagination-buttons').html(btnsHtml);
        }

        // Go to specific page
        function goToFlatsPage(page) {
            if (page < 1) return;
            flatsCurrentPage = page;
            renderFlatsTable();
            const tableEl = document.getElementById('flats-table-card');
            if (tableEl) {
                tableEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        // Change page size (10, 25, 50, 100)
        function changeFlatsPageSize(size) {
            flatsPageSize = parseInt(size, 10) || 10;
            flatsCurrentPage = 1;
            renderFlatsTable();
        }

        // Quick filter inside table: resets to page 1 and re-renders
        function filterFlatsTable() {
            flatsCurrentPage = 1;
            renderFlatsTable();
        }

        // Inspect Single Flat Modal
        function inspectFlatById(flatId) {
            if (!currentVerifyData) return;
            const flats = currentVerifyData.all_flats || [];
            const f = flats.find(item => item.id == flatId);
            if (!f) return;

            $('#modal-flat-title').text(`Flat No. ${f.flat_number} (${f.flat_code || 'Details'})`);

            let docButtonsHtml = '';
            if (f.developer_plan || f.developer_plan_url) {
                const url = f.developer_plan_url || (f.developer_plan ? `{{ asset('storage') }}/${f.developer_plan}` : '#');
                docButtonsHtml += `<a href="${url}" target="_blank" class="px-2.5 py-1 bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white border border-sky-200 rounded text-[10px] font-bold uppercase transition flex items-center gap-1"><i class="bi bi-file-earmark-pdf"></i> Dev Plan</a>`;
            }
            if (f.building_plan_copy || f.building_plan_url) {
                const url = f.building_plan_url || (f.building_plan_copy ? `{{ asset('storage') }}/${f.building_plan_copy}` : '#');
                docButtonsHtml += `<a href="${url}" target="_blank" class="px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 rounded text-[10px] font-bold uppercase transition flex items-center gap-1"><i class="bi bi-file-earmark-richtext"></i> Bldg Plan</a>`;
            }
            if (f.oc_copy_file || f.oc_copy_url) {
                const url = f.oc_copy_url || (f.oc_copy_file ? `{{ asset('storage') }}/${f.oc_copy_file}` : '#');
                docButtonsHtml += `<a href="${url}" target="_blank" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 rounded text-[10px] font-bold uppercase transition flex items-center gap-1"><i class="bi bi-file-earmark-check"></i> OC Copy</a>`;
            }

            let gridHtml = `
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">Linked Project</span>
                    <span class="font-black text-slate-800 uppercase font-mono">Record #${f.record_index || '1'}: ${f.record_project_name || f.project_name || 'N/A'}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">Colonizer / Developer</span>
                    <span class="font-bold text-slate-800">${f.developer_name || 'N/A'}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">Flat Number</span>
                    <span class="font-black text-violet-700 text-sm font-mono">${f.flat_number}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">Tower / Block</span>
                    <span class="font-bold text-slate-800 font-mono">${f.block_tower_number || 'N/A'}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">Floor Details</span>
                    <span class="font-bold text-slate-800">${f.floor || 'N/A'}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">Unique Flat Code</span>
                    <span class="font-bold text-sky-700 font-mono">${f.flat_code || 'N/A'}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">District & Town</span>
                    <span class="font-bold text-slate-800">${f.town_name || ''}, ${f.district_name || ''}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">Sector / Address</span>
                    <span class="font-bold text-slate-800">Sector ${f.sector_no || 'N/A'}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">OC Status</span>
                    <span class="font-bold text-amber-700">${f.oc_status || 'Pending'}</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase font-mono">Draw Quota</span>
                    <span class="font-bold text-emerald-700">${f.flats_offered_for_draw || 'Eligible'}</span>
                </div>
            `;

            if (docButtonsHtml) {
                gridHtml += `
                    <div class="col-span-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between gap-2 flex-wrap">
                        <span class="text-[9px] font-bold text-slate-500 uppercase font-mono">Project Documents:</span>
                        <div class="flex items-center gap-1.5">${docButtonsHtml}</div>
                    </div>
                `;
            }

            const isDtpApproved = (f.is_aproved_by_dtp == 1);
            $('#modal-flat-grid').html(gridHtml);
            $('#modal-flat-actions').html(`
                <a href="${flatShowBaseUrl}/${f.secure_id || f.id}" target="_blank" class="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold uppercase transition flex items-center gap-1 shadow-2xs">
                    <i class="bi bi-box-arrow-up-right"></i> Open Verification Dossier
                </a>
            `);
            $('#inspect-modal').removeClass('hidden').addClass('flex');
        }

        function closeInspectModal() {
            $('#inspect-modal').addClass('hidden').removeClass('flex');
        }

        // Export Flats to CSV
        function exportFlatsToCSV() {
            if (!currentVerifyData) return;
            const flats = currentVerifyData.all_flats || [];
            if (flats.length === 0) {
                Swal.fire('No Data', 'No flats to export for this license.', 'info');
                return;
            }

            let csv = "SNo,LicenseNo,RecordNo,ProjectName,Colonizer,FlatNo,BlockTower,Floor,FlatCode,District,Town,Sector\n";
            $.each(flats, function(i, f) {
                csv += `"${i+1}","${currentVerifyData.license_no || ''}","${f.record_index || '1'}","${f.record_project_name || f.project_name || ''}","${f.developer_name || ''}","${f.flat_number || ''}","${f.block_tower_number || ''}","${f.floor || ''}","${f.flat_code || ''}","${f.district_name || ''}","${f.town_name || ''}","${f.sector_no || ''}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `EWS_Flats_${currentVerifyData.license_no}_${new Date().toISOString().slice(0,10)}.csv`;
            a.click();
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
