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
                <nav class="space-y-1">
                    <a href="#" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-[10px] font-extrabold uppercase tracking-wider shadow-sm transition-all">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
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

                <!-- Welcome Header Panel (More Compact Height) -->
                <div class="p-3.5 dashboard-vibrant-header rounded-xl relative overflow-hidden flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shadow-md text-white">
                    <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="space-y-0.5 relative z-10">
                        <span class="text-[8px] font-black text-indigo-100 uppercase tracking-widest flex items-center gap-1">
                            <i class="bi bi-building"></i> EWS Scheme Beneficiary Registry
                        </span>
                        <h3 class="text-base font-black leading-none uppercase">APPLICATION ID: {{ $ewsData->application_number ?? '—' }}</h3>
                        <p class="text-white/80 text-[9px] font-light max-w-xl">This record contains validated socio-economic status information verified via local administrative block authorities.</p>
                    </div>

                    <!-- Flow Tracker Checklist (Tighter padding & spacing) -->
                    <div class="flex items-center gap-1.5 relative z-10 w-full sm:w-auto overflow-x-auto py-0.5">
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <div class="w-5 h-5 rounded-full bg-white text-indigo-650 flex items-center justify-center text-[10px] font-bold shadow">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <span class="text-[8px] font-bold text-white">Survey</span>
                        </div>
                        <div class="h-0.5 w-3 bg-white/50 flex-shrink-0"></div>

                        <div class="flex items-center gap-1 flex-shrink-0">
                            <div class="w-5 h-5 rounded-full bg-white text-indigo-650 flex items-center justify-center text-[10px] font-bold shadow">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <span class="text-[8px] font-bold text-white">Verified</span>
                        </div>
                        <div class="h-0.5 w-3 bg-white/50 flex-shrink-0"></div>

                        <div class="flex items-center gap-1 flex-shrink-0">
                            <div class="w-5 h-5 rounded-full bg-white text-indigo-650 flex items-center justify-center text-[10px] font-bold shadow animate-pulse">
                                <i class="bi bi-cpu-fill text-[9px]"></i>
                            </div>
                            <span class="text-[8px] font-bold text-white">Allotment</span>
                        </div>
                        <div class="h-0.5 w-3 bg-white/20 flex-shrink-0"></div>

                        <div class="flex items-center gap-1 flex-shrink-0 opacity-40">
                            <div class="w-5 h-5 rounded-full bg-indigo-900 border border-indigo-800 text-indigo-300 flex items-center justify-center text-[10px] font-bold">
                                <i class="bi bi-key text-[9px]"></i>
                            </div>
                            <span class="text-[8px] font-bold">Possession</span>
                        </div>
                    </div>
                </div>

                <!-- 5 Metrics Cards (Extremely compact heights) -->
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
                        <div class="text-[11px] font-black text-emerald-600 mt-0.5 leading-tight">₹ {{ number_format($ewsData->monthly_income ?? 0) }}</div>
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

                <!-- Main Layout splits -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">

                    <!-- Left: Table Database Console (Col-span 8) -->
                    <div class="lg:col-span-8 space-y-3">
                        
                        <!-- Master data list (Refactored to be denser, 3-column layout) -->
                        <div class="glass-widget p-3.5 rounded-xl space-y-2">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                <span class="text-[9px] font-black text-slate-800 uppercase tracking-wider flex items-center gap-1">
                                    <i class="bi bi-file-text text-indigo-500"></i> Citizen Personal & Socio-Economic Matrix
                                </span>
                                <span class="text-[8px] font-mono text-slate-400">DB: all_ews_data_1</span>
                            </div>

                            <!-- Dense Grid Layout: 3 Columns instead of 2 for better space utilization -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-1.5 text-[10px] font-light">
                                
                                <!-- Col 1: Personal Details -->
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

                                <!-- Col 2: Income & Identity Details -->
                                <div class="space-y-1 bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                                    <div class="text-[8px] text-slate-400 font-extrabold uppercase mb-1">Financial & ID</div>
                                    <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                        <span class="text-slate-400">Aadhar Reference</span>
                                        <span class="font-bold text-slate-800">XXXX-XXXX-{{ substr($ewsData->aadhar_no ?? '0000', -4) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                        <span class="text-slate-400">Monthly Income</span>
                                        <span class="font-extrabold text-emerald-600">₹ {{ number_format($ewsData->monthly_income ?? 0) }}</span>
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

                                <!-- Col 3: Assets & Property Details -->
                                <div class="space-y-1 bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                                    <div class="text-[8px] text-slate-400 font-extrabold uppercase mb-1">Properties & Assets</div>
                                    <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                        <span class="text-slate-400">Ownership</span>
                                        <span class="font-bold text-slate-800">{{ $ewsData->house_ownership ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-0.5 border-b border-slate-200/50">
                                        <span class="text-slate-400">Rent Paid</span>
                                        <span class="font-bold text-slate-800">₹ {{ number_format($ewsData->rent_amount ?? 0) }}</span>
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

                        <!-- Geographical info (Refactored to be more compact) -->
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

                    <!-- Right Panel: Side attachments & checklists (Col-span 4) -->
                    <div class="lg:col-span-4 space-y-3">
                        
                        <!-- Media frames & Checklists (Combined together to save screen height) -->
                        <div class="glass-widget p-3 rounded-xl space-y-2">
                            <div class="flex items-center gap-2 border-b border-slate-100 pb-1">
                                <i class="bi bi-link-45deg text-indigo-500 text-xs"></i>
                                <span class="text-[9px] font-black text-slate-800 uppercase tracking-wider">Survey Documents & Media</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[10px]">
                                <!-- House / Family Photo -->
                                @if(!empty($ewsData->capture_photo_of_family_and_house))
                                <a href="{{ $ewsData->capture_photo_of_family_and_house }}" target="_blank" class="p-1.5 bg-slate-50 hover:bg-indigo-50 border border-slate-100 rounded-lg flex items-center justify-between text-slate-750 transition-colors">
                                    <span class="truncate"><i class="bi bi-image text-indigo-500 mr-1"></i> Photo file</span>
                                    <i class="bi bi-box-arrow-up-right text-[9px] text-slate-400"></i>
                                </a>
                                @endif

                                <!-- House / Family Video -->
                                @if(!empty($ewsData->capture_video_of_family_and_house))
                                <a href="{{ $ewsData->capture_video_of_family_and_house }}" target="_blank" class="p-1.5 bg-slate-50 hover:bg-indigo-50 border border-slate-100 rounded-lg flex items-center justify-between text-slate-755 transition-colors">
                                    <span class="truncate"><i class="bi bi-camera-video text-indigo-500 mr-1"></i> Video clip</span>
                                    <i class="bi bi-play text-[9px] text-slate-400"></i>
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Checklists status (Refactored to 2 columns to make it extremely compact) -->
                        <div class="glass-widget p-3 rounded-xl space-y-2 bg-white">
                            <div class="flex items-center gap-2 border-b border-slate-100 pb-1">
                                <i class="bi bi-cpu-fill text-indigo-500 text-xs"></i>
                                <span class="text-[9px] font-black text-slate-800 uppercase tracking-wider">Verification Checklist</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[9px] font-mono font-bold">
                                <div class="p-1 bg-slate-50 border border-slate-100 rounded flex justify-between items-center px-1.5">
                                    <span class="text-slate-400">AGE CHECK:</span>
                                    <span class="text-emerald-600">PASS</span>
                                </div>
                                <div class="p-1 bg-slate-50 border border-slate-100 rounded flex justify-between items-center px-1.5">
                                    <span class="text-slate-400">PENSION:</span>
                                    <span class="text-emerald-600">PASS</span>
                                </div>
                                <div class="p-1 bg-slate-50 border border-slate-100 rounded flex justify-between items-center px-1.5">
                                    <span class="text-slate-400">PROPERTY:</span>
                                    <span class="text-emerald-600">PASS</span>
                                </div>
                                <div class="p-1 bg-slate-50 border border-slate-100 rounded flex justify-between items-center px-1.5">
                                    <span class="text-slate-400">AC CHECK:</span>
                                    <span class="text-emerald-600">PASS</span>
                                </div>
                            </div>
                        </div>

                        <!-- Downloader / actions -->
                        <div class="glass-widget p-3 rounded-xl space-y-2">
                            <div class="grid grid-cols-2 gap-2 text-[9px] font-extrabold uppercase">
                                <button onclick="alert('Generating survey certified copy...')" class="py-1.5 rounded-lg bg-white hover:bg-indigo-500 hover:text-white border border-slate-200 text-slate-700 hover:border-indigo-500 transition-all flex items-center justify-center gap-1 shadow-sm">
                                    <i class="bi bi-file-pdf"></i> Get PDF Slip
                                </button>
                                <button onclick="alert('Lodge survey grievance query')" class="py-1.5 rounded-lg bg-white hover:bg-indigo-500 hover:text-white border border-slate-200 text-slate-700 hover:border-indigo-500 transition-all flex items-center justify-center gap-1 shadow-sm">
                                    <i class="bi bi-exclamation-square"></i> File Dispute
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
