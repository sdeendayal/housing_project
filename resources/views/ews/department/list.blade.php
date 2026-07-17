<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Beneficiary List | Housing for All Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- jQuery & DataTables CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            overflow-y: auto;
        }

        /* Custom DataTables Styling overrides to match design guidelines */
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
            background: #ea580c !important;
            color: #ffffff !important;
            border-color: #ea580c !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important;
            color: #ea580c !important;
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
<body class="bg-[#f3f6fc] text-slate-800 min-h-screen flex">

    <!-- 1. Left Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-6 z-40 bg-[#1e293b] text-slate-300 shadow-xl border-r border-slate-800">
        <!-- Logo -->
        <div class="px-6 mb-6 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-tr from-orange-600 to-amber-500 rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-xl font-bold">business</span>
            </div>
            <div>
                <a href="{{ route('ews.department.dashboard') }}">
                    <h1 class="text-md font-extrabold text-white leading-tight">EWS DEPT</h1>
                    <p class="text-[9px] uppercase tracking-wider text-orange-400 font-bold">Housing Haryana</p>
                </a>
            </div>
        </div>

        <!-- Collapsible Structured Sidebar Navigation Links -->
        <nav class="flex-grow px-3 space-y-3.5 overflow-y-auto text-xs">
            
            <!-- Dashboard Link -->
            <a href="{{ route('ews.department.dashboard') }}" class="w-full flex items-center gap-3 rounded-lg px-4 py-2.5 hover:bg-slate-800 hover:text-white transition-all text-left font-bold">
                <span class="material-symbols-outlined text-base">dashboard</span>
                <span>Overview Dashboard</span>
            </a>

            <!-- Main Dropdown Toggle Button -->
            <button onclick="toggleFunnelSubmenu()" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left font-bold text-slate-350">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-base">filter_alt</span>
                    <span>EWS Registry Funnel</span>
                </div>
                <span id="submenu-arrow" class="material-symbols-outlined text-sm">keyboard_arrow_right</span>
            </button>

            <!-- Collapsible submenus wrapper -->
            <div id="funnel-submenus" class="hidden space-y-3.5 pl-2 border-l border-slate-700/60 ml-4 transition-all duration-300">
                <!-- Group 1: Registration Phase -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">1. Registration Phase</div>
                    <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'registered' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">list_alt</span>
                            <span>Registered</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($registeredCount) }}</span>
                    </a>
                </div>

                <!-- Group 2: Eligibility Rejections -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">2. Eligibility Rejections</div>
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'rejected_ppp' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">cancel</span>
                            <span>PPP Exclusion</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedPppCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'rejected_property' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">domain_disabled</span>
                            <span>Property in India</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedPropertyCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'rejected_ownership' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">home_work</span>
                            <span>House Ownership</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedOwnershipCount) }}</span>
                    </a>
                </div>

                <!-- Group 3: Verification Visited/Absent -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">3. Verification Visited/Absent</div>
                    <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'eligible_draw' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">how_to_reg</span>
                            <span>Eligible for Draw</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($eligibleDrawCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'booking' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            <span>Visited</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($bookingCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'not_visited' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">warning</span>
                            <span>Absent</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($notVisitedCount) }}</span>
                    </a>
                </div>

                <!-- Group 4: ADC Verification Outcomes -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">4. ADC Verification Outcomes</div>
                    <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'adc_passed' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">check_circle_outline</span>
                            <span>Passed</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($adcPassedCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'adc_failed' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">error_outline</span>
                            <span>Failed</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($adcFailedCount) }}</span>
                    </a>
                </div>

                <!-- Group 5: Final Draw Allotment -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">5. Final Draw Allotment</div>
                    <a href="{{ route('ews.department.list', ['type' => 'all', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'all' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">groups</span>
                            <span>Total Beneficiaries</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($totalCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'allotted' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            <span>Allotted</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($allottedCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'pending' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">hourglass_empty</span>
                            <span>Pending</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($pendingCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ $type === 'draw_remaining' ? 'bg-orange-600 text-white font-bold' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">hourglass_disabled</span>
                            <span>Unallotted Draw</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($drawRemainingCount) }}</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="mt-auto px-6 pt-4 border-t border-slate-800">
            <div class="mb-4 px-2">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Active Scheme</p>
                <p class="text-xs font-bold text-orange-400 uppercase mt-0.5">EWS HOUSING</p>
            </div>
            <a href="{{ route('ews.department.logout') }}" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-rose-500 hover:bg-rose-950/30 hover:text-rose-400 transition-all font-bold">
                <span class="material-symbols-outlined text-base">logout</span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px]">
        
        <!-- Top Header / Navbar -->
        <header class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-6 bg-white shadow-sm border-b border-slate-200">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.department.dashboard', ['district_id' => $districtId]) }}" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-700 transition mr-2">
                    <span class="material-symbols-outlined text-md">arrow_back</span>
                    <span class="text-xs font-bold uppercase">Back to Overview</span>
                </a>
                <div class="h-5 w-[1px] bg-slate-200"></div>
                <span class="text-xs text-slate-500 font-medium">EWS Beneficiary Registry Database</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-700">{{ $user->name }}</p>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase">EWS Administrator</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-sm">
                    EW
                </div>
            </div>
        </header>

        <!-- Content Body Wrapper -->
        <main class="mt-16 p-4 flex-grow flex flex-col">

            <!-- Datatables Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-150 p-6 flex flex-col flex-grow min-h-[500px]">
                <div class="pb-3.5 border-b border-slate-100 mb-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-orange-600 text-lg font-bold">badge</span>
                            <span id="table-title">Beneficiaries List Database</span>
                        </h3>
                        <p class="text-[9px] text-slate-400 uppercase font-semibold">Active server-side datatable listings showing registration details</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <select id="district-filter" onchange="filterListByDistrict(this.value)" class="bg-[#f8fafc] border border-slate-200 rounded-lg px-2.5 py-1.5 text-[9.5px] font-extrabold text-slate-705 focus:outline-none focus:border-orange-500 transition shadow-sm cursor-pointer min-w-[160px]">
                            <option value="">ALL DISTRICTS</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ $districtId == $district->id ? 'selected' : '' }}>
                                    {{ strtoupper($district->name) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="bg-slate-100 text-slate-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                            Type: {{ $type }}
                        </div>
                    </div>
                </div>

                <!-- Yajra DataTable Container -->
                <div class="flex-grow overflow-x-auto pr-1">
                    <table class="w-full text-left border-collapse" id="beneficiary-table">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                                <th style="width: 5%;">S.No.</th>
                                <th>Application Number</th>
                                <th>Full Name</th>
                                <th>District</th>
                                <th>Aadhar Number</th>
                                <th>Mobile Number</th>
                                <th>Flat Number</th>
                                <th>Status</th>
                                <th style="text-align: right; width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            <!-- Populated dynamically by Ajax Datatables -->
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- DataTables JS Logic -->
    <script>
        let currentType = "{{ $type }}";
        let table;

        $(document).ready(function() {
            // Update title text
            let titleText = 'All Beneficiaries List';
            if (currentType === 'registered') titleText = 'Registered Applications List';
            if (currentType === 'allotted') titleText = 'Allotted Beneficiaries List';
            if (currentType === 'pending') titleText = 'Pending Beneficiaries List';
            if (currentType === 'rejected_ppp') titleText = 'PPP Exclusion (Rejected) List';
            if (currentType === 'rejected_property') titleText = 'Property in India (Rejected) List';
            if (currentType === 'rejected_ownership') titleText = 'House Ownership (Rejected) List';
            if (currentType === 'eligible_draw') titleText = 'Eligible for Draw List';
            if (currentType === 'booking') titleText = 'Verification Completed (Visited) List';
            if (currentType === 'not_visited') titleText = 'Verification Pending (Not Visited) List';
            if (currentType === 'adc_passed') titleText = 'ADC Verification (Passed) List';
            if (currentType === 'adc_failed') titleText = 'ADC Verification (Failed) List';
            if (currentType === 'draw_remaining') titleText = 'Unallotted Draw (Remaining Eligible) List';
            $('#table-title').text(titleText);

            table = $('#beneficiary-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('ews.department.beneficiary.data') }}",
                    data: function (d) {
                        d.type = currentType;
                        d.district_id = "{{ $districtId }}";
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'application_number', name: 'application_number', class: 'font-mono uppercase font-bold text-slate-500 text-[10px]' },
                    { data: 'full_name', name: 'full_name', class: 'font-bold text-slate-800 uppercase' },
                    { data: 'dist_name', name: 'dist_name', class: 'uppercase font-bold text-slate-600' },
                    { data: 'aadhar_no', name: 'aadhar_no', class: 'font-mono text-slate-500' },
                    { data: 'mobile_number', name: 'mobile_number', class: 'font-mono text-slate-700' },
                    { data: 'flat_no', name: 'flat_no', class: 'text-orange-600 font-mono font-bold' },
                    { 
                        data: 'status', 
                        name: 'status',
                        render: function (data, type, row) {
                            if (data === 'Allotted') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-[9px] font-black uppercase text-emerald-700 tracking-wide border border-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Allotted</span>';
                            } else if (data === 'Pending') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-55/15 text-[9px] font-black uppercase text-amber-700 tracking-wide border border-amber-250"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>';
                            } else if (data === 'Rejected') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-[9px] font-black uppercase text-rose-700 tracking-wide border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Rejected</span>';
                            } else if (data === 'Eligible') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 text-[9px] font-black uppercase text-indigo-700 tracking-wide border border-indigo-200"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>Eligible</span>';
                            } else if (data === 'Visited') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-[9px] font-black uppercase text-emerald-700 tracking-wide border border-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Visited</span>';
                            } else if (data === 'Not Visited') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-55/15 text-[9px] font-black uppercase text-amber-700 tracking-wide border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Not Visited</span>';
                            } else if (data === 'Passed') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-[9px] font-black uppercase text-emerald-700 tracking-wide border border-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Passed</span>';
                            } else if (data === 'Failed') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-[9px] font-black uppercase text-rose-700 tracking-wide border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Failed</span>';
                            } else if (data === 'Unallotted') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-50 text-[9px] font-black uppercase text-slate-700 tracking-wide border border-slate-200"><span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>Unallotted</span>';
                            } else {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-[9px] font-black uppercase text-blue-700 tracking-wide border border-blue-200"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Registered</span>';
                            }
                        }
                    },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-right' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search beneficiaries...",
                    processing: '<div class="flex items-center justify-center p-2 text-orange-600 font-bold text-[10px]"><i class="bi bi-arrow-repeat animate-spin mr-1"></i> Fetching registry...</div>'
                },
            });

            if (currentType && currentType !== 'all') {
                toggleFunnelSubmenu();
            }
        });

        function toggleFunnelSubmenu() {
            const container = document.getElementById('funnel-submenus');
            const arrow = document.getElementById('submenu-arrow');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                arrow.textContent = 'keyboard_arrow_down';
            } else {
                container.classList.add('hidden');
                arrow.textContent = 'keyboard_arrow_right';
            }
        }

        function filterListByDistrict(districtId) {
            let url = new URL(window.location.href);
            if (districtId) {
                url.searchParams.set('district_id', districtId);
            } else {
                url.searchParams.delete('district_id');
            }
            window.location.href = url.toString();
        }
    </script>
</body>
</html>
