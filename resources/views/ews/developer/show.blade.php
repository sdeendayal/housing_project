<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Flat Dossier - {{ $flat->flat_code }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            aside, header, #action-toolbar, #btn-back-top { display: none !important; }
            .print-clean { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
            .no-print { display: none !important; }
            #printable-area { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    @include('ews.developer.partials.sidebar')

    <!-- RIGHT CONTAINER WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between shrink-0 shadow-xs z-10 gap-3">
            <!-- Left: Back Navigation + Breadcrumb & Title -->
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('ews.developer.dashboard') }}" id="btn-back-top" title="Back to Dashboard"
                    class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-sky-50 hover:text-sky-600 text-slate-700 border border-slate-200 flex items-center justify-center transition-all shadow-2xs shrink-0 group">
                    <i class="bi bi-arrow-left text-base transition-transform group-hover:-translate-x-0.5"></i>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 whitespace-nowrap overflow-hidden">
                        <a href="{{ route('ews.developer.dashboard') }}" class="hover:text-sky-600 transition-colors">Developer Portal</a>
                        <i class="bi bi-chevron-right text-[9px] text-slate-300"></i>
                        <span class="text-slate-600">Unit Dossier</span>
                        <i class="bi bi-chevron-right text-[9px] text-slate-300"></i>
                        <span class="font-mono text-sky-700 font-bold truncate">{{ $flat->flat_code ?? $flat->flat_number }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 whitespace-nowrap">
                        <h2 class="text-xs sm:text-sm font-black tracking-tight text-slate-900 uppercase">
                            Flat Unit {{ $flat->flat_number }}
                            <span class="text-slate-400 font-medium font-sans">({{ $flat->block_tower_number ?? 'Block' }}, {{ $flat->floor ?? 'Floor' }})</span>
                        </h2>
                        <span class="hidden sm:inline-flex px-2 py-0.5 rounded-full text-[8.5px] font-black uppercase bg-sky-50 text-sky-700 border border-sky-200">
                            Verified Master Unit
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Action Buttons Toolbar -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <div id="action-toolbar" class="flex items-center gap-2 shrink-0">
                    <!-- Print Button -->
                    <button type="button" onclick="window.print()" title="Print Dossier"
                        class="px-2.5 sm:px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 shadow-2xs flex items-center gap-1.5 transition-all shrink-0">
                        <i class="bi bi-printer text-slate-500"></i>
                        <span class="hidden md:inline">Print</span>
                    </button>

                    <!-- Edit Flat Button -->
                    <a href="{{ route('ews.developer.flats.edit', $flat->secure_id ?? \App\Helpers\EwsHelper::encodeSecureId($flat->id)) }}" title="Edit Flat Specifications"
                        class="px-3 sm:px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-lg text-xs font-black uppercase flex items-center gap-1.5 transition-all shadow-xs shrink-0 whitespace-nowrap">
                        <i class="bi bi-pencil-square text-xs"></i>
                        <span>Edit Flat</span>
                    </a>

                    <!-- STP Status Badge (Read-Only for Developer) -->
                    @if($flat->is_aproved_by_dtp)
                        <div class="px-3 py-1.5 bg-emerald-50 border border-emerald-300 rounded-lg flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                            <i class="bi bi-patch-check-fill text-emerald-600 text-sm"></i>
                            <div class="text-left leading-none">
                                <div class="text-[9.5px] font-black uppercase text-emerald-800">STP Approved</div>
                                <div class="text-[8px] font-mono text-emerald-600 mt-0.5">{{ $flat->dtp_approved_at ? \Carbon\Carbon::parse($flat->dtp_approved_at)->format('d M, Y') : 'Certified' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="px-3 py-1.5 bg-amber-50 border border-amber-300 rounded-lg flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                            <i class="bi bi-hourglass-split text-amber-600 text-sm"></i>
                            <div class="text-left leading-none">
                                <div class="text-[9.5px] font-black uppercase text-amber-800">STP Pending</div>
                                <div class="text-[8px] font-mono text-amber-600 mt-0.5">Awaiting Review</div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="w-px h-6 bg-slate-200 hidden xl:block shrink-0"></div>

                <!-- Profile Context -->
                <div class="text-right hidden xl:block shrink-0">
                    <div class="text-[10px] text-slate-700 font-bold flex items-center gap-1 justify-end">
                        <span>{{ $user->name }}</span>
                        <i class="bi bi-person-circle text-sky-600"></i>
                    </div>
                    <div class="text-[8.5px] text-slate-500 font-mono">Developer Portal | Mobile: {{ $user->mobile }}</div>
                </div>
            </div>
        </header>

        <!-- Main Content Scrollable Workspace -->
        <div id="printable-area" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 custom-scroll">

            <!-- Compact Hero Identity Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 rounded-xl px-4 py-2.5 text-white shadow-md relative overflow-hidden print-clean">
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-sky-400 to-indigo-600 flex items-center justify-center text-white text-base font-black shadow-md shadow-sky-500/20 shrink-0 border border-white/20">
                            <i class="bi bi-building-check"></i>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-sm sm:text-base font-black tracking-tight text-white font-mono">
                                    {{ $flat->flat_code }}
                                </h1>
                                <span class="px-2 py-0.5 bg-sky-500/25 border border-sky-400/40 text-sky-200 rounded text-[9px] font-black uppercase tracking-wider">
                                    Block / Tower: {{ strtoupper($flat->block_tower_number) }}
                                </span>
                                <span class="px-2 py-0.5 bg-violet-500/25 border border-violet-400/40 text-violet-200 rounded text-[9px] font-black uppercase tracking-wider">
                                    {{ ucfirst($flat->floor) }}
                                </span>
                                <span class="px-2 py-0.5 bg-emerald-500/25 border border-emerald-400/40 text-emerald-200 rounded text-[9px] font-black uppercase tracking-wider">
                                    Flat No. {{ $flat->flat_number }}
                                </span>
                                @if($flat->is_aproved_by_dtp)
                                    <span class="px-2 py-0.5 bg-teal-400/25 border border-teal-300/50 text-teal-200 rounded text-[9px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                                        <i class="bi bi-patch-check-fill text-teal-300"></i> STP Certified
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-amber-400/25 border border-amber-300/50 text-amber-200 rounded text-[9px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                                        <i class="bi bi-hourglass-split text-amber-300"></i> STP Pending
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-0.5 mt-0.5 text-[11px] text-slate-300">
                                <span class="flex items-center gap-1 font-semibold">
                                    <i class="bi bi-building text-sky-400 text-[10px]"></i>
                                    <span>Project: <strong>{{ ucwords(strtolower($flat->project_name)) }}</strong></span>
                                </span>
                                <span class="text-slate-500 text-[9px]">•</span>
                                <span class="flex items-center gap-1 text-slate-300">
                                    <i class="bi bi-geo-alt-fill text-rose-400 text-[10px]"></i>
                                    <span>{{ ucwords(strtolower($flat->town_name)) }}, {{ strtoupper($flat->district_name) }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($flat->created_at)
                    <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 sm:border-l border-white/10 pt-1.5 sm:pt-0 sm:pl-4 shrink-0 gap-0.5 text-right">
                        <span class="text-[8px] uppercase tracking-widest font-mono text-slate-400 font-bold">Registered On</span>
                        <div class="flex sm:flex-col items-baseline sm:items-end gap-1.5 sm:gap-0 font-mono">
                            <span class="text-[11px] font-bold text-emerald-400">{{ $flat->created_at->format('d M, Y') }}</span>
                            <span class="text-[8.5px] text-slate-400">{{ $flat->created_at->format('h:i A') }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Main High-Density Grid (Pack Maximum Data Cleanly) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                <!-- Column Left: Flat Identity & Project Specs (7 Cols) -->
                <div class="lg:col-span-7 space-y-4">
                    
                    <!-- 1. Flat Architecture & Identification -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden print-clean">
                        <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-150 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-houses-fill text-sky-600 text-xs"></i>
                                <h3 class="text-[10.5px] font-black uppercase tracking-wider text-slate-800">Flat Architectural Identity</h3>
                            </div>
                            <span class="text-[9px] font-mono font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-100">Unit Dossier</span>
                        </div>
                        <div class="p-4 grid grid-cols-2 sm:grid-cols-4 gap-3.5 text-xs">
                            <div class="p-2.5 bg-slate-50/70 rounded-lg border border-slate-150/70">
                                <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">Flat Number</span>
                                <span class="text-base font-black font-mono text-violet-700 mt-0.5 block">{{ $flat->flat_number }}</span>
                            </div>
                            <div class="p-2.5 bg-slate-50/70 rounded-lg border border-slate-150/70">
                                <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">Block / Tower</span>
                                <span class="text-base font-black font-mono text-indigo-700 mt-0.5 block uppercase">{{ $flat->block_tower_number }}</span>
                            </div>
                            <div class="p-2.5 bg-slate-50/70 rounded-lg border border-slate-150/70">
                                <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">Floor Level</span>
                                <span class="text-sm font-bold text-slate-800 mt-0.5 block capitalize">{{ $flat->floor }}</span>
                            </div>
                            <div class="p-2.5 bg-slate-50/70 rounded-lg border border-slate-150/70">
                                <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">Unique Flat Code</span>
                                <div class="flex items-center justify-between mt-0.5">
                                    <span class="text-xs font-black font-mono text-emerald-700">{{ $flat->flat_code }}</span>
                                    <button type="button" onclick="copyCode('{{ $flat->flat_code }}')" title="Copy Code" class="text-slate-400 hover:text-sky-600 text-xs no-print">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Project Specifications & Developer Profile -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden print-clean">
                        <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-150 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-building text-indigo-600 text-xs"></i>
                                <h3 class="text-[10.5px] font-black uppercase tracking-wider text-slate-800">Project & Developer Profile</h3>
                            </div>
                            <span class="text-[9px] font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">RERA & License</span>
                        </div>
                        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                            <div>
                                <span class="block text-[9px] font-mono uppercase text-slate-400 font-bold">Project Name</span>
                                <span class="font-bold text-slate-900 text-sm capitalize">{{ ucwords(strtolower($flat->project_name)) }}</span>
                            </div>
                            <div>
                                <span class="block text-[9px] font-mono uppercase text-slate-400 font-bold">Developer Name</span>
                                <span class="font-bold text-slate-800 capitalize">{{ ucwords(strtolower($flat->developer_name ?: ($project->developer_name ?? $user->name))) }}</span>
                            </div>
                            <div>
                                <span class="block text-[9px] font-mono uppercase text-slate-400 font-bold">License Number</span>
                                <span class="font-mono font-bold text-indigo-700 bg-indigo-50/80 px-2.5 py-0.5 rounded text-[11px] inline-block border border-indigo-100 mt-0.5 uppercase">
                                    {{ strtoupper($flat->license_no ?: ($project->license_no ?? 'N/A')) }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[9px] font-mono uppercase text-slate-400 font-bold">Sector Number</span>
                                <span class="font-bold text-slate-800">{{ $flat->sector_no ?: ($project->sector_no ?? 'N/A') }}</span>
                            </div>
                            <div>
                                <span class="block text-[9px] font-mono uppercase text-slate-400 font-bold">Total Project Area</span>
                                <span class="font-bold text-slate-800 capitalize">{{ ucfirst($flat->area_of_project ?: ($project->area_of_project ?? 'N/A')) }}</span>
                            </div>
                            <div>
                                <span class="block text-[9px] font-mono uppercase text-slate-400 font-bold">Location & Town</span>
                                <span class="font-bold text-slate-800 capitalize">{{ ucwords(strtolower($flat->town_name)) }} (District: {{ strtoupper($flat->district_name) }})</span>
                            </div>
                            <div class="sm:col-span-2 pt-1 border-t border-slate-100">
                                <span class="block text-[9px] font-mono uppercase text-slate-400 font-bold">Project Site Address</span>
                                <span class="font-medium text-slate-700 leading-relaxed capitalize">{{ ucfirst($flat->address_of_project ?: ($project->address ?? 'N/A')) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Project Inventory Quota Telemetry -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden print-clean">
                        <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-150 flex items-center gap-2">
                            <i class="bi bi-pie-chart-fill text-amber-600 text-xs"></i>
                            <h3 class="text-[10.5px] font-black uppercase tracking-wider text-slate-800">Project Flat Inventory Telemetry</h3>
                        </div>
                        <div class="p-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-150">
                                <span class="block text-[8px] font-mono uppercase text-slate-400 font-bold">Total Flats in Project</span>
                                <span class="text-lg font-black font-mono text-slate-800 mt-0.5 block">
                                    {{ $flat->total_flats_in_project !== null ? number_format($flat->total_flats_in_project) : ($project && $project->total_flats_in_project ? number_format($project->total_flats_in_project) : 'N/A') }}
                                </span>
                            </div>
                            <div class="p-2.5 bg-sky-50/70 rounded-xl border border-sky-150">
                                <span class="block text-[8px] font-mono uppercase text-sky-700 font-bold">Total EWS Flats</span>
                                <span class="text-lg font-black font-mono text-sky-700 mt-0.5 block">
                                    {{ $flat->total_ews_flats_in_project !== null ? number_format($flat->total_ews_flats_in_project) : ($project && $project->total_ews_flats_in_project ? number_format($project->total_ews_flats_in_project) : 'N/A') }}
                                </span>
                            </div>
                            <div class="p-2.5 bg-emerald-50/70 rounded-xl border border-emerald-150">
                                <span class="block text-[8px] font-mono uppercase text-emerald-700 font-bold">Offered For Draw</span>
                                <span class="text-lg font-black font-mono text-emerald-700 mt-0.5 block">
                                    @if(strtolower($flat->oc_status ?? '') === 'complete')
                                        All (Complete OC)
                                    @elseif($flat->flats_offered_for_draw !== null)
                                        {{ number_format($flat->flats_offered_for_draw) }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            <div class="p-2.5 bg-amber-50/70 rounded-xl border border-amber-150">
                                <span class="block text-[8px] font-mono uppercase text-amber-700 font-bold">Remaining for OC</span>
                                <span class="text-lg font-black font-mono text-amber-700 mt-0.5 block">
                                    @if(strtolower($flat->oc_status ?? '') === 'complete')
                                        0 (Full Cleared)
                                    @elseif($flat->remaining_flats_for_oc !== null)
                                        {{ number_format($flat->remaining_flats_for_oc) }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Column Right: Planning, Approvals, OC & STP Status (5 Cols) -->
                <div class="lg:col-span-5 space-y-4">
                    
                    <!-- 4. Planning & Building Plan Approvals -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden print-clean">
                        <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-150 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-check-fill text-indigo-600 text-xs"></i>
                                <h3 class="text-[10.5px] font-black uppercase tracking-wider text-slate-800">Sanction & Plan Approvals</h3>
                            </div>
                            @php
                                $planApproved = strtolower($flat->approved_building_plan ?: ($project->approved_building_plan ?? 'no')) === 'yes';
                            @endphp
                            @if($planApproved)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-[8.5px] font-black uppercase inline-flex items-center gap-1">
                                    <i class="bi bi-check-circle-fill"></i> Approved
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 border border-rose-200 rounded text-[8.5px] font-black uppercase inline-flex items-center gap-1">
                                    <i class="bi bi-x-circle-fill"></i> Not Approved
                                </span>
                            @endif
                        </div>
                        <div class="p-4 space-y-3 text-xs">
                            <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-150">
                                <div>
                                    <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">Building Plan Approval Date</span>
                                    <span class="font-mono font-bold text-slate-800 text-xs">
                                        {{ $flat->building_plan_approval_date ? \Carbon\Carbon::parse($flat->building_plan_approval_date)->format('d M, Y') : ($project && $project->building_plan_approval_date ? \Carbon\Carbon::parse($project->building_plan_approval_date)->format('d M, Y') : 'N/A') }}
                                    </span>
                                </div>
                                <i class="bi bi-calendar-check text-slate-400 text-base"></i>
                            </div>

                            <!-- Developer Plan PDF -->
                            @php
                                $devPlanFile = $flat->developer_plan ?: ($project->developer_plan ?? null);
                            @endphp
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                                        <i class="bi bi-file-earmark-pdf-fill text-base"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block text-[9.5px] font-bold text-slate-800 truncate">Developer Layout Plan</span>
                                        <span class="block text-[8px] text-slate-400 font-mono">Architecture Plan PDF</span>
                                    </div>
                                </div>
                                @if(!empty($devPlanFile))
                                    <a href="{{ asset('storage/' . $devPlanFile) }}" target="_blank"
                                        class="px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[9px] font-black uppercase flex items-center gap-1 shadow-sm shrink-0 transition-all no-print">
                                        <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                                        <span>View PDF</span>
                                    </a>
                                @else
                                    <span class="text-[9px] text-slate-400 font-mono font-bold shrink-0">Not Uploaded</span>
                                @endif
                            </div>

                            <!-- Building Plan Sanction Copy PDF -->
                            @php
                                $bldgPlanCopyFile = $flat->building_plan_copy ?: ($project->building_plan_copy ?? null);
                            @endphp
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                                        <i class="bi bi-file-earmark-text-fill text-base"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block text-[9.5px] font-bold text-slate-800 truncate">Sanctioned Building Plan</span>
                                        <span class="block text-[8px] text-slate-400 font-mono">Official Approval Copy</span>
                                    </div>
                                </div>
                                @if(!empty($bldgPlanCopyFile))
                                    <a href="{{ asset('storage/' . $bldgPlanCopyFile) }}" target="_blank"
                                        class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-[9px] font-black uppercase flex items-center gap-1 shadow-sm shrink-0 transition-all no-print">
                                        <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                                        <span>View PDF</span>
                                    </a>
                                @else
                                    <span class="text-[9px] text-slate-400 font-mono font-bold shrink-0">Not Uploaded</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 5. Occupation Certificate (OC) Verification -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden print-clean">
                        <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-150 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-shield-check text-emerald-600 text-xs"></i>
                                <h3 class="text-[10.5px] font-black uppercase tracking-wider text-slate-800">Occupation Certificate (OC)</h3>
                            </div>
                            @php
                                $ocApproved = strtolower($flat->oc_status_yn ?: ($project->oc_status_yn ?? 'no')) === 'yes';
                            @endphp
                            @if($ocApproved)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-[8.5px] font-black uppercase inline-flex items-center gap-1">
                                    <i class="bi bi-check-circle-fill"></i> Received
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 rounded text-[8.5px] font-black uppercase inline-flex items-center gap-1">
                                    <i class="bi bi-hourglass-split"></i> Pending
                                </span>
                            @endif
                        </div>
                        <div class="p-4 space-y-3 text-xs">
                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-150">
                                    <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">OC Type</span>
                                    <span class="font-bold text-slate-800 text-xs uppercase mt-0.5 block">
                                        {{ $flat->oc_status ? ucfirst($flat->oc_status) : 'N/A' }}
                                    </span>
                                </div>
                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-150">
                                    <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">OC Issue Date</span>
                                    <span class="font-mono font-bold text-slate-800 text-xs mt-0.5 block">
                                        {{ $flat->oc_date ? \Carbon\Carbon::parse($flat->oc_date)->format('d M, Y') : 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            <!-- OC Copy PDF -->
                            @php
                                $ocFile = $flat->oc_copy_file;
                            @endphp
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <i class="bi bi-file-earmark-check-fill text-base"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block text-[9.5px] font-bold text-slate-800 truncate">Occupation Certificate Copy</span>
                                        <span class="block text-[8px] text-slate-400 font-mono">Official Authority OC Copy</span>
                                    </div>
                                </div>
                                @if(!empty($ocFile))
                                    <a href="{{ asset('storage/' . $ocFile) }}" target="_blank"
                                        class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[9px] font-black uppercase flex items-center gap-1 shadow-sm shrink-0 transition-all no-print">
                                        <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                                        <span>View OC</span>
                                    </a>
                                @else
                                    <span class="text-[9px] text-slate-400 font-mono font-bold shrink-0">Not Uploaded</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 6. STP Statutory Approval Status -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden print-clean">
                        <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-150 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-patch-check-fill text-teal-600 text-xs"></i>
                                <h3 class="text-[10.5px] font-black uppercase tracking-wider text-slate-800">STP Statutory Approval Status</h3>
                            </div>
                            @if($flat->is_aproved_by_dtp)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-[8.5px] font-black uppercase inline-flex items-center gap-1 shadow-2xs">
                                    <i class="bi bi-check-circle-fill"></i> STP Approved
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 rounded text-[8.5px] font-black uppercase inline-flex items-center gap-1 shadow-2xs">
                                    <i class="bi bi-hourglass-split"></i> Pending STP
                                </span>
                            @endif
                        </div>
                        <div class="p-4 space-y-2.5 text-xs">
                            <div class="p-3 {{ $flat->is_aproved_by_dtp ? 'bg-emerald-50/60 border-emerald-200' : 'bg-amber-50/60 border-amber-200' }} rounded-xl border flex items-center justify-between">
                                <div>
                                    <span class="block text-[8.5px] font-mono uppercase text-slate-500 font-bold">Clearance Status</span>
                                    <span class="text-xs font-black {{ $flat->is_aproved_by_dtp ? 'text-emerald-800' : 'text-amber-800' }} mt-0.5 block">
                                        {{ $flat->is_aproved_by_dtp ? 'APPROVED & CERTIFIED BY STP' : 'PENDING OFFICIAL STP CERTIFICATION' }}
                                    </span>
                                    <span class="text-[8.5px] font-mono {{ $flat->is_aproved_by_dtp ? 'text-emerald-700' : 'text-slate-500' }}">
                                        Database Field: <strong>is_aproved_by_DTP = {{ $flat->is_aproved_by_dtp ? '1' : '0' }}</strong>
                                    </span>
                                </div>
                                <div class="w-9 h-9 rounded-xl {{ $flat->is_aproved_by_dtp ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center text-base font-black shrink-0">
                                    <i class="bi {{ $flat->is_aproved_by_dtp ? 'bi-shield-fill-check' : 'bi-shield-exclamation' }}"></i>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-150">
                                    <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">Approved By Official</span>
                                    <span class="font-bold text-slate-800 text-xs mt-0.5 block truncate">
                                        {{ $flat->dtp_user_name ?? ($flat->is_aproved_by_dtp ? 'STP Official' : 'Pending Verification') }}
                                    </span>
                                </div>
                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-150">
                                    <span class="block text-[8.5px] font-mono uppercase text-slate-400 font-bold">Approval Date & Time</span>
                                    <span class="font-mono font-bold text-slate-800 text-xs mt-0.5 block">
                                        {{ $flat->dtp_approved_at ? \Carbon\Carbon::parse($flat->dtp_approved_at)->format('d M, Y h:i A') : 'Awaiting Review' }}
                                    </span>
                                </div>
                            </div>

                            @if(!$flat->is_aproved_by_dtp)
                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-slate-500 text-[11px] flex items-center gap-2">
                                    <i class="bi bi-info-circle text-amber-600 text-xs shrink-0"></i>
                                    <span>Verification and statutory certification is strictly conducted by Senior Town Planner (STP) authority.</span>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>
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
    </script>
</body>
</html>