<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer - Register Flat</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        .dev-shadow {
            box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08);
        }
        /* Custom Select2 Tailwind Styling */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            background-color: #f8fafc !important; /* bg-slate-50 */
            border: 1px solid #cbd5e1 !important; /* border-slate-250 */
            border-radius: 0.5rem !important; /* rounded-lg */
            height: 38px !important;
            padding: 5px 12px !important;
            display: flex;
            align-items: center;
            outline: none !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important; /* text-slate-800 */
            font-size: 0.75rem !important; /* text-xs */
            font-weight: 700 !important;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3b82f6 !important; /* focus:border-sky-500 */
            box-shadow: 0 0 0 1px #3b82f6 !important;
        }
        .select2-dropdown {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            overflow: hidden;
            z-index: 9999;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6 !important; /* bg-sky-500 */
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.375rem !important;
            padding: 6px 10px !important;
            font-size: 0.75rem !important;
            outline: none !important;
        }

        /* Premium Dependent Dropdown Shimmer & Loader Styling */
        @keyframes shimmerGlow {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .dropdown-loader-overlay {
            background: linear-gradient(135deg, rgba(255,255,255,0.97) 0%, rgba(240,249,255,0.94) 100%);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            box-shadow: 0 4px 15px -3px rgba(14, 165, 233, 0.25), 0 0 0 1px rgba(56, 189, 248, 0.5);
        }
        .dropdown-loader-shimmer {
            background: linear-gradient(90deg, rgba(56,189,248,0) 0%, rgba(56,189,248,0.95) 50%, rgba(99,102,241,0) 100%);
            background-size: 200% 100%;
            animation: shimmerGlow 1.5s infinite linear;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    @include('ews.developer.partials.sidebar')

    <!-- MAIN CONTAINER -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.developer.dashboard') }}" class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 hover:bg-sky-600 hover:text-white transition-all shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase">Register EWS Flat Details</h2>
                    <p class="text-[8px] text-slate-400 font-mono uppercase">Private Builder Flat Entry Form</p>
                </div>
            </div>

            <div class="text-right">
                <div class="text-[10px] text-slate-650 font-bold flex items-center gap-1 justify-end">
                    <span>{{ $user->name }}</span>
                    @if(!empty($displayZoneName))
                        <span class="text-[9px] bg-sky-100 text-sky-800 font-extrabold uppercase px-1.5 py-0.5 rounded border border-sky-200">({{ $displayZoneName }})</span>
                    @endif
                </div>
                <div class="text-[8.5px] text-slate-500 font-mono">Zone: <span class="font-bold text-slate-700 uppercase">{{ $displayZoneName ?? 'N/A' }}</span> | Mobile: {{ $user->mobile }}</div>
            </div>
        </header>

        <!-- Form Workspace -->
        <div class="flex-1 overflow-y-auto p-6">
            
            <div class="w-full max-w-4xl mx-auto">
                
                <!-- Form Card -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm dev-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-150 bg-slate-50/50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                                    <i class="bi bi-plus-circle text-sky-500 text-sm"></i>
                                    Flat Registry Form
                                </h3>
                                <span id="mode-status-badge" class="text-[8.5px] font-black uppercase px-2 py-0.5 rounded {{ old('bulk_mode') == '1' ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-sky-100 text-sky-800 border border-sky-200' }}">
                                    {{ old('bulk_mode') == '1' ? 'Bulk Generation Mode' : 'Single Flat Mode' }}
                                </span>
                            </div>
                            <p class="text-[8px] text-slate-400 font-mono mt-0.5 uppercase" id="mode-status-desc">
                                {{ old('bulk_mode') == '1' ? 'Allotment record builder inputs (Bulk Generation of Multiple Flats)' : 'Allotment record builder inputs (Single Flat Entry)' }}
                            </p>
                        </div>
                        
                        <div class="inline-flex p-0.5 bg-slate-200/80 rounded-lg text-[9px] font-black uppercase tracking-wider">
                            <button type="button" id="tab-single" onclick="switchMode('single')"
                                class="px-3 py-1 {{ old('bulk_mode') == '1' ? 'text-slate-600 hover:text-slate-800' : 'bg-white text-sky-700 shadow-sm' }} rounded-md transition-all font-black cursor-pointer">
                                <i class="bi bi-file-earmark-plus me-0.5"></i> Single Flat
                            </button>
                            <button type="button" id="tab-bulk" onclick="switchMode('bulk')"
                                class="px-3 py-1 {{ old('bulk_mode') == '1' ? 'bg-white text-sky-700 shadow-sm' : 'text-slate-600 hover:text-slate-800' }} rounded-md transition-all font-black cursor-pointer">
                                <i class="bi bi-grid-3x3-gap-fill me-0.5"></i> Bulk Generation
                            </button>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <form method="POST" action="{{ route('ews.developer.flats.store') }}" enctype="multipart/form-data" class="p-6 space-y-4" id="devCreateForm">
                        @csrf

                        <!-- Row 1: Register Flat & Zone -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Step 1: Register Flat Input Field -->
                            <div class="space-y-1">
                                <label for="register_flat" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-emerald-100 text-emerald-800 me-1.5">Step 1</span>
                                    <span>Register Flat</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="register_flat" name="register_flat" value="{{ old('register_flat') }}" placeholder="Enter flat name / registry title" required autocomplete="off"
                                        class="w-full bg-white border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-pencil-square"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Step 2: Zone Dropdown -->
                            <div class="space-y-1">
                                <label for="zone_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 2</span>
                                    <span>Select Zone</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <select id="zone_id" name="zone_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled {{ old('zone_id') ? '' : 'selected' }}>{{ old('register_flat') ? 'Choose Zone...' : '🔒 Step 1: Enter Register Flat First...' }}</option>
                                    @if(isset($zones))
                                        @foreach($zones as $z)
                                            <option value="{{ $z->id }}" {{ old('zone_id') == $z->id ? 'selected' : '' }}>
                                                {{ strtoupper($z->name) }}{{ !str_contains(strtoupper($z->name), 'ZONE') ? ' ZONE' : '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="hidden" name="zone_name" id="zone_name" value="{{ $displayZoneName }}">
                            </div>
                        </div>

                        <!-- Row 2: District & Town -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Step 3: District Dropdown -->
                            <div class="space-y-1">
                                <label for="district_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 3</span>
                                    <span>Select District</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative dropdown-select-wrapper">
                                    <select id="district_id" name="district_id" required disabled
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                        <option value="" disabled selected>🔒 Step 2: Select Zone First...</option>
                                        @if(old('zone_id') && isset($districts))
                                            @foreach($districts as $district)
                                                @if($district->zone_id == old('zone_id'))
                                                    <option value="{{ $district->id }}" {{ (old('district_id') == $district->id) ? 'selected' : '' }}>
                                                        {{ strtoupper($district->name) }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Step 4: Town Dropdown -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label for="town_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 4</span>
                                        <span>Name of Town</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <button type="button" onclick="openAddTownModal()" class="inline-flex items-center gap-1 text-[9.5px] font-bold text-sky-600 hover:text-sky-800 bg-sky-50 hover:bg-sky-100 border border-sky-200 hover:border-sky-300 px-2 py-0.5 rounded shadow-2xs transition-all cursor-pointer">
                                        <i class="bi bi-plus-circle-fill text-[10px]"></i>
                                        <span>Add Town</span>
                                    </button>
                                </div>
                                <div class="relative dropdown-select-wrapper">
                                    <select id="town_id" name="town_id" required
                                        class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                        <option value="" disabled selected>Choose a town...</option>
                                        @foreach($towns as $town)
                                            <option value="{{ $town->id }}" {{ old('town_id') == $town->id ? 'selected' : '' }}>
                                                {{ strtoupper($town->name) }}{{ !empty($town->type) ? ' (' . strtoupper($town->type) . ')' : '' }}
                                            </option>
                                        @endforeach
                                        <option value="new">+ Add New Town</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Developer Name & Name of Project -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Step 5: Developer Name (NOT prefilled from Auth!) -->
                            <div class="space-y-1">
                                <label for="developer_name" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 5</span>
                                    <span>Developer Name</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="developer_name" name="developer_name" value="{{ old('developer_name') }}" autocomplete="off" placeholder="🔒 Step 4: Select Town First..." required
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-building"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Step 6: Project Selection with prominent "+ Add Project" (Identical to STP) -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label for="project_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 6</span>
                                        <span>Name of Project</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <button type="button" id="btn_open_add_project" onclick="toggleNewProjectInline()" 
                                        class="inline-flex items-center gap-1.5 text-[10px] font-black text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 hover:border-emerald-400 px-2.5 py-0.5 rounded-md shadow-xs transition-all cursor-pointer opacity-50 pointer-events-none">
                                        <i class="bi bi-plus-circle-fill text-emerald-600"></i>
                                        <span>Add Project</span>
                                    </button>
                                </div>
                                <div class="relative dropdown-select-wrapper">
                                    <select id="project_id" name="project_id" required disabled
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                        <option value="" disabled selected>🔒 Step 5: Enter Developer Name First...</option>
                                        @if(isset($projects))
                                            @foreach($projects as $proj)
                                                <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}
                                                    data-developer="{{ $proj->developer_name ?? '' }}"
                                                    data-sector="{{ $proj->sector_no ?? '' }}" 
                                                    data-address="{{ $proj->address ?? '' }}"
                                                    data-license="{{ $proj->license_no ?? '' }}" 
                                                    data-area="{{ $proj->area_of_project ?? '' }}"
                                                    data-approved="{{ $proj->approved_building_plan ?? '' }}"
                                                    data-approval-date="{{ $proj->building_plan_approval_date ?? '' }}"
                                                    data-oc-yn="{{ $proj->oc_status_yn ?? '' }}">
                                                    {{ strtoupper($proj->name) }}{{ !empty($proj->project_abbr) ? ' [' . $proj->project_abbr . ']' : '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                        <option value="new">+ Add New Project</option>
                                    </select>
                                </div>

                                <!-- New Project Input (Identical to STP Login) -->
                                <div id="new_project_container" class="hidden mt-2">
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="new_project_name" name="new_project_name" placeholder="Enter new project name (e.g. Signature Global Park 4)"
                                            class="w-full bg-white border border-sky-400 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                        <button type="button" id="btn_save_project_ajax" onclick="saveNewProjectAjax()"
                                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider whitespace-nowrap flex items-center gap-1 shadow-sm transition-all shrink-0 cursor-pointer">
                                            <i class="bi bi-plus-circle-fill"></i>
                                            <span>Save Project</span>
                                        </button>
                                    </div>
                                    <div id="project_similarity_alert" class="hidden text-[10.5px] font-bold text-amber-800 bg-amber-50 border border-amber-300 rounded-lg p-2.5 mt-2 flex items-start gap-2 shadow-xs transition-all">
                                        <i class="bi bi-exclamation-triangle-fill text-amber-600 text-sm mt-0.5 shrink-0"></i>
                                        <div>
                                            <span id="project_similarity_msg"></span>
                                        </div>
                                    </div>
                                    <p class="text-[8.5px] text-slate-400 mt-1 italic">Click 'Save Project' to instantly save into ews_projects, or auto-saves on form submit.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4: Address of Project & Sector No. (Separate Independent Fields) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Step 7: Address of Project (Textarea) -->
                            <div class="space-y-1">
                                <label for="address_of_project" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 7</span>
                                    <span>Address of Project</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <textarea id="address_of_project" name="address_of_project" rows="2" placeholder="🔒 Step 6: Select Project First..." required
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none resize-none transition-all">{{ old('address_of_project') }}</textarea>
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-signpost-2-fill text-emerald-500"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Step 8: Sector No. -->
                            <div class="space-y-1">
                                <label for="sector_no" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 8</span>
                                    <span>Sector No.</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="sector_no" name="sector_no" value="{{ old('sector_no') }}" placeholder="🔒 Step 7: Enter Address First..." required autocomplete="off"
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-geo-alt-fill text-sky-500"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Row 5: License No., Area of Project & Developer Plan (PDF 500 KB) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3.5 bg-slate-50/90 border border-slate-200 rounded-xl">
                            <!-- Step 9: License No. -->
                            <div class="space-y-1">
                                <label for="license_no" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 9</span>
                                    <span>License No.</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="license_no" name="license_no" value="{{ old('license_no') }}" placeholder="🔒 Step 8: Enter Sector No. First..." required autocomplete="off"
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-card-checklist text-sky-600"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Area of Project -->
                            <div class="space-y-1">
                                <label for="area_of_project" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                    <span>Area of Project</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="area_of_project" name="area_of_project" value="{{ old('area_of_project') }}" placeholder="e.g. 5.5 Acres / 26,000 Sq. Yds" required autocomplete="off"
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-aspect-ratio text-indigo-500"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Developer Plan (PDF Max 500 KB) -->
                            <div class="space-y-1">
                                <label for="developer_plan" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                    <span>Developer Plan (PDF)</span>
                                    <span class="px-1.5 py-0.2 rounded text-[8px] font-extrabold bg-amber-100 text-amber-800">Max 500 KB</span>
                                </label>
                                <input type="file" id="developer_plan" name="developer_plan" accept=".pdf" onchange="validateDeveloperPlanPdf(this)" disabled
                                    class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[9.5px] file:font-black file:uppercase file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 file:cursor-pointer border border-slate-250 rounded-lg bg-slate-100 opacity-60 p-1 cursor-not-allowed">
                                <p id="developer_plan_hint" class="text-[8.5px] text-slate-400 font-mono">🔒 Front-end validated (.pdf only, max 500KB)</p>
                            </div>
                        </div>

                        <!-- Row 6: Approved of Building Plan -->
                        <div class="border border-indigo-200/80 rounded-xl p-4 bg-gradient-to-br from-white to-indigo-50/30 space-y-3">
                            <div class="flex items-center justify-between border-b border-indigo-150 pb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-indigo-500/15 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                        <i class="bi bi-building-check"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-indigo-100 text-indigo-800">Step 10</span>
                                            <span>Approved of Building Plan</span>
                                        </h4>
                                        <p class="text-[9px] text-slate-400 font-mono">Town & Country Planning statutory building approval</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black text-indigo-800 bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded uppercase font-mono">
                                    Statutory Plan
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Dropdown: Approved of Building Plan (Yes/No) -->
                                <div class="space-y-1">
                                    <label for="approved_building_plan" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                        Approved of Building Plan <span class="text-red-500">*</span>
                                    </label>
                                    <select id="approved_building_plan" name="approved_building_plan" onchange="handleApprovedBuildingPlanChange()" disabled required
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                        <option value="" disabled selected>🔒 Step 9: Fill License & Area First...</option>
                                        <option value="yes" {{ old('approved_building_plan') == 'yes' ? 'selected' : '' }}>Yes (Approved)</option>
                                        <option value="no" {{ old('approved_building_plan') == 'no' ? 'selected' : '' }}>No (Not Approved)</option>
                                    </select>
                                </div>

                                <!-- Date of Approved (Shown when Yes) -->
                                <div id="building_plan_date_wrapper" class="space-y-1 {{ old('approved_building_plan') == 'yes' ? '' : 'hidden' }}">
                                    <label for="building_plan_approval_date" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                        Date of Approved <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="building_plan_approval_date" name="building_plan_approval_date" value="{{ old('building_plan_approval_date') }}"
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                </div>

                                <!-- Building Plan Copy (Shown when Yes) -->
                                <div id="building_plan_copy_wrapper" class="space-y-1 {{ old('approved_building_plan') == 'yes' ? '' : 'hidden' }}">
                                    <label for="building_plan_copy" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                        <span>Building Plan Copy (PDF)</span>
                                        <span class="text-[8.5px] text-indigo-700 font-mono">Max 10MB</span>
                                    </label>
                                    <input type="file" id="building_plan_copy" name="building_plan_copy" accept=".pdf" onchange="validateBuildingPlanCopyPdf(this)"
                                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[9.5px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer border border-slate-250 rounded-lg bg-white p-1">
                                </div>
                            </div>
                        </div>

                        <!-- Row 7: Status of OC (Occupation Certificate) Verification Section -->
                        <div class="border border-slate-200 rounded-xl p-4 bg-gradient-to-br from-slate-50/70 to-teal-50/20 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-150 pb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-teal-500/15 text-teal-700 flex items-center justify-center font-bold text-xs">
                                        <i class="bi bi-file-earmark-check-fill"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-teal-100 text-teal-800">Step 11</span>
                                            <span>Status of OC (Occupation Certificate)</span>
                                        </h4>
                                        <p class="text-[9px] text-slate-400 font-mono">Select statutory OC category & submit allotment limits</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black text-teal-800 bg-teal-100 border border-teal-200 px-2 py-0.5 rounded uppercase font-mono">
                                    Statutory Clearance
                                </span>
                            </div>

                            <!-- Status of OC Dropdown (Yes/No) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label for="oc_status_yn" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                        Status of OC <span class="text-red-500">*</span>
                                    </label>
                                    <select id="oc_status_yn" name="oc_status_yn" onchange="handleOcStatusYnChange()" disabled required
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                        <option value="" disabled selected>🔒 Step 10: Complete Building Plan First...</option>
                                        <option value="yes" {{ old('oc_status_yn') == 'yes' ? 'selected' : '' }}>Yes (OC Received)</option>
                                        <option value="no" {{ old('oc_status_yn') == 'no' ? 'selected' : '' }}>No (OC Not Received)</option>
                                    </select>
                                </div>

                                <!-- Part/Complete Dropdown (Revealed when Yes) -->
                                <div id="oc_category_wrapper" class="space-y-1 {{ old('oc_status_yn') == 'yes' ? '' : 'hidden' }}">
                                    <label for="oc_status" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                        OC Category (Part / Complete) <span class="text-red-500">*</span>
                                    </label>
                                    <select id="oc_status" name="oc_status" onchange="handleOcStatusChange()"
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                        <option value="" disabled selected>Choose OC Category...</option>
                                        <option value="part" {{ old('oc_status') == 'part' ? 'selected' : '' }}>Part (Partial Allotment)</option>
                                        <option value="complete" {{ old('oc_status') == 'complete' ? 'selected' : '' }}>Complete (Full Project Completion)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Dynamic OC Fields (Date of OC, Copy, Allotment limits) -->
                            <div id="oc_fields_container" class="{{ old('oc_status') ? '' : 'hidden' }} space-y-3 pt-2 border-t border-slate-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Date of OC -->
                                    <div class="space-y-1">
                                        <label for="oc_date" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                            Date of OC <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" id="oc_date" name="oc_date" value="{{ old('oc_date') }}"
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-medium focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                    </div>

                                    <!-- Copy Attached OC (PDF) -->
                                    <div class="space-y-1">
                                        <label for="oc_copy" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between">
                                            <span>Copy Attached OC (PDF)</span>
                                            <span class="text-[8.5px] text-teal-700 font-mono">Max 10MB</span>
                                        </label>
                                        <input type="file" id="oc_copy" name="oc_copy" accept=".pdf"
                                            class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[9.5px] file:font-black file:uppercase file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 file:cursor-pointer border border-slate-250 rounded-lg bg-white p-1">
                                    </div>

                                    <!-- Total Flats in Project -->
                                    <div class="space-y-1">
                                        <label for="total_flats_in_project" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                            Total Flats in Project
                                        </label>
                                        <input type="number" min="1" id="total_flats_in_project" name="total_flats_in_project" value="{{ old('total_flats_in_project') }}" placeholder="e.g. 500"
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-medium focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Total EWS Flats in Project -->
                                    <div class="space-y-1">
                                        <label for="total_ews_flats_in_project" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                            Total EWS Flats in Project
                                        </label>
                                        <input type="number" min="1" id="total_ews_flats_in_project" name="total_ews_flats_in_project" value="{{ old('total_ews_flats_in_project') }}" placeholder="e.g. 100"
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-medium focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                    </div>
                                </div>

                                <!-- Part OC Specific Fields -->
                                <div id="part_oc_extra_fields" class="{{ old('oc_status') == 'part' ? '' : 'hidden' }} bg-amber-50/80 border border-amber-200 rounded-xl p-3 space-y-2">
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="font-extrabold uppercase text-amber-800 flex items-center gap-1.5 font-mono">
                                            <i class="bi bi-pie-chart-fill text-amber-600"></i> Part OC Breakdown
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label for="flats_offered_for_draw" class="block text-[10px] font-black uppercase text-slate-700 tracking-wider">
                                                No. of Flats Offered for Draw [Number] <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" min="0" id="flats_offered_for_draw" name="flats_offered_for_draw" value="{{ old('flats_offered_for_draw') }}" placeholder="e.g. 40"
                                                class="w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                        </div>

                                        <div class="space-y-1">
                                            <label for="remaining_flats_for_oc" class="block text-[10px] font-black uppercase text-slate-700 tracking-wider">
                                                Remaining Flats for OC
                                            </label>
                                            <input type="number" min="0" id="remaining_flats_for_oc" name="remaining_flats_for_oc" value="{{ old('remaining_flats_for_oc') }}" placeholder="e.g. 60"
                                                class="w-full bg-white border border-amber-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 12: Block Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Block / Tower No. Selection -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label for="block_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 12</span>
                                        <span>Block / Tower No.</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <button type="button" id="btn_open_add_block" onclick="openAddBlockInline()" class="inline-flex items-center gap-1 text-[9.5px] font-bold text-sky-600 hover:text-sky-800 bg-sky-50 hover:bg-sky-100 border border-sky-200 hover:border-sky-300 px-2 py-0.5 rounded shadow-2xs transition-all cursor-pointer">
                                        <i class="bi bi-plus-circle-fill text-[10px]"></i>
                                        <span>Add Block</span>
                                    </button>
                                </div>
                                <div class="relative dropdown-select-wrapper">
                                    <select id="block_id" name="block_id" required disabled
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                        <option value="" disabled selected>🔒 Step 6: Select Project First...</option>
                                        <option value="new">+ Add New Block/Tower</option>
                                    </select>
                                </div>
                                
                                <!-- New Block Input -->
                                <div id="new_block_container" class="hidden mt-2">
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="new_block_name" name="new_block_name" placeholder="Enter new block/tower number (e.g. T-02)"
                                            class="w-full bg-white border border-sky-400 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                        <button type="button" id="btn_save_block_ajax" onclick="saveNewBlockAjax()"
                                            class="px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider whitespace-nowrap flex items-center gap-1 shadow-sm transition-all shrink-0">
                                            <i class="bi bi-plus-circle-fill"></i>
                                            <span>Save Block</span>
                                        </button>
                                    </div>
                                    <div id="block_similarity_alert" class="hidden text-[10.5px] font-bold text-amber-800 bg-amber-50 border border-amber-300 rounded-lg p-2.5 mt-2 flex items-start gap-2 shadow-xs transition-all">
                                        <i class="bi bi-exclamation-triangle-fill text-amber-600 text-sm mt-0.5 shrink-0"></i>
                                        <span id="block_similarity_message"></span>
                                    </div>
                                    <p class="text-[8.5px] text-slate-400 mt-1 italic">Click 'Save Block' to instantly save into ews_blocks, or auto-saves on form submit.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 13: Flat Registration Fields (Single & Bulk) -->
                        <div id="step-13-wrapper">
                            <input type="hidden" name="bulk_mode" id="bulk_mode" value="{{ old('bulk_mode', '0') }}">

                            <!-- Single Mode Flat Input Fields -->
                            <div id="single-fields-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                                <!-- Floor (Single) -->
                                <div class="space-y-1">
                                    <label for="floor_number_single" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 13</span>
                                        <span>Floor (Number)</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <select id="floor_number_single" name="floor_number" required disabled
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:outline-none font-bold">
                                        <option value="" disabled {{ old('floor_number') === null || old('floor_number') === '' ? 'selected' : '' }}>Choose Floor...</option>
                                        <option value="0" {{ old('floor_number') !== null && old('floor_number') !== '' && old('floor_number') == '0' ? 'selected' : '' }}>Ground Floor (0)</option>
                                        @for($f = 1; $f <= 100; $f++)
                                            <option value="{{ $f }}" {{ old('floor_number') !== null && old('floor_number') !== '' && old('floor_number') == $f ? 'selected' : '' }}>Floor {{ $f }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Flat Number -->
                                <div class="space-y-1">
                                    <label for="flat_number" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 13</span>
                                        <span>Flat Number</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <input type="text" id="flat_number" name="flat_number" value="{{ old('flat_number') }}" placeholder="🔒 Step 12: Select Block First..." required disabled
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                </div>
                            </div>

                            <!-- Bulk Generation Fields (Hidden by default) -->
                            <div id="bulk-fields-container" class="hidden space-y-4 border-t border-slate-100 pt-3">
                                <div class="bg-gradient-to-r from-sky-50 to-indigo-50 border border-sky-200 rounded-lg p-3 flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-md bg-sky-600 text-white flex items-center justify-center text-xs shadow-xs shrink-0">
                                            <i class="bi bi-grid-3x3-gap-fill"></i>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-black uppercase text-slate-800 tracking-wider">Bulk Flat Generation Active</div>
                                            <div class="text-[9px] text-slate-500 font-medium">Configure floor sequence or custom comma-separated flats to register together</div>
                                        </div>
                                    </div>
                                    <span class="text-[8.5px] font-extrabold text-sky-700 bg-white border border-sky-200 px-2 py-0.5 rounded shadow-2xs font-mono uppercase">Step 13</span>
                                </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Floor (Number) -->
                                <div class="space-y-1">
                                    <label for="floor_number_bulk" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 13</span>
                                        <span>Floor (Number)</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <select id="floor_number_bulk" name="floor_number" disabled
                                        class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:outline-none font-bold">
                                        <option value="" disabled {{ old('floor_number') === null || old('floor_number') === '' ? 'selected' : '' }}>Choose Floor...</option>
                                        <option value="0" {{ old('floor_number') !== null && old('floor_number') !== '' && old('floor_number') == '0' ? 'selected' : '' }}>Ground Floor (0)</option>
                                        @for($f = 1; $f <= 100; $f++)
                                            <option value="{{ $f }}" {{ old('floor_number') !== null && old('floor_number') !== '' && old('floor_number') == $f ? 'selected' : '' }}>Floor {{ $f }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Flat Numbering Pattern -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Flat Numbering Pattern <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-4 py-2 text-xs font-bold">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="flat_number_type" value="range" {{ old('flat_number_type', 'range') === 'range' ? 'checked' : '' }} onchange="toggleFlatInputType('range')"
                                                class="text-sky-600 focus:ring-sky-500">
                                            <span>Numerical Range</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="flat_number_type" value="custom" {{ old('flat_number_type') === 'custom' ? 'checked' : '' }} onchange="toggleFlatInputType('custom')"
                                                class="text-sky-600 focus:ring-sky-500">
                                            <span>Custom List</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Range Settings Sub-container -->
                            <div id="bulk-range-container" class="space-y-3 p-3 bg-slate-50/50 rounded-lg border border-slate-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label for="from_flat" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">From Flat (Sequence) <span class="text-red-500">*</span></label>
                                        <input type="number" id="from_flat" name="from_flat" value="{{ old('from_flat') }}" min="1" placeholder="e.g. 1" disabled
                                            class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                    </div>
                                    <div class="space-y-1">
                                        <label for="to_flat" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">To Flat (Sequence) <span class="text-red-500">*</span></label>
                                        <input type="number" id="to_flat" name="to_flat" value="{{ old('to_flat') }}" min="1" placeholder="e.g. 10" disabled
                                            class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="floor_prefix_enabled" name="floor_prefix_enabled" value="1" {{ old('floor_prefix_enabled', '1') == '1' ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-sky-600 focus:ring-sky-500 h-3.5 w-3.5">
                                    <label for="floor_prefix_enabled" class="text-xs text-slate-600 font-medium select-none">
                                        Prefix Floor Number to Flat (e.g. Floor 2 + Seq 1 = Flat 201)
                                    </label>
                                </div>
                            </div>

                            <!-- Custom List Sub-container -->
                            <div id="bulk-custom-container" class="hidden space-y-1 p-3 bg-slate-50/50 rounded-lg border border-slate-200">
                                <label for="custom_flat_numbers" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                    Flat Numbers (Comma Separated) <span class="text-red-500">*</span>
                                </label>
                                <textarea id="custom_flat_numbers" name="custom_flat_numbers" rows="2" placeholder="e.g. 101, 102, 104, 105" disabled
                                    class="w-full bg-slate-100 cursor-not-allowed opacity-60 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">{{ old('custom_flat_numbers') }}</textarea>
                                <p class="text-[9px] text-slate-400">Enter comma-separated flat numbers (exact designations without auto-prefixing).</p>
                            </div>
                        </div>
                    </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-between border-t border-slate-150 pt-4">
                            <a href="{{ route('ews.developer.dashboard') }}"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold uppercase rounded-lg text-xs tracking-wider transition-all">
                                Cancel
                            </a>
                            <button type="submit" id="btn_submit_flat"
                                class="px-6 py-2 bg-sky-600 hover:bg-sky-700 text-white font-black uppercase rounded-lg text-xs tracking-wider shadow-sm transition-all flex items-center gap-1.5 opacity-50 cursor-not-allowed">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span>Save & Register Flat</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Add New Town Popup -->
    <div id="modal_add_town" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all duration-300" onclick="if(event.target === this) closeAddTownModal()">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 flex flex-col overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modal_add_town_content">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-sky-50 via-white to-indigo-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shadow-xs shrink-0">
                        <i class="bi bi-geo-alt-fill text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Add New Town</h3>
                        <p class="text-[10px] text-slate-500 font-medium">
                            District: <span class="font-bold text-sky-700" id="modal_town_dist_name">-</span> &bull; Zone: <span class="font-bold text-slate-700" id="modal_town_zone_name">-</span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeAddTownModal()" class="w-7 h-7 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-3.5">
                <!-- Town Name -->
                <div class="space-y-1">
                    <label for="modal_new_town_name" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                        Town Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modal_new_town_name" placeholder="e.g. Sohna, Manesar, Pataudi, Nilokheri"
                        class="w-full bg-slate-50 border border-slate-300 focus:border-sky-500 focus:bg-white rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:outline-none transition-all">
                </div>

                <!-- Municipality / Town Type -->
                <div class="space-y-1">
                    <label for="modal_new_town_type" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                        Municipality / Town Type <span class="text-red-500">*</span>
                    </label>
                    <select id="modal_new_town_type" onchange="toggleCustomTownTypeModal(this.value)"
                        class="w-full bg-slate-50 border border-slate-300 focus:border-sky-500 focus:bg-white rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:outline-none transition-all">
                        <option value="" disabled selected>Choose Municipality / Town Type...</option>
                        @if(isset($townTypes))
                            @foreach($townTypes as $tType)
                                <option value="{{ $tType }}">{{ $tType }}</option>
                            @endforeach
                        @endif
                        <option value="other">+ Other (Custom Type)</option>
                    </select>
                </div>

                <!-- Custom Town Type container (shown if 'other') -->
                <div id="modal_custom_town_type_container" class="hidden space-y-1">
                    <label for="modal_custom_town_type" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                        Specify Custom Municipality Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modal_custom_town_type" placeholder="e.g. Cantonment Board, Industrial Township"
                        class="w-full bg-white border border-sky-400 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:outline-none">
                </div>

                <!-- Live Similarity / Duplicate Alert -->
                <div id="modal_town_similarity_alert" class="hidden text-[10.5px] font-bold text-amber-800 bg-amber-50 border border-amber-300 rounded-lg p-2.5 flex items-start gap-2 shadow-xs transition-all">
                    <i class="bi bi-exclamation-triangle-fill text-amber-600 text-sm mt-0.5 shrink-0"></i>
                    <span id="modal_town_similarity_message"></span>
                </div>

                <div class="p-2.5 bg-sky-50/70 border border-sky-200 rounded-xl flex items-start gap-2">
                    <i class="bi bi-info-circle-fill text-sky-600 text-xs mt-0.5 shrink-0"></i>
                    <div class="text-[9.5px] text-sky-800 leading-tight">
                        This Town will be mapped under <strong id="modal_notice_dist">-</strong> (<span id="modal_notice_zone">-</span>). It will immediately appear in your town list.
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddTownModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold uppercase rounded-lg text-[10px] tracking-wider transition-all cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="modal_btn_save_town" onclick="saveNewTownAjax()" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-black uppercase rounded-lg text-[10px] tracking-wider shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="bi bi-plus-circle-fill text-xs"></i>
                    <span>Save Town</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Add New Project Popup -->
    <div id="modal_add_project" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all duration-300" onclick="if(event.target === this) closeAddProjectModal()">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 flex flex-col overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modal_add_project_content">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-emerald-50 via-white to-sky-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-xs shrink-0">
                        <i class="bi bi-buildings-fill text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Add New Project</h3>
                        <p class="text-[10px] text-slate-500 font-medium" id="modal_project_context_info">
                            District: <span class="font-bold text-slate-700" id="modal_proj_dist_name">-</span> &bull; Town: <span class="font-bold text-sky-700" id="modal_proj_town_name">-</span> &bull; Developer: <span class="font-bold text-amber-700" id="modal_proj_dev_name">-</span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeAddProjectModal()" class="w-7 h-7 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-3.5">
                <!-- Project Name -->
                <div class="space-y-1">
                    <label for="modal_project_name_input" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modal_project_name_input" placeholder="e.g. Signature Global Park 4, TDI Kingsbury"
                        class="w-full bg-slate-50 border border-slate-300 focus:border-emerald-500 focus:bg-white rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:outline-none transition-all">
                </div>

                <div class="p-2.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-start gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-xs mt-0.5 shrink-0"></i>
                    <div class="text-[9.5px] text-emerald-800 leading-tight">
                        Multiple projects can be registered under this Developer profile. It will appear immediately in your project selection list.
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddProjectModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold uppercase rounded-lg text-[10px] tracking-wider transition-all cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="modal_btn_save_project" onclick="saveNewProjectModalAjax()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase rounded-lg text-[10px] tracking-wider shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="bi bi-plus-circle-fill text-xs"></i>
                    <span>Save Project</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        const activeDropdownLoaders = {};

        function showDropdownLoader(selectId, text = 'Loading data...') {
            const selectEl = document.getElementById(selectId);
            if (!selectEl) return;
            
            let wrapper = selectEl.closest('.dropdown-select-wrapper');
            if (!wrapper) {
                wrapper = selectEl.parentElement;
                if (wrapper) wrapper.classList.add('relative');
            }
            if (!wrapper) return;

            activeDropdownLoaders[selectId] = Date.now();

            let loader = document.getElementById(`loader-overlay-${selectId}`);
            if (loader) {
                const textLabel = loader.querySelector('.loader-text-label');
                if (textLabel) textLabel.textContent = text;
                return;
            }

            loader = document.createElement('div');
            loader.id = `loader-overlay-${selectId}`;
            loader.className = 'dropdown-loader-overlay absolute inset-0 rounded-lg flex items-center justify-between px-3 z-30 transition-all duration-300 opacity-0 scale-98 pointer-events-auto cursor-wait select-none overflow-hidden';
            
            loader.innerHTML = `
                <div class="dropdown-loader-shimmer absolute top-0 inset-x-0 h-[2px]"></div>
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="relative flex items-center justify-center w-4 h-4 shrink-0">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-sky-400/40 animate-ping"></span>
                        <div class="w-3.5 h-3.5 rounded-full border-2 border-sky-200 border-t-sky-600 border-r-indigo-600 animate-spin"></div>
                    </div>
                    <div class="flex flex-col truncate">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9.5px] font-black uppercase tracking-wider text-slate-800 font-mono loader-text-label truncate">${text}</span>
                            <span class="inline-flex space-x-0.5 shrink-0">
                                <span class="w-1 h-1 bg-sky-600 rounded-full animate-bounce" style="animation-delay: -0.3s"></span>
                                <span class="w-1 h-1 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: -0.15s"></span>
                                <span class="w-1 h-1 bg-sky-600 rounded-full animate-bounce"></span>
                            </span>
                        </div>
                        <span class="text-[7.5px] text-slate-400 font-mono tracking-tight uppercase">Synchronizing records...</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[7.5px] font-black font-mono bg-sky-50 text-sky-700 border border-sky-200 shadow-2xs shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                    <span>SYNCING</span>
                </span>
                <div class="dropdown-loader-shimmer absolute bottom-0 inset-x-0 h-[1.5px]"></div>
            `;

            wrapper.appendChild(loader);

            requestAnimationFrame(() => {
                loader.classList.remove('opacity-0', 'scale-98');
                loader.classList.add('opacity-100', 'scale-100');
            });
        }

        function hideDropdownLoader(selectId, minDisplayMs = 450) {
            const loader = document.getElementById(`loader-overlay-${selectId}`);
            if (!loader) return;
            
            const startedAt = activeDropdownLoaders[selectId] || 0;
            const elapsed = Date.now() - startedAt;
            const remaining = Math.max(0, minDisplayMs - elapsed);

            setTimeout(() => {
                const currentLoader = document.getElementById(`loader-overlay-${selectId}`);
                if (!currentLoader) return;
                currentLoader.classList.remove('opacity-100', 'scale-100');
                currentLoader.classList.add('opacity-0', 'scale-98');
                setTimeout(() => {
                    if (currentLoader && currentLoader.parentElement) {
                        currentLoader.parentElement.removeChild(currentLoader);
                    }
                    delete activeDropdownLoaders[selectId];
                }, 250);
            }, remaining);
        }

        function switchMode(mode, isInitial = false) {
            const bulkModeInput = document.getElementById('bulk_mode');
            const tabSingle = document.getElementById('tab-single');
            const tabBulk = document.getElementById('tab-bulk');
            
            const singleFields = document.getElementById('single-fields-container');
            const bulkFields = document.getElementById('bulk-fields-container');
            
            const floorSingle = document.getElementById('floor_number_single');
            const flatNumInput = document.getElementById('flat_number');
            
            const floorBulk = document.getElementById('floor_number_bulk');
            const fromFlat = document.getElementById('from_flat');
            const toFlat = document.getElementById('to_flat');
            const customFlats = document.getElementById('custom_flat_numbers');

            const blockSelect = document.getElementById('block_id');
            const isBlockReady = blockSelect && blockSelect.value && blockSelect.value !== '';

            const modeBadge = document.getElementById('mode-status-badge');
            const modeDesc = document.getElementById('mode-status-desc');
            const submitBtn = document.getElementById('btn_submit_flat');

            if (mode === 'bulk') {
                if (bulkModeInput) bulkModeInput.value = '1';
                if (tabBulk) {
                    tabBulk.className = "px-3 py-1 bg-white text-sky-700 shadow-sm rounded-md transition-all font-black cursor-pointer";
                }
                if (tabSingle) {
                    tabSingle.className = "px-3 py-1 text-slate-600 hover:text-slate-800 rounded-md transition-all font-black cursor-pointer";
                }
                
                if (modeBadge) {
                    modeBadge.className = "text-[8.5px] font-black uppercase px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 border border-indigo-200";
                    modeBadge.textContent = "Bulk Generation Mode";
                }
                if (modeDesc) {
                    modeDesc.textContent = "Allotment record builder inputs (Bulk Generation of Multiple Flats)";
                }
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="bi bi-grid-3x3-gap-fill"></i><span>Save & Generate Flats (Bulk)</span>';
                }
                
                if (singleFields) singleFields.classList.add('hidden');
                if (bulkFields) bulkFields.classList.remove('hidden');
                
                if (floorSingle) { floorSingle.required = false; floorSingle.disabled = true; }
                if (flatNumInput) { flatNumInput.required = false; flatNumInput.disabled = true; }
                
                if (floorBulk) {
                    floorBulk.required = true;
                    floorBulk.disabled = !isBlockReady;
                    if (isBlockReady) {
                        floorBulk.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        floorBulk.classList.add('bg-white');
                    } else {
                        floorBulk.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        floorBulk.classList.remove('bg-white');
                    }
                }

                // Reset/nullify all Single Flat fields when switching to Bulk Generation
                if (!isInitial) {
                    if (singleFields) {
                        singleFields.querySelectorAll('input, select, textarea').forEach(el => {
                            el.value = "";
                            if (el.tagName === 'SELECT') {
                                el.selectedIndex = 0;
                            }
                        });
                    }
                    if (floorSingle) {
                        floorSingle.value = "";
                        floorSingle.selectedIndex = 0;
                    }
                    if (flatNumInput) flatNumInput.value = "";
                }
                
                const checkedRadio = document.querySelector('input[name="flat_number_type"]:checked');
                toggleFlatInputType(checkedRadio ? checkedRadio.value : 'range');
            } else {
                if (bulkModeInput) bulkModeInput.value = '0';
                if (tabSingle) {
                    tabSingle.className = "px-3 py-1 bg-white text-sky-700 shadow-sm rounded-md transition-all font-black cursor-pointer";
                }
                if (tabBulk) {
                    tabBulk.className = "px-3 py-1 text-slate-600 hover:text-slate-800 rounded-md transition-all font-black cursor-pointer";
                }
                
                if (modeBadge) {
                    modeBadge.className = "text-[8.5px] font-black uppercase px-2 py-0.5 rounded bg-sky-100 text-sky-800 border border-sky-200";
                    modeBadge.textContent = "Single Flat Mode";
                }
                if (modeDesc) {
                    modeDesc.textContent = "Allotment record builder inputs (Single Flat Entry)";
                }
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill"></i><span>Save & Register Flat</span>';
                }
                
                if (singleFields) singleFields.classList.remove('hidden');
                if (bulkFields) bulkFields.classList.add('hidden');
                
                if (floorSingle) {
                    floorSingle.required = true;
                    floorSingle.disabled = !isBlockReady;
                    if (isBlockReady) {
                        floorSingle.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        floorSingle.classList.add('bg-white');
                    } else {
                        floorSingle.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        floorSingle.classList.remove('bg-white');
                    }
                }
                if (flatNumInput) {
                    flatNumInput.required = true;
                    flatNumInput.disabled = !isBlockReady;
                    if (isBlockReady) {
                        flatNumInput.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        flatNumInput.classList.add('bg-white');
                        flatNumInput.placeholder = "e.g. 101, A-202";
                    } else {
                        flatNumInput.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        flatNumInput.classList.remove('bg-white');
                        flatNumInput.placeholder = "🔒 Step 12: Select Block First...";
                    }
                }
                
                if (floorBulk) { floorBulk.required = false; floorBulk.disabled = true; }
                if (fromFlat) { fromFlat.required = false; fromFlat.disabled = true; }
                if (toFlat) { toFlat.required = false; toFlat.disabled = true; }
                if (customFlats) { customFlats.required = false; customFlats.disabled = true; }

                // Reset/nullify all Bulk Generation fields when switching to Single Flat
                if (!isInitial) {
                    if (bulkFields) {
                        bulkFields.querySelectorAll('input:not([type="radio"]):not([type="checkbox"]):not([type="hidden"]), select, textarea').forEach(el => {
                            el.value = "";
                            if (el.tagName === 'SELECT') {
                                el.selectedIndex = 0;
                            }
                        });
                    }
                    if (floorBulk) {
                        floorBulk.value = "";
                        floorBulk.selectedIndex = 0;
                    }
                    if (fromFlat) fromFlat.value = "";
                    if (toFlat) toFlat.value = "";
                    if (customFlats) customFlats.value = "";
                    const prefixCheckbox = document.getElementById('floor_prefix_enabled');
                    if (prefixCheckbox) prefixCheckbox.checked = true;
                    const rangeRadio = document.querySelector('input[name="flat_number_type"][value="range"]');
                    if (rangeRadio) rangeRadio.checked = true;
                    const rangeContainer = document.getElementById('bulk-range-container');
                    const customContainer = document.getElementById('bulk-custom-container');
                    if (rangeContainer) rangeContainer.classList.remove('hidden');
                    if (customContainer) customContainer.classList.add('hidden');
                }
            }
        }

        function toggleFlatInputType(type) {
            const rangeContainer = document.getElementById('bulk-range-container');
            const customContainer = document.getElementById('bulk-custom-container');
            const fromFlat = document.getElementById('from_flat');
            const toFlat = document.getElementById('to_flat');
            const customFlats = document.getElementById('custom_flat_numbers');
            
            const isBulk = document.getElementById('bulk_mode') && document.getElementById('bulk_mode').value === '1';
            const blockSelect = document.getElementById('block_id');
            const isBlockReady = blockSelect && blockSelect.value && blockSelect.value !== '';

            if (type === 'custom') {
                if (rangeContainer) rangeContainer.classList.add('hidden');
                if (customContainer) customContainer.classList.remove('hidden');
                
                // Clear numerical range inputs
                if (fromFlat) fromFlat.value = '';
                if (toFlat) toFlat.value = '';
                
                if (isBulk) {
                    if (fromFlat) { fromFlat.required = false; fromFlat.disabled = true; }
                    if (toFlat) { toFlat.required = false; toFlat.disabled = true; }
                    if (customFlats) {
                        customFlats.required = true;
                        customFlats.disabled = !isBlockReady;
                        customFlats.placeholder = isBlockReady ? "e.g. 101, 102, 104, 105" : "🔒 Step 12: Select Block First...";
                        if (isBlockReady) {
                            customFlats.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                            customFlats.classList.add('bg-white');
                        } else {
                            customFlats.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                            customFlats.classList.remove('bg-white');
                        }
                    }
                }
            } else {
                if (rangeContainer) rangeContainer.classList.remove('hidden');
                if (customContainer) customContainer.classList.add('hidden');
                
                // Clear custom list input
                if (customFlats) customFlats.value = '';
                
                if (isBulk) {
                    if (customFlats) { customFlats.required = false; customFlats.disabled = true; }
                    if (fromFlat) {
                        fromFlat.required = true;
                        fromFlat.disabled = !isBlockReady;
                        fromFlat.placeholder = isBlockReady ? "e.g. 1" : "🔒 Step 12: Select Block First...";
                        if (isBlockReady) {
                            fromFlat.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                            fromFlat.classList.add('bg-white');
                        } else {
                            fromFlat.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                            fromFlat.classList.remove('bg-white');
                        }
                    }
                    if (toFlat) {
                        toFlat.required = true;
                        toFlat.disabled = !isBlockReady;
                        toFlat.placeholder = isBlockReady ? "e.g. 10" : "🔒 Step 12: Select Block First...";
                        if (isBlockReady) {
                            toFlat.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                            toFlat.classList.add('bg-white');
                        } else {
                            toFlat.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                            toFlat.classList.remove('bg-white');
                        }
                    }
                }
            }
        }

        // AJAX and Dynamic Fields Logic for Towns, Projects, and Blocks
        const districtSelect = document.getElementById('district_id');
        const townSelect = document.getElementById('town_id');

        const projectSelect = document.getElementById('project_id');
        const newProjectContainer = document.getElementById('new_project_container');
        const newProjectInput = document.getElementById('new_project_name');
        
        const blockSelect = document.getElementById('block_id');
        const newBlockContainer = document.getElementById('new_block_container');
        const newBlockInput = document.getElementById('new_block_name');

        $(document).ready(function() {
            // Initialize Select2 search elements
            if ($('#zone_id').is('select')) {
                $('#zone_id').select2();
            }
            if ($('#district_id').is('select')) {
                $('#district_id').select2();
            }
            $('#town_id').select2();
            $('#project_id').select2();
            $('#block_id').select2();

            // Delegated click listeners for form mode tabs
            $(document).on('click', '#tab-single', function(e) {
                e.preventDefault();
                switchMode('single');
            });
            $(document).on('click', '#tab-bulk', function(e) {
                e.preventDefault();
                switchMode('bulk');
            });

            // Direct non-recursive change listeners
            $(document).on('input change keyup paste', '#register_flat', function() {
                initSequentialLockState();
            });
            $(document).on('change', '#zone_id', function() {
                if (typeof handleZoneChange === 'function') handleZoneChange();
                initSequentialLockState();
            });
            $(document).on('change', '#district_id', function() {
                if (typeof handleDistrictChange === 'function') handleDistrictChange();
                initSequentialLockState();
            });
            $(document).on('change', '#town_id', function() {
                handleTownChange();
                initSequentialLockState();
            });
            $(document).on('change', '#project_id', function() {
                handleProjectChange();
                initSequentialLockState();
            });
            $(document).on('change', '#block_id', function() {
                handleBlockChange();
                initSequentialLockState();
            });
            $(document).on('input change keyup paste', '#developer_name', function() {
                initSequentialLockState();
            });
            $(document).on('input change keyup paste', '#sector_no', function() {
                initSequentialLockState();
            });
            $(document).on('input change keyup paste', '#address_of_project', function() {
                initSequentialLockState();
            });
            $(document).on('input change keyup paste', '#license_no', function() {
                initSequentialLockState();
            });
            $(document).on('input change keyup paste', '#area_of_project', function() {
                initSequentialLockState();
            });
            $(document).on('change', '#approved_building_plan', function() {
                handleApprovedBuildingPlanChange();
            });
            $(document).on('input change', '#building_plan_approval_date', function() {
                initSequentialLockState();
            });
            $(document).on('change', '#oc_status_yn', function() {
                handleOcStatusYnChange();
            });
            $(document).on('change', '#oc_status', function() {
                handleOcStatusChange();
                initSequentialLockState();
            });

            // SweetAlert2 Toast Notifications for session messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: {!! json_encode(session('success')) !!},
                    confirmButtonColor: '#3b82f6'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: {!! json_encode(session('error')) !!},
                    confirmButtonColor: '#3b82f6'
                });
            @endif

            @if(isset($errors) && $errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: `<ul class="text-left list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>`,
                    confirmButtonColor: '#3b82f6'
                });
            @endif
            // Initialize form mode
            switchMode('{{ old('bulk_mode', '0') == '1' ? 'bulk' : 'single' }}', true);

            // Set sequential lock states across all 8 steps
            initSequentialLockState();
        });

        function updateInputLock(inputEl, enabled, placeholderText = null) {
            if (!inputEl) return;
            inputEl.disabled = !enabled;
            if (enabled) {
                inputEl.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                inputEl.classList.add('bg-white');
            } else {
                inputEl.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                inputEl.classList.remove('bg-white');
            }
            if (placeholderText) {
                inputEl.placeholder = placeholderText;
            }
        }

        function validateDeveloperPlanPdf(input) {
            if (!input || !input.files || input.files.length === 0) return true;
            const file = input.files[0];
            const maxBytes = 500 * 1024; // 500 KB
            const fileName = file.name || '';
            const fileExt = fileName.split('.').pop().toLowerCase();

            if (fileExt !== 'pdf' && file.type !== 'application/pdf') {
                input.value = '';
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Developer Plan must be a PDF document (.pdf only).',
                    confirmButtonColor: '#ef4444'
                });
                initSequentialLockState();
                return false;
            }

            if (file.size > maxBytes) {
                const sizeInKb = (file.size / 1024).toFixed(1);
                input.value = '';
                Swal.fire({
                    icon: 'warning',
                    title: 'File Too Large (Max 500 KB)',
                    html: `Uploaded Developer Plan size is <strong>${sizeInKb} KB</strong>.<br>Maximum allowed file size is <strong>500 KB</strong>.<br><br>Please compress the PDF and upload again.`,
                    confirmButtonColor: '#f59e0b'
                });
                initSequentialLockState();
                return false;
            }

            const sizeInKb = (file.size / 1024).toFixed(1);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: `Developer Plan PDF verified (${sizeInKb} KB)`
            });
            initSequentialLockState();
            return true;
        }

        function validateBuildingPlanCopyPdf(input) {
            if (!input || !input.files || input.files.length === 0) return true;
            const file = input.files[0];
            const fileName = file.name || '';
            const fileExt = fileName.split('.').pop().toLowerCase();

            if (fileExt !== 'pdf' && file.type !== 'application/pdf') {
                input.value = '';
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Building Plan Copy must be a PDF document (.pdf only).',
                    confirmButtonColor: '#ef4444'
                });
                return false;
            }

            const sizeInKb = (file.size / 1024).toFixed(1);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: `Building Plan Copy verified (${sizeInKb} KB)`
            });
            return true;
        }

        function handleApprovedBuildingPlanChange() {
            const planSelect = document.getElementById('approved_building_plan');
            const val = planSelect ? planSelect.value : '';
            const dateWrapper = document.getElementById('building_plan_date_wrapper');
            const copyWrapper = document.getElementById('building_plan_copy_wrapper');
            const dateInput = document.getElementById('building_plan_approval_date');

            if (val === 'no') {
                Swal.fire({
                    icon: 'error',
                    title: 'Approved Building Plan Mandatory',
                    html: 'Approved Building Plan is <strong>mandatory</strong> to register EWS flats.<br><br>Without an approved building plan, you cannot proceed or submit this registration.',
                    confirmButtonColor: '#ef4444'
                });
                if (dateWrapper) dateWrapper.classList.add('hidden');
                if (copyWrapper) copyWrapper.classList.add('hidden');
                if (dateInput) { dateInput.value = ''; dateInput.required = false; }
            } else if (val === 'yes') {
                if (dateWrapper) dateWrapper.classList.remove('hidden');
                if (copyWrapper) copyWrapper.classList.remove('hidden');
                if (dateInput) dateInput.required = true;
            } else {
                if (dateWrapper) dateWrapper.classList.add('hidden');
                if (copyWrapper) copyWrapper.classList.add('hidden');
                if (dateInput) { dateInput.value = ''; dateInput.required = false; }
            }
            initSequentialLockState();
        }

        function handleOcStatusYnChange() {
            const ocYnSelect = document.getElementById('oc_status_yn');
            const val = ocYnSelect ? ocYnSelect.value : '';
            const categoryWrapper = document.getElementById('oc_category_wrapper');
            const ocSelect = document.getElementById('oc_status');
            const fieldsContainer = document.getElementById('oc_fields_container');

            if (val === 'no') {
                Swal.fire({
                    icon: 'error',
                    title: 'Occupation Certificate (OC) Mandatory',
                    html: 'Valid Occupation Certificate (OC) is <strong>mandatory</strong> to offer flats for allotment.<br><br>Flats cannot be registered or submitted without an Occupation Certificate.',
                    confirmButtonColor: '#ef4444'
                });
                if (categoryWrapper) categoryWrapper.classList.add('hidden');
                if (ocSelect) { ocSelect.value = ''; ocSelect.required = false; }
                if (fieldsContainer) fieldsContainer.classList.add('hidden');
            } else if (val === 'yes') {
                if (categoryWrapper) categoryWrapper.classList.remove('hidden');
                if (ocSelect) ocSelect.required = true;
            } else {
                if (categoryWrapper) categoryWrapper.classList.add('hidden');
                if (ocSelect) { ocSelect.value = ''; ocSelect.required = false; }
                if (fieldsContainer) fieldsContainer.classList.add('hidden');
            }
            initSequentialLockState();
        }

        function initSequentialLockState() {
            const regFlatInput = document.getElementById('register_flat');
            const hasRegFlat = !!(regFlatInput && regFlatInput.value.trim() !== '');

            const zoneSelect = document.getElementById('zone_id');
            const hasZone = !!(zoneSelect && zoneSelect.value && zoneSelect.value !== '');

            const hasDist = !!(districtSelect && districtSelect.value && districtSelect.value !== '');
            const hasTown = !!(townSelect && townSelect.value && townSelect.value !== '' && townSelect.value !== 'new');

            const devNameInput = document.getElementById('developer_name');
            const hasDevName = !!(devNameInput && devNameInput.value.trim() !== '');

            const hasProj = !!(projectSelect && projectSelect.value && projectSelect.value !== '' && projectSelect.value !== 'new');

            const sectorInput = document.getElementById('sector_no');
            const hasSector = !!(sectorInput && sectorInput.value.trim() !== '');

            const addressInput = document.getElementById('address_of_project');
            const hasAddress = !!(addressInput && addressInput.value.trim() !== '');

            const licenseInput = document.getElementById('license_no');
            const hasLicense = !!(licenseInput && licenseInput.value.trim() !== '');

            const areaInput = document.getElementById('area_of_project');
            const hasArea = !!(areaInput && areaInput.value.trim() !== '');

            const devPlanInput = document.getElementById('developer_plan');

            const approvedPlanSelect = document.getElementById('approved_building_plan');
            const isApprovedPlanYes = !!(approvedPlanSelect && approvedPlanSelect.value === 'yes');
            const approvalDateInput = document.getElementById('building_plan_approval_date');
            const hasApprovalDate = !!(approvalDateInput && approvalDateInput.value);

            const ocYnSelect = document.getElementById('oc_status_yn');
            const isOcYnYes = !!(ocYnSelect && ocYnSelect.value === 'yes');

            const ocSelect = document.getElementById('oc_status');
            const hasOc = !!(ocSelect && ocSelect.value && ocSelect.value !== '');

            const hasBlock = !!(blockSelect && blockSelect.value && blockSelect.value !== '');

            // Step 2: Zone Lock State (requires Step 1 Register Flat)
            if (hasRegFlat) {
                updateSelectLock(zoneSelect, true, "Choose Zone...");
            } else {
                if (zoneSelect && zoneSelect.value) {
                    zoneSelect.value = '';
                    $(zoneSelect).val('');
                }
                updateSelectLock(zoneSelect, false, "🔒 Step 1: Enter Register Flat First...");
            }

            // Step 3: District Lock State (requires Step 1 & Step 2)
            if (hasRegFlat && hasZone) {
                updateSelectLock(districtSelect, true, "Choose a district...");
            } else {
                let placeholder = "🔒 Step 2: Select Zone First...";
                if (!hasRegFlat) placeholder = "🔒 Step 1: Enter Register Flat First...";
                updateSelectLock(districtSelect, false, placeholder);
            }

            // Step 4: Town Lock State (requires Step 3 District)
            if (hasRegFlat && hasZone && hasDist) {
                updateSelectLock(townSelect, true, "Choose a town...");
            } else {
                let placeholder = "🔒 Step 3: Select District First...";
                if (!hasRegFlat) placeholder = "🔒 Step 1: Enter Register Flat First...";
                else if (!hasZone) placeholder = "🔒 Step 2: Select Zone First...";
                updateSelectLock(townSelect, false, placeholder);
            }

            // Step 5: Developer Name (requires Step 4 Town) - NOT prefilled from Auth!
            if (hasRegFlat && hasZone && hasDist && hasTown) {
                updateInputLock(devNameInput, true, "Enter Developer Name...");
            } else {
                let placeholder = "🔒 Step 4: Select Town First...";
                if (!hasRegFlat) placeholder = "🔒 Step 1: Enter Register Flat First...";
                else if (!hasZone) placeholder = "🔒 Step 2: Select Zone First...";
                else if (!hasDist) placeholder = "🔒 Step 3: Select District First...";
                updateInputLock(devNameInput, false, placeholder);
            }

            // Step 6: Project Lock State (requires Step 5 Developer Name)
            const addProjBtn = document.getElementById('btn_open_add_project');
            if (hasRegFlat && hasZone && hasDist && hasTown && hasDevName) {
                updateSelectLock(projectSelect, true, "Choose a project...");
                if (addProjBtn) {
                    addProjBtn.disabled = false;
                    addProjBtn.classList.remove('opacity-50', 'pointer-events-none');
                }
            } else {
                let placeholder = "🔒 Step 5: Enter Developer Name First...";
                if (!hasRegFlat) placeholder = "🔒 Step 1: Enter Register Flat First...";
                else if (!hasZone) placeholder = "🔒 Step 2: Select Zone First...";
                else if (!hasDist) placeholder = "🔒 Step 3: Select District First...";
                else if (!hasTown) placeholder = "🔒 Step 4: Select Town First...";
                updateSelectLock(projectSelect, false, placeholder);
                if (addProjBtn) {
                    addProjBtn.disabled = true;
                    addProjBtn.classList.add('opacity-50', 'pointer-events-none');
                }
            }

            // Step 7: Address of Project (requires Step 6 Project)
            if (hasRegFlat && hasZone && hasDist && hasTown && hasDevName && hasProj) {
                updateInputLock(addressInput, true, "Enter full project location & site address...");
            } else {
                let placeholder = "🔒 Step 6: Select Project First...";
                updateInputLock(addressInput, false, placeholder);
            }

            // Step 8: Sector No. (requires Step 7 Address)
            if (hasRegFlat && hasZone && hasDist && hasTown && hasDevName && hasProj && hasAddress) {
                updateInputLock(sectorInput, true, "e.g. Sector 89, Sector 36");
            } else {
                let placeholder = "🔒 Step 7: Enter Address First...";
                updateInputLock(sectorInput, false, placeholder);
            }

            // Step 9: License No., Area of Project & Developer Plan (requires Step 8 Sector No.)
            const step8Ready = (hasRegFlat && hasZone && hasDist && hasTown && hasDevName && hasProj && hasAddress && hasSector);
            if (step8Ready) {
                updateInputLock(licenseInput, true, "e.g. LIC-HR-2024-001");
                updateInputLock(areaInput, true, "e.g. 5.5 Acres / 26,000 Sq. Yds");
                if (devPlanInput) {
                    devPlanInput.disabled = false;
                    devPlanInput.classList.remove('opacity-60', 'cursor-not-allowed', 'bg-slate-100');
                    devPlanInput.classList.add('bg-white');
                }
            } else {
                let placeholder = "🔒 Step 8: Enter Sector No. First...";
                updateInputLock(licenseInput, false, placeholder);
                updateInputLock(areaInput, false, placeholder);
                if (devPlanInput) {
                    devPlanInput.disabled = true;
                    devPlanInput.classList.add('opacity-60', 'cursor-not-allowed', 'bg-slate-100');
                    devPlanInput.classList.remove('bg-white');
                }
            }

            // Step 10: Approved of Building Plan (requires Step 9 License & Area)
            const step9Ready = step8Ready && hasLicense && hasArea;
            if (step9Ready) {
                updateInputLock(approvedPlanSelect, true);
                if (!approvedPlanSelect.value && approvedPlanSelect.options && approvedPlanSelect.options.length > 0) {
                    approvedPlanSelect.options[0].textContent = "Choose Approval Status (Yes / No)...";
                }
            } else {
                updateInputLock(approvedPlanSelect, false);
                let placeholder = "🔒 Step 9: Fill License & Area First...";
                if (approvedPlanSelect && approvedPlanSelect.options && approvedPlanSelect.options.length > 0) {
                    approvedPlanSelect.options[0].textContent = placeholder;
                }
            }

            // Step 11: Status of OC (requires Step 10 Approved == 'yes' and approval date)
            const step10Ready = step9Ready && isApprovedPlanYes && hasApprovalDate;
            if (step10Ready) {
                updateInputLock(ocYnSelect, true);
                if (!ocYnSelect.value && ocYnSelect.options && ocYnSelect.options.length > 0) {
                    ocYnSelect.options[0].textContent = "Choose Status of OC (Yes / No)...";
                }
            } else {
                updateInputLock(ocYnSelect, false);
                let placeholder = "🔒 Step 10: Complete Building Plan First...";
                if (ocYnSelect && ocYnSelect.options && ocYnSelect.options.length > 0) {
                    ocYnSelect.options[0].textContent = placeholder;
                }
            }

            // Step 12: Block Selection (depends ONLY on Project, exactly like STP!)
            const addBlkBtn = document.getElementById('btn_save_block_ajax');
            const openAddBlkBtn = document.getElementById('btn_open_add_block');
            if (hasProj) {
                updateSelectLock(blockSelect, true, "Choose a block/tower...");
                if (addBlkBtn) {
                    addBlkBtn.disabled = false;
                    addBlkBtn.classList.remove('opacity-50', 'pointer-events-none');
                }
                if (openAddBlkBtn) {
                    openAddBlkBtn.disabled = false;
                    openAddBlkBtn.classList.remove('opacity-50', 'pointer-events-none');
                }
            } else {
                let placeholder = "🔒 Step 6: Select Project First...";
                updateSelectLock(blockSelect, false, placeholder);
                if (addBlkBtn) {
                    addBlkBtn.disabled = true;
                    addBlkBtn.classList.add('opacity-50', 'pointer-events-none');
                }
                if (openAddBlkBtn) {
                    openAddBlkBtn.disabled = true;
                    openAddBlkBtn.classList.add('opacity-50', 'pointer-events-none');
                }
            }

            // Step 13: Floor & Flat Lock State (requires Block, exactly like STP!)
            if (hasProj && hasBlock) {
                setFlatInputsLock(false);
            } else {
                setFlatInputsLock(true);
            }
        }

        function fetchTowns(districtId, selectedTownId = null) {
            if (!districtId) {
                initSequentialLockState();
                return;
            }
            
            showDropdownLoader('town_id', 'Loading Towns');
            townSelect.innerHTML = '<option value="" disabled selected>Loading towns...</option>';
            $(townSelect).trigger('change.select2');
            
            fetch(`{{ route('ews.developer.towns') }}?district_id=${districtId}`)
                .then(res => res.json())
                .then(data => {
                    hideDropdownLoader('town_id');
                    townSelect.innerHTML = '<option value="" disabled selected>Choose a town...</option>';
                    data.forEach(t => {
                        const isSel = selectedTownId && selectedTownId == t.id ? 'selected' : '';
                        const typeBadge = t.type ? ` (${t.type.toUpperCase()})` : '';
                        townSelect.innerHTML += `<option value="${t.id}" ${isSel}>${t.name.toUpperCase()}${typeBadge}</option>`;
                    });
                    townSelect.innerHTML += '<option value="new">+ Add New Town</option>';
                    
                    if (selectedTownId) {
                        $(townSelect).val(selectedTownId).trigger('change.select2');
                        handleTownChange();
                    } else {
                        $(townSelect).val('').trigger('change.select2');
                        handleTownChange();
                    }
                    initSequentialLockState();
                })
                .catch(err => {
                    hideDropdownLoader('town_id');
                    console.error('Error fetching towns:', err);
                    townSelect.innerHTML = '<option value="" disabled selected>Choose a town...</option><option value="new">+ Add New Town</option>';
                    $(townSelect).trigger('change.select2');
                    initSequentialLockState();
                });
        }

        function updateSelectLock(selectEl, enabled, placeholderText = null) {
            if (!selectEl) return;
            const $el = $(selectEl);
            const s2Data = $el.data('select2');
            const $s2 = (s2Data && s2Data.$container && s2Data.$container.length) ? s2Data.$container : $el.next('.select2-container');

            $el.prop('disabled', !enabled);
            selectEl.disabled = !enabled;

            if (enabled) {
                selectEl.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                if ($s2 && $s2.length) {
                    $s2.removeClass('opacity-60 cursor-not-allowed pointer-events-none select2-container--disabled');
                    $s2.find('.select2-selection').removeClass('bg-slate-100 cursor-not-allowed opacity-60');
                    $s2.find('.select2-selection').attr('tabindex', '0').attr('aria-disabled', 'false');
                }
                if (placeholderText && selectEl.options && selectEl.options.length > 0) {
                    selectEl.options[0].textContent = placeholderText;
                }
            } else {
                selectEl.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                if ($s2 && $s2.length) {
                    $s2.addClass('opacity-60 cursor-not-allowed pointer-events-none select2-container--disabled');
                    $s2.find('.select2-selection').addClass('bg-slate-100 cursor-not-allowed opacity-60');
                    $s2.find('.select2-selection').attr('tabindex', '-1').attr('aria-disabled', 'true');
                }
                if (placeholderText && selectEl.options && selectEl.options.length > 0) {
                    selectEl.options[0].textContent = placeholderText;
                }
            }

            // Update Select2 visible text ONLY if disabled or empty
            if (!enabled || !selectEl.value) {
                const select2Container = document.getElementById(`select2-${selectEl.id}-container`);
                if (select2Container && placeholderText) {
                    select2Container.textContent = placeholderText;
                    select2Container.title = placeholderText;
                }
            }
        }

        function setFlatInputsLock(locked) {
            const isBulk = document.getElementById('bulk_mode') && document.getElementById('bulk_mode').value === '1';
            const singleFloor = document.getElementById('floor_number_single');
            const flatNum = document.getElementById('flat_number');
            const bulkFloor = document.getElementById('floor_number_bulk');
            const fromFlat = document.getElementById('from_flat');
            const toFlat = document.getElementById('to_flat');
            const customFlats = document.getElementById('custom_flat_numbers');
            const submitBtn = document.querySelector('button[type="submit"]');

            if (locked) {
                [singleFloor, flatNum, bulkFloor, fromFlat, toFlat, customFlats].forEach(el => {
                    if (!el) return;
                    el.disabled = true;
                    el.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                    el.classList.remove('bg-white');
                });
                document.querySelectorAll('input[name="flat_number_type"]').forEach(r => r.disabled = true);
                if (flatNum) {
                    flatNum.placeholder = "🔒 Step 12: Select Block First...";
                }
                if (fromFlat) {
                    fromFlat.placeholder = "🔒 Step 12: Select Block First...";
                }
                if (toFlat) {
                    toFlat.placeholder = "🔒 Step 12: Select Block First...";
                }
                if (customFlats) {
                    customFlats.placeholder = "🔒 Step 12: Select Block First...";
                }
                if (submitBtn) {
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } else {
                if (submitBtn) {
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                document.querySelectorAll('input[name="flat_number_type"]').forEach(r => r.disabled = false);

                if (isBulk) {
                    if (singleFloor) { singleFloor.disabled = true; singleFloor.required = false; }
                    if (flatNum) { flatNum.disabled = true; flatNum.required = false; }

                    if (bulkFloor) {
                        bulkFloor.disabled = false;
                        bulkFloor.required = true;
                        bulkFloor.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        bulkFloor.classList.add('bg-white');
                    }
                    if (fromFlat) { fromFlat.placeholder = "e.g. 1"; }
                    if (toFlat) { toFlat.placeholder = "e.g. 10"; }
                    if (customFlats) { customFlats.placeholder = "e.g. 101, 102, 104, 105"; }
                    const currentType = document.querySelector('input[name="flat_number_type"]:checked')?.value || 'range';
                    toggleFlatInputType(currentType);
                } else {
                    if (bulkFloor) { bulkFloor.disabled = true; bulkFloor.required = false; }
                    if (fromFlat) { fromFlat.disabled = true; fromFlat.required = false; }
                    if (toFlat) { toFlat.disabled = true; toFlat.required = false; }
                    if (customFlats) { customFlats.disabled = true; customFlats.required = false; }

                    if (singleFloor) {
                        singleFloor.disabled = false;
                        singleFloor.required = true;
                        singleFloor.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        singleFloor.classList.add('bg-white');
                    }
                    if (flatNum) {
                        flatNum.disabled = false;
                        flatNum.required = true;
                        flatNum.placeholder = "e.g. 101, A-202";
                        flatNum.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                        flatNum.classList.add('bg-white');
                    }
                }
            }
        }

        function fetchProjects(districtId, townId = null, selectedProjectId = null) {
            if (!districtId) {
                initSequentialLockState();
                clearBlocks();
                return;
            }

            if (!townId || townId === 'new') {
                initSequentialLockState();
                projectSelect.innerHTML = '<option value="" disabled selected>🔒 Step 4: Select Town First...</option>';
                $(projectSelect).val('').trigger('change.select2');
                clearBlocks();
                return;
            }
            
            showDropdownLoader('project_id', 'Fetching Projects');
            projectSelect.innerHTML = '<option value="" disabled selected>Loading projects...</option>';
            $(projectSelect).val('').trigger('change.select2');
            
            const zoneInput = document.getElementById('zone_id');
            const zoneParam = zoneInput && zoneInput.value ? `&zone_id=${zoneInput.value}` : '';

            fetch(`{{ route('ews.developer.projects') }}?district_id=${districtId}&town_id=${townId}${zoneParam}`)
                .then(res => res.json())
                .then(data => {
                    hideDropdownLoader('project_id');
                    projectSelect.innerHTML = '<option value="" disabled selected>Choose a project...</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(proj => {
                            const isSel = selectedProjectId && selectedProjectId == proj.id ? 'selected' : '';
                            const abbrTag = proj.project_abbr ? ` [${proj.project_abbr}]` : '';
                            projectSelect.innerHTML += `<option value="${proj.id}" ${isSel}
                                data-developer="${proj.developer_name || ''}"
                                data-sector="${proj.sector_no || ''}"
                                data-address="${proj.address || ''}"
                                data-license="${proj.license_no || ''}"
                                data-area="${proj.area_of_project || ''}"
                                data-approved="${proj.approved_building_plan || ''}"
                                data-approval-date="${proj.building_plan_approval_date || ''}"
                                data-oc-yn="${proj.oc_status_yn || ''}">${proj.name.toUpperCase()}${abbrTag}</option>`;
                        });
                    }
                    projectSelect.innerHTML += '<option value="new">+ Add New Project</option>';
                    
                    if (selectedProjectId) {
                        $(projectSelect).val(selectedProjectId).trigger('change');
                    } else {
                        $(projectSelect).val('').trigger('change.select2');
                        handleProjectChange();
                    }
                    // Enforce lock according to Step 4 (License No)
                    initSequentialLockState();
                })
                .catch(err => {
                    hideDropdownLoader('project_id');
                    console.error('Error fetching projects:', err);
                    projectSelect.innerHTML = '<option value="" disabled selected>Choose a project...</option><option value="new">+ Add New Project</option>';
                    $(projectSelect).val('').trigger('change.select2');
                    initSequentialLockState();
                });
        }


        // Client-Side Fuzzy & Duplicate Similarity Detection Engine
        function normalizeStr(str) {
            if (!str) return '';
            let s = str.toLowerCase().trim();
            s = s.replace(/\s*\([^)]*\)/g, '');
            return s.replace(/[^a-z0-9]/g, '');
        }

        function collapseRepeats(str) {
            if (!str) return '';
            return str.replace(/(.)\1+/g, '$1');
        }

        function levenshteinDistance(s1, s2) {
            const m = s1.length, n = s2.length;
            const d = [];
            for (let i = 0; i <= m; i++) d[i] = [i];
            for (let j = 0; j <= n; j++) d[0][j] = j;
            for (let j = 1; j <= n; j++) {
                for (let i = 1; i <= m; i++) {
                    if (s1[i - 1] === s2[j - 1]) {
                        d[i][j] = d[i - 1][j - 1];
                    } else {
                        d[i][j] = Math.min(d[i - 1][j] + 1, d[i][j - 1] + 1, d[i - 1][j - 1] + 1);
                    }
                }
            }
            return d[m][n];
        }

        function calculateSimilarityScore(s1, s2) {
            const lev = levenshteinDistance(s1, s2);
            const maxLen = Math.max(s1.length, s2.length);
            if (maxLen === 0) return 100;
            return ((maxLen - lev) / maxLen) * 100;
        }

        function findDuplicateOrSimilar(input, optionsList, threshold = 85.0) {
            const cleanInput = (input || '').trim();
            if (!cleanInput) return null;

            const normInput = normalizeStr(cleanInput);
            if (!normInput) return null;

            const collapsedInput = collapseRepeats(normInput);

            for (let i = 0; i < optionsList.length; i++) {
                let opt = optionsList[i];
                let rawText = typeof opt === 'string' ? opt : (opt.text || opt.name || '');
                let cleanOpt = rawText.trim();
                if (!cleanOpt || cleanOpt.startsWith('+ Add') || cleanOpt.startsWith('Choose') || cleanOpt.startsWith('🔒') || cleanOpt.startsWith('Loading')) {
                    continue;
                }

                // 1. Exact case-insensitive match
                if (cleanInput.toLowerCase() === cleanOpt.toLowerCase()) {
                    return { match: true, existing: cleanOpt, reason: 'Exact match' };
                }

                const normOpt = normalizeStr(cleanOpt);
                if (!normOpt) continue;

                // 2. Canonical match (without spaces, hyphens, punctuation)
                if (normInput === normOpt) {
                    return { match: true, existing: cleanOpt, reason: 'Identical (ignoring spaces & punctuation)' };
                }

                // 3. Repeated character match (e.g. behaat vs behat, aanandkamboj vs anand kamboj)
                const collapsedOpt = collapseRepeats(normOpt);
                if (collapsedInput === collapsedOpt) {
                    return { match: true, existing: cleanOpt, reason: 'Duplicate with repeated characters' };
                }

                // 4. Levenshtein / Edit distance
                const lev = levenshteinDistance(normInput, normOpt);
                const score = calculateSimilarityScore(normInput, normOpt);
                const minLen = Math.min(normInput.length, normOpt.length);

                if (minLen >= 3 && lev === 1 && score >= 80.0) {
                    return { match: true, existing: cleanOpt, reason: 'Almost identical spelling' };
                }
                if (minLen >= 8 && lev <= 2 && score >= 85.0) {
                    return { match: true, existing: cleanOpt, reason: 'Almost identical spelling' };
                }
                if (score >= threshold && minLen >= 4) {
                    return { match: true, existing: cleanOpt, reason: Math.round(score) + '% similar name' };
                }
            }
            return null;
        }

        let previousTownValue = '';

        function openAddTownModal() {
            const distId = districtSelect ? districtSelect.value : '';
            const distText = districtSelect && districtSelect.selectedIndex >= 0 && districtSelect.options[districtSelect.selectedIndex] ? 
                districtSelect.options[districtSelect.selectedIndex].text.trim() : '';
            const zoneName = (document.getElementById('zone_name') ? document.getElementById('zone_name').value : '') || 'Zone';

            if (!distId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'District Required',
                    text: 'Please select a District first before adding a Town.',
                    confirmButtonColor: '#0284c7'
                });
                if (townSelect && townSelect.value === 'new') {
                    townSelect.value = previousTownValue || '';
                    $(townSelect).val(previousTownValue || '').trigger('change.select2');
                }
                return;
            }

            // Populate context labels
            const distSpan = document.getElementById('modal_town_dist_name');
            const zoneSpan = document.getElementById('modal_town_zone_name');
            const noticeDist = document.getElementById('modal_notice_dist');
            const noticeZone = document.getElementById('modal_notice_zone');
            if (distSpan) distSpan.textContent = distText;
            if (zoneSpan) zoneSpan.textContent = zoneName;
            if (noticeDist) noticeDist.textContent = distText;
            if (noticeZone) noticeZone.textContent = zoneName;

            // Reset inputs
            const townInput = document.getElementById('modal_new_town_name');
            const typeSelect = document.getElementById('modal_new_town_type');
            const customTypeInput = document.getElementById('modal_custom_town_type');
            const alertBox = document.getElementById('modal_town_similarity_alert');
            if (townInput) townInput.value = '';
            if (typeSelect) typeSelect.value = '';
            if (customTypeInput) customTypeInput.value = '';
            if (alertBox) alertBox.classList.add('hidden');
            toggleCustomTownTypeModal('');

            const modal = document.getElementById('modal_add_town');
            const content = document.getElementById('modal_add_town_content');
            if (modal && content) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                    if (townInput) townInput.focus();
                }, 20);
            }
        }

        function closeAddTownModal() {
            const modal = document.getElementById('modal_add_town');
            const content = document.getElementById('modal_add_town_content');
            if (modal && content) {
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }, 200);
            }

            // If townSelect is on 'new', reset to previous value
            if (townSelect && townSelect.value === 'new') {
                townSelect.value = previousTownValue || '';
                $(townSelect).val(previousTownValue || '').trigger('change.select2');
            }
        }

        function toggleCustomTownTypeModal(val) {
            const customContainer = document.getElementById('modal_custom_town_type_container');
            const customInput = document.getElementById('modal_custom_town_type');
            if (val === 'other') {
                if (customContainer) customContainer.classList.remove('hidden');
                if (customInput) {
                    customInput.required = true;
                    customInput.focus();
                }
            } else {
                if (customContainer) customContainer.classList.add('hidden');
                if (customInput) {
                    customInput.required = false;
                    customInput.value = '';
                }
            }
        }

        function saveNewTownAjax() {
            const districtId = districtSelect ? districtSelect.value : '';
            const townInput = document.getElementById('modal_new_town_name');
            const townName = townInput ? townInput.value.trim() : '';
            const townTypeSelect = document.getElementById('modal_new_town_type');
            let townType = townTypeSelect ? townTypeSelect.value : '';

            if (!districtId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'District Required',
                    text: 'Please select a district first before creating a town.',
                    confirmButtonColor: '#0284c7'
                });
                return;
            }

            if (!townName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Town Name Required',
                    text: 'Please enter the new town name.',
                    confirmButtonColor: '#0284c7'
                });
                if (townInput) townInput.focus();
                return;
            }

            // Client-side Duplicate & Similarity Check
            const townOptions = Array.from(townSelect.options).map(o => o.text);
            const dupCheck = findDuplicateOrSimilar(townName, townOptions);
            if (dupCheck) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Already Exists / Similar Name Found',
                    html: `Town '<strong>${townName}</strong>' already exists or is too similar to existing town '<strong>${dupCheck.existing}</strong>' (${dupCheck.reason}).<br><br>Please select <strong>${dupCheck.existing}</strong> from the dropdown list.`,
                    confirmButtonColor: '#f59e0b'
                });
                if (townInput) townInput.focus();
                return;
            }

            if (townType === 'other') {
                const customTypeInput = document.getElementById('modal_custom_town_type');
                townType = customTypeInput ? customTypeInput.value.trim() : '';
                if (!townType) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Type Required',
                        text: 'Please enter the custom municipality type.',
                        confirmButtonColor: '#0284c7'
                    });
                    if (customTypeInput) customTypeInput.focus();
                    return;
                }
            } else if (!townType) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Type Required',
                    text: 'Please select a Municipality / Town Type.',
                    confirmButtonColor: '#0284c7'
                });
                if (townTypeSelect) townTypeSelect.focus();
                return;
            }

            const saveBtn = document.getElementById('modal_btn_save_town');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> <span>Saving...</span>';
            }

            fetch('{{ route("ews.developer.towns.store-ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    district_id: districtId,
                    town_name: townName,
                    town_type: townType
                })
            })
            .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
            .then(({ ok, data }) => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-plus-circle-fill text-xs"></i> <span>Save Town</span>';
                }

                if (!ok || !data.success) {
                    Swal.fire({
                        icon: data.duplicate ? 'warning' : 'error',
                        title: data.duplicate ? 'Already Exists / Similar Name' : 'Cannot Save Town',
                        text: data.message || 'Could not save town.',
                        confirmButtonColor: data.duplicate ? '#f59e0b' : '#ef4444'
                    });
                    return;
                }

                if (data.success && data.town) {
                    let opt = Array.from(townSelect.options).find(o => o.value == data.town.id);
                    const typeBadge = data.town.type ? ` (${data.town.type.toUpperCase()})` : '';
                    if (!opt) {
                        const newOption = document.createElement('option');
                        newOption.value = data.town.id;
                        newOption.textContent = `${data.town.name.toUpperCase()}${typeBadge}`;
                        
                        const addNewOpt = Array.from(townSelect.options).find(o => o.value === 'new');
                        if (addNewOpt) {
                            townSelect.insertBefore(newOption, addNewOpt);
                        } else {
                            townSelect.appendChild(newOption);
                        }
                    }

                    // Add new type to modal type dropdown if it was custom
                    if (data.town.type && townTypeSelect) {
                        const existsType = Array.from(townTypeSelect.options).some(o => o.value.toLowerCase() === data.town.type.toLowerCase());
                        if (!existsType) {
                            const newTypeOpt = document.createElement('option');
                            newTypeOpt.value = data.town.type;
                            newTypeOpt.textContent = data.town.type;
                            const otherOpt = Array.from(townTypeSelect.options).find(o => o.value === 'other');
                            if (otherOpt) {
                                townTypeSelect.insertBefore(newTypeOpt, otherOpt);
                            } else {
                                townTypeSelect.appendChild(newTypeOpt);
                            }
                        }
                    }

                    previousTownValue = data.town.id;
                    townSelect.value = data.town.id;
                    $(townSelect).val(data.town.id).trigger('change');
                    townSelect.dispatchEvent(new Event('change'));
                    
                    closeAddTownModal();

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message || `Town '${data.town.name}' saved successfully!`
                    });

                    handleTownChange();
                }
            })
            .catch(err => {
                console.error(err);
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-plus-circle-fill text-xs"></i> <span>Save Town</span>';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'An error occurred while saving the town.',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        function saveNewProjectAjax() {
            const districtId = districtSelect ? districtSelect.value : '';
            const townId = townSelect ? townSelect.value : '';
            const projInput = document.getElementById('new_project_name');
            const projName = projInput ? projInput.value.trim() : '';
            const saveBtn = document.getElementById('btn_save_project_ajax');

            if (!districtId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'District Required',
                    text: 'Please select Step 3: District first before creating a project.',
                    confirmButtonColor: '#0284c7'
                });
                return;
            }

            if (!townId || townId === 'new') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Town Required',
                    text: 'Please select Step 4: Name of Town first before creating a project.',
                    confirmButtonColor: '#0284c7'
                });
                return;
            }

            if (!projName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Project Name Required',
                    text: 'Please enter the new project name.',
                    confirmButtonColor: '#0284c7'
                });
                if (projInput) projInput.focus();
                return;
            }

            // Client-side Duplicate & Similarity Check
            const projOptions = Array.from(projectSelect.options).map(o => o.text);
            const dupCheck = findDuplicateOrSimilar(projName, projOptions);
            if (dupCheck) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Already Exists / Similar Name Found',
                    html: `Project '<strong>${projName}</strong>' already exists or is too similar to existing project '<strong>${dupCheck.existing}</strong>' (${dupCheck.reason}).<br><br>Please select <strong>${dupCheck.existing}</strong> from the dropdown list.`,
                    confirmButtonColor: '#f59e0b'
                });
                if (projInput) projInput.focus();
                return;
            }

            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Saving...';
            }

            fetch('{{ route("ews.developer.projects.store-ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    district_id: districtId,
                    town_id: townId,
                    zone_id: document.getElementById('zone_id') ? document.getElementById('zone_id').value : '',
                    developer_name: document.getElementById('developer_name') ? document.getElementById('developer_name').value.trim() : '',
                    project_name: projName
                })
            })
            .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
            .then(({ ok, data }) => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-plus-circle-fill"></i> <span>Save Project</span>';
                }

                if (!ok || !data.success) {
                    Swal.fire({
                        icon: data.duplicate ? 'warning' : 'error',
                        title: data.duplicate ? 'Already Exists / Similar Name' : 'Cannot Save Project',
                        text: data.message || 'Could not save project.',
                        confirmButtonColor: data.duplicate ? '#f59e0b' : '#ef4444'
                    });
                    return;
                }

                if (data.success && data.project) {
                    let opt = Array.from(projectSelect.options).find(o => o.value == data.project.id);
                    if (!opt) {
                        const newOption = document.createElement('option');
                        newOption.value = data.project.id;
                        newOption.textContent = data.project.name.toUpperCase();
                        
                        const addNewOpt = Array.from(projectSelect.options).find(o => o.value === 'new');
                        if (addNewOpt) {
                            projectSelect.insertBefore(newOption, addNewOpt);
                        } else {
                            projectSelect.appendChild(newOption);
                        }
                    }

                    projectSelect.value = data.project.id;
                    $(projectSelect).val(data.project.id).trigger('change.select2');
                    
                    if (newProjectContainer) newProjectContainer.classList.add('hidden');
                    if (newProjectInput) {
                        newProjectInput.required = false;
                        newProjectInput.value = '';
                    }
                    const alertBox = document.getElementById('project_similarity_alert');
                    if (alertBox) alertBox.classList.add('hidden');

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message || `Project '${data.project.name}' saved successfully!`
                    });

                    handleProjectChange();
                }
            })
            .catch(err => {
                console.error(err);
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-plus-circle-fill"></i> <span>Save Project</span>';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'An error occurred while saving the project.',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        function fetchBlocks(projectId, selectedBlockId = null) {
            if (!projectId || projectId === 'new') {
                blockSelect.innerHTML = '<option value="" disabled selected>Choose a block/tower...</option><option value="new">+ Add New Block/Tower</option>';
                updateSelectLock(blockSelect, true, "Choose a block/tower...");
                $(blockSelect).trigger('change.select2');
                handleBlockChange();
                return;
            }
            
            showDropdownLoader('block_id', 'Loading Blocks');
            blockSelect.innerHTML = '<option value="" disabled selected>Loading blocks...</option>';
            $(blockSelect).trigger('change.select2');
            
            const distId = districtSelect ? districtSelect.value : '';
            const townId = townSelect ? townSelect.value : '';
            const zoneInput = document.getElementById('zone_id');
            const zoneParam = zoneInput && zoneInput.value ? `&zone_id=${zoneInput.value}` : '';
            const distParam = distId ? `&district_id=${distId}` : '';
            const townParam = townId && townId !== 'new' ? `&town_id=${townId}` : '';

            fetch(`{{ route('ews.developer.blocks') }}?project_id=${projectId}${townParam}${distParam}${zoneParam}`)
                .then(res => res.json())
                .then(data => {
                    hideDropdownLoader('block_id');
                    blockSelect.innerHTML = '<option value="" disabled selected>Choose a block/tower...</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(blk => {
                            const isSel = selectedBlockId && selectedBlockId == blk.id ? 'selected' : '';
                            blockSelect.innerHTML += `<option value="${blk.id}" ${isSel}>${blk.name.toUpperCase()}</option>`;
                        });
                    }
                    blockSelect.innerHTML += '<option value="new">+ Add New Block/Tower</option>';
                    
                    updateSelectLock(blockSelect, true, "Choose a block/tower...");
                    
                    if (selectedBlockId) {
                        $(blockSelect).val(selectedBlockId).trigger('change.select2');
                        blockSelect.dispatchEvent(new Event('change'));
                    } else {
                        $(blockSelect).val('').trigger('change.select2');
                        handleBlockChange();
                    }
                    initSequentialLockState();
                })
                .catch(err => {
                    hideDropdownLoader('block_id');
                    console.error('Error fetching blocks:', err);
                    blockSelect.innerHTML = '<option value="" disabled selected>Choose a block/tower...</option><option value="new">+ Add New Block/Tower</option>';
                    updateSelectLock(blockSelect, true, "Choose a block/tower...");
                    $(blockSelect).trigger('change.select2');
                    initSequentialLockState();
                });
        }

        function saveNewBlockAjax() {
            const projectId = projectSelect ? projectSelect.value : '';
            const blockInput = document.getElementById('new_block_name');
            const blockName = blockInput ? blockInput.value.trim() : '';
            const saveBtn = document.getElementById('btn_save_block_ajax');

            if (!projectId || projectId === 'new') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Project Required',
                    text: 'Please select a valid Project first before creating a Block/Tower.',
                    confirmButtonColor: '#0284c7'
                });
                return;
            }

            if (!blockName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Block/Tower Name Required',
                    text: 'Please enter the block/tower name or number.',
                    confirmButtonColor: '#0284c7'
                });
                if (blockInput) blockInput.focus();
                return;
            }

            // Client-side Duplicate & Similarity Check
            const blockOptions = Array.from(blockSelect.options).map(o => o.text);
            const dupCheck = findDuplicateOrSimilar(blockName, blockOptions);
            if (dupCheck) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Already Exists / Similar Name Found',
                    html: `Block/Tower '<strong>${blockName}</strong>' already exists or is too similar to existing '<strong>${dupCheck.existing}</strong>' (${dupCheck.reason}) under this project.<br><br>Please select <strong>${dupCheck.existing}</strong> from the dropdown list.`,
                    confirmButtonColor: '#f59e0b'
                });
                if (blockInput) blockInput.focus();
                return;
            }

            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Saving...';
            }

            const distId = districtSelect ? districtSelect.value : '';
            const townId = townSelect ? townSelect.value : '';
            const zoneInput = document.getElementById('zone_id');
            const zoneId = zoneInput ? zoneInput.value : '';

            fetch('{{ route("ews.developer.blocks.store-ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    project_id: projectId,
                    town_id: townId,
                    district_id: distId,
                    zone_id: zoneId,
                    block_name: blockName
                })
            })
            .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
            .then(({ ok, data }) => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-plus-circle-fill"></i> <span>Save Block</span>';
                }

                if (!ok || !data.success) {
                    Swal.fire({
                        icon: data.duplicate ? 'warning' : 'error',
                        title: data.duplicate ? 'Already Exists / Similar Name' : 'Cannot Save Block',
                        text: data.message || 'Could not save block/tower.',
                        confirmButtonColor: data.duplicate ? '#f59e0b' : '#ef4444'
                    });
                    return;
                }

                if (data.success && data.block) {
                    let opt = Array.from(blockSelect.options).find(o => o.value == data.block.id);
                    if (!opt) {
                        const newOption = document.createElement('option');
                        newOption.value = data.block.id;
                        newOption.textContent = data.block.name.toUpperCase();
                        
                        const addNewOpt = Array.from(blockSelect.options).find(o => o.value === 'new');
                        if (addNewOpt) {
                            blockSelect.insertBefore(newOption, addNewOpt);
                        } else {
                            blockSelect.appendChild(newOption);
                        }
                    }

                    blockSelect.value = data.block.id;
                    $(blockSelect).val(data.block.id).trigger('change.select2');
                    
                    if (newBlockContainer) newBlockContainer.classList.add('hidden');
                    if (newBlockInput) {
                        newBlockInput.required = false;
                        newBlockInput.value = '';
                    }
                    const alertBox = document.getElementById('block_similarity_alert');
                    if (alertBox) alertBox.classList.add('hidden');

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message || `Block/Tower '${data.block.name}' saved successfully!`
                    });

                    handleBlockChange();
                }
            })
            .catch(err => {
                console.error(err);
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-plus-circle-fill"></i> <span>Save Block</span>';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'An error occurred while saving the block.',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        function clearBlocks() {
            blockSelect.innerHTML = '<option value="" disabled selected>🔒 Step 6: Select Project First...</option><option value="new">+ Add New Block/Tower</option>';
            updateSelectLock(blockSelect, false, "🔒 Step 6: Select Project First...");
            $(blockSelect).val('').trigger('change.select2');
            handleBlockChange();
            initSequentialLockState();
        }

        function handleTownChange() {
            const townVal = townSelect ? townSelect.value : '';
            const distId = districtSelect ? districtSelect.value : '';

            if (townVal === 'new') {
                openAddTownModal();
                return;
            }

            if (townVal && townVal !== '') {
                previousTownValue = townVal;
                // Fetch projects in the background so options are ready
                fetchProjects(distId, townVal, projectSelect.value);
            } else {
                previousTownValue = '';
                projectSelect.innerHTML = '<option value="" disabled selected>🔒 Step 5: Enter Developer Name First...</option>';
                $(projectSelect).val('').trigger('change.select2');
                if (newProjectContainer) newProjectContainer.classList.add('hidden');
                if (newProjectInput) {
                    newProjectInput.required = false;
                    newProjectInput.value = '';
                }
                clearBlocks();
                setFlatInputsLock(true);
            }
            initSequentialLockState();
        }

        function handleZoneChange() {
            const zoneSelect = document.getElementById('zone_id');
            if (!zoneSelect) return;
            const zoneId = zoneSelect.value;
            const selectedText = zoneSelect.options[zoneSelect.selectedIndex] ? zoneSelect.options[zoneSelect.selectedIndex].text : '';
            const zoneNameInput = document.getElementById('zone_name');
            if (zoneNameInput) {
                zoneNameInput.value = selectedText.replace(/\s*ZONE\s*$/i, '').trim();
            }

            if (!zoneId) {
                districtSelect.innerHTML = '<option value="" disabled selected>🔒 Step 2: Select Zone First...</option>';
                $(districtSelect).val('').trigger('change.select2');
                townSelect.innerHTML = '<option value="" disabled selected>🔒 Step 3: Select District First...</option>';
                $(townSelect).val('').trigger('change.select2');
                projectSelect.innerHTML = '<option value="" disabled selected>🔒 Step 5: Enter Developer Name First...</option>';
                $(projectSelect).val('').trigger('change.select2');
                clearBlocks();
                setFlatInputsLock(true);
                initSequentialLockState();
                return;
            }

            // Fetch districts for this zone strictly from database mapping
            showDropdownLoader('district_id', 'Fetching Districts');
            districtSelect.innerHTML = '<option value="" disabled selected>Loading districts for selected zone...</option>';
            $(districtSelect).trigger('change.select2');

            fetch(`{{ route('ews.developer.districts') }}?zone_id=${zoneId}`)
                .then(res => res.json())
                .then(data => {
                    hideDropdownLoader('district_id');
                    districtSelect.innerHTML = '<option value="" disabled selected>Choose a district...</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(d => {
                            districtSelect.innerHTML += `<option value="${d.id}">${d.name.toUpperCase()}</option>`;
                        });
                    } else {
                        districtSelect.innerHTML = '<option value="" disabled selected>No districts mapped to this zone</option>';
                    }
                    $(districtSelect).val('').trigger('change.select2');
                    
                    // Reset subsequent fields
                    townSelect.innerHTML = '<option value="" disabled selected>🔒 Step 3: Select District First...</option>';
                    $(townSelect).val('').trigger('change.select2');

                    projectSelect.innerHTML = '<option value="" disabled selected>🔒 Step 5: Enter Developer Name First...</option>';
                    $(projectSelect).val('').trigger('change.select2');

                    clearBlocks();
                    setFlatInputsLock(true);
                    initSequentialLockState();
                })
                .catch(err => {
                    hideDropdownLoader('district_id');
                    console.error('Error fetching districts:', err);
                    districtSelect.innerHTML = '<option value="" disabled selected>Choose a district...</option>';
                    $(districtSelect).val('').trigger('change.select2');
                    initSequentialLockState();
                });
        }

        function openAddProjectModal() {
            const distId = districtSelect ? districtSelect.value : '';
            const townId = townSelect ? townSelect.value : '';
            const devName = document.getElementById('developer_name') ? document.getElementById('developer_name').value.trim() : '';
            const distText = districtSelect && districtSelect.selectedIndex >= 0 && districtSelect.options[districtSelect.selectedIndex] ? 
                districtSelect.options[districtSelect.selectedIndex].text.trim() : '';
            const townText = townSelect && townSelect.selectedIndex >= 0 && townSelect.options[townSelect.selectedIndex] ? 
                townSelect.options[townSelect.selectedIndex].text.trim() : '';

            if (!distId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'District Required',
                    text: 'Please select Step 3: District first before adding a Project.',
                    confirmButtonColor: '#0284c7'
                });
                if (projectSelect && projectSelect.value === 'new') {
                    $(projectSelect).val('').trigger('change.select2');
                }
                return;
            }

            if (!townId || townId === 'new') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Town Required',
                    text: 'Please select Step 4: Town first before adding a Project.',
                    confirmButtonColor: '#0284c7'
                });
                if (projectSelect && projectSelect.value === 'new') {
                    $(projectSelect).val('').trigger('change.select2');
                }
                return;
            }

            if (!devName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Developer Name Required',
                    text: 'Please enter Step 5: Developer Name first before adding a Project.',
                    confirmButtonColor: '#0284c7'
                });
                if (projectSelect && projectSelect.value === 'new') {
                    $(projectSelect).val('').trigger('change.select2');
                }
                const devInput = document.getElementById('developer_name');
                if (devInput) devInput.focus();
                return;
            }

            // Set modal context badges
            const distSpan = document.getElementById('modal_proj_dist_name');
            const townSpan = document.getElementById('modal_proj_town_name');
            const devSpan = document.getElementById('modal_proj_dev_name') || document.getElementById('modal_proj_license_no');
            if (distSpan) distSpan.textContent = distText;
            if (townSpan) townSpan.textContent = townText;
            if (devSpan) devSpan.textContent = devName;

            // Reset inputs
            const nameInput = document.getElementById('modal_project_name_input');
            if (nameInput) nameInput.value = '';

            const modal = document.getElementById('modal_add_project');
            const content = document.getElementById('modal_add_project_content');
            if (modal && content) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                    if (nameInput) nameInput.focus();
                }, 20);
            }
        }

        function closeAddProjectModal() {
            const modal = document.getElementById('modal_add_project');
            const content = document.getElementById('modal_add_project_content');
            if (modal && content) {
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }, 200);
            }

            if (projectSelect && projectSelect.value === 'new') {
                $(projectSelect).val('').trigger('change.select2');
            }
        }

        function saveNewProjectModalAjax() {
            const districtId = districtSelect ? districtSelect.value : '';
            const townId = townSelect ? townSelect.value : '';
            const zoneId = document.getElementById('zone_id') ? document.getElementById('zone_id').value : '';
            const developerName = document.getElementById('developer_name') ? document.getElementById('developer_name').value.trim() : '';

            const nameInput = document.getElementById('modal_project_name_input');
            const projName = nameInput ? nameInput.value.trim() : '';
            const saveBtn = document.getElementById('modal_btn_save_project');

            if (!districtId || !townId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'District & Town Required',
                    text: 'Please select District and Town first.',
                    confirmButtonColor: '#0284c7'
                });
                return;
            }

            if (!projName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Project Name Required',
                    text: 'Please enter the project name.',
                    confirmButtonColor: '#0284c7'
                });
                if (nameInput) nameInput.focus();
                return;
            }

            // Client-side Duplicate & Similarity Check
            const projOptions = Array.from(projectSelect.options).map(o => o.text);
            const dupCheck = findDuplicateOrSimilar(projName, projOptions);
            if (dupCheck) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Already Exists / Similar Name Found',
                    html: `Project '<strong>${projName}</strong>' already exists or is too similar to existing project '<strong>${dupCheck.existing}</strong>' (${dupCheck.reason}).<br><br>Please select <strong>${dupCheck.existing}</strong> from the dropdown list.`,
                    confirmButtonColor: '#f59e0b'
                });
                if (nameInput) nameInput.focus();
                return;
            }

            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Saving...';
            }

            fetch('{{ route("ews.developer.projects.store-ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    district_id: districtId,
                    town_id: townId,
                    zone_id: zoneId,
                    developer_name: developerName,
                    project_name: projName
                })
            })
            .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })).catch(() => ({ ok: res.ok, status: res.status, data: {} })))
            .then(({ ok, data }) => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-plus-circle-fill text-xs"></i> <span>Save Project</span>';
                }

                if (!ok || !data.success) {
                    Swal.fire({
                        icon: data.duplicate ? 'warning' : 'error',
                        title: data.duplicate ? 'Already Exists' : 'Cannot Save Project',
                        text: data.message || 'Could not save project.',
                        confirmButtonColor: data.duplicate ? '#f59e0b' : '#ef4444'
                    });
                    return;
                }

                if (data.success && data.project) {
                    let opt = Array.from(projectSelect.options).find(o => o.value == data.project.id);
                    if (!opt) {
                        const newOption = document.createElement('option');
                        newOption.value = data.project.id;
                        newOption.textContent = data.project.name.toUpperCase();
                        
                        const addNewOpt = Array.from(projectSelect.options).find(o => o.value === 'new');
                        if (addNewOpt) {
                            projectSelect.insertBefore(newOption, addNewOpt);
                        } else {
                            projectSelect.appendChild(newOption);
                        }
                    }

                    projectSelect.value = data.project.id;
                    $(projectSelect).val(data.project.id).trigger('change.select2');
                    
                    closeAddProjectModal();

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message || `Project '${data.project.name}' saved successfully!`
                    });

                    try {
                        handleProjectChange();
                    } catch (e) {
                        console.warn('handleProjectChange error:', e);
                        initSequentialLockState();
                    }
                }
            })
            .catch(err => {
                console.error('Save Project Error:', err);
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-plus-circle-fill text-xs"></i> <span>Save Project</span>';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot Save Project',
                    text: (err && err.message) ? err.message : 'An error occurred while saving the project.',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        function handleOcStatusChange() {
            const ocStatus = document.getElementById('oc_status').value;
            const ocFieldsContainer = document.getElementById('oc_fields_container');
            const ocDateWrapper = document.getElementById('oc_date_wrapper');
            const partOcExtra = document.getElementById('part_oc_extra_fields');
            const ocDateInput = document.getElementById('oc_date');
            const flatsOfferedInput = document.getElementById('flats_offered_for_draw');
            const remFlatsInput = document.getElementById('remaining_flats_for_oc');

            if (ocStatus === 'part' || ocStatus === 'complete') {
                if (ocFieldsContainer) ocFieldsContainer.classList.remove('hidden');
                if (ocDateWrapper) ocDateWrapper.classList.remove('hidden');
                if (ocDateInput) ocDateInput.required = true;

                if (ocStatus === 'part') {
                    if (partOcExtra) partOcExtra.classList.remove('hidden');
                    if (flatsOfferedInput) {
                        flatsOfferedInput.required = true;
                    }
                } else {
                    // complete
                    if (partOcExtra) partOcExtra.classList.add('hidden');
                    if (flatsOfferedInput) {
                        flatsOfferedInput.required = false;
                        flatsOfferedInput.value = '';
                    }
                    if (remFlatsInput) {
                        remFlatsInput.value = '';
                    }
                }
            } else {
                if (ocFieldsContainer) ocFieldsContainer.classList.add('hidden');
                if (ocDateWrapper) ocDateWrapper.classList.add('hidden');
                if (partOcExtra) partOcExtra.classList.add('hidden');
                if (ocDateInput) ocDateInput.required = false;
                if (flatsOfferedInput) flatsOfferedInput.required = false;
            }
        }

        function autoCalculateRemainingFlats() {
            // Manual entry - no auto-calculation or coupling between total ews and remaining oc flats
        }

        function toggleNewProjectInline() {
            if (!projectSelect) return;
            const distId = districtSelect ? districtSelect.value : '';
            const townId = townSelect ? townSelect.value : '';
            if (!distId || !townId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'District & Town Required',
                    text: 'Please select Step 3: District and Step 4: Town first.',
                    confirmButtonColor: '#0284c7'
                });
                return;
            }
            $(projectSelect).val('new').trigger('change');
        }

        function handleProjectChange() {
            const val = projectSelect ? projectSelect.value : '';
            if (val === 'new') {
                if (newProjectContainer) {
                    newProjectContainer.classList.remove('hidden');
                    if (newProjectInput) {
                        newProjectInput.required = true;
                        setTimeout(() => {
                            if (newProjectInput) newProjectInput.focus({ preventScroll: true });
                        }, 50);
                    }
                }
                blockSelect.innerHTML = '<option value="" disabled selected>Choose a block/tower...</option><option value="new">+ Add New Block/Tower</option>';
                updateSelectLock(blockSelect, true, "Choose a block/tower...");
                $(blockSelect).val('').trigger('change.select2');
                if (newBlockContainer) newBlockContainer.classList.add('hidden');
                if (newBlockInput) {
                    newBlockInput.required = false;
                    newBlockInput.value = '';
                }
            } else {
                if (newProjectContainer) newProjectContainer.classList.add('hidden');
                if (newProjectInput) {
                    newProjectInput.required = false;
                    newProjectInput.value = '';
                }
                
                if (val && val !== '') {
                    const opt = projectSelect.options[projectSelect.selectedIndex];
                    if (opt) {
                        const dev = opt.getAttribute('data-developer');
                        const sec = opt.getAttribute('data-sector');
                        const addr = opt.getAttribute('data-address');
                        const lic = opt.getAttribute('data-license');
                        const area = opt.getAttribute('data-area');
                        const approved = opt.getAttribute('data-approved');
                        const appDate = opt.getAttribute('data-approval-date');
                        const ocYn = opt.getAttribute('data-oc-yn');

                        if (dev && document.getElementById('developer_name') && !document.getElementById('developer_name').value) {
                            document.getElementById('developer_name').value = dev;
                        }
                        if (sec && document.getElementById('sector_no')) {
                            document.getElementById('sector_no').value = sec;
                        }
                        if (addr && document.getElementById('address_of_project')) {
                            document.getElementById('address_of_project').value = addr;
                        }
                        if (lic && document.getElementById('license_no')) {
                            document.getElementById('license_no').value = lic;
                        }
                        if (area && document.getElementById('area_of_project')) {
                            document.getElementById('area_of_project').value = area;
                        }
                        if (approved && document.getElementById('approved_building_plan')) {
                            document.getElementById('approved_building_plan').value = approved;
                            if (typeof handleApprovedBuildingPlanChange === 'function') handleApprovedBuildingPlanChange();
                        }
                        if (appDate && document.getElementById('building_plan_approval_date')) {
                            document.getElementById('building_plan_approval_date').value = appDate;
                        }
                        if (ocYn && document.getElementById('oc_status_yn')) {
                            document.getElementById('oc_status_yn').value = ocYn;
                            if (typeof handleOcStatusYnChange === 'function') handleOcStatusYnChange();
                        }
                    }
                    fetchBlocks(val);
                } else {
                    clearBlocks();
                    setFlatInputsLock(true);
                }
            }
            initSequentialLockState();
        }

        function openAddBlockInline() {
            const projVal = projectSelect ? projectSelect.value : '';
            if (!projVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Project Required',
                    text: 'Please select Step 6: Name of Project first before adding a Block/Tower.',
                    confirmButtonColor: '#0284c7'
                });
                return;
            }
            if (newBlockContainer) {
                newBlockContainer.classList.remove('hidden');
                if (newBlockInput) {
                    newBlockInput.required = true;
                    setTimeout(() => {
                        if (newBlockInput) newBlockInput.focus({ preventScroll: true });
                    }, 50);
                }
            }
            $(blockSelect).val('new').trigger('change.select2');
            setFlatInputsLock(false);
            initSequentialLockState();
        }

        function handleBlockChange() {
            const val = blockSelect ? blockSelect.value : '';
            if (val === 'new') {
                if (newBlockContainer) newBlockContainer.classList.remove('hidden');
                if (newBlockInput) {
                    newBlockInput.required = true;
                    setTimeout(() => { if (newBlockInput) newBlockInput.focus({ preventScroll: true }); }, 50);
                }
                setFlatInputsLock(false);
            } else {
                if (newBlockContainer) newBlockContainer.classList.add('hidden');
                if (newBlockInput) {
                    newBlockInput.required = false;
                    newBlockInput.value = '';
                }
                if (val && val !== '') {
                    setFlatInputsLock(false);
                } else {
                    setFlatInputsLock(true);
                }
            }
            initSequentialLockState();
        }

