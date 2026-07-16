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
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Registry Matrix</span>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                        <i class="bi bi-folder-fill text-sky-400"></i>
                        <span>Flats Registry</span>
                    </a>
                    <a href="{{ route('ews.developer.flats.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-plus-circle text-slate-400"></i>
                        <span>Register Flat</span>
                    </a>
                </div>
            </div>

            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Audit Trails</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-activity text-slate-400"></i>
                        <span>Developer logs</span>
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

    <!-- RIGHT CONTAINER WORKSPACE (Unified Header & Grid) -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <div class="md:hidden w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase">Registry Database Management</h2>
                    <p class="text-[8px] text-slate-455 font-mono uppercase">Developer Administration Sandbox</p>
                </div>
            </div>

            <!-- Profile context details -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-[10px] text-slate-650 font-bold">EWS Developer Team</div>
                    <div class="text-[8px] text-slate-400 font-mono">ID: #DEV_{{ substr(md5($user->id), 0, 6) }}</div>
                </div>
                <a href="{{ route('ews.developer.logout') }}" class="md:hidden px-3 py-1.5 bg-red-50 text-red-650 rounded-lg text-[9px] font-black uppercase border border-red-100">
                    Logout
                </a>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scroll">
            
            <!-- SECTION A: Registry Listing Card -->
            <section class="bg-white border border-slate-200/80 rounded-xl shadow-sm dev-shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <i class="bi bi-file-earmark-text text-sky-500 text-sm"></i>
                            EWS Flats Registry Database
                        </h3>
                        <p class="text-[8px] text-slate-400 font-mono mt-0.5 uppercase">Allotment proforma details under policy 23.10.2025</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 self-end sm:self-auto">
                        <!-- District Dropdown Filter -->
                        <div class="flex items-center gap-1.5 mr-2">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">District:</span>
                            <select id="filter-district" class="bg-white border border-slate-200 hover:border-slate-350 text-slate-700 text-[10px] font-bold rounded px-2.5 py-1 outline-none transition-all shadow-sm">
                                <option value="">ALL DISTRICTS</option>
                                @foreach($districts as $dist)
                                    <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-px h-5 bg-slate-200 mx-1"></div>

                        <!-- Export Actions -->
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">Export As:</span>
                        <a href="#" id="export-csv"
                            class="px-2.5 py-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold uppercase rounded text-[9px] shadow-sm flex items-center gap-1">
                            <i class="bi bi-file-earmark-spreadsheet text-emerald-600"></i>
                            <span>CSV</span>
                        </a>
                        <a href="#" id="export-pdf"
                            class="px-2.5 py-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold uppercase rounded text-[9px] shadow-sm flex items-center gap-1">
                            <i class="bi bi-file-pdf text-rose-600"></i>
                            <span>PDF</span>
                        </a>

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
            const table = $('#flats-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('ews.developer.flats.data') }}",
                    data: function (d) {
                        d.district_id = $('#filter-district').val();
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
