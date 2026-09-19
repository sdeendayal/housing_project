<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Handover Physical Possession - #{{ $beneficiary->application_number }} - EWS Portal</title>
    
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
        .code-font { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dense-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="h-full flex overflow-hidden">

    <!-- LEFT SIDEBAR -->
    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0 select-none z-20">
        <!-- Brand Header -->
        <div class="h-14 flex items-center gap-3 px-5 border-b border-slate-800 bg-slate-950/80">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-400 to-sky-600 flex items-center justify-center shadow-lg shadow-sky-500/20">
                <i class="bi bi-key-fill text-white text-xs"></i>
            </div>
            <div>
                <h1 class="text-xs font-black tracking-tight text-white uppercase">EWS Portal</h1>
                <p class="text-[8px] text-slate-400 font-mono tracking-widest uppercase">Physical Possession</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 px-3 py-4 space-y-4 overflow-y-auto custom-scroll">
            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Possession Management</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.possession.index') }}" 
                        class="flex items-center gap-2 px-3 py-2 rounded-lg bg-sky-600 text-white font-bold shadow-md shadow-sky-600/20 text-xs transition-all">
                        <i class="bi bi-key text-white text-sm"></i>
                        <span>Physical Possession</span>
                    </a>
                    <a href="{{ route('ews.developer.dashboard') }}" 
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-speedometer2 text-slate-400 text-sm"></i>
                        <span>STP Dashboard</span>
                    </a>
                    <a href="{{ route('ews.developer.dashboard', ['view' => 'district']) }}" 
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-building text-slate-400 text-sm"></i>
                        <span>{{ $displayZoneName }} Flats</span>
                    </a>
                </div>
            </div>

            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Audit & Tracking</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.possession.logs') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-clock-history text-slate-400 text-sm"></i>
                        <span>Possession Audit Logs</span>
                    </a>
                    <a href="{{ route('ews.developer.logs') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-journal-text text-slate-400 text-sm"></i>
                        <span>General STP Logs</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Info & Logout -->
        <div class="p-3 border-t border-slate-800 bg-slate-950 flex flex-col gap-2 shrink-0">
            <div class="flex items-center gap-2 px-1">
                <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs text-sky-400 font-bold">
                    <i class="bi bi-person"></i>
                </div>
                <div class="truncate">
                    <div class="text-[11px] font-bold text-white truncate">{{ $user->name }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">{{ $displayZoneName }}</div>
                </div>
            </div>
            <a href="{{ route('ews.developer.logout') }}" class="w-full py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg text-[10px] font-bold uppercase transition-all flex items-center justify-center gap-1.5 border border-red-500/20">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout Session</span>
            </a>
        </div>
    </aside>

    <!-- RIGHT MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        <!-- Compact Top Navbar -->
        <header class="h-14 bg-white border-b border-slate-200 px-5 flex items-center justify-between shrink-0 shadow-xs z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.developer.possession.index') }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-sm font-bold transition-all border border-slate-200 shadow-xs" title="Back to Allotted Beneficiaries">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-tight flex items-center gap-1.5">
                            Handover Physical Possession
                        </h2>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">#{{ $beneficiary->application_number }}</span>
                        @if((int)($beneficiary->is_possession_given ?? 0) === 1)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="bi bi-check-circle-fill text-emerald-500"></i> Given
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                <i class="bi bi-hourglass-split text-amber-500"></i> Pending
                            </span>
                        @endif
                    </div>
                    <p class="text-[10px] text-slate-500 font-medium">Beneficiary: <strong class="text-slate-700">{{ $beneficiary->full_name }}</strong> &bull; {{ $beneficiary->dist_name }} District</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('ews.developer.possession.index') }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-300 shadow-xs">
                    <i class="bi bi-list-ul"></i>
                    <span>All Beneficiaries</span>
                </a>
            </div>
        </header>

        <!-- Main Body (High Information Density Layout) -->
        <main class="flex-1 overflow-y-auto p-4 custom-scroll">
            <div class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-start">
                
                <!-- LEFT COLUMN: Dense Information & Property Summary (5 Columns) -->
                <div class="lg:col-span-5 space-y-3">

                    <!-- 1. Beneficiary Personal & Allotment Card -->
                    <div class="dense-card p-3.5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-md bg-sky-50 text-sky-600 flex items-center justify-center text-xs font-bold">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <h3 class="text-xs font-black uppercase text-slate-800 tracking-wide">Beneficiary Profile</h3>
                            </div>
                            <div class="flex items-center gap-1 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-[10px] font-mono font-bold text-slate-700 shadow-2xs" title="Unique Secure ID: {{ $beneficiary->secure_id }}">
                                <i class="bi bi-shield-lock-fill text-sky-600 text-[10px]"></i>
                                <span>{{ substr($beneficiary->secure_id, 0, 8) . '...' . substr($beneficiary->secure_id, -6) }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $beneficiary->secure_id }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Secure ID Copied!', showConfirmButton:false, timer:1500});" class="text-slate-400 hover:text-sky-600 ml-0.5" title="Copy Full 32-digit Secure ID">
                                    <i class="bi bi-copy text-[9.5px]"></i>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="block text-[9.5px] uppercase font-bold text-slate-400">Beneficiary Name</span>
                                <span class="font-extrabold text-slate-900 uppercase">{{ $beneficiary->full_name }}</span>
                            </div>

                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="block text-[9.5px] uppercase font-bold text-slate-400">Application Number</span>
                                <span class="font-mono font-bold text-slate-800">#{{ $beneficiary->application_number }}</span>
                            </div>

                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="block text-[9.5px] uppercase font-bold text-slate-400">Mobile Number</span>
                                @if($beneficiary->mobile_number)
                                    <a href="tel:{{ $beneficiary->mobile_number }}" class="font-mono font-bold text-sky-700 hover:underline inline-flex items-center gap-1">
                                        <i class="bi bi-telephone text-[10px]"></i> {{ $beneficiary->mobile_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </div>

                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="block text-[9.5px] uppercase font-bold text-slate-400">Aadhaar (Masked)</span>
                                <span class="font-mono font-bold text-slate-700">
                                    {{ $beneficiary->aadhar_no ? (substr($beneficiary->aadhar_no, 0, 4) . ' XXXX ' . substr($beneficiary->aadhar_no, -4)) : 'Not Available' }}
                                </span>
                            </div>

                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="block text-[9.5px] uppercase font-bold text-slate-400">District / Zone</span>
                                <span class="font-bold text-slate-800">{{ $beneficiary->dist_name }} ({{ $displayZoneName }})</span>
                            </div>

                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="block text-[9.5px] uppercase font-bold text-slate-400">Property / Phase</span>
                                <span class="font-bold text-slate-800">{{ $beneficiary->property_type ?? 'EWS Flat' }} &bull; Phase {{ $beneficiary->phase ?? '8' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Flat Breakdown & Project Card -->
                    <div class="dense-card p-3.5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                    <i class="bi bi-building"></i>
                                </div>
                                <h3 class="text-xs font-black uppercase text-slate-800 tracking-wide">Flat & Project Architecture</h3>
                            </div>
                            <span class="text-[10px] font-mono text-indigo-600 font-bold">{{ $project ? $project->project_abbr : 'EWS' }}</span>
                        </div>

                        <!-- Flat Number Banner -->
                        <div class="p-2.5 rounded-lg bg-gradient-to-r from-sky-50 to-indigo-50 border border-sky-200/80 flex items-center justify-between mb-2.5">
                            <div>
                                <span class="block text-[9px] uppercase font-bold text-sky-600 tracking-wider">Allotted Flat Code</span>
                                <span class="font-mono font-black text-sm text-sky-950 tracking-tight">{{ $beneficiary->flat_no }}</span>
                            </div>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $beneficiary->flat_no }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Flat code copied!', showConfirmButton:false, timer:1500});" class="px-2 py-1 bg-white hover:bg-sky-100 text-sky-700 rounded border border-sky-300 text-[10px] font-bold shadow-xs flex items-center gap-1 transition-all">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>

                        <!-- Single-line Floor, Block, Unit badges -->
                        <div class="grid grid-cols-3 gap-2 mb-2.5 text-center">
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200">
                                <span class="block text-[9px] uppercase font-bold text-slate-400">Floor</span>
                                <span class="font-mono font-extrabold text-slate-900 text-xs">{{ $flatBreakdown['floor'] }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-indigo-50/70 border border-indigo-200">
                                <span class="block text-[9px] uppercase font-bold text-indigo-500">Block / Tower</span>
                                <span class="font-mono font-extrabold text-indigo-900 text-xs">{{ $flatBreakdown['block'] }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-amber-50/70 border border-amber-200">
                                <span class="block text-[9px] uppercase font-bold text-amber-600">Flat / Unit</span>
                                <span class="font-mono font-extrabold text-amber-900 text-xs">{{ $flatBreakdown['unit'] }}</span>
                            </div>
                        </div>

                        <div class="text-[11px] text-slate-600 flex items-center justify-between p-2 rounded bg-slate-50 border border-slate-100">
                            <span>Project Name:</span>
                            <strong class="text-slate-900 font-bold truncate max-w-[280px]" title="{{ $project->name ?? 'N/A' }}">{{ $project->name ?? 'Not Assigned / General' }}</strong>
                        </div>
                    </div>

                    <!-- 3. Current Possession State & Audit History Card -->
                    <div class="dense-card p-3.5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <h3 class="text-xs font-black uppercase text-slate-800 tracking-wide">Possession State & History</h3>
                            </div>
                            <span class="text-[10px] font-mono font-bold text-slate-500">{{ count($auditLogs) }} Event(s)</span>
                        </div>

                        <!-- Current State Highlights -->
                        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="block text-[9.5px] uppercase font-bold text-slate-400">Handover Date</span>
                                <span class="font-mono font-bold text-slate-800">
                                    {{ $possession && $possession->possession_given_at ? $possession->possession_given_at->format('d M Y, h:i A') : 'Not Given Yet' }}
                                </span>
                            </div>
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="block text-[9.5px] uppercase font-bold text-slate-400">Action Officer</span>
                                <span class="font-bold text-slate-800 truncate block">
                                    {{ $possession->stp_user_name ?? ($possession ? 'STP User' : 'Pending') }}
                                </span>
                            </div>
                        </div>

                        <!-- Compact Timeline -->
                        <div class="space-y-2 max-h-48 overflow-y-auto custom-scroll pr-1">
                            @forelse($auditLogs as $log)
                                <div class="p-2 rounded-md bg-slate-50 border border-slate-200/80 text-[11px] flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-1.5 font-bold text-slate-800">
                                            <span class="px-1.5 py-0.2 rounded text-[9.5px] uppercase font-black {{ $log->new_status === 'GIVEN' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $log->new_status }}
                                            </span>
                                            <span>{{ $log->action }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            By <strong class="text-slate-700">{{ $log->stp_user_name ?: 'System' }}</strong>
                                            @if($log->latitude && $log->longitude)
                                                &bull; <a href="https://maps.google.com/?q={{ $log->latitude }},{{ $log->longitude }}" target="_blank" class="text-sky-600 hover:underline"><i class="bi bi-geo-alt"></i> GPS</a>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-[9.5px] font-mono text-slate-400 whitespace-nowrap">
                                        {{ $log->created_at ? $log->created_at->format('d M, h:i A') : '' }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-3 text-slate-400 text-xs italic">
                                    No audit history recorded yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Physical Possession Handover Action Form / Frozen Summary Card (7 Columns) -->
                <div class="lg:col-span-7">
                    @php
                        $isGiven = (int)($beneficiary->is_possession_given ?? 0) === 1 || ($possession && $possession->possession_status === 'GIVEN');
                    @endphp

                    @if($isGiven)
                        <!-- ================================================================= -->
                        <!-- FROZEN / LOCKED: POSSESSION COMPLETED & VERIFIED SUMMARY CARD     -->
                        <!-- ================================================================= -->
                        <div class="dense-card p-4 shadow-sm border-emerald-200">
                            <!-- Card Header -->
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-lg shadow-md shadow-emerald-600/20">
                                        <i class="bi bi-shield-fill-check"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-black uppercase tracking-wide text-slate-900">Physical Possession Completed</h3>
                                            <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                Locked / Frozen
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-slate-500">Possession has been officially handed over. Records are locked against further modification.</p>
                                    </div>
                                </div>

                                <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-circle-fill text-emerald-500"></i> GIVEN
                                </span>
                            </div>

                            <!-- Verified Key Meta Details (Grid) -->
                            <div class="grid grid-cols-3 gap-2 text-xs mb-3.5">
                                <div class="p-2.5 rounded-lg bg-emerald-50/60 border border-emerald-100">
                                    <span class="block text-[9px] uppercase font-bold text-emerald-700">Handover Timestamp</span>
                                    <span class="font-mono font-black text-slate-900 text-xs">
                                        {{ $possession && $possession->possession_given_at ? $possession->possession_given_at->format('d M Y, h:i A') : ($beneficiary->possession_given_at ? date('d M Y, h:i A', strtotime($beneficiary->possession_given_at)) : 'Verified') }}
                                    </span>
                                </div>

                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                                    <span class="block text-[9px] uppercase font-bold text-slate-400">Verifying Officer</span>
                                    <span class="font-bold text-slate-900 text-xs truncate block" title="{{ $possession->stp_user_name ?? $user->name }}">
                                        {{ $possession->stp_user_name ?? $user->name }}
                                    </span>
                                </div>

                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                                    <span class="block text-[9px] uppercase font-bold text-slate-400">Verification ID</span>
                                    <span class="font-mono font-bold text-slate-800 text-xs">
                                        #POSS-{{ $possession ? $possession->id : $beneficiary->id }}
                                    </span>
                                </div>
                            </div>

                            <!-- Documents & Evidence Summary -->
                            <div class="space-y-3 mb-3.5">
                                <!-- Signed Possession Letter Box -->
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                    <div class="flex items-center gap-3 truncate">
                                        <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0 border border-rose-200">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </div>
                                        <div class="truncate">
                                            <span class="block text-[9.5px] uppercase font-bold text-slate-400">Signed Possession Letter</span>
                                            <span class="font-bold text-slate-900 text-xs truncate block max-w-[320px]">
                                                {{ $possession->possession_letter_original_name ?: 'Signed_Possession_Letter.pdf' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($possession && $possession->possession_letter_path)
                                        <a href="/storage/{{ ltrim($possession->possession_letter_path, '/') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition-all whitespace-nowrap">
                                            <i class="bi bi-eye"></i> View PDF
                                        </a>
                                    @else
                                        <span class="text-xs font-mono text-slate-400">Not Uploaded</span>
                                    @endif
                                </div>

                                <!-- Beneficiary Photo + GPS Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Beneficiary Photo Box -->
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                        <span class="block text-[9.5px] uppercase font-bold text-slate-400 mb-1.5 flex items-center gap-1">
                                            <i class="bi bi-camera-fill text-sky-500"></i> Beneficiary Photo at Flat
                                        </span>
                                        <div class="h-44 rounded-lg border border-slate-200 bg-slate-900/5 overflow-hidden relative flex items-center justify-center">
                                            @if($possession && $possession->beneficiary_flat_photo_path)
                                                <img src="/storage/{{ ltrim($possession->beneficiary_flat_photo_path, '/') }}" alt="Flat Photo" class="w-full h-full object-cover" />
                                                <a href="/storage/{{ ltrim($possession->beneficiary_flat_photo_path, '/') }}" target="_blank" class="absolute inset-0 bg-slate-900/40 hover:bg-slate-900/60 flex items-center justify-center text-white text-xs font-bold opacity-0 hover:opacity-100 transition-opacity gap-1.5">
                                                    <i class="bi bi-zoom-in text-sm"></i> View Full Photo
                                                </a>
                                            @else
                                                <div class="text-center text-slate-400">
                                                    <i class="bi bi-image text-2xl"></i>
                                                    <div class="text-[10px] mt-1">No photo available</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- GPS Geo-Location Box -->
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col justify-between">
                                        <div>
                                            <span class="block text-[9.5px] uppercase font-bold text-slate-400 mb-1.5 flex items-center gap-1">
                                                <i class="bi bi-geo-alt-fill text-rose-500"></i> Geo-Tagged GPS Coordinates
                                            </span>
                                            
                                            <div class="space-y-2 mb-3">
                                                <div class="p-2 rounded bg-white border border-slate-200 flex items-center justify-between text-xs">
                                                    <span class="text-[10px] uppercase font-bold text-slate-400">Latitude:</span>
                                                    <span class="font-mono font-bold text-slate-900">{{ $possession->latitude ?? '-' }}</span>
                                                </div>
                                                <div class="p-2 rounded bg-white border border-slate-200 flex items-center justify-between text-xs">
                                                    <span class="text-[10px] uppercase font-bold text-slate-400">Longitude:</span>
                                                    <span class="font-mono font-bold text-slate-900">{{ $possession->longitude ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if($possession && $possession->latitude && $possession->longitude)
                                            <a href="https://maps.google.com/?q={{ $possession->latitude }},{{ $possession->longitude }}" target="_blank" class="w-full py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-all">
                                                <i class="bi bi-geo-alt"></i> Open in Google Maps
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Remarks Box -->
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="block text-[9.5px] uppercase font-bold text-slate-400 mb-1">Remarks / Handover Notes</span>
                                    <div class="text-xs text-slate-800 bg-white p-2.5 rounded-lg border border-slate-200 min-h-[44px]">
                                        {{ $possession->remarks ?: 'No additional remarks entered during handover.' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Action Bar for Completed State -->
                            <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                                <a href="{{ route('ews.developer.possession.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all flex items-center gap-1.5">
                                    <i class="bi bi-arrow-left"></i> Back to Beneficiaries
                                </a>

                                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-all flex items-center gap-1.5 shadow-xs">
                                    <i class="bi bi-printer"></i> Print Summary
                                </button>
                            </div>
                        </div>

                    @else
                        <!-- ================================================================= -->
                        <!-- PENDING: FIRST TIME SUBMISSION FORM                               -->
                        <!-- ================================================================= -->
                        <div class="dense-card p-4 shadow-sm border-sky-100">
                            <!-- Form Header -->
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center text-sm shadow-md shadow-sky-600/20">
                                        <i class="bi bi-file-earmark-check-fill"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-black uppercase tracking-wide text-slate-900">Physical Possession Submission</h3>
                                        <p class="text-[10px] text-slate-500">Submit physical handover with signed possession letter, flat photo & live GPS</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    Web Module v2.0
                                </span>
                            </div>

                            <!-- Possession Form -->
                            <form id="possession-form" enctype="multipart/form-data" class="space-y-3.5">
                                @csrf

                                <!-- Status Selection Button Group -->
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                        Possession Handover Status <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="possession_status" value="GIVEN" class="sr-only peer" checked>
                                            <div class="p-2.5 rounded-lg border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/70 text-slate-600 peer-checked:text-emerald-900 flex items-center gap-2 transition-all shadow-xs">
                                                <div class="w-6 h-6 rounded-full bg-slate-200 peer-checked:bg-emerald-500 text-white flex items-center justify-center text-xs">
                                                    <i class="bi bi-check-lg"></i>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black uppercase">Possession Given</div>
                                                    <div class="text-[9.5px] text-slate-500">Letter & photo mandatory</div>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="possession_status" value="PENDING" class="sr-only peer">
                                            <div class="p-2.5 rounded-lg border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50/70 text-slate-600 peer-checked:text-amber-900 flex items-center gap-2 transition-all shadow-xs">
                                                <div class="w-6 h-6 rounded-full bg-slate-200 peer-checked:bg-amber-500 text-white flex items-center justify-center text-xs">
                                                    <i class="bi bi-hourglass-split"></i>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black uppercase">Hold / Pending</div>
                                                    <div class="text-[9.5px] text-slate-500">No action needed</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Possession Given Content Section -->
                                <div id="possession-given-section" class="space-y-3.5">
                                    <!-- Document 1: Signed Possession Letter (PDF) -->
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="bi bi-file-earmark-pdf-fill text-rose-500"></i>
                                                <span>Signed Possession Letter</span>
                                                <span class="text-rose-500 text-xs">*</span>
                                            </label>
                                            <span class="text-[10px] text-slate-400 font-medium">PDF only (Max: 500 KB)</span>
                                        </div>

                                        <div class="relative flex items-center">
                                            <input type="file" id="possession_letter" name="possession_letter" accept="application/pdf" class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-sky-600 file:text-white hover:file:bg-sky-700 file:cursor-pointer cursor-pointer border border-slate-300 rounded-lg p-1 bg-white" />
                                        </div>
                                        <div id="letter-file-info" class="text-[10.5px] text-slate-500 mt-1 hidden"></div>
                                    </div>

                                    <!-- Document 2: Photo of Beneficiary at Flat -->
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="bi bi-camera-fill text-sky-500"></i>
                                                <span>Beneficiary Photo at Flat</span>
                                                <span class="text-rose-500 text-xs">*</span>
                                            </label>
                                            <span class="text-[10px] text-slate-400 font-medium">JPEG / PNG (Max: 500 KB)</span>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex-1">
                                                <input type="file" id="beneficiary_flat_photo" name="beneficiary_flat_photo" accept="image/jpeg,image/png,image/jpg" capture="environment" class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-sky-600 file:text-white hover:file:bg-sky-700 file:cursor-pointer cursor-pointer border border-slate-300 rounded-lg p-1 bg-white" />
                                                <p class="text-[10px] text-slate-400 mt-1">Photo should clearly show beneficiary standing in front of the assigned flat (Max: 500 KB).</p>
                                            </div>

                                            <!-- Thumbnail preview container -->
                                            <div id="photo-preview-wrap" class="w-16 h-16 rounded-lg border border-slate-300 bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden relative">
                                                <img id="photo-preview-img" src="" alt="" class="w-full h-full object-cover hidden" />
                                                <i id="photo-preview-placeholder" class="bi bi-image text-slate-400 text-xl"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- GPS Geo-Location Auto Capture -->
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="bi bi-geo-alt-fill text-rose-500"></i>
                                                <span>GPS Coordinates (Geo-Tagging)</span>
                                                <span class="text-rose-500 text-xs">*</span>
                                            </label>
                                            <button type="button" onclick="detectLiveGps()" id="btn-detect-gps" class="px-2.5 py-1 rounded bg-sky-600 hover:bg-sky-700 text-white text-[10.5px] font-bold flex items-center gap-1 shadow-xs transition-all">
                                                <i class="bi bi-crosshair"></i> Auto-Detect Live GPS
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <span class="block text-[9.5px] uppercase font-bold text-slate-400 mb-0.5">Latitude</span>
                                                <input type="text" id="latitude" name="latitude" value="" placeholder="e.g. 28.89551" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-mono text-xs font-bold text-slate-800 focus:ring-1 focus:ring-sky-500 focus:outline-none" required />
                                            </div>
                                            <div>
                                                <span class="block text-[9.5px] uppercase font-bold text-slate-400 mb-0.5">Longitude</span>
                                                <input type="text" id="longitude" name="longitude" value="" placeholder="e.g. 76.60661" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-mono text-xs font-bold text-slate-800 focus:ring-1 focus:ring-sky-500 focus:outline-none" required />
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between mt-1 text-[10px]">
                                            <span id="gps-status-msg" class="text-slate-400">Click "Auto-Detect" while standing at the site.</span>
                                            <a id="gps-maps-link" href="#" target="_blank" class="text-sky-600 hover:underline font-bold hidden">
                                                <i class="bi bi-box-arrow-up-right"></i> Verify on Google Maps
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Remarks / Handover Notes -->
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label for="remarks" class="block text-xs font-bold text-slate-700">
                                                Remarks / Field Notes <span class="text-slate-400 font-normal">(Optional)</span>
                                            </label>
                                            <span id="remarks-word-count" class="text-[10px] font-mono font-semibold text-slate-400">0 / 1000 words</span>
                                        </div>
                                        <textarea id="remarks" name="remarks" rows="3" placeholder="Enter any verification details or key handover notes (up to 1000 words)..." class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white text-xs text-slate-800 focus:ring-1 focus:ring-sky-500 focus:outline-none"></textarea>
                                    </div>

                                    <!-- Sticky Submit Action Buttons -->
                                    <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-200">
                                        <a href="{{ route('ews.developer.possession.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all">
                                            Cancel
                                        </a>

                                        <button type="submit" id="btn-submit-possession" class="px-5 py-2 rounded-lg bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-sky-600/20 transition-all flex items-center gap-2">
                                            <i class="bi bi-check2-circle text-sm"></i>
                                            <span>Save & Complete Handover</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Notice shown when Hold / Pending is selected -->
                                <div id="possession-pending-notice" class="hidden p-6 rounded-xl bg-amber-50/70 border border-amber-200 text-center space-y-2 animate-in fade-in duration-150">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-lg">
                                        <i class="bi bi-pause-circle-fill"></i>
                                    </div>
                                    <div class="text-xs font-black text-amber-900 uppercase">Hold / Pending Selected</div>
                                    <p class="text-[11px] text-amber-700 max-w-sm mx-auto">Physical possession is kept on hold / pending. No documents or submit action required at this time.</p>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>

    <!-- SCRIPT FOR GPS DETECTION, FILE PREVIEWS & FORM SUBMIT -->
    <script>
        // Toggle fields between GIVEN and PENDING (Hold)
        function toggleStatusFields() {
            const status = $('input[name="possession_status"]:checked').val();
            if (status === 'PENDING') {
                $('#possession-given-section').hide();
                $('#possession-pending-notice').removeClass('hidden').show();
                $('#latitude').prop('required', false);
                $('#longitude').prop('required', false);
            } else {
                $('#possession-given-section').show();
                $('#possession-pending-notice').addClass('hidden').hide();
                $('#latitude').prop('required', true);
                $('#longitude').prop('required', true);
            }
        }

        $('input[name="possession_status"]').on('change', toggleStatusFields);
        $(document).ready(function() {
            toggleStatusFields();
        });

        // Live GPS Geolocation
        function detectLiveGps() {
            const btn = $('#btn-detect-gps');
            const statusMsg = $('#gps-status-msg');
            const mapsLink = $('#gps-maps-link');

            if (!navigator.geolocation) {
                Swal.fire({
                    icon: 'error',
                    title: 'GPS Not Supported',
                    text: 'Your browser or device does not support GPS Geolocation.'
                });
                return;
            }

            btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Detecting GPS...');
            statusMsg.text('Fetching current satellite coordinates, please allow location access...');

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    const lat = pos.coords.latitude.toFixed(6);
                    const lng = pos.coords.longitude.toFixed(6);
                    const acc = Math.round(pos.coords.accuracy);

                    $('#latitude').val(lat);
                    $('#longitude').val(lng);

                    statusMsg.html(`<span class="text-emerald-600 font-bold"><i class="bi bi-check-circle"></i> GPS Locked (Accuracy: ~${acc}m)</span>`);
                    mapsLink.attr('href', `https://maps.google.com/?q=${lat},${lng}`).removeClass('hidden');

                    btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill"></i> GPS Captured');
                },
                function (err) {
                    btn.prop('disabled', false).html('<i class="bi bi-crosshair"></i> Auto-Detect Live GPS');
                    statusMsg.html('<span class="text-rose-600 font-bold"><i class="bi bi-exclamation-triangle"></i> Location permission denied or timed out.</span>');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Location Permission Denied',
                        text: 'Please allow location permission in your browser or enter the coordinates manually.'
                    });
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        }

        // Live Photo Preview (Max: 500 KB)
        $('#beneficiary_flat_photo').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 500 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Photo Size Exceeded',
                        text: `Beneficiary flat photo size must not exceed 500 KB. Current file is ${Math.round(file.size / 1024)} KB. Please compress the image and try again.`,
                        confirmButtonColor: '#0284c7'
                    });
                    $(this).val('');
                    $('#photo-preview-img').addClass('hidden');
                    $('#photo-preview-placeholder').removeClass('hidden');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(evt) {
                    $('#photo-preview-placeholder').addClass('hidden');
                    $('#photo-preview-img').attr('src', evt.target.result).removeClass('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Letter File Size Validation (Max: 500 KB)
        $('#possession_letter').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 500 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'PDF Size Exceeded',
                        text: `Possession letter PDF must not exceed 500 KB. Current file is ${Math.round(file.size / 1024)} KB.`,
                        confirmButtonColor: '#0284c7'
                    });
                    $(this).val('');
                    $('#letter-file-info').addClass('hidden');
                    return;
                }
                $('#letter-file-info').removeClass('hidden').html(`<i class="bi bi-file-earmark-check text-emerald-600"></i> Selected: <strong>${file.name}</strong> (${Math.round(file.size / 1024)} KB)`);
            }
        });

        // Remarks 1000 Words Counter
        function getRemarksWordCount() {
            const text = $('#remarks').val().trim();
            const words = text ? text.split(/\s+/).length : 0;
            const counter = $('#remarks-word-count');
            if (words > 1000) {
                counter.html(`<span class="text-rose-600 font-bold">${words} / 1000 words (Limit Exceeded!)</span>`);
            } else {
                counter.html(`${words} / 1000 words`);
            }
            return words;
        }

        $('#remarks').on('input', getRemarksWordCount);
        getRemarksWordCount();

        // AJAX Form Submission
        $('#possession-form').on('submit', function (e) {
            e.preventDefault();

            const status = $('input[name="possession_status"]:checked').val();
            const lat = $('#latitude').val().trim();
            const lng = $('#longitude').val().trim();
            const hasExistingLetter = {{ ($possession && $possession->possession_letter_path) ? 'true' : 'false' }};
            const hasExistingPhoto = {{ ($possession && $possession->beneficiary_flat_photo_path) ? 'true' : 'false' }};
            const letterInput = $('#possession_letter')[0].files.length;
            const photoInput = $('#beneficiary_flat_photo')[0].files.length;

            if (status === 'GIVEN') {
                if (!hasExistingLetter && letterInput === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Possession Letter Required',
                        text: 'Please select and upload the signed possession letter in PDF format (Max: 500 KB).',
                        confirmButtonColor: '#0284c7'
                    });
                    return;
                }
                if (!hasExistingPhoto && photoInput === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Beneficiary Photo Required',
                        text: 'Please upload the beneficiary photo in front of the assigned flat (Max: 500 KB).',
                        confirmButtonColor: '#0284c7'
                    });
                    return;
                }
                if (!lat || !lng) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'GPS Coordinates Required',
                        text: 'Please click "Auto-Detect Live GPS" or enter valid latitude & longitude coordinates.',
                        confirmButtonColor: '#0284c7'
                    });
                    return;
                }

                const words = getRemarksWordCount();
                if (words > 1000) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Remarks Too Long',
                        text: `Remarks cannot exceed 1000 words. Current count is ${words} words. Please shorten your notes.`,
                        confirmButtonColor: '#0284c7'
                    });
                    return;
                }
            } else if (status === 'PENDING') {
                const remarks = $('#remarks').val().trim();
                if (!remarks) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pending Reason Required',
                        text: 'Please explain the reason for pending or on-hold possession in remarks.',
                        confirmButtonColor: '#0284c7'
                    });
                    return;
                }
            }

            const formData = new FormData(this);
            const submitBtn = $('#btn-submit-possession');
            submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Saving Possession...');

            $.ajax({
                url: `/ews/developer/possession/submit/{{ $beneficiary->secure_id }}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Possession Updated!',
                            text: res.message || 'Physical possession record saved successfully.',
                            confirmButtonColor: '#0284c7',
                            confirmButtonText: 'Back to List',
                            showCancelButton: true,
                            cancelButtonText: 'Stay on Page',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('ews.developer.possession.index') }}";
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Failed to submit possession.', 'error');
                        submitBtn.prop('disabled', false).html('<i class="bi bi-check2-circle text-sm"></i> Save & Complete Handover');
                    }
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check2-circle text-sm"></i> Save & Complete Handover');
                    let errMsg = 'An unexpected error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Submission Failed', errMsg, 'error');
                }
            });
        });
    </script>
</body>
</html>