// duplicate handleZoneChange removed

        function handleDistrictChange() {
            if (!districtSelect) return;
            const distId = districtSelect.value;
            if (distId) {
                fetchTowns(distId);
                projectSelect.innerHTML = '<option value="" disabled selected>🔒 Step 4: Select Town First...</option>';
                $(projectSelect).val('').trigger('change.select2');
            } else {
                townSelect.innerHTML = '<option value="" disabled selected>🔒 Step 3: Select District First...</option>';
                $(townSelect).val('').trigger('change.select2');
                projectSelect.innerHTML = '<option value="" disabled selected>🔒 Step 3: Select District First...</option>';
                $(projectSelect).val('').trigger('change.select2');
            }
            clearBlocks();
            setFlatInputsLock(true);
            initSequentialLockState();
        }

        // Live Input Similarity Checkers
        const townModalInput = document.getElementById('modal_new_town_name');
        if (townModalInput) {
            townModalInput.addEventListener('input', function() {
                const alertBox = document.getElementById('modal_town_similarity_alert');
                const alertMsg = document.getElementById('modal_town_similarity_msg') || document.getElementById('modal_town_similarity_message');
                const townOptions = Array.from(townSelect.options).map(o => o.text);
                const dup = findDuplicateOrSimilar(this.value, townOptions);
                if (dup && alertBox && alertMsg) {
                    alertMsg.innerHTML = `<strong>Attention:</strong> Similar town '<strong>${dup.existing}</strong>' already exists in the dropdown (${dup.reason}). Please select it instead.`;
                    alertBox.classList.remove('hidden');
                } else if (alertBox) {
                    alertBox.classList.add('hidden');
                }
            });
        }

        const projInputEl = document.getElementById('new_project_name');
        if (projInputEl) {
            projInputEl.addEventListener('input', function() {
                const alertBox = document.getElementById('project_similarity_alert');
                const alertMsg = document.getElementById('project_similarity_msg');
                const projOptions = Array.from(projectSelect.options).map(o => o.text);
                const dup = findDuplicateOrSimilar(this.value, projOptions);
                if (dup && alertBox && alertMsg) {
                    alertMsg.innerHTML = `<strong>Attention:</strong> Similar project '<strong>${dup.existing}</strong>' already exists in the dropdown (${dup.reason}). Please select it instead.`;
                    alertBox.classList.remove('hidden');
                } else if (alertBox) {
                    alertBox.classList.add('hidden');
                }
            });
        }

        const blockInputEl = document.getElementById('new_block_name');
        if (blockInputEl) {
            blockInputEl.addEventListener('input', function() {
                const alertBox = document.getElementById('block_similarity_alert');
                const alertMsg = document.getElementById('block_similarity_msg');
                const blockOptions = Array.from(blockSelect.options).map(o => o.text);
                const dup = findDuplicateOrSimilar(this.value, blockOptions);
                if (dup && alertBox && alertMsg) {
                    alertMsg.innerHTML = `<strong>Attention:</strong> Block/Tower '<strong>${dup.existing}</strong>' already exists in the dropdown (${dup.reason}). Please select it instead.`;
                    alertBox.classList.remove('hidden');
                } else if (alertBox) {
                    alertBox.classList.add('hidden');
                }
            });
        }

        document.getElementById('custom_flat_numbers').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9,\s]/g, '');
        });

        // Initial setup on page load - Hierarchical Project Master (Zone + District + Town)
        if (districtSelect && districtSelect.value) {
            fetchTowns(districtSelect.value, '{{ old("town_id") }}');
            
            @if(old('town_id'))
                fetchProjects(districtSelect.value, '{{ old("town_id") }}', '{{ old("project_id") }}');
            @endif

            @if(old('project_id'))
                fetchBlocks('{{ old("project_id") }}', '{{ old("block_id") }}');
            @endif
        }
        initSequentialLockState();

        // Strict Sequential Validation on Form Submission
        document.getElementById('devCreateForm').addEventListener('submit', function (e) {
            const regFlatVal = document.getElementById('register_flat') ? document.getElementById('register_flat').value.trim() : '';
            const zoneVal = document.getElementById('zone_id') ? document.getElementById('zone_id').value : '';
            const distVal = districtSelect ? districtSelect.value : '';
            const townVal = townSelect ? townSelect.value : '';
            const devNameVal = document.getElementById('developer_name') ? document.getElementById('developer_name').value.trim() : '';
            const projVal = projectSelect ? projectSelect.value : '';
            const sectorVal = document.getElementById('sector_no') ? document.getElementById('sector_no').value.trim() : '';
            const addressVal = document.getElementById('address_of_project') ? document.getElementById('address_of_project').value.trim() : '';
            const licenseVal = document.getElementById('license_no') ? document.getElementById('license_no').value.trim() : '';
            const areaVal = document.getElementById('area_of_project') ? document.getElementById('area_of_project').value.trim() : '';
            const approvedPlanVal = document.getElementById('approved_building_plan') ? document.getElementById('approved_building_plan').value : '';
            const approvalDateVal = document.getElementById('building_plan_approval_date') ? document.getElementById('building_plan_approval_date').value : '';
            const ocYnVal = document.getElementById('oc_status_yn') ? document.getElementById('oc_status_yn').value : '';
            const ocStatusVal = document.getElementById('oc_status') ? document.getElementById('oc_status').value : '';
            const blkVal = blockSelect ? blockSelect.value : '';

            if (!regFlatVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 1 Incomplete', text: 'Please enter Register Flat name or title.', confirmButtonColor: '#0284c7' });
                const el = document.getElementById('register_flat'); if (el) el.focus();
                return;
            }

            if (!zoneVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 2 Incomplete', text: 'Please select a Zone from the dropdown.', confirmButtonColor: '#0284c7' });
                return;
            }

            if (!distVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 3 Incomplete', text: 'Please select a District first.', confirmButtonColor: '#0284c7' });
                return;
            }

            if (!townVal || townVal === 'new') {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 4 Incomplete', text: 'Please select a Town from the list.', confirmButtonColor: '#0284c7' });
                return;
            }

            if (!devNameVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 5 Incomplete', text: 'Please enter Developer Name.', confirmButtonColor: '#0284c7' });
                const el = document.getElementById('developer_name'); if (el) el.focus();
                return;
            }

            if (!projVal || (projVal === 'new' && !document.getElementById('new_project_name').value.trim())) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 6 Incomplete', text: 'Please select or add the Name of Project.', confirmButtonColor: '#0284c7' });
                return;
            }

            if (!sectorVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 7 Incomplete', text: 'Please enter Sector No.', confirmButtonColor: '#0284c7' });
                const el = document.getElementById('sector_no'); if (el) el.focus();
                return;
            }

            if (!addressVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 8 Incomplete', text: 'Please enter Address of Project.', confirmButtonColor: '#0284c7' });
                const el = document.getElementById('address_of_project'); if (el) el.focus();
                return;
            }

            if (!licenseVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 9 Incomplete', text: 'Please enter License No.', confirmButtonColor: '#0284c7' });
                const el = document.getElementById('license_no'); if (el) el.focus();
                return;
            }

            if (!areaVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 9 Incomplete', text: 'Please enter Area of Project.', confirmButtonColor: '#0284c7' });
                const el = document.getElementById('area_of_project'); if (el) el.focus();
                return;
            }

            if (approvedPlanVal !== 'yes') {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Approved Building Plan Mandatory',
                    html: 'Approved Building Plan is <strong>mandatory</strong> to register EWS flats.<br><br>Without an approved building plan, registration cannot proceed or be submitted.',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            if (!approvalDateVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 10 Incomplete', text: 'Please enter Date of Approved for Building Plan.', confirmButtonColor: '#0284c7' });
                const el = document.getElementById('building_plan_approval_date'); if (el) el.focus();
                return;
            }

            if (ocYnVal !== 'yes') {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Occupation Certificate (OC) Mandatory',
                    html: 'Occupation Certificate (OC) is <strong>mandatory</strong> to offer flats for allotment.<br><br>Flats cannot be registered or submitted without an Occupation Certificate.',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            if (!ocStatusVal) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 11 Incomplete', text: 'Please select OC Category (Part / Complete).', confirmButtonColor: '#0284c7' });
                return;
            }

            if (ocStatusVal === 'part') {
                const offeredFlats = document.getElementById('flats_offered_for_draw') ? document.getElementById('flats_offered_for_draw').value.trim() : '';
                if (!offeredFlats) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Part OC Incomplete', text: 'Please specify the number of flats offered for draw for Part OC.', confirmButtonColor: '#0284c7' });
                    return;
                }
            }

            if (!blkVal || (blkVal === 'new' && !document.getElementById('new_block_name').value.trim())) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Step 12 Incomplete', text: 'Please select or add the Block / Tower Number.', confirmButtonColor: '#0284c7' });
                return;
            }

            // Step 13: Validate Floor and Flat (Single or Bulk)
            const isBulk = document.getElementById('bulk_mode') && document.getElementById('bulk_mode').value === '1';
            if (isBulk) {
                const floorBulk = document.getElementById('floor_number_bulk');
                if (!floorBulk || floorBulk.value === '') {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Step 13 Incomplete', text: 'Please select a Floor number.', confirmButtonColor: '#0284c7' });
                    return;
                }
                const flatType = document.querySelector('input[name="flat_number_type"]:checked')?.value || 'range';
                if (flatType === 'range') {
                    const fromVal = document.getElementById('from_flat')?.value?.trim();
                    const toVal = document.getElementById('to_flat')?.value?.trim();
                    if (!fromVal || !toVal) {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Step 13 Incomplete', text: 'Please enter both From and To flat numbers.', confirmButtonColor: '#0284c7' });
                        return;
                    }
                    if (parseInt(toVal, 10) < parseInt(fromVal, 10)) {
                        e.preventDefault();
                        Swal.fire({ icon: 'error', title: 'Invalid Flat Range', text: '"To Flat" sequence must be greater than or equal to "From Flat".', confirmButtonColor: '#ef4444' });
                        return;
                    }
                } else {
                    const customVal = document.getElementById('custom_flat_numbers')?.value?.trim();
                    if (!customVal) {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Step 13 Incomplete', text: 'Please enter comma-separated flat numbers.', confirmButtonColor: '#0284c7' });
                        return;
                    }
                }
            } else {
                const floorSingle = document.getElementById('floor_number_single');
                if (!floorSingle || floorSingle.value === '') {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Step 13 Incomplete', text: 'Please select a Floor number for the flat.', confirmButtonColor: '#0284c7' });
                    return;
                }
                const singleFlatVal = document.getElementById('flat_number')?.value?.trim();
                if (!singleFlatVal) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Step 13 Incomplete', text: 'Please enter Flat Number.', confirmButtonColor: '#0284c7' });
                    const el = document.getElementById('flat_number'); if (el) el.focus();
                    return;
                }
            }

            // Defensive sanitization: Disable inactive fields so no stale/conflicting data is transmitted
            if (isBulk) {
                const floorSingle = document.getElementById('floor_number_single');
                const flatNum = document.getElementById('flat_number');
                if (floorSingle) floorSingle.disabled = true;
                if (flatNum) flatNum.disabled = true;

                const flatType = document.querySelector('input[name="flat_number_type"]:checked')?.value || 'range';
                if (flatType === 'range') {
                    const customFlats = document.getElementById('custom_flat_numbers');
                    if (customFlats) customFlats.disabled = true;
                } else {
                    const fromF = document.getElementById('from_flat');
                    const toF = document.getElementById('to_flat');
                    if (fromF) fromF.disabled = true;
                    if (toF) toF.disabled = true;
                }
            } else {
                const floorBulk = document.getElementById('floor_number_bulk');
                const fromF = document.getElementById('from_flat');
                const toF = document.getElementById('to_flat');
                const customFlats = document.getElementById('custom_flat_numbers');
                const prefixCheck = document.getElementById('floor_prefix_enabled');
                if (floorBulk) floorBulk.disabled = true;
                if (fromF) fromF.disabled = true;
                if (toF) toF.disabled = true;
                if (customFlats) customFlats.disabled = true;
                if (prefixCheck) prefixCheck.disabled = true;
            }

            // Inactive dependent modals/fields cleanup
            if (townSelect && townSelect.value !== 'new') {
                const ntn = document.getElementById('new_town_name');
                const ntt = document.getElementById('new_town_type');
                const ctt = document.getElementById('custom_town_type');
                if (ntn) ntn.disabled = true;
                if (ntt) ntt.disabled = true;
                if (ctt) ctt.disabled = true;
            }
            if (projectSelect && projectSelect.value !== 'new') {
                const npn = document.getElementById('new_project_name');
                const npa = document.getElementById('new_project_address');
                const nps = document.getElementById('new_project_sector');
                if (npn) npn.disabled = true;
                if (npa) npa.disabled = true;
                if (nps) nps.disabled = true;
            }
            if (blockSelect && blockSelect.value !== 'new') {
                const nbn = document.getElementById('new_block_name');
                if (nbn) nbn.disabled = true;
            }
            if (document.getElementById('oc_status')?.value !== 'part') {
                const fod = document.getElementById('flats_offered_for_draw');
                const rfo = document.getElementById('remaining_flats_for_oc');
                if (fod) fod.disabled = true;
                if (rfo) rfo.disabled = true;
            }

            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Saving Entry...';
                setTimeout(() => {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                }, 10);
            }
        });
    </script>
</body>
</html>
