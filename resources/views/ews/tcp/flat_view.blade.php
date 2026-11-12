@php
    $isDepartment = request()->routeIs('ews.department.*') || ($user && $user->role === 'ews_department');
    $isStp = request()->routeIs('ews.stp.*') || ($user && in_array($user->role, ['ews_stp', 'stp']));
    $isApproved = !empty($flat->is_aproved_by_dtp) || !empty($flat->is_approved_by_dtp);
    $flatsCount = $stats['total_flats'] ?? \Illuminate\Support\Facades\DB::table('ews_builder_flats')->count();
    $developerFlatsCount = $flatsCount;

    $tcpLicenceData = $tcpLicenceData ?? null;
    $hasApiSuccess = !empty($tcpLicenceData['success']) && !empty($tcpLicenceData['data']);
    $api = $hasApiSuccess ? $tcpLicenceData['data'] : null;

    // Helper comparison function for text matching
    $checkMatch = function($val1, $val2) {
        $c1 = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$val1)));
        $c2 = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$val2)));
        if (empty($c1) && empty($c2)) return null;
        if (empty($c1) || empty($c2)) return false;
        return ($c1 === $c2 || str_contains($c1, $c2) || str_contains($c2, $c1));
    };

    $devLicence = $flat->license_no ?? '';
    $devDeveloper = $project->colonizer_name ?? $flat->developer_name ?? $creator->name ?? '';
    $devProject = $flat->project_name ?? '';
    $devDistrict = $flat->district_name ?? '';
    // Strict Zone-wise District Filtering based on logged in user's zone
    $userZoneId = $user->zone_id ?? null;
    if (empty($userZoneId) && !empty($user)) {
        $cleanZone = strtoupper(trim(str_ireplace(' ZONE', '', ($user->zone_name ?? '') ?: ($user->district_name ?? ''))));
        if ($cleanZone) {
            $stpZoneObj = \Illuminate\Support\Facades\DB::table('ews_stp_districts')->where('name', $cleanZone)->first();
            if ($stpZoneObj) {
                $userZoneId = $stpZoneObj->id;
            } else {
                $distObj = \Illuminate\Support\Facades\DB::table('ews_districts')->where('name', $cleanZone)->first();
                if ($distObj && $distObj->zone_id) {
                    $userZoneId = $distObj->zone_id;
                }
            }
        }
    }
    if (empty($userZoneId) && !empty($flat->zone_id)) {
        $userZoneId = $flat->zone_id;
    }

    $distQuery = \Illuminate\Support\Facades\DB::table('ews_districts')->orderBy('name', 'asc');
    if (!empty($userZoneId)) {
        $distQuery->where('zone_id', $userZoneId);
    }
    $allDistricts = $distQuery->get();
    $currentDistId = $flat->district_id ?: ($flat->dist_id ?: null);
    $currentDistName = strtoupper(trim($devDistrict ?: ($flat->district_name ?: '')));
    $devSector = $flat->sector_no ?? ($project->sector ?? '');
    $devArea = $flat->area_of_project ?? ($project->total_project_area ?? '');
    $devTown = $flat->town_name ?? '';
    $devZone = $flat->zone_name ?? 'Zone';

    $apiLic = $api['LicenceNo'] ?? null;
    $apiProject = $api['ColonyName'] ?? null;
    $apiDeveloper = $api['Developer'] ?? null;
    $apiDistrict = $api['District'] ?? null;
    $apiSector = $api['Sector'] ?? null;
    $apiArea = $api['AreaInAcre'] ?? null;
    $apiDevPlan = $api['DevelopmentPlan'] ?? null;
    $apiPurpose = $api['Purpose'] ?? null;
    $apiValid = $api['LicenceValidUpto'] ?? null;
    $apiStatus = $api['Status'] ?? null;
    $apiCase = $api['CaseNo'] ?? null;
    $apiApp = $api['ApplicationNo'] ?? null;

    $mLic = $apiLic ? $checkMatch($devLicence, $apiLic) : null;
    $mProject = $apiProject ? $checkMatch($devProject, $apiProject) : null;
    $mDeveloper = $apiDeveloper ? $checkMatch($devDeveloper, $apiDeveloper) : null;
    $mDistrict = $apiDistrict ? $checkMatch($devDistrict, $apiDistrict) : null;
    $mSector = $apiSector ? $checkMatch($devSector, $apiSector) : null;
    $mArea = $apiArea ? $checkMatch($devArea, $apiArea) : null;

    $discrepancyCount = 0;
    if ($api) {
        if ($apiLic && !$mLic) $discrepancyCount++;
        if ($apiProject && !$mProject) $discrepancyCount++;
        if ($apiDeveloper && !$mDeveloper) $discrepancyCount++;
        if ($apiDistrict && !$mDistrict) $discrepancyCount++;
        if ($apiSector && !$mSector) $discrepancyCount++;
        if ($apiArea && !$mArea) $discrepancyCount++;
    }
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EWS Flat Dossier - Flat #{{ $flat->flat_number }} | {{ $isDepartment ? 'Department' : ($isStp ? 'STP Haryana' : 'TCP') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .animate-pulse-subtle { animation: pulse-subtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

        @media print {
            body { background: white !important; }
            header, aside, #btn-back, .no-print { display: none !important; }
            #printable-area { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="h-screen overflow-hidden flex {{ $isDepartment ? 'bg-[#f1f8f4]' : 'bg-[#f8fafc]' }} text-slate-800">

    <!-- 1. Left Navigation Sidebar -->
    @if($isDepartment)
        @include('ews.department.partials.sidebar')
    @elseif($isStp)
        @include('ews.stp.partials.sidebar')
    @else
        @include('ews.tcp.partials.sidebar')
    @endif

    <!-- 2. Right Workspace Container -->
    <div class="{{ $isDepartment ? 'flex-1 min-w-0 flex flex-col ml-[260px] h-screen overflow-hidden' : 'flex-1 flex flex-col h-full overflow-hidden min-w-0' }}">
        
        <!-- Top Navigation Bar (Comfortable 52px height) -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs shrink-0 h-13 px-4 pr-36 sm:pr-44 flex items-center justify-between">
            <!-- Left: Back Button, Emblem & Identification -->
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ $backUrl ?? route($isDepartment ? 'ews.department.builder-flats' : ($isStp ? 'ews.stp.dashboard' : 'ews.tcp.dashboard')) }}" id="btn-back" title="Back to Master Matrix"
                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-sky-100 text-slate-700 hover:text-sky-700 border border-slate-200 flex items-center justify-center transition-all shadow-xs shrink-0 group">
                    <i class="bi bi-arrow-left text-sm transition-transform group-hover:-translate-x-0.5"></i>
                </a>

                <div class="flex items-center gap-2 min-w-0">
                    <img src="{{ asset('Haryana_emblem.png') }}" class="w-7 h-7 object-contain shrink-0" alt="Haryana Emblem">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-900 hidden sm:inline">EWS FLAT DOSSIER:</span>
                        <span class="text-sm font-black font-mono text-violet-700" id="header-flat-num">FLAT #{{ $flat->flat_number }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200" id="header-tower-num">TOWER: {{ $flat->block_tower_number ?? '—' }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200" id="header-floor">{{ $flat->floor ?? '—' }}</span>
                        @if($isStp)
                            <span class="px-2 py-0.5 rounded text-xs font-bold uppercase flex items-center gap-1 {{ $hasApiSuccess ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}" id="top-api-badge">
                                <i class="bi {{ $hasApiSuccess ? 'bi-patch-check-fill text-emerald-600' : 'bi-exclamation-triangle-fill text-amber-600' }}"></i>
                                <span id="top-api-badge-text">{{ $hasApiSuccess ? 'TCP API Verified' : 'TCP Unverified' }}</span>
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded text-xs font-bold uppercase">Read-Only</span>
                        @endif

                        <!-- STP Clearance Status Badge -->
                        @if($flat->is_aproved_by_dtp)
                            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-xs font-bold uppercase inline-flex items-center gap-1 shadow-xs">
                                <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                <span>STP Approved</span>
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 border border-amber-300 rounded-lg text-xs font-bold uppercase inline-flex items-center gap-1 shadow-xs">
                                <i class="bi bi-hourglass-split text-amber-600"></i>
                                <span>Pending Clearance</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Action Bar (Print button shifted safely away from browser language widget) -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- Print Button -->
                <button type="button" onclick="window.print()" title="Print Unit Dossier"
                    class="px-2.5 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-bold flex items-center gap-1.5 shadow-xs cursor-pointer transition-all">
                    <i class="bi bi-printer text-slate-500"></i>
                    <span class="hidden sm:inline">Print</span>
                </button>
            </div>
        </header>

        <!-- Main Workspace (Spacious, Zero Cramping, Clean Hierarchy) -->
        <main class="flex-1 overflow-y-auto px-4 py-3 space-y-3.5 custom-scroll" id="printable-area">
            
            <!-- 1. Executive Hero Ribbon -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 text-white rounded-xl px-5 py-3 shadow-sm border border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="w-9 h-9 rounded-lg bg-cyan-500/20 text-cyan-400 border border-cyan-400/30 flex items-center justify-center text-lg shrink-0">
                        <i class="bi bi-building"></i>
                    </div>
                    <span class="text-base font-black uppercase tracking-wider text-cyan-300" id="hero-project-name">{{ $flat->project_name ?? '—' }}</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-sm text-slate-300">Colonizer: <strong class="text-white font-bold" id="hero-developer-name">{{ $project->colonizer_name ?? $flat->developer_name ?? $creator->name ?? '—' }}</strong></span>
                    <span class="text-slate-600">•</span>
                    <span class="text-sm font-mono text-emerald-400 font-bold" id="hero-license-no">Lic #{{ $flat->license_no ?? '—' }}</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-sm text-slate-300" id="hero-district-town">
                        <i class="bi bi-geo-alt-fill text-rose-400 text-xs"></i> 
                        {{ $flat->town_name ?? '' }}{{ $flat->town_name && $flat->district_name ? ', ' : '' }}{{ $flat->district_name ?? '' }} ({{ $flat->zone_name ?? 'Zone' }})
                    </span>
                    <span class="text-slate-600">•</span>
                    <span class="text-sm text-amber-300 font-bold" id="hero-sector">Sector {{ $flat->sector_no ?? ($project->sector ?? '—') }}</span>
                </div>
            </div>

            <!-- 2. Symmetrical 3-Column Executive Overview Grid (Balanced Height, Clean, No Awkward Scrollbars) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">

                <!-- CARD 1: Architectural & Statutory Unit Data -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-150">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                                <i class="bi bi-grid-3x3-gap-fill text-sky-600 text-sm"></i>
                                <span>Architectural Identity</span>
                            </h3>
                            <span class="text-xs font-bold uppercase px-2 py-0.5 rounded bg-sky-50 text-sky-700 border border-sky-200">Unit Spec</span>
                        </div>

                        <!-- 4 Key-Value Metric Boxes -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Flat Number</span>
                                <span class="text-2xl font-black font-mono text-violet-700 leading-tight block mt-0.5" id="card-flat-number">{{ $flat->flat_number }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Block / Tower</span>
                                <span class="text-base font-black font-mono text-indigo-700 truncate leading-tight block mt-0.5" id="card-block-tower">{{ $flat->block_tower_number ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Floor Details</span>
                                <span class="text-sm font-bold text-slate-800 leading-tight truncate block mt-0.5" id="card-floor">{{ $flat->floor ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Register Flat</span>
                                <span class="text-sm font-bold text-slate-700 leading-tight uppercase block mt-0.5">{{ $flat->register_flat ?? 'YES' }}</span>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- CARD 2: Project & Colonizer Statutory Profile -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-150">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                                <i class="bi bi-buildings-fill text-indigo-600 text-sm"></i>
                                <span>Project & Colonizer Profile</span>
                            </h3>
                            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200" id="card-license-badge">
                                {{ $flat->license_no ? 'Lic #' . $flat->license_no : 'Project' }}
                            </span>
                        </div>

                        <!-- Project Specifications Key-Value List -->
                        <div class="divide-y divide-slate-100 text-xs">
                            <div class="py-1.5 flex items-start justify-between gap-2">
                                <span class="text-slate-500 font-medium shrink-0">Project Name:</span>
                                <span class="font-bold text-slate-900 text-sm break-words text-right" id="card-project-name" title="{{ $flat->project_name }}">{{ $flat->project_name ?: '—' }}</span>
                            </div>
                            <div class="py-1.5 flex items-start justify-between gap-2">
                                <span class="text-slate-500 font-medium shrink-0">Developer / Colonizer:</span>
                                <span class="font-bold text-slate-800 text-sm break-words text-right" id="card-developer-name" title="{{ $devDeveloper }}">{{ $devDeveloper ?: '—' }}</span>
                            </div>
                            <div class="py-1.5 flex items-center justify-between gap-2">
                                <span class="text-slate-500 font-medium shrink-0">License Number:</span>
                                <span class="font-mono font-bold text-emerald-700 text-sm" id="card-license-no">{{ $flat->license_no ?: '—' }}</span>
                            </div>
                            <div class="py-1.5 flex items-center justify-between gap-2">
                                <span class="text-slate-500 font-medium shrink-0">Administrative Zone:</span>
                                <span class="font-bold text-slate-700 uppercase" id="card-zone-name">{{ $flat->zone_name ?: 'ROHTAK ZONE' }}</span>
                            </div>
                            <div class="py-1.5 flex items-center justify-between gap-2">
                                <span class="text-slate-500 font-medium shrink-0">District & Town:</span>
                                <span class="font-bold text-slate-700 uppercase text-right" id="card-district-town">
                                    {{ $flat->town_name ?? '' }}{{ $flat->town_name && $flat->district_name ? ', ' : '' }}{{ $flat->district_name ?? '—' }}
                                </span>
                            </div>
                            <div class="py-1.5 flex items-center justify-between gap-2">
                                <span class="text-slate-500 font-medium shrink-0">Town Planning Sector:</span>
                                <span class="font-bold text-amber-700" id="card-sector-no">Sector {{ $flat->sector_no ?: ($project->sector ?? '—') }}</span>
                            </div>
                            <div class="py-1.5 flex items-center justify-between gap-2">
                                <span class="text-slate-500 font-medium shrink-0">Total Project Area:</span>
                                <span class="font-bold text-slate-800" id="card-area">{{ $flat->area_of_project ? $flat->area_of_project . ' Acres' : '—' }}</span>
                            </div>
                            <div class="py-1.5 flex items-start justify-between gap-2">
                                <span class="text-slate-500 font-medium shrink-0">Site Address / Plan:</span>
                                <div class="text-right">
                                    <span class="font-medium text-slate-600 break-words text-xs block" id="card-address" title="{{ $flat->address_of_project }}">{{ $flat->address_of_project ?: '—' }}</span>
                                    @php
                                        $devPlanRaw = $flat->developer_plan ?: ($project->developer_plan ?? null);
                                        $devPlanUrl = null;
                                        if (!empty($devPlanRaw)) {
                                            if (str_starts_with($devPlanRaw, 'http://') || str_starts_with($devPlanRaw, 'https://')) {
                                                $devPlanUrl = $devPlanRaw;
                                            } elseif (str_starts_with($devPlanRaw, 'storage/')) {
                                                $devPlanUrl = asset($devPlanRaw);
                                            } else {
                                                $devPlanUrl = asset('storage/' . ltrim($devPlanRaw, '/'));
                                            }
                                        }
                                    @endphp
                                    @if(!empty($devPlanUrl))
                                        <a href="{{ $devPlanUrl }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-600 hover:text-sky-800 underline mt-0.5">
                                            <i class="bi bi-file-earmark-pdf"></i> Developer Plan PDF
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Compliance Sanctions, OC Clearance & Quotas (Steps 10 & 11) -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-150">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                                <i class="bi bi-shield-check text-emerald-600 text-sm"></i>
                                <span>Sanctions, OC & Quotas</span>
                            </h3>
                        </div>

                        <!-- Step 10 Sanctions Row -->
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 mb-2">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-700 uppercase">Building Plan Sanction:</span>
                                <span class="px-2 py-0.5 rounded font-black text-xs uppercase {{ strtolower($flat->approved_building_plan ?? '') === 'no' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}" id="card-building-plan-status">
                                    {{ $flat->approved_building_plan ?? 'Approved' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-600">
                                <span>Date: <strong class="font-mono text-slate-800 font-bold" id="card-building-plan-date">{{ $flat->building_plan_approval_date ?? '—' }}</strong></span>
                                @php
                                    $bldgPlanRaw = $flat->building_plan_copy ?: ($project->building_plan_copy ?? null);
                                    $bldgPlanUrl = null;
                                    if (!empty($bldgPlanRaw)) {
                                        if (str_starts_with($bldgPlanRaw, 'http://') || str_starts_with($bldgPlanRaw, 'https://')) {
                                            $bldgPlanUrl = $bldgPlanRaw;
                                        } elseif (str_starts_with($bldgPlanRaw, 'storage/')) {
                                            $bldgPlanUrl = asset($bldgPlanRaw);
                                        } else {
                                            $bldgPlanUrl = asset('storage/' . ltrim($bldgPlanRaw, '/'));
                                        }
                                    }
                                @endphp
                                @if(!empty($bldgPlanUrl))
                                    <a href="{{ $bldgPlanUrl }}" target="_blank"
                                        class="px-2 py-0.5 bg-sky-600 hover:bg-sky-700 text-white rounded text-xs font-bold uppercase inline-flex items-center gap-1 shadow-xs transition">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                        <span>Plan PDF</span>
                                    </a>
                                @else
                                    <span class="font-mono text-slate-400 italic text-xs">No PDF</span>
                                @endif
                            </div>
                        </div>

                        <!-- Step 11 OC Compliance Row -->
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 mb-2.5">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-700 uppercase">Occupation Cert (OC):</span>
                                <span class="px-2 py-0.5 rounded font-black text-xs uppercase bg-amber-100 text-amber-800" id="card-oc-status">
                                    {{ $flat->oc_status ?? ($flat->oc_status_yn ?? 'Part') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-600">
                                <span>Date: <strong class="font-mono text-slate-800 font-bold" id="card-oc-date">{{ $flat->oc_date ?? '—' }}</strong></span>
                                @php
                                    $ocRaw = $flat->oc_copy_file ?: ($flat->oc_copy ?: ($project->oc_copy ?? null));
                                    $ocFileUrl = null;
                                    if (!empty($ocRaw)) {
                                        if (str_starts_with($ocRaw, 'http://') || str_starts_with($ocRaw, 'https://')) {
                                            $ocFileUrl = $ocRaw;
                                        } elseif (str_starts_with($ocRaw, 'storage/')) {
                                            $ocFileUrl = asset($ocRaw);
                                        } else {
                                            $ocFileUrl = asset('storage/' . ltrim($ocRaw, '/'));
                                        }
                                    }
                                @endphp
                                @if(!empty($ocFileUrl))
                                    <a href="{{ $ocFileUrl }}" target="_blank"
                                        class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold uppercase inline-flex items-center gap-1 shadow-xs transition">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                        <span>OC PDF</span>
                                    </a>
                                @else
                                    <span class="font-mono text-slate-400 italic text-xs">No OC PDF</span>
                                @endif
                            </div>
                        </div>

                        <!-- 4 Quota Metric Chips -->
                        <div class="grid grid-cols-4 gap-1.5 text-center mb-1">
                            <div class="bg-slate-50 p-2 rounded-lg border border-slate-200">
                                <span class="block text-xs font-bold uppercase text-slate-500">Total</span>
                                <span class="text-base font-black font-mono text-slate-800 mt-0.5 block" id="card-total-flats">{{ $flat->total_flats_in_project ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-lg border border-slate-200">
                                <span class="block text-xs font-bold uppercase text-slate-500">EWS</span>
                                <span class="text-base font-black font-mono text-emerald-700 mt-0.5 block" id="card-total-ews">{{ $flat->total_ews_flats_in_project ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-lg border border-slate-200">
                                <span class="block text-xs font-bold uppercase text-slate-500">Draw</span>
                                <span class="text-base font-black font-mono text-sky-700 mt-0.5 block" id="card-draw-flats">{{ $flat->flats_offered_for_draw ?? '—' }}</span>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-lg border border-slate-200">
                                <span class="block text-xs font-bold uppercase text-slate-500">Rem OC</span>
                                <span class="text-base font-black font-mono text-amber-700 mt-0.5 block" id="card-rem-oc">{{ $flat->remaining_flats_for_oc ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- STP Clearance Status Footer -->
                    <div class="mt-3 pt-2.5 border-t border-slate-150">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-500 uppercase">STP Clearance:</span>
                            @if($flat->is_aproved_by_dtp)
                                <span class="font-mono font-bold text-emerald-700 flex items-center gap-1">
                                    <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                    <span>{{ $flat->dtp_user_name ?? 'STP' }} ({{ $flat->dtp_approved_at ? date('d/m/Y', strtotime($flat->dtp_approved_at)) : 'Approved' }})</span>
                                </span>
                            @else
                                <span class="font-mono font-bold text-amber-700 flex items-center gap-1">
                                    <i class="bi bi-hourglass-split text-amber-600"></i>
                                    <span>Awaiting Clearance</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- 3. DEDICATED HARYANA TCP (EODB) LIVE GATEWAY & RECONCILIATION SUITE -->
            <!-- ========================================================================= -->
            <section id="section-tcp-suite" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                
                <!-- Form Header Bar (Clean Light Theme) -->
                <div class="bg-gradient-to-r from-sky-50 via-slate-50 to-indigo-50/60 border-b border-slate-200/90 text-slate-800 px-5 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 border border-indigo-200 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 leading-tight">
                                EWS Flat Statutory Verification & Reconciliation Form
                            </h2>
                            <p class="text-xs text-slate-500">Cross-examine Developer Entry with Official TCP Haryana API & Submit Final Approval</p>
                        </div>
                    </div>

                    <!-- Actions: Licence Badge + Adopt All Button -->
                    <div class="flex items-center gap-2 shrink-0 flex-wrap">
                        <div class="flex items-center gap-1.5 bg-white border border-slate-300 px-3 py-1.5 rounded-lg select-all shadow-xs" title="Licence Number is fixed for this flat and cannot be edited">
                            <span class="text-xs font-mono font-bold text-slate-500">LIC:</span>
                            <span class="text-sm font-mono font-bold text-emerald-700" id="suite-licence-display">{{ $flat->license_no ?: '—' }}</span>
                            <i class="bi bi-lock-fill text-slate-400 text-xs ml-0.5" title="Locked: Licence Number cannot be edited"></i>
                        </div>
                        <input type="hidden" id="query-licence-input" value="{{ $flat->license_no ?? '' }}">

                        @if($isStp && !$isApproved)
                            <button type="button" onclick="adoptAllApiValues()" id="btn-suite-adopt-all"
                                class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold uppercase flex items-center gap-1.5 shadow-sm transition cursor-pointer {{ $hasApiSuccess ? '' : 'opacity-60 pointer-events-none' }}"
                                title="Adopt all official TCP API values into the form">
                                <i class="bi bi-check2-all text-sm"></i>
                                <span>⚡ Adopt All Official Values</span>
                            </button>
                        @elseif($isApproved)
                            <span class="px-3.5 py-1.5 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-xs font-bold uppercase inline-flex items-center gap-1.5 shadow-2xs">
                                <i class="bi bi-lock-fill text-emerald-600"></i> Dossier Approved & Locked
                            </span>
                        @endif
                    </div>
                </div>


                <!-- Form Fields Grid (6 Structured Statutory Fields in Spacious 2-Column Grid) -->
                <div class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-5">
                    
                    <!-- 1. Licence Number (Locked) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                                        <i class="bi bi-card-heading"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800 block">Licence Number</span>
                                        <span class="text-[10px] text-slate-400 font-medium">Statutory Project Licence Identifier</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Full-Width Spacious Inputs -->
                            <div class="space-y-3 mb-3.5">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-person-fill text-slate-400"></i>
                                        <span>Developer Entry</span>
                                    </label>
                                    <input type="text" id="form-inp-licence" value="{{ $devLicence }}" readonly
                                        class="w-full bg-slate-100 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-mono font-bold text-slate-700 cursor-not-allowed select-all shadow-2xs min-h-[44px]"
                                        title="Licence Number is fixed for this flat and cannot be modified">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-800 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                        <span>TCP Official API Record</span>
                                    </label>
                                    <div class="w-full bg-emerald-50/90 border border-emerald-300 rounded-xl px-4 py-2.5 text-sm font-mono font-bold text-emerald-950 break-words whitespace-normal leading-relaxed select-all flex items-center min-h-[44px] shadow-2xs" id="cmp-api-licence">
                                        {{ $apiLic ?: '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="card-action-licence" class="pt-1">
                            <div class="text-xs font-bold text-slate-500 flex items-center justify-center gap-1.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                <i class="bi bi-lock-fill text-slate-400"></i> Fixed Licence Number (Locked)
                            </div>
                        </div>
                    </div>

                    <!-- 2. Project / Colony Name -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                                        <i class="bi bi-buildings"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800 block">Project / Colony Name</span>
                                        <span class="text-[10px] text-slate-400 font-medium">Approved Colony Title</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Full-Width Spacious Inputs -->
                            <div class="space-y-3 mb-3.5">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-person-fill text-slate-400"></i>
                                        <span>Developer Entry</span>
                                    </label>
                                    <input type="text" id="form-inp-project" name="project_name" value="{{ $devProject }}"
                                        class="w-full {{ $isApproved ? 'bg-slate-100 text-slate-600 cursor-not-allowed select-all' : 'bg-white text-slate-800 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100' }} border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-bold uppercase transition shadow-2xs min-h-[44px]"
                                        placeholder="Project Name" {{ $isApproved ? 'readonly' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-800 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                        <span>TCP Official API Record</span>
                                    </label>
                                    <div class="w-full bg-emerald-50/90 border border-emerald-300 rounded-xl px-4 py-2.5 text-sm font-bold text-emerald-950 break-words whitespace-normal leading-relaxed select-all flex items-center min-h-[44px] shadow-2xs" id="cmp-api-project" title="{{ $apiProject }}">
                                        {{ $apiProject ?: '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="card-action-project" class="pt-1">
                            @if($isApproved)
                                <div class="text-xs font-bold text-slate-500 flex items-center justify-center gap-1.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <i class="bi bi-lock-fill text-slate-400"></i> Locked (Approved)
                                </div>
                            @elseif($apiProject && $isStp)
                                <button type="button" onclick="adoptFieldToInput('project', '{{ addslashes($apiProject ?? '') }}')"
                                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value
                                </button>
                            @else
                                <div class="text-xs text-slate-400 font-mono text-center py-2.5">—</div>
                            @endif
                        </div>
                    </div>

                    <!-- 3. Developer / Colonizer -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800 block">Developer / Colonizer</span>
                                        <span class="text-[10px] text-slate-400 font-medium">Official Licensee Entity</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Full-Width Spacious Inputs (No Truncation) -->
                            <div class="space-y-3 mb-3.5">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-person-fill text-slate-400"></i>
                                        <span>Developer Entry</span>
                                    </label>
                                    <input type="text" id="form-inp-developer" name="developer_name" value="{{ $devDeveloper }}"
                                        class="w-full {{ $isApproved ? 'bg-slate-100 text-slate-600 cursor-not-allowed select-all' : 'bg-white text-slate-800 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100' }} border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-bold uppercase transition shadow-2xs min-h-[44px]"
                                        placeholder="Developer Name" {{ $isApproved ? 'readonly' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-800 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                        <span>TCP Official API Record</span>
                                    </label>
                                    <div class="w-full bg-emerald-50/90 border border-emerald-300 rounded-xl px-4 py-2.5 text-sm font-bold text-emerald-950 break-words whitespace-normal leading-relaxed select-all flex items-center min-h-[44px] shadow-2xs" id="cmp-api-developer" title="{{ $apiDeveloper }}">
                                        {{ $apiDeveloper ?: '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="card-action-developer" class="pt-1">
                            @if($isApproved)
                                <div class="text-xs font-bold text-slate-500 flex items-center justify-center gap-1.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <i class="bi bi-lock-fill text-slate-400"></i> Locked (Approved)
                                </div>
                            @elseif($apiDeveloper && $isStp)
                                <button type="button" onclick="adoptFieldToInput('developer', '{{ addslashes($apiDeveloper ?? '') }}')"
                                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value
                                </button>
                            @else
                                <div class="text-xs text-slate-400 font-mono text-center py-2.5">—</div>
                            @endif
                        </div>
                    </div>

                    <!-- 4. District & Town -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800 block">District & Town</span>
                                        <span class="text-[10px] text-slate-400 font-medium">Administrative Jurisdiction</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Full-Width Spacious Inputs -->
                            <div class="space-y-3 mb-3.5">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-person-fill text-slate-400"></i>
                                        <span>Developer Entry</span>
                                    </label>

                                    <!-- Hidden district name input to ensure sync -->
                                    <input type="hidden" id="form-inp-district-name" name="district_name" value="{{ $devDistrict ?: ($flat->district_name ?? '') }}">

                                    @if($isApproved)
                                        <div class="w-full bg-slate-100 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 uppercase cursor-not-allowed select-all flex items-center min-h-[44px] shadow-2xs">
                                            <i class="bi bi-lock-fill text-slate-400 mr-2"></i>
                                            <span>{{ $devDistrict ?: ($flat->district_name ?? '—') }}</span>
                                        </div>
                                    @else
                                        <select id="form-inp-district" name="district_id" onchange="onDistrictDropdownChange(this)"
                                            class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-800 uppercase focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs min-h-[44px] cursor-pointer">
                                            <option value="">— Select District —</option>
                                            @foreach($allDistricts as $d)
                                                @php
                                                    $isSelected = ($currentDistId && (int)$currentDistId === (int)$d->id) 
                                                        || ($currentDistName && strtoupper(trim($d->name)) === $currentDistName);
                                                @endphp
                                                <option value="{{ $d->id }}" data-name="{{ strtoupper($d->name) }}" data-zone-id="{{ $d->zone_id }}" {{ $isSelected ? 'selected' : '' }}>
                                                    {{ strtoupper($d->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-800 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                        <span>TCP Official API Record</span>
                                    </label>
                                    <div class="w-full bg-emerald-50/90 border border-emerald-300 rounded-xl px-4 py-2.5 text-sm font-bold text-emerald-950 break-words whitespace-normal leading-relaxed select-all flex items-center min-h-[44px] shadow-2xs" id="cmp-api-district" title="{{ $apiDistrict }}">
                                        {{ $apiDistrict ?: '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="card-action-district" class="pt-1">
                            @if($isApproved)
                                <div class="text-xs font-bold text-slate-500 flex items-center justify-center gap-1.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <i class="bi bi-lock-fill text-slate-400"></i> Locked (Approved)
                                </div>
                            @elseif($apiDistrict && $isStp)
                                <button type="button" onclick="adoptFieldToInput('district', '{{ addslashes($apiDistrict ?? '') }}')"
                                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value
                                </button>
                            @else
                                <div class="text-xs text-slate-400 font-mono text-center py-2.5">—</div>
                            @endif
                        </div>
                    </div>

                    <!-- 5. Town Planning Sector -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                                        <i class="bi bi-pin-map"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800 block">Town Planning Sector</span>
                                        <span class="text-[10px] text-slate-400 font-medium">Urban Master Sector Code</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Full-Width Spacious Inputs -->
                            <div class="space-y-3 mb-3.5">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-person-fill text-slate-400"></i>
                                        <span>Developer Entry</span>
                                    </label>
                                    <input type="text" id="form-inp-sector" name="sector_no" value="{{ $devSector }}"
                                        class="w-full {{ $isApproved ? 'bg-slate-100 text-slate-600 cursor-not-allowed select-all' : 'bg-white text-slate-800 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100' }} border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-bold transition shadow-2xs min-h-[44px]"
                                        placeholder="Sector Number" {{ $isApproved ? 'readonly' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-800 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                        <span>TCP Official API Record</span>
                                    </label>
                                    <div class="w-full bg-emerald-50/90 border border-emerald-300 rounded-xl px-4 py-2.5 text-sm font-bold text-emerald-950 break-words whitespace-normal leading-relaxed select-all flex items-center min-h-[44px] shadow-2xs" id="cmp-api-sector">
                                        {{ $apiSector ? 'Sector ' . $apiSector : '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="card-action-sector" class="pt-1">
                            @if($isApproved)
                                <div class="text-xs font-bold text-slate-500 flex items-center justify-center gap-1.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <i class="bi bi-lock-fill text-slate-400"></i> Locked (Approved)
                                </div>
                            @elseif($apiSector && $isStp)
                                <button type="button" onclick="adoptFieldToInput('sector', '{{ addslashes($apiSector ?? '') }}')"
                                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value
                                </button>
                            @else
                                <div class="text-xs text-slate-400 font-mono text-center py-2.5">—</div>
                            @endif
                        </div>
                    </div>

                    <!-- 6. Total Project Area -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-2xs">
                                        <i class="bi bi-aspect-ratio"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800 block">Total Project Area</span>
                                        <span class="text-[10px] text-slate-400 font-medium">Licensed Acreage</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Full-Width Spacious Inputs -->
                            <div class="space-y-3 mb-3.5">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-person-fill text-slate-400"></i>
                                        <span>Developer Entry</span>
                                    </label>
                                    <input type="text" id="form-inp-area" name="area_of_project" value="{{ $devArea }}"
                                        class="w-full {{ $isApproved ? 'bg-slate-100 text-slate-600 cursor-not-allowed select-all' : 'bg-white text-slate-800 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100' }} border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-bold transition shadow-2xs min-h-[44px]"
                                        placeholder="Project Area (Acres)" {{ $isApproved ? 'readonly' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-800 mb-1.5 flex items-center gap-1.5">
                                        <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                        <span>TCP Official API Record</span>
                                    </label>
                                    <div class="w-full bg-emerald-50/90 border border-emerald-300 rounded-xl px-4 py-2.5 text-sm font-bold text-emerald-950 break-words whitespace-normal leading-relaxed select-all flex items-center min-h-[44px] shadow-2xs" id="cmp-api-area">
                                        {{ $apiArea ? $apiArea . ' Acres' : '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="card-action-area" class="pt-1">
                            @if($isApproved)
                                <div class="text-xs font-bold text-slate-500 flex items-center justify-center gap-1.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <i class="bi bi-lock-fill text-slate-400"></i> Locked (Approved)
                                </div>
                            @elseif($apiArea && $isStp)
                                <button type="button" onclick="adoptFieldToInput('area', '{{ addslashes($apiArea ?? '') }}')"
                                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value
                                </button>
                            @else
                                <div class="text-xs text-slate-400 font-mono text-center py-2.5">—</div>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- STP Remarks Textarea Section -->
                @if($isStp && !$flat->is_aproved_by_dtp)
                    <div class="px-5 py-4 bg-slate-50 border-t border-slate-200">
                        <label for="form-stp-remarks" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="bi bi-chat-left-text-fill text-indigo-600"></i>
                                <span>STP Verification & Approval Remarks</span>
                                <span class="text-rose-500 font-bold text-sm">*</span>
                            </span>
                            <span class="text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full uppercase tracking-wider">Required</span>
                        </label>
                        <textarea id="form-stp-remarks" name="stp_remarks" rows="3" required
                            placeholder="Enter official STP verification / approval remarks here (Mandatory before submitting approval)..."
                            class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs resize-y"></textarea>
                    </div>
                @elseif($flat->is_aproved_by_dtp)
                    <!-- Read-only approved remarks display -->
                    <div class="px-5 py-4 bg-emerald-50/50 border-t border-emerald-200">
                        <div class="block text-xs font-black uppercase tracking-wider text-emerald-900 mb-1.5 flex items-center gap-1.5">
                            <i class="bi bi-chat-left-quote-fill text-emerald-700"></i>
                            <span>Official Certified STP Verification Remarks</span>
                        </div>
                        <div class="w-full bg-white border border-emerald-200 rounded-xl px-4 py-3 text-sm text-emerald-950 font-medium whitespace-pre-wrap">
                            {{ $flat->dtp_remarks ?: 'Dossier certified and approved in accordance with Haryana Town and Country Planning (TCP) statutory norms.' }}
                        </div>
                    </div>
                @endif

                <!-- Form Footer: STP Approval & Submission Bar -->
                <div class="px-5 py-3.5 bg-gradient-to-r from-slate-100 to-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <!-- Left: Status Indication -->
                    <div class="flex items-center gap-2.5">
                        @if($flat->is_aproved_by_dtp)
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-base shrink-0">
                                <i class="bi bi-patch-check-fill"></i>
                            </div>
                            <div>
                                <span class="text-xs font-black uppercase text-emerald-800 block leading-tight">STP Approved & Certified</span>
                                <span class="text-xs font-mono text-emerald-600 block">By {{ $flat->dtp_user_name ?? 'STP Official' }} ({{ $flat->dtp_approved_at ? date('d M, Y h:i A', strtotime($flat->dtp_approved_at)) : 'Approved' }})</span>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-base shrink-0">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <span class="text-xs font-black uppercase text-amber-800 block leading-tight">Pending STP Clearance</span>
                                <span class="text-xs text-slate-500 block">Reconcile specifications, enter remarks and click Approve & Submit</span>
                            </div>
                        @endif
                    </div>

                    <!-- Right: Submit / Approve Actions -->
                    @if($isStp)
                        <div class="flex items-center gap-2.5 shrink-0">
                            @if(!$flat->is_aproved_by_dtp)
                                <button type="button" onclick="submitAndApproveFlat()" id="btn-approve-submit"
                                    class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white rounded-xl text-xs font-black uppercase flex items-center gap-2 shadow-md hover:shadow-lg transition cursor-pointer">
                                    <i class="bi bi-patch-check-fill text-emerald-200 text-base"></i>
                                    <span>✓ Approve & Submit Dossier</span>
                                </button>
                            @else
                                <div class="px-4 py-2.5 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-black uppercase flex items-center gap-2 shadow-2xs select-none">
                                    <i class="bi bi-lock-fill text-emerald-700"></i>
                                    <span>Approved & Finalized (Locked)</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Additional TCP Official Metadata (Development Plan, Purpose, Validity, Case Reference) -->
                <div class="px-4 pb-4">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <i class="bi bi-journal-text text-sky-600"></i>
                            <span>Additional Official TCP Haryana Grant Specifications</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            
                            <!-- 1. Development Plan -->
                            <div class="bg-white p-2.5 rounded-lg border border-slate-200 flex flex-col justify-between">
                                <div>
                                    <span class="text-slate-400 font-bold uppercase block text-xs">Master Development Plan</span>
                                    <span class="font-bold text-slate-800 text-sm block mt-0.5 break-words leading-snug" id="cmp-api-devplan" title="{{ $apiDevPlan }}">{{ $apiDevPlan ?: '—' }}</span>
                                </div>
                                <div class="mt-2" id="action-devplan">
                                    @if($isStp && $apiDevPlan)
                                        <button type="button" onclick="adoptField('address_of_project', '{{ addslashes($apiDevPlan) }}', 'Site Address / Plan')" class="w-full py-1 px-2 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-300 rounded text-xs font-bold uppercase transition">Apply As Address</button>
                                    @endif
                                </div>
                            </div>

                            <!-- 2. Approved Purpose -->
                            <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                                <span class="text-slate-400 font-bold uppercase block text-xs">Approved Scheme / Purpose</span>
                                <span class="font-bold text-slate-800 text-sm block mt-0.5 break-words leading-snug" id="cmp-api-purpose" title="{{ $apiPurpose }}">{{ $apiPurpose ?: '—' }}</span>
                            </div>

                            <!-- 3. Licence Validity & Status -->
                            <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                                <span class="text-slate-400 font-bold uppercase block text-xs">Licence Validity & Grant Status</span>
                                <span class="font-bold text-emerald-800 text-sm block mt-0.5" id="cmp-api-validity">
                                    @if($apiValid || $apiStatus)
                                        Valid: {{ $apiValid ? date('d/m/Y', strtotime($apiValid)) : '—' }} ({{ $apiStatus ?? 'Granted' }})
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                            <!-- 4. Case & Application No -->
                            <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                                <span class="text-slate-400 font-bold uppercase block text-xs">Govt Case & App Number</span>
                                <span class="font-mono font-bold text-slate-800 text-sm block mt-0.5" id="cmp-api-case">
                                    @if($apiCase || $apiApp)
                                        {{ $apiCase ?? '—' }} / {{ $apiApp ?? '—' }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

            <!-- ========================================================================= -->
            <!-- 4. RECENT AUDIT TRAIL / MODIFICATION LOGS (Collapsible) -->
            <!-- ========================================================================= -->
            {{-- Hidden as per user request: Dossier Audit Trail & Modification History
            @if(isset($auditLogs) && $auditLogs->isNotEmpty())
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-150 cursor-pointer" onclick="$('#audit-trail-content').slideToggle(200)">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                            <i class="bi bi-clock-history text-indigo-600"></i>
                            <span>Dossier Audit Trail & Modification History</span>
                            <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">{{ $auditLogs->count() }} Entries</span>
                        </h3>
                        <span class="text-xs font-bold text-sky-600 hover:text-sky-800 flex items-center gap-1">
                            <i class="bi bi-chevron-down"></i> Toggle History
                        </span>
                    </div>

                    <div id="audit-trail-content" class="space-y-2 pt-1 text-xs">
                        @foreach($auditLogs->take(5) as $log)
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800">{{ $log->action }}</span>
                                        <span class="text-slate-400">•</span>
                                        <span class="text-slate-600 font-medium">{{ $log->summary }}</span>
                                    </div>
                                    @if(!empty($log->changed_fields))
                                        <div class="mt-1 text-xs text-slate-500 font-mono flex items-center gap-2 flex-wrap">
                                            @foreach($log->changed_fields as $fKey => $fVal)
                                                <span class="bg-white px-2 py-0.5 rounded border border-slate-200">
                                                    <strong>{{ $fVal['label'] ?? $fKey }}:</strong> {{ $fVal['old'] ?? '—' }} &rarr; <span class="text-emerald-700 font-bold">{{ $fVal['new'] ?? '—' }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs font-mono text-slate-400 shrink-0 sm:text-right">
                                    <div>Officer: <strong class="text-slate-700">{{ $log->user_name ?? 'STP Officer' }}</strong> ({{ strtoupper($log->user_role ?? 'STP') }})</div>
                                    <div>{{ $log->created_at ? $log->created_at->format('d M, Y h:i A') : '—' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            --}}

        </main>

        <!-- Compact Bottom Status Footer (Height: 28px) -->
        <footer class="bg-white border-t border-slate-200 py-1.5 px-4 text-center shrink-0">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                Department of Housing for All • Developed by DITECH (CRID)
            </p>
        </footer>

    </div>

    <!-- ========================================================================= -->
    <!-- 5. STP RECONCILE & EDIT MODAL (Tailwind Interactive) -->
    <!-- ========================================================================= -->
    @if($isStp)
        <div id="modal-reconcile" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-slate-900 via-sky-950 to-indigo-950 text-white px-5 py-3 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-400 text-base">
                            <i class="bi bi-sliders2"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-sky-200">STP Dossier Reconciliation & Modification Console</h3>
                            <p class="text-xs text-slate-300 font-mono">Reconcile Flat Specifications with Haryana TCP API Official Records</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeReconcileModal()" class="w-7 h-7 rounded-lg hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-center text-sm transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Fast Fill Ribbon -->
                <div class="bg-sky-50 border-b border-sky-200 px-5 py-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="text-xs text-sky-900 font-medium">
                        <i class="bi bi-magic text-sky-600"></i> Click any blue pill beside an input to copy TCP API value, or adopt all at once:
                    </div>
                    <button type="button" onclick="adoptAllModalFields()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold uppercase flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                        <i class="bi bi-check2-all"></i>
                        <span>⚡ Pre-fill All from TCP API</span>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form id="form-reconcile-flat" onsubmit="submitReconcileForm(event)" class="flex-1 overflow-y-auto p-5 space-y-4 custom-scroll">
                    @csrf
                    <input type="hidden" name="id" value="{{ $flat->secure_id }}">
                    <input type="hidden" name="secure_id" value="{{ $flat->secure_id }}">

                    <!-- Group 1: Project & Colonizer Specifications -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-building text-indigo-600"></i>
                                <span>1. Project & Colonizer Identity</span>
                            </h4>
                            <span class="text-xs font-mono text-slate-400">Step 8 & 9 Details</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <!-- Developer / Colonizer Name -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="font-bold text-slate-700 uppercase">Developer / Colonizer Name</label>
                                    <button type="button" onclick="copyModalField('inp-mod-developer', '{{ addslashes($api['Developer'] ?? '') }}')"
                                        class="text-xs font-bold text-sky-600 hover:text-sky-800 {{ !empty($api['Developer']) ? '' : 'hidden' }}" id="pill-mod-developer">
                                        ⚡ Use API: <span class="font-mono">{{ Str::limit($api['Developer'] ?? '', 45) }}</span>
                                    </button>
                                </div>
                                <input type="text" name="developer_name" id="inp-mod-developer" value="{{ $flat->developer_name }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none uppercase text-sm">
                            </div>

                            <!-- Project / Colony Name -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="font-bold text-slate-700 uppercase">Project / Colony Name</label>
                                    <button type="button" onclick="copyModalField('inp-mod-project', '{{ addslashes($api['ColonyName'] ?? '') }}')"
                                        class="text-xs font-bold text-sky-600 hover:text-sky-800 {{ !empty($api['ColonyName']) ? '' : 'hidden' }}" id="pill-mod-project">
                                        ⚡ Use API: <span class="font-mono">{{ Str::limit($api['ColonyName'] ?? '', 45) }}</span>
                                    </button>
                                </div>
                                <input type="text" name="project_name" id="inp-mod-project" value="{{ $flat->project_name }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none uppercase text-sm">
                            </div>

                            <!-- Licence Number (Locked: cannot be edited) -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="font-bold text-slate-700 uppercase">Licence Number <span class="text-xs text-slate-400 font-normal lowercase">(fixed)</span></label>
                                    <span class="text-xs font-mono text-slate-400 flex items-center gap-1"><i class="bi bi-lock-fill text-slate-400"></i> Fixed</span>
                                </div>
                                <input type="text" name="license_no" id="inp-mod-licence" value="{{ $flat->license_no }}" readonly
                                    class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-1.5 text-slate-600 font-mono font-bold cursor-not-allowed outline-none uppercase text-sm" title="Licence Number is fixed and cannot be edited">
                            </div>

                            <!-- District Name -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="font-bold text-slate-700 uppercase">District Name</label>
                                    <button type="button" onclick="copyModalField('inp-mod-district', '{{ addslashes($api['District'] ?? '') }}')"
                                        class="text-xs font-bold text-sky-600 hover:text-sky-800 {{ !empty($api['District']) ? '' : 'hidden' }}" id="pill-mod-district">
                                        ⚡ Use API: <span class="font-mono">{{ $api['District'] ?? '' }}</span>
                                    </button>
                                </div>
                                <input type="text" name="district_name" id="inp-mod-district" value="{{ $flat->district_name }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none uppercase text-sm">
                            </div>

                            <!-- Town Name -->
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Town / Tehsil</label>
                                <input type="text" name="town_name" id="inp-mod-town" value="{{ $flat->town_name }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none uppercase text-sm">
                            </div>

                            <!-- Sector Number -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="font-bold text-slate-700 uppercase">Sector Number</label>
                                    <button type="button" onclick="copyModalField('inp-mod-sector', '{{ addslashes($api['Sector'] ?? '') }}')"
                                        class="text-xs font-bold text-sky-600 hover:text-sky-800 {{ !empty($api['Sector']) ? '' : 'hidden' }}" id="pill-mod-sector">
                                        ⚡ Use API: <span class="font-mono">{{ $api['Sector'] ?? '' }}</span>
                                    </button>
                                </div>
                                <input type="text" name="sector_no" id="inp-mod-sector" value="{{ $flat->sector_no }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none text-sm">
                            </div>

                            <!-- Total Project Area -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="font-bold text-slate-700 uppercase">Total Project Area (Acres)</label>
                                    <button type="button" onclick="copyModalField('inp-mod-area', '{{ addslashes($api['AreaInAcre'] ?? '') }}')"
                                        class="text-xs font-bold text-sky-600 hover:text-sky-800 {{ !empty($api['AreaInAcre']) ? '' : 'hidden' }}" id="pill-mod-area">
                                        ⚡ Use API: <span class="font-mono">{{ $api['AreaInAcre'] ?? '' }}</span>
                                    </button>
                                </div>
                                <input type="text" name="area_of_project" id="inp-mod-area" value="{{ $flat->area_of_project }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none text-sm">
                            </div>

                            <!-- Site Address / Development Plan -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="font-bold text-slate-700 uppercase">Site Address / Dev Plan</label>
                                    <button type="button" onclick="copyModalField('inp-mod-address', '{{ addslashes($api['DevelopmentPlan'] ?? '') }}')"
                                        class="text-xs font-bold text-sky-600 hover:text-sky-800 {{ !empty($api['DevelopmentPlan']) ? '' : 'hidden' }}" id="pill-mod-address">
                                        ⚡ Use Plan: <span class="font-mono">{{ Str::limit($api['DevelopmentPlan'] ?? '', 45) }}</span>
                                    </button>
                                </div>
                                <input type="text" name="address_of_project" id="inp-mod-address" value="{{ $flat->address_of_project }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-medium focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Group 2: Architectural Identity -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-grid-3x3-gap-fill text-sky-600"></i>
                                <span>2. Architectural Identity</span>
                            </h4>
                            <span class="text-xs font-mono text-slate-400">Unit Identification</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Flat Number</label>
                                <input type="text" name="flat_number" id="inp-mod-flat-num" value="{{ $flat->flat_number }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-mono font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Block / Tower</label>
                                <input type="text" name="block_tower_number" id="inp-mod-block" value="{{ $flat->block_tower_number }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Floor Details</label>
                                <input type="text" name="floor" id="inp-mod-floor" value="{{ $flat->floor }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Group 3: Sanctions, OC & Quotas -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-shield-check text-emerald-600"></i>
                                <span>3. Sanctions, OC Clearance & Quotas</span>
                            </h4>
                            <span class="text-xs font-mono text-slate-400">Steps 10 & 11</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Building Plan Sanction</label>
                                <select name="approved_building_plan" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-800 font-bold outline-none text-sm">
                                    <option value="yes" {{ strtolower($flat->approved_building_plan ?? '') === 'yes' ? 'selected' : '' }}>YES (Sanctioned)</option>
                                    <option value="no" {{ strtolower($flat->approved_building_plan ?? '') === 'no' ? 'selected' : '' }}>NO (Pending)</option>
                                </select>
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Sanction Date</label>
                                <input type="date" name="building_plan_approval_date" value="{{ $flat->building_plan_approval_date }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-800 font-mono text-xs outline-none">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">OC Status</label>
                                <select name="oc_status" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-800 font-bold outline-none text-sm">
                                    <option value="complete" {{ strtolower($flat->oc_status ?? '') === 'complete' ? 'selected' : '' }}>Complete</option>
                                    <option value="part" {{ strtolower($flat->oc_status ?? '') === 'part' ? 'selected' : '' }}>Part</option>
                                    <option value="none" {{ strtolower($flat->oc_status ?? '') === 'none' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">OC Date</label>
                                <input type="date" name="oc_date" value="{{ $flat->oc_date }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-800 font-mono text-xs outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Total Project Flats</label>
                                <input type="number" name="total_flats_in_project" value="{{ $flat->total_flats_in_project }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-800 font-mono text-sm outline-none">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Total EWS Flats</label>
                                <input type="number" name="total_ews_flats_in_project" value="{{ $flat->total_ews_flats_in_project }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-800 font-mono text-sm outline-none">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Flats For Draw</label>
                                <input type="number" name="flats_offered_for_draw" value="{{ $flat->flats_offered_for_draw }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-800 font-mono text-sm outline-none">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 uppercase block mb-1">Remaining OC Flats</label>
                                <input type="number" name="remaining_flats_for_oc" value="{{ $flat->remaining_flats_for_oc }}"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-800 font-mono text-sm outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Group 4: STP Justification Remarks -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                        <label class="font-bold text-slate-700 uppercase text-xs block">
                            <i class="bi bi-pencil-fill text-indigo-600"></i> STP Officer Remarks / Reason for Modification
                        </label>
                        <textarea name="stp_remarks" rows="2" placeholder="e.g. Corrected Developer Name and Project Name as per official TCP Haryana Licence No 242 OF 2007."
                            class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-slate-800 text-xs focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none"></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-200">
                        <button type="button" onclick="closeReconcileModal()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase transition">
                            Cancel
                        </button>
                        <button type="submit" id="btn-save-reconcile"
                            class="px-5 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-700 hover:to-indigo-700 text-white rounded-lg text-xs font-black uppercase flex items-center gap-1.5 shadow-sm transition">
                            <i class="bi bi-check-circle-fill"></i>
                            <span id="btn-save-reconcile-text">Save Changes & Update Dossier</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- Script for copy code, TCP Haryana API live query, and STP Reconciliation -->
    <script>
        // Global in-memory cache of current API response
        let currentTcpData = @json($api);
        const flatId = "{{ $flat->secure_id }}";
        const flatSecureId = "{{ $flat->secure_id }}";

        function copyFlatCode() {
            const el = document.getElementById('dossier-flat-code');
            if (!el) return;
            const code = el.innerText;
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

        function scrollToTcpSuite() {
            document.getElementById('section-tcp-suite').scrollIntoView({ behavior: 'smooth' });
        }

        function openReconcileModal() {
            $('#modal-reconcile').removeClass('hidden').addClass('flex');
            updateModalPills();
        }

        function closeReconcileModal() {
            $('#modal-reconcile').removeClass('flex').addClass('hidden');
        }

        function copyModalField(targetId, val) {
            if (val) {
                $('#' + targetId).val(val).addClass('bg-sky-50 border-sky-400');
                setTimeout(() => $('#' + targetId).removeClass('bg-sky-50 border-sky-400'), 1000);
            }
        }

        function loadSampleLicence(sampleNo) {
            $('#query-licence-input').val(sampleNo);
            fetchLiveTcpLicence();
        }

        // Live API Fetch Handler
        function fetchLiveTcpLicence() {
            const licNo = $('#query-licence-input').val().trim();
            if (!licNo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Licence Number Required',
                    text: 'Please enter a valid Haryana TCP licence number (e.g. 242 OF 2007).',
                    timer: 3000
                });
                return;
            }

            // Spinner state
            $('#btn-fetch-icon').addClass('animate-spin');
            $('#btn-fetch-text').text('Querying TCP Portal...');
            $('#btn-fetch-licence').prop('disabled', true);

            $.ajax({
                url: "{{ route('ews.stp.flats.fetch-tcp-licence', $flat->secure_id) }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    licence_no: licNo
                },
                success: function(resp) {
                    if (resp.success && resp.data) {
                        currentTcpData = resp.data;
                        renderApiComparisonData(resp.data);
                        updateModalPills();

                        $('#btn-suite-adopt-all').removeClass('opacity-60 pointer-events-none');
                        $('#suite-status-pill').removeClass('bg-amber-500/20 text-amber-300 border-amber-400/30').addClass('bg-emerald-500/20 text-emerald-300 border-emerald-400/30');
                        $('#suite-status-text').text('TCP Master Connected');
                        $('#top-api-badge').removeClass('bg-amber-100 text-amber-800 border-amber-300').addClass('bg-emerald-100 text-emerald-800 border-emerald-300');
                        $('#top-api-badge-text').text('TCP API Verified');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'TCP Licence Data Retrieved: ' + resp.licence_no,
                            showConfirmButton: false,
                            timer: 2500
                        });
                    } else {
                        $('#suite-status-pill').removeClass('bg-emerald-500/20 text-emerald-300 border-emerald-400/30').addClass('bg-amber-500/20 text-amber-300 border-amber-400/30');
                        $('#suite-status-text').text('No Record');
                        Swal.fire({
                            icon: 'warning',
                            title: 'कोई डेटा नहीं मिला',
                            text: resp.message || 'इस लाइसेंस नंबर के अगेंस्ट कोई डेटा प्राप्त नहीं हुआ।',
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'API Query Failed',
                        text: xhr.responseJSON?.message || 'Failed to retrieve data from TCP Haryana API.'
                    });
                },
                complete: function() {
                    $('#btn-fetch-icon').removeClass('animate-spin');
                    $('#btn-fetch-text').text('Verify Live Record');
                    $('#btn-fetch-licence').prop('disabled', false);
                }
            });
        }

        // Render the retrieved TCP data in the 6 Comparison Cards and Metadata Strip
        function renderApiComparisonData(data) {
            if (!data) return;

            // 1. Update Official Values in the 6 Cards
            $('#cmp-api-licence').text(data.LicenceNo || '—');
            $('#cmp-api-project').text(data.ColonyName || '—').attr('title', data.ColonyName || '');
            $('#cmp-api-developer').text(data.Developer || '—').attr('title', data.Developer || '');
            $('#cmp-api-district').text(data.District || '—');
            $('#cmp-api-sector').text(data.Sector ? 'Sector ' + data.Sector : '—');
            $('#cmp-api-area').text(data.AreaInAcre ? data.AreaInAcre + ' Acres' : '—');

            // 2. Action buttons for STP adoption
            @if($isStp)
                if (data.ColonyName) {
                    $('#card-action-project').html(`<button type="button" onclick="adoptFieldToInput('project', '${escapeQuotes(data.ColonyName)}')" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer"><i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value</button>`);
                }
                if (data.Developer) {
                    $('#card-action-developer').html(`<button type="button" onclick="adoptFieldToInput('developer', '${escapeQuotes(data.Developer)}')" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer"><i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value</button>`);
                }
                if (data.District) {
                    $('#card-action-district').html(`<button type="button" onclick="adoptFieldToInput('district', '${escapeQuotes(data.District)}')" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer"><i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value</button>`);
                }
                if (data.Sector) {
                    $('#card-action-sector').html(`<button type="button" onclick="adoptFieldToInput('sector', '${escapeQuotes(data.Sector)}')" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer"><i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value</button>`);
                }
                if (data.AreaInAcre) {
                    $('#card-action-area').html(`<button type="button" onclick="adoptFieldToInput('area', '${escapeQuotes(data.AreaInAcre)}')" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase flex items-center justify-center gap-2 shadow-sm transition cursor-pointer"><i class="bi bi-lightning-charge-fill text-yellow-300"></i> Adopt Official TCP Value</button>`);
                }
            @endif

            // 3. Update Additional TCP Metadata Tiles
            $('#cmp-api-devplan').text(data.DevelopmentPlan || '—').attr('title', data.DevelopmentPlan || '');
            $('#cmp-api-purpose').text(data.Purpose || '—').attr('title', data.Purpose || '');

            let valStr = '';
            if (data.LicenceValidUpto || data.Status) {
                valStr = 'Valid: ' + (data.LicenceValidUpto || '—') + ' (' + (data.Status || 'Granted') + ')';
            }
            $('#cmp-api-validity').text(valStr || '—');
            $('#cmp-api-case').text((data.CaseNo || '—') + ' / ' + (data.ApplicationNo || '—'));

            if (data.DevelopmentPlan) {
                $('#action-devplan').html(`<button type="button" onclick="adoptField('address_of_project', '${escapeQuotes(data.DevelopmentPlan)}', 'Site Address / Plan')" class="w-full py-1 px-2 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-300 rounded text-xs font-bold uppercase transition">Apply As Address</button>`);
            }

            // 4. Update Status Badges in Header
            $('#top-api-badge').removeClass('bg-amber-100 text-amber-800 border-amber-300').addClass('bg-emerald-100 text-emerald-800 border-emerald-300');
            $('#top-api-badge-text').text('TCP API Verified');
            $('#suite-status-pill').removeClass('bg-amber-500/20 text-amber-300 border-amber-400/30').addClass('bg-emerald-500/20 text-emerald-300 border-emerald-400/30');
            $('#suite-status-text').text('TCP Master Connected');
        }

        function updateModalPills() {
            if (!currentTcpData) return;

            if (currentTcpData.Developer) {
                $('#pill-mod-developer').removeClass('hidden').find('span').text(currentTcpData.Developer.slice(0, 45));
            }
            if (currentTcpData.ColonyName) {
                $('#pill-mod-project').removeClass('hidden').find('span').text(currentTcpData.ColonyName.slice(0, 45));
            }
            if (currentTcpData.District) {
                $('#pill-mod-district').removeClass('hidden').find('span').text(currentTcpData.District);
            }
            if (currentTcpData.Sector) {
                $('#pill-mod-sector').removeClass('hidden').find('span').text(currentTcpData.Sector);
            }
            if (currentTcpData.AreaInAcre) {
                $('#pill-mod-area').removeClass('hidden').find('span').text(currentTcpData.AreaInAcre);
            }
            if (currentTcpData.DevelopmentPlan) {
                $('#pill-mod-address').removeClass('hidden').find('span').text(currentTcpData.DevelopmentPlan.slice(0, 45));
            }
        }

        function adoptAllModalFields() {
            if (!currentTcpData) {
                Swal.fire({ icon: 'info', title: 'No TCP API Data', text: 'Please fetch TCP Haryana record first.' });
                return;
            }
            if (currentTcpData.Developer) $('#inp-mod-developer').val(currentTcpData.Developer);
            if (currentTcpData.ColonyName) $('#inp-mod-project').val(currentTcpData.ColonyName);
            if (currentTcpData.District) $('#inp-mod-district').val(currentTcpData.District);
            if (currentTcpData.Sector) $('#inp-mod-sector').val(currentTcpData.Sector);
            if (currentTcpData.AreaInAcre) $('#inp-mod-area').val(currentTcpData.AreaInAcre);
            if (currentTcpData.DevelopmentPlan) $('#inp-mod-address').val(currentTcpData.DevelopmentPlan);

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'All official values copied into form!',
                showConfirmButton: false,
                timer: 2000
            });
        }

        // Single field adoption by STP
        function adoptField(fieldName, fieldValue, label) {
            Swal.fire({
                title: 'Adopt Official Value?',
                html: `Are you sure you want to update <strong>${label}</strong> with official TCP Haryana record:<br><div class="p-2.5 mt-2 bg-emerald-50 text-emerald-900 font-bold rounded-lg border border-emerald-300 text-sm">${fieldValue}</div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Adopt Value'
            }).then((result) => {
                if (result.isConfirmed) {
                    const postData = {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        [fieldName]: fieldValue,
                        stp_remarks: `Adopted official ${label} from TCP Haryana API.`
                    };

                    $.ajax({
                        url: "{{ route('ews.stp.flats.reconcile', $flat->secure_id) }}",
                        type: 'POST',
                        data: postData,
                        success: function(resp) {
                            if (resp.success) {
                                applyUpdatedValuesToDOM(resp.flat);
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: `${label} updated successfully!`,
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: xhr.responseJSON?.message || 'Could not update field.'
                            });
                        }
                    });
                }
            });
        }

        function onDistrictDropdownChange(sel) {
            const opt = $(sel).find('option:selected');
            const distName = opt.data('name') || opt.text().trim();
            $('#form-inp-district-name').val(distName);
        }

        // Adopt an official TCP value directly into its form input field
        function adoptFieldToInput(key, val) {
            if (!val) return;
            if (key === 'district') {
                const targetName = String(val).trim().toUpperCase();
                let matched = false;
                $('#form-inp-district option').each(function() {
                    const optVal = $(this).val();
                    if (!optVal) return;
                    const optName = String($(this).data('name') || $(this).text()).trim().toUpperCase();
                    if (optName === targetName || optName.includes(targetName) || targetName.includes(optName) ||
                        (targetName.includes('GURGAON') && optName.includes('GURUGRAM')) ||
                        (targetName.includes('GURUGRAM') && optName.includes('GURGAON'))) {
                        $('#form-inp-district').val(optVal).trigger('change');
                        $('#form-inp-district-name').val($(this).data('name') || $(this).text().trim());
                        matched = true;
                        return false;
                    }
                });
                if (!matched) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'District Outside Assigned Zone',
                        html: `The TCP API district <strong>${val}</strong> does not belong to your assigned zone.<br><span class="text-xs text-slate-500">Only districts under your assigned zone jurisdiction can be selected.</span>`,
                        confirmButtonColor: '#0284c7'
                    });
                    return;
                }
            } else {
                $('#form-inp-' + key).val(val);
            }

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Official TCP value adopted into form!',
                showConfirmButton: false,
                timer: 2000
            });
        }

        // Adopt All official API values into flat dossier form fields
        function adoptAllApiValues() {
            if (!currentTcpData) {
                Swal.fire({ icon: 'warning', title: 'No TCP Data Available', text: 'Official TCP records are not loaded yet.' });
                return;
            }

            if (currentTcpData.ColonyName) adoptFieldToInput('project', currentTcpData.ColonyName);
            if (currentTcpData.Developer) adoptFieldToInput('developer', currentTcpData.Developer);
            if (currentTcpData.District) adoptFieldToInput('district', currentTcpData.District);
            if (currentTcpData.Sector) adoptFieldToInput('sector', currentTcpData.Sector);
            if (currentTcpData.AreaInAcre) adoptFieldToInput('area', currentTcpData.AreaInAcre);

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'All official TCP values adopted into form fields!',
                showConfirmButton: false,
                timer: 2500
            });
        }

        // Final Submission: Reconciles entries, saves remarks, and sets STP approval flag to 1
        function submitAndApproveFlat() {
            const projectName = $('#form-inp-project').val();
            const developerName = $('#form-inp-developer').val();
            const districtId = $('#form-inp-district').val();
            const districtName = $('#form-inp-district option:selected').data('name') || $('#form-inp-district-name').val() || $('#form-inp-district').val();
            const sectorNo = $('#form-inp-sector').val();
            const areaOfProject = $('#form-inp-area').val();
            const stpRemarks = ($('#form-stp-remarks').val() || '').trim();

            if (!stpRemarks) {
                $('#form-stp-remarks').addClass('border-rose-500 ring-2 ring-rose-200').focus();
                Swal.fire({
                    icon: 'warning',
                    title: 'Remarks Required',
                    text: 'Please enter STP Verification & Approval Remarks before submitting.',
                    confirmButtonColor: '#e11d48'
                });
                return;
            }
            $('#form-stp-remarks').removeClass('border-rose-500 ring-2 ring-rose-200');

            Swal.fire({
                title: 'Approve & Submit Flat Dossier?',
                html: 'This will certify the flat specifications, save any reconciled values into the database, and grant official <strong>STP Approval</strong>.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Approve & Submit'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Submitting Approval...',
                        text: 'Certifying flat dossier and saving audit logs.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    const postData = {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        project_name: projectName,
                        developer_name: developerName,
                        district_id: districtId,
                        district_name: districtName,
                        sector_no: sectorNo,
                        area_of_project: areaOfProject,
                        stp_remarks: stpRemarks,
                        approve_dtp: 1
                    };

                    $.ajax({
                        url: "{{ route('ews.stp.flats.reconcile', $flat->secure_id) }}",
                        type: 'POST',
                        data: postData,
                        success: function(resp) {
                            if (resp.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dossier Approved & Certified!',
                                    text: resp.message || 'Flat dossier has been officially verified and approved.',
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Approval Failed',
                                    text: resp.message || 'Could not approve flat dossier.'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Error',
                                text: xhr.responseJSON?.message || 'Server error occurred while submitting approval.'
                            });
                        }
                    });
                }
            });
        }

        // Revoke STP Approval back to Pending
        function revokeFlatApproval() {
            Swal.fire({
                title: 'Revoke STP Approval?',
                text: 'This will return the flat to Pending Clearance status.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Revoke Approval'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('ews.stp.flats.reconcile', $flat->secure_id) }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            approve_dtp: 0,
                            stp_remarks: 'STP Approval revoked for re-examination.'
                        },
                        success: function(resp) {
                            window.location.reload();
                        }
                    });
                }
            });
        }

        // Submit complete reconciliation modal form
        function submitReconcileForm(e) {
            e.preventDefault();

            $('#btn-save-reconcile').prop('disabled', true);
            $('#btn-save-reconcile-text').text('Saving Updates...');

            const formData = $('#form-reconcile-flat').serialize();

            $.ajax({
                url: "{{ route('ews.stp.flats.reconcile', $flat->secure_id) }}",
                type: 'POST',
                data: formData,
                success: function(resp) {
                    if (resp.success) {
                        applyUpdatedValuesToDOM(resp.flat);
                        closeReconcileModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Dossier Updated!',
                            text: resp.message,
                            timer: 2500
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        text: xhr.responseJSON?.message || 'Could not save modifications.'
                    });
                },
                complete: function() {
                    $('#btn-save-reconcile').prop('disabled', false);
                    $('#btn-save-reconcile-text').text('Save Changes & Update Dossier');
                }
            });
        }

        // Apply updated flat values to DOM in real-time
        function applyUpdatedValuesToDOM(flat) {
            if (!flat) return;

            // Hero Ribbon
            if (flat.project_name) $('#hero-project-name').text(flat.project_name);
            if (flat.developer_name) $('#hero-developer-name').text(flat.developer_name);
            if (flat.license_no) $('#hero-license-no').text('Lic #' + flat.license_no);
            if (flat.district_name || flat.town_name) {
                $('#hero-district-town').html(`<i class="bi bi-geo-alt-fill text-rose-400 text-xs"></i> ${flat.town_name || ''}, ${flat.district_name || ''} (${flat.zone_name || 'Zone'})`);
            }
            if (flat.sector_no) $('#hero-sector').text('Sector ' + flat.sector_no);

            // Card 1
            if (flat.flat_number) {
                $('#card-flat-number, #header-flat-num').text('FLAT #' + flat.flat_number);
            }
            if (flat.block_tower_number) {
                $('#card-block-tower, #header-tower-num').text('TOWER: ' + flat.block_tower_number);
            }
            if (flat.floor) {
                $('#card-floor, #header-floor').text(flat.floor);
            }

            // Card 2
            if (flat.project_name) {
                $('#card-project-name').text(flat.project_name).attr('title', flat.project_name);
            }
            if (flat.developer_name) {
                $('#card-developer-name').text(flat.developer_name).attr('title', flat.developer_name);
            }
            if (flat.license_no) {
                $('#card-license-no').text(flat.license_no);
                $('#card-license-badge').text('Lic #' + flat.license_no);
                $('#query-licence-input').val(flat.license_no);
            }
            if (flat.zone_name) $('#card-zone-name').text(flat.zone_name);
            if (flat.district_name || flat.town_name) {
                const distTownStr = (flat.town_name || '') + (flat.town_name && flat.district_name ? ', ' : '') + (flat.district_name || '—');
                $('#card-district-town').text(distTownStr);
            }
            if (flat.sector_no) {
                $('#card-sector-no').text('Sector ' + flat.sector_no);
            }
            if (flat.area_of_project) {
                $('#card-area').text(flat.area_of_project + ' Acres');
            }
            if (flat.address_of_project) $('#card-address').text(flat.address_of_project).attr('title', flat.address_of_project);

            // Card 3
            if (flat.approved_building_plan) $('#card-building-plan-status').text(flat.approved_building_plan.toUpperCase());
            if (flat.building_plan_approval_date) $('#card-building-plan-date').text(flat.building_plan_approval_date);
            if (flat.oc_status) $('#card-oc-status').text(flat.oc_status.toUpperCase());
            if (flat.oc_date) $('#card-oc-date').text(flat.oc_date);
            if (flat.total_flats_in_project) $('#card-total-flats').text(flat.total_flats_in_project);
            if (flat.total_ews_flats_in_project) $('#card-total-ews').text(flat.total_ews_flats_in_project);
            if (flat.flats_offered_for_draw) $('#card-draw-flats').text(flat.flats_offered_for_draw);
            if (flat.remaining_flats_for_oc) $('#card-rem-oc').text(flat.remaining_flats_for_oc);

            // Comparison Cards: Developer entry boxes
            if (flat.license_no) $('#cmp-dev-licence').text(flat.license_no);
            if (flat.developer_name) $('#cmp-dev-developer').text(flat.developer_name).attr('title', flat.developer_name);
            if (flat.project_name) $('#cmp-dev-project').text(flat.project_name).attr('title', flat.project_name);
            if (flat.district_name || flat.town_name) {
                const distTownStr = (flat.town_name || '') + (flat.town_name && flat.district_name ? ', ' : '') + (flat.district_name || '—');
                $('#cmp-dev-district').text(distTownStr);
            }
            if (flat.sector_no) $('#cmp-dev-sector').text('Sector ' + flat.sector_no);
            if (flat.area_of_project) $('#cmp-dev-area').text(flat.area_of_project + ' Acres');

            // Re-evaluate comparison if API data is present
            if (currentTcpData) {
                renderApiComparisonData(currentTcpData);
            }
        }

        // Initialize active sidebar link
        $(document).ready(function() {
            $('#sidebar-nav-all').removeClass('text-slate-300').addClass('bg-slate-800 text-white font-bold');
            $('#sidebar-nav-all .tcp-nav-dot, #sidebar-nav-all .stp-nav-dot').removeClass('hidden');

            // If API data exists on load, render comparison statuses
            @if($hasApiSuccess)
                renderApiComparisonData(currentTcpData);
            @endif
        });
    </script>
</body>
</html>
