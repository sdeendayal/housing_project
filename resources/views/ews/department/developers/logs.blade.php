<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Activity Logs | EWS Department</title>
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
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #059669 !important; color: #ffffff !important; border-color: #059669 !important; }
    </style>
</head>
<body class="bg-[#f1f8f4] text-slate-800 h-screen flex overflow-hidden">

    <!-- 1. Left Sidebar -->
    @include('ews.department.partials.sidebar')

    <!-- 2. Main Page Area -->
    <div class="flex-1 min-w-0 flex flex-col ml-[260px] h-screen overflow-hidden">
        
        <!-- Header -->
        <header class="sticky top-0 w-full z-40 h-16 flex justify-between items-center px-6 bg-white shadow-sm border-b border-emerald-100 shrink-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.department.dashboard') }}" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-700 transition mr-2">
                    <span class="material-symbols-outlined text-md">arrow_back</span>
                    <span class="text-xs font-bold uppercase">Dashboard</span>
                </a>
                <div class="h-5 w-[1px] bg-slate-200"></div>
                <span class="text-xs text-slate-500 font-medium">EWS Developer Activity & System Audit Logs</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-700">{{ $user->name }}</p>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase">EWS Administrator</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm shadow-inner">
                    EW
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6 flex-grow flex flex-col gap-6 min-w-0 overflow-y-auto">

            <!-- Datatable Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-150 p-6 flex flex-col flex-grow">
                <div class="pb-4 border-b border-slate-100 mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-lg">receipt_long</span>
                            <span>Developer Activity Audit Logs</span>
                        </h3>
                        <p class="text-[9px] text-slate-400 font-semibold uppercase mt-0.5">Real-time log of form creations, updates, deletions, and logins by developer accounts</p>
                    </div>

                    <!-- Export Action Buttons -->
                    <div class="flex items-center gap-1.5 bg-slate-50 p-1 rounded-xl border border-slate-200">
                        <button type="button" onclick="exportData('excel')" class="px-2.5 py-1 bg-white hover:bg-emerald-600 hover:text-white text-emerald-705 border border-emerald-200 rounded-lg text-[10px] font-bold transition flex items-center gap-1 shadow-sm">
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
                </div>

                <div class="flex-grow overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="logs-table">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                                <th style="width: 5%;">S.No.</th>
                                <th>Developer Name & Mobile</th>
                                <th>Action Performed</th>
                                <th>Action Details</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
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
        let logsTable;
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#logs-table').on('error.dt', function (e, settings, techNote, message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Load Warning',
                    text: 'Unable to fetch activity logs. Please refresh or try again.',
                    confirmButtonColor: '#059669',
                    customClass: { popup: 'rounded-2xl font-sans' }
                });
            });

            logsTable = $('#logs-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                lengthMenu: [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                ajax: "{{ route('ews.department.developer-logs.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'developer_info', name: 'developer_info', orderable: false, searchable: false },
                    { data: 'action_badge', name: 'action_badge', orderable: false, searchable: false },
                    { data: 'details', name: 'details', class: 'font-medium text-slate-700' },
                    { data: 'ip_address', name: 'ip_address', class: 'font-mono text-slate-500 text-[10px]' },
                    { data: 'created_at', name: 'created_at', class: 'font-mono text-slate-500 text-[10px]' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search logs, action, IP...",
                }
            });
        });

        function exportData(format) {
            let search = logsTable ? logsTable.search() : '';
            let url = new URL("{{ route('ews.department.export.developer-logs') }}");
            
            url.searchParams.set('format', format);
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
