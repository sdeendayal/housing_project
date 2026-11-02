@php
    $isDepartment = request()->routeIs('ews.department.*') || ($user && $user->role === 'ews_department');
    $isStp = request()->routeIs('ews.stp.*') || ($user && in_array($user->role, ['ews_stp', 'stp']));
    $flatsCount = $stats['total_flats'] ?? \Illuminate\Support\Facades\DB::table('ews_builder_flats')->count();
    $developerFlatsCount = $flatsCount;
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Flat Dossier - {{ $flat->flat_code ?? $flat->flat_number }} | {{ $isDepartment ? 'Department' : 'TCP' }} Haryana</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        @media print {
            body { background: white !important; }
            header, aside, #btn-back, .no-print { display: none !important; }
            #printable-area { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="h-screen overflow-hidden flex {{ $isDepartment ? 'bg-[#f1f8f4]' : 'bg-[#f8fafc]' }} text-slate-800">

    <!-- 1. Left Sidebar (TCP, STP or Department) -->
    @if($isDepartment)
        @include('ews.department.partials.sidebar')
    @elseif($isStp)
        @include('ews.stp.partials.sidebar')
    @else
        @include('ews.tcp.partials.sidebar')
    @endif

    <!-- 2. Right Workspace Container -->
    <div class="{{ $isDepartment ? 'flex-1 min-w-0 flex flex-col ml-[260px] h-screen overflow-hidden' : 'flex-1 flex flex-col h-full overflow-hidden min-w-0' }}">
        
        <!-- Top Compact Navigation & Title Bar (Height: 44px) -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs shrink-0 h-11 px-3 sm:px-4 flex items-center justify-between">
            <!-- Left: Back Button, Emblem & Identity -->
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ $backUrl ?? route($isDepartment ? 'ews.department.builder-flats' : 'ews.tcp.dashboard') }}" id="btn-back" title="Back to Master Matrix"
                    class="w-7 h-7 rounded-md bg-slate-100 hover:bg-sky-100 text-slate-700 hover:text-sky-700 border border-slate-200 flex items-center justify-center transition-all shadow-2xs shrink-0 group">
                    <i class="bi bi-arrow-left text-xs transition-transform group-hover:-translate-x-0.5"></i>
                </a>

                <div class="flex items-center gap-1.5 min-w-0">
                    <img src="{{ asset('Haryana_emblem.png') }}" class="w-5 h-5 object-contain shrink-0" alt="Haryana Emblem">
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="text-xs font-black uppercase tracking-wide text-slate-900">EWS FLAT DOSSIER:</span>
                        <span class="text-xs font-black font-mono text-violet-700">FLAT #{{ $flat->flat_number }}</span>
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">TOWER: {{ $flat->block_tower_number ?? '—' }}</span>
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">{{ $flat->floor ?? '—' }}</span>
                        <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-[7.5px] font-black uppercase">Read-Only</span>
                    </div>
                </div>
            </div>

            <!-- Right: Action Bar (Print + Copy Code + DTP Badge) -->
            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Flat Unique Code Copy Badge -->
                <div class="hidden sm:flex items-center gap-1 bg-slate-100 hover:bg-slate-200 border border-slate-300/80 px-2 py-0.5 rounded cursor-pointer transition-all" onclick="copyFlatCode()" title="Click to Copy Unique Flat Code">
                    <span class="text-[8px] font-mono font-black uppercase text-slate-500">CODE:</span>
                    <span class="text-[9.5px] font-mono font-black text-emerald-700 select-all" id="dossier-flat-code">{{ $flat->flat_code ?? $flat->flat_number }}</span>
                    <i class="bi bi-copy text-[9px] text-slate-400 hover:text-slate-700 ml-0.5"></i>
                </div>

                <!-- DTP Status Badge -->
                @if($flat->is_aproved_by_dtp)
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded text-[8.5px] font-black uppercase inline-flex items-center gap-1 shadow-2xs">
                        <i class="bi bi-patch-check-fill text-emerald-600"></i>
                        <span>DTP Approved</span>
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 border border-amber-300 rounded text-[8.5px] font-black uppercase inline-flex items-center gap-1 shadow-2xs">
                        <i class="bi bi-hourglass-split text-amber-600"></i>
                        <span>Pending Clearance</span>
                    </span>
                @endif

                <!-- Print Button -->
                <button type="button" onclick="window.print()" title="Print Unit Dossier"
                    class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded text-[9px] font-bold flex items-center gap-1 shadow-2xs cursor-pointer transition-all">
                    <i class="bi bi-printer text-slate-500"></i>
                    <span class="hidden md:inline">Print</span>
                </button>
            </div>
        </header>

        <!-- Main Workspace (Zero-Waste High Density Viewport) -->
        <main class="flex-1 overflow-y-auto px-3 sm:px-4 py-2 space-y-2 custom-scroll" id="printable-area">
            
            <!-- Compact Hero Ribbon (Height ~40px) -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 text-white rounded-lg px-3 py-1.5 shadow-2xs border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="w-6 h-6 rounded bg-cyan-500/20 text-cyan-400 border border-cyan-400/30 flex items-center justify-center text-xs shrink-0">
                        <i class="bi bi-building"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-wider text-cyan-300">{{ $flat->project_name ?? '—' }}</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-[10px] text-slate-300 font-medium">Colonizer: <strong class="text-white">{{ $project->colonizer_name ?? $flat->developer_name ?? $creator->name ?? '—' }}</strong></span>
                    <span class="text-slate-600">•</span>
                    <span class="text-[10px] font-mono text-emerald-400 font-bold">Lic #{{ $flat->license_no ?? '—' }}</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-[10px] text-slate-300"><i class="bi bi-geo-alt-fill text-rose-400 text-[9px]"></i> {{ $flat->town_name ?? '' }}, {{ $flat->district_name ?? '' }} ({{ $flat->zone_name ?? 'Zone' }})</span>
                    @if($project && !empty($project->sector))
                        <span class="text-slate-600">•</span>
                        <span class="text-[10px] text-amber-300 font-bold">Sector {{ $project->sector }}</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 text-[9px] text-slate-400 font-mono shrink-0">
                    <span>RECORD ID: <strong class="text-slate-200">#{{ $flat->id }}</strong></span>
                    <span class="text-slate-600">•</span>
                    <span>REGISTERED: <strong class="text-slate-200">{{ $flat->created_at ? $flat->created_at->format('d/m/Y') : '—' }}</strong></span>
                </div>
            </div>

            <!-- 3-Column Ultra-Dense Executive Grid ("Kam Jagah Me Jyada Chiz") -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">

                <!-- COLUMN 1: Architectural & Statutory Unit Data -->
                <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-150">
                            <h3 class="text-[10px] font-black uppercase tracking-wider text-slate-900 flex items-center gap-1">
                                <i class="bi bi-grid-3x3-gap-fill text-sky-600 text-xs"></i>
                                <span>Architectural Identity</span>
                            </h3>
                            <span class="text-[8px] font-black uppercase px-1.5 py-0.2 rounded bg-sky-50 text-sky-700 border border-sky-200">Unit Spec</span>
                        </div>

                        <!-- 4 Compact Key-Value Metric Boxes -->
                        <div class="grid grid-cols-2 gap-1.5 mb-2">
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Flat Number</span>
                                <span class="text-sm font-black font-mono text-violet-700 leading-none">{{ $flat->flat_number }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Block / Tower</span>
                                <span class="text-xs font-black font-mono text-indigo-700 truncate leading-none">{{ $flat->block_tower_number ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Floor Details</span>
                                <span class="text-xs font-bold text-slate-800 leading-none truncate">{{ $flat->floor ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Register Flat</span>
                                <span class="text-xs font-bold text-slate-700 leading-none uppercase">{{ $flat->register_flat ?? 'YES' }}</span>
                            </div>
                        </div>

                        <!-- Dense Data Rows -->
                        <div class="divide-y divide-slate-100 text-[10px]">
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Unique Dossier Code:</span>
                                <span class="font-mono font-bold text-emerald-700 select-all">{{ $flat->flat_code ?? '—' }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Registry Scheme:</span>
                                <span class="font-bold text-slate-700">Haryana EWS Colony Matrix</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Database Node:</span>
                                <span class="font-mono text-slate-500 text-[9px]">ID #{{ $flat->id }} • {{ substr($flat->secure_id ?? '', 0, 8) }}...</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Entry Officer:</span>
                                <span class="font-bold text-slate-700 truncate max-w-[150px]">{{ $creator->name ?? 'Authorized Officer' }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Entry Timestamp:</span>
                                <span class="font-mono text-slate-500 text-[9px]">{{ $flat->created_at ? $flat->created_at->format('d M, Y h:i A') : '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Hash Badge -->
                    <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[8px] font-mono text-slate-400">
                        <span>INTEGRITY VERIFIED</span>
                        <span class="text-emerald-600 font-bold flex items-center gap-0.5"><i class="bi bi-shield-check"></i> STATE COMPLIANT</span>
                    </div>
                </div>

                <!-- COLUMN 2: Project, Developer & Geographic Profile -->
                <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-150">
                            <h3 class="text-[10px] font-black uppercase tracking-wider text-slate-900 flex items-center gap-1">
                                <i class="bi bi-briefcase-fill text-indigo-600 text-xs"></i>
                                <span>Project & Colonizer Profile</span>
                            </h3>
                            <span class="text-[8px] font-mono font-bold px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ $flat->license_no ? 'Lic #' . $flat->license_no : 'Project' }}
                            </span>
                        </div>

                        <!-- Dense Data Rows -->
                        <div class="divide-y divide-slate-100 text-[10px]">
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Project Name:</span>
                                <span class="font-bold text-slate-900 uppercase truncate max-w-[160px]">{{ $flat->project_name ?? '—' }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Developer / Colonizer:</span>
                                <span class="font-bold text-slate-800 uppercase truncate max-w-[160px]">{{ $project->colonizer_name ?? $flat->developer_name ?? $creator->name ?? '—' }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">License Number:</span>
                                <span class="font-mono font-black text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">{{ $flat->license_no ?? '—' }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Administrative Zone:</span>
                                <span class="font-black text-sky-700 uppercase">{{ $flat->zone_name ?? 'ROHTAK ZONE' }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">District & Town:</span>
                                <span class="font-bold text-slate-800 uppercase">{{ $flat->town_name ?? '' }}, {{ $flat->district_name ?? '—' }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Sector:</span>
                                <span class="font-bold text-indigo-700">Sector {{ $project->sector ?? $flat->sector_no ?? '—' }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Total Project Area:</span>
                                <span class="font-semibold text-slate-700">{{ $flat->area_of_project ?? ($project->total_project_area ?? '—') }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Site Address:</span>
                                <span class="text-slate-600 truncate max-w-[160px]" title="{{ $flat->address_of_project ?? ($project->site_address ?? '—') }}">{{ $flat->address_of_project ?? ($project->site_address ?? '—') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Developer Plan Link Strip -->
                    <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[8px] font-bold text-slate-400 uppercase">Developer Layout Plan:</span>
                        @if(!empty($flat->developer_plan) || ($project && !empty($project->developer_plan_pdf)))
                            <a href="{{ asset($flat->developer_plan ?? $project->developer_plan_pdf) }}" target="_blank"
                                class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 hover:border-indigo-600 rounded text-[8px] font-black uppercase inline-flex items-center gap-1 transition shadow-2xs">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500"></i>
                                <span>View Plan</span>
                                <i class="bi bi-box-arrow-up-right text-[7px]"></i>
                            </a>
                        @else
                            <span class="text-[8px] text-slate-400 font-mono italic">Not Attached</span>
                        @endif
                    </div>
                </div>

                <!-- COLUMN 3: Compliance Sanctions, OC Clearance & Quotas (Steps 10 & 11) -->
                <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-150">
                            <h3 class="text-[10px] font-black uppercase tracking-wider text-slate-900 flex items-center gap-1">
                                <i class="bi bi-shield-check text-emerald-600 text-xs"></i>
                                <span>Sanctions, OC & Quotas</span>
                            </h3>
                            <span class="text-[8px] font-black uppercase px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Steps 10 & 11</span>
                        </div>

                        <!-- Step 10 Sanctions Row -->
                        <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5 mb-1.5">
                            <div class="flex items-center justify-between text-[9px] mb-1">
                                <span class="font-bold text-slate-700 uppercase">Building Plan Sanction:</span>
                                <span class="px-1.5 py-0.2 rounded font-black text-[8px] uppercase {{ strtolower($flat->approved_building_plan ?? '') === 'no' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $flat->approved_building_plan ?? 'Approved' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[8.5px] text-slate-500">
                                <span>Date: <strong class="font-mono text-slate-700">{{ $flat->building_plan_approval_date ?? '—' }}</strong></span>
                                @if(!empty($flat->building_plan_copy))
                                    <a href="{{ asset($flat->building_plan_copy) }}" target="_blank"
                                        class="px-1.5 py-0.2 bg-sky-600 hover:bg-sky-700 text-white rounded text-[7.5px] font-black uppercase inline-flex items-center gap-0.5 shadow-2xs">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                        <span>Plan PDF</span>
                                    </a>
                                @else
                                    <span class="font-mono text-slate-400 italic text-[7.5px]">No PDF</span>
                                @endif
                            </div>
                        </div>

                        <!-- Step 11 OC Compliance Row -->
                        <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5 mb-1.5">
                            <div class="flex items-center justify-between text-[9px] mb-1">
                                <span class="font-bold text-slate-700 uppercase">Occupation Cert (OC):</span>
                                <span class="px-1.5 py-0.2 rounded font-black text-[8px] uppercase bg-amber-100 text-amber-800">
                                    {{ $flat->oc_status ?? ($flat->oc_status_yn ?? 'Part') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[8.5px] text-slate-500">
                                <span>Date: <strong class="font-mono text-slate-700">{{ $flat->oc_date ?? '—' }}</strong></span>
                                @if(!empty($flat->oc_copy_file))
                                    <a href="{{ asset($flat->oc_copy_file) }}" target="_blank"
                                        class="px-1.5 py-0.2 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[7.5px] font-black uppercase inline-flex items-center gap-0.5 shadow-2xs">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                        <span>OC PDF</span>
                                    </a>
                                @else
                                    <span class="font-mono text-slate-400 italic text-[7.5px]">No OC PDF</span>
                                @endif
                            </div>
                        </div>

                        <!-- 4 Compact Quota Metric Chips -->
                        <div class="grid grid-cols-4 gap-1 text-center mb-1">
                            <div class="bg-slate-50 p-1 rounded border border-slate-200">
                                <span class="block text-[6.5px] font-black uppercase text-slate-400">Total</span>
                                <span class="text-xs font-black font-mono text-slate-800">{{ $flat->total_flats_in_project ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 p-1 rounded border border-slate-200">
                                <span class="block text-[6.5px] font-black uppercase text-slate-400">EWS</span>
                                <span class="text-xs font-black font-mono text-emerald-700">{{ $flat->total_ews_flats_in_project ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 p-1 rounded border border-slate-200">
                                <span class="block text-[6.5px] font-black uppercase text-slate-400">Draw</span>
                                <span class="text-xs font-black font-mono text-sky-700">{{ $flat->flats_offered_for_draw ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 p-1 rounded border border-slate-200">
                                <span class="block text-[6.5px] font-black uppercase text-slate-400">Rem OC</span>
                                <span class="text-xs font-black font-mono text-amber-700">{{ $flat->remaining_flats_for_oc ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- DTP Certification Audit Box -->
                    <div class="mt-1.5 pt-1.5 border-t border-slate-150">
                        <div class="flex items-center justify-between text-[8.5px]">
                            <span class="font-bold text-slate-400 uppercase">DTP Clearance:</span>
                            @if($flat->is_aproved_by_dtp)
                                <span class="font-mono font-bold text-emerald-700 flex items-center gap-0.5">
                                    <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                    <span>{{ $flat->dtp_user_name ?? 'DTP' }} ({{ $flat->dtp_approved_at ? date('d/m/Y', strtotime($flat->dtp_approved_at)) : 'Approved' }})</span>
                                </span>
                            @else
                                <span class="font-mono font-bold text-amber-700 flex items-center gap-0.5">
                                    <i class="bi bi-hourglass-split text-amber-600"></i>
                                    <span>Awaiting Clearance</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </main>

        <!-- Compact Bottom Status Footer (Height: 22px) -->
        <footer class="bg-white border-t border-slate-200 py-1 px-4 text-center shrink-0">
            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">
                Town and Country Planning Department • Government of Haryana • EWS Colony Regulatory Cell
            </p>
        </footer>

    </div>

    <!-- Script for copy code & navigation -->
    <script>
        function copyFlatCode() {
            const code = document.getElementById('dossier-flat-code').innerText;
            navigator.clipboard.writeText(code).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Copied: ' + code,
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }

        // Sidebar Navigation Handler for TCP
        function activateTcpNav(target) {
            let url = "{{ route('ews.tcp.dashboard') }}";
            if (target && target !== 'dashboard') {
                url += "?scope=" + target;
            }
            window.location.href = url;
        }

        // Sidebar Navigation Handler for STP
        function activateStpNav(target) {
            let url = "{{ route('ews.stp.dashboard') }}";
            if (target && target !== 'dashboard') {
                url += "?scope=" + target;
            }
            window.location.href = url;
        }

        // Ensure active sidebar link has visual indicator
        $(document).ready(function() {
            $('#sidebar-nav-all').removeClass('text-slate-300').addClass('bg-slate-800 text-white font-bold');
            $('#sidebar-nav-all .tcp-nav-dot, #sidebar-nav-all .stp-nav-dot').removeClass('hidden');
        });
    </script>
</body>
</html>