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
    <aside class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-5 z-40 bg-[#1e293b] text-slate-300 shadow-xl border-r border-slate-800">
        <!-- Logo -->
        <div class="px-6 mb-5 flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-tr from-orange-600 to-amber-500 rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-lg font-bold">business</span>
            </div>
            <div>
                <a href="{{ route('ews.department.dashboard') }}">
                    <h1 class="text-sm font-extrabold text-white leading-tight">EWS DEPT</h1>
                    <p class="text-[8px] uppercase tracking-wider text-orange-400 font-bold">Housing Haryana</p>
                </a>
            </div>
        </div>

        <!-- Collapsible Structured Sidebar Navigation Links -->
        <nav class="flex-grow px-3 space-y-2 overflow-y-auto text-xs">
            
            <!-- Dashboard Link -->
            <a href="{{ route('ews.department.dashboard') }}" class="w-full flex items-center gap-3 rounded-lg px-4 py-2 bg-orange-650 text-white font-bold transition-all text-left">
                <span class="material-symbols-outlined text-base">dashboard</span>
                <span>Overview Dashboard</span>
            </a>

            <!-- Main Dropdown Toggle Button -->
            <button onclick="toggleFunnelSubmenu()" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left font-bold text-slate-350">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-base">filter_alt</span>
                    <span>EWS Registry Funnel</span>
                </div>
                <span id="submenu-arrow" class="material-symbols-outlined text-sm">keyboard_arrow_right</span>
            </button>

            <!-- Collapsible submenus wrapper -->
            <div id="funnel-submenus" class="hidden space-y-3.5 pl-2 border-l border-slate-700/60 ml-4 transition-all duration-300">
                <!-- Group 1: Registration Phase -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">1. Registration Phase</div>
                    <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">list_alt</span>
                            <span>Registered</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($registeredCount) }}</span>
                    </a>
                </div>

                <!-- Group 2: Eligibility Rejections -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">2. Eligibility Rejections</div>
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">cancel</span>
                            <span>PPP Exclusion</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedPppCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">domain_disabled</span>
                            <span>Property in India</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedPropertyCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">home_work</span>
                            <span>House Ownership</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedOwnershipCount) }}</span>
                    </a>
                </div>

                <!-- Group 3: Verification Visited/Absent -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">3. Verification Visited/Absent</div>
                    <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">how_to_reg</span>
                            <span>Eligible for Draw</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($eligibleDrawCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            <span>Visited</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($bookingCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">warning</span>
                            <span>Absent</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($notVisitedCount) }}</span>
                    </a>
                </div>

                <!-- Group 4: ADC Verification Outcomes -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">4. ADC Verification Outcomes</div>
                    <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">check_circle_outline</span>
                            <span>Passed</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($adcPassedCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">error_outline</span>
                            <span>Failed</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($adcFailedCount) }}</span>
                    </a>
                </div>

                <!-- Group 5: Final Draw Allotment -->
                <div class="space-y-1">
                    <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">5. Final Draw Allotment</div>
                    <a href="{{ route('ews.department.list', ['type' => 'all', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">groups</span>
                            <span>Total Beneficiaries</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($totalCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            <span>Allotted</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($allottedCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">hourglass_empty</span>
                            <span>Pending</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($pendingCount) }}</span>
                    </a>
                    <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId]) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">hourglass_disabled</span>
                            <span>Unallotted Draw</span>
                        </div>
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($drawRemainingCount) }}</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="mt-auto px-6 pt-4 border-t border-slate-800">
            <div class="mb-4 px-2">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Active Scheme</p>
                <p class="text-xs font-bold text-orange-400 uppercase mt-0.5">EWS HOUSING</p>
            </div>
            <a href="{{ route('ews.department.logout') }}" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-rose-500 hover:bg-rose-950/30 hover:text-rose-400 transition-all font-bold">
                <span class="material-symbols-outlined text-base">logout</span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px] h-screen overflow-hidden">
        
        <!-- Top Header / Navbar -->
        <header class="h-14 flex justify-between items-center px-6 bg-white shadow-sm border-b border-slate-200 shrink-0">
            <div class="flex items-center gap-3">
                <h2 class="text-sm font-extrabold text-[#0f172a]">Department Panel</h2>
                <div class="h-4 w-[1px] bg-slate-200"></div>
                <span class="text-[11px] text-slate-500 font-medium">EWS Verification Funnel & Analytics Dashboard</span>
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

        <!-- Content Body Wrapper (Compact layout, no vertical scrolling) -->
        <main class="p-4 flex-grow flex flex-col gap-4 overflow-hidden">

            <!-- Banner Header (Super Compact) -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#0f172a] via-[#1e293b] to-[#334155] py-2.5 px-4 border border-slate-700/10 shrink-0 shadow-sm">
                <div class="relative flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-white text-lg">analytics</span>
                        <div>
                            <h2 class="text-xs font-black tracking-tight leading-tight">EWS Verification Funnel Dashboard</h2>
                            <p class="text-[8px] text-slate-350 font-semibold uppercase leading-none mt-0.5">Application flow tracking console</p>
                        </div>
                    </div>
                    <div class="bg-white/15 rounded px-2 py-0.5 text-[9px] font-bold">
                        <span>{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- District Filter Card (Premium Design) -->
            <div class="bg-white rounded-xl p-3 border border-slate-150 flex items-center justify-between shadow-sm shrink-0">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-orange-600 text-sm">filter_list</span>
                    <span class="text-[11px] font-black uppercase text-slate-750 tracking-wider">Filter by District:</span>
                </div>
                <div class="flex items-center gap-3">
                    <select id="district-select" onchange="filterByDistrict(this.value)" class="bg-[#f8fafc] border border-slate-200 rounded-lg px-3 py-1.5 text-[10.5px] font-extrabold text-slate-700 focus:outline-none focus:border-orange-500 transition shadow-sm cursor-pointer min-w-[200px]">
                        <option value="">ALL DISTRICTS</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ $districtId == $district->id ? 'selected' : '' }}>
                                {{ strtoupper($district->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- EWS STAGE 1: APPLICATIONS & PRE-VERIFICATION REJECTIONS (High Density) -->
            <div class="space-y-1.5 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <h3 class="text-[9px] font-black text-slate-700 uppercase tracking-wider">Phase 1: Registered Applications & Eligibility Filters</h3>
                </div>

                <div class="grid grid-cols-4 gap-3">
                    <!-- Total Registered -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between h-[85px] shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none">1. Registered</span>
                                <h2 class="text-xl font-black text-blue-600 font-mono mt-0.5">{{ number_format($registeredCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded bg-blue-50 text-blue-600 flex items-center justify-center"><span class="material-symbols-outlined text-xs">list_alt</span></span>
                        </div>
                        <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId]) }}" class="flex items-center justify-between text-[9px] text-blue-600 hover:text-blue-700 font-black uppercase tracking-wider leading-none">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs">chevron_right</span>
                        </a>
                    </div>

                    <!-- PPP Exclusion -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between h-[85px] shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none">2. PPP Exclusion</span>
                                <h2 class="text-xl font-black text-rose-600 font-mono mt-0.5">{{ number_format($rejectedPppCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded bg-rose-50 text-rose-600 flex items-center justify-center"><span class="material-symbols-outlined text-xs">cancel</span></span>
                        </div>
                        <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId]) }}" class="flex items-center justify-between text-[9px] text-rose-600 hover:text-rose-700 font-black uppercase tracking-wider leading-none">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs">chevron_right</span>
                        </a>
                    </div>

                    <!-- Property in India -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between h-[85px] shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none">3. Prop in India</span>
                                <h2 class="text-xl font-black text-rose-700 font-mono mt-0.5">{{ number_format($rejectedPropertyCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded bg-rose-50 text-rose-700 flex items-center justify-center"><span class="material-symbols-outlined text-xs">domain_disabled</span></span>
                        </div>
                        <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId]) }}" class="flex items-center justify-between text-[9px] text-rose-700 hover:text-rose-800 font-black uppercase tracking-wider leading-none">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs">chevron_right</span>
                        </a>
                    </div>

                    <!-- House Ownership -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between h-[85px] shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none">4. House Ownership</span>
                                <h2 class="text-xl font-black text-rose-700 font-mono mt-0.5">{{ number_format($rejectedOwnershipCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded bg-rose-50 text-rose-700 flex items-center justify-center"><span class="material-symbols-outlined text-xs">home_work</span></span>
                        </div>
                        <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId]) }}" class="flex items-center justify-between text-[9px] text-rose-700 hover:text-rose-800 font-black uppercase tracking-wider leading-none">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs">chevron_right</span>
                        </a>
                    </div>
                </div>

                <!-- Explanation Banner 1 -->
                <div class="bg-slate-100 border border-slate-200 rounded-lg p-2 text-[10px] font-bold text-slate-650 flex items-center gap-2 shrink-0">
                    <span class="material-symbols-outlined text-slate-400 text-xs">info</span>
                    <span>
                        Filtration: Registered ({{ number_format($registeredCount) }}) - Rejections ({{ number_format($rejectedPppCount) }} PPP + {{ number_format($rejectedPropertyCount) }} Prop + {{ number_format($rejectedOwnershipCount) }} House) = Eligible for Draw ({{ number_format($eligibleDrawCount) }})
                    </span>
                </div>
            </div>

            <!-- EWS STAGE 2: VERIFICATION PROCESS, ADC STATUS & ALLOTMENT (High Density) -->
            <div class="space-y-1.5 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <h3 class="text-[9px] font-black text-slate-700 uppercase tracking-wider">Phase 2: EWS Draw Eligibility, Physical Verification & Allotments</h3>
                </div>

                <div class="grid grid-cols-4 gap-3">
                    <!-- Eligible for Draw -->
                    <div class="bg-white rounded-xl p-3 border border-slate-150 flex flex-col justify-between h-[100px] shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none">5. Eligible for Draw</span>
                                <h2 class="text-xl font-black text-indigo-700 font-mono mt-0.5">{{ number_format($eligibleDrawCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded bg-indigo-50 text-indigo-650 flex items-center justify-center"><span class="material-symbols-outlined text-xs">how_to_reg</span></span>
                        </div>
                        <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId]) }}" class="flex items-center justify-between text-[9px] text-indigo-650 hover:text-indigo-700 font-black uppercase tracking-wider leading-none">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs">chevron_right</span>
                        </a>
                    </div>

                    <!-- Verification Visited / Absent -->
                    <div class="bg-white rounded-xl p-2.5 border border-slate-150 flex flex-col justify-between h-[100px] shadow-sm">
                        <div>
                            <span class="text-[7.5px] uppercase text-slate-400 font-extrabold tracking-wider leading-none">6. Verification Visits</span>
                            <div class="space-y-1.5 text-[10px] font-bold text-slate-700 mt-1.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                        <span class="text-[9px]">Visited:</span>
                                        <span class="text-emerald-700 font-extrabold font-mono text-[9.5px]">{{ number_format($bookingCount) }}</span>
                                    </div>
                                    <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId]) }}" class="text-slate-450 hover:text-orange-600 transition flex items-center"><span class="material-symbols-outlined text-[10px]">visibility</span></a>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-1">
                                    <div class="flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                        <span class="text-[9px]">Absent:</span>
                                        <span class="text-rose-700 font-extrabold font-mono text-[9.5px]">{{ number_format($notVisitedCount) }}</span>
                                    </div>
                                    <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId]) }}" class="text-slate-450 hover:text-orange-600 transition flex items-center"><span class="material-symbols-outlined text-[10px]">visibility</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="text-[7px] text-slate-400 font-black uppercase text-right leading-none border-t border-slate-100 pt-1">
                            Sum: {{ number_format($bookingCount + $notVisitedCount) }}
                        </div>
                    </div>

                    <!-- ADC Verification outcomes -->
                    <div class="bg-white rounded-xl p-2.5 border border-slate-150 flex flex-col justify-between h-[100px] shadow-sm">
                        <div>
                            <span class="text-[7.5px] uppercase text-slate-400 font-extrabold tracking-wider leading-none">7. ADC Verification Status</span>
                            <div class="space-y-1.5 text-[10px] font-bold text-slate-700 mt-1.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                        <span class="text-[9px]">Passed:</span>
                                        <span class="text-emerald-700 font-extrabold font-mono text-[9.5px]">{{ number_format($adcPassedCount) }}</span>
                                    </div>
                                    <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId]) }}" class="text-slate-450 hover:text-orange-600 transition flex items-center"><span class="material-symbols-outlined text-[10px]">visibility</span></a>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-1">
                                    <div class="flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                        <span class="text-[9px]">Failed:</span>
                                        <span class="text-rose-700 font-extrabold font-mono text-[9.5px]">{{ number_format($adcFailedCount) }}</span>
                                    </div>
                                    <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId]) }}" class="text-slate-450 hover:text-orange-600 transition flex items-center"><span class="material-symbols-outlined text-[10px]">visibility</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="text-[7px] text-slate-400 font-black uppercase text-right leading-none border-t border-slate-100 pt-1">
                            Sum: {{ number_format($adcPassedCount + $adcFailedCount) }}
                        </div>
                    </div>

                    <!-- Final Draw Outcomes -->
                    <div class="bg-white rounded-xl p-2.5 border border-slate-150 flex flex-col justify-between h-[100px] shadow-sm">
                        <div>
                            <span class="text-[7.5px] uppercase text-slate-400 font-extrabold tracking-wider leading-none">8. Allocation Outcomes</span>
                            <div class="space-y-1 text-[9.5px] font-bold text-slate-700 mt-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                        <span>Allotted:</span>
                                        <span class="text-emerald-700 font-mono text-[9px]">{{ number_format($allottedCount) }}</span>
                                    </div>
                                    <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId]) }}" class="text-slate-450 hover:text-orange-600 transition flex items-center"><span class="material-symbols-outlined text-[9px]">visibility</span></a>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-0.5">
                                    <div class="flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                        <span>Pending:</span>
                                        <span class="text-amber-700 font-mono text-[9px]">{{ number_format($pendingCount) }}</span>
                                    </div>
                                    <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId]) }}" class="text-slate-450 hover:text-orange-600 transition flex items-center"><span class="material-symbols-outlined text-[9px]">visibility</span></a>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-0.5">
                                    <div class="flex items-center gap-1">
                                        <span class="w-1 h-1 rounded-full bg-slate-450"></span>
                                        <span>Unallotted:</span>
                                        <span class="text-slate-650 font-mono text-[9px]">{{ number_format($drawRemainingCount) }}</span>
                                    </div>
                                    <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId]) }}" class="text-slate-450 hover:text-orange-600 transition flex items-center"><span class="material-symbols-outlined text-[9px]">visibility</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="text-[7px] text-slate-400 font-black uppercase text-right leading-none border-t border-slate-100 pt-0.5">
                            Sum: {{ number_format($adcPassedCount) }}
                        </div>
                    </div>
                </div>

                <!-- Explanation Banner 2 -->
                <div class="bg-slate-100 border border-slate-200 rounded-lg p-2 text-[10px] font-bold text-slate-650 flex items-center gap-2 shrink-0">
                    <span class="material-symbols-outlined text-slate-400 text-xs">info</span>
                    <span>
                        Outcomes: Visited ({{ number_format($bookingCount) }}) -> Passed ({{ number_format($adcPassedCount) }}) [Allotted: {{ $allottedCount }} + Pending: {{ $pendingCount }} + Unallotted: {{ $drawRemainingCount }}] & Failed ({{ number_format($adcFailedCount) }})
                    </span>
                </div>
            </div>

        </main>
    </div>

    <!-- JS Dropdown Toggle logic -->
    <script>
        function toggleFunnelSubmenu() {
            const container = document.getElementById('funnel-submenus');
            const arrow = document.getElementById('submenu-arrow');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                arrow.textContent = 'keyboard_arrow_down';
            } else {
                container.classList.add('hidden');
                arrow.textContent = 'keyboard_arrow_right';
            }
        }

        function filterByDistrict(districtId) {
            let url = new URL(window.location.href);
            if (districtId) {
                url.searchParams.set('district_id', districtId);
            } else {
                url.searchParams.delete('district_id');
            }
            window.location.href = url.toString();
        }
    </script>

</body>
</html>
