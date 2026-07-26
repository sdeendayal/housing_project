<!DOCTYPE html>
<html class="light h-full" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>MMGAV Villager Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(at 0% 0%, rgba(244, 245, 247, 1) 0, transparent 50%), 
                        radial-gradient(at 50% 0%, rgba(238, 242, 255, 0.7) 0, transparent 50%), 
                        radial-gradient(at 100% 0%, rgba(244, 245, 247, 1) 0, transparent 50%), 
                        #f8fafc;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #090b11 0%, #111424 50%, #1b1737 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 40;
                width: 16rem;
                transform: translateX(-100%);
            }
            .sidebar-open .sidebar {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; right: 0; bottom: 0; left: 0;
                background-color: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
                z-index: 30;
                transition: all 0.3s ease;
            }
            .sidebar-open .sidebar-overlay {
                display: block;
            }
        }
        @media (min-width: 769px) {
            .sidebar {
                width: 16rem;
                transform: translateX(0);
            }
            .collapsed .sidebar {
                width: 0;
                opacity: 0;
                pointer-events: none;
                transform: translateX(-100%);
            }
            .sidebar-overlay {
                display: none;
            }
        }
        .tab-btn {
            position: relative;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tab-btn::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            background: linear-gradient(to bottom, #818cf8, #c084fc);
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transition: all 0.25s ease-in-out;
        }
        .tab-btn-active::before {
            opacity: 1;
        }
        .tab-btn-active {
            background: linear-gradient(90deg, rgba(129, 140, 248, 0.15) 0%, rgba(192, 132, 252, 0.03) 100%) !important;
            color: #818cf8 !important;
            box-shadow: 0 4px 15px rgba(129, 140, 248, 0.05);
        }
        .tab-btn:hover span {
            transform: scale(1.1);
        }
        .tab-btn span {
            transition: transform 0.2s ease;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.015);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.05);
            border-color: rgba(203, 213, 225, 1);
        }
        .glow-icon {
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        @keyframes pulse-subtle {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2), 0 0 0 0 rgba(79, 70, 229, 0.25);
            }
            50% {
                transform: scale(1.04);
                box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35), 0 0 0 6px rgba(79, 70, 229, 0);
            }
        }
        .animate-pulse-subtle {
            animation: pulse-subtle 2s infinite ease-in-out;
        }
    </style>
