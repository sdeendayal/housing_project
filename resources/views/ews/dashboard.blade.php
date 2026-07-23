<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Citizen Portal - Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        .dashboard-vibrant-header {
            background: linear-gradient(135deg, #6366f1 0%, #d946ef 100%);
        }
        .glass-widget {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 15px rgba(148, 163, 184, 0.03);
            transition: all 0.2s ease;
        }
        .glass-widget:hover {
            border-color: rgba(99, 102, 241, 0.2);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.05);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6366f1;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden text-slate-700 relative text-xs">

    <!-- Ambient background glows -->
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-indigo-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-pink-500/5 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Page Wrapper -->
    <div class="w-full h-full flex flex-col md:flex-row overflow-hidden relative z-10">

        <!-- Sidebar Left Console Menu -->
        <aside class="w-full md:w-52 bg-white border-r border-slate-200/80 flex flex-col justify-between p-3 flex-shrink-0 shadow-sm">
            <div class="space-y-4">
                <!-- Branding Header -->
                <div class="flex items-center gap-2 px-1 py-1">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center shadow-md shadow-indigo-500/20">
                        <i class="bi bi-houses text-xs text-white"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black tracking-wider text-indigo-600">EWS CITIZEN</div>
                        <div class="text-[8px] text-slate-400 tracking-widest font-bold uppercase">Control Panel</div>
                    </div>
                </div>

                <div class="border-t border-slate-100 my-1.5"></div>

                <!-- Navigation List -->
                <nav class="space-y-1" id="sidebar-nav">
                    <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-white bg-gradient-to-r from-indigo-500 to-purple-600 text-[10px] font-extrabold uppercase tracking-wider shadow-sm transition-all text-left">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </button>
                    
                    <button onclick="switchTab('rejections')" id="nav-rejections" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-900 text-[10px] font-bold uppercase tracking-wider transition-all text-left">
                        <i class="bi bi-exclamation-triangle-fill text-slate-400"></i>
                        <span>Verification Status</span>
                    </button>

                    @if(!$pppExclusion && !$propertyReject && !$houseReject)
                    <button onclick="switchTab('bookings')" id="nav-bookings" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-900 text-[10px] font-bold uppercase tracking-wider transition-all text-left">
                        <i class="bi bi-calendar-check-fill text-slate-400"></i>
                        <span>Draw & Bookings</span>
                    </button>
                    @endif

                    @if(!$pppExclusion && !$propertyReject && !$houseReject && $eligibleDraw && $booking)
                    <button onclick="switchTab('allotment')" id="nav-allotment" class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-900 text-[10px] font-bold uppercase tracking-wider transition-all text-left">
                        <i class="bi bi-house-check-fill text-slate-400"></i>
                        <span>Allotment Status</span>
                    </button>
                    @endif
                </nav>
            </div>

            <!-- Profile Info Card -->
            <div class="p-2 bg-slate-50 border border-slate-200 rounded-xl flex flex-col gap-1.5 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-600 font-black uppercase text-[10px]">
                        {{ substr($ewsData->full_name ?? 'U', 0, 2) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[9px] font-bold text-slate-900 truncate leading-tight">{{ $ewsData->full_name ?? 'Citizen User' }}</div>
                        <div class="text-[8px] text-slate-400 truncate leading-tight">+91 {{ $ewsData->mobile_number ?? '0000000000' }}</div>
                    </div>
                </div>
                <a href="{{ route('ews.logout') }}" class="w-full py-1 rounded-lg bg-white hover:bg-red-50 hover:text-red-650 border border-slate-200 hover:border-red-200 text-slate-600 font-extrabold text-[8px] transition-colors flex items-center justify-center gap-1 uppercase tracking-wider shadow-sm">
                    <i class="bi bi-box-arrow-left"></i> Logout Account
                </a>
            </div>
        </aside>

        <!-- Main Body Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50/50 overflow-hidden">
            
            <!-- Top bar -->
            <header class="h-10 border-b border-slate-200/80 bg-white flex items-center justify-between px-4 flex-shrink-0 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Active System File ID:</span>
                    <span class="text-[9px] font-black text-indigo-600 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded font-mono">{{ $ewsData->application_number ?? '—' }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 px-2.5 py-0.5 rounded-lg text-[9px] font-extrabold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-emerald-600 tracking-wider">SECURE PORTAL</span>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content Frame -->
            <main class="flex-1 overflow-y-auto p-3.5 space-y-3 custom-scrollbar">

                <!-- 1. DASHBOARD PROFILE SECTION -->
                <div id="section-dashboard" class="space-y-3">
                    <!-- Welcome Header Panel -->
                    <div class="p-3.5 dashboard-vibrant-header rounded-xl relative overflow-hidden flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shadow-md text-white">
                        <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="space-y-0.5 relative z-10">
                            <span class="text-[8px] font-black text-indigo-100 uppercase tracking-widest flex items-center gap-1">
                                <i class="bi bi-building"></i> EWS Scheme Beneficiary Registry
                            </span>
                            <h3 class="text-base font-black leading-none uppercase">APPLICATION ID: {{ $ewsData->application_number ?? '—' }}</h3>
                            <p class="text-white/80 text-[9px] font-light max-w-xl">This record contains validated socio-economic status information verified via local administrative block authorities.</p>
                        </div>
                    </div>

                    <!-- 5 Metrics Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                        <div class="glass-widget px-3 py-1.5 rounded-lg">
                            <span class="text-[8px] text-slate-400 uppercase font-bold tracking-wider">File Status</span>
                            <div class="text-[11px] font-black text-indigo-600 mt-0.5 uppercase leading-tight">{{ $ewsData->status ?? 'Verified' }}</div>
                        </div>
                        <div class="glass-widget px-3 py-1.5 rounded-lg">
                            <span class="text-[8px] text-slate-400 uppercase font-bold tracking-wider">Income Bracket</span>
                            <div class="text-[11px] font-black text-slate-800 mt-0.5 leading-tight">{{ $ewsData->IncomeVerified ?? '—' }}</div>
                        </div>
                        <div class="glass-widget px-3 py-1.5 rounded-lg">
                            <span class="text-[8px] text-slate-400 uppercase font-bold tracking-wider">Monthly Income</span>
                            <div class="text-[11px] font-black text-emerald-600 mt-0.5 leading-tight">
                                @if(is_numeric($ewsData->monthly_income ?? null))
                                    ₹ {{ number_format((float)$ewsData->monthly_income) }}
                                @else
                                    {{ $ewsData->monthly_income ?? '—' }}
                                @endif
                            </div>
                        </div>
                        <div class="glass-widget px-3 py-1.5 rounded-lg">
                            <span class="text-[8px] text-slate-400 uppercase font-bold tracking-wider">Caste Group</span>
                            <div class="text-[11px] font-black text-slate-800 mt-0.5 leading-tight">{{ $ewsData->caste ?? 'General' }}</div>
                        </div>
                        <div class="glass-widget px-3 py-1.5 rounded-lg col-span-2 sm:col-span-1">
                            <span class="text-[8px] text-slate-400 uppercase font-bold tracking-wider">Admin Block</span>
                            <div class="text-[11px] font-black text-slate-700 mt-0.5 leading-tight truncate">{{ $ewsData->bt_name ?? '—' }}</div>
                        </div>
                    </div>

                    <!-- Layout splits -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                        <div class="lg:col-span-8 space-y-3">
                            <div class="glass-widget p-3.5 rounded-xl space-y-2">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-wider flex items-center gap-1">
                                        <i class="bi bi-file-text text-indigo-500"></i> Citizen Personal & Socio-Economic Matrix
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-1.5 text-[10px] font-light">
                                    <div class="space-y-1 bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                                        <div class="text-[8px] text-slate-400 font-extrabold uppercase mb-1">General Info</div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Full Name</span>
                                            <span class="font-bold text-slate-800">{{ $ewsData->full_name ?? '—' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Father's Name</span>
                                            <span class="font-bold text-slate-800 truncate max-w-[80px]" title="{{ $ewsData->fathers_full_name ?? ($ewsData->father_name ?? '') }}">{{ $ewsData->fathers_full_name ?? ($ewsData->father_name ?? '—') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Age / Gender</span>
                                            <span class="font-bold text-slate-800">{{ $ewsData->age ?? '—' }} yrs / {{ $ewsData->gender ?? '—' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5">
                                            <span class="text-slate-400">Date of Birth</span>
                                            <span class="font-bold text-slate-800">{{ $ewsData->date_of_birth ?? '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1 bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                                        <div class="text-[8px] text-slate-400 font-extrabold uppercase mb-1">Financial & ID</div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Aadhar Reference</span>
                                            <span class="font-bold text-slate-800">XXXX-XXXX-{{ substr($ewsData->aadhar_no ?? '0000', -4) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Monthly Income</span>
                                            <span class="font-extrabold text-emerald-600">
                                                @if(is_numeric($ewsData->monthly_income ?? null))
                                                    ₹ {{ number_format((float)$ewsData->monthly_income) }}
                                                @else
                                                    {{ $ewsData->monthly_income ?? '—' }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Electricity A/c</span>
                                            <span class="font-bold text-slate-800">{{ $ewsData->electricity_bill_account_no ?? '0' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5">
                                            <span class="text-slate-400">Marital Status</span>
                                            <span class="font-bold text-slate-800">{{ $ewsData->MaritalStatus ?? ($ewsData->do_you_have_spouce ?? '—') }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1 bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                                        <div class="text-[8px] text-slate-400 font-extrabold uppercase mb-1">Properties & Assets</div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Ownership</span>
                                            <span class="font-bold text-slate-800">{{ $ewsData->house_ownership ?? '—' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Rent Paid</span>
                                            <span class="font-bold text-slate-800">
                                                @if(is_numeric($ewsData->rent_amount ?? null))
                                                    ₹ {{ number_format((float)$ewsData->rent_amount) }}
                                                @else
                                                    {{ $ewsData->rent_amount ?? '—' }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                            <span class="text-slate-400">Vehicle Type</span>
                                            <span class="font-bold text-slate-800 truncate max-w-[80px]" title="{{ $ewsData->vehicle_ownership ?? '—' }} ({{ $ewsData->type_of_vehicle ?? '—' }})">
                                                {{ $ewsData->vehicle_ownership ?? '—' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-0.5">
                                            <span class="text-slate-400">Property in India?</span>
                                            <span class="font-bold text-slate-800">{{ $ewsData->do_you_own_any_property_or_house_across_india ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="glass-widget p-3 rounded-xl space-y-2 bg-white">
                                <div class="flex items-center gap-2 border-b border-slate-100 pb-1">
                                    <i class="bi bi-geo-alt-fill text-indigo-500 text-xs"></i>
                                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-wider">Geographical Coordinates</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2.5 text-[10px] font-light">
                                    <div class="bg-slate-50 border border-slate-100 p-1.5 rounded-lg flex flex-col justify-between">
                                        <span class="text-slate-400 text-[8px] uppercase font-bold">House Coordinates</span>
                                        <div class="flex items-center justify-between font-bold text-slate-800 mt-0.5">
                                            <span>{{ $ewsData->coordinates_of_current_house_address ?? '—' }}</span>
                                            <a href="https://maps.google.com/?q={{ urlencode($ewsData->coordinates_of_current_house_address ?? '') }}" target="_blank" class="text-indigo-600 hover:text-indigo-500">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 p-1.5 rounded-lg flex flex-col justify-between">
                                        <span class="text-slate-400 text-[8px] uppercase font-bold">Ward Details</span>
                                        <span class="font-bold text-slate-800 mt-0.5 block">Ward No: {{ $ewsData->ward_no ?? '—' }}</span>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 p-1.5 rounded-lg flex flex-col justify-between">
                                        <span class="text-slate-400 text-[8px] uppercase font-bold">Survey Block</span>
                                        <span class="font-bold text-slate-800 mt-0.5 block truncate">{{ $ewsData->bt_name ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel -->
                        <div class="lg:col-span-4 space-y-3">
                            <div class="glass-widget p-3 rounded-xl space-y-2 bg-white">
                                <div class="flex items-center gap-2 border-b border-slate-100 pb-1">
                                    <i class="bi bi-camera-video-fill text-indigo-500 text-xs"></i>
                                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-wider">Survey Documents & Media Preview</span>
                                </div>
                                <div class="space-y-2.5">
                                    @if(!empty($ewsData->capture_photo_of_family_and_house))
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-center text-[9px] font-bold text-slate-500">
                                            <span><i class="bi bi-image text-indigo-500"></i> House & Family Photo</span>
                                            <a href="{{ $ewsData->capture_photo_of_family_and_house }}" target="_blank" class="text-indigo-600 hover:text-indigo-500 text-[8px] uppercase font-bold flex items-center gap-0.5">
                                                <span>Open Full</span> <i class="bi bi-box-arrow-up-right text-[7px]"></i>
                                            </a>
                                        </div>
                                        <div class="relative group overflow-hidden rounded-lg border border-slate-200 bg-slate-50 shadow-sm cursor-zoom-in" onclick="openPhotoModal('{{ $ewsData->capture_photo_of_family_and_house }}')">
                                            <img src="{{ $ewsData->capture_photo_of_family_and_house }}" class="w-full h-24 object-cover group-hover:scale-105 transition-transform duration-255" alt="EWS House Photo">
                                            <div class="absolute inset-0 bg-slate-950/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                <i class="bi bi-zoom-in text-lg"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($ewsData->capture_video_of_family_and_house))
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-center text-[9px] font-bold text-slate-500">
                                            <span><i class="bi bi-play-btn text-indigo-500"></i> Survey Video Clip</span>
                                            <a href="{{ $ewsData->capture_video_of_family_and_house }}" target="_blank" class="text-indigo-600 hover:text-indigo-500 text-[8px] uppercase font-bold flex items-center gap-0.5">
                                                <span>Open Full</span> <i class="bi bi-box-arrow-up-right text-[7px]"></i>
                                            </a>
                                        </div>
                                        <div class="rounded-lg overflow-hidden border border-slate-200 shadow-sm bg-slate-950">
                                            <video controls class="w-full h-24 object-cover focus:outline-none">
                                                <source src="{{ $ewsData->capture_video_of_family_and_house }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                    @endif

                                    @if(empty($ewsData->capture_photo_of_family_and_house) && empty($ewsData->capture_video_of_family_and_house))
                                    <div class="text-center py-4 text-slate-400 text-[10px] font-light">
                                        No digital survey photo or video attachments found.
                                    </div>
                                    @endif
                                </div>
                            </div>




                        </div>
                    </div>
                </div>

                <!-- 2. VERIFICATION & EXCLUSIONS SECTION -->
                <div id="section-rejections" class="space-y-3 hidden">
                    <!-- Heading -->
                    <div class="p-3.5 bg-gradient-to-r from-red-500/10 to-transparent border border-red-500/20 rounded-xl text-slate-900 flex justify-between items-center">
                        <div class="space-y-0.5">
                            <span class="text-[8px] font-black text-red-655 uppercase tracking-widest flex items-center gap-1">
                                <i class="bi bi-shield-fill-exclamation text-red-500"></i> Exclusion Matrix
                            </span>
                            <h3 class="text-sm font-black uppercase">System Exclusion & Rejections Checker</h3>
                            <p class="text-slate-500 text-[9px] font-light">Cross-references registered mobile against criteria databases (PPP, Property, House Ownership).</p>
                        </div>
                        <span class="text-[9px] font-bold text-red-600 bg-red-100 border border-red-200 px-2 py-0.5 rounded font-mono">DATABASE SYNCED</span>
                    </div>

                    <!-- Exclusions Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        
                        <!-- PPP Exclusion Card -->
                        <div class="glass-widget p-3.5 rounded-xl flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                    <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-wider">PPP Exclusion Check</span>
                                    @if($pppExclusion)
                                        <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[8px] font-bold rounded uppercase">FAILED</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-bold rounded uppercase">PASSED</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 leading-relaxed font-light mt-1">
                                    @if($pppExclusion)
                                        <strong class="text-red-600 font-bold block mb-1">Status: Rejected Application</strong>
                                        
                                        @if(!empty($ewsData->exclusion))
                                            <div class="mt-1.5 p-2 bg-red-50 border border-red-100 rounded-lg text-[10px] text-slate-700">
                                                <div class="font-extrabold text-red-700 uppercase text-[9px] mb-1 tracking-wider flex items-center gap-1">
                                                    <i class="bi bi-info-circle-fill"></i> Exclusion Trigger (अस्वीकृति कारण):
                                                </div>
                                                <div class="font-bold uppercase">
                                                    {{ $ewsData->exclusion }}
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <strong class="text-emerald-600 font-bold block mb-1">Status: Passed</strong>
                                        Congratulation! Your application satisfies PPP eligibility criteria. (बधाई हो! आपका आवेदन परिवार पहचान पत्र (PPP) के पात्रता मानदंडों को पूरा करता है।)
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(!$pppExclusion)
                        <!-- Property in India Card -->
                        <div class="glass-widget p-3.5 rounded-xl flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                    <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-wider">Property in India Check</span>
                                    @if($propertyReject)
                                        <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[8px] font-bold rounded uppercase">FAILED</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-bold rounded uppercase">PASSED</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 leading-relaxed font-light mt-1">
                                    @if($propertyReject)
                                        <strong class="text-red-600 font-bold block mb-1">Status: Rejected Application</strong>
                                        "Application rejected because citizen owns registered property or land across India." (enke nam ind me koi property h)
                                    @else
                                        <strong class="text-emerald-600 font-bold block mb-1">Status: Passed</strong>
                                        No registered property found across India. (भारत में आपके नाम पर कोई अन्य पंजीकृत संपत्ति नहीं पाई गई है।)
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!$pppExclusion && !$propertyReject)
                        <!-- Existing House Card -->
                        <div class="glass-widget p-3.5 rounded-xl flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                    <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-wider">House Ownership Check</span>
                                    @if($houseReject)
                                        <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[8px] font-bold rounded uppercase">FAILED</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-bold rounded uppercase">PASSED</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 leading-relaxed font-light mt-1">
                                    @if($houseReject)
                                        <strong class="text-red-600 font-bold block mb-1">Status: Rejected Application</strong>
                                        "Application rejected because citizen already owns a residential house." (enke nam koi phle se ghar h)
                                    @else
                                        <strong class="text-emerald-600 font-bold block mb-1">Status: Passed</strong>
                                        No registered residential house found. (आपके नाम पर कोई पहले से पंजीकृत पक्का मकान नहीं पाया गया है।)
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                <!-- 3. DRAW & BOOKINGS SECTION -->
                <div id="section-bookings" class="space-y-3 hidden">
                    <!-- Heading -->
                    <div class="p-3.5 bg-gradient-to-r from-indigo-500/10 to-transparent border border-indigo-500/20 rounded-xl text-slate-900 flex justify-between items-center">
                        <div class="space-y-0.5">
                            <span class="text-[8px] font-black text-indigo-655 uppercase tracking-widest flex items-center gap-1">
                                <i class="bi bi-calendar-check text-indigo-500"></i> Draw & Bookings Registry
                            </span>
                            <h3 class="text-sm font-black uppercase">Draw List Selection & Booking Audit</h3>
                            <p class="text-slate-500 text-[9px] font-light">Details about draw list inclusion, plot bookings status, and ADC level verification checks.</p>
                        </div>
                        <span class="text-[9px] font-bold text-indigo-600 bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded font-mono">SYNC STATUS: LIVE</span>
                    </div>

                    <!-- Selection workflow logs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        
                        <!-- Eligible in Draw List -->
                        <div class="glass-widget p-3.5 rounded-xl flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                    <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-wider">Draw list Eligibility</span>
                                    @if($eligibleDraw)
                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-bold rounded uppercase">ELIGIBLE</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 text-[8px] font-bold rounded uppercase">NOT ELIGIBLE</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 leading-relaxed font-light mt-1">
                                    @if($eligibleDraw)
                                        <strong class="text-emerald-600 font-bold block mb-1">Status: Draw List Confirmed</strong>
                                        Citizen is included in the EWS scheme draw list and qualified for booking phase selection.
                                    @else
                                        <strong class="text-slate-500 font-bold block mb-1">Status: Non-Eligible</strong>
                                        Citizen record is not found in the draw list selection parameters.
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Plot Booking Status -->
                        <div class="glass-widget p-3.5 rounded-xl flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                    <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-wider">Plot Booking Status</span>
                                    @if($booking)
                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-bold rounded uppercase">BOOKED</span>
                                    @elseif($eligibleDraw)
                                        <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[8px] font-bold rounded uppercase">UNBOOKED</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 text-[8px] font-bold rounded uppercase">NO BOOKING</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 leading-relaxed font-light mt-1">
                                    @if($booking)
                                        <strong class="text-emerald-600 font-bold block mb-1">Status: Booking Confirmed</strong>
                                        Citizen has registered plot booking selection. Form references, payment logs, and assets are cleared.
                                    @elseif($eligibleDraw)
                                        <strong class="text-red-655 font-bold block mb-1">Status: Rejected/Expired</strong>
                                        "Application rejected because citizen did not book the allotted plot within the deadline." (انہوں نے booking नहीं की)
                                    @else
                                        <strong class="text-slate-500 font-bold block mb-1">Status: No Record</strong>
                                        Ineligible for booking due to exclusion criteria failure.
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- ADC Level Verification Check -->
                        <div class="glass-widget p-3.5 rounded-xl flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                    <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-wider">ADC Level Clearance</span>
                                    @if($booking)
                                        @if($eligibleFinal)
                                            <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-bold rounded uppercase">PASSED</span>
                                        @else
                                            <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[8px] font-bold rounded uppercase">REJECTED</span>
                                        @endif
                                    @else
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 text-[8px] font-bold rounded uppercase">PENDING</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 leading-relaxed font-light mt-1">
                                    @if($booking)
                                        @if($eligibleFinal)
                                            <strong class="text-emerald-600 font-bold block mb-1">Status: ADC Verified</strong>
                                            Citizen has cleared the final physical verification and documentation assessment conducted by the ADC committee.
                                        @else
                                            <strong class="text-red-600 font-bold block mb-1">Status: Excluded by ADC</strong>
                                            "Application rejected at the final ADC level verification checkpoint." (ये ADC level पर बाहर हो गए)
                                        @endif
                                    @else
                                        <strong class="text-slate-450 font-bold block mb-1">Status: Awaiting Verification</strong>
                                        Final verification is pending due to incomplete booking logs.
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 4. ALLOTMENT STATUS SECTION -->
                <div id="section-allotment" class="space-y-3 hidden">
                    <!-- Heading -->
                    <div class="p-3.5 bg-gradient-to-r from-emerald-500/10 to-transparent border border-emerald-500/20 rounded-xl text-slate-900 flex justify-between items-center">
                        <div class="space-y-0.5">
                            <span class="text-[8px] font-black text-emerald-650 uppercase tracking-widest flex items-center gap-1">
                                <i class="bi bi-house-check text-emerald-500"></i> Allotment & Waiting Registry
                            </span>
                            <h3 class="text-sm font-black uppercase">Final Flat/Plot Allotment Status</h3>
                            <p class="text-slate-500 text-[9px] font-light">Official allotment lists and waiting queue registries details.</p>
                        </div>
                        <span class="text-[9px] font-bold text-emerald-600 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded font-mono">REFRESHED STATUS</span>
                    </div>

                    <!-- Details Matrix -->
                    <div class="grid grid-cols-1 {{ $allotted ? '' : 'md:grid-cols-2' }} gap-3">
                        
                        <!-- Final Allotment Card -->
                        <div class="glass-widget p-3.5 rounded-xl flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                    <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-wider">EWS Final Allotment</span>
                                    @if($allotted)
                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-bold rounded uppercase">ALLOTTED</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 text-[8px] font-bold rounded uppercase">NOT ALLOTTED</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 leading-relaxed font-light mt-1">
                                    @if($allotted)
                                        <strong class="text-emerald-600 font-bold block mb-1">Congratulations! Plot/Flat Allotted</strong>
                                        "Plot/Flat allotted successfully. Details of your allotted residential asset are verified and registered."
                                        <div class="mt-2 p-2 bg-emerald-50/50 border border-emerald-100 rounded-lg text-emerald-950 text-xs font-mono font-black space-y-0.5">
                                            <div>Asset Number: <span class="text-indigo-600">{{ $allotted->flat_no ?? 'FLAT-Not Specified' }}</span></div>
                                            <div>Allotted To: <span>{{ $allotted->full_name ?? '—' }}</span></div>
                                            <div>Ref File No: <span>{{ $allotted->application_number ?? '—' }}</span></div>
                                        </div>
                                    @else
                                        <strong class="text-slate-500 font-bold block mb-1">Status: Unallotted</strong>
                                        Citizen does not have any allotted properties in the current phase allotments.
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(!$allotted)
                        <!-- Waiting / Pending List Card -->
                        <div class="glass-widget p-3.5 rounded-xl flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                    <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-wider">Waiting / Queue List Status</span>
                                    @if($waiting)
                                        <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[8px] font-bold rounded uppercase animate-pulse">PENDING IN WAITING</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 text-[8px] font-bold rounded uppercase">NOT IN QUEUE</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 leading-relaxed font-light mt-1">
                                    @if($waiting)
                                        <strong class="text-amber-600 font-bold block mb-1">Status: Waiting List Active</strong>
                                        "Flat will be allotted, but currently your application is in the Pending/Waiting list queue." (makan milegi par abhi pending me h)
                                    @else
                                        <strong class="text-slate-500 font-bold block mb-1">Status: No Queue Record</strong>
                                        Citizen is not registered in the active EWS waiting list queue.
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </main>
        </div>

    </div>

    <!-- Script for Switching Tabs -->
    <script>
        function switchTab(tabId) {
            // Hide all sections
            document.getElementById('section-dashboard').classList.add('hidden');
            document.getElementById('section-rejections').classList.add('hidden');
            document.getElementById('section-bookings').classList.add('hidden');
            document.getElementById('section-allotment').classList.add('hidden');

            // Show selected section
            document.getElementById('section-' + tabId).classList.remove('hidden');

            // Reset all buttons style
            var buttons = ['dashboard', 'rejections', 'bookings', 'allotment'];
            buttons.forEach(function(b) {
                var btn = document.getElementById('nav-' + b);
                if (b === tabId) {
                    btn.className = "w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-white bg-gradient-to-r from-indigo-500 to-purple-600 text-[10px] font-extrabold uppercase tracking-wider shadow-sm transition-all text-left";
                    // Reset icon color to white inside active button
                    var icon = btn.querySelector('i');
                    if (icon) icon.className = icon.className.replace('text-slate-400', 'text-white');
                } else {
                    btn.className = "w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-900 text-[10px] font-bold uppercase tracking-wider transition-all text-left";
                    // Reset icon color to slate inside inactive button
                    var icon = btn.querySelector('i');
                    if (icon) icon.className = icon.className.replace('text-white', 'text-slate-400');
                }
            });
        }

        // LIGHTBOX MODAL FOR IMAGE PREVIEW
        function openPhotoModal(src) {
            var modal = document.getElementById('photoModal');
            var img = document.getElementById('modalImg');
            var dl = document.getElementById('modalDownload');
            img.src = src;
            dl.href = src;
            modal.classList.remove('hidden');
            setTimeout(function() {
                modal.classList.remove('opacity-0');
            }, 50);
        }
        
        function closePhotoModal(e) {
            if (e) e.stopPropagation();
            var modal = document.getElementById('photoModal');
            modal.classList.add('opacity-0');
            setTimeout(function() {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>

    <!-- Lightbox Modal Container -->
    <div id="photoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 hidden opacity-0 transition-opacity duration-300" onclick="closePhotoModal()">
        <button class="absolute top-4 right-4 text-white text-2xl hover:text-slate-300 transition-colors" onclick="closePhotoModal(event)">
            <i class="bi bi-x-circle-fill"></i>
        </button>
        <div class="max-w-[90vw] max-h-[90vh] overflow-hidden rounded-xl border border-white/20 shadow-2xl bg-[#0c0a10]" onclick="event.stopPropagation()">
            <img id="modalImg" src="" class="max-w-full max-h-[80vh] object-contain" alt="Enlarged survey photo">
            <div class="p-3 text-center text-white text-xs font-semibold bg-slate-900/95 flex justify-between items-center">
                <span>EWS Housing Survey Capture Photo</span>
                <a id="modalDownload" href="" download class="px-3 py-1 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-bold transition-all text-[10px]">Download File</a>
            </div>
        </div>
    </div>

</body>
</html>
