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
            background-color: #f4f7f6;
        }
        /* Custom premium scrollbar */
        main::-webkit-scrollbar {
            width: 5px;
        }
        main::-webkit-scrollbar-track {
            background: transparent;
        }
        main::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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
<body class="bg-[#f4f7f6] text-slate-800 h-screen flex">

    <!-- 1. Left Sidebar (Breeze Icy Blue Theme) -->
    @include('ews.department.partials.sidebar')

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px] h-screen overflow-hidden">
        

        <!-- Content Body Wrapper -->
        <main class="px-5 py-4 flex-grow flex flex-col gap-4 overflow-y-auto">

            <!-- Banner Header -->
            <div class="relative overflow-hidden rounded-xl py-4 px-5 border border-emerald-400/30 shrink-0 shadow-[0_4px_20px_rgba(16,185,129,0.1),inset_0_0_12px_rgba(16,185,129,0.05)] animate-fade-in-up"
                 style="background-image: linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px), linear-gradient(to right, #059669, #10b981, #0c7a60); background-size: 16px 16px, 16px 16px, 100% 100%;">
                <!-- Glowing Backdrop Mesh Gradients -->
                <div class="absolute -left-16 -top-16 w-36 h-36 bg-white/20 rounded-full blur-2xl pointer-events-none animate-pulse"></div>
                <div class="absolute right-32 -bottom-16 w-32 h-32 bg-emerald-300/10 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative flex items-center justify-between text-white z-10">
                    <div class="flex items-center gap-3.5">
                        <!-- High-Tech Glowing Monitoring Icon -->
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/15 border border-white/20 shadow-[0_2px_8px_rgba(255,255,255,0.1)] flex-shrink-0">
                            <span class="material-symbols-outlined text-white text-lg animate-pulse" style="font-variation-settings: 'FILL' 1;">monitoring</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-black tracking-widest leading-none uppercase" style="text-shadow: 0 1px 8px rgba(6, 95, 70, 0.35);">
                                    <span class="text-white font-black">EWS</span> <span class="text-white font-semibold">Operations Console</span>
                                </h2>
                                <!-- Live Pulsating LED Status Light -->
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                </span>
                            </div>
                            <p class="text-[10px] text-emerald-50 font-bold uppercase tracking-wider leading-none mt-2" style="text-shadow: 0 1px 4px rgba(6, 95, 70, 0.2);">
                                Application flow tracking & developer management system
                            </p>
                        </div>
                    </div>
                    <!-- Premium Cyber Clock/Date Badge -->
                    <div class="flex items-center gap-2 bg-[#044e3a]/45 backdrop-blur-md px-3 py-1.5 rounded-lg border border-emerald-400/35 text-[10px] font-black text-white tracking-widest uppercase shadow-[0_2px_8px_rgba(16,185,129,0.05)]">
                        <span class="material-symbols-outlined text-[11px] text-emerald-300">calendar_today</span>
                        <span class="text-white font-extrabold">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- District & Phase Filter Card -->
            <div class="bg-gradient-to-r from-slate-50 via-blue-50/15 to-slate-50 rounded-xl p-3 border border-slate-200 flex flex-wrap items-center justify-between gap-3 shadow-sm shrink-0 animate-fade-in-up delay-1">
                <div class="flex items-center gap-4 flex-wrap w-full md:w-auto justify-between md:justify-start">
                    <!-- District Filter -->
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-blue-600 text-sm">filter_list</span>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">District:</span>
                        <select id="district-select" onchange="applyFilters()" class="bg-white border border-blue-200 text-blue-900 font-extrabold text-[10px] rounded-lg px-3 py-1.5 focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm cursor-pointer min-w-[160px]">
                            <option value="">ALL DISTRICTS</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ $districtId == $district->id ? 'selected' : '' }}>
                                    {{ strtoupper($district->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Phase Filter -->
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-blue-550 text-sm">filter_alt</span>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Phase:</span>
                        <select id="phase-select" onchange="applyFilters()" class="bg-white border border-blue-200 text-blue-900 font-extrabold text-[10px] rounded-lg px-3 py-1.5 focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm cursor-pointer min-w-[110px]">
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
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 pulse-dot"></span>
                    <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Stage 01 / Pre-Verification Filters</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Total Registration Card -->
                    <div class="bg-gradient-to-br from-[#eef2ff] to-[#e0e7ff] text-slate-800 rounded-xl p-3 flex flex-col justify-between shadow-sm border border-[#c7d2fe] min-h-[115px] hover:-translate-y-0.5 transition-all duration-300">
                        <div>
                            <a href="{{ route('ews.department.list', ['type' => 'ppt_members', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex justify-between items-start group/header">
                                <div>
                                    <span class="text-[8.5px] uppercase tracking-wider text-indigo-650 font-extrabold leading-none">Total Registration</span>
                                    <h2 class="text-xl font-black text-[#3730a3] font-mono mt-0.5">{{ number_format($totalRegistrationCount) }}</h2>
                                </div>
                                <span class="w-7 h-7 rounded-lg bg-[#c7d2fe]/50 text-[#4f46e5] flex items-center justify-center border border-[#a5b4fc] shadow-sm"><span class="material-symbols-outlined text-xs">groups</span></span>
                            </a>
                            
                            <div class="space-y-1 text-[9.5px] font-bold text-slate-700 mt-2 pt-1.5 border-t border-[#c7d2fe]/40">
                                <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-emerald-700">Verified in Survey:</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-emerald-700 font-extrabold font-mono">{{ number_format($registeredCount) }}</span>
                                        <span class="text-slate-350 group-hover:text-emerald-750 transition flex items-center"><span class="material-symbols-outlined text-[10px]">visibility</span></span>
                                    </div>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'not_in_survey', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-rose-700">Rejected in Survey:</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-rose-700 font-extrabold font-mono">{{ number_format($notInSurveyCount) }}</span>
                                        <span class="text-slate-350 group-hover:text-rose-750 transition flex items-center"><span class="material-symbols-outlined text-[10px]">visibility</span></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- PPP Exclusion Card -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId, 'phase' => $phase]) }}" class="bg-gradient-to-br from-[#fff1f2] to-[#ffe4e6] text-slate-800 rounded-xl p-3 flex flex-col justify-between shadow-sm border border-[#fecdd3] min-h-[115px] hover:-translate-y-0.5 transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-rose-650 font-extrabold leading-none">2. PPP Exclusion</span>
                                <h2 class="text-xl font-black text-[#9f1239] font-mono mt-0.5">{{ number_format($rejectedPppCount) }}</h2>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-[#fecdd3]/50 text-[#e11d48] flex items-center justify-center border border-[#fda4af] shadow-sm"><span class="material-symbols-outlined text-xs">cancel</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] text-[#9f1239] font-black uppercase tracking-wider leading-none border-t border-[#fecdd3]/40 pt-2 mt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Property in India Card -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId, 'phase' => $phase]) }}" class="bg-gradient-to-br from-[#fff7ed] to-[#ffedd5] text-slate-800 rounded-xl p-3 flex flex-col justify-between shadow-sm border border-[#fed7aa] min-h-[115px] hover:-translate-y-0.5 transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-orange-655 font-extrabold leading-none">3. Property in India</span>
                                <h2 class="text-xl font-black text-[#9a3412] font-mono mt-0.5">{{ number_format($rejectedPropertyCount) }}</h2>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-[#fed7aa]/50 text-[#ea580c] flex items-center justify-center border border-[#fdbb74] shadow-sm"><span class="material-symbols-outlined text-xs">domain_disabled</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] text-[#9a3412] font-black uppercase tracking-wider leading-none border-t border-[#fed7aa]/40 pt-2 mt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- House Ownership Card -->
                    <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId, 'phase' => $phase]) }}" class="bg-gradient-to-br from-[#fffbeb] to-[#fef3c7] text-slate-800 rounded-xl p-3 flex flex-col justify-between shadow-sm border border-[#fde68a] min-h-[115px] hover:-translate-y-0.5 transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-amber-655 font-extrabold leading-none">4. House Ownership</span>
                                <h2 class="text-xl font-black text-[#92400e] font-mono mt-0.5">{{ number_format($rejectedOwnershipCount) }}</h2>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-[#fde68a]/50 text-[#d97706] flex items-center justify-center border border-[#fcd34d] shadow-sm"><span class="material-symbols-outlined text-xs">home_work</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] text-[#92400e] font-black uppercase tracking-wider leading-none border-t border-[#fde68a]/40 pt-2 mt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>
                </div>

                <!-- Explanation Banner 1 -->
                <div class="bg-white border border-slate-200 rounded-lg py-1.5 px-3 text-[9px] font-bold text-slate-500 flex items-center gap-2 shrink-0 leading-none">
                    <span class="material-symbols-outlined text-slate-400 text-xs">info</span>
                    <span>
                        Filtration: Survey Registry ({{ number_format($eligibleDrawCount + $rejectedPppCount + $rejectedPropertyCount + $rejectedOwnershipCount) }}) - Rejections ({{ number_format($rejectedPppCount) }} PPP + {{ number_format($rejectedPropertyCount) }} Property + {{ number_format($rejectedOwnershipCount) }} House) = Eligible for booking ({{ number_format($eligibleDrawCount) }})
                    </span>
                </div>
            </div>

            <!-- EWS STAGE 2: VERIFICATION PROCESS, ADC STATUS & ALLOTMENT -->
            <div class="space-y-1.5 shrink-0 animate-fade-in-up delay-3">
                <div class="flex items-center gap-2 pb-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 pulse-dot"></span>
                    <h3 class="text-[9px] font-black text-slate-550 uppercase tracking-widest leading-none">Stage 02 / Verification, ADC Status & Allotments</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Eligible for booking -->
                    <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId, 'phase' => $phase]) }}" class="bg-gradient-to-br from-[#eff6ff] to-[#dbeafe] text-slate-800 rounded-xl p-3 flex flex-col justify-between shadow-sm border border-[#bfdbfe] min-h-[115px] hover:-translate-y-0.5 transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-blue-650 font-extrabold leading-none">5. Eligible for booking</span>
                                <h2 class="text-xl font-black text-[#1e40af] font-mono mt-0.5">{{ number_format($eligibleDrawCount) }}</h2>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-[#bfdbfe]/50 text-[#2563eb] flex items-center justify-center border border-[#93c5fd] shadow-sm"><span class="material-symbols-outlined text-xs">how_to_reg</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[9px] text-[#1e40af] font-black uppercase tracking-wider leading-none border-t border-[#bfdbfe]/40 pt-2">
                            <span>View List</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Booking amount received -->
                    <div class="bg-gradient-to-br from-[#ecfdf5] to-[#d1fae5] text-slate-800 rounded-xl p-3 flex flex-col justify-between shadow-sm border border-[#a7f3d0] min-h-[115px] hover:-translate-y-0.5 transition-all duration-300">
                        <div>
                            <span class="text-[8.5px] uppercase text-emerald-650 font-extrabold tracking-wider leading-none">6. Booking Amount</span>
                            <div class="space-y-1.5 text-[9.5px] font-bold text-slate-700 mt-2 pt-1.5 border-t border-[#a7f3d0]/40">
                                <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-emerald-700">Received:</span>
                                        <span class="text-[#065f46] font-extrabold font-mono">{{ number_format($bookingCount) }}</span>
                                    </div>
                                    <span class="text-slate-350 group-hover:text-emerald-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group border-t border-[#a7f3d0]/10 pt-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-rose-700">Not Received:</span>
                                        <span class="text-rose-700 font-extrabold font-mono">{{ number_format($notVisitedCount) }}</span>
                                    </div>
                                    <span class="text-slate-350 group-hover:text-rose-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ADC Verification -->
                    <div class="bg-gradient-to-br from-[#f0fdf4] to-[#dcfce7] text-slate-800 rounded-xl p-3 flex flex-col justify-between shadow-sm border border-[#bbf7d0] min-h-[115px] hover:-translate-y-0.5 transition-all duration-300">
                        <div>
                            <span class="text-[8.5px] uppercase text-emerald-650 font-extrabold tracking-wider leading-none">7. ADC Verification</span>
                            <div class="space-y-1.5 text-[9.5px] font-bold text-slate-700 mt-2 pt-1.5 border-t border-[#bbf7d0]/40">
                                <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-emerald-700">Eligible:</span>
                                        <span class="text-emerald-805 font-extrabold font-mono">{{ number_format($adcPassedCount) }}</span>
                                    </div>
                                    <span class="text-slate-350 group-hover:text-emerald-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group border-t border-[#bbf7d0]/10 pt-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-rose-700">Not Eligible:</span>
                                        <span class="text-rose-700 font-extrabold font-mono">{{ number_format($adcFailedCount) }}</span>
                                    </div>
                                    <span class="text-slate-355 group-hover:text-rose-750 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Allocation Status -->
                    <div class="bg-gradient-to-br from-[#faf5ff] to-[#f3e8ff] text-slate-800 rounded-xl p-3 flex flex-col justify-between shadow-sm border border-[#e9d5ff] min-h-[115px] hover:-translate-y-0.5 transition-all duration-300">
                        <div>
                            <span class="text-[8.5px] uppercase text-purple-650 font-extrabold tracking-wider leading-none">8. Allocation Status</span>
                            <div class="space-y-1 text-[9.5px] font-bold text-slate-700 mt-1.5 pt-1 border-t border-[#e9d5ff]/40">
                                <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-emerald-700">Allotted:</span>
                                        <span class="text-[#6b21a8] font-extrabold font-mono text-[10px]">{{ number_format($allottedCount) }}</span>
                                    </div>
                                    <span class="text-slate-300 group-hover:text-emerald-750 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group border-t border-purple-100/10 pt-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-amber-700">Waiting:</span>
                                        <span class="text-amber-700 font-extrabold font-mono text-[10px]">{{ number_format($pendingCount) }}</span>
                                    </div>
                                    <span class="text-slate-300 group-hover:text-amber-750 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                                <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId, 'phase' => $phase]) }}" class="flex items-center justify-between p-0.5 rounded hover:bg-white/60 transition group border-t border-purple-100/10 pt-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        <span class="text-slate-600 group-hover:text-slate-900">Unallotted:</span>
                                        <span class="text-slate-655 font-extrabold font-mono text-[10px]">{{ number_format($drawRemainingCount) }}</span>
                                    </div>
                                    <span class="text-slate-300 group-hover:text-slate-700 transition flex items-center p-0.5" title="View List"><span class="material-symbols-outlined text-xs">visibility</span></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Explanation Banner 2 -->
                <div class="bg-white border border-slate-200 rounded-lg py-1.5 px-3 text-[9px] font-bold text-slate-500 flex items-center gap-2 shrink-0 leading-none">
                    <span class="material-symbols-outlined text-slate-400 text-xs">info</span>
                    <span>
                        Outcomes: Booking Amount Received ({{ number_format($bookingCount) }}) -> Eligible ({{ number_format($adcPassedCount) }}) [Allotted: {{ $allottedCount }} + Waiting: {{ $pendingCount }} + Unallotted Draw: {{ $drawRemainingCount }}] & Not Eligible ({{ number_format($adcFailedCount) }})
                    </span>
                </div>
            </div>

            <!-- EWS STAGE 3: DEVELOPERS MANAGEMENT & FORM SUBMISSIONS -->
            <div class="space-y-1.5 shrink-0 pt-1.5 border-t border-slate-200 animate-fade-in-up delay-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 pulse-dot"></span>
                        <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Stage 03 / Developers & Audits</h3>
                    </div>
                    <a href="{{ route('ews.department.developers.index') }}" class="text-[9px] font-black text-blue-600 hover:text-blue-700 uppercase flex items-center gap-0.5 transition leading-none">
                        <span>Manage Developers</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Developer Accounts Card -->
                    <a href="{{ route('ews.department.developers.index') }}" class="bg-gradient-to-br from-[#f8fafc] to-[#f1f5f9] text-slate-800 rounded-xl p-3 flex flex-col justify-between min-h-[90px] shadow-sm border border-slate-200 hover:-translate-y-0.5 transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-slate-500 font-extrabold leading-none">Developer Accounts</span>
                                <h2 class="text-xl font-black text-slate-800 font-mono mt-0.5">{{ number_format($developerCount) }}</h2>
                                <p class="text-[9px] text-slate-550 font-medium leading-tight">Registered credentials</p>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-150 group-hover:bg-emerald-600 group-hover:text-white transition">
                                <span class="material-symbols-outlined text-xs">engineering</span>
                            </span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-200 flex items-center justify-between text-[9px] text-emerald-700 font-black uppercase tracking-wider mt-1">
                            <span>Manage Credentials</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Developer Flat Submissions Card -->
                    <a href="{{ route('ews.department.developer-flats.index') }}" class="bg-gradient-to-br from-[#f0fdfa] to-[#ccfbf1] text-slate-800 rounded-xl p-3 flex flex-col justify-between min-h-[90px] shadow-sm border border-[#99f6e4] hover:-translate-y-0.5 transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-[#115e59] font-extrabold leading-none">Builder Flat Submissions</span>
                                <h2 class="text-xl font-black text-[#115e59] font-mono mt-0.5">{{ number_format($developerFlatsCount) }}</h2>
                                <p class="text-[9px] text-slate-600 font-medium leading-tight">Builder form entries</p>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-[#99f6e4]/60 text-[#0d9488] flex items-center justify-center border border-[#5eead4] group-hover:bg-[#0d9488] group-hover:text-white transition">
                                <span class="material-symbols-outlined text-xs">apartment</span>
                            </span>
                        </div>
                        <div class="pt-1.5 border-t border-[#99f6e4]/40 flex items-center justify-between text-[9px] text-[#0d9488] font-black uppercase tracking-wider mt-1">
                            <span>View Submitted Forms</span>
                            <span class="material-symbols-outlined text-xs group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                        </div>
                    </a>

                    <!-- Developer Activity Logs Card -->
                    <a href="{{ route('ews.department.developer-logs.index') }}" class="bg-gradient-to-br from-[#f0f9ff] to-[#e0f2fe] text-slate-800 rounded-xl p-3 flex flex-col justify-between min-h-[90px] shadow-sm border border-[#bae6fd] hover:-translate-y-0.5 transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[8.5px] uppercase tracking-wider text-[#075985] font-extrabold leading-none">Developer Activity Logs</span>
                                <h2 class="text-xl font-black text-[#075985] font-mono mt-0.5">{{ number_format($developerLogsCount) }}</h2>
                                <p class="text-[9px] text-slate-600 font-medium leading-tight">Action & audit logs</p>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-[#bae6fd]/60 text-[#0284c7] flex items-center justify-center border border-[#7dd3fc] group-hover:bg-[#0284c7] group-hover:text-white transition">
                                <span class="material-symbols-outlined text-xs">receipt_long</span>
                            </span>
                        </div>
                        <div class="pt-1.5 border-t border-[#bae6fd]/40 flex items-center justify-between text-[9px] text-[#0284c7] font-black uppercase tracking-wider mt-1">
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
