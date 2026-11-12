<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS STP - Master Flat Matrix | {{ $licenseNo ?? 'Verification' }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'Fira Code', monospace; }
        .dev-shadow { box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08); }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @media print {
            body { background: white !important; }
            aside, header, #action-toolbar, #btn-back-top, .no-print { display: none !important; }
            .print-clean { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
            #printable-area { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    @include('ews.stp.partials.sidebar')

    <!-- WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.stp.verify') }}" class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 hover:bg-sky-600 hover:text-white transition-all shadow-sm" title="Back to Verification Console">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-400">
                        <a href="{{ route('ews.stp.verify') }}" class="hover:text-sky-600 transition-colors">STP Verify Console</a>
                        <span>/</span>
                        <span class="text-slate-600">License Matrix</span>
                        <span>/</span>
                        <span class="font-mono text-emerald-700 font-black">{{ $licenseNo }}</span>
                    </div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase mt-0.5 flex items-center gap-2">
                        <span>Verified Master Flat Matrix View</span>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-[8.5px] font-mono font-bold">
                            {{ $flats->count() }} Units Retrieved
                        </span>
                    </h2>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <i class="bi bi-search absolute left-2.5 top-2 text-slate-400 text-xs"></i>
                    <input type="text" id="matrix-search-input" onkeyup="filterMatrixTable()" placeholder="Quick filter matrix..."
                        class="text-xs pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-sky-500 font-medium text-slate-800 shadow-2xs w-48 sm:w-60">
                </div>
                <button type="button" onclick="exportMatrixToCSV()"
                    class="px-2.5 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 shadow-2xs flex items-center gap-1.5 transition">
                    <i class="bi bi-file-earmark-excel text-emerald-600"></i> <span class="hidden sm:inline">CSV</span>
                </button>
                <button type="button" onclick="window.print()"
                    class="px-2.5 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 shadow-2xs flex items-center gap-1.5 transition">
                    <i class="bi bi-printer text-slate-600"></i> <span class="hidden sm:inline">Print</span>
                </button>
            </div>
        </header>

        <!-- Main Workspace -->
        <div id="printable-area" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3.5 custom-scroll">
            
            <!-- Compact KPI Ribbon (Including STP Clearance Telemetry) -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 text-base">
                        <i class="bi bi-houses-fill"></i>
                    </div>
                    <div>
                        <span class="block text-[8.5px] uppercase font-mono text-slate-400 font-bold">Total Flat Units</span>
                        <span class="text-base font-black font-mono text-slate-800">{{ $flats->count() }}</span>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-emerald-200/90 bg-emerald-50/20 shadow-2xs flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 text-base">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div>
                        <span class="block text-[8.5px] uppercase font-mono text-emerald-700 font-bold">Approved by STP</span>
                        <span class="text-base font-black font-mono text-emerald-800">
                            {{ $flats->where('is_aproved_by_dtp', 1)->count() }} Units
                        </span>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-amber-200/90 bg-amber-50/20 shadow-2xs flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-base">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <span class="block text-[8.5px] uppercase font-mono text-amber-700 font-bold">Pending STP</span>
                        <span class="text-base font-black font-mono text-amber-800">
                            {{ $flats->where('is_aproved_by_dtp', 0)->count() }} Units
                        </span>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 text-base">
                        <i class="bi bi-building-check"></i>
                    </div>
                    <div>
                        <span class="block text-[8.5px] uppercase font-mono text-slate-400 font-bold">Plan Approved</span>
                        <span class="text-base font-black font-mono text-indigo-700">
                            {{ $flats->where('approved_building_plan', 'yes')->count() }} Units
                        </span>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 text-base">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <span class="block text-[8.5px] uppercase font-mono text-slate-400 font-bold">OC Received</span>
                        <span class="text-base font-black font-mono text-teal-700">
                            {{ $flats->filter(fn($f) => !empty($f->oc_status) && strtolower($f->oc_status) !== 'pending')->count() }} Units
                        </span>
                    </div>
                </div>
            </div>

            <!-- Master High-Density Table Card -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-150 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-xs uppercase font-mono bg-emerald-100 text-emerald-900 border border-emerald-300 px-2 py-0.5 rounded">
                            LICENSE: {{ $licenseNo }}
                        </span>
                        <span class="text-[11px] text-slate-500 font-bold">
                            Zone: <strong class="text-slate-800 uppercase">{{ $displayZoneName ?? 'ZONE' }}</strong>
                        </span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">
                        High-Density Full Matrix Layout
                    </span>
                </div>

                <div class="overflow-x-auto custom-scroll">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100/80 border-b border-slate-200 text-[9px] text-slate-600 uppercase font-black tracking-wider">
                                <th class="px-3 py-2.5" style="width: 3%;">#</th>
                                <th class="px-3 py-2.5">Flat No.</th>
                                <th class="px-3 py-2.5">Block / Tower</th>
                                <th class="px-3 py-2.5">Floor</th>
                                <th class="px-3 py-2.5">Project Name</th>
                                <th class="px-3 py-2.5">Colonizer</th>
                                <th class="px-3 py-2.5">Location</th>
                                <th class="px-3 py-2.5">Building Plan</th>
                                <th class="px-3 py-2.5">OC Status</th>
                                <th class="px-3 py-2.5">Draw Quota</th>
                                <th class="px-3 py-2.5">Documents</th>
                                <th class="px-3 py-2.5">STP Status</th>
                                <th class="px-3 py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-150 text-slate-700">
                            @forelse($flats as $index => $f)
                                @php
                                    $locationText = collect([$f->town_name, $f->district_name, $f->sector_no ? 'Sec ' . $f->sector_no : null])->filter()->join(', ');
                                    $planApproved = strtolower($f->approved_building_plan ?? '') === 'yes';
                                    $hasOc = !empty($f->oc_status) && strtolower($f->oc_status) !== 'pending';
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors matrix-row" data-search="{{ ($f->flat_number ?? '') . ' ' . ($f->block_tower_number ?? '') . ' ' . ($f->flat_code ?? '') . ' ' . ($f->project_name ?? '') . ' ' . ($f->developer_name ?? '') . ' ' . $locationText }}">
                                    <td class="px-3 py-2 font-mono text-slate-400 text-[11px]">{{ $index + 1 }}</td>
                                    
                                    <!-- Flat Number -->
                                    <td class="px-3 py-2 font-black text-violet-700 font-mono text-sm whitespace-nowrap">
                                        {{ $f->flat_number ?? '-' }}
                                    </td>

                                    <!-- Block / Tower -->
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-mono">
                                            {{ $f->block_tower_number ?? 'Block N/A' }}
                                        </span>
                                    </td>

                                    <!-- Floor -->
                                    <td class="px-3 py-2 font-medium text-slate-700 capitalize whitespace-nowrap">
                                        {{ $f->floor ?? '-' }}
                                    </td>

                                    <!-- Project Name -->
                                    <td class="px-3 py-2 font-bold text-slate-900 text-xs">
                                        {{ ucwords(strtolower($f->project_name ?? '-')) }}
                                    </td>

                                    <!-- Colonizer -->
                                    <td class="px-3 py-2 text-slate-700 font-medium text-xs">
                                        {{ ucwords(strtolower($f->developer_name ?: '-')) }}
                                    </td>

                                    <!-- Location -->
                                    <td class="px-3 py-2 text-slate-600 text-[11px]">
                                        {{ $locationText }}
                                    </td>

                                    <!-- Approved Building Plan -->
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if($planApproved)
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded text-[9px] font-extrabold uppercase inline-flex items-center gap-0.5">
                                                <i class="bi bi-check-circle-fill text-[8px]"></i> Approved
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-[9px] font-bold uppercase">
                                                Pending
                                            </span>
                                        @endif
                                    </td>

                                    <!-- OC Status -->
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-black uppercase font-mono {{ $hasOc ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $f->oc_status ? ucfirst($f->oc_status) : 'Pending' }}
                                        </span>
                                    </td>

                                    <!-- Draw Quota -->
                                    <td class="px-3 py-2 font-mono font-bold text-slate-700 whitespace-nowrap">
                                        {{ $f->flats_offered_for_draw ? $f->flats_offered_for_draw . ' Units' : 'Eligible' }}
                                    </td>

                                    <!-- Attached Documents -->
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="flex items-center gap-1 no-print">
                                            @if(!empty($f->developer_plan))
                                                <a href="{{ asset('storage/' . $f->developer_plan) }}" target="_blank" title="Developer Plan" class="p-1 text-sky-600 hover:text-sky-800 bg-sky-50 rounded border border-sky-200 text-[10px]">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            @endif
                                            @if(!empty($f->building_plan_copy))
                                                <a href="{{ asset('storage/' . $f->building_plan_copy) }}" target="_blank" title="Sanctioned Building Plan" class="p-1 text-indigo-600 hover:text-indigo-800 bg-indigo-50 rounded border border-indigo-200 text-[10px]">
                                                    <i class="bi bi-file-earmark-richtext"></i>
                                                </a>
                                            @endif
                                            @if(!empty($f->oc_copy_file))
                                                <a href="{{ asset('storage/' . $f->oc_copy_file) }}" target="_blank" title="OC Copy" class="p-1 text-emerald-600 hover:text-emerald-800 bg-emerald-50 rounded border border-emerald-200 text-[10px]">
                                                    <i class="bi bi-file-earmark-check"></i>
                                                </a>
                                            @endif
                                            @if(empty($f->developer_plan) && empty($f->building_plan_copy) && empty($f->oc_copy_file))
                                                <span class="text-[9px] text-slate-400 font-mono">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- STP Approval Status -->
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if($f->is_aproved_by_dtp)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded text-[9px] font-black uppercase inline-flex items-center gap-1 shadow-2xs" title="Approved by {{ $f->dtp_user_name ?? 'STP' }} on {{ $f->dtp_approved_at ?? '' }}">
                                                <i class="bi bi-patch-check-fill text-emerald-600"></i> Approved
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 border border-amber-300 rounded text-[9px] font-black uppercase inline-flex items-center gap-1 shadow-2xs">
                                                <i class="bi bi-hourglass-split text-amber-600"></i> Pending
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 justify-end no-print">
                                            <a href="{{ route('ews.stp.flats.show', $f->secure_id) }}" target="_blank" title="View Full Dossier in Dedicated Page"
                                                class="px-2.5 py-1 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-200 hover:border-sky-600 rounded-lg text-[10px] font-black uppercase transition-all flex items-center gap-1 shadow-2xs">
                                                <i class="bi bi-eye-fill"></i> View
                                            </a>
                                            @if(!$f->is_aproved_by_dtp)
                                                <a href="{{ route('ews.stp.flats.edit', $f->secure_id) }}" target="_blank" title="Edit Flat Specifications (Developer Form)"
                                                    class="px-2.5 py-1 bg-amber-50 hover:bg-amber-600 text-amber-800 hover:text-white border border-amber-200 hover:border-amber-600 rounded-lg text-[10px] font-black uppercase transition-all flex items-center gap-1 shadow-2xs">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                            @else
                                                <span class="px-2.5 py-1 bg-slate-100 text-slate-400 border border-slate-200 rounded-lg text-[10px] font-bold uppercase flex items-center gap-1 cursor-not-allowed" title="Locked: Flat has been approved and cannot be edited">
                                                    <i class="bi bi-lock-fill text-slate-400"></i> Locked
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="px-4 py-8 text-center text-slate-400 font-mono text-xs">
                                        <i class="bi bi-inbox text-3xl block text-slate-300 mb-2"></i>
                                        No flat records found under License {{ $licenseNo }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function filterMatrixTable() {
            const query = $('#matrix-search-input').val().toLowerCase().trim();
            $('.matrix-row').each(function() {
                const text = $(this).attr('data-search').toLowerCase();
                if (text.indexOf(query) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        function copyCode(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'COPIED',
                    text: 'Flat Code copied: ' + text,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#ffffff',
                    color: '#1e293b'
                });
            });
        }

        function exportMatrixToCSV() {
            let csv = "SNo,FlatNo,BlockTower,Floor,FlatCode,ProjectName,Colonizer,Location,ApprovedPlan,OCStatus,DrawQuota\n";
            $('.matrix-row:visible').each(function(i) {
                const flatNo = $(this).find('td:nth-child(2)').text().trim();
                const block = $(this).find('td:nth-child(3)').text().trim();
                const floor = $(this).find('td:nth-child(4)').text().trim();
                const code = $(this).find('td:nth-child(5)').text().trim();
                const proj = $(this).find('td:nth-child(6)').text().trim();
                const colonizer = $(this).find('td:nth-child(7)').text().trim();
                const loc = $(this).find('td:nth-child(8)').text().trim();
                const plan = $(this).find('td:nth-child(9)').text().trim();
                const oc = $(this).find('td:nth-child(10)').text().trim();
                const quota = $(this).find('td:nth-child(11)').text().trim();

                csv += `"${i+1}","${flatNo}","${block}","${floor}","${code}","${proj}","${colonizer}","${loc}","${plan}","${oc}","${quota}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `EWS_Master_Matrix_{{ $licenseNo }}_${new Date().toISOString().slice(0,10)}.csv`;
            a.click();
            URL.revokeObjectURL(url);
        }

        // SweetAlert Confirmation and AJAX Approval for STP in Matrix
        function approveFlatDtpFromMatrix(secureId, flatNum, blockName) {
            Swal.fire({
                title: 'Official STP Approval Confirmation',
                html: `Are you sure you want to certify and approve Flat <strong>${flatNum}</strong> (${blockName}) under STP Authority?<br><br>
                <div class="p-3 bg-emerald-50 rounded-lg text-emerald-900 font-mono text-xs text-left border border-emerald-200 space-y-1">
                    <div>&bull; Database field <strong>is_aproved_by_DTP</strong> will be set to <strong>1</strong>.</div>
                    <div>&bull; Action will be recorded in digital audit logs with your officer credentials.</div>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-patch-check-fill"></i> Yes, Approve Now',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Recording STP Approval...',
                        text: 'Please wait while digital clearance is authenticated.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch("{{ url('ews/stp/flats') }}/" + secureId + "/approve-dtp", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Flat Approved Successfully',
                                text: data.message,
                                confirmButtonColor: '#059669'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Approval Failed',
                                text: data.message || 'Could not record approval',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Failed to communicate with server.',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>