</head>
<body class="h-full flex flex-col overflow-hidden select-none">

    <!-- Outer Wrapper -->
    <div id="dashboard-wrapper" class="flex flex-1 h-full overflow-hidden min-h-0 relative">

        @if($ownerInfo)
            <!-- Mobile Overlay Background -->
            <div onclick="toggleSidebar()" class="sidebar-overlay"></div>

            <!-- Stylish & Collapsible Sidebar -->
            <aside id="sidebar" class="sidebar text-slate-400 flex-shrink-0 flex flex-col z-20 overflow-hidden">
                <!-- Branding Header -->
                <div class="p-5 border-b border-slate-900/60 flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/25 flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px] font-bold">real_estate_agent</span>
                    </div>
                    <div>
                        <h1 class="text-xs font-extrabold tracking-wide uppercase text-slate-100">Housing For All</h1>
                        <p class="text-[8px] text-slate-500 font-bold uppercase tracking-wider leading-none mt-0.5">MMGAY Scheme</p>
                    </div>
                </div>

                <!-- Navigation links -->
                <nav class="flex-grow p-4 space-y-6 overflow-y-auto">
                    <!-- Section 1 -->
                    <div>
                        <span class="text-[9px] text-slate-500 font-extrabold tracking-[0.15em] uppercase px-3 block mb-3 leading-none">Applicant Portal</span>
                        <div class="space-y-1">
                            <button onclick="switchTab('overview')" id="tab-btn-overview" class="tab-btn tab-btn-active w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all hover:bg-slate-800/30 hover:text-white text-left">
                                <span class="material-symbols-outlined text-[18px]">dashboard</span>
                                <span class="tab-label">Dashboard Overview</span>
                            </button>
                            <button onclick="switchTab('profile')" id="tab-btn-profile" class="tab-btn w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all hover:bg-slate-800/30 hover:text-white text-left">
                                <span class="material-symbols-outlined text-[18px]">person</span>
                                <span class="tab-label">Applicant Identity</span>
                            </button>
                            <button onclick="switchTab('location')" id="tab-btn-location" class="tab-btn w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all hover:bg-slate-800/30 hover:text-white text-left">
                                <span class="material-symbols-outlined text-[18px]">location_on</span>
                                <span class="tab-label">Location Mapping</span>
                            </button>
                            <button onclick="switchTab('property')" id="tab-btn-property" class="tab-btn w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all hover:bg-slate-800/30 hover:text-white text-left">
                                <span class="material-symbols-outlined text-[18px]">real_estate_agent</span>
                                <span class="tab-label">Property Allotted Details</span>
                            </button>
                            @if($possessionApplication)
                            <button onclick="switchTab('possession')" id="tab-btn-possession" class="tab-btn w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all hover:bg-slate-800/30 hover:text-white text-left">
                                <span class="material-symbols-outlined text-[18px]">event_available</span>
                                <span class="tab-label">Physical Possession</span>
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Section 2 -->
                    <div>
                        <span class="text-[9px] text-slate-500 font-extrabold tracking-[0.15em] uppercase px-3 block mb-3 leading-none">Audit & Helpdesk</span>
                        <div class="space-y-1">
                            <button onclick="switchTab('audit')" id="tab-btn-audit" class="tab-btn w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all hover:bg-slate-800/30 hover:text-white text-left">
                                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                                <span class="tab-label">Audit & Remarks</span>
                            </button>
                            <button onclick="switchTab('support')" id="tab-btn-support" class="tab-btn w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all hover:bg-slate-800/30 hover:text-white text-left">
                                <span class="material-symbols-outlined text-[18px]">support_agent</span>
                                <span class="tab-label">Help & Support</span>
                            </button>
                        </div>
                    </div>
                </nav>

                <!-- Allotted Property Details (Sidebar Capsule) -->
                @if(isset($ownerInfo->FlatNo))
                <div class="mx-4 mb-2 p-3 bg-white/5 border border-white/5 rounded-2xl flex flex-col gap-1.5 backdrop-blur-md shadow-sm">
                    <div class="flex items-center gap-2 text-indigo-400">
                        <span class="material-symbols-outlined text-[15px] font-bold">real_estate_agent</span>
                        <span class="text-[9px] font-extrabold uppercase tracking-wider">Allotted Property</span>
                    </div>
                    <div class="text-[10px] text-slate-300 font-semibold pl-6">
                        <div class="flex justify-between border-b border-white/5 pb-1">
                            <span class="text-slate-500 text-[8px] uppercase tracking-wider">Flat ID:</span>
                            <span class="font-mono text-slate-300">#{{ $ownerInfo->FlatId }}</span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pt-1 pb-1">
                            <span class="text-slate-500 text-[8px] uppercase tracking-wider">Flat No:</span>
                            <span class="truncate max-w-[100px] text-slate-300" title="{{ $ownerInfo->FlatNo }}">{{ $ownerInfo->FlatNo }}</span>
                        </div>
                        <div class="flex justify-between pt-1">
                            <span class="text-slate-500 text-[8px] uppercase tracking-wider">Village:</span>
                            <span class="truncate max-w-[100px] text-slate-300" title="{{ $ownerInfo->VillageName }}">{{ $ownerInfo->VillageName }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Sidebar Footer Profile Card (Floating Glass Capsule) -->
                <div class="m-4 p-3.5 bg-white/5 border border-white/10 rounded-2xl flex items-center gap-3 backdrop-blur-md shadow-lg shadow-black/20 flex-shrink-0">
                    <div class="w-8 h-8 bg-gradient-to-tr from-blue-500 to-indigo-500 text-white rounded-full flex items-center justify-center font-extrabold text-xs shadow-md shadow-blue-500/25 flex-shrink-0">
                        {{ substr($ownerInfo->OwnerName, 0, 1) }}
                    </div>
                    <div class="overflow-hidden min-w-0">
                        <h4 class="text-xs font-bold text-slate-200 truncate leading-snug">{{ $ownerInfo->OwnerName }}</h4>
                        <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider font-mono leading-none mt-0.5">Owner ID: #{{ $ownerInfo->OwnerId }}</p>
                    </div>
                </div>
            </aside>

            <!-- Right Workspace -->
            <div class="flex-grow flex flex-col overflow-hidden min-h-0">
                
                <!-- Floating Header Bar -->
                <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-6 py-4 flex justify-between items-center z-10 flex-shrink-0">
                    <div class="flex items-center">
                        <!-- Toggle Button -->
                        <button onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-800 focus:outline-none flex items-center justify-center w-8 h-8 rounded-lg hover:bg-slate-100 transition-colors mr-3 active:scale-95">
                            <span class="material-symbols-outlined text-[20px] font-bold" id="menu-icon">menu_open</span>
                        </button>
                        <div>
                            <h2 id="header-title" class="text-sm font-extrabold text-slate-800 tracking-wide leading-none">Dashboard Overview</h2>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider leading-none mt-1">Mukhyamantri Grameen Awas Yojana</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex items-center gap-2 pr-4 border-r border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] text-slate-500 font-bold font-mono">Verified Villager session</span>
                        </div>
                        <form action="{{ route('mmgav.villager.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-all shadow-sm active:scale-95">
                                <span class="material-symbols-outlined text-[13px] font-bold">logout</span>
                                Logout
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Main Scrollable Body -->
                <main class="flex-grow p-6 overflow-y-auto space-y-6">

                    <!-- Payment Status Banner -->
                    @if($ownerInfo)
                        @if($ownerInfo->IsPaid == 1)
                            <div class="glass-card p-4 rounded-2xl flex items-center justify-between border-l-[4px] border-l-emerald-500 bg-emerald-50/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/15">
                                        <span class="material-symbols-outlined text-[18px] font-bold">payments</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-emerald-800 leading-snug">Payment Status: Received</h4>
                                        <p class="text-[10px] text-emerald-600 mt-0.5 font-medium">Your flat allotment payment has been successfully received and verified.</p>
                                    </div>
                                </div>
                                <span class="bg-emerald-100/80 text-emerald-800 text-[9px] font-extrabold uppercase px-2.5 py-1 rounded-lg">Paid</span>
                            </div>
                        @else
                            <div class="glass-card p-4 rounded-2xl flex items-center justify-between border-l-[4px] border-l-amber-500 bg-amber-50/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-md shadow-amber-500/15">
                                        <span class="material-symbols-outlined text-[18px] font-bold">hourglass_empty</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-amber-800 leading-snug">Payment Status: Pending</h4>
                                        <p class="text-[10px] text-amber-600 mt-0.5 font-medium">Your flat allotment payment is currently pending. Please proceed with payment.</p>
                                    </div>
                                </div>
                                <span class="bg-amber-100/80 text-amber-800 text-[9px] font-extrabold uppercase px-2.5 py-1 rounded-lg">Pending</span>
                            </div>
                        @endif
                    @endif

                    <!-- Physical Possession Status Banner -->
                    @if($possessionApplication)
                        @if($possessionApplication->physical_possession_status === 'Eligible for Physical Possession')
                            <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-l-[4px] border-l-blue-500 bg-blue-50/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-blue-500/15">
                                        <span class="material-symbols-outlined text-[18px] font-bold">hourglass_empty</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-blue-800 leading-snug">Physical Possession: Eligible</h4>
                                        <p class="text-[10px] text-blue-600 mt-0.5 font-medium">You are eligible for physical possession. Awaiting the Block Development Officer (BDPO) to schedule visit slots.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-800 text-[9px] font-extrabold uppercase px-2.5 py-1 rounded-lg">Awaiting Schedule</span>
                                </div>
                            </div>
                        @elseif($possessionApplication->physical_possession_status === 'Rejected')
                            <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-l-[4px] border-l-rose-500 bg-rose-50/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-500 to-red-500 text-white flex items-center justify-center shadow-md shadow-rose-500/15">
                                        <span class="material-symbols-outlined text-[18px] font-bold">error</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-rose-800 leading-snug">Physical Possession: Reschedule Required</h4>
                                        <p class="text-[10px] text-rose-600 mt-0.5 font-medium">Your physical possession verification was rejected. Remarks: <strong class="text-rose-850">{{ $possessionApplication->remarks ?? 'N/A' }}</strong>. Please select a new slot for rescheduling.</p>
                                    </div>
                                </div>
                                <button type="button" onclick="openSlotSelectionModal()" class="bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-extrabold uppercase px-3.5 py-2 rounded-xl transition shadow shadow-rose-500/10 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">touch_app</span> Reschedule Slot
                                </button>
                            </div>
                        @elseif($possessionApplication->physical_possession_status === 'Visit Scheduled')
                            <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-l-[4px] border-l-amber-500 bg-amber-50/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-md shadow-amber-500/15">
                                        <span class="material-symbols-outlined text-[18px] font-bold">calendar_month</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-amber-800 leading-snug">Physical Possession: Visit Scheduled</h4>
                                        <p class="text-[10px] text-amber-600 mt-0.5 font-medium">BDPO has scheduled your visit. Please select your preferred time slot.</p>
                                    </div>
                                </div>
                                <button type="button" onclick="openSlotSelectionModal()" class="bg-amber-600 hover:bg-amber-700 text-white text-[10px] font-extrabold uppercase px-3.5 py-2 rounded-xl transition shadow shadow-amber-500/10 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">touch_app</span> Select Visit Slot
                                </button>
                            </div>
                        @elseif($possessionApplication->physical_possession_status === 'Slot Selected')
                            <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-l-[4px] border-l-indigo-500 bg-indigo-50/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 text-white flex items-center justify-center shadow-md shadow-indigo-500/15">
                                        <span class="material-symbols-outlined text-[18px] font-bold">schedule</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-indigo-800 leading-snug">Physical Possession: Slot Selected</h4>
                                        <p class="text-[10px] text-indigo-600 mt-0.5 font-medium">Visit date: <strong class="text-indigo-800">{{ date('d M Y, h:i A', strtotime($possessionApplication->citizen_visit_date)) }}</strong>. Awaiting BDPO site verification.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('mmgay.villager.download-slip') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-extrabold uppercase px-3 py-2 rounded-xl transition shadow flex items-center gap-1 active:scale-95">
                                        <span class="material-symbols-outlined text-[14px]">picture_as_pdf</span> Download Slip
                                    </a>
                                    <span class="bg-indigo-100 text-indigo-800 text-[9px] font-extrabold uppercase px-2.5 py-1 rounded-lg">Awaiting Visit</span>
                                </div>
                            </div>
                        @elseif($possessionApplication->physical_possession_status === 'Site Verified')
                            <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-l-[4px] border-l-blue-500 bg-blue-50/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-blue-500/15">
                                        <span class="material-symbols-outlined text-[18px] font-bold">verified</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-blue-800 leading-snug">Physical Possession: Site Verified</h4>
                                        <p class="text-[10px] text-blue-600 mt-0.5 font-medium">BDPO has completed site verification. E-Possession Report generation is in progress.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('mmgay.villager.download-slip') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-extrabold uppercase px-3 py-2 rounded-xl transition shadow flex items-center gap-1 active:scale-95">
                                        <span class="material-symbols-outlined text-[14px]">picture_as_pdf</span> Download Slip
                                    </a>
                                    <span class="bg-blue-100 text-blue-800 text-[9px] font-extrabold uppercase px-2.5 py-1 rounded-lg">Site Verified</span>
                                </div>
                            </div>
                        @elseif($possessionApplication->physical_possession_status === 'Verified')
                            <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-l-[4px] border-l-emerald-500 bg-emerald-50/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/15">
                                        <span class="material-symbols-outlined text-[18px] font-bold">check_circle</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-emerald-800 leading-snug">Physical Possession: Verified & Completed</h4>
                                        <p class="text-[10px] text-emerald-600 mt-0.5 font-medium">Your physical possession was successfully verified on {{ date('d M Y', strtotime($possessionApplication->verified_at)) }}.</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 mt-2 md:mt-0">
                                    <a href="{{ route('mmgay.villager.download-certificate', $possessionApplication->secure_id) }}?inline=1" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-extrabold uppercase px-3.5 py-2 rounded-xl transition shadow shadow-emerald-500/10 flex items-center gap-1 active:scale-95">
                                        <span class="material-symbols-outlined text-[14px]">picture_as_pdf</span> Download Report
                                    </a>
                                    @if($possessionApplication->possession_certificate)
                                        <a href="{{ asset('storage/' . $possessionApplication->possession_certificate) }}" target="_blank" rel="noopener" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-extrabold uppercase px-3.5 py-2 rounded-xl transition shadow shadow-blue-500/10 flex items-center gap-1 active:scale-95">
                                            <span class="material-symbols-outlined text-[14px]">description</span> Final Possession Letter
                                        </a>
                                    @endif
                                    @if($possessionApplication->site_engineer_file)
                                        <a href="{{ asset('storage/' . $possessionApplication->site_engineer_file) }}" target="_blank" rel="noopener" class="bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-extrabold uppercase px-3.5 py-2 rounded-xl transition shadow shadow-teal-500/10 flex items-center gap-1 active:scale-95">
                                            <span class="material-symbols-outlined text-[14px]">task</span> BDO Signed Report
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- 1. OVERVIEW TAB -->
                    <div id="tab-overview" class="tab-content space-y-6">
                        
                        <!-- Top KPI Summary Row -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Registration No -->
                            <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                                <div class="w-8 h-8 bg-gradient-to-tr from-blue-500 to-cyan-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-500/10 glow-icon">
                                    <span class="material-symbols-outlined text-[18px] font-bold">badge</span>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[9px] text-slate-400 font-extrabold uppercase block tracking-wider leading-none">Reg No.</span>
                                    <span class="text-xs font-bold text-slate-700 block truncate mt-1" title="{{ $ownerInfo->RegistrationNo }}">{{ $ownerInfo->RegistrationNo ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Scheme & Phase -->
                            <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                                <div class="w-8 h-8 bg-gradient-to-tr from-indigo-500 to-purple-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-500/10 glow-icon">
                                    <span class="material-symbols-outlined text-[18px] font-bold">grid_view</span>
                                </div>
                                <div>
                                    <span class="text-[9px] text-slate-400 font-extrabold uppercase block tracking-wider leading-none">Scheme / Phase</span>
                                    <span class="text-xs font-bold text-slate-700 block mt-1">MMGAY - Phase {{ $ownerInfo->Phase ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                                <div class="w-8 h-8 bg-gradient-to-tr from-amber-500 to-orange-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-amber-500/10 glow-icon">
                                    <span class="material-symbols-outlined text-[18px] font-bold">groups</span>
                                </div>
                                <div>
                                    <span class="text-[9px] text-slate-400 font-extrabold uppercase block tracking-wider leading-none">Caste Category</span>
                                    <span class="text-xs font-bold text-slate-700 block mt-1">{{ $ownerInfo->Caste ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Status Card -->
                            <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                                @if($ownerInfo->IsAllotmentCancelled == 1)
                                    <div class="w-8 h-8 bg-gradient-to-tr from-rose-500 to-red-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-red-500/10 glow-icon">
                                        <span class="material-symbols-outlined text-[18px] font-bold">cancel</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-extrabold uppercase block tracking-wider leading-none">Status</span>
                                        <span class="text-xs font-bold text-rose-600 block mt-1">Cancelled</span>
                                    </div>
                                @elseif($ownerInfo->IsRejected == 1)
                                    <div class="w-8 h-8 bg-gradient-to-tr from-rose-500 to-red-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-red-500/10 glow-icon">
                                        <span class="material-symbols-outlined text-[18px] font-bold">warning</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-extrabold uppercase block tracking-wider leading-none">Status</span>
                                        <span class="text-xs font-bold text-red-600 block mt-1">Rejected</span>
                                    </div>
                                @elseif($ownerInfo->IsPaid == 1 || $ownerInfo->IsPaymentApproved == 1)
                                    <div class="w-8 h-8 bg-gradient-to-tr from-emerald-500 to-teal-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/10 glow-icon">
                                        <span class="material-symbols-outlined text-[18px] font-bold">payments</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-extrabold uppercase block tracking-wider leading-none">Status</span>
                                        <span class="text-xs font-bold text-emerald-600 block mt-1">Approved & Paid</span>
                                    </div>
                                @elseif($ownerInfo->IsApproved == 1)
                                    <div class="w-8 h-8 bg-gradient-to-tr from-emerald-500 to-teal-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/10 glow-icon">
                                        <span class="material-symbols-outlined text-[18px] font-bold">verified</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-extrabold uppercase block tracking-wider leading-none">Status</span>
                                        <span class="text-xs font-bold text-emerald-600 block mt-1">Approved</span>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gradient-to-tr from-blue-500 to-indigo-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-500/10 glow-icon">
                                        <span class="material-symbols-outlined text-[18px] font-bold">hourglass_empty</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-extrabold uppercase block tracking-wider leading-none">Status</span>
                                        <span class="text-xs font-bold text-blue-600 block mt-1">Registered</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Stepper Tracker -->
                        <div class="glass-card p-6 rounded-2xl">
                            <h3 class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-4 leading-none">Application Lifecycle Tracker</h3>
                            
                            @php
                                $isCancelled = ($ownerInfo->IsAllotmentCancelled == 1);
                                $isRejected = ($ownerInfo->IsRejected == 1);
                                $isApproved = ($ownerInfo->IsApproved == 1);
                                
                                // Determine active lifecycle step index (1-4)
                                if ($isApproved || $isRejected || $isCancelled) {
                                    $lifecycleStep = 4;
                                    $progressPercent = 100;
                                } else {
                                    $lifecycleStep = 2; // Pending verification
                                    $progressPercent = 33.33; // 4 steps = 3 segments. 1st segment completed (Step 1 to 2)
                                }
                                
                                $lifecycleSteps = [
                                    [
                                        'index' => 1,
                                        'title' => 'Registered',
                                        'subtitle' => 'Profile matches master records',
                                        'default_icon' => 'how_to_reg',
                                    ],
                                    [
                                        'index' => 2,
                                        'title' => 'Under Review',
                                        'subtitle' => 'Documents verified by cell',
                                        'default_icon' => 'rate_review',
                                    ],
                                    [
                                        'index' => 3,
                                        'title' => 'District Audit',
                                        'subtitle' => 'Block & village verified',
                                        'default_icon' => 'fact_check',
                                    ],
                                    [
                                        'index' => 4,
                                        'title' => $isCancelled ? 'Allotment Cancelled' : ($isRejected ? 'Rejected / Ineligible' : ($isApproved ? 'Approved' : 'Pending Decision')),
                                        'subtitle' => $isCancelled ? 'Cancelled by DC Office' : ($isRejected ? ($ownerInfo->Remarks ?? 'Not eligible') : ($isApproved ? 'Profile verification success' : 'Awaiting board approval')),
                                        'default_icon' => $isCancelled || $isRejected ? 'close' : ($isApproved ? 'done_all' : 'hourglass_top'),
                                    ]
                                ];
                            @endphp

                            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0 mt-4 pl-4 md:pl-0">
                                <!-- Connecting Line Background -->
                                <div class="absolute left-[20px] top-4 md:left-[12.5%] md:right-[12.5%] md:top-5 h-[90%] md:h-[2px] w-[2px] md:w-auto bg-slate-200/60 z-0 rounded-full">
                                    <!-- Active Line Fill for Mobile -->
                                    <div class="md:hidden bg-gradient-to-b from-emerald-500 to-indigo-600 w-full transition-all duration-500 rounded-full" style="height: {{ $progressPercent }}%"></div>
                                    <!-- Active Line Fill for Desktop -->
                                    <div class="hidden md:block bg-gradient-to-r from-emerald-500 via-blue-500 to-indigo-600 h-full transition-all duration-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
                                </div>
                                
                                @foreach($lifecycleSteps as $s)
                                    @php
                                        $idx = $s['index'];
                                        $isCompletedStep = ($idx < $lifecycleStep) || ($isApproved && $idx === 4);
                                        $isCurrentStep = ($idx === $lifecycleStep) && !$isApproved && !$isRejected && !$isCancelled;
                                        $isPendingStep = ($idx > $lifecycleStep);
                                        
                                        $circleClass = '';
                                        $iconName = $s['default_icon'];
                                        $titleClass = '';
                                        $subtitleClass = '';
                                        $badgeText = '';
                                        
                                        if ($idx === 4 && ($isCancelled || $isRejected)) {
                                            // Special style for Step 4 rejection
                                            $circleClass = 'bg-rose-500 text-white shadow-lg shadow-rose-500/25 border-2 border-rose-500 ring-4 ring-rose-50';
                                            $iconName = 'close';
                                            $titleClass = 'text-rose-600 font-bold';
                                            $subtitleClass = 'text-rose-400 font-semibold';
                                            $badgeText = $isCancelled ? 'Cancelled' : 'Rejected';
                                        } elseif ($isCompletedStep) {
                                            $circleClass = 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 border-2 border-emerald-500';
                                            $iconName = ($idx === 4) ? 'done_all' : 'check';
                                            $titleClass = 'text-slate-800 font-semibold';
                                            $subtitleClass = 'text-slate-400';
                                        } elseif ($isCurrentStep) {
                                            $circleClass = 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 border-2 border-indigo-600 ring-4 ring-indigo-50 animate-pulse-subtle';
                                            $titleClass = 'text-indigo-600 font-extrabold';
                                            $subtitleClass = 'text-indigo-400 font-semibold';
                                            $badgeText = 'Active';
                                        } else {
                                            $circleClass = 'bg-slate-50 text-slate-400 border border-slate-200';
                                            $titleClass = 'text-slate-400 font-medium';
                                            $subtitleClass = 'text-slate-300';
                                        }
                                    @endphp
                                    
                                    <div class="relative flex md:flex-col items-center md:text-center gap-3.5 md:gap-2.5 z-10 md:w-1/4 group transition duration-300">
                                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center transition duration-300 {{ $circleClass }}">
                                            <span class="material-symbols-outlined text-[15px] md:text-[18px] font-bold">{{ $iconName }}</span>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5 justify-center">
                                                <h4 class="text-xs {{ $titleClass }}">{{ $s['title'] }}</h4>
                                                @if($badgeText)
                                                    <span class="text-[7px] font-extrabold uppercase px-1 py-0.5 rounded leading-none {{ ($badgeText === 'Rejected' || $badgeText === 'Cancelled') ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700' }}">
                                                        {{ $badgeText }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-[9px] {{ $subtitleClass }} mt-0.5">{{ $s['subtitle'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Audit Verdict Panel (Glassmorphic Red Block) -->
                        @if($ownerInfo->IsRejected == 1)
                            <div class="glass-card p-5 rounded-2xl border-l-[4px] border-l-rose-500">
                                <h3 class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-3 leading-none">Official Audit Verdict</h3>
                                <div class="bg-rose-50/40 border border-rose-100 p-4 rounded-xl flex gap-3.5 items-start">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-500 to-red-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-red-500/10">
                                        <span class="material-symbols-outlined text-[16px] font-bold">report</span>
                                    </div>
                                    <div class="text-xs">
                                        <h4 class="font-extrabold text-rose-800">Application Deemed Ineligible</h4>
                                        <p class="text-rose-700 mt-1 leading-relaxed">
                                            The verification desk has updated your profile status to <strong>Rejected</strong>. 
                                            Official Reason: <span class="underline underline-offset-2 font-bold">{{ $ownerInfo->Remarks ?? 'Not eligible for the scheme.' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Summary Data Grid -->
                        <div class="glass-card p-5 rounded-2xl">
                            <h3 class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-4 leading-none">Primary Registration Records</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-xs">
                                <div class="border-l-2 border-blue-500 pl-4 py-1">
                                    <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider leading-none">Applicant Name</span>
                                    <span class="font-bold text-slate-800 block mt-1.5">{{ $ownerInfo->OwnerName }}</span>
                                </div>
                                <div class="border-l-2 border-indigo-500 pl-4 py-1">
                                    <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider leading-none">Mobile Number</span>
                                    <span class="font-bold text-slate-800 block mt-1.5 font-mono">{{ $ownerInfo->MobileNo }}</span>
                                </div>
                                <div class="border-l-2 border-amber-500 pl-4 py-1">
                                    <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider leading-none">Family PPP ID</span>
                                    <span class="font-bold text-slate-800 block mt-1.5 font-mono">{{ $ownerInfo->PPPId ?? '—' }}</span>
                                </div>
                                <div class="border-l-2 border-emerald-500 pl-4 py-1">
                                    <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider leading-none">District Name</span>
                                    <span class="font-bold text-slate-800 block mt-1.5">{{ $ownerInfo->DistrictName ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Physical Possession Timeline -->
                        @if($possessionApplication)
                            <div class="glass-card p-5 rounded-2xl">
                                <h3 class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-4 leading-none">Physical Possession Progress Timeline</h3>
                                <div class="space-y-4 pl-1.5 mt-2">
                                    @forelse($logs as $log)
                                        <div class="relative pl-5 border-l-2 border-slate-200 last:border-l-0 pb-1 text-xs">
                                            <span class="absolute -left-[5.5px] top-1.5 w-2.5 h-2.5 rounded-full bg-indigo-500 border border-white"></span>
                                            <div class="flex items-center justify-between font-bold text-slate-700 text-[10px]">
                                                <span class="uppercase tracking-wider text-indigo-600">
                                                    {{ $log->new_status }}
                                                </span>
                                                <span class="text-slate-400 font-normal">
                                                    {{ Carbon\Carbon::parse($log->created_at)->format('d M Y - h:i A') }}
                                                </span>
                                            </div>
                                            <p class="text-slate-500 text-[11px] mt-0.5 leading-normal">{{ $log->remarks }}</p>
                                            <p class="text-[9px] text-slate-400 uppercase mt-0.5 font-bold tracking-wider">
                                                Action By: {{ $log->changed_by_type === 'officer' ? 'BDPO Officer' : 'Applicant' }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-slate-400 font-semibold text-[11px] py-1">No activity log found.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- 2. APPLICANT PROFILE TAB -->
                    <div id="tab-profile" class="tab-content space-y-5 hidden">
                        <div class="glass-card overflow-hidden rounded-2xl">
                            <div class="px-5 py-3.5 bg-slate-50/50 border-b border-slate-200/50 flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Detailed Profile Information</h3>
                                <span class="bg-slate-200/80 text-slate-700 text-[9px] px-2 py-0.5 rounded-lg font-bold uppercase">A-Z Profiles</span>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-5 text-xs">
                                <!-- Profile summary -->
                                <div class="md:border-r border-slate-200/60 pr-0 md:pr-5 flex flex-col items-center justify-center text-center py-4">
                                    <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-full flex items-center justify-center font-extrabold text-2xl shadow-md shadow-blue-500/10 mb-3">
                                        {{ substr($ownerInfo->OwnerName, 0, 1) }}
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-800">{{ $ownerInfo->OwnerName }}</h4>
                                    <span class="text-[9px] text-slate-400 mt-0.5 font-mono font-bold">Owner ID: #{{ $ownerInfo->OwnerId }}</span>
                                </div>

                                <!-- Fields grid -->
                                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Relation & Father/Husband Name</span>
                                        <span class="font-bold text-slate-700 block mt-1.5 truncate" title="{{ $ownerInfo->FatherHusbandName }}">{{ $ownerInfo->FatherHusbandName ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Gender</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->Gender ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">PPP Family ID</span>
                                        <span class="font-bold text-slate-700 block mt-1.5 font-mono">{{ $ownerInfo->PPPId ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">PPP Member ID</span>
                                        <span class="font-bold text-slate-700 block mt-1.5 font-mono">{{ $ownerInfo->MemberId ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Caste / Category</span>
                                        <span class="font-bold text-slate-700 block mt-1.5 truncate" title="{{ $ownerInfo->Caste }}">{{ $ownerInfo->Caste ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Registered Mobile</span>
                                        <span class="font-bold text-slate-700 block mt-1.5 font-mono">{{ $ownerInfo->MobileNo ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. LOCATION MAPPING TAB -->
                    <div id="tab-location" class="tab-content space-y-5 hidden">
                        <div class="glass-card overflow-hidden rounded-2xl">
                            <div class="px-5 py-3.5 bg-slate-50/50 border-b border-slate-200/50">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Location & Area Mapping</h3>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3.5 text-xs">
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">District Name</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->DistrictName ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Block Name</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->BlockName ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Village Name</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->VillageName ?? 'N/A' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Flat / Plot ID</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->FlatId ?? '0' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Flat Number</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->FlatNo ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="bg-blue-50/10 p-4 rounded-xl border border-blue-100/50 text-xs">
                                    <span class="text-blue-500 font-extrabold uppercase text-[8px] block tracking-wider">Complete Registered Address</span>
                                    <span class="font-semibold text-slate-800 block mt-1.5 leading-relaxed">{{ $ownerInfo->OwnerAddress ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. AUDIT & REMARKS TAB -->
                    <div id="tab-audit" class="tab-content space-y-5 hidden">
                        <div class="glass-card overflow-hidden rounded-2xl">
                            <div class="px-5 py-3.5 bg-slate-50/50 border-b border-slate-200/50 flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Audit Trail & Verification Flags</h3>
                                <span class="text-[9px] text-slate-400 font-mono">Sync Date: {{ $ownerInfo->CreatedDate ? date('d-m-Y', strtotime($ownerInfo->CreatedDate)) : '—' }}</span>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <!-- Left -->
                                <div class="bg-slate-50/30 p-3.5 rounded-xl border border-slate-200/60">
                                    <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-slate-400">gavel</span>
                                        Verification Checklist
                                    </h4>
                                    <div class="space-y-2 font-semibold text-slate-600">
                                        <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                            <span>Scheme Approved:</span>
                                            <span class="{{ $ownerInfo->IsApproved ? 'text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg text-[10px]' : 'text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg text-[10px]' }}">{{ $ownerInfo->IsApproved ? 'Yes' : 'No' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                            <span>Scheme Rejected:</span>
                                            <span class="{{ $ownerInfo->IsRejected ? 'text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded-lg text-[10px]' : 'text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg text-[10px]' }}">{{ $ownerInfo->IsRejected ? 'Yes' : 'No' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                            <span>DC Reconsidered:</span>
                                            <span class="{{ $ownerInfo->IsDcReconsidered ? 'text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-lg text-[10px]' : 'text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg text-[10px]' }}">{{ $ownerInfo->IsDcReconsidered ? 'Yes' : 'No' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                            <span>Allotment Cancelled:</span>
                                            <span class="{{ $ownerInfo->IsAllotmentCancelled ? 'text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded-lg text-[10px]' : 'text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg text-[10px]' }}">{{ $ownerInfo->IsAllotmentCancelled ? 'Yes' : 'No' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span>DC Reopened Count:</span>
                                            <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-lg text-[10px]">{{ $ownerInfo->DCReOpenedCount ?? '0' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right -->
                                <div class="bg-slate-50/30 p-3.5 rounded-xl border border-slate-200/60 flex flex-col justify-between">
                                    <div class="space-y-3">
                                        <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px] text-slate-400">rate_review</span>
                                            Official Remarks
                                        </h4>
                                        <div>
                                            <span class="text-[8px] text-slate-400 font-extrabold uppercase tracking-wider block">Office Verification Remarks</span>
                                            <p class="font-semibold text-slate-700 bg-white p-2.5 rounded-lg border border-slate-200/40 mt-1 leading-relaxed shadow-sm">{{ $ownerInfo->Remarks ?? 'No Remarks' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-[8px] text-slate-400 font-extrabold uppercase tracking-wider block">DC Office Remarks</span>
                                            <p class="font-semibold text-slate-700 bg-white p-2.5 rounded-lg border border-slate-200/40 mt-1 leading-relaxed shadow-sm">{{ $ownerInfo->DCRemarks ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-[9px] text-slate-400 text-right mt-3 font-mono font-bold">
                                        Updated: {{ $ownerInfo->UpdatedDate ? date('d-m-Y H:i', strtotime($ownerInfo->UpdatedDate)) : '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 7. PHYSICAL POSSESSION TAB -->
                    @if($possessionApplication)
                    <div id="tab-possession" class="tab-content space-y-5 hidden">
                        <!-- Upper Detail Card -->
                        <div class="glass-card overflow-hidden rounded-2xl">
                            <div class="px-5 py-4 bg-slate-50/50 border-b border-slate-200/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">Physical Possession Status & Verification Details</h3>
                                    <p class="text-[9px] text-slate-400 mt-1 font-bold">App Number: <span class="font-mono">{{ $possessionApplication->application_number }}</span> | Slip ID: <span class="font-mono">{{ $possessionApplication->slip_id }}</span></p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($possessionApplication->physical_possession_status !== 'Visit Scheduled')
                                        <a href="{{ route('mmgay.villager.download-slip') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-extrabold uppercase px-3 py-2 rounded-xl transition shadow flex items-center gap-1 active:scale-95">
                                            <span class="material-symbols-outlined text-[13px]">picture_as_pdf</span> Download Slip
                                        </a>
                                    @endif
                                    @if($possessionApplication->physical_possession_status === 'Verified')
                                        <a href="{{ route('mmgay.villager.download-certificate', $possessionApplication->secure_id) }}?inline=1" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-extrabold uppercase px-3 py-2 rounded-xl transition shadow flex items-center gap-1 active:scale-95">
                                            <span class="material-symbols-outlined text-[13px]">verified_user</span> Download Report
                                        </a>
                                    @endif
                                    @if($possessionApplication->possession_certificate)
                                        <a href="{{ asset('storage/' . $possessionApplication->possession_certificate) }}" target="_blank" rel="noopener" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-extrabold uppercase px-3 py-2 rounded-xl transition shadow flex items-center gap-1 active:scale-95">
                                            <span class="material-symbols-outlined text-[13px]">description</span> Final Possession Letter
                                        </a>
                                    @endif
                                    @if($possessionApplication->site_engineer_file)
                                        <a href="{{ asset('storage/' . $possessionApplication->site_engineer_file) }}" target="_blank" rel="noopener" class="bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-extrabold uppercase px-3 py-2 rounded-xl transition shadow flex items-center gap-1 active:scale-95">
                                            <span class="material-symbols-outlined text-[13px]">task</span> BDO Signed Report
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="p-5 space-y-6">
                                <!-- Stepper status tracker -->
                                <div>
                                    <h4 class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-4 leading-none">Possession Milestone Tracker</h4>
                                    
                                    @php
                                        $status = $possessionApplication->physical_possession_status;
                                        
                                        // Status order mapping for 5-step milestone tracker
                                        $statusOrder = [
                                            'Eligible for Physical Possession' => 1,
                                            'Visit Scheduled' => 2,
                                            'Slot Selected' => 3,
                                            'Site Verified' => 4,
                                            'Verified' => 5,
                                            'Rejected' => 4 // Highlight verification step on rejection
                                        ];
                                        
                                        $currentStep = $statusOrder[$status] ?? 1;
                                        
                                        // 5 steps = 4 segments. Calculate segment progress percent.
                                        $progressPercent = ($currentStep - 1) * 25;
                                        
                                        $steps = [
                                            [
                                                'index' => 1,
                                                'title' => 'Eligible',
                                                'subtitle' => 'Possession Eligible',
                                                'icon' => 'assignment_turned_in',
                                            ],
                                            [
                                                'index' => 2,
                                                'title' => 'Visit Scheduled',
                                                'subtitle' => 'BDPO offered slots',
                                                'icon' => 'calendar_today',
                                            ],
                                            [
                                                'index' => 3,
                                                'title' => 'Slot Selected',
                                                'subtitle' => 'Citizen confirmed date',
                                                'icon' => 'how_to_reg',
                                            ],
                                            [
                                                'index' => 4,
                                                'title' => 'Site Verified',
                                                'subtitle' => 'GPS & Photo captured',
                                                'icon' => 'pin_drop',
                                            ],
                                            [
                                                'index' => 5,
                                                'title' => 'Verified & Approved',
                                                'subtitle' => 'Report & Letter uploaded',
                                                'icon' => 'verified_user',
                                            ]
                                        ];
                                    @endphp

                                    <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0 mt-4 pl-4 md:pl-0">
                                        <!-- Line Background -->
                                        <div class="absolute left-[20px] top-4 md:left-[10%] md:right-[10%] md:top-5 h-[90%] md:h-[2px] w-[2px] md:w-auto bg-slate-200/60 z-0 rounded-full">
                                            <!-- Active Line Fill for Mobile -->
                                            <div class="md:hidden bg-gradient-to-b from-emerald-500 to-indigo-600 w-full transition-all duration-500 rounded-full" style="height: {{ $progressPercent }}%"></div>
                                            <!-- Active Line Fill for Desktop -->
                                            <div class="hidden md:block bg-gradient-to-r from-emerald-500 via-blue-500 to-indigo-600 h-full transition-all duration-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        
                                        @foreach($steps as $s)
                                            @php
                                                $idx = $s['index'];
                                                $isCompletedStep = ($idx < $currentStep) || ($status === 'Verified');
                                                $isCurrentStep = ($idx === $currentStep) && ($status !== 'Verified');
                                                $isPendingStep = ($idx > $currentStep);
                                                
                                                // Default classes
                                                $circleClass = '';
                                                $iconName = $s['icon'];
                                                $titleClass = '';
                                                $subtitleClass = '';
                                                $badgeText = '';
                                                
                                                if ($status === 'Rejected' && $idx === 4) {
                                                    // Special styling for rejection at the verification step
                                                    $circleClass = 'bg-rose-500 text-white shadow-lg shadow-rose-500/20 border-2 border-rose-500 ring-4 ring-rose-50';
                                                    $iconName = 'cancel';
                                                    $titleClass = 'text-rose-600 font-bold';
                                                    $subtitleClass = 'text-rose-400 font-semibold';
                                                    $badgeText = 'Rejected';
                                                } elseif ($isCompletedStep) {
                                                    $circleClass = 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 border-2 border-emerald-500';
                                                    $iconName = 'check';
                                                    $titleClass = 'text-slate-800 font-semibold';
                                                    $subtitleClass = 'text-slate-400';
                                                } elseif ($isCurrentStep) {
                                                    $circleClass = 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 border-2 border-indigo-600 ring-4 ring-indigo-50 animate-pulse-subtle';
                                                    $titleClass = 'text-indigo-600 font-extrabold';
                                                    $subtitleClass = 'text-indigo-400 font-semibold';
                                                    $badgeText = 'Current';
                                                } else {
                                                    $circleClass = 'bg-slate-50 text-slate-400 border border-slate-200';
                                                    $titleClass = 'text-slate-400 font-medium';
                                                    $subtitleClass = 'text-slate-300';
                                                }
                                            @endphp

                                            <div class="relative flex md:flex-col items-center md:text-center gap-3.5 md:gap-2.5 z-10 md:w-1/5 group transition duration-300">
                                                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center transition duration-300 {{ $circleClass }}">
                                                    <span class="material-symbols-outlined text-[15px] md:text-[18px] font-bold">{{ $iconName }}</span>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-1.5 justify-center">
                                                        <h4 class="text-xs {{ $titleClass }}">{{ $s['title'] }}</h4>
                                                        @if($badgeText)
                                                            <span class="text-[7px] font-extrabold uppercase px-1 py-0.5 rounded leading-none {{ $badgeText === 'Rejected' ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700' }}">
                                                                {{ $badgeText }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[9px] {{ $subtitleClass }} mt-0.5">{{ $s['subtitle'] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <hr class="border-slate-100">

                                <!-- Grid Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                                    <!-- Appointment Details Block -->
                                    <div class="space-y-4">
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[9px] text-slate-400 mb-2">Verification Appointment Info</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-slate-50/40 p-3 rounded-xl border border-slate-200/40">
                                                <span class="text-slate-400 font-extrabold uppercase text-[8px] block">Current Status</span>
                                                <span class="font-bold text-blue-600 block mt-1">{{ $possessionApplication->physical_possession_status }}</span>
                                            </div>
                                            <div class="bg-slate-50/40 p-3 rounded-xl border border-slate-200/40">
                                                <span class="text-slate-400 font-extrabold uppercase text-[8px] block">Scheduled Visit Date</span>
                                                <span class="font-bold text-slate-700 block mt-1">
                                                    {{ $possessionApplication->citizen_visit_date ? date('d M Y, h:i A', strtotime($possessionApplication->citizen_visit_date)) : 'Awaiting confirmation' }}
                                                </span>
                                            </div>
                                            <div class="bg-slate-50/40 p-3 rounded-xl border border-slate-200/40">
                                                <span class="text-slate-400 font-extrabold uppercase text-[8px] block">BDPO Officer ID</span>
                                                <span class="font-bold text-slate-700 block mt-1 font-mono">
                                                    {{ $possessionApplication->verified_by ? '#'.$possessionApplication->verified_by : '—' }}
                                                </span>
                                            </div>
                                            <div class="bg-slate-50/40 p-3 rounded-xl border border-slate-200/40">
                                                <span class="text-slate-400 font-extrabold uppercase text-[8px] block">Verified DateTime</span>
                                                <span class="font-bold text-slate-700 block mt-1">
                                                    {{ $possessionApplication->verified_at ? date('d M Y, h:i A', strtotime($possessionApplication->verified_at)) : '—' }}
                                                </span>
                                            </div>
                                        </div>

                                        @if($possessionApplication->remarks)
                                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                                                <span class="text-slate-400 font-extrabold uppercase text-[8px] block">BDPO Verification Remarks</span>
                                                <p class="font-semibold text-slate-700 mt-1 leading-relaxed">{{ $possessionApplication->remarks }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Location Mapping Block -->
                                    <div class="space-y-4">
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[9px] text-slate-400 mb-2">Captured Site Geolocation & Coordinates</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-slate-50/40 p-3 rounded-xl border border-slate-200/40">
                                                <span class="text-slate-400 font-extrabold uppercase text-[8px] block">GPS Latitude</span>
                                                <span class="font-bold text-slate-700 block mt-1 font-mono">{{ $possessionApplication->latitude ?? '—' }}</span>
                                            </div>
                                            <div class="bg-slate-50/40 p-3 rounded-xl border border-slate-200/40">
                                                <span class="text-slate-400 font-extrabold uppercase text-[8px] block">GPS Longitude</span>
                                                <span class="font-bold text-slate-700 block mt-1 font-mono">{{ $possessionApplication->longitude ?? '—' }}</span>
                                            </div>
                                        </div>

                                        @if($possessionApplication->latitude && $possessionApplication->longitude)
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ $possessionApplication->latitude }},{{ $possessionApplication->longitude }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 transition py-2 px-3 rounded-xl font-bold border border-blue-100/60 mt-2 text-center text-[10px] active:scale-95">
                                                <span class="material-symbols-outlined text-[15px]">map</span> View Captured Location on Google Maps
                                            </a>
                                        @endif

                                        @if($possessionApplication->plot_image)
                                            <div class="mt-2 space-y-1">
                                                <span class="text-slate-400 font-extrabold uppercase text-[8px] block">Captured Site Image</span>
                                                <div class="relative w-48 h-32 rounded-xl overflow-hidden border border-slate-200 shadow-sm group">
                                                    <img src="{{ asset('storage/' . $possessionApplication->plot_image) }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110" alt="Captured Plot Image">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Physical Possession Logs Section -->
                        <div class="glass-card overflow-hidden rounded-2xl">
                            <div class="px-5 py-3.5 bg-slate-50/50 border-b border-slate-200/50 flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Physical Possession Audit Log</h3>
                                <span class="bg-amber-100 text-amber-800 text-[9px] px-2 py-0.5 rounded-lg font-bold uppercase">Timeline</span>
                            </div>
                            <div class="p-5 space-y-4">
                                @if(count($logs) > 0)
                                    <div class="relative border-l-2 border-slate-200 pl-6 space-y-5">
                                        @foreach($logs as $log)
                                            <div class="relative">
                                                <!-- Marker -->
                                                <div class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-slate-200 bg-white flex items-center justify-center">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                                </div>
                                                <div class="text-xs">
                                                    <span class="text-[9px] text-slate-400 font-bold font-mono">{{ date('d M Y - h:i A', strtotime($log->created_at)) }}</span>
                                                    <h4 class="font-bold text-slate-800 mt-0.5">Status: <span class="text-blue-600">{{ $log->old_status }}</span> &rarr; <span class="text-green-600">{{ $log->new_status }}</span></h4>
                                                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed bg-slate-50 p-2 rounded-lg border border-slate-100">{{ $log->remarks }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 text-center font-bold">No possession status logs found.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 5. HELP & SUPPORT TAB -->
                    <div id="tab-support" class="tab-content space-y-5 hidden">
                        <div class="glass-card overflow-hidden rounded-2xl">
                            <div class="px-5 py-3.5 bg-slate-50/50 border-b border-slate-100">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Official Help & Support Channels</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div class="border border-slate-200/65 rounded-2xl p-4 hover:bg-slate-50/50 transition-all flex gap-3.5 items-start glass-card">
                                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">phone_in_talk</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs">Helpline Center</h4>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Call: <strong class="text-slate-700">0172-2585852</strong>, <strong class="text-slate-700">0172-2568687</strong>, <strong class="text-slate-700">0172-2567233</strong></p>
                                    </div>
                                </div>
                                <div class="border border-slate-200/65 rounded-2xl p-4 hover:bg-slate-50/50 transition-all flex gap-3.5 items-start glass-card">
                                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">mail</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs">Support Email</h4>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Write to: <strong class="text-slate-700">director-hfa@hry.gov.in</strong>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6. PROPERTY ALLOTTED DETAILS TAB -->
                    <div id="tab-property" class="tab-content space-y-5 hidden">
                        <div class="glass-card overflow-hidden rounded-2xl">
                            <div class="px-5 py-3.5 bg-slate-50/50 border-b border-slate-200/50 flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Property Allotted Details</h3>
                                <span class="bg-indigo-100 text-indigo-800 text-[9px] px-2 py-0.5 rounded-lg font-bold uppercase">Allotment Details</span>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3.5 text-xs">
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Flat / Plot ID</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->FlatId ?? '—' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Flat Number</span>
                                        <span class="font-bold text-slate-700 block mt-1.5 font-mono">{{ $ownerInfo->FlatNo ?? '—' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Village Name</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->VillageName ?? '—' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">Block Name</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->BlockName ?? '—' }}</span>
                                    </div>
                                    <div class="bg-slate-50/30 p-3 rounded-xl border border-slate-200/40">
                                        <span class="text-slate-400 font-extrabold uppercase text-[8px] block tracking-wider">District Name</span>
                                        <span class="font-bold text-slate-700 block mt-1.5">{{ $ownerInfo->DistrictName ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </main>

                <!-- Footer -->
                <footer class="bg-slate-900 border-t border-slate-800 text-slate-500 py-3 text-center z-10 flex-shrink-0">
                    <div class="max-w-full mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-1 text-[9px] leading-none">
                        <p>© 2026 Department of Housing For All, Government of Haryana. All rights reserved.</p>
                        <p class="text-slate-600/90 font-medium font-mono">Designed by Citizen Resources Information Department, Haryana (CRID)</p>
                    </div>
                </footer>
            </div>
        @else
            <!-- Fallback profile view if ownerInfo record is missing -->
            <main class="flex-grow p-6 overflow-y-auto">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-8 text-center max-w-2xl mx-auto my-12">
                    <span class="material-symbols-outlined text-[64px] text-amber-500 mb-2">warning_amber</span>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">No Matching Scheme Record Found</h3>
                    <p class="text-xs text-slate-500 mb-6">
                        We could not locate a matching profile in our central scheme database for your registered mobile number: <strong>{{ $user->mobile }}</strong>. 
                    </p>
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-left text-xs mb-6">
                        <h4 class="font-bold text-slate-700 mb-2">Your Account Profile</h4>
                        <div class="grid grid-cols-2 gap-2 text-slate-600">
                            <div><span class="font-bold">Name:</span> {{ $user->name }}</div>
                            <div><span class="font-bold">Mobile:</span> {{ $user->mobile }}</div>
                            <div><span class="font-bold">Scheme:</span> {{ $user->scheme ?? 'N/A' }}</div>
                            <div><span class="font-bold">District:</span> {{ $user->district_name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </main>
        @endif

    </div>

    <!-- Tab Switching & Toggle Script -->
    <script>
        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(function(content) {
                content.classList.add('hidden');
            });
            // Show the selected tab content
            document.getElementById('tab-' + tabId).classList.remove('hidden');

            // Reset navigation button classes
            document.querySelectorAll('.tab-btn').forEach(function(btn) {
                btn.className = "tab-btn w-full flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all hover:bg-slate-800/40 hover:text-slate-300 text-left";
            });

            // Set the clicked navigation button as active
            var activeBtn = document.getElementById('tab-btn-' + tabId);
            activeBtn.className = "tab-btn tab-btn-active w-full flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all text-left text-blue-400";

            // Update the header bar title dynamically
            var headerTitle = document.getElementById('header-title');
            var labelSpan = activeBtn.querySelector('.tab-label');
            headerTitle.innerText = labelSpan ? labelSpan.innerText : 'Dashboard Overview';
            
            // Close mobile sidebar if clicked
            if (window.innerWidth <= 768) {
                document.getElementById('dashboard-wrapper').classList.remove('sidebar-open');
                document.getElementById('menu-icon').innerText = 'menu';
            }
        }

        function toggleSidebar() {
            const wrapper = document.getElementById('dashboard-wrapper');
            const menuIcon = document.getElementById('menu-icon');
            
            if (window.innerWidth <= 768) {
                wrapper.classList.toggle('sidebar-open');
                const isOpen = wrapper.classList.contains('sidebar-open');
                menuIcon.innerText = isOpen ? 'menu_open' : 'menu';
            } else {
                wrapper.classList.toggle('collapsed');
                const isCollapsed = wrapper.classList.contains('collapsed');
                menuIcon.innerText = isCollapsed ? 'menu' : 'menu_open';
            }
        }

        function openSlotSelectionModal() {
            Swal.fire({
                title: '<span class="text-sm font-extrabold text-slate-800 uppercase tracking-wide block">Select Visit Slot</span>',
                html: `
                    <form id="swal_slot_form" class="text-left space-y-3 mt-3">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">Select one option for the physical visit:</p>
                        
                        @if($possessionApplication && $possessionApplication->visit_slot_1)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition cursor-pointer block">
                                <input type="radio" name="selected_slot" value="{{ date('Y-m-d H:i:s', strtotime($possessionApplication->visit_slot_1)) }}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300" required>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Option 1</span>
                                    <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ date('d M Y - h:i A', strtotime($possessionApplication->visit_slot_1)) }}</span>
                                </div>
                            </label>
                        @endif
                        
                        @if($possessionApplication && $possessionApplication->visit_slot_2)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition cursor-pointer block">
                                <input type="radio" name="selected_slot" value="{{ date('Y-m-d H:i:s', strtotime($possessionApplication->visit_slot_2)) }}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300" required>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Option 2</span>
                                    <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ date('d M Y - h:i A', strtotime($possessionApplication->visit_slot_2)) }}</span>
                                </div>
                            </label>
                        @endif
                        
                        @if($possessionApplication && $possessionApplication->visit_slot_3)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition cursor-pointer block">
                                <input type="radio" name="selected_slot" value="{{ date('Y-m-d H:i:s', strtotime($possessionApplication->visit_slot_3)) }}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300" required>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Option 3</span>
                                    <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ date('d M Y - h:i A', strtotime($possessionApplication->visit_slot_3)) }}</span>
                                </div>
                            </label>
                        @endif
                        
                        @if($possessionApplication && $possessionApplication->visit_instructions)
                            <div class="bg-blue-50 border border-blue-100 text-blue-800 p-3 rounded-xl text-[10px] leading-relaxed mt-2 font-medium">
                                <strong class="text-blue-900 block font-bold uppercase tracking-wider text-[9px]">Instructions:</strong>
                                "{{ $possessionApplication->visit_instructions }}"
                            </div>
                        @endif
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirm Selection',
                confirmButtonColor: '#0058bc',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('swal_slot_form');
                    const selected = form.querySelector('input[name="selected_slot"]:checked');
                    if (!selected) {
                        Swal.showValidationMessage('Please select one of the available slots');
                        return false;
                    }
                    return selected.value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('swal_slot_form');
                    const selectedRadio = form.querySelector('input[name="selected_slot"]:checked');
                    const optionDiv = selectedRadio.nextElementSibling;
                    const optionLabel = optionDiv.querySelector('span.uppercase').textContent.trim();
                    const dateStr = optionDiv.querySelector('span.text-xs').textContent.trim();
                    
                    Swal.fire({
                        icon: 'question',
                        title: 'Confirm Slot Choice',
                        html: `You have selected:<br><strong class="text-[#0058bc] text-sm">${optionLabel}: ${dateStr}</strong><br><br>Are you sure you want to select this visit slot?`,
                        showCancelButton: true,
                        confirmButtonColor: '#0058bc',
                        cancelButtonColor: '#cbd5e1',
                        confirmButtonText: 'Yes, Confirm',
                        cancelButtonText: 'Cancel'
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            Swal.fire({
                                title: 'Confirming Selection...',
                                text: 'Please wait, updating your choice.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Create dynamic form and submit
                            const formEl = document.createElement('form');
                            formEl.method = 'POST';
                            formEl.action = "{{ route('mmgay.villager.submit.post') }}";
                            
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = "{{ csrf_token() }}";
                            formEl.appendChild(csrfInput);

                            const slotInput = document.createElement('input');
                            slotInput.type = 'hidden';
                            slotInput.name = 'selected_slot';
                            slotInput.value = result.value;
                            formEl.appendChild(slotInput);

                            document.body.appendChild(formEl);
                            formEl.submit();
                        } else {
                            // Re-open slot selection modal if cancelled
                            openSlotSelectionModal();
                        }
                    });
                }
            });
        }

        // Render Laravel Session Alerts via SweetAlert2
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '<span class="text-sm font-bold text-slate-800">Success</span>',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0058bc'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-sm font-bold text-slate-800">Error</span>',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ba1a1a'
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-sm font-bold text-slate-800">Validation Failures</span>',
                    html: `
                        <div class="text-left text-xs text-slate-600 space-y-1 pl-4 pr-2 max-h-48 overflow-y-auto list-disc">
                            <ul class="list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    `,
                    confirmButtonColor: '#ba1a1a'
                });
            @endif
        });
    </script>
    @include('partials.global-loader')
</body>
</html>
