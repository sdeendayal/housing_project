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

            <!-- District Filter Card -->
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

            <!-- EWS STAGE 1: APPLICATIONS & PRE-VERIFICATION REJECTIONS -->
            <div class="space-y-1.5 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <h3 class="text-[9px] font-black text-slate-700 uppercase tracking-wider">Registered Applications & Eligibility Filters</h3>
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

            <!-- EWS STAGE 2: VERIFICATION PROCESS, ADC STATUS & ALLOTMENT -->
            <div class="space-y-1.5 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <h3 class="text-[9px] font-black text-slate-700 uppercase tracking-wider">EWS Draw Eligibility, Physical Verification & Allotments</h3>
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

            <!-- EWS STAGE 3: DEVELOPERS MANAGEMENT & FORM SUBMISSIONS -->
            <div class="space-y-1.5 shrink-0 pt-2 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h3 class="text-[9px] font-black text-slate-700 uppercase tracking-wider">EWS Developers Hub & Submissions Management</h3>
                    </div>
                    <a href="{{ route('ews.department.developers.index') }}" class="text-[9px] font-black text-amber-700 hover:text-amber-800 uppercase flex items-center gap-1">
                        <span>Manage Developers</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <!-- Developer Accounts Card -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-3.5 border border-slate-700 flex flex-col justify-between shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-amber-400 font-extrabold leading-none">Developer Accounts</span>
                                <h2 class="text-2xl font-black text-white font-mono mt-1">{{ number_format($developerCount) }}</h2>
                                <p class="text-[9px] text-slate-400 font-medium mt-0.5">Registered developer credentials</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30">
                                <span class="material-symbols-outlined text-base">engineering</span>
                            </span>
                        </div>
                        <div class="pt-3 border-t border-slate-700/80 flex items-center justify-between">
                            <a href="{{ route('ews.department.developers.index') }}" class="text-[9.5px] text-amber-400 hover:text-amber-300 font-black uppercase tracking-wider flex items-center gap-1">
                                <span>View / Add Developers</span>
                                <span class="material-symbols-outlined text-xs">chevron_right</span>
                            </a>
                        </div>
                    </div>

                    <!-- Developer Flat Submissions Card -->
                    <div class="bg-white rounded-xl p-3.5 border border-slate-150 flex flex-col justify-between shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none">Builder Flat Submissions</span>
                                <h2 class="text-2xl font-black text-amber-700 font-mono mt-1">{{ number_format($developerFlatsCount) }}</h2>
                                <p class="text-[9px] text-slate-500 font-medium mt-0.5">Total builder flat form entries</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-100">
                                <span class="material-symbols-outlined text-base">apartment</span>
                            </span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('ews.department.developer-flats.index') }}" class="text-[9.5px] text-amber-700 hover:text-amber-800 font-black uppercase tracking-wider flex items-center gap-1">
                                <span>View Submitted Forms</span>
                                <span class="material-symbols-outlined text-xs">chevron_right</span>
                            </a>
                        </div>
                    </div>

                    <!-- Developer Activity Logs Card -->
                    <div class="bg-white rounded-xl p-3.5 border border-slate-150 flex flex-col justify-between shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-slate-400 font-extrabold leading-none">Developer Activity Logs</span>
                                <h2 class="text-2xl font-black text-slate-800 font-mono mt-1">{{ number_format($developerLogsCount) }}</h2>
                                <p class="text-[9px] text-slate-500 font-medium mt-0.5">Action & audit logs</p>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200">
                                <span class="material-symbols-outlined text-base">receipt_long</span>
                            </span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('ews.department.developer-logs.index') }}" class="text-[9.5px] text-slate-700 hover:text-slate-900 font-black uppercase tracking-wider flex items-center gap-1">
                                <span>View Activity Logs</span>
                                <span class="material-symbols-outlined text-xs">chevron_right</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- JS Dropdown Toggle logic -->
    <script>
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

    @include('partials.global-toast')
</body>
</html>
