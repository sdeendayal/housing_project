<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Flat Form Submissions | EWS Department</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- jQuery, DataTables & SweetAlert2 CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        table.dataTable { border-bottom: 1px solid #e2e8f0 !important; margin-top: 15px !important; margin-bottom: 15px !important; }
        table.dataTable thead th { background-color: #f8fafc !important; color: #1e293b !important; font-weight: 800 !important; text-transform: uppercase !important; font-size: 9px !important; letter-spacing: 0.05em !important; border-bottom: 1px solid #cbd5e1 !important; padding: 12px 10px !important; }
        table.dataTable tbody td { font-size: 11px !important; padding: 12px 10px !important; color: #334155 !important; border-bottom: 1px solid #f1f5f9 !important; }
        table.dataTable tbody tr:hover { background-color: #f8fafc !important; }
        .dataTables_wrapper .dataTables_length select { background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 20px 4px 8px; font-size: 10px; font-weight: bold; outline: none; }
        .dataTables_wrapper .dataTables_filter input { background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 5px 10px; font-size: 10px; font-weight: 500; outline: none; margin-left: 8px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #d97706 !important; color: #ffffff !important; border-color: #d97706 !important; }
    </style>
</head>
<body class="bg-[#f3f6fc] text-slate-800 min-h-screen flex">

    <!-- 1. Left Sidebar -->
    @include('ews.department.partials.sidebar')

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px]">
        
        <!-- Header -->
        <header class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-6 bg-white shadow-sm border-b border-slate-200">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.department.dashboard') }}" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-700 transition mr-2">
                    <span class="material-symbols-outlined text-md">arrow_back</span>
                    <span class="text-xs font-bold uppercase">Dashboard</span>
                </a>
                <div class="h-5 w-[1px] bg-slate-200"></div>
                <span class="text-xs text-slate-500 font-medium">EWS Developer Form Submissions & Builder Flats Registry</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-700">{{ $user->name }}</p>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase">EWS Administrator</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                    EW
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mt-16 p-6 flex-grow flex flex-col gap-6">

            <!-- Datatable Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-150 p-6 flex flex-col flex-grow">
                <div class="pb-4 border-b border-slate-100 mb-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-600 text-lg">apartment</span>
                            <span>Developer Flat Form Submissions</span>
                        </h3>
                        <p class="text-[9px] text-slate-400 font-semibold uppercase mt-0.5">Showing all flat records entered by developers across Haryana districts</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Export Action Buttons -->
                        <div class="flex items-center gap-1.5 bg-slate-50 p-1 rounded-xl border border-slate-200">
                            <button type="button" onclick="exportData('excel')" class="px-2.5 py-1 bg-white hover:bg-emerald-600 hover:text-white text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-bold transition flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-sm">table_view</span>
                                <span>Excel</span>
                            </button>
                            <button type="button" onclick="exportData('csv')" class="px-2.5 py-1 bg-white hover:bg-sky-600 hover:text-white text-sky-700 border border-sky-200 rounded-lg text-[10px] font-bold transition flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-sm">csv</span>
                                <span>CSV</span>
                            </button>
                            <button type="button" onclick="exportData('pdf')" class="px-2.5 py-1 bg-white hover:bg-rose-600 hover:text-white text-rose-700 border border-rose-200 rounded-lg text-[10px] font-bold transition flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                <span>PDF</span>
                            </button>
                        </div>

                        <select id="district-filter" onchange="filterFlats(this.value)" class="bg-[#f8fafc] border border-slate-200 rounded-lg px-3 py-1.5 text-[10px] font-extrabold text-slate-700 focus:outline-none focus:border-amber-500 min-w-[170px]">
                            <option value="">ALL DISTRICTS</option>
                            @foreach($districts as $dist)
                                <option value="{{ $dist->id }}">{{ strtoupper($dist->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex-grow overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="flats-table">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                                <th style="width: 5%;">S.No.</th>
                                <th>District</th>
                                <th>Town Name</th>
                                <th>Project Name</th>
                                <th>Block / Tower</th>
                                <th>Floor</th>
                                <th>Flat Number</th>
                                <th>Unique Flat Code</th>
                                <th>Submitted By Developer</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- DataTables Script -->
    <script>
        let flatsTable;
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#flats-table').on('error.dt', function (e, settings, techNote, message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Load Warning',
                    text: 'Unable to fetch flat submissions data. Please refresh or try again.',
                    confirmButtonColor: '#d97706',
                    customClass: { popup: 'rounded-2xl font-sans' }
                });
            });

            flatsTable = $('#flats-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                lengthMenu: [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                ajax: {
                    url: "{{ route('ews.department.developer-flats.data') }}",
                    data: function(d) {
                        d.district_id = $('#district-filter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'district_name', name: 'district_name', class: 'uppercase font-bold text-slate-600' },
                    { data: 'town_name', name: 'town_name', class: 'uppercase font-semibold text-slate-700' },
                    { data: 'project_name', name: 'project_name', class: 'font-bold text-slate-800 uppercase' },
                    { data: 'block_tower_number', name: 'block_tower_number', class: 'font-mono text-slate-600 font-bold' },
                    { data: 'floor', name: 'floor', class: 'font-bold text-slate-700' },
                    { data: 'flat_number', name: 'flat_number', class: 'font-mono font-bold text-amber-700' },
                    { data: 'flat_code', name: 'flat_code', class: 'font-mono font-bold text-emerald-700' },
                    { data: 'created_by_info', name: 'created_by_info', orderable: false, searchable: false }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search project, block, flat...",
                }
            });
        });

        function filterFlats(districtId) {
            flatsTable.ajax.reload();
        }

        function exportData(format) {
            let search = flatsTable ? flatsTable.search() : '';
            let districtId = $('#district-filter').val();
            let url = new URL("{{ route('ews.department.export.developer-flats') }}");
            
            url.searchParams.set('format', format);
            if (districtId) url.searchParams.set('district_id', districtId);
            if (search) url.searchParams.set('search', search);

            if (format === 'pdf') {
                window.open(url.toString(), '_blank');
                return;
            }

            Swal.fire({
                icon: 'info',
                title: 'Export Started',
                text: 'Your ' + format.toUpperCase() + ' download is starting natively in the background.',
                timer: 3500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                customClass: { popup: 'rounded-xl font-sans' }
            });

            // Trigger direct native browser download
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url.toString();
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
    @include('partials.global-toast')
</body>
</html>
