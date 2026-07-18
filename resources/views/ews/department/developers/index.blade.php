<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Accounts Management | EWS Department</title>
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
                <span class="text-xs text-slate-500 font-medium">EWS Developers Account Management & CRUD</span>
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

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-xs font-bold shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-bold shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-rose-600">error</span>
                        <span>Please fix the following validation errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-[11px] space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header Action Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-150 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-xl">engineering</span>
                        <span>Developer Accounts Management</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Create, update, and manage EWS developer login credentials and access.</p>
                </div>
                <button type="button" onclick="openAddModal()" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    <span>Add New Developer</span>
                </button>
            </div>

            <!-- Datatable Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-150 p-6 flex flex-col flex-grow">
                <div class="pb-4 border-b border-slate-100 mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-lg">manage_accounts</span>
                        <span>All Developer Accounts</span>
                    </h3>

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
                </div>

                <div class="flex-grow overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="developers-table">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                                <th style="width: 5%;">S.No.</th>
                                <th>Developer / Team Name</th>
                                <th>Mobile Number (Login ID)</th>
                                <th>Email Address</th>
                                <th>District</th>
                                <th>Flat Submissions</th>
                                <th>Status</th>
                                <th style="text-align: right; width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- ADD DEVELOPER MODAL -->
    <div id="add-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-150 w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-150">
            <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400">person_add</span>
                    <h3 class="text-sm font-black uppercase tracking-wider">Add New Developer</h3>
                </div>
                <button type="button" onclick="closeAddModal()" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('ews.department.developers.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Developer / Team Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Acme Infra Developer" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Mobile Number (Login Mobile ID) *</label>
                    <input type="text" name="mobile" maxlength="10" required placeholder="10-digit mobile number" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold font-mono focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="developer@example.com" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Assigned District</label>
                    <select name="district_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                        @foreach($districts as $dist)
                            <option value="{{ $dist->name }}">{{ strtoupper($dist->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Password *</label>
                    <input type="password" name="password" required minlength="6" placeholder="Account Password" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Account Status</label>
                    <select name="Is_Active" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition cursor-pointer">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-xs font-bold rounded-lg hover:bg-amber-700 transition cursor-pointer">Create Developer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT DEVELOPER MODAL -->
    <div id="edit-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-150 w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-150">
            <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400">edit_note</span>
                    <h3 class="text-sm font-black uppercase tracking-wider">Edit Developer Account</h3>
                </div>
                <button type="button" onclick="closeEditModal()" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form id="edit-form" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Developer / Team Name *</label>
                    <input type="text" id="edit-name" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Mobile Number (Login Mobile ID) *</label>
                    <input type="text" id="edit-mobile" name="mobile" maxlength="10" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold font-mono focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Email Address *</label>
                    <input type="email" id="edit-email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Assigned District</label>
                    <select id="edit-district_name" name="district_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                        @foreach($districts as $dist)
                            <option value="{{ $dist->name }}">{{ strtoupper($dist->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Password (Leave blank to keep current)</label>
                    <input type="password" name="password" minlength="6" placeholder="New Password" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider mb-1">Account Status</label>
                    <select id="edit-Is_Active" name="Is_Active" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-bold focus:outline-none focus:border-amber-500">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition cursor-pointer">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-xs font-bold rounded-lg hover:bg-amber-700 transition cursor-pointer">Update Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTables Script -->
    <script>
        let devTable;
        $(document).ready(function() {
            devTable = $('#developers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ews.department.developers.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name', class: 'font-bold text-slate-800 uppercase' },
                    { data: 'mobile', name: 'mobile', class: 'font-mono font-bold text-amber-700' },
                    { data: 'email', name: 'email', class: 'font-mono text-slate-600' },
                    { data: 'district_name', name: 'district_name', class: 'uppercase font-bold text-slate-600' },
                    { data: 'flats_count', name: 'flats_count', class: 'font-mono font-bold text-slate-700' },
                    { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, class: 'text-right' }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search developers...",
                }
            });
        });

        function exportData(format) {
            let search = devTable ? devTable.search() : '';
            let url = new URL("{{ route('ews.department.export.developers') }}");
            url.searchParams.set('format', format);
            if (search) url.searchParams.set('search', search);

            if (format === 'pdf') {
                window.open(url.toString(), '_blank');
                return;
            }

            Swal.fire({
                title: 'Generating ' + format.toUpperCase() + ' Export...',
                html: '<div class="text-xs text-slate-500 font-medium">Please wait while developer records are prepared for download.</div>',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(url.toString())
                .then(response => {
                    if (!response.ok) throw new Error('Export failed.');
                    return response.blob();
                })
                .then(blob => {
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = downloadUrl;
                    a.download = 'ews_developers_' + (format === 'excel' ? 'excel.csv' : 'csv');
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(downloadUrl);
                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Downloaded!',
                        text: 'Developer list saved successfully.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({ icon: 'error', title: 'Export Failed', text: 'Error generating file.', confirmButtonColor: '#ef4444' });
                });
        }

        function openAddModal() {
            const m = document.getElementById('add-modal');
            m.classList.remove('hidden');
            m.style.display = 'flex';
        }
        function closeAddModal() {
            const m = document.getElementById('add-modal');
            m.classList.add('hidden');
            m.style.display = 'none';
        }

        function openEditModal(data) {
            document.getElementById('edit-form').action = "/ews/department/developers/" + (data.secure_id || data.id);
            document.getElementById('edit-name').value = data.name;
            document.getElementById('edit-mobile').value = data.mobile;
            document.getElementById('edit-email').value = data.email;
            document.getElementById('edit-district_name').value = data.district_name || '';
            document.getElementById('edit-Is_Active').value = data.Is_Active;
            
            const m = document.getElementById('edit-modal');
            m.classList.remove('hidden');
            m.style.display = 'flex';
        }
        function closeEditModal() {
            const m = document.getElementById('edit-modal');
            m.classList.add('hidden');
            m.style.display = 'none';
        }

        // Close modal when clicking outside content area
        window.addEventListener('click', function(event) {
            const addModal = document.getElementById('add-modal');
            const editModal = document.getElementById('edit-modal');
            if (event.target === addModal) closeAddModal();
            if (event.target === editModal) closeEditModal();
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });

        function confirmDelete(secureId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this developer account?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-2xl font-sans',
                    confirmButton: 'px-4 py-2 font-bold text-xs rounded-xl',
                    cancelButton: 'px-4 py-2 font-bold text-xs rounded-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + secureId).submit();
                }
            });
        }
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl font-sans' }
                });
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: '{!! implode("<br>", $errors->all()) !!}',
                    confirmButtonColor: '#ef4444',
                    customClass: { popup: 'rounded-2xl font-sans' }
                });
            });
        </script>
    @endif
    @include('partials.global-toast')
</body>
</html>
