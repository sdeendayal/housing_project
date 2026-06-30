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
                            
                            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0 mt-2 pl-4 md:pl-0">
                                <!-- Connecting Line -->
                                <div class="absolute left-[20px] top-4 md:left-[12.5%] md:right-[12.5%] md:top-5 h-[90%] md:h-[2px] w-[2px] md:w-auto bg-slate-100 z-0"></div>
                                
                                <!-- Step 1 -->
                                <div class="relative flex md:flex-col items-center md:text-center gap-3.5 md:gap-2 z-10 md:w-1/4">
                                    <div class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25">
                                        <span class="material-symbols-outlined text-[16px] md:text-[18px] font-bold">check</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800">Registered</h4>
                                        <p class="text-[9px] text-slate-400">Profile matches master records</p>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div class="relative flex md:flex-col items-center md:text-center gap-3.5 md:gap-2 z-10 md:w-1/4">
                                    <div class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25">
                                        <span class="material-symbols-outlined text-[16px] md:text-[18px] font-bold">check</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800">Under Review</h4>
                                        <p class="text-[9px] text-slate-400">Documents verified by cell</p>
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div class="relative flex md:flex-col items-center md:text-center gap-3.5 md:gap-2 z-10 md:w-1/4">
                                    <div class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25">
                                        <span class="material-symbols-outlined text-[16px] md:text-[18px] font-bold">check</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800">District Audit</h4>
                                        <p class="text-[9px] text-slate-400">Block & village verified</p>
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div class="relative flex md:flex-col items-center md:text-center gap-3.5 md:gap-2 z-10 md:w-1/4">
                                    @if($ownerInfo->IsAllotmentCancelled == 1)
                                        <div class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/25 animate-pulse">
                                            <span class="material-symbols-outlined text-[16px] md:text-[18px] font-bold">close</span>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-rose-600">Allotment Cancelled</h4>
                                            <p class="text-[9px] text-rose-400">Cancelled by DC Office</p>
                                        </div>
                                    @elseif($ownerInfo->IsRejected == 1)
                                        <div class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/25 animate-pulse">
                                            <span class="material-symbols-outlined text-[16px] md:text-[18px] font-bold">close</span>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-rose-600">Rejected / Ineligible</h4>
                                            <p class="text-[9px] text-rose-400">{{ $ownerInfo->Remarks ?? 'Not eligible' }}</p>
                                        </div>
                                    @elseif($ownerInfo->IsApproved == 1)
                                        <div class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25 animate-pulse">
                                            <span class="material-symbols-outlined text-[16px] md:text-[18px] font-bold">done_all</span>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-emerald-600">Approved</h4>
                                            <p class="text-[9px] text-emerald-400">Profile verification success</p>
                                        </div>
                                    @else
                                        <div class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-blue-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/25 animate-pulse">
                                            <span class="material-symbols-outlined text-[16px] md:text-[18px]">hourglass_top</span>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-blue-600">Pending Decision</h4>
                                            <p class="text-[9px] text-blue-400">Awaiting board approval</p>
                                        </div>
                                    @endif
                                </div>
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

                    <!-- 5. HELP & SUPPORT TAB -->
                    <div id="tab-support" class="tab-content space-y-5 hidden">
                        <div class="glass-card overflow-hidden rounded-2xl">
                            <div class="px-5 py-3.5 bg-slate-50/50 border-b border-slate-100">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Official Help & Support Channels</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                <div class="border border-slate-200/65 rounded-2xl p-4 hover:bg-slate-50/50 transition-all flex gap-3.5 items-start glass-card">
                                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">phone_in_talk</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs">Helpline Center</h4>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Call: <strong class="text-slate-700">1800-180-2128</strong> (Toll Free).</p>
                                    </div>
                                </div>
                                <div class="border border-slate-200/65 rounded-2xl p-4 hover:bg-slate-50/50 transition-all flex gap-3.5 items-start glass-card">
                                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">mail</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs">Support Email</h4>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Write to: <strong class="text-slate-700">housingforall@hry.gov.in</strong>.</p>
                                    </div>
                                </div>
                                <div class="border border-slate-200/65 rounded-2xl p-4 hover:bg-slate-50/50 transition-all flex gap-3.5 items-start glass-card">
                                    <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">domain</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs">Nodal Office</h4>
                                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Visit Nodal District HQ or the Department of Housing cell.</p>
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
    </script>

</body>
</html>
