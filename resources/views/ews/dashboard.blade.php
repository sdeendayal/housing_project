<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Citizen Portal - Control Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            box-shadow: 0 4px 20px rgba(148, 163, 184, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-widget:hover {
            border-color: rgba(99, 102, 241, 0.25);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
            transform: translateY(-1px);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
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
<body class="h-full flex overflow-hidden text-slate-700 relative">

    <!-- Ambient Glow Blobs in Background -->
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-indigo-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-pink-500/5 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Page Wrapper -->
    <div class="w-full h-full flex flex-col md:flex-row overflow-hidden relative z-10">

        <!-- Sidebar Left Console Menu -->
        <aside class="w-full md:w-56 bg-white border-r border-slate-200/80 flex flex-col justify-between p-4 flex-shrink-0 shadow-sm">
            <div class="space-y-5">
                <!-- Branding Header -->
                <div class="flex items-center gap-2.5 px-1 py-1">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center shadow-md shadow-indigo-500/20">
                        <i class="bi bi-houses text-sm text-white"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-black tracking-wider text-indigo-600">EWS CITIZEN</div>
                        <div class="text-[8px] text-slate-400 tracking-widest font-bold uppercase">Control Panel</div>
                    </div>
                </div>

                <div class="border-t border-slate-100 my-2"></div>

                <!-- Navigation List -->
                <nav class="space-y-1">
                    <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-[10px] font-extrabold uppercase tracking-wider shadow-md shadow-indigo-500/10 transition-all">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Console Dashboard</span>
                    </a>
                    
                    <a href="#" onclick="alert('Physical possession check is pending draw list declaration.')" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 text-[10px] font-bold uppercase tracking-wider transition-all">
                        <i class="bi bi-patch-check-fill text-slate-400"></i>
                        <span>Possession Clearance</span>
                    </a>

                    <a href="#" onclick="alert('Verification check logs: Connected & Synced.')" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 text-[10px] font-bold uppercase tracking-wider transition-all">
                        <i class="bi bi-hdd-network-fill text-slate-400"></i>
                        <span>DB Source logs</span>
                    </a>
                </nav>
            </div>

            <!-- Profile Info Card -->
            <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl flex flex-col gap-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-650 font-black uppercase text-xs">
                        {{ substr($ewsData->full_name ?? 'U', 0, 2) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[10px] font-bold text-slate-900 truncate">{{ $ewsData->full_name ?? 'Citizen User' }}</div>
                        <div class="text-[8px] text-slate-500 truncate">+91 {{ $ewsData->mobile_number ?? '0000000000' }}</div>
                    </div>
                </div>
                <a href="{{ route('ews.logout') }}" class="w-full py-1.5 rounded-lg bg-white hover:bg-red-50 hover:text-red-650 border border-slate-200 hover:border-red-200 text-slate-600 font-extrabold text-[9px] transition-colors flex items-center justify-center gap-1 uppercase tracking-wider shadow-sm">
                    <i class="bi bi-box-arrow-left"></i> Logout Account
                </a>
            </div>
        </aside>

        <!-- Main Body Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50/50 overflow-hidden">
            
            <!-- Top bar -->
            <header class="h-12 border-b border-slate-200/80 bg-white flex items-center justify-between px-6 flex-shrink-0 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Active System File ID:</span>
                    <span class="text-[9px] font-black text-indigo-600 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded uppercase font-mono">{{ $ewsData->application_number ?? '—' }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-3 py-1 rounded-xl text-[9px] font-extrabold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-emerald-600 tracking-wider">SECURE PORTAL</span>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content Frame -->
            <main class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">

                <!-- Welcome Header Panel (Modern Vibrant Gradient Theme) -->
                <div class="p-5 dashboard-vibrant-header rounded-2xl relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-lg text-white">
                    <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="space-y-1 relative z-10">
                        <span class="text-[9px] font-black text-indigo-200 uppercase tracking-widest flex items-center gap-1">
                            <i class="bi bi-building"></i> EWS Scheme Beneficiary Registry
                        </span>
                        <h3 class="text-xl font-black leading-none uppercase">APPLICATION ID: {{ $ewsData->application_number ?? '—' }}</h3>
                        <p class="text-white/80 text-[10px] font-light max-w-xl">This record contains validated socio-economic status information verified via local administrative block authorities.</p>
                    </div>

                    <!-- Flow Tracker Checklist -->
                    <div class="flex items-center gap-2 relative z-10 w-full md:w-auto overflow-x-auto py-1">
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-white text-indigo-650 flex items-center justify-center text-xs font-bold shadow-md shadow-white/20">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <span class="text-[9px] font-bold text-white">Survey</span>
                        </div>
                        <div class="h-0.5 w-4 bg-white/50 flex-shrink-0"></div>

                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-white text-indigo-650 flex items-center justify-center text-xs font-bold shadow-md shadow-white/20">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <span class="text-[9px] font-bold text-white">Verified</span>
                        </div>
                        <div class="h-0.5 w-4 bg-white/50 flex-shrink-0"></div>

                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <div class="w-6 h-6 rounded-full bg-white text-indigo-650 flex items-center justify-center text-xs font-bold shadow-md shadow-white/20 animate-pulse">
                                <i class="bi bi-cpu-fill"></i>
                            </div>
                            <span class="text-[9px] font-bold text-white animate-pulse">Allotment</span>
                        </div>
                        <div class="h-0.5 w-4 bg-white/20 flex-shrink-0"></div>

                        <div class="flex items-center gap-1.5 flex-shrink-0 opacity-40">
                            <div class="w-6 h-6 rounded-full bg-indigo-900 border border-indigo-800 text-indigo-300 flex items-center justify-center text-xs font-bold">
                                <i class="bi bi-key"></i>
                            </div>
                            <span class="text-[9px] font-bold">Possession</span>
                        </div>
                    </div>
                </div>

                <!-- 5 Metrics Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    
                    <div class="glass-widget p-2.5 rounded-xl">
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider">File Status</span>
                        <div class="text-xs font-black text-indigo-600 mt-1 uppercase">{{ $ewsData->status ?? 'Verified' }}</div>
                    </div>

                    <div class="glass-widget p-2.5 rounded-xl">
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider">Income Bracket</span>
                        <div class="text-xs font-black text-slate-900 mt-1">{{ $ewsData->IncomeVerified ?? '—' }}</div>
                    </div>

                    <div class="glass-widget p-2.5 rounded-xl">
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider">Monthly Income</span>
                        <div class="text-xs font-black text-emerald-600 mt-1">₹ {{ number_format($ewsData->monthly_income ?? 0) }}</div>
                    </div>

                    <div class="glass-widget p-2.5 rounded-xl">
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider">Caste Group</span>
                        <div class="text-xs font-black text-slate-900 mt-1">{{ $ewsData->caste ?? 'General' }}</div>
                    </div>

                    <div class="glass-widget p-2.5 rounded-xl col-span-2 sm:col-span-1">
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider">Admin Block</span>
                        <div class="text-xs font-black text-slate-700 mt-1 truncate">{{ $ewsData->bt_name ?? '—' }}</div>
                    </div>

                </div>

                <!-- Main Layout splits -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                    <!-- Left: Table Database Console (Col-span 8) -->
                    <div class="lg:col-span-8 space-y-4">
                        
                        <!-- Master data list -->
                        <div class="glass-widget p-4 rounded-2xl space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest flex items-center gap-1.5">
                                    <i class="bi bi-file-text text-indigo-500"></i> Citizen Personal & Socio-Economic Matrix
                                </span>
                                <span class="text-[8px] font-mono text-slate-400">DB: all_ews_data_1</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1.5 text-[11px] font-light">
                                <div class="space-y-1.5">
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Full Name</span>
                                        <span class="font-extrabold text-slate-800">{{ $ewsData->full_name ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Father's Name</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->fathers_full_name ?? ($ewsData->father_name ?? '—') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Age / Gender</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->age ?? '—' }} yrs / {{ $ewsData->gender ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Date of Birth</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->date_of_birth ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Aadhar Reference</span>
                                        <span class="font-bold text-slate-800">XXXX-XXXX-{{ substr($ewsData->aadhar_no ?? '0000', -4) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Marital Status</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->MaritalStatus ?? ($ewsData->do_you_have_spouce ?? '—') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1">
                                        <span class="text-slate-400">Caste Category</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->caste ?? '—' }}</span>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Monthly Income</span>
                                        <span class="font-bold text-emerald-600">₹ {{ number_format($ewsData->monthly_income ?? 0) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Electricity A/c</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->electricity_bill_account_no ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">House Ownership</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->house_ownership ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Rent Paid</span>
                                        <span class="font-bold text-slate-800">₹ {{ number_format($ewsData->rent_amount ?? 0) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Vehicle Type</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->vehicle_ownership ?? '—' }} ({{ $ewsData->type_of_vehicle ?? '—' }})</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-400">Vehicle Reg Number</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->vehicle_registration_number ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1">
                                        <span class="text-slate-400">Property across India?</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->do_you_own_any_property_or_house_across_india ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Geographical info -->
                        <div class="glass-widget p-4 rounded-2xl space-y-3 bg-white">
                            <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                                <i class="bi bi-geo-alt-fill text-indigo-500 text-xs"></i>
                                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest">Geographical & Surveyor Location Coordinates</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-[11px] font-light">
                                <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xl">
                                    <span class="text-slate-400 block text-[9px] uppercase font-bold">House Coordinates</span>
                                    <div class="flex items-center justify-between font-bold text-slate-800 mt-1">
                                        <span>{{ $ewsData->coordinates_of_current_house_address ?? '—' }}</span>
                                        <a href="https://maps.google.com/?q={{ urlencode($ewsData->coordinates_of_current_house_address ?? '') }}" target="_blank" class="text-indigo-600 hover:text-indigo-500">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xl">
                                    <span class="text-slate-400 block text-[9px] uppercase font-bold">Survey Ward Number</span>
                                    <span class="font-bold text-slate-800 mt-1 block">Ward No: {{ $ewsData->ward_no ?? '—' }}</span>
                                </div>
                                <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xl">
                                    <span class="text-slate-400 block text-[9px] uppercase font-bold">Survey Block Area</span>
                                    <span class="font-bold text-slate-800 mt-1 block truncate">{{ $ewsData->bt_name ?? '—' }}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Panel: Side attachments & checklists (Col-span 4) -->
                    <div class="lg:col-span-4 space-y-4">
                        
                        <!-- Media frames -->
                        <div class="glass-widget p-4 rounded-2xl space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                                <i class="bi bi-camera-video-fill text-indigo-500 text-xs"></i>
                                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest">Survey Media Records</span>
                            </div>

                            <div class="space-y-2">
                                @if(!empty($ewsData->capture_photo_of_family_and_house))
                                <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between text-xs">
                                    <span class="text-[10px] text-slate-500"><i class="bi bi-image text-indigo-400 mr-1"></i> House Photo file</span>
                                    <a href="{{ $ewsData->capture_photo_of_family_and_house }}" target="_blank" class="bg-white hover:bg-indigo-500 text-slate-700 hover:text-white font-extrabold text-[9px] px-2 py-1 rounded border border-slate-200 uppercase transition-colors shadow-sm">
                                        Open Image
                                    </a>
                                </div>
                                @endif

                                @if(!empty($ewsData->capture_video_of_family_and_house))
                                <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between text-xs">
                                    <span class="text-[10px] text-slate-500"><i class="bi bi-camera-video text-indigo-400 mr-1"></i> Survey Video clip</span>
                                    <a href="{{ $ewsData->capture_video_of_family_and_house }}" target="_blank" class="bg-white hover:bg-indigo-500 text-slate-700 hover:text-white font-extrabold text-[9px] px-2 py-1 rounded border border-slate-200 uppercase transition-colors shadow-sm">
                                        Play Video
                                    </a>
                                </div>
                                @endif

                                @if(empty($ewsData->capture_photo_of_family_and_house) && empty($ewsData->capture_video_of_family_and_house))
                                <div class="p-3 bg-slate-50 border border-slate-100 text-center rounded-xl text-slate-400 text-[10px]">
                                    No digital survey media files attached.
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Checklists status -->
                        <div class="glass-widget p-4 rounded-2xl space-y-3 bg-white">
                            <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                                <i class="bi bi-cpu-fill text-indigo-500 text-xs"></i>
                                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest">Automatic Verification Checklists</span>
                            </div>

                            <div class="space-y-2 text-[10px] font-mono font-bold">
                                <div class="flex items-center justify-between p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                    <span class="text-slate-400">AGE RANGE CHECK:</span>
                                    <span class="text-emerald-600 font-extrabold">PASSED</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                    <span class="text-slate-400">PENSION MATCH CHECK:</span>
                                    <span class="text-emerald-600 font-extrabold">PASSED</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                    <span class="text-slate-400">PROPERTY LIMIT CHECK:</span>
                                    <span class="text-emerald-600 font-extrabold">PASSED</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                    <span class="text-slate-400">AC EXCLUSION CHECK:</span>
                                    <span class="text-emerald-600 font-extrabold">PASSED</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                    <span class="text-slate-400">VEHICLE CLASS CHECK:</span>
                                    <span class="text-emerald-600 font-extrabold">PASSED</span>
                                </div>
                            </div>
                        </div>

                        <!-- Downloader / actions -->
                        <div class="glass-widget p-4 rounded-2xl space-y-2.5">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Portal Quick Actions</span>
                            <div class="grid grid-cols-2 gap-2 text-[10px] font-extrabold uppercase">
                                <button onclick="alert('Generating survey certified copy...')" class="py-2.5 rounded-xl bg-white hover:bg-indigo-500 hover:text-white border border-slate-200 text-slate-700 hover:border-indigo-500 transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                    <i class="bi bi-file-pdf text-xs"></i> Download Copy
                                </button>
                                <button onclick="alert('Lodge survey grievance query')" class="py-2.5 rounded-xl bg-white hover:bg-indigo-500 hover:text-white border border-slate-200 text-slate-700 hover:border-indigo-500 transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                    <i class="bi bi-exclamation-square text-xs"></i> File Dispute
                                </button>
                            </div>
                        </div>

                    </div>

                </div>

            </main>
        </div>

    </div>

</body>
</html>
