<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer - Registry Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery & DataTables CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        .code-font {
            font-family: 'Fira Code', monospace;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .dev-shadow {
            box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08);
        }

        /* Custom DataTables Styling to align with clean design */
        table.dataTable {
            border-bottom: 1px solid #e2e8f0 !important;
            margin-top: 15px !important;
            margin-bottom: 15px !important;
        }
        table.dataTable thead th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            font-size: 9px !important;
            letter-spacing: 0.05em !important;
            border-bottom: 1px solid #cbd5e1 !important;
            padding: 12px 10px !important;
        }
        table.dataTable tbody td {
            font-size: 11px !important;
            padding: 12px 10px !important;
            color: #334155 !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        table.dataTable tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .dataTables_wrapper .dataTables_length select {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px 20px 4px 8px;
            font-size: 10px;
            font-weight: bold;
            outline: none;
            color: #334155;
        }
        .dataTables_wrapper .dataTables_filter input {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 500;
            outline: none;
            margin-left: 8px;
            color: #334155;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 10px !important;
            margin-left: 3px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            font-size: 10px !important;
            font-weight: bold !important;
            color: #334155 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #3b82f6 !important;
            color: #ffffff !important;
            border-color: #3b82f6 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important;
            color: #1e3a8a !important;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 10px;
            color: #64748b;
            margin-top: 12px;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 12px;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    @include('ews.developer.partials.sidebar')

    <!-- RIGHT CONTAINER WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase">
                        @if($currentView === 'district')
                            {{ !empty($user->district_name) ? (str_contains(strtoupper($user->district_name), 'ZONE') ? strtoupper($user->district_name) : strtoupper($user->district_name) . ' Zone') : 'Zone' }} Master Registry
                        @elseif($currentView === 'my_flats')
                            My Registered Flats Inventory
                        @else
                            Developer Command Dashboard
                        @endif
                    </h2>
                    <p class="text-[8px] text-slate-455 font-mono uppercase">EWS Builder Housing Administration</p>
                </div>
            </div>

            <!-- Profile context details -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-[10px] text-slate-700 font-bold flex items-center gap-1 justify-end">
                        <span>{{ $user->name }}</span>
                        <i class="bi bi-person-circle text-sky-600"></i>
                    </div>
                    <div class="text-[8.5px] text-slate-500 font-mono">
                        Developer Portal | Mobile: {{ $user->mobile }}
                    </div>
                </div>
                <a href="{{ route('ews.developer.logout') }}" class="md:hidden px-3 py-1.5 bg-red-50 text-red-650 rounded-lg text-[9px] font-black uppercase border border-red-100">
                    Logout
                </a>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3.5 custom-scroll">
            
            @if($currentView === 'dashboard')
                <!-- DASHBOARD VIEW: OVERVIEW TELEMETRY & PROJECT BREAKDOWN -->
                
                <!-- Sleek Compact Welcome Header -->
                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 rounded-xl px-4 py-2.5 text-white shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 relative overflow-hidden">
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-8 h-8 rounded-lg bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-400 shrink-0">
                            <i class="bi bi-speedometer2 text-sm"></i>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-2.5 gap-y-0.5">
                            <h2 class="text-xs font-black tracking-tight text-white uppercase">Welcome, {{ $user->name }}</h2>
                            <span class="text-[9px] text-slate-400 font-mono hidden md:inline">| EWS Developer Portal</span>
                        </div>
                    </div>
                </div>

            @elseif($currentView === 'district')
                <!-- Compact Zone Master Header -->
                <div class="bg-gradient-to-r from-sky-600 to-indigo-700 rounded-xl px-4 py-2 text-white shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white shrink-0">
                            <i class="bi bi-building text-xs"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xs font-black uppercase tracking-wider">{{ $displayZoneName ?? 'Zone' }} Master EWS Flats</h2>
                                <span class="px-1.5 py-0.2 bg-white/20 text-white rounded text-[8px] font-black uppercase">Master</span>
                            </div>
                            <p class="text-[8.5px] text-sky-100 font-mono">Viewing all allotment proforma flats registered under {{ $displayZoneName ?? 'Zone' }} Authority</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-base font-black font-mono leading-none">{{ $stats['total_flats'] }}</span>
                        <span class="block text-[7px] uppercase tracking-widest text-sky-200">Zone Total</span>
                    </div>
                </div>
            @elseif($currentView === 'my_flats')
                <!-- Compact Personal Inventory Header -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-xl px-4 py-2 text-white shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white shrink-0">
                            <i class="bi bi-person-check-fill text-xs"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xs font-black uppercase tracking-wider">Flats Registered By My Account</h2>
                                <span class="px-1.5 py-0.2 bg-white/20 text-white rounded text-[8px] font-black uppercase">My Entries</span>
                            </div>
                            <p class="text-[8.5px] text-emerald-100 font-mono">Viewing flats created directly by your developer account (ID: #{{ $user->id }})</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-base font-black font-mono leading-none">{{ $stats['my_flats'] }}</span>
                        <span class="block text-[7px] uppercase tracking-widest text-emerald-200">My Total</span>
                    </div>
                </div>
            @endif

            <!-- REGISTRY DATABASE TABLE SECTION -->
            <section id="table-section" class="bg-white border border-slate-200/80 rounded-xl shadow-sm dev-shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
                    <!-- Flat Register By Me Card in Table Header -->
                    <div class="inline-flex items-center gap-3 bg-white border border-emerald-200/90 rounded-xl px-3.5 py-2 shadow-2xs">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-150 shrink-0">
                            <i class="bi bi-person-check-fill text-base"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[9.5px] font-black uppercase tracking-wider text-emerald-700">Flats Registered By Me</span>
                                <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-[8px] font-black uppercase font-mono">My Flats</span>
                            </div>
                            <div class="flex items-baseline gap-1.5 mt-0.5 leading-none">
                                <h3 id="table-heading-title" class="text-xl font-black text-emerald-600 font-mono tracking-tight">{{ $stats['my_flats'] }}</h3>
                                <span class="text-[8.5px] text-emerald-600/70 font-mono uppercase font-semibold">Registered Flats</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 self-end sm:self-auto">
                        <input type="hidden" id="filter-ownership" value="my_flats">

                        <!-- Filter-wise Exports (CSV, EXCEL, PDF) -->
                        <div class="inline-flex gap-1 items-center bg-slate-100 p-1 rounded-lg">
                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider px-1">Export:</span>
                            <button type="button" onclick="triggerExport('csv')"
                                class="px-2 py-1 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 font-bold uppercase rounded text-[9px] shadow-sm flex items-center gap-1 transition-all">
                                <i class="bi bi-file-earmark-spreadsheet text-emerald-600"></i>
                                <span>CSV</span>
                            </button>
                            <button type="button" onclick="triggerExport('excel')"
                                class="px-2 py-1 bg-white hover:bg-green-50 text-slate-700 hover:text-green-700 border border-slate-200 hover:border-green-300 font-bold uppercase rounded text-[9px] shadow-sm flex items-center gap-1 transition-all">
                                <i class="bi bi-file-earmark-excel-fill text-green-600"></i>
                                <span>EXCEL</span>
                            </button>
                            <button type="button" onclick="triggerExport('pdf')"
                                class="px-2 py-1 bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-200 hover:border-rose-300 font-bold uppercase rounded text-[9px] shadow-sm flex items-center gap-1 transition-all">
                                <i class="bi bi-file-pdf-fill text-rose-600"></i>
                                <span>PDF</span>
                            </button>
                        </div>

                        <div class="w-px h-5 bg-slate-200 mx-1"></div>

                        <!-- Dedicated Page Add Link -->
                        <a href="{{ route('ews.developer.flats.create') }}"
                            class="px-3.5 py-1.5 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-750 text-white font-black uppercase tracking-wider rounded-lg text-[9px] shadow-md flex items-center gap-1 transition-all">
                            <i class="bi bi-plus-lg"></i>
                            <span>Register EWS Flat</span>
                        </a>
                    </div>
                </div>

                <!-- Cascading Filter Console (Zone -> District -> Town -> Project) -->
                <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                        <!-- Step 1: District Filter (Developer's Added Districts) -->
                        <div class="lg:col-span-3 space-y-1">
                            <label for="filter-district" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                <span class="flex items-center gap-1">
                                    <span class="px-1 py-0.2 rounded text-[8px] bg-sky-100 text-sky-800 font-black">1</span>
                                    <span>Select District</span>
                                </span>
                                <span class="text-[8px] text-slate-400 font-mono font-bold">{{ count($districts ?? []) }} Added</span>
                            </label>
                            <div class="relative">
                                <select id="filter-district" onchange="onDistrictFilterChange()"
                                    class="w-full bg-white border border-slate-250 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none shadow-2xs">
                                    <option value="">{{ count($districts ?? []) > 0 ? 'All Districts (' . count($districts) . ')' : 'No Districts with Flats' }}</option>
                                    @foreach($districts ?? [] as $d)
                                        <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Town Filter (Developer's Added Towns) -->
                        <div id="filter-town-box" class="lg:col-span-3 space-y-1">
                            <label for="filter-town" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center gap-1">
                                <span class="px-1 py-0.2 rounded text-[8px] bg-violet-100 text-violet-800 font-black">2</span>
                                <span>Name of Town</span>
                            </label>
                            <select id="filter-town" onchange="onTownFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none shadow-2xs">
                                <option value="">{{ count($townsList ?? []) > 0 ? 'All Towns (' . count($townsList) . ')' : 'No Towns with Flats' }}</option>
                                @foreach($townsList ?? [] as $t)
                                    <option value="{{ $t->id }}">{{ strtoupper($t->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 3: Project Filter (Developer's Added Projects) -->
                        <div id="filter-project-box" class="lg:col-span-3 space-y-1">
                            <label for="filter-project" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center gap-1">
                                <span class="px-1 py-0.2 rounded text-[8px] bg-indigo-100 text-indigo-800 font-black">3</span>
                                <span>Name of Project</span>
                            </label>
                            <select id="filter-project" onchange="onProjectFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-2xs">
                                <option value="">{{ count($projectsList ?? []) > 0 ? 'All Projects (' . count($projectsList) . ')' : 'No Projects Registered' }}</option>
                                @foreach($projectsList ?? [] as $p)
                                    <option value="{{ $p->id }}">{{ strtoupper($p->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 4: Live Filtered Count Badge & Reset Filters -->
                        <div class="lg:col-span-3 space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center gap-1">
                                    <i class="bi bi-funnel-fill text-sky-600 text-[10px]"></i>
                                    <span>Filtered Records</span>
                                </label>
                                <span id="filter-status-pill" class="text-[8px] font-mono font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 uppercase">
                                    All Records
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <!-- Counter Box -->
                                <div class="flex-1 bg-white border border-sky-200/90 rounded-lg px-2.5 py-1.5 flex items-center justify-between shadow-2xs h-[34px]">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <div class="w-5 h-5 rounded bg-sky-50 text-sky-600 flex items-center justify-center text-xs shrink-0">
                                            <i class="bi bi-buildings"></i>
                                        </div>
                                        <span id="filter-count-label" class="text-[9px] font-bold uppercase tracking-wider text-slate-500 truncate">Matched:</span>
                                    </div>
                                    <div class="flex items-baseline gap-1 shrink-0">
                                        <span id="filtered-count-num" class="font-mono font-black text-sm text-sky-700">{{ number_format($stats['my_flats'] ?? 0) }}</span>
                                    </div>
                                </div>

                                <!-- Reset Filter Button -->
                                <button type="button" onclick="resetAllFilters()" title="Reset All Filters"
                                    class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 hover:text-slate-900 rounded-lg text-[9.5px] font-black uppercase tracking-wider transition-all shadow-2xs flex items-center justify-center gap-1.5 shrink-0 h-[34px]">
                                    <i class="bi bi-arrow-counterclockwise text-xs"></i>
                                    <span>Reset</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content (Yajra Server-side) -->
                <div class="p-5">
                    <table class="w-full text-left border-collapse" id="flats-table">
                        <thead>
                            <tr class="bg-slate-55/30 border-b border-slate-200 text-[9px] text-slate-500 uppercase font-black tracking-wider">
                                <th style="width: 5%;">S.No.</th>
                                <th id="th-col-1">District Name</th>
                                <th id="th-col-2">Town Name</th>
                                <th id="th-col-3">Project Name</th>
                                <th id="th-col-4">Block / Tower No.</th>
                                <th id="th-col-5">Floor Details</th>
                                <th id="th-col-6">Flat No.</th>
                                <th style="text-align: right; width: 12%;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-[11px] font-medium text-slate-655">
                            <!-- Populated dynamically via Ajax Datatables -->
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </div>

    <!-- Projects Modal -->
    <div id="projects-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 flex flex-col max-h-[80vh] overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="projects-modal-content">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2 text-indigo-700">
                    <i class="bi bi-diagram-3-fill text-lg"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Projects in {{ strtoupper($user->district_name ?? 'District') }}</h3>
                </div>
                <button onclick="closeProjectsModal()" class="w-6 h-6 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="p-6 overflow-y-auto space-y-2 flex-1 custom-scroll">
                @forelse($projectsList as $index => $proj)
                    <div class="px-4 py-3 bg-slate-50 border border-slate-150 rounded-xl flex items-center hover:bg-slate-100/70 transition-all">
                        <span class="text-xs font-bold text-slate-700">{{ $index + 1 }}. {{ strtoupper($proj->name) }}</span>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs font-medium">No projects registered in {{ $user->district_name }}.</div>
                @endforelse
            </div>
            <!-- Footer -->
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-150 text-right">
                <button onclick="closeProjectsModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-350 text-slate-750 font-bold uppercase rounded-lg text-[9.5px] transition-all">
                    Close View
                </button>
            </div>
        </div>
    </div>

    <!-- Towns Modal -->
    <div id="towns-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 flex flex-col max-h-[80vh] overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="towns-modal-content">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2 text-violet-750">
                    <i class="bi bi-pin-map-fill text-lg"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Towns in {{ strtoupper($user->district_name ?? 'District') }}</h3>
                </div>
                <button onclick="closeTownsModal()" class="w-6 h-6 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="p-6 overflow-y-auto space-y-2 flex-1 custom-scroll">
                @forelse($townsList as $index => $town)
                    <div class="px-4 py-3 bg-slate-50 border border-slate-150 rounded-xl flex items-center hover:bg-slate-100/70 transition-all">
                        <span class="text-xs font-bold text-slate-700">{{ $index + 1 }}. {{ strtoupper($town->name) }}</span>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs font-medium">No towns mapped in {{ $user->district_name }}.</div>
                @endforelse
            </div>
            <!-- Footer -->
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-150 text-right">
                <button onclick="closeTownsModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-350 text-slate-750 font-bold uppercase rounded-lg text-[9.5px] transition-all">
                    Close View
                </button>
            </div>
        </div>
    </div>

    <!-- Alert / Toast Messages via SweetAlert -->
    <script>
        let table;

        function openProjectsModal(e) {
            if (e) e.preventDefault();
            const modal = document.getElementById('projects-modal');
            const content = document.getElementById('projects-modal-content');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeProjectsModal() {
            const modal = document.getElementById('projects-modal');
            const content = document.getElementById('projects-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        function openTownsModal(e) {
            if (e) e.preventDefault();
            const modal = document.getElementById('towns-modal');
            const content = document.getElementById('towns-modal-content');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeTownsModal() {
            const modal = document.getElementById('towns-modal');
            const content = document.getElementById('towns-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        function triggerExport(type) {
            const scope = $('#filter-ownership').val();
            const districtId = $('#filter-district').val();
            const townId = $('#filter-town').val();
            const projectId = $('#filter-project').val();
            const searchVal = table ? table.search() : '';
            let baseUrl = '';
            if (type === 'csv') baseUrl = "{{ route('ews.developer.flats.export.csv') }}";
            else if (type === 'excel') baseUrl = "{{ route('ews.developer.flats.export.excel') }}";
            else if (type === 'pdf') baseUrl = "{{ route('ews.developer.flats.export.pdf') }}";

            const url = new URL(baseUrl, window.location.origin);
            if (scope) url.searchParams.append('ownership_scope', scope);
            if (districtId) url.searchParams.append('district_id', districtId);
            if (townId) url.searchParams.append('town_id', townId);
            if (projectId) url.searchParams.append('project_id', projectId);
            if (searchVal) url.searchParams.append('search', searchVal);

            window.location.href = url.toString();
        }

        const devFilterData = {!! json_encode($developerFilterTree ?? ['districts' => [], 'towns' => [], 'projects' => []]) !!};

        function onDistrictFilterChange() {
            const districtId = $('#filter-district').val();

            // Filter towns belonging ONLY to this developer and district
            let filteredTowns = devFilterData.towns || [];
            if (districtId) {
                filteredTowns = filteredTowns.filter(t => String(t.district_id) === String(districtId));
            }
            let seenTowns = new Set();
            let uniqueTowns = [];
            filteredTowns.forEach(t => {
                if (!seenTowns.has(String(t.id))) {
                    seenTowns.add(String(t.id));
                    uniqueTowns.push(t);
                }
            });

            let townOptions = '<option value="">' + (uniqueTowns.length ? 'All Towns (' + uniqueTowns.length + ')' : 'No Towns Available') + '</option>';
            uniqueTowns.forEach(t => {
                townOptions += '<option value="' + t.id + '">' + t.name + '</option>';
            });
            $('#filter-town').html(townOptions).val('');

            // Filter projects belonging ONLY to this developer and district
            let filteredProjects = devFilterData.projects || [];
            if (districtId) {
                filteredProjects = filteredProjects.filter(p => String(p.district_id) === String(districtId));
            }
            let seenProjects = new Set();
            let uniqueProjects = [];
            filteredProjects.forEach(p => {
                if (!seenProjects.has(String(p.id))) {
                    seenProjects.add(String(p.id));
                    uniqueProjects.push(p);
                }
            });

            let projOptions = '<option value="">' + (uniqueProjects.length ? 'All Projects (' + uniqueProjects.length + ')' : 'No Projects Available') + '</option>';
            uniqueProjects.forEach(p => {
                projOptions += '<option value="' + p.id + '">' + p.name + '</option>';
            });
            $('#filter-project').html(projOptions).val('');

            if (table) {
                table.draw();
            }
        }

        function onTownFilterChange() {
            const districtId = $('#filter-district').val();
            const townId = $('#filter-town').val();

            // Filter projects belonging ONLY to this developer, district, and town
            let filteredProjects = devFilterData.projects || [];
            if (townId) {
                filteredProjects = filteredProjects.filter(p => String(p.town_id) === String(townId));
            } else if (districtId) {
                filteredProjects = filteredProjects.filter(p => String(p.district_id) === String(districtId));
            }

            let seenProjects = new Set();
            let uniqueProjects = [];
            filteredProjects.forEach(p => {
                if (!seenProjects.has(String(p.id))) {
                    seenProjects.add(String(p.id));
                    uniqueProjects.push(p);
                }
            });

            let projOptions = '<option value="">' + (uniqueProjects.length ? 'All Projects (' + uniqueProjects.length + ')' : 'No Projects Available') + '</option>';
            uniqueProjects.forEach(p => {
                projOptions += '<option value="' + p.id + '">' + p.name + '</option>';
            });
            $('#filter-project').html(projOptions).val('');

            if (table) {
                table.draw();
            }
        }

        function onProjectFilterChange() {
            if (table) {
                table.draw();
            }
        }

        function resetAllFilters() {
            $('#filter-district').val('');
            if (table) {
                table.search('');
                const searchInput = $('div.dataTables_filter input');
                if (searchInput.length) {
                    searchInput.val('');
                }
            }
            onDistrictFilterChange();
        }

        function setOwnershipFilter(scope) {
            $('#filter-ownership').val(scope || 'my_flats');
            if (table) {
                table.draw();
            }
        }

        // Trigger Swal alert on Laravel session flash messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'SUCCESS',
                text: "{{ session('success') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                background: '#ffffff',
                color: '#1e293b',
                iconColor: '#3b82f6'
            });
        @endif

        // Delete confirmation trigger
        function confirmDelete(id) {
            Swal.fire({
                title: 'DELETE RECORD?',
                text: "This will permanently remove the EWS flat details from registry database and generate system deletion log.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'CONFIRM DELETE',
                cancelButtonText: 'CANCEL',
                background: '#ffffff',
                color: '#1e293b',
                iconColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }

        // Initialize Yajra Server-side DataTables
        $(document).ready(function() {
            table = $('#flats-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                lengthMenu: [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                ajax: {
                    url: "{{ route('ews.developer.flats.data') }}",
                    data: function (d) {
                        d.ownership_scope = $('#filter-ownership').val();
                        d.district_id = $('#filter-district').val();
                        d.town_id = $('#filter-town').val();
                        d.project_id = $('#filter-project').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'district_name', name: 'district_name', class: 'font-bold text-slate-900 uppercase' },
                    { data: 'town_name', name: 'town_name', class: 'font-semibold text-slate-700 capitalize' },
                    { data: 'project_name', name: 'project_name', class: 'text-slate-600 font-bold capitalize' },
                    { data: 'block_tower_number', name: 'block_tower_number', class: 'text-indigo-650 font-mono font-bold uppercase' },
                    { data: 'floor', name: 'floor', class: 'font-bold text-slate-800 capitalize' },
                    { data: 'flat_number', name: 'flat_number', class: 'text-violet-655 font-black font-mono' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-right' }
                ],
                drawCallback: function(settings) {
                    const api = this.api();
                    const info = api.page.info();
                    const filteredCount = (info && typeof info.recordsDisplay !== 'undefined') ? info.recordsDisplay : 0;
                    const totalCount = (info && typeof info.recordsTotal !== 'undefined') ? info.recordsTotal : 0;

                    const countNumEl = document.getElementById('filtered-count-num');
                    const statusPill = document.getElementById('filter-status-pill');
                    const countLabel = document.getElementById('filter-count-label');

                    if (countNumEl) {
                        countNumEl.textContent = Number(filteredCount).toLocaleString();
                        if (filteredCount === 0) {
                            countNumEl.classList.remove('text-sky-700');
                            countNumEl.classList.add('text-rose-600');
                        } else {
                            countNumEl.classList.remove('text-rose-600');
                            countNumEl.classList.add('text-sky-700');
                        }
                    }

                    const distVal = $('#filter-district').val();
                    const townVal = $('#filter-town').val();
                    const projVal = $('#filter-project').val();
                    const searchVal = (api.search() && typeof api.search() === 'string') ? api.search().trim() : '';

                    const isFiltered = (distVal !== '' || townVal !== '' || projVal !== '' || searchVal !== '');

                    if (statusPill) {
                        if (isFiltered) {
                            statusPill.className = "text-[8px] font-mono font-bold px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase flex items-center gap-1";
                            statusPill.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Filtered';
                        } else {
                            statusPill.className = "text-[8px] font-mono font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 uppercase";
                            statusPill.textContent = "All Records";
                        }
                    }

                    if (countLabel) {
                        countLabel.textContent = isFiltered ? "Matched:" : "Total:";
                    }
                },
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search matrix...",
                    processing: '<div class="flex items-center justify-center p-2 text-indigo-600 font-bold text-[10px]"><i class="bi bi-arrow-repeat animate-spin mr-1"></i> Fetching records...</div>'
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [] // Disable default ordering, sorting resolved server-side
            });
        });
    </script>
</body>
</html>
