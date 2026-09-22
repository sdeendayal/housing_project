<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS STP - Registry Dashboard</title>
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
                            STP Command Dashboard
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
                        @if(!empty($displayZoneName))
                            <span class="text-[9px] bg-sky-100 text-sky-800 font-extrabold uppercase px-1.5 py-0.5 rounded border border-sky-200">({{ $displayZoneName }})</span>
                        @endif
                        <i class="bi bi-person-circle text-sky-600"></i>
                    </div>
                    <div class="text-[8.5px] text-slate-500 font-mono">
                        Zone: <span class="font-bold text-slate-700 uppercase">{{ $displayZoneName ?? 'N/A' }}</span> | Mobile: {{ $user->mobile }}
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
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded text-[8.5px] font-black uppercase">
                                <i class="bi bi-geo-alt-fill text-[8px]"></i> {{ $displayZoneName ?? 'ZONE' }}
                            </span>
                            <span class="text-[9px] text-slate-400 font-mono hidden md:inline">| EWS Registry Console</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 relative z-10">
                        <a href="{{ route('ews.developer.flats.create') }}" class="px-3 py-1.5 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white rounded-lg text-[9.5px] font-black uppercase tracking-wider shadow-sm flex items-center gap-1 transition-all">
                            <i class="bi bi-plus-lg text-[10px]"></i>
                            <span>Register New Flat</span>
                        </a>
                    </div>
                </div>

                <!-- Telemetry Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                    <!-- Stat 1: Zone Allotted Flats from 4,211 State Pool -->
                    <a href="{{ route('ews.developer.dashboard', ['view' => 'allotted']) }}" class="bg-white border border-amber-200/80 hover:border-amber-400 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between transition-all group">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-wider text-amber-600">Total Allotted Flats</span>
                            <h4 class="text-xl font-black text-amber-600 font-mono mt-0.5">{{ number_format($stats['total_allotted']) }}</h4>
                            <span class="block text-[8px] text-slate-400 font-mono uppercase mt-1">From 4,211 State Pool</span>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 group-hover:scale-110 transition-all">
                            <i class="bi bi-houses-fill text-lg"></i>
                        </div>
                    </a>

                    <!-- Stat 2: Total Zone Offered Flats -->
                    <a href="{{ route('ews.developer.dashboard', ['view' => 'district']) }}" class="bg-white border border-sky-200/80 hover:border-sky-400 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between transition-all group">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-wider text-sky-600">All {{ strtoupper(trim(str_ireplace('ZONE', '', $displayZoneName ?? 'ROHTAK'))) }} Offered Flats</span>
                            <h4 class="text-xl font-black text-sky-600 font-mono mt-0.5">{{ $stats['total_flats'] }}</h4>
                            <span class="block text-[8px] text-slate-400 font-mono uppercase mt-1">Total Offered In Zone</span>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100 group-hover:scale-110 transition-all">
                            <i class="bi bi-building text-lg"></i>
                        </div>
                    </a>

                    <!-- Stat 3: Offered By Me -->
                    <a href="{{ route('ews.developer.dashboard', ['view' => 'my_flats']) }}" class="bg-white border border-emerald-200/80 hover:border-emerald-400 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between transition-all group">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-wider text-emerald-600">Offered By Me</span>
                            <h4 class="text-xl font-black text-emerald-600 font-mono mt-0.5">{{ $stats['my_flats'] }}</h4>
                            <span class="block text-[8px] text-emerald-500 font-mono uppercase mt-1">Flats Offered By Me</span>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 group-hover:scale-110 transition-all">
                            <i class="bi bi-person-check-fill text-lg"></i>
                        </div>
                    </a>
                </div>


            @elseif($currentView === 'allotted')
                <!-- Compact Zone Allotted Header -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl px-4 py-2 text-white shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white shrink-0">
                            <i class="bi bi-houses-fill text-xs"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xs font-black uppercase tracking-wider">{{ $displayZoneName ?? 'Zone' }} Allotted Flats Pool</h2>
                                <span class="px-1.5 py-0.2 bg-white/20 text-white rounded text-[8px] font-black uppercase">Pool</span>
                            </div>
                            <p class="text-[8.5px] text-amber-100 font-mono">{{ number_format($stats['total_allotted']) }} flats in {{ $displayZoneName ?? 'Zone' }} (out of {{ number_format($stats['state_allotted_total'] ?? 4211) }} state total)</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-base font-black font-mono leading-none">{{ number_format($stats['total_allotted']) }}</span>
                        <span class="block text-[7px] uppercase tracking-widest text-amber-200">Allotted</span>
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
                    <div>
                        <h3 id="table-heading-title" class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <i class="bi bi-file-earmark-text text-sky-500 text-sm"></i>
                            @if($currentView === 'allotted')
                                {{ $displayZoneName ?? 'Zone' }} Allotted Flats Pool (From 4,211 State Allotments)
                            @elseif($currentView === 'my_flats')
                                Flats Offered By Me
                            @else
                                All {{ $displayZoneName ?? 'Zone' }} Offered Builder Flats
                            @endif
                            @if(!empty($displayZoneName))
                                <span class="px-2 py-0.5 bg-sky-100 text-sky-800 border border-sky-200 rounded text-[9px] font-black uppercase">
                                    <i class="bi bi-geo-alt-fill me-0.5"></i> {{ $displayZoneName }}
                                </span>
                            @endif
                        </h3>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 self-end sm:self-auto">
                        <!-- Ownership Scope Filter Tabs -->
                        <div class="inline-flex bg-slate-200/80 p-0.5 rounded-lg text-[10px] font-bold">
                            <button type="button" id="btn-scope-all" onclick="setOwnershipFilter('all')"
                                class="px-3 py-1 rounded-md transition-all uppercase tracking-wider {{ in_array($currentView, ['my_flats', 'allotted']) ? 'text-slate-600 font-bold' : 'bg-white text-sky-700 shadow-sm font-black' }}">
                                <i class="bi bi-building me-1"></i> All {{ trim(str_ireplace('ZONE', '', $displayZoneName ?? 'Rohtak')) }} Offered ({{ $stats['total_flats'] }})
                            </button>
                            <button type="button" id="btn-scope-my" onclick="setOwnershipFilter('my_flats')"
                                class="px-3 py-1 rounded-md transition-all uppercase tracking-wider {{ $currentView === 'my_flats' ? 'bg-white text-emerald-700 shadow-sm font-black' : 'text-slate-600 font-bold' }}">
                                <i class="bi bi-person-check-fill me-1"></i> Offered By Me ({{ $stats['my_flats'] }})
                            </button>
                            <button type="button" id="btn-scope-allotted" onclick="setOwnershipFilter('allotted')"
                                class="px-3 py-1 rounded-md transition-all uppercase tracking-wider {{ $currentView === 'allotted' ? 'bg-white text-amber-700 shadow-sm font-black' : 'text-slate-600 font-bold' }}">
                                <i class="bi bi-houses-fill me-1"></i> Zone Allotted ({{ number_format($stats['total_allotted']) }})
                            </button>
                        </div>

                        <input type="hidden" id="filter-ownership" value="{{ $currentView === 'allotted' ? 'allotted' : ($currentView === 'my_flats' ? 'my_flats' : 'all') }}">

                        <div class="w-px h-5 bg-slate-200 mx-1"></div>

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
                        <!-- Step 1: District Filter (Filtered by Zone) -->
                        <div class="lg:col-span-3 space-y-1">
                            <label for="filter-district" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                <span class="flex items-center gap-1">
                                    <span class="px-1 py-0.2 rounded text-[8px] bg-sky-100 text-sky-800 font-black">1</span>
                                    <span>Select District</span>
                                </span>
                                <span class="text-[8px] text-slate-400 font-mono font-bold">{{ count($districts ?? []) }} In Zone</span>
                            </label>
                            <div class="relative">
                                <select id="filter-district" onchange="onDistrictFilterChange()"
                                    class="w-full bg-white border border-slate-250 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none shadow-2xs">
                                    <option value="">All {{ $displayZoneName ?? 'Zone' }} Districts</option>
                                    @foreach($districts ?? [] as $d)
                                        <option value="{{ $d->id }}">{{ strtoupper($d->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Town Filter (Cascading) -->
                        <div id="filter-town-box" class="lg:col-span-3 space-y-1">
                            <label for="filter-town" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center gap-1">
                                <span class="px-1 py-0.2 rounded text-[8px] bg-violet-100 text-violet-800 font-black">2</span>
                                <span>Name of Town</span>
                            </label>
                            <select id="filter-town" onchange="onTownFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none shadow-2xs">
                                <option value="">All Towns</option>
                                @foreach($townsList ?? [] as $t)
                                    <option value="{{ $t->id }}">{{ strtoupper($t->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 3: Project Filter (Cascading) -->
                        <div id="filter-project-box" class="lg:col-span-4 space-y-1">
                            <label for="filter-project" class="block text-[9.5px] font-black uppercase text-slate-600 tracking-wider flex items-center gap-1">
                                <span class="px-1 py-0.2 rounded text-[8px] bg-indigo-100 text-indigo-800 font-black">3</span>
                                <span>Name of Project</span>
                            </label>
                            <select id="filter-project" onchange="onProjectFilterChange()"
                                class="w-full bg-white border border-slate-250 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-2xs">
                                <option value="">All Projects</option>
                                @foreach($projectsList ?? [] as $p)
                                    <option value="{{ $p->id }}">{{ strtoupper($p->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reset Filter Button -->
                        <div class="lg:col-span-2 flex items-center">
                            <button type="button" onclick="resetAllFilters()"
                                class="w-full px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 hover:text-slate-900 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-2xs flex items-center justify-center gap-1.5">
                                <i class="bi bi-arrow-counterclockwise text-xs"></i>
                                <span>Reset Filters</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content (Yajra Server-side) -->
                <div class="p-5">
                    <table class="w-full text-left border-collapse" id="flats-table">
                        <thead>
                            <tr class="bg-slate-55/30 border-b border-slate-200 text-[9px] text-slate-500 uppercase font-black tracking-wider">
                                <th style="width: 5%;">S.No.</th>
                                <th id="th-col-1">{{ $currentView === 'allotted' ? 'District' : 'District Name' }}</th>
                                <th id="th-col-2">Town Name</th>
                                <th id="th-col-3">Project Name</th>
                                <th id="th-col-4">Block / Tower No.</th>
                                <th id="th-col-5">{{ $currentView === 'allotted' ? 'Allottee Name' : 'Floor Details' }}</th>
                                <th id="th-col-6">Flat No.</th>
                                <th id="th-col-7">{{ $currentView === 'allotted' ? 'Mobile No.' : 'Unique Flat Code' }}</th>
                                <th id="th-col-8">{{ $currentView === 'allotted' ? 'Possession Status' : 'Ownership' }}</th>
                                <th style="text-align: right; width: 14%;">Actions</th>
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

        function onDistrictFilterChange() {
            const districtId = $('#filter-district').val();

            // Fetch towns for selected district
            let townUrl = "{{ route('ews.developer.towns') }}";
            if (districtId) townUrl += "?district_id=" + districtId;

            $.getJSON(townUrl, function(data) {
                let options = '<option value="">All Towns</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-town').html(options);
            });

            // Fetch projects for selected district
            let projUrl = "{{ route('ews.developer.projects') }}";
            if (districtId) projUrl += "?district_id=" + districtId;

            $.getJSON(projUrl, function(data) {
                let options = '<option value="">All Projects</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-project').html(options);
            });

            if (table) {
                table.draw();
            }
        }

        function onTownFilterChange() {
            const districtId = $('#filter-district').val();
            const townId = $('#filter-town').val();

            let projUrl = "{{ route('ews.developer.projects') }}";
            let params = [];
            if (districtId) params.push("district_id=" + districtId);
            if (townId) params.push("town_id=" + townId);
            if (params.length) projUrl += "?" + params.join("&");

            $.getJSON(projUrl, function(data) {
                let options = '<option value="">All Projects</option>';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.id + '">' + item.name.toUpperCase() + '</option>';
                });
                $('#filter-project').html(options);
            });

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
            onDistrictFilterChange();
        }

        function setOwnershipFilter(scope) {
            $('#filter-ownership').val(scope);

            // Reset button active styling
            $('#btn-scope-all').removeClass('bg-white text-sky-700 shadow-sm font-black').addClass('text-slate-600 font-bold');
            $('#btn-scope-my').removeClass('bg-white text-emerald-700 shadow-sm font-black').addClass('text-slate-600 font-bold');
            $('#btn-scope-allotted').removeClass('bg-white text-amber-700 shadow-sm font-black').addClass('text-slate-600 font-bold');

            if (scope === 'my_flats') {
                $('#btn-scope-my').addClass('bg-white text-emerald-700 shadow-sm font-black').removeClass('text-slate-600 font-bold');
                $('#table-heading-title').html('<i class="bi bi-person-check-fill text-emerald-500 text-sm"></i> Flats Offered By Me');

                if (table) {
                    table.columns([2, 3, 4]).visible(true);
                }

                $('#th-col-1').text('District Name');
                $('#th-col-2').text('Town Name');
                $('#th-col-3').text('Project Name');
                $('#th-col-4').text('Block / Tower No.');
                $('#th-col-5').text('Floor Details');
                $('#th-col-6').text('Flat No.');
                $('#th-col-7').text('Unique Flat Code');
                $('#th-col-8').text('Ownership');

                $('#filter-town-box, #filter-project-box').show();

                // Sidebar Menu Active Toggle
                $('#nav-my-flats').addClass('bg-slate-800 text-white font-bold').removeClass('text-slate-300 font-medium');
                $('#nav-district-flats').removeClass('bg-slate-800 text-white font-bold').addClass('text-slate-300 font-medium');
            } else if (scope === 'allotted') {
                $('#btn-scope-allotted').addClass('bg-white text-amber-700 shadow-sm font-black').removeClass('text-slate-600 font-bold');
                $('#table-heading-title').html('<i class="bi bi-houses-fill text-amber-500 text-sm"></i> {{ $displayZoneName ?? "Zone" }} Allotted Flats Pool (From 4,211 State Allotments)');

                if (table) {
                    table.columns([2, 3, 4]).visible(false);
                }

                $('#th-col-1').text('District');
                $('#th-col-5').text('Allottee Name');
                $('#th-col-6').text('Flat No.');
                $('#th-col-7').text('Mobile No.');
                $('#th-col-8').text('Possession Status');

                // For allotted pool, town & project are consolidated, district filter operates directly
                $('#filter-town-box, #filter-project-box').hide();

                // Sidebar Menu Active Toggle
                $('#nav-my-flats').removeClass('bg-slate-800 text-white font-bold').addClass('text-slate-300 font-medium');
                $('#nav-district-flats').removeClass('bg-slate-800 text-white font-bold').addClass('text-slate-300 font-medium');
            } else {
                $('#btn-scope-all').addClass('bg-white text-sky-700 shadow-sm font-black').removeClass('text-slate-600 font-bold');
                $('#table-heading-title').html('<i class="bi bi-file-earmark-text text-sky-500 text-sm"></i> All {{ $displayZoneName ?? "Zone" }} Offered Builder Flats');

                if (table) {
                    table.columns([2, 3, 4]).visible(true);
                }

                $('#th-col-1').text('District Name');
                $('#th-col-2').text('Town Name');
                $('#th-col-3').text('Project Name');
                $('#th-col-4').text('Block / Tower No.');
                $('#th-col-5').text('Floor Details');
                $('#th-col-6').text('Flat No.');
                $('#th-col-7').text('Unique Flat Code');
                $('#th-col-8').text('Ownership');

                $('#filter-town-box, #filter-project-box').show();

                // Sidebar Menu Active Toggle
                $('#nav-district-flats').addClass('bg-slate-800 text-white font-bold').removeClass('text-slate-300 font-medium');
                $('#nav-my-flats').removeClass('bg-slate-800 text-white font-bold').addClass('text-slate-300 font-medium');
            }
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
            // Apply initial column labels if starting on allotted view
            const initScope = $('#filter-ownership').val();
            if (initScope === 'allotted') {
                $('#th-col-1').text('District');
                $('#th-col-5').text('Allottee Name');
                $('#th-col-6').text('Flat No.');
                $('#th-col-7').text('Mobile No.');
                $('#th-col-8').text('Possession Status');
                $('#filter-town-box, #filter-project-box').hide();
            }

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
                    { data: 'town_name', name: 'town_name', visible: initScope !== 'allotted' },
                    { data: 'project_name', name: 'project_name', class: 'text-slate-500', visible: initScope !== 'allotted' },
                    { data: 'block_tower_number', name: 'block_tower_number', class: 'text-indigo-650 font-mono font-bold', visible: initScope !== 'allotted' },
                    { data: 'floor', name: 'floor', class: 'font-bold text-slate-800' },
                    { data: 'flat_number', name: 'flat_number', class: 'text-violet-655 font-black font-mono' },
                    { data: 'flat_code', name: 'flat_code', class: 'text-emerald-600 font-bold font-mono' },
                    { data: 'added_by', name: 'added_by', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-right' }
                ],
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
