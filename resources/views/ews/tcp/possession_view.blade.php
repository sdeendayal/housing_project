@php
    $isDepartment = request()->routeIs('ews.department.*') || ($user && $user->role === 'ews_department');
    $isStp = request()->routeIs('ews.stp.*') || ($user && in_array($user->role, ['ews_stp', 'stp']));
    $isGiven = ($beneficiary->is_possession_given == 1 || $beneficiary->possession_status === 'Possession Given' || $beneficiary->possession_status === 'GIVEN');
    $flatsCount = $stats['total_allotted'] ?? 4211;
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiary Possession Dossier - App #{{ $beneficiary->application_number }} | TCP Haryana</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 13px; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        @media print {
            body { background: white !important; font-size: 11px !important; }
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
        
        <!-- Top Compact Navigation Bar (Height: 44px) -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs shrink-0 h-11 px-3 sm:px-4 flex items-center justify-between">
            <!-- Left: Back Button, Emblem & Identity -->
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ $backUrl ?? route('ews.tcp.possession') }}" id="btn-back" title="Back to Physical Possession Pool"
                    class="w-7 h-7 rounded-md bg-slate-100 hover:bg-indigo-100 text-slate-700 hover:text-indigo-700 border border-slate-200 flex items-center justify-center transition-all shadow-2xs shrink-0 group">
                    <i class="bi bi-arrow-left text-xs transition-transform group-hover:-translate-x-0.5"></i>
                </a>

                <div class="flex items-center gap-1.5 min-w-0">
                    <img src="{{ asset('Haryana_emblem.png') }}" class="w-5 h-5 object-contain shrink-0" alt="Haryana Emblem">
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="text-xs font-black uppercase tracking-wide text-slate-900 hidden sm:inline">PHYSICAL POSSESSION DOSSIER:</span>
                        <span class="text-xs font-black font-mono text-indigo-700">APP #{{ $beneficiary->application_number }}</span>
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-violet-50 text-violet-700 border border-violet-200">FLAT: {{ $beneficiary->flat_no ?? '—' }}</span>
                        
                        @if($isGiven)
                            <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded text-[7.5px] font-black uppercase inline-flex items-center gap-0.5 shadow-2xs">
                                <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                <span>Possession Given</span>
                            </span>
                        @else
                            <span class="px-1.5 py-0.2 bg-amber-100 text-amber-800 border border-amber-300 rounded text-[7.5px] font-black uppercase inline-flex items-center gap-0.5 shadow-2xs">
                                <i class="bi bi-clock-history text-amber-600"></i>
                                <span>Pending Possession</span>
                            </span>
                        @endif

                        <span class="px-1.5 py-0.2 bg-indigo-100 text-indigo-800 border border-indigo-200 rounded text-[7.5px] font-black uppercase hidden md:inline">4,211 Pool</span>
                    </div>
                </div>
            </div>

            <!-- Right: Action Bar (Record Nav + Copy App # + Print) -->
            <div class="flex items-center gap-1.5 shrink-0">
                
                <!-- Prev / Next Record Arrows -->
                <div class="flex items-center rounded-md border border-slate-250 bg-slate-50 p-0.5 shadow-2xs">
                    @if($prevBeneficiary)
                        <a href="{{ route('ews.tcp.possession.show', $prevBeneficiary->secure_id ?: $prevBeneficiary->id) }}" title="Previous Record"
                            class="px-2 py-0.5 hover:bg-white text-slate-700 rounded text-[9px] font-bold flex items-center gap-0.5 transition">
                            <i class="bi bi-chevron-left text-[9px]"></i>
                            <span class="hidden md:inline">Prev</span>
                        </a>
                    @else
                        <span class="px-2 py-0.5 text-slate-300 text-[9px] font-bold flex items-center gap-0.5 cursor-not-allowed">
                            <i class="bi bi-chevron-left text-[9px]"></i>
                            <span class="hidden md:inline">Prev</span>
                        </span>
                    @endif

                    <div class="h-3 w-px bg-slate-200"></div>

                    @if($nextBeneficiary)
                        <a href="{{ route('ews.tcp.possession.show', $nextBeneficiary->secure_id ?: $nextBeneficiary->id) }}" title="Next Record"
                            class="px-2 py-0.5 hover:bg-white text-slate-700 rounded text-[9px] font-bold flex items-center gap-0.5 transition">
                            <span class="hidden md:inline">Next</span>
                            <i class="bi bi-chevron-right text-[9px]"></i>
                        </a>
                    @else
                        <span class="px-2 py-0.5 text-slate-300 text-[9px] font-bold flex items-center gap-0.5 cursor-not-allowed">
                            <span class="hidden md:inline">Next</span>
                            <i class="bi bi-chevron-right text-[9px]"></i>
                        </span>
                    @endif
                </div>

                <!-- Copy App # Badge -->
                <div class="hidden sm:flex items-center gap-1 bg-slate-100 hover:bg-slate-200 border border-slate-300/80 px-2 py-0.5 rounded cursor-pointer transition-all" onclick="copyApplicationNo()" title="Click to Copy Application Number">
                    <span class="text-[8px] font-mono font-black uppercase text-slate-500">APP #:</span>
                    <span class="text-[9.5px] font-mono font-black text-indigo-700 select-all" id="dossier-app-no">{{ $beneficiary->application_number }}</span>
                    <i class="bi bi-copy text-[9px] text-slate-400 hover:text-slate-700 ml-0.5"></i>
                </div>

                <!-- Print Button -->
                <button type="button" onclick="window.print()" title="Print Beneficiary Dossier"
                    class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-250 rounded text-[9px] font-bold flex items-center gap-1 shadow-2xs cursor-pointer transition-all">
                    <i class="bi bi-printer text-slate-500"></i>
                    <span class="hidden md:inline">Print</span>
                </button>
            </div>
        </header>

        <!-- Main Workspace (Zero-Waste High Density Viewport) -->
        <main class="flex-1 overflow-y-auto px-3 sm:px-4 py-2.5 space-y-2.5 custom-scroll" id="printable-area">
            
            <!-- Compact Hero Ribbon (Height ~38px) -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 text-white rounded-lg px-3 py-1.5 shadow-2xs border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="w-6 h-6 rounded bg-indigo-500/20 text-indigo-400 border border-indigo-400/30 flex items-center justify-center text-xs shrink-0">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-wider text-cyan-300">{{ $beneficiary->full_name }}</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-[10px] text-slate-300 font-medium">Mobile: <strong class="text-white font-mono">{{ $beneficiary->mobile_number ? substr((string)$beneficiary->mobile_number, 0, 2) . '******' . substr((string)$beneficiary->mobile_number, -2) : 'N/A' }}</strong></span>
                    <span class="text-slate-600">•</span>
                    <span class="text-[10px] font-mono text-emerald-400 font-bold">Flat: {{ $beneficiary->flat_no ?? '—' }}</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-[10px] text-slate-300"><i class="bi bi-geo-alt-fill text-rose-400 text-[9px]"></i> {{ $handover->town_name ?? $beneficiary->dist_name }}, {{ $beneficiary->dist_name }} ({{ $handover->zone_name ?? 'Haryana State' }})</span>
                </div>

                <div class="flex items-center gap-2 text-[9px] text-slate-400 font-mono shrink-0">
                    <span>ALLOTTED: <strong class="text-slate-200">{{ $beneficiary->created_at ? date('d/m/Y', strtotime($beneficiary->created_at)) : 'State Allotment Ledger' }}</strong></span>
                </div>
            </div>

            <!-- 3-Column Ultra-Dense Executive Grid ("Kam Jagah Me Jyada Content") -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">

                <!-- COLUMN 1: Beneficiary Identity & Allotment Dossier -->
                <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-150">
                            <h3 class="text-[10px] font-black uppercase tracking-wider text-slate-900 flex items-center gap-1">
                                <i class="bi bi-person-badge-fill text-indigo-600 text-xs"></i>
                                <span>Beneficiary Identity</span>
                            </h3>
                            <span class="text-[8px] font-black uppercase px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">Allottee Profile</span>
                        </div>

                        <!-- 4 Compact Key-Value Metric Boxes -->
                        <div class="grid grid-cols-2 gap-1.5 mb-2">
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Application Number</span>
                                <span class="text-sm font-black font-mono text-indigo-700 leading-none">#{{ $beneficiary->application_number }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Beneficiary Name</span>
                                <span class="text-xs font-black text-slate-900 truncate leading-none uppercase">{{ $beneficiary->full_name }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Contact Mobile</span>
                                <span class="text-xs font-bold font-mono text-slate-800 leading-none truncate">{{ $beneficiary->mobile_number ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">District</span>
                                <span class="text-xs font-bold text-slate-700 leading-none uppercase">{{ $beneficiary->dist_name }}</span>
                            </div>
                        </div>

                        <!-- Dense Data Rows -->
                        <div class="divide-y divide-slate-100 text-[10px]">
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Allotment Date:</span>
                                <span class="font-mono text-slate-700 font-bold text-[9.5px]">{{ $beneficiary->created_at ? date('d M, Y h:i A', strtotime($beneficiary->created_at)) : 'State Allotment Ledger' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Hash Badge -->
                    <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[8px] font-mono text-slate-400">
                        <span>MASTER POOL VERIFIED</span>
                        <span class="text-emerald-600 font-bold flex items-center gap-0.5"><i class="bi bi-shield-check"></i> STATE COMPLIANT</span>
                    </div>
                </div>

                <!-- COLUMN 2: Allotted Unit & Housing Scheme Spec -->
                <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-150">
                            <h3 class="text-[10px] font-black uppercase tracking-wider text-slate-900 flex items-center gap-1">
                                <i class="bi bi-building text-sky-600 text-xs"></i>
                                <span>Allotted Unit Specification</span>
                            </h3>
                            <span class="text-[8px] font-mono font-bold px-1.5 py-0.2 rounded bg-sky-50 text-sky-700 border border-sky-200">
                                Unit Details
                            </span>
                        </div>

                        <!-- Compact Key-Value Metric Boxes -->
                        <div class="grid grid-cols-2 gap-1.5 mb-2">
                            <div class="col-span-2 bg-slate-50 border border-slate-200/80 rounded p-1.5 flex items-center justify-between">
                                <div>
                                    <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Allotted Flat Code</span>
                                    <span class="text-sm font-black font-mono text-violet-700 leading-none" title="{{ $beneficiary->flat_no }}">{{ $beneficiary->flat_no ?? '—' }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[8px] font-mono font-bold bg-violet-100 text-violet-800 border border-violet-200">UNIT ALLOTTED</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Town / Location</span>
                                <span class="text-xs font-black text-slate-900 truncate leading-none uppercase">{{ $handover->town_name ?? ($builderFlat->town_name ?? $beneficiary->dist_name) }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/80 rounded p-1.5">
                                <span class="block text-[7.5px] font-bold text-slate-400 uppercase">Block / Tower</span>
                                <span class="text-xs font-bold text-slate-700 leading-none uppercase">{{ $builderFlat->block_tower_number ?? 'Block EWS' }} / {{ $builderFlat->floor ?? 'Allotted' }}</span>
                            </div>
                        </div>

                        <!-- Dense Data Rows -->
                        <div class="divide-y divide-slate-100 text-[10px]">
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Developer / Colonizer:</span>
                                <span class="font-bold text-slate-800 uppercase truncate max-w-[160px]">{{ $project->developer_name ?? ($builderFlat->developer_name ?? 'Haryana EWS Cell') }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Statutory License #:</span>
                                <span class="font-mono font-black text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">{{ $project->license_no ?? ($builderFlat->license_no ?? ($handover->license_no ?? 'LIC-HR-EWS')) }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Administrative Zone:</span>
                                <span class="font-black text-sky-700 uppercase">{{ $handover->zone_name ?? ($project->zone_name ?? ($builderFlat->zone_name ?? 'Haryana State')) }}</span>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <span class="text-slate-400 font-medium">District & Sector:</span>
                                <span class="font-bold text-slate-800 uppercase">{{ $beneficiary->dist_name }} {{ !empty($project->sector_no) ? '• Sec ' . $project->sector_no : '' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Builder Flat Blueprint Link Strip (if matched) -->
                    <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[8px] font-bold text-slate-400 uppercase">Builder Flat Record:</span>
                        @if($builderFlat)
                            <a href="{{ route('ews.tcp.flats.show', $builderFlat->id) }}" target="_blank"
                                class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 hover:border-indigo-600 rounded text-[8px] font-black uppercase inline-flex items-center gap-1 transition shadow-2xs">
                                <i class="bi bi-building-check text-indigo-500"></i>
                                <span>View Unit Blueprint</span>
                                <i class="bi bi-box-arrow-up-right text-[7px]"></i>
                            </a>
                        @else
                            <span class="text-[8px] text-slate-400 font-mono italic">Allotted Master Pool Flat</span>
                        @endif
                    </div>
                </div>

                <!-- COLUMN 3: Physical Possession Handover Audit & Evidence -->
                <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-150">
                            <h3 class="text-[10px] font-black uppercase tracking-wider text-slate-900 flex items-center gap-1">
                                <i class="bi bi-key-fill {{ $isGiven ? 'text-emerald-600' : 'text-amber-500' }} text-xs"></i>
                                <span>Physical Possession Audit</span>
                            </h3>
                            @if($isGiven)
                                <span class="text-[8px] font-black uppercase px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Certified Handover
                                </span>
                            @else
                                <span class="text-[8px] font-black uppercase px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 border border-amber-200">
                                    Awaiting Handover
                                </span>
                            @endif
                        </div>

                        @if($isGiven)
                            <!-- Possession Handover Confirmed Box -->
                            <div class="bg-emerald-50/70 border border-emerald-200 rounded-lg p-2 mb-2 space-y-1.5">
                                <div class="flex items-center justify-between text-[9.5px]">
                                    <span class="font-black text-emerald-900 flex items-center gap-1">
                                        <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                        <span>Possession Successfully Handed Over</span>
                                    </span>
                                    <span class="font-mono text-emerald-800 font-black text-[9px] bg-emerald-100 px-1.5 py-0.2 rounded border border-emerald-300">
                                        {{ $handover->possession_given_at ? date('d M, Y', strtotime($handover->possession_given_at)) : 'Certified' }}
                                    </span>
                                </div>
                                
                                <div class="text-[8.5px] text-slate-600 space-y-1 pt-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 font-medium">Verifying Officer:</span>
                                        <span class="font-bold text-slate-800">{{ $stpUser->name ?? ($handover->stp_user_id ? 'STP Officer #' . $handover->stp_user_id : 'Special Town Planning Cell') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 font-medium">Timestamp:</span>
                                        <span class="font-mono text-slate-700 font-bold">{{ $handover->possession_given_at ? date('d/m/Y h:i A', strtotime($handover->possession_given_at)) : 'Recorded' }}</span>
                                    </div>
                                    @if(!empty($handover->remarks))
                                        <div class="bg-white/80 p-1.5 rounded border border-emerald-200/60 text-[9px] text-slate-700">
                                            <strong>Remarks:</strong> {{ $handover->remarks }}
                                        </div>
                                    @endif
                                </div>

                                <!-- GPS Geolocation Strip -->
                                @if(!empty($handover->latitude) && !empty($handover->longitude))
                                    <div class="flex items-center justify-between pt-1 border-t border-emerald-200/60 text-[8px]">
                                        <div class="font-mono text-emerald-900 font-bold flex items-center gap-1">
                                            <i class="bi bi-geo-alt-fill text-rose-500"></i>
                                            <span>GPS: {{ number_format((float)$handover->latitude, 4) }}, {{ number_format((float)$handover->longitude, 4) }}</span>
                                        </div>
                                        <a href="https://www.google.com/maps?q={{ $handover->latitude }},{{ $handover->longitude }}" target="_blank"
                                            class="px-1.5 py-0.2 bg-white hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded font-bold uppercase transition flex items-center gap-0.5 shadow-2xs">
                                            <span>Open Map</span>
                                            <i class="bi bi-box-arrow-up-right text-[6.5px]"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Uploaded Digital Evidence Strip (Dense) -->
                            <div class="space-y-1.5">
                                <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Uploaded Handover Evidence:</span>
                                
                                <div class="grid grid-cols-2 gap-1.5">
                                    <!-- Signed Possession Letter PDF -->
                                    <div class="bg-slate-50 border border-slate-200 rounded p-1.5 flex flex-col justify-between">
                                        <div>
                                            <span class="block text-[7px] font-bold text-slate-400 uppercase">Signed Possession Letter</span>
                                            <span class="block text-[8.5px] font-bold text-slate-800 truncate" title="{{ $handover->possession_letter_original_name ?? 'possession_letter.pdf' }}">
                                                {{ $handover->possession_letter_original_name ?? 'Possession Letter' }}
                                            </span>
                                        </div>
                                        <div class="mt-1 pt-1 border-t border-slate-200 flex items-center justify-between">
                                            @if(!empty($handover->possession_letter_path))
                                                <a href="{{ asset('storage/' . $handover->possession_letter_path) }}" target="_blank"
                                                    class="px-1.5 py-0.2 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 hover:border-rose-600 rounded text-[7.5px] font-black uppercase inline-flex items-center gap-0.5 transition shadow-2xs">
                                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                                <span>View PDF</span>
                                                </a>
                                            @else
                                                <span class="text-[7.5px] text-slate-400 italic font-mono">Not Uploaded</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Beneficiary Flat Site Photo -->
                                    <div class="bg-slate-50 border border-slate-200 rounded p-1.5 flex flex-col justify-between">
                                        <div>
                                            <span class="block text-[7px] font-bold text-slate-400 uppercase">Site Handover Photo</span>
                                            <span class="block text-[8.5px] font-bold text-slate-800 truncate">Beneficiary at Flat</span>
                                        </div>
                                        <div class="mt-1 pt-1 border-t border-slate-200 flex items-center justify-between">
                                            @if(!empty($handover->beneficiary_flat_photo_path))
                                                <a href="{{ asset('storage/' . $handover->beneficiary_flat_photo_path) }}" target="_blank"
                                                    class="px-1.5 py-0.2 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 hover:border-indigo-600 rounded text-[7.5px] font-black uppercase inline-flex items-center gap-0.5 transition shadow-2xs">
                                                <i class="bi bi-image-fill"></i>
                                                <span>View Photo</span>
                                                </a>
                                            @else
                                                <span class="text-[7.5px] text-slate-400 italic font-mono">Not Uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Pending Possession State Box -->
                            <div class="bg-amber-50/80 border border-amber-200 rounded-lg p-2.5 mb-2 space-y-2">
                                <div class="flex items-center justify-between text-[9.5px]">
                                    <span class="font-black text-amber-900 flex items-center gap-1">
                                        <i class="bi bi-clock-history text-amber-600"></i>
                                        <span>Awaiting Physical Handover</span>
                                    </span>
                                    <span class="font-mono text-amber-800 font-bold text-[8.5px] bg-amber-100 px-1.5 py-0.2 rounded border border-amber-300">
                                        PENDING
                                    </span>
                                </div>
                                
                                <p class="text-[9px] text-slate-600 leading-relaxed">
                                    This beneficiary is legally allotted Flat <strong class="text-violet-700 font-mono">{{ $beneficiary->flat_no ?? '—' }}</strong> under the 4,211 master pool. On-site inspection, biometric key delivery, and upload of signed handover certificate are currently pending.
                                </p>

                                <div class="bg-white/80 p-2 rounded border border-amber-200 text-[8.5px] space-y-1">
                                    <div class="flex items-center justify-between text-slate-500">
                                        <span>Inspection Authority:</span>
                                        <span class="font-bold text-slate-800">STP / DTP Cell ({{ $beneficiary->dist_name }})</span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-500">
                                        <span>Possession Letter:</span>
                                        <span class="font-mono text-amber-700 font-bold">Pending Generation</span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-500">
                                        <span>Flat Site Photo:</span>
                                        <span class="font-mono text-amber-700 font-bold">Pending Handover Visit</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Audit Trail Bottom Strip -->
                    <div class="mt-2 pt-1.5 border-t border-slate-150">
                        <div class="flex items-center justify-between text-[8px] font-mono text-slate-400">
                            <span>GOVERNMENT OF HARYANA</span>
                            <span class="text-indigo-600 font-bold flex items-center gap-0.5">
                                <i class="bi bi-shield-lock-fill"></i>
                                <span>TCP REGULATORY CELL</span>
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </main>

        <!-- Compact Bottom Status Footer (Height: 22px) -->
        <footer class="bg-white border-t border-slate-200 py-1 px-4 text-center shrink-0">
            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">
                Town and Country Planning Department • Government of Haryana • EWS Allotted Beneficiaries Physical Possession Ledger
            </p>
        </footer>

    </div>

    <!-- Script for copy application number & notifications -->
    <script>
        function copyApplicationNo() {
            const appNo = document.getElementById('dossier-app-no').innerText;
            navigator.clipboard.writeText(appNo).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Copied Application #: ' + appNo,
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }
    </script>
</body>
</html>
