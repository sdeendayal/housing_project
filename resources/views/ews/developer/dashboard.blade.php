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

    <!-- DEEP NAVY / SLATE SIDEBAR -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 shrink-0 h-full shadow-xl z-20">
        <!-- Brand logo -->
        <div class="h-16 px-6 border-b border-slate-800 flex items-center gap-2.5 shrink-0 bg-slate-950">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="bi bi-shield-fill-check text-white text-sm"></i>
            </div>
            <div>
                <h1 class="text-xs font-black tracking-tight text-white uppercase">EWS Portal</h1>
                <p class="text-[8px] text-slate-500 font-mono tracking-widest uppercase">Developer Hub</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto custom-scroll">
            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Navigation Console</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.dashboard') }}" id="nav-dashboard"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ $currentView === 'dashboard' ? 'bg-slate-800 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all">
                        <i class="bi bi-speedometer2 text-sky-400"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('ews.developer.dashboard', ['view' => 'district']) }}" id="nav-district-flats"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ $currentView === 'district' ? 'bg-slate-800 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all">
                        <i class="bi bi-building text-sky-400"></i>
                        <span>{{ !empty($user->district_name) ? strtoupper($user->district_name) : 'My District' }} Flats</span>
                    </a>
                    <a href="{{ route('ews.developer.dashboard', ['view' => 'my_flats']) }}" id="nav-my-flats"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ $currentView === 'my_flats' ? 'bg-slate-800 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all">
                        <i class="bi bi-person-check-fill text-emerald-400"></i>
                        <span>Flats Added By Me</span>
                    </a>
                    <a href="{{ route('ews.developer.flats.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-plus-circle text-slate-400"></i>
                        <span>Register Flat</span>
                    </a>
                </div>
            </div>

            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Audit & Activity</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-journal-text text-slate-400"></i>
                        <span>Developer Logs</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Session Details -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex flex-col gap-2 shrink-0">
            <div class="flex items-center justify-between text-[9px] text-slate-500 font-mono">
                <span>VERSION: 2.4-stable</span>
            </div>
            <a href="{{ route('ews.developer.logout') }}" class="w-full py-1.5 bg-red-500/20 hover:bg-red-600 text-red-300 rounded-lg text-[9px] font-black uppercase transition-all flex items-center justify-center gap-1 border border-red-500/30">
                <i class="bi bi-power"></i>
                <span>Logout Session</span>
            </a>
        </div>
    </aside>

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
                            {{ $user->district_name ?? 'District' }} District Master Registry
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
                        @if(!empty($user->district_name))
                            <span class="text-[9px] bg-sky-100 text-sky-800 font-extrabold uppercase px-1.5 py-0.5 rounded border border-sky-200">({{ strtoupper($user->district_name) }})</span>
                        @endif
                        <i class="bi bi-person-circle text-sky-600"></i>
                    </div>
                    <div class="text-[8.5px] text-slate-500 font-mono">
                        District: <span class="font-bold text-slate-700 uppercase">{{ $user->district_name ?? 'N/A' }}</span> | Mobile: {{ $user->mobile }}
                    </div>
                </div>
                <a href="{{ route('ews.developer.logout') }}" class="md:hidden px-3 py-1.5 bg-red-50 text-red-650 rounded-lg text-[9px] font-black uppercase border border-red-100">
                    Logout
                </a>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5 custom-scroll">
            
            @if($currentView === 'dashboard')
                <!-- DASHBOARD VIEW: OVERVIEW TELEMETRY & PROJECT BREAKDOWN -->
                
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-indigo-950 rounded-xl p-6 text-white shadow-md relative overflow-hidden">
                    <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-gradient-to-l from-indigo-500/10 to-transparent pointer-events-none"></div>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                        <div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-md text-[9px] font-black uppercase mb-2">
                                <i class="bi bi-geo-alt-fill"></i> ASSIGNED DISTRICT: {{ strtoupper($user->district_name ?? 'ALL') }}
                            </span>
                            <h2 class="text-lg font-black tracking-tight text-white">Welcome, {{ $user->name }}</h2>
                            <p class="text-xs text-slate-300 mt-0.5">EWS Builder Flats Registry Console & Inventory Management Panel</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('ews.developer.flats.create') }}" class="px-4 py-2 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white rounded-lg text-xs font-black uppercase tracking-wider shadow-lg flex items-center gap-1.5 transition-all">
                                <i class="bi bi-plus-lg"></i>
                                <span>Register New Flat</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Telemetry Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Stat 1: Total District Flats -->
                    <a href="{{ route('ews.developer.dashboard', ['view' => 'district']) }}" class="bg-white border border-sky-200/80 hover:border-sky-400 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between transition-all group">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-wider text-sky-600">District Master</span>
                            <h4 class="text-xl font-black text-sky-600 font-mono mt-0.5">{{ $stats['total_flats'] }}</h4>
                            <span class="block text-[8px] text-slate-400 font-mono uppercase mt-1">Total {{ $user->district_name ?? '' }} Flats</span>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100 group-hover:scale-110 transition-all">
                            <i class="bi bi-building text-lg"></i>
                        </div>
                    </a>

                    <!-- Stat 2: Flats Added By Me -->
                    <a href="{{ route('ews.developer.dashboard', ['view' => 'my_flats']) }}" class="bg-white border border-emerald-200/80 hover:border-emerald-400 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between transition-all group">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-wider text-emerald-600">Personal Entries</span>
                            <h4 class="text-xl font-black text-emerald-600 font-mono mt-0.5">{{ $stats['my_flats'] }}</h4>
                            <span class="block text-[8px] text-emerald-500 font-mono uppercase mt-1">My Created Flats</span>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 group-hover:scale-110 transition-all">
                            <i class="bi bi-person-check-fill text-lg"></i>
                        </div>
                    </a>

                    <!-- Stat 3: Active Projects -->
                    <div class="bg-white border border-indigo-200/80 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-wider text-indigo-600">District Projects</span>
                            <h4 class="text-xl font-black text-indigo-600 font-mono mt-0.5">{{ $stats['total_projects'] }}</h4>
                            <span class="block text-[8px] text-slate-400 font-mono uppercase mt-1">Active Projects</span>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                            <i class="bi bi-diagram-3-fill text-lg"></i>
                        </div>
                    </div>

                    <!-- Stat 4: Coverage Towns -->
                    <div class="bg-white border border-violet-200/80 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between">
                        <div>
                            <span class="block text-[9px] font-black uppercase tracking-wider text-violet-600">Coverage Towns</span>
                            <h4 class="text-xl font-black text-violet-600 font-mono mt-0.5">{{ $stats['total_towns'] }}</h4>
                            <span class="block text-[8px] text-slate-400 font-mono uppercase mt-1">Mapped Towns</span>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center border border-violet-100">
                            <i class="bi bi-pin-map-fill text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- District Project Breakdown Cards Grid -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm dev-shadow space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-150 pb-3">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-buildings-fill text-sky-500 text-base"></i>
                            <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider">
                                {{ $user->district_name ?? 'District' }} EWS Projects Breakdown
                            </h3>
                        </div>
                        <span class="text-[9px] font-mono font-bold text-slate-400 uppercase">
                            {{ count($projectBreakdown) }} Active Project Lines
                        </span>
                    </div>

                    @if(count($projectBreakdown) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @foreach($projectBreakdown as $project)
                                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 flex items-center justify-between">
                                    <div>
                                        <div class="text-[9px] font-black uppercase text-slate-400 tracking-wider">{{ $project->town_name }}</div>
                                        <div class="text-xs font-bold text-slate-900 mt-0.5">{{ $project->project_name }}</div>
                                        <div class="text-[8px] text-slate-500 font-mono mt-1">Towers: {{ $project->towers_count }} Blocks</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2.5 py-1 bg-sky-100 text-sky-800 font-black font-mono rounded-md text-xs">
                                            {{ $project->total_flats }} Flats
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-slate-400 text-xs italic">
                            No EWS builder projects registered yet in {{ $user->district_name ?? 'District' }}.
                        </div>
                    @endif
                </div>
            @elseif($currentView === 'district')
                <!-- DISTRICT FLATS VIEW -->
                <div class="bg-gradient-to-r from-sky-600 to-indigo-700 rounded-xl p-5 text-white shadow-sm flex items-center justify-between">
                    <div>
                        <span class="inline-block px-2 py-0.5 bg-white/20 text-white rounded text-[9px] font-black uppercase mb-1">
                            <i class="bi bi-building"></i> DISTRICT SCOPED MATRIX
                        </span>
                        <h2 class="text-base font-black uppercase tracking-wider">{{ $user->district_name ?? 'District' }} Master EWS Flats</h2>
                        <p class="text-[9px] text-sky-100 font-mono">Viewing all allotment proforma flats registered under {{ $user->district_name }} District Authority</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black font-mono">{{ $stats['total_flats'] }}</span>
                        <span class="block text-[8px] uppercase tracking-widest text-sky-200">District Total</span>
                    </div>
                </div>
            @elseif($currentView === 'my_flats')
                <!-- MY FLATS INVENTORY VIEW -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-xl p-5 text-white shadow-sm flex items-center justify-between">
                    <div>
                        <span class="inline-block px-2 py-0.5 bg-white/20 text-white rounded text-[9px] font-black uppercase mb-1">
                            <i class="bi bi-person-check-fill"></i> PERSONAL INVENTORY
                        </span>
                        <h2 class="text-base font-black uppercase tracking-wider">Flats Registered By My Account</h2>
                        <p class="text-[9px] text-emerald-100 font-mono">Viewing flats created directly by your developer account (ID: #{{ $user->id }})</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black font-mono">{{ $stats['my_flats'] }}</span>
                        <span class="block text-[8px] uppercase tracking-widest text-emerald-200">My Entries</span>
                    </div>
                </div>
            @endif

            <!-- REGISTRY DATABASE TABLE SECTION -->
            <section class="bg-white border border-slate-200/80 rounded-xl shadow-sm dev-shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <i class="bi bi-file-earmark-text text-sky-500 text-sm"></i>
                            EWS Builder Flats Table
                            @if(!empty($user->district_name))
                                <span class="px-2 py-0.5 bg-sky-100 text-sky-800 border border-sky-200 rounded text-[9px] font-black uppercase">
                                    <i class="bi bi-geo-alt-fill me-0.5"></i> {{ strtoupper($user->district_name) }}
                                </span>
                            @endif
                        </h3>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 self-end sm:self-auto">
                        <!-- Ownership Scope Filter Tabs -->
                        <div class="inline-flex bg-slate-200/80 p-0.5 rounded-lg text-[10px] font-bold">
                            <button type="button" id="btn-scope-all" onclick="setOwnershipFilter('all')"
                                class="px-3 py-1 rounded-md transition-all uppercase tracking-wider {{ $currentView === 'my_flats' ? 'text-slate-600 font-bold' : 'bg-white text-sky-700 shadow-sm font-black' }}">
                                <i class="bi bi-building me-1"></i> All District Records
                            </button>
                            <button type="button" id="btn-scope-my" onclick="setOwnershipFilter('my_flats')"
                                class="px-3 py-1 rounded-md transition-all uppercase tracking-wider {{ $currentView === 'my_flats' ? 'bg-white text-emerald-700 shadow-sm font-black' : 'text-slate-600 font-bold' }}">
                                <i class="bi bi-person-check-fill me-1"></i> Added By Me Only
                            </button>
                        </div>

                        <input type="hidden" id="filter-ownership" value="{{ $currentView === 'my_flats' ? 'my_flats' : 'all' }}">

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

                <!-- Table Content (Yajra Server-side) -->
                <div class="p-5">
                    <table class="w-full text-left border-collapse" id="flats-table">
                        <thead>
                            <tr class="bg-slate-55/30 border-b border-slate-200 text-[9px] text-slate-500 uppercase font-black tracking-wider">
                                <th style="width: 5%;">S.No.</th>
                                <th>District Name</th>
                                <th>Town Name</th>
                                <th>Project Name</th>
                                <th>Block / Tower No.</th>
                                <th>Floor Details</th>
                                <th>Flat No.</th>
                                <th>Ownership</th>
                                <th style="text-align: right; width: 15%;">Actions</th>
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

    <!-- Alert / Toast Messages via SweetAlert -->
    <script>
        let table;

        function triggerExport(type) {
            const scope = $('#filter-ownership').val();
            const searchVal = table ? table.search() : '';
            let baseUrl = '';
            if (type === 'csv') baseUrl = "{{ route('ews.developer.flats.export.csv') }}";
            else if (type === 'excel') baseUrl = "{{ route('ews.developer.flats.export.excel') }}";
            else if (type === 'pdf') baseUrl = "{{ route('ews.developer.flats.export.pdf') }}";

            const url = new URL(baseUrl, window.location.origin);
            if (scope) url.searchParams.append('ownership_scope', scope);
            if (searchVal) url.searchParams.append('search', searchVal);

            window.location.href = url.toString();
        }

        function setOwnershipFilter(scope) {
            $('#filter-ownership').val(scope);
            if (scope === 'my_flats') {
                $('#btn-scope-my').addClass('bg-white text-emerald-700 shadow-sm font-black').removeClass('text-slate-600 font-bold');
                $('#btn-scope-all').removeClass('bg-white text-sky-700 shadow-sm font-black').addClass('text-slate-600 font-bold');
                
                // Sidebar Menu Active Toggle
                $('#nav-my-flats').addClass('bg-slate-800 text-white font-bold').removeClass('text-slate-300 font-medium');
                $('#nav-district-flats').removeClass('bg-slate-800 text-white font-bold').addClass('text-slate-300 font-medium');
            } else {
                $('#btn-scope-all').addClass('bg-white text-sky-700 shadow-sm font-black').removeClass('text-slate-600 font-bold');
                $('#btn-scope-my').removeClass('bg-white text-emerald-700 shadow-sm font-black').addClass('text-slate-600 font-bold');

                // Sidebar Menu Active Toggle
                $('#nav-district-flats').addClass('bg-slate-800 text-white font-bold').removeClass('text-slate-300 font-medium');
                $('#nav-my-flats').removeClass('bg-slate-800 text-white font-bold').addClass('text-slate-300 font-medium');
            }
            if (table) {
                table.ajax.reload();
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
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'district_name', name: 'district_name', class: 'font-bold text-slate-900' },
                    { data: 'town_name', name: 'town_name' },
                    { data: 'project_name', name: 'project_name', class: 'text-slate-500' },
                    { data: 'block_tower_number', name: 'block_tower_number', class: 'text-indigo-650 font-mono font-bold' },
                    { data: 'floor', name: 'floor' },
                    { data: 'flat_number', name: 'flat_number', class: 'text-violet-650 font-black font-mono' },
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

            // Redraw on district filter change
            $('#filter-district').on('change', function() {
                table.draw();
            });

            // Handle Export Clicks dynamically incorporating active filters
            $('#export-csv').on('click', function(e) {
                e.preventDefault();
                let search = $('#flats-table_filter input').val() || '';
                let districtId = $('#filter-district').val() || '';
                let url = "{{ route('ews.developer.flats.export.csv') }}?search=" + encodeURIComponent(search) + "&district_id=" + districtId;
                window.location.href = url;
            });

            $('#export-pdf').on('click', function(e) {
                e.preventDefault();
                let search = $('#flats-table_filter input').val() || '';
                let districtId = $('#filter-district').val() || '';
                let url = "{{ route('ews.developer.flats.export.pdf') }}?search=" + encodeURIComponent(search) + "&district_id=" + districtId;
                window.location.href = url;
            });
        });
    </script>
</body>
</html>
