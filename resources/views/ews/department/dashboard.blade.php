<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Department Dashboard | Housing for All Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            overflow: hidden; /* Avoid main body scrollbars completely */
        }
    </style>
</head>
<body class="bg-[#f3f6fc] text-slate-800 h-screen flex">

    <!-- 1. Left Sidebar -->
    @include('ews.department.partials.sidebar')

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px] h-screen overflow-hidden">
        
        <!-- Top Header / Navbar -->
        <header class="h-14 flex justify-between items-center px-6 bg-white shadow-sm border-b border-slate-200 shrink-0">
            <div class="flex items-center gap-3">
                <h2 class="text-sm font-extrabold text-[#0f172a]">Department Panel</h2>
                <div class="h-4 w-[1px] bg-slate-200"></div>
                <span class="text-[11px] text-slate-500 font-medium">EWS Verification Funnel & Developer Management Dashboard</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-700">{{ $user->name }}</p>
                    <p class="text-[9px] text-slate-400 font-semibold uppercase leading-none">EWS Administrator</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-xs">
                    EW
                </div>
            </div>
        </header>

        <!-- Content Body Wrapper -->
        <main class="p-4 flex-grow flex flex-col gap-4 overflow-y-auto">

            <!-- Banner Header -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#0f172a] via-[#1e293b] to-[#334155] py-2.5 px-4 border border-slate-700/10 shrink-0 shadow-sm">
                <div class="relative flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-white text-lg">analytics</span>
                        <div>
                            <h2 class="text-xs font-black tracking-tight leading-tight">EWS Verification Funnel & Developer Operations Console</h2>
                            <p class="text-[8px] text-slate-350 font-semibold uppercase leading-none mt-0.5">Application flow tracking & developer management system</p>
                        </div>
                    </div>
                    <div class="bg-white/15 rounded px-2 py-0.5 text-[9px] font-bold">
                        <span>{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- District & Phase Filter Card -->
            <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-wrap items-center justify-between gap-3 shadow-sm shrink-0">
                <div class="flex items-center gap-4 flex-wrap">
                    <!-- District Filter -->
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-600 text-sm">filter_list</span>
                        <span class="text-[11px] font-black uppercase text-slate-750 tracking-wider">District:</span>
                        <select id="district-select" onchange="applyFilters()" class="bg-[#f8fafc] border border-slate-200 rounded-lg px-3 py-1.5 text-[10.5px] font-extrabold text-slate-700 focus:outline-none focus:border-orange-500 transition shadow-sm cursor-pointer min-w-[180px]">
                            <option value="">ALL DISTRICTS</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ $districtId == $district->id ? 'selected' : '' }}>
                                    {{ strtoupper($district->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Phase Filter -->
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-sm">filter_alt</span>
                        <span class="text-[11px] font-black uppercase text-slate-750 tracking-wider">Phase:</span>
                        <select id="phase-select" onchange="applyFilters()" class="bg-[#f8fafc] border border-slate-200 rounded-lg px-3 py-1.5 text-[10.5px] font-extrabold text-slate-700 focus:outline-none focus:border-blue-500 transition shadow-sm cursor-pointer min-w-[120px]">
                            <option value="">ALL PHASES</option>
                            <option value="1" {{ ($phase ?? '') == '1' ? 'selected' : '' }}>PHASE 1</option>
                            <option value="2" {{ ($phase ?? '') == '2' ? 'selected' : '' }}>PHASE 2</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- EWS STAGE 1: APPLICATIONS & PRE-VERIFICATION REJECTIONS -->
            <div class="space-y-2 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Registered Applications & Eligibility Filters</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Total Registration & Survey App Verification -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between min-h-[120px] shadow-sm">
                        <div>
                            <a href="{{ route('ews.department.list', ['type' => 'ppt_members', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex justify-between items-start cursor-pointer group/header">
                                <div>
                                    <span class="text-[8.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover/header:text-blue-600 transition">Total Registration</span>
                                    <h2 class="text-2xl font-black text-blue-600 font-mono mt-1 group-hover/header:text-blue-700 transition">{{ number_format($totalRegistrationCount) }}</h2>
                                </div>
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover/header:bg-blue-600 group-hover/header:text-white transition"><span class="material-symbols-outlined text-sm">groups</span></span>
                            </a>
                            
                            <div class="space-y-1 text-xs font-bold text-slate-700 mt-2 pt-2 border-t border-slate-100">
                                <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-blue-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        <span class="text-slate-655 group-hover:text-blue-800">Verify in survey app:</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-blue-700 font-extrabold font-mono">{{ number_format($registeredCount) }}</span>
                                        <span class="text-slate-300 group-hover:text-blue-700 transition flex items-center"><span class="material-symbols-outlined text-[13px]">visibility</span></span>
                                    </div>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'not_in_survey', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-rose-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-slate-655 group-hover:text-rose-800">Rejected in survey app:</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-rose-700 font-extrabold font-mono">{{ number_format($notInSurveyCount) }}</span>
                                        <span class="text-slate-300 group-hover:text-rose-700 transition flex items-center"><span class="material-symbols-outlined text-[13px]">visibility</span></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- PPP Exclusion -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId, 'phase' => $phase]) }}" class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between min-h-[98px] shadow-sm hover:shadow-md hover:border-rose-300 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-rose-600 transition whitespace-nowrap">2. PPP Exclusion</span>
                                <h2 class="text-2xl font-black text-rose-600 font-mono mt-1">{{ number_format($rejectedPppCount) }}</h2>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition"><span class="material-symbols-outlined text-sm">cancel</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-rose-600 font-black uppercase tracking-wider leading-none border-t border-slate-100 pt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Property in India -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId, 'phase' => $phase]) }}" class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between min-h-[98px] shadow-sm hover:shadow-md hover:border-rose-300 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-rose-700 transition whitespace-nowrap">3. Property in India</span>
                                <h2 class="text-2xl font-black text-rose-700 font-mono mt-1">{{ number_format($rejectedPropertyCount) }}</h2>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center group-hover:bg-rose-700 group-hover:text-white transition"><span class="material-symbols-outlined text-sm">domain_disabled</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-rose-700 font-black uppercase tracking-wider leading-none border-t border-slate-100 pt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- House Ownership -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId, 'phase' => $phase]) }}" class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between min-h-[98px] shadow-sm hover:shadow-md hover:border-rose-300 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-rose-700 transition whitespace-nowrap">4. House Ownership</span>
                                <h2 class="text-2xl font-black text-rose-700 font-mono mt-1">{{ number_format($rejectedOwnershipCount) }}</h2>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center group-hover:bg-rose-700 group-hover:text-white transition"><span class="material-symbols-outlined text-sm">home_work</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-rose-700 font-black uppercase tracking-wider leading-none border-t border-slate-100 pt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>
                </div>

                <!-- Explanation Banner 1 -->
                <div class="bg-slate-100 border border-slate-200 rounded-lg p-2 text-xs font-bold text-slate-650 flex items-center gap-2 shrink-0">
                    <span class="material-symbols-outlined text-slate-400 text-sm">info</span>
                    <span>
                        Filtration: Verify in survey app ({{ number_format($eligibleDrawCount + $rejectedPppCount + $rejectedPropertyCount + $rejectedOwnershipCount) }}) - Rejections ({{ number_format($rejectedPppCount) }} PPP + {{ number_format($rejectedPropertyCount) }} Prop + {{ number_format($rejectedOwnershipCount) }} House) = Eligible for booking ({{ number_format($eligibleDrawCount) }})
                    </span>
                </div>
            </div>

            <!-- EWS STAGE 2: VERIFICATION PROCESS, ADC STATUS & ALLOTMENT -->
            <div class="space-y-2 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">EWS Draw Eligibility, Physical Verification & Allotments</h3>
                </div>

                <div class="grid grid-cols-4 gap-3">
                    <!-- Eligible for booking -->
                    <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId, 'phase' => $phase]) }}" class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between min-h-[120px] shadow-sm hover:shadow-md hover:border-indigo-300 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-indigo-650 transition whitespace-nowrap">5. Eligible for booking</span>
                                <h2 class="text-2xl font-black text-indigo-700 font-mono mt-1.5">{{ number_format($eligibleDrawCount) }}</h2>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-650 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition"><span class="material-symbols-outlined text-sm">how_to_reg</span></span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-indigo-650 font-black uppercase tracking-wider leading-none border-t border-slate-100 pt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Booking amount received -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between min-h-[120px] shadow-sm">
                        <div>
                            <span class="text-[8.5px] uppercase text-slate-400 font-extrabold tracking-wider leading-none whitespace-nowrap">6. Booking amount received</span>
                            <div class="space-y-1 text-xs font-bold text-slate-700 mt-1.5">
                                <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-1 rounded-lg hover:bg-emerald-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-800">Received:</span>
                                        <span class="text-emerald-700 font-extrabold font-mono text-sm">{{ number_format($bookingCount) }}</span>
                                    </div>
                                    <span class="text-slate-400 group-hover:text-emerald-700 transition flex items-center p-0.5" title="View Booking amount received List"><span class="material-symbols-outlined text-sm">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-1 rounded-lg hover:bg-rose-50/80 transition cursor-pointer group border-t border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-rose-800">Not received:</span>
                                        <span class="text-rose-700 font-extrabold font-mono text-sm">{{ number_format($notVisitedCount) }}</span>
                                    </div>
                                    <span class="text-slate-400 group-hover:text-rose-700 transition flex items-center p-0.5" title="View Booking amount not received List"><span class="material-symbols-outlined text-sm">visibility</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="text-[9px] text-slate-400 font-extrabold uppercase text-right leading-none border-t border-slate-100 pt-1 mt-1 font-mono">
                            Sum: {{ number_format($bookingCount + $notVisitedCount) }}
                        </div>
                    </div>

                    <!-- ADC Verification outcomes -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between min-h-[120px] shadow-sm">
                        <div>
                            <span class="text-[8.5px] uppercase text-slate-400 font-extrabold tracking-wider leading-none whitespace-nowrap">7. Eligibility verified by ADC</span>
                            <div class="space-y-1 text-xs font-bold text-slate-700 mt-1.5">
                                <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-1 rounded-lg hover:bg-emerald-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-800">Eligible:</span>
                                        <span class="text-emerald-700 font-extrabold font-mono text-sm">{{ number_format($adcPassedCount) }}</span>
                                    </div>
                                    <span class="text-slate-400 group-hover:text-emerald-700 transition flex items-center p-0.5" title="View Eligible List"><span class="material-symbols-outlined text-sm">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-1 rounded-lg hover:bg-rose-50/80 transition cursor-pointer group border-t border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-rose-800">Not Eligible:</span>
                                        <span class="text-rose-700 font-extrabold font-mono text-sm">{{ number_format($adcFailedCount) }}</span>
                                    </div>
                                    <span class="text-slate-400 group-hover:text-rose-700 transition flex items-center p-0.5" title="View Not Eligible List"><span class="material-symbols-outlined text-sm">visibility</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="text-[9px] text-slate-400 font-extrabold uppercase text-right leading-none border-t border-slate-100 pt-1 mt-1 font-mono">
                            Sum: {{ number_format($adcPassedCount + $adcFailedCount) }}
                        </div>
                    </div>

                    <!-- Final Draw Outcomes -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between min-h-[120px] shadow-sm">
                        <div>
                            <span class="text-[8.5px] uppercase text-slate-400 font-extrabold tracking-wider leading-none whitespace-nowrap">8. Flat allocation status</span>
                            <div class="space-y-1 text-xs font-bold text-slate-700 mt-1">
                                <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-1 rounded-lg hover:bg-emerald-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-[11.5px] font-bold text-slate-700 group-hover:text-emerald-800">Allotted:</span>
                                        <span class="text-emerald-700 font-extrabold font-mono text-xs">{{ number_format($allottedCount) }}</span>
                                    </div>
                                    <span class="text-slate-400 group-hover:text-emerald-700 transition flex items-center p-0.5" title="View Allotted List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-1 rounded-lg hover:bg-amber-50/80 transition cursor-pointer group border-t border-slate-100">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                                        <span class="text-[11.5px] font-bold text-slate-700 group-hover:text-amber-800">Waiting:</span>
                                        <span class="text-amber-700 font-extrabold font-mono text-xs">{{ number_format($pendingCount) }}</span>
                                    </div>
                                    <span class="text-slate-400 group-hover:text-amber-700 transition flex items-center p-0.5" title="View Waiting List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer group border-t border-slate-100">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shrink-0"></span>
                                        <span class="text-[11.5px] font-bold text-slate-700 group-hover:text-slate-900">Unallotted:</span>
                                        <span class="text-slate-655 font-extrabold font-mono text-xs">{{ number_format($drawRemainingCount) }}</span>
                                    </div>
                                    <span class="text-slate-400 group-hover:text-slate-700 transition flex items-center p-0.5" title="View Unallotted List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="text-[9.5px] text-slate-400 font-extrabold uppercase text-right leading-none border-t border-slate-100 pt-1 mt-1">
                            Sum: {{ number_format($allottedCount + $pendingCount + $drawRemainingCount) }}
                        </div>
                    </div>
                </div>

                <!-- Explanation Banner 2 -->
                <div class="bg-slate-100 border border-slate-200 rounded-lg p-2 text-xs font-bold text-slate-650 flex items-center gap-2 shrink-0">
                    <span class="material-symbols-outlined text-slate-400 text-sm">info</span>
                    <span>
                        Outcomes: Booking Amount Received ({{ number_format($bookingCount) }}) -> Eligible ({{ number_format($adcPassedCount) }}) [Allotted: {{ $allottedCount }} + Waiting: {{ $pendingCount }} + Unallotted: {{ $drawRemainingCount }}] & Not Eligible ({{ number_format($adcFailedCount) }})
                    </span>
                </div>
            </div>

            <!-- EWS STAGE 3: DEVELOPERS MANAGEMENT & FORM SUBMISSIONS -->
            <div class="space-y-2 shrink-0 pt-2 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">EWS Developers Hub & Submissions Management</h3>
                    </div>
                    <a href="{{ route('ews.department.developers.index') }}" class="text-xs font-black text-amber-700 hover:text-amber-800 uppercase flex items-center gap-1">
                        <span>Manage Developers</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <!-- Developer Accounts Card -->
                    <a href="{{ route('ews.department.developers.index') }}" class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-3.5 border border-slate-700 flex flex-col justify-between shadow-sm hover:shadow-md hover:border-slate-600 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10.5px] uppercase tracking-wider text-amber-400 font-extrabold leading-none group-hover:text-amber-300 transition">Developer Accounts</span>
                                <h2 class="text-2xl font-black text-white font-mono mt-1">{{ number_format($developerCount) }}</h2>
                                <p class="text-xs text-slate-300 font-medium mt-0.5">Registered developer credentials</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30 group-hover:bg-amber-500 group-hover:text-slate-900 transition">
                                <span class="material-symbols-outlined text-base">engineering</span>
                            </span>
                        </div>
                        <div class="pt-3 border-t border-slate-700/80 flex items-center justify-between text-xs text-amber-400 font-black uppercase tracking-wider">
                            <span>View / Add Developers</span>
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Developer Flat Submissions Card -->
                    <a href="{{ route('ews.department.developer-flats.index') }}" class="bg-white rounded-xl p-3.5 border border-slate-150 flex flex-col justify-between shadow-sm hover:shadow-md hover:border-amber-300 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-amber-700 transition">Builder Flat Submissions</span>
                                <h2 class="text-2xl font-black text-amber-700 font-mono mt-1">{{ number_format($developerFlatsCount) }}</h2>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">Total builder flat form entries</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-100 group-hover:bg-amber-600 group-hover:text-white transition">
                                <span class="material-symbols-outlined text-base">apartment</span>
                            </span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-amber-700 font-black uppercase tracking-wider">
                            <span>View Submitted Forms</span>
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Developer Activity Logs Card -->
                    <a href="{{ route('ews.department.developer-logs.index') }}" class="bg-white rounded-xl p-3.5 border border-slate-150 flex flex-col justify-between shadow-sm hover:shadow-md hover:border-slate-300 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-slate-700 transition">Developer Activity Logs</span>
                                <h2 class="text-2xl font-black text-slate-800 font-mono mt-1">{{ number_format($developerLogsCount) }}</h2>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">Action & audit logs</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200 group-hover:bg-slate-800 group-hover:text-white transition">
                                <span class="material-symbols-outlined text-base">receipt_long</span>
                            </span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-700 font-black uppercase tracking-wider">
                            <span>View Activity Logs</span>
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>
                </div>
            </div>

        </main>
    </div>

    <!-- JS Dropdown Toggle logic -->
    <script>
        function applyFilters() {
            let url = new URL(window.location.href);
            let districtId = document.getElementById('district-select').value;
            let phase = document.getElementById('phase-select').value;
            
            if (districtId) {
                url.searchParams.set('district_id', districtId);
            } else {
                url.searchParams.delete('district_id');
            }
            
            if (phase) {
                url.searchParams.set('phase', phase);
            } else {
                url.searchParams.delete('phase');
            }
            
            window.location.href = url.toString();
        }
    </script>

    @include('partials.global-toast')
</body>
</html>
