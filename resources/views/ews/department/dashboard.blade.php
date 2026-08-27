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
            background-color: #f1f8f4;
        }
        /* Custom premium scrollbar */
        main::-webkit-scrollbar {
            width: 5px;
        }
        main::-webkit-scrollbar-track {
            background: transparent;
        }
        main::-webkit-scrollbar-thumb {
            background: #a7f3d0;
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb:hover {
            background: #34d399;
        }

        /* Slide & Fade in animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            opacity: 0;
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.1s; }
        .delay-3 { animation-delay: 0.15s; }
        .delay-4 { animation-delay: 0.2s; }

        /* Subtle fika green gradient cards */
        .premium-card {
            background: linear-gradient(135deg, #ffffff 0%, #f3faf6 100%);
            border: 1px solid #d1fae5;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .premium-card:hover {
            transform: translateY(-2px);
            border-color: rgba(16, 185, 129, 0.5) !important;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.1), 0 8px 8px -6px rgba(16, 185, 129, 0.1) !important;
        }

        /* Pulse indicator */
        .pulse-dot {
            position: relative;
        }
        .pulse-dot::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: inherit;
            border-radius: inherit;
            animation: pulse 1.6s infinite;
            opacity: 0.6;
        }
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.6;
            }
            100% {
                transform: scale(2.4);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-[#f1f8f4] text-slate-800 h-screen flex">

    <!-- 1. Left Sidebar -->
    @include('ews.department.partials.sidebar')

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px] h-screen overflow-hidden">
        
        <!-- Top Header / Navbar -->
        <header class="h-14 flex justify-between items-center px-6 bg-white shadow-sm border-b border-emerald-100 shrink-0">
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
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shadow-inner">
                    EW
                </div>
            </div>
        </header>

        <!-- Content Body Wrapper -->
        <main class="px-5 py-4 flex-grow flex flex-col gap-4 overflow-y-auto">

            <!-- Banner Header -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#064e3b] via-[#022c22] to-[#0f172a] py-2 px-4 border border-emerald-800/10 shrink-0 shadow-sm animate-fade-in-up">
                <div class="relative flex items-center justify-between text-white">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-white text-base">analytics</span>
                        <div>
                            <h2 class="text-xs font-black tracking-wider leading-tight uppercase">EWS Operations Console</h2>
                            <p class="text-[8px] text-slate-350 font-semibold uppercase leading-none mt-0.5">Application flow tracking & developer management system</p>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded border border-white/10 px-2 py-0.5 text-[8.5px] font-bold tracking-wider">
                        <span>{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- District & Phase Filter Card -->
            <div class="bg-white rounded-xl p-2.5 border border-slate-150 flex flex-wrap items-center justify-between gap-3 shadow-sm shrink-0 animate-fade-in-up delay-1">
                <div class="flex items-center gap-4 flex-wrap w-full md:w-auto justify-between md:justify-start">
                    <!-- District Filter -->
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-sm">filter_list</span>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">District:</span>
                        <select id="district-select" onchange="applyFilters()" class="bg-[#f8fafc] border border-slate-200 rounded-lg px-2.5 py-1 text-[10px] font-extrabold text-slate-700 focus:outline-none focus:border-emerald-500 transition shadow-sm cursor-pointer min-w-[160px]">
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
                        <span class="material-symbols-outlined text-emerald-500 text-sm">filter_alt</span>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Phase:</span>
                        <select id="phase-select" onchange="applyFilters()" class="bg-[#f8fafc] border border-slate-200 rounded-lg px-2.5 py-1 text-[10px] font-extrabold text-slate-700 focus:outline-none focus:border-emerald-500 transition shadow-sm cursor-pointer min-w-[110px]">
                            <option value="">ALL PHASES</option>
                            <option value="1" {{ ($phase ?? '') == '1' ? 'selected' : '' }}>PHASE 1</option>
                            <option value="2" {{ ($phase ?? '') == '2' ? 'selected' : '' }}>PHASE 2</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- EWS STAGE 1: APPLICATIONS & PRE-VERIFICATION REJECTIONS -->
            <div class="space-y-1.5 shrink-0 animate-fade-in-up delay-2">
                <div class="flex items-center gap-2 pb-0.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></span>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Stage 01 / Pre-Verification Filters</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Total Registration & Survey App Verification -->
                    <div class="premium-card premium-card-blue rounded-xl p-3 flex flex-col justify-between shadow-sm min-h-[105px]">
                        <div>
                            <a href="{{ route('ews.department.list', ['type' => 'ppt_members', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex justify-between items-start cursor-pointer group/header">
                                <div>
                                    <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover/header:text-blue-600 transition">Total Registration</span>
                                    <h2 class="text-xl font-black text-blue-600 font-mono mt-0.5 group-hover/header:text-blue-700 transition">{{ number_format($totalRegistrationCount) }}</h2>
                                </div>
                                <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 group-hover/header:bg-blue-600 group-hover/header:text-white transition"><span class="material-symbols-outlined text-xs">groups</span></span>
                            </a>
                            
                            <div class="space-y-1 text-[10px] font-bold text-slate-700 mt-1.5 pt-1.5 border-t border-slate-100">
                                <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-blue-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-blue-800">Verified in Survey:</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-blue-700 font-extrabold font-mono">{{ number_format($registeredCount) }}</span>
                                        <span class="text-slate-300 group-hover:text-blue-700 transition flex items-center"><span class="material-symbols-outlined text-[10px]">visibility</span></span>
                                    </div>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'not_in_survey', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-rose-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-rose-800">Rejected in Survey:</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-rose-700 font-extrabold font-mono">{{ number_format($notInSurveyCount) }}</span>
                                        <span class="text-slate-300 group-hover:text-rose-700 transition flex items-center"><span class="material-symbols-outlined text-[10px]">visibility</span></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- PPP Exclusion -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId, 'phase' => $phase]) }}" class="premium-card premium-card-rose rounded-xl p-3 flex flex-col justify-between shadow-sm hover:border-rose-300 min-h-[105px] group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-rose-600 transition">2. PPP Exclusion</span>
                                <h2 class="text-xl font-black text-rose-600 font-mono mt-0.5">{{ number_format($rejectedPppCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 group-hover:bg-rose-600 group-hover:text-white transition"><span class="material-symbols-outlined text-xs">cancel</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] text-rose-600 font-black uppercase tracking-wider leading-none border-t border-slate-100 pt-1.5 mt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Property in India -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId, 'phase' => $phase]) }}" class="premium-card premium-card-rose rounded-xl p-3 flex flex-col justify-between shadow-sm hover:border-rose-300 min-h-[105px] group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-rose-700 transition">3. Property in India</span>
                                <h2 class="text-xl font-black text-rose-700 font-mono mt-0.5">{{ number_format($rejectedPropertyCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center border border-rose-100 group-hover:bg-rose-750 group-hover:text-white transition"><span class="material-symbols-outlined text-xs">domain_disabled</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] text-rose-700 font-black uppercase tracking-wider leading-none border-t border-slate-100 pt-1.5 mt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- House Ownership -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId, 'phase' => $phase]) }}" class="premium-card premium-card-rose rounded-xl p-3 flex flex-col justify-between shadow-sm hover:border-rose-300 min-h-[105px] group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-rose-700 transition">4. House Ownership</span>
                                <h2 class="text-xl font-black text-rose-700 font-mono mt-0.5">{{ number_format($rejectedOwnershipCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center border border-rose-100 group-hover:bg-rose-750 group-hover:text-white transition"><span class="material-symbols-outlined text-xs">home_work</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] text-rose-700 font-black uppercase tracking-wider leading-none border-t border-slate-100 pt-1.5 mt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>
                </div>

                <!-- Explanation Banner 1 -->
                <div class="bg-slate-100 border border-slate-200 rounded-lg py-1.5 px-3 text-[10px] font-bold text-slate-600 flex items-center gap-2 shrink-0">
                    <span class="material-symbols-outlined text-slate-400 text-sm">info</span>
                    <span>
                        Filtration: Verify in survey app ({{ number_format($eligibleDrawCount + $rejectedPppCount + $rejectedPropertyCount + $rejectedOwnershipCount) }}) - Rejections ({{ number_format($rejectedPppCount) }} PPP + {{ number_format($rejectedPropertyCount) }} Prop + {{ number_format($rejectedOwnershipCount) }} House) = Eligible for booking ({{ number_format($eligibleDrawCount) }})
                    </span>
                </div>
            </div>

            <!-- EWS STAGE 2: VERIFICATION PROCESS, ADC STATUS & ALLOTMENT -->
            <div class="space-y-1.5 shrink-0 animate-fade-in-up delay-3">
                <div class="flex items-center gap-2 pb-0.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></span>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Stage 02 / Verification, ADC Status & Allotments</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Eligible for booking -->
                    <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId, 'phase' => $phase]) }}" class="premium-card premium-card-indigo rounded-xl p-3 flex flex-col justify-between shadow-sm hover:border-indigo-350 min-h-[125px] group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-indigo-650 transition">5. Eligible for booking</span>
                                <h2 class="text-xl font-black text-indigo-700 font-mono mt-1">{{ number_format($eligibleDrawCount) }}</h2>
                            </div>
                            <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-650 flex items-center justify-center border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition"><span class="material-symbols-outlined text-xs">how_to_reg</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] text-indigo-650 font-black uppercase tracking-wider leading-none border-t border-slate-100 pt-1.5">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Booking amount received -->
                    <div class="premium-card premium-card-emerald rounded-xl p-3 flex flex-col justify-between shadow-sm min-h-[125px]">
                        <div>
                            <span class="text-[8px] uppercase text-slate-400 font-extrabold tracking-wider leading-none">6. Booking Amount</span>
                            <div class="space-y-1.5 text-[10px] font-bold text-slate-700 mt-2">
                                <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-emerald-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-emerald-800">Received:</span>
                                        <span class="text-emerald-700 font-extrabold font-mono">{{ number_format($bookingCount) }}</span>
                                    </div>
                                    <span class="text-slate-350 group-hover:text-emerald-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-rose-50/80 transition cursor-pointer group border-t border-slate-100/50 pt-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-rose-800">Not Received:</span>
                                        <span class="text-rose-700 font-extrabold font-mono">{{ number_format($notVisitedCount) }}</span>
                                    </div>
                                    <span class="text-slate-355 group-hover:text-rose-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="text-[8px] text-slate-400 font-extrabold uppercase text-right leading-none border-t border-slate-100 pt-1 font-mono">
                            Sum: {{ number_format($bookingCount + $notVisitedCount) }}
                        </div>
                    </div>

                    <!-- ADC Verification outcomes -->
                    <div class="premium-card premium-card-emerald rounded-xl p-3 flex flex-col justify-between shadow-sm min-h-[125px]">
                        <div>
                            <span class="text-[8px] uppercase text-slate-400 font-extrabold tracking-wider leading-none">7. ADC Verification</span>
                            <div class="space-y-1.5 text-[10px] font-bold text-slate-700 mt-2">
                                <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-emerald-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-emerald-800">Eligible:</span>
                                        <span class="text-emerald-700 font-extrabold font-mono">{{ number_format($adcPassedCount) }}</span>
                                    </div>
                                    <span class="text-slate-350 group-hover:text-emerald-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-rose-50/80 transition cursor-pointer group border-t border-slate-100/50 pt-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-rose-800">Not Eligible:</span>
                                        <span class="text-rose-700 font-extrabold font-mono">{{ number_format($adcFailedCount) }}</span>
                                    </div>
                                    <span class="text-slate-355 group-hover:text-rose-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="text-[8px] text-slate-400 font-extrabold uppercase text-right leading-none border-t border-slate-100 pt-1 font-mono">
                            Sum: {{ number_format($adcPassedCount + $adcFailedCount) }}
                        </div>
                    </div>

                    <!-- Final Draw Outcomes -->
                    <div class="premium-card premium-card-amber rounded-xl p-3 flex flex-col justify-between shadow-sm min-h-[125px]">
                        <div>
                            <span class="text-[8px] uppercase text-slate-400 font-extrabold tracking-wider leading-none">8. Allocation Status</span>
                            <div class="space-y-1 text-[10px] font-bold text-slate-700 mt-1">
                                <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-emerald-50/80 transition cursor-pointer group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-emerald-800">Allotted:</span>
                                        <span class="text-emerald-700 font-extrabold font-mono text-[11px]">{{ number_format($allottedCount) }}</span>
                                    </div>
                                    <span class="text-slate-350 group-hover:text-emerald-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-amber-50/80 transition cursor-pointer group border-t border-slate-100/50 pt-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-amber-800">Waiting:</span>
                                        <span class="text-amber-700 font-extrabold font-mono text-[11px]">{{ number_format($pendingCount) }}</span>
                                    </div>
                                    <span class="text-slate-350 group-hover:text-amber-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-slate-100 transition cursor-pointer group border-t border-slate-100/50 pt-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-slate-450 shrink-0"></span>
                                        <span class="text-slate-500 group-hover:text-slate-900">Unallotted:</span>
                                        <span class="text-slate-655 font-extrabold font-mono text-[11px]">{{ number_format($drawRemainingCount) }}</span>
                                    </div>
                                    <span class="text-slate-350 group-hover:text-slate-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="text-[8px] text-slate-400 font-extrabold uppercase text-right leading-none border-t border-slate-100 pt-0.5 font-mono">
                            Sum: {{ number_format($allottedCount + $pendingCount + $drawRemainingCount) }}
                        </div>
                    </div>
                </div>

                <!-- Explanation Banner 2 -->
                <div class="bg-slate-100 border border-slate-200 rounded-lg py-1.5 px-3 text-[10px] font-bold text-slate-600 flex items-center gap-2 shrink-0">
                    <span class="material-symbols-outlined text-slate-400 text-sm">info</span>
                    <span>
                        Outcomes: Booking Amount Received ({{ number_format($bookingCount) }}) -> Eligible ({{ number_format($adcPassedCount) }}) [Allotted: {{ $allottedCount }} + Waiting: {{ $pendingCount }} + Unallotted: {{ $drawRemainingCount }}] & Not Eligible ({{ number_format($adcFailedCount) }})
                    </span>
                </div>
            </div>

            <!-- EWS STAGE 3: DEVELOPERS MANAGEMENT & FORM SUBMISSIONS -->
            <div class="space-y-1.5 shrink-0 pt-1 border-t border-slate-200 animate-fade-in-up delay-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></span>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Stage 03 / Developers & Audits</h3>
                    </div>
                    <a href="{{ route('ews.department.developers.index') }}" class="text-[9px] font-black text-emerald-700 hover:text-emerald-800 uppercase flex items-center gap-0.5 transition">
                        <span>Manage Developers</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <!-- Developer Accounts Card -->
                    <a href="{{ route('ews.department.developers.index') }}" class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-3 border border-slate-700 flex flex-col justify-between min-h-[90px] shadow-sm hover:shadow-md hover:border-slate-600 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-emerald-400 font-extrabold leading-none group-hover:text-emerald-300 transition">Developer Accounts</span>
                                <h2 class="text-xl font-black text-white font-mono mt-0.5">{{ number_format($developerCount) }}</h2>
                                <p class="text-[9px] text-slate-350 font-medium leading-tight">Registered credentials</p>
                            </div>
                            <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 group-hover:bg-emerald-500 group-hover:text-slate-900 transition">
                                <span class="material-symbols-outlined text-xs">engineering</span>
                            </span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-700/80 flex items-center justify-between text-[9px] text-emerald-400 font-black uppercase tracking-wider mt-1.5">
                            <span>Manage Credentials</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Developer Flat Submissions Card -->
                    <a href="{{ route('ews.department.developer-flats.index') }}" class="premium-card rounded-xl p-3 flex flex-col justify-between min-h-[90px] shadow-sm hover:border-emerald-350 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-emerald-700 transition">Builder Flat Submissions</span>
                                <h2 class="text-xl font-black text-emerald-700 font-mono mt-0.5">{{ number_format($developerFlatsCount) }}</h2>
                                <p class="text-[9px] text-slate-500 font-medium leading-tight">Builder form entries</p>
                            </div>
                            <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition"><span class="material-symbols-outlined text-xs">apartment</span></span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between text-[9px] text-emerald-700 font-black uppercase tracking-wider mt-1.5">
                            <span>View Submitted Forms</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Developer Activity Logs Card -->
                    <a href="{{ route('ews.department.developer-logs.index') }}" class="premium-card rounded-xl p-3 flex flex-col justify-between min-h-[90px] shadow-sm hover:border-emerald-350 transition cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400 font-extrabold leading-none group-hover:text-emerald-700 transition">Developer Activity Logs</span>
                                <h2 class="text-xl font-black text-emerald-700 font-mono mt-0.5">{{ number_format($developerLogsCount) }}</h2>
                                <p class="text-[9px] text-slate-500 font-medium leading-tight">Action & audit logs</p>
                            </div>
                            <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition"><span class="material-symbols-outlined text-xs">receipt_long</span></span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between text-[9px] text-emerald-700 font-black uppercase tracking-wider mt-1.5">
                            <span>View Audit Trail</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
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
