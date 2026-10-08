<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EWS STP - Physical Possession Module</title>
    
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
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .code-font { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dev-shadow { box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08); }

        table.dataTable {
            border-bottom: 1px solid #e2e8f0 !important;
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }
        table.dataTable thead th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            font-size: 10px !important;
            letter-spacing: 0.04em;
            padding: 8px 8px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            white-space: nowrap !important;
        }
        table.dataTable tbody td {
            padding: 7px 8px !important;
            font-size: 11.5px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle;
            white-space: nowrap !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0284c7 !important;
            color: white !important;
            border: none !important;
            border-radius: 6px;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden">

    @include('ews.developer.partials.sidebar')

    <!-- RIGHT MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-sky-50 border border-sky-200 flex items-center justify-center text-sky-600 shadow-sm">
                    <i class="bi bi-houses-fill text-lg"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black tracking-wide text-slate-800 uppercase flex items-center gap-2">
                        Physical Possession Module
                    </h2>
                    <p class="text-[10px] text-slate-500 font-medium">Verify handover, upload signed possession letters & photos, and track GPS location.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 flex items-center gap-1.5">
                    <i class="bi bi-geo-alt-fill text-rose-500"></i>
                    <span>Zone: <strong class="text-slate-900">{{ $displayZoneName }}</strong></span>
                </span>
            </div>
        </header>

        <!-- Dynamic Scrollable Content -->
        <div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
            
            <!-- STATS COUNTER CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Card -->
                <div class="bg-white border border-slate-200/80 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Allotted Flats</div>
                        <div class="text-2xl font-black text-slate-800 mt-1" id="stat-total">{{ number_format($stats['total_allotted']) }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Scope: <span class="font-bold text-slate-600" id="stat-project-name">{{ $scopeName }}</span></div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-xl shadow-inner">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>

                <!-- Possession Given Card -->
                <div class="bg-white border border-emerald-200/80 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600">Possession Given</div>
                        <div class="text-2xl font-black text-emerald-700 mt-1" id="stat-given">{{ number_format($stats['possession_given']) }}</div>
                        <div class="text-[10px] text-emerald-600 font-bold mt-0.5" id="stat-given-pct">
                            {{ $stats['total_allotted'] > 0 ? round(($stats['possession_given'] / $stats['total_allotted']) * 100, 1) : 0 }}% Complete
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 text-xl shadow-inner">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                </div>

                <!-- Possession Pending Card -->
                <div class="bg-white border border-amber-200/80 rounded-xl p-4 shadow-sm dev-shadow flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600">Pending Possession</div>
                        <div class="text-2xl font-black text-amber-700 mt-1" id="stat-pending">{{ number_format($stats['possession_pending']) }}</div>
                        <div class="text-[10px] text-amber-600 font-bold mt-0.5" id="stat-pending-desc">Awaiting physical handover</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 text-xl shadow-inner">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>

            <!-- HIERARCHICAL FILTER BAR (Zone -> District -> Project) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm dev-shadow">
                <form id="filter-form" onsubmit="return false;" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    
                    <!-- 1. Frozen Assigned Zone (Locked to Logged-in STP) -->
                    <div class="md:col-span-3">
                        <label class="block text-[10px] font-black uppercase text-slate-600 mb-1 tracking-wider flex items-center justify-between">
                            <span><i class="bi bi-shield-lock-fill text-sky-600"></i> Assigned Zone</span>
                            <span class="text-[9px] text-slate-400 lowercase font-mono">fixed</span>
                        </label>
                        <div class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-xs font-black text-slate-800 flex items-center justify-between select-none shadow-inner cursor-not-allowed">
                            <span class="flex items-center gap-1.5 truncate">
                                <i class="bi bi-lock-fill text-slate-500 text-xs"></i>
                                <span class="tracking-wide">{{ $displayZoneName }}</span>
                            </span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 uppercase tracking-wider shrink-0">Assigned</span>
                        </div>
                        <input type="hidden" name="zone_id" id="filter-zone-id" value="{{ $assignedZoneId }}">
                    </div>

                    <!-- 2. District Select -->
                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-black uppercase text-slate-600 mb-1 tracking-wider">
                            <i class="bi bi-geo-alt text-sky-600"></i> District
                        </label>
                        <select name="district_id" id="filter-district" onchange="onDistrictChange(this.value)"
                            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-sky-500 focus:bg-white transition-all cursor-pointer">
                            <option value="" selected>-- All Districts ({{ $displayZoneName }}) --</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">
                                    {{ strtoupper($d->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Project Select (with Abbreviation) -->
                    <div class="md:col-span-5">
                        <label class="block text-[10px] font-black uppercase text-slate-600 mb-1 tracking-wider">
                            <i class="bi bi-buildings text-sky-600"></i> Project Name
                        </label>
                        <select name="project_id" id="filter-project" onchange="onProjectChange(this.value)"
                            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-sky-500 focus:bg-white transition-all cursor-pointer">
                            <option value="" selected>-- All Projects ({{ $displayZoneName }}) --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->name }} {{ $p->project_abbr ? '[' . $p->project_abbr . ']' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <!-- Secondary Sub-filter bar -->
                <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Filter By Status:</span>
                        <div class="inline-flex rounded-xl border border-slate-200 bg-slate-100 p-1 shadow-inner gap-1" role="group">
                            <button type="button" onclick="filterByStatus('')" 
                                class="status-btn px-3.5 py-1.5 text-xs rounded-lg transition-all duration-150 flex items-center gap-1.5 bg-sky-600 text-white shadow-md font-black scale-[1.02] ring-2 ring-sky-300" 
                                data-status="">
                                <i class="bi bi-grid-fill text-[11px]"></i>
                                <span>All</span>
                                <span id="count-all" class="count-badge px-1.5 py-0.2 rounded-full text-[10.5px] font-mono font-bold bg-white/25 text-white transition-all">{{ $stats['total_allotted'] }}</span>
                            </button>
                            <button type="button" onclick="filterByStatus('PENDING')" 
                                class="status-btn px-3.5 py-1.5 text-xs rounded-lg transition-all duration-150 flex items-center gap-1.5 text-slate-600 hover:bg-slate-200/70 hover:text-slate-900 font-bold" 
                                data-status="PENDING">
                                <i class="bi bi-hourglass-split text-[11px] text-amber-500"></i>
                                <span>Pending</span>
                                <span id="count-pending" class="count-badge px-1.5 py-0.2 rounded-full text-[10.5px] font-mono font-bold bg-slate-200 text-slate-700 transition-all">{{ $stats['possession_pending'] }}</span>
                            </button>
                            <button type="button" onclick="filterByStatus('GIVEN')" 
                                class="status-btn px-3.5 py-1.5 text-xs rounded-lg transition-all duration-150 flex items-center gap-1.5 text-slate-600 hover:bg-slate-200/70 hover:text-slate-900 font-bold" 
                                data-status="GIVEN">
                                <i class="bi bi-check-circle-fill text-[11px] text-emerald-500"></i>
                                <span>Given</span>
                                <span id="count-given" class="count-badge px-1.5 py-0.2 rounded-full text-[10.5px] font-mono font-bold bg-slate-200 text-slate-700 transition-all">{{ $stats['possession_given'] }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Block / Tower:</span>
                        <input type="text" id="filter-block" placeholder="e.g. A, ET-01, EWS" onkeyup="debounceReload()"
                            class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-md text-xs font-medium focus:ring-1 focus:ring-sky-500">
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Search:</span>
                        <input type="text" id="search-keyword" placeholder="Search name, app no, flat, mobile..." onkeyup="debounceReload()"
                            class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-md text-xs font-medium w-64 focus:ring-1 focus:ring-sky-500">
                    </div>
                </div>
            </div>

            <!-- BENEFICIARIES DATA TABLE -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm dev-shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <span>Allotted Beneficiaries</span>
                            <span id="project-abbr-badge" class="hidden px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">Abbr: <span id="project-abbr-text"></span></span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Click "Handover Possession" on any record to capture signed documents and GPS location.</p>
                    </div>
                    <div class="text-[11px] font-mono font-bold text-slate-500" id="table-record-count">
                        Loading records...
                    </div>
                </div>

                <div class="p-3 overflow-x-auto">
                    <table id="beneficiaries-table" class="w-full text-left" style="width:100%">
                        <thead>
                            <tr>
                                <th class="w-10 text-center whitespace-nowrap">#</th>
                                <th class="whitespace-nowrap">App No</th>
                                <th class="whitespace-nowrap">Beneficiary Name</th>
                                <th class="whitespace-nowrap">Mobile</th>
                                <th class="whitespace-nowrap">Flat Number</th>
                                <th class="whitespace-nowrap">Floor / Block / Unit</th>
                                <th class="whitespace-nowrap">Status</th>
                                <th class="whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL 1: PHYSICAL POSSESSION SUBMISSION MODAL -->
    <div id="possessionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-400">
                        <i class="bi bi-file-earmark-check-fill text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wide">Handover Physical Possession</h3>
                        <p class="text-[10px] text-slate-300">Submit signed documents & verify geo-location</p>
                    </div>
                </div>
                <button type="button" onclick="closePossessionModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-sm transition-all">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Beneficiary Summary Pill -->
            <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between text-xs gap-2">
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Beneficiary</span>
                    <span class="font-extrabold text-slate-800" id="modal-ben-name">-</span>
                    <span class="text-slate-400 font-mono text-[10px] ml-1" id="modal-ben-app">#</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Flat No</span>
                    <span class="font-extrabold text-sky-700 font-mono" id="modal-ben-flat">-</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Mobile</span>
                    <span class="font-bold text-slate-700 font-mono" id="modal-ben-mobile">-</span>
                </div>
            </div>

            <!-- Form -->
            <form id="possession-submit-form" onsubmit="handlePossessionSubmit(event)" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" id="modal-ben-id" name="beneficiary_id">

                <!-- 1. Possession Status Toggle -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5 tracking-wider">
                        Possession Status <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition-all bg-white hover:bg-emerald-50/20" id="label-status-given">
                            <input type="radio" name="possession_status" value="GIVEN" checked onchange="toggleFormFields('GIVEN')" class="text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                            <div>
                                <div class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                                    <i class="bi bi-check-circle-fill text-emerald-500"></i> Possession Given
                                </div>
                                <div class="text-[10px] text-slate-500">Handover completed with letter</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-amber-500 cursor-pointer transition-all bg-white hover:bg-amber-50/20" id="label-status-pending">
                            <input type="radio" name="possession_status" value="PENDING" onchange="toggleFormFields('PENDING')" class="text-amber-600 focus:ring-amber-500 h-4 w-4">
                            <div>
                                <div class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                                    <i class="bi bi-clock-history text-amber-500"></i> Pending
                                </div>
                                <div class="text-[10px] text-slate-500">Not handed over yet</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- GIVEN SPECIFIC FIELDS CONTAINER -->
                <div id="given-fields-container" class="space-y-4">
                    
                    <!-- File 1: Possession Letter PDF (<= 500 KB) -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-file-earmark-pdf-fill text-red-500"></i>
                                Possession Letter (Signed) <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-red-50 text-red-600 font-bold border border-red-200">
                                PDF only, max 500 KB
                            </span>
                        </div>
                        <input type="file" name="possession_letter" id="input-possession-letter" accept="application/pdf" onchange="validatePdfSize(this)"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-red-500 file:text-white hover:file:bg-red-600 cursor-pointer">
                        <div id="pdf-size-feedback" class="text-[11px] mt-1 font-medium text-slate-500"></div>
                        <div id="existing-letter-preview" class="hidden mt-2 text-xs">
                            <a href="#" target="_blank" class="text-sky-600 hover:underline font-bold inline-flex items-center gap-1">
                                <i class="bi bi-paperclip"></i> View current uploaded letter
                            </a>
                        </div>
                    </div>

                    <!-- File 2: Beneficiary Flat Photo -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-camera-fill text-sky-500"></i>
                                Beneficiary with Flat Photo <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-sky-50 text-sky-600 font-bold border border-sky-200">
                                JPG, PNG max 2 MB
                            </span>
                        </div>
                        <input type="file" name="beneficiary_flat_photo" id="input-flat-photo" accept="image/jpeg,image/png,image/jpg" onchange="previewPhoto(this)"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-600 file:text-white hover:file:bg-sky-700 cursor-pointer">
                        
                        <!-- Thumbnail Preview -->
                        <div id="photo-preview-container" class="hidden mt-2.5 flex items-center gap-3">
                            <img id="photo-preview-img" src="" class="w-16 h-16 object-cover rounded-lg border border-slate-300 shadow-sm">
                            <span class="text-xs text-slate-600 font-medium">Selected image preview</span>
                        </div>
                    </div>

                    <!-- GPS Coordinates (Lat / Long) -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-geo-alt-fill text-rose-500"></i>
                                Handover GPS Geolocation <span class="text-red-500">*</span>
                            </label>
                            <button type="button" onclick="detectGPSLocation()" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-[10px] font-bold flex items-center gap-1 shadow-sm transition-all">
                                <i class="bi bi-crosshair"></i> Detect GPS Location
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Latitude</span>
                                <input type="number" step="any" name="latitude" id="input-latitude" placeholder="28.xxxxxx"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold text-slate-800 focus:ring-1 focus:ring-sky-500">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Longitude</span>
                                <input type="number" step="any" name="longitude" id="input-longitude" placeholder="77.xxxxxx"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold text-slate-800 focus:ring-1 focus:ring-sky-500">
                            </div>
                        </div>
                        <div id="gps-status-feedback" class="text-[10px] text-slate-500 font-medium"></div>
                    </div>
                </div>

                <!-- Remarks / Notes (Reason if Pending) -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1">
                        <span id="remarks-label">Handover Remarks</span>
                        <span id="remarks-required-star" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea name="remarks" id="input-remarks" rows="2" placeholder="Enter any handover remarks, comments, or reason if pending..."
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 focus:ring-1 focus:ring-sky-500"></textarea>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closePossessionModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="btn-submit-possession" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-extrabold uppercase tracking-wide transition-all shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                        <i class="bi bi-shield-check"></i>
                        <span>Save & Submit Possession</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: VIEW POSSESSION DETAILS & AUDIT TRAIL -->
    <div id="viewModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-150 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400">
                        <i class="bi bi-patch-check-fill text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wide">Possession Verification Record</h3>
                        <p class="text-[10px] text-slate-300 font-mono" id="view-modal-subheading">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeViewModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-sm transition-all">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Content Area -->
            <div class="p-6 overflow-y-auto custom-scroll space-y-5">
                
                <!-- Status & Date Banner -->
                <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/40 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-black uppercase text-emerald-800 tracking-wider">Status</div>
                        <div class="text-base font-black text-emerald-700 flex items-center gap-1.5 mt-0.5">
                            <i class="bi bi-check-circle-fill"></i> Physical Possession Given
                        </div>
                        <div class="text-xs text-slate-600 mt-1">Handover Date: <strong id="view-given-at" class="text-slate-800">-</strong></div>
                    </div>
                    <div id="view-map-btn-container"></div>
                </div>

                <!-- Evidence Documents Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Letter Card -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-bold uppercase text-slate-500 flex items-center gap-1 mb-2">
                                <i class="bi bi-file-earmark-pdf-fill text-red-500"></i> Possession Letter (PDF)
                            </div>
                            <div class="text-xs text-slate-700 font-medium truncate" id="view-letter-name">No letter uploaded</div>
                        </div>
                        <div class="mt-4" id="view-letter-action"></div>
                    </div>

                    <!-- Photo Card -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-bold uppercase text-slate-500 flex items-center gap-1 mb-2">
                                <i class="bi bi-camera-fill text-sky-500"></i> Beneficiary & Flat Photo
                            </div>
                            <div id="view-photo-container" class="mt-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Remarks & Geolocation Card -->
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Captured GPS Coordinates:</span>
                        <span class="font-mono font-bold text-slate-700" id="view-coords">-</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Remarks:</span>
                        <p class="text-slate-700 italic bg-white p-2.5 rounded-lg border border-slate-200" id="view-remarks">No remarks provided.</p>
                    </div>
                </div>

                <!-- Audit History Timeline -->
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 mb-2 flex items-center gap-1.5">
                        <i class="bi bi-clock-history text-indigo-500"></i> Audit History Trail
                    </h4>
                    <div id="view-audit-logs" class="space-y-2 text-xs">
                        <div class="text-slate-400 text-center py-2">No history recorded</div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end shrink-0">
                <button type="button" onclick="closeViewModal()" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let table = null;
        let debounceTimer = null;
        let currentStatusFilter = '';
        let currentDistrictId = '';
        let currentProjectId = '';
        const displayZoneName = "{{ $displayZoneName }}";

        $(document).ready(function () {
            // Clean up any stale query parameters from URL so browser address bar remains clean
            if (window.location.search) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            initDataTable();
        });

        function onDistrictChange(districtId) {
            currentDistrictId = districtId || '';
            currentProjectId = ''; // reset project when district changes
            
            const projectSelect = $('#filter-project');
            projectSelect.prop('disabled', true).html('<option value="">Loading projects...</option>');

            $.ajax({
                url: "{{ route('ews.developer.possession.district-projects') }}",
                type: "GET",
                data: { district_id: currentDistrictId },
                success: function (res) {
                    projectSelect.prop('disabled', false);
                    let allLabel = currentDistrictId ? '-- All Projects in District --' : `-- All Projects (${displayZoneName}) --`;
                    let options = `<option value="" selected>${allLabel}</option>`;
                    if (res.projects && res.projects.length > 0) {
                        res.projects.forEach(function (p) {
                            const abbr = p.project_abbr ? ` [${p.project_abbr}]` : '';
                            options += `<option value="${p.id}">${p.name}${abbr}</option>`;
                        });
                    }
                    projectSelect.html(options);
                },
                error: function () {
                    projectSelect.prop('disabled', false);
                    projectSelect.html('<option value="">Error loading projects</option>');
                }
            });

            loadStats(currentDistrictId, currentProjectId);
            reloadDataTable();
        }

        function onProjectChange(projectId) {
            currentProjectId = projectId || '';
            loadStats(currentDistrictId, currentProjectId);
            reloadDataTable();
        }

        function loadStats(districtId, projectId) {
            $.ajax({
                url: "{{ route('ews.developer.possession.project-stats') }}",
                type: "GET",
                data: { district_id: districtId, project_id: projectId },
                success: function (res) {
                    if (res.success && res.stats) {
                        const s = res.stats;
                        $('#stat-total').text(Number(s.total_allotted).toLocaleString());
                        $('#stat-given').text(Number(s.possession_given).toLocaleString());
                        $('#stat-pending').text(Number(s.possession_pending).toLocaleString());
                        $('#stat-project-name').text(res.scope_name || 'All Assigned Projects');

                        let pct = 0;
                        if (s.total_allotted > 0) {
                            pct = ((s.possession_given / s.total_allotted) * 100).toFixed(1);
                        }
                        $('#stat-given-pct').text(`${pct}% Complete`);
                        $('#stat-pending-desc').text('Awaiting physical handover');

                        $('#count-all').text(s.total_allotted);
                        $('#count-pending').text(s.possession_pending);
                        $('#count-given').text(s.possession_given);

                        if (res.project_abbr) {
                            $('#project-abbr-text').text(res.project_abbr);
                            $('#project-abbr-badge').removeClass('hidden');
                        } else {
                            $('#project-abbr-badge').addClass('hidden');
                        }
                    }
                }
            });
        }

        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#beneficiaries-table')) {
                $('#beneficiaries-table').DataTable().destroy();
            }

            table = $('#beneficiaries-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('ews.developer.possession.beneficiaries-data') }}",
                    data: function (d) {
                        d.district_id = currentDistrictId;
                        d.project_id = currentProjectId;
                        d.block = $('#filter-block').val();
                        d.possession_status = currentStatusFilter;
                        d.search_keyword = $('#search-keyword').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-mono text-slate-400 font-semibold whitespace-nowrap' },
                    { 
                        data: 'application_number', 
                        name: 'application_number',
                        className: 'whitespace-nowrap',
                        render: function(data, type, row) {
                            return `<span class="font-mono font-bold text-slate-800">#${data}</span>`;
                        }
                    },
                    { 
                        data: 'full_name', 
                        name: 'full_name',
                        className: 'whitespace-nowrap',
                        render: function(data, type, row) {
                            return `<span class="font-bold text-slate-900 uppercase tracking-tight">${data}</span>`;
                        }
                    },
                    { 
                        data: 'mobile_number', 
                        name: 'mobile_number',
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            return `<span class="font-mono text-slate-600">${data || '-'}</span>`;
                        }
                    },
                    { 
                        data: 'flat_no', 
                        name: 'flat_no',
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            if (!data) return '<span class="text-slate-400 font-mono">-</span>';
                            return `<div class="whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-mono font-bold tracking-tight text-sky-800 bg-sky-50 border border-sky-200 shadow-xs whitespace-nowrap select-all">
                                    <i class="bi bi-door-closed-fill text-sky-500 text-[9px]"></i>
                                    <span>${data}</span>
                                </span>
                            </div>`;
                        }
                    },
                    { 
                        data: 'flat_breakdown', 
                        name: 'flat_breakdown',
                        orderable: false,
                        className: 'whitespace-nowrap',
                        render: function(data) {
                            if (!data) return '<span class="text-slate-400 font-mono">-</span>';
                            const floorHtml = `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200/80 text-slate-700 font-medium whitespace-nowrap text-[10.5px]">
                                <span class="text-[8.5px] text-slate-400 uppercase font-bold">Fl</span><strong class="font-bold text-slate-900">${data.floor || '-'}</strong>
                            </span>`;
                            
                            const blockHtml = (data.block && data.block !== '-' && data.block !== '') 
                                ? `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-indigo-50 border border-indigo-200/80 text-indigo-700 font-medium whitespace-nowrap text-[10.5px]">
                                    <span class="text-[8.5px] text-indigo-400 uppercase font-bold">Blk</span><strong class="font-bold text-indigo-900">${data.block}</strong>
                                </span>` 
                                : '';
                                
                            const unitHtml = `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-50 border border-amber-200/80 text-amber-800 font-medium whitespace-nowrap text-[10.5px]">
                                <span class="text-[8.5px] text-amber-500 uppercase font-bold">Unit</span><strong class="font-bold text-amber-900 font-mono">${data.unit || '-'}</strong>
                            </span>`;

                            return `<div class="inline-flex items-center gap-1 whitespace-nowrap">
                                ${floorHtml}
                                ${blockHtml}
                                ${unitHtml}
                            </div>`;
                        }
                    },
                    { data: 'possession_badge', name: 'possession_status', orderable: false, searchable: false, className: 'whitespace-nowrap' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'whitespace-nowrap' }
                ],
                order: [[1, 'asc']],
                drawCallback: function (settings) {
                    const info = table.page.info();
                    $('#table-record-count').text(`Showing ${info.start + 1} to ${info.end} of ${info.recordsTotal} Records`);
                },
                language: {
                    emptyTable: "No allotted beneficiaries found for this project.",
                    processing: '<div class="flex items-center justify-center p-4 text-sky-600"><i class="bi bi-arrow-repeat animate-spin text-xl mr-2"></i> Loading beneficiaries...</div>'
                }
            });
        }

        function reloadDataTable() {
            if (table) {
                table.ajax.reload(null, false);
            } else {
                initDataTable();
            }
        }

        function debounceReload() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                reloadDataTable();
            }, 300);
        }

        function filterByStatus(status) {
            currentStatusFilter = status;
            $('.status-btn').each(function() {
                const btnStatus = String($(this).data('status'));
                const isActive = (btnStatus === String(status));
                const badge = $(this).find('.count-badge');
                const icon = $(this).find('i');

                // Clear all active/inactive classes
                $(this).removeClass(
                    'bg-sky-600 bg-amber-500 bg-emerald-600 text-white shadow-md ring-2 ring-sky-300 ring-amber-300 ring-emerald-300 font-black scale-[1.02] ' +
                    'text-slate-600 hover:bg-slate-200/70 hover:text-slate-900 font-bold'
                );
                badge.removeClass('bg-white/25 text-white bg-slate-200 text-slate-700');
                icon.removeClass('text-white text-sky-500 text-amber-500 text-emerald-500');

                if (isActive) {
                    if (btnStatus === '') {
                        $(this).addClass('bg-sky-600 text-white shadow-md ring-2 ring-sky-300 font-black scale-[1.02]');
                        badge.addClass('bg-white/25 text-white');
                        icon.addClass('text-white');
                    } else if (btnStatus === 'PENDING') {
                        $(this).addClass('bg-amber-500 text-white shadow-md ring-2 ring-amber-300 font-black scale-[1.02]');
                        badge.addClass('bg-white/25 text-white');
                        icon.addClass('text-white');
                    } else if (btnStatus === 'GIVEN') {
                        $(this).addClass('bg-emerald-600 text-white shadow-md ring-2 ring-emerald-300 font-black scale-[1.02]');
                        badge.addClass('bg-white/25 text-white');
                        icon.addClass('text-white');
                    }
                } else {
                    $(this).addClass('text-slate-600 hover:bg-slate-200/70 hover:text-slate-900 font-bold');
                    badge.addClass('bg-slate-200 text-slate-700');
                    if (btnStatus === '') {
                        icon.addClass('text-sky-500');
                    } else if (btnStatus === 'PENDING') {
                        icon.addClass('text-amber-500');
                    } else if (btnStatus === 'GIVEN') {
                        icon.addClass('text-emerald-500');
                    }
                }
            });
            reloadDataTable();
        }

        // ==========================================
        // MODAL 1: SUBMIT / HANDOVER POSSESSION
        // ==========================================

        function openPossessionModal(beneficiaryId) {
            Swal.fire({
                title: 'Loading details...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ url('ews/developer/possession/beneficiary') }}/${beneficiaryId}",
                type: 'GET',
                success: function(response) {
                    Swal.close();
                    if (!response.success) {
                        Swal.fire('Error', response.message, 'error');
                        return;
                    }

                    const b = response.beneficiary;
                    const p = response.possession;

                    $('#modal-ben-id').val(b.id);
                    $('#modal-ben-name').text(b.full_name);
                    $('#modal-ben-app').text('#' + b.application_number);
                    $('#modal-ben-flat').text(b.flat_number);
                    $('#modal-ben-mobile').text(b.mobile_number || '-');

                    // Reset form
                    $('#possession-submit-form')[0].reset();
                    $('#photo-preview-container').addClass('hidden');
                    $('#pdf-size-feedback').text('');
                    $('#gps-status-feedback').text('');

                    if (p) {
                        // Populate existing values
                        if (p.possession_status === 'GIVEN') {
                            $('input[name="possession_status"][value="GIVEN"]').prop('checked', true);
                            toggleFormFields('GIVEN');
                        } else {
                            $('input[name="possession_status"][value="PENDING"]').prop('checked', true);
                            toggleFormFields('PENDING');
                        }

                        $('#input-latitude').val(p.latitude || '');
                        $('#input-longitude').val(p.longitude || '');
                        $('#input-remarks').val(p.remarks || '');

                        if (p.possession_letter_url) {
                            $('#existing-letter-preview').removeClass('hidden').find('a').attr('href', p.possession_letter_url);
                        } else {
                            $('#existing-letter-preview').addClass('hidden');
                        }

                        if (p.beneficiary_flat_photo_url) {
                            $('#photo-preview-img').attr('src', p.beneficiary_flat_photo_url);
                            $('#photo-preview-container').removeClass('hidden');
                        }
                    } else {
                        $('input[name="possession_status"][value="GIVEN"]').prop('checked', true);
                        toggleFormFields('GIVEN');
                        $('#existing-letter-preview').addClass('hidden');
                    }

                    $('#possessionModal').removeClass('hidden');

                    // Auto-detect live GPS coordinates if not already set
                    if (!$('#input-latitude').val() || !$('#input-longitude').val()) {
                        detectLocation();
                    }
                },
                error: function(err) {
                    Swal.fire('Error', 'Failed to fetch beneficiary details.', 'error');
                }
            });
        }

        function closePossessionModal() {
            $('#possessionModal').addClass('hidden');
        }

        function toggleFormFields(status) {
            if (status === 'GIVEN') {
                $('#given-fields-container').slideDown(200);
                $('#remarks-label').text('Handover Remarks');
                $('#remarks-required-star').addClass('hidden');
            } else {
                $('#given-fields-container').slideUp(200);
                $('#remarks-label').text('Reason for Pending Possession');
                $('#remarks-required-star').removeClass('hidden');
            }
        }

        function validatePdfSize(input) {
            const feedback = $('#pdf-size-feedback');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const sizeKb = (file.size / 1024).toFixed(1);

                if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
                    feedback.html('<span class="text-red-500 font-bold">⚠️ Invalid file: Only PDF files are permitted.</span>');
                    input.value = '';
                    return;
                }

                if (file.size > 500 * 1024) {
                    feedback.html(`<span class="text-red-500 font-bold">⚠️ File size (${sizeKb} KB) exceeds the maximum allowed 500 KB limit!</span>`);
                    input.value = '';
                    Swal.fire({
                        icon: 'warning',
                        title: 'PDF File Too Large',
                        text: `Your selected file is ${sizeKb} KB. Please upload a PDF under 500 KB as per government norms.`
                    });
                } else {
                    feedback.html(`<span class="text-emerald-600 font-bold">✓ Valid PDF (${sizeKb} KB / max 500 KB)</span>`);
                }
            } else {
                feedback.text('');
            }
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#photo-preview-img').attr('src', e.target.result);
                    $('#photo-preview-container').removeClass('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#photo-preview-container').addClass('hidden');
            }
        }

        function detectGPSLocation() {
            const feedback = $('#gps-status-feedback');
            if (!navigator.geolocation) {
                feedback.html('<span class="text-red-500">Geolocation is not supported by your browser.</span>');
                return;
            }

            feedback.html('<span class="text-sky-600 flex items-center gap-1"><i class="bi bi-arrow-repeat animate-spin"></i> Fetching device GPS location...</span>');

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(7);
                    const lng = position.coords.longitude.toFixed(7);
                    const acc = position.coords.accuracy ? position.coords.accuracy.toFixed(1) : '-';

                    $('#input-latitude').val(lat);
                    $('#input-longitude').val(lng);
                    feedback.html(`<span class="text-emerald-600 font-bold">✓ Location detected! Lat: ${lat}, Long: ${lng} (Accuracy: ~${acc}m)</span>`);
                },
                function(error) {
                    feedback.html(`<span class="text-amber-600">Location detection failed (${error.message}). You can type manually.</span>`);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        function handlePossessionSubmit(e) {
            e.preventDefault();
            const beneficiaryId = $('#modal-ben-id').val();
            const form = document.getElementById('possession-submit-form');
            const formData = new FormData(form);

            const btn = $('#btn-submit-possession');
            btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Saving...');

            $.ajax({
                url: "{{ url('ews/developer/possession/submit') }}/${beneficiaryId}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html('<i class="bi bi-shield-check"></i> Save & Submit Possession');
                    if (response.success) {
                        closePossessionModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        reloadDataTable();
                        // Update stats locally via AJAX
                        loadStats(currentDistrictId, currentProjectId);
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="bi bi-shield-check"></i> Save & Submit Possession');
                    let errMsg = 'Failed to submit possession.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Validation Error', errMsg, 'warning');
                }
            });
        }

        // ==========================================
        // MODAL 2: VIEW DETAILS & AUDIT LOGS
        // ==========================================

        function viewPossessionDetails(beneficiaryId) {
            Swal.fire({
                title: 'Loading details...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ url('ews/developer/possession/beneficiary') }}/${beneficiaryId}",
                type: 'GET',
                success: function(res) {
                    Swal.close();
                    if (!res.success) {
                        Swal.fire('Error', res.message, 'error');
                        return;
                    }

                    const b = res.beneficiary;
                    const p = res.possession;
                    const logs = res.audit_history || [];

                    $('#view-modal-subheading').text(`${b.full_name} | Flat: ${b.flat_number} (#${b.application_number})`);
                    $('#view-given-at').text(p && p.possession_given_at ? p.possession_given_at : (b.possession_given_at || 'Recorded'));
                    $('#view-remarks').text(p && p.remarks ? p.remarks : 'No remarks recorded.');
                    
                    if (p && p.latitude && p.longitude) {
                        $('#view-coords').text(`${p.latitude}, ${p.longitude}`);
                        $('#view-map-btn-container').html(`
                            <a href="https://www.google.com/maps?q=${p.latitude},${p.longitude}" target="_blank"
                                class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold transition-all flex items-center gap-1 shadow-sm">
                                <i class="bi bi-map-fill"></i> View On Google Maps
                            </a>
                        `);
                    } else {
                        $('#view-coords').text('Coordinates not recorded');
                        $('#view-map-btn-container').html('');
                    }

                    // Letter Preview
                    if (p && p.possession_letter_url) {
                        $('#view-letter-name').text(p.possession_letter_name || 'possession_letter.pdf');
                        $('#view-letter-action').html(`
                            <a href="${p.possession_letter_url}" target="_blank"
                                class="w-full py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg flex items-center justify-center gap-1.5 transition-all shadow-sm">
                                <i class="bi bi-file-earmark-pdf"></i> View / Download Signed Letter
                            </a>
                        `);
                    } else {
                        $('#view-letter-name').text('No letter uploaded');
                        $('#view-letter-action').html('');
                    }

                    // Photo Preview
                    if (p && p.beneficiary_flat_photo_url) {
                        $('#view-photo-container').html(`
                            <a href="${p.beneficiary_flat_photo_url}" target="_blank" class="block group relative overflow-hidden rounded-lg">
                                <img src="${p.beneficiary_flat_photo_url}" class="w-full h-32 object-cover rounded-lg border border-slate-300 transition-all group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition-all">
                                    <i class="bi bi-zoom-in mr-1"></i> Click to Enlarge
                                </div>
                            </a>
                        `);
                    } else {
                        $('#view-photo-container').html('<span class="text-slate-400 italic">No photo uploaded</span>');
                    }

                    // Audit Logs Timeline
                    let logsHtml = '';
                    if (logs.length > 0) {
                        logs.forEach(log => {
                            logsHtml += `
                                <div class="p-2.5 rounded-lg border border-slate-200 bg-white flex items-center justify-between">
                                    <div class="space-y-0.5">
                                        <div class="font-extrabold text-slate-800 flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>${log.action}</span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">${log.new_status}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-500">Performed by: <strong class="text-slate-700">${log.performed_by || 'STP Officer'}</strong></div>
                                    </div>
                                    <div class="text-right text-[10px] font-mono text-slate-400">
                                        <div>${log.timestamp}</div>
                                        <div>IP: ${log.ip_address || '-'}</div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        logsHtml = '<div class="text-slate-400 text-center py-2 italic">No audit history found.</div>';
                    }
                    $('#view-audit-logs').html(logsHtml);

                    $('#viewModal').removeClass('hidden');
                },
                error: function() {
                    Swal.fire('Error', 'Failed to load details.', 'error');
                }
            });
        }

        function closeViewModal() {
            $('#viewModal').addClass('hidden');
        }
    </script>
</body>
</html>
