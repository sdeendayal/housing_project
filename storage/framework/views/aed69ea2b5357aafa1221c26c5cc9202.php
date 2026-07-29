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
    <!-- jQuery, DataTables & SweetAlert2 CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
    <?php echo $__env->make('ews.department.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px]">
        
        <!-- Top Header / Navbar -->
        <header class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-6 bg-white shadow-sm border-b border-slate-200">
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('ews.department.dashboard', ['district_id' => $districtId])); ?>" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-700 transition mr-2">
                    <span class="material-symbols-outlined text-md">arrow_back</span>
                    <span class="text-xs font-bold uppercase">Back to Overview</span>
                </a>
                <div class="h-5 w-[1px] bg-slate-200"></div>
                <span class="text-xs text-slate-500 font-medium">EWS Beneficiary Registry Database</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-700"><?php echo e($user->name); ?></p>
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
                    <div class="flex items-center gap-3 flex-wrap">
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

                        <select id="district-filter" onchange="filterListByDistrict(this.value)" class="bg-[#f8fafc] border border-slate-200 rounded-lg px-2.5 py-1.5 text-[9.5px] font-extrabold text-slate-705 focus:outline-none focus:border-orange-500 transition shadow-sm cursor-pointer min-w-[150px]">
                            <option value="">ALL DISTRICTS</option>
                            <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($district->id); ?>" <?php echo e($districtId == $district->id ? 'selected' : ''); ?>>
                                    <?php echo e(strtoupper($district->name)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="bg-slate-100 text-slate-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider">
                            Type: <?php echo e($type); ?>

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
                                <th>Mobile Number</th>
                                <?php if($type === 'allotted'): ?>
                                    <th>Flat Number</th>
                                <?php endif; ?>
                                <?php if($type !== 'ppt_members' && $type !== 'not_in_survey'): ?>
                                    <th>Status</th>
                                <?php endif; ?>
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
        let currentType = "<?php echo e($type); ?>";
        let table;

        $(document).ready(function() {
            // Update title text
            let titleText = 'All Beneficiaries List';
            if (currentType === 'ppt_members') titleText = 'Total registration List';
            if (currentType === 'registered') titleText = 'Verify in survey app List';
            if (currentType === 'not_in_survey') titleText = 'Rejected in survey app List';
            if (currentType === 'allotted') titleText = 'Allotted Beneficiaries List';
            if (currentType === 'pending') titleText = 'Waiting Beneficiaries List';
            if (currentType === 'rejected_ppp') titleText = 'PPP Exclusion (Rejected) List';
            if (currentType === 'rejected_property') titleText = 'Property in India (Rejected) List';
            if (currentType === 'rejected_ownership') titleText = 'House Ownership (Rejected) List';
            if (currentType === 'eligible_draw') titleText = 'Eligible for booking List';
            if (currentType === 'booking') titleText = 'Booking amount received List';
            if (currentType === 'not_visited') titleText = 'Booking amount not received List';
            if (currentType === 'adc_passed') titleText = 'ADC Eligibility (Eligible) List';
            if (currentType === 'adc_failed') titleText = 'ADC Eligibility (Not Eligible) List';
            if (currentType === 'draw_remaining') titleText = 'Unallotted Draw (Remaining Eligible) List';
            $('#table-title').text(titleText);

            let columnsConfig = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'application_number', name: 'application_number', class: 'font-mono uppercase font-bold text-slate-500 text-[10px]' },
                { data: 'full_name', name: 'full_name', class: 'font-bold text-slate-800 uppercase' },
                { data: 'dist_name', name: 'dist_name', class: 'uppercase font-bold text-slate-600' },
                { data: 'mobile_number', name: 'mobile_number', class: 'font-mono text-slate-700' }
            ];

            if (currentType === 'allotted') {
                columnsConfig.push({ data: 'flat_no', name: 'flat_no', class: 'text-orange-600 font-mono font-bold' });
            }

            if (currentType !== 'ppt_members' && currentType !== 'not_in_survey') {
                columnsConfig.push(
                    { 
                        data: 'status', 
                        name: 'status',
                        render: function (data, type, row) {
                            if (data === 'Allotted') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-[9px] font-black uppercase text-emerald-700 tracking-wide border border-emerald-100 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Allotted</span>';
                            } else if (data === 'Pending' || data === 'Waiting' || data === 'Waiting Beneficiaries') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-55/15 text-[9px] font-black uppercase text-amber-700 tracking-wide border border-amber-250 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Waiting</span>';
                            } else if (data === 'Rejected') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-[9px] font-black uppercase text-rose-700 tracking-wide border border-rose-200 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Rejected</span>';
                            } else if (data === 'Eligible' || data === 'Eligible for booking') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 text-[9px] font-black uppercase text-indigo-700 tracking-wide border border-indigo-200 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>Eligible for booking</span>';
                            } else if (data === 'Visited' || data === 'Booking Amount Received') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-[9px] font-black uppercase text-emerald-700 tracking-wide border border-emerald-100 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Booking amount received</span>';
                            } else if (data === 'Not Visited' || data === 'Booking Amount Not Received') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-55/15 text-[9px] font-black uppercase text-amber-700 tracking-wide border border-amber-200 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Booking amount not received</span>';
                            } else if (data === 'Passed' || data === 'Eligible') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-[9px] font-black uppercase text-emerald-700 tracking-wide border border-emerald-100 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Eligible</span>';
                            } else if (data === 'Failed' || data === 'Not Eligible') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-[9px] font-black uppercase text-rose-700 tracking-wide border border-rose-200 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Not Eligible</span>';
                            } else if (data === 'Unallotted') {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-50 text-[9px] font-black uppercase text-slate-700 tracking-wide border border-slate-200 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>Unallotted</span>';
                            } else {
                                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-[9px] font-black uppercase text-blue-700 tracking-wide border border-blue-200 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Verify in survey app</span>';
                            }
                        }
                    }
                );
            }

            columnsConfig.push(
                { data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-right' }
            );

            $.fn.dataTable.ext.errMode = 'none';
            $('#beneficiary-table').on('error.dt', function (e, settings, techNote, message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Load Warning',
                    text: 'Unable to fetch beneficiary data. Please refresh or try again.',
                    confirmButtonColor: '#ea580c',
                    customClass: { popup: 'rounded-2xl font-sans' }
                });
            });

            table = $('#beneficiary-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                lengthMenu: [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                ajax: {
                    url: "<?php echo e(route('ews.department.beneficiary.data')); ?>",
                    data: function (d) {
                        d.type = currentType;
                        d.district_id = "<?php echo e($districtId); ?>";
                    }
                },
                columns: columnsConfig,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search beneficiaries...",
                    processing: '<div class="flex items-center justify-center p-2 text-orange-600 font-bold text-[10px]"><i class="bi bi-arrow-repeat animate-spin mr-1"></i> Fetching registry...</div>'
                },
            });
        });

        function filterListByDistrict(districtId) {
            let url = new URL(window.location.href);
            if (districtId) {
                url.searchParams.set('district_id', districtId);
            } else {
                url.searchParams.delete('district_id');
            }
            window.location.href = url.toString();
        }

        function exportData(format) {
            let search = table ? table.search() : '';
            let url = new URL("<?php echo e(route('ews.department.export.beneficiaries')); ?>");
            
            url.searchParams.set('format', format);
            url.searchParams.set('type', currentType);
            if ("<?php echo e($districtId); ?>") url.searchParams.set('district_id', "<?php echo e($districtId); ?>");
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
    <?php echo $__env->make('partials.global-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH E:\sports\housing_project\resources\views/ews/department/list.blade.php ENDPATH**/ ?>