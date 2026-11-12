<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS STP - Edit Flat Specifications</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .code-font { font-family: 'Fira Code', monospace; }
        .dev-shadow { box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08); }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    @include('ews.stp.partials.sidebar')

    <!-- MAIN CONTAINER -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.stp.flats.show', $flat->secure_id) }}" class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 hover:bg-sky-600 hover:text-white transition-all shadow-sm" title="Back to Dossier">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase flex items-center gap-2">
                        <span>Edit EWS Flat Details</span>
                        <span class="px-2 py-0.5 rounded text-[8.5px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200">
                            {{ $flat->flat_code ?? $flat->flat_number }}
                        </span>
                    </h2>
                    <p class="text-[8px] text-slate-400 font-mono uppercase">Modify Statutory Specifications & Re-submit Allotment Limits</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('ews.stp.verify') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase transition flex items-center gap-1 shadow-2xs">
                    <i class="bi bi-patch-check"></i> <span class="hidden sm:inline">Verify Console</span>
                </a>
                <div class="text-right">
                    <div class="text-[10px] text-slate-700 font-bold flex items-center gap-1 justify-end">
                        <span>{{ $user->name }}</span>
                        @if(!empty($displayZoneName))
                            <span class="text-[9px] bg-sky-100 text-sky-800 font-extrabold uppercase px-1.5 py-0.5 rounded border border-sky-200">({{ $displayZoneName }})</span>
                        @endif
                    </div>
                    <div class="text-[8.5px] text-slate-500 font-mono">Zone: <span class="font-bold text-slate-700 uppercase">{{ $displayZoneName ?? 'N/A' }}</span> | Mobile: {{ $user->mobile }}</div>
                </div>
            </div>
        </header>

        <!-- Form Workspace -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 custom-scroll">
            
            <div class="w-full max-w-4xl mx-auto space-y-4">
                
                @if(session('error'))
                    <div class="p-3.5 bg-rose-50 border border-rose-300 rounded-xl text-rose-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                        <i class="bi bi-exclamation-octagon-fill text-rose-600 text-base shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 bg-rose-50 border border-rose-300 rounded-xl text-rose-800 text-xs shadow-xs space-y-1">
                        <div class="font-black uppercase tracking-wider flex items-center gap-1.5">
                            <i class="bi bi-exclamation-triangle-fill text-rose-600"></i> Please resolve the following errors:
                        </div>
                        <ul class="list-disc list-inside text-[11px] font-medium space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Card -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm dev-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-150 bg-slate-50/50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                                    <i class="bi bi-pencil-square text-sky-500 text-sm"></i>
                                    Flat Specification Modification Form
                                </h3>
                                <span class="text-[8.5px] font-black uppercase px-2 py-0.5 rounded bg-sky-100 text-sky-800 border border-sky-200">
                                    Developer Form Parity
                                </span>
                            </div>
                            <p class="text-[8px] text-slate-400 font-mono mt-0.5 uppercase">
                                All changes are tracked in the system audit trail with user identity and field difference values.
                            </p>
                        </div>
                        <div class="text-[9px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200">
                            UNIT ID: #{{ $flat->id }}
                        </div>
                    </div>

                    <!-- Form Content -->
                    @if(!empty($flat->is_aproved_by_dtp))
                        <div class="mx-6 mt-6 p-4 bg-emerald-50 border-2 border-emerald-400 rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                                    <i class="bi bi-lock-fill"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black uppercase text-emerald-900 tracking-wide">Dossier Officially Approved & Locked</h4>
                                    <p class="text-xs text-emerald-700 mt-0.5">This EWS Flat has already been verified and certified by STP. No modifications or re-submissions are permitted.</p>
                                </div>
                            </div>
                            <a href="{{ route('ews.stp.flats.show', $flat->secure_id) }}"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase rounded-xl shadow-xs transition flex items-center gap-1.5 shrink-0">
                                <i class="bi bi-eye"></i> View Dossier
                            </a>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('ews.stp.flats.update', $flat->secure_id) }}" enctype="multipart/form-data" class="p-6 space-y-4" id="stpEditForm">
                        @csrf
                        @method('PUT')

                        <!-- Row 1: Register Flat & Zone -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Step 1: Register Flat Input Field -->
                            <div class="space-y-1">
                                <label for="register_flat" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-emerald-100 text-emerald-800 me-1.5">Step 1</span>
                                    <span>Register Flat / Title</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="register_flat" name="register_flat" 
                                        value="{{ old('register_flat', $flat->register_flat ?: ($flat->flat_code ?: ('Flat Unit ' . $flat->flat_number))) }}" 
                                        placeholder="Enter flat title / registry name" required autocomplete="off"
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-pencil-square"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Step 2: Zone Dropdown (Locked to Assigned Zone) -->
                            @php
                                $selectedZoneId = $zone->id ?? ($flat->zone_id ?? ($user->zone_id ?? 2));
                                $zoneLabel = $displayZoneName ?? ($zone->name ?? 'ROHTAK ZONE');
                                if (!str_contains(strtoupper($zoneLabel), 'ZONE')) {
                                    $zoneLabel .= ' ZONE';
                                }
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 2</span>
                                        <span>Assigned Zone</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <span class="text-[8.5px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.2 rounded flex items-center gap-1">
                                        <i class="bi bi-lock-fill text-[9px]"></i> Locked
                                    </span>
                                </div>
                                <input type="hidden" name="zone_id" id="zone_id" value="{{ $selectedZoneId }}">
                                <input type="hidden" name="zone_name" id="zone_name" value="{{ $zoneLabel }}">
                                <div class="relative">
                                    <input type="text" readonly disabled
                                        value="{{ strtoupper($zoneLabel) }}"
                                        class="w-full bg-slate-100 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-700 font-extrabold cursor-not-allowed uppercase shadow-inner" />
                                    <span class="absolute right-3 top-2 text-slate-400">
                                        <i class="bi bi-shield-lock-fill text-sky-600"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: District & Town -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Step 3: District Dropdown -->
                            <div class="space-y-1">
                                <label for="district_id" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 3</span>
                                    <span>Select District</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <select id="district_id" name="district_id" required onchange="onDistrictChanged()"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a district...</option>
                                    @foreach($districts as $d)
                                        <option value="{{ $d->id }}" {{ (old('district_id', $flat->district_id) == $d->id) ? 'selected' : '' }}>
                                            {{ strtoupper($d->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Step 4: Town Dropdown -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label for="town_id" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 4</span>
                                        <span>Name of Town</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <button type="button" onclick="openAddTownModal()" class="inline-flex items-center gap-1 text-[9.5px] font-bold text-sky-600 hover:text-sky-800 bg-sky-50 hover:bg-sky-100 border border-sky-200 hover:border-sky-300 px-2 py-0.5 rounded shadow-2xs transition-all cursor-pointer">
                                        <i class="bi bi-plus-circle-fill text-[10px]"></i>
                                        <span>Add Town</span>
                                    </button>
                                </div>
                                <select id="town_id" name="town_id" required onchange="onTownChanged()"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a town...</option>
                                    @foreach($towns as $town)
                                        <option value="{{ $town->id }}" {{ old('town_id', $flat->town_id) == $town->id ? 'selected' : '' }}>
                                            {{ strtoupper($town->name) }}{{ !empty($town->type) ? ' (' . strtoupper($town->type) . ')' : '' }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Add New Town</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Developer Name & Name of Project -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Step 5: Developer Name -->
                            <div class="space-y-1">
                                <label for="developer_name" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 5</span>
                                    <span>Colonizer / Developer Name</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="developer_name" name="developer_name" 
                                        value="{{ old('developer_name', $flat->developer_name ?: ($project->developer_name ?? '')) }}" 
                                        autocomplete="off" placeholder="Enter developer / colonizer name" required
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-building"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Step 6: Project Selection with "+ Add Project" -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label for="project_id" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 6</span>
                                        <span>Name of Project</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <button type="button" onclick="toggleNewProjectInline()" 
                                        class="inline-flex items-center gap-1 text-[9.5px] font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 hover:border-emerald-400 px-2 py-0.5 rounded shadow-2xs transition-all cursor-pointer">
                                        <i class="bi bi-plus-circle-fill text-[10px]"></i>
                                        <span>Add Project</span>
                                    </button>
                                </div>
                                <select id="project_id" name="project_id" required onchange="onProjectChanged()"
                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a project...</option>
                                    @foreach($projects as $proj)
                                        <option value="{{ $proj->id }}" {{ old('project_id', $flat->project_id) == $proj->id ? 'selected' : '' }}
                                            data-developer="{{ $proj->developer_name ?? '' }}"
                                            data-sector="{{ $proj->sector_no ?? '' }}" 
                                            data-address="{{ $proj->address ?? '' }}"
                                            data-license="{{ $proj->license_no ?? '' }}" 
                                            data-area="{{ $proj->area_of_project ?? '' }}"
                                            data-approved="{{ $proj->approved_building_plan ?? '' }}"
                                            data-approval-date="{{ $proj->building_plan_approval_date ?? '' }}"
                                            data-oc-yn="{{ $proj->oc_status_yn ?? '' }}">
                                            {{ strtoupper($proj->name) }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Add New Project</option>
                                </select>

                                <!-- Inline Add Project Input -->
                                <div id="new_project_container" class="hidden mt-2">
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="new_project_name" name="new_project_name" placeholder="Enter new project name"
                                            class="w-full bg-white border border-sky-400 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                        <button type="button" onclick="saveNewProjectAjax()"
                                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-black uppercase whitespace-nowrap flex items-center gap-1 shadow-sm transition-all shrink-0 cursor-pointer">
                                            <i class="bi bi-plus-circle-fill"></i>
                                            <span>Save Project</span>
                                        </button>
                                    </div>
                                    <p class="text-[8.5px] text-slate-400 mt-1 italic">Click 'Save Project' to immediately persist in master projects table.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4: Address of Project & Sector No. -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Step 7: Address of Project -->
                            <div class="space-y-1">
                                <label for="address_of_project" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 7</span>
                                    <span>Address of Project</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <textarea id="address_of_project" name="address_of_project" rows="2" placeholder="Enter complete project site address" required
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none resize-none">{{ old('address_of_project', $flat->address_of_project ?: ($project->address ?? '')) }}</textarea>
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-signpost-2-fill text-emerald-500"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Step 8: Sector No. -->
                            <div class="space-y-1">
                                <label for="sector_no" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 8</span>
                                    <span>Sector No.</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="sector_no" name="sector_no" 
                                        value="{{ old('sector_no', $flat->sector_no ?: ($project->sector_no ?? '')) }}" 
                                        placeholder="e.g. 89 or Sector 89" required autocomplete="off"
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-geo-alt-fill text-sky-500"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Row 5: License No., Area of Project & Developer Plan (PDF) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-3.5 bg-slate-50/90 border border-slate-200 rounded-xl">
                            <!-- Step 9: License No. -->
                            <div class="space-y-1">
                                <label for="license_no" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 9</span>
                                        <span>License No.</span> <span class="text-red-500 ms-0.5">*</span>
                                    </div>
                                    <button type="button" onclick="fetchTcpLicenceForEditForm()" class="text-[8px] font-bold text-sky-600 hover:text-sky-800 uppercase flex items-center gap-0.5 cursor-pointer">
                                        <i class="bi bi-cloud-arrow-down-fill"></i> Fetch TCP Haryana
                                    </button>
                                </label>
                                <div class="relative">
                                    <input type="text" id="license_no" name="license_no" 
                                        value="{{ old('license_no', $flat->license_no ?: ($project->license_no ?? '')) }}" 
                                        placeholder="e.g. LIC-hr-01" required autocomplete="off"
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-mono">
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
                                    <input type="text" id="area_of_project" name="area_of_project" 
                                        value="{{ old('area_of_project', $flat->area_of_project ?: ($project->area_of_project ?? '')) }}" 
                                        placeholder="e.g. 5.5 Acres / 26,000 Sq. Yds" required autocomplete="off"
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-2.5 text-slate-400">
                                        <i class="bi bi-aspect-ratio text-indigo-500"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Developer Plan (PDF Max 500 KB) -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label for="developer_plan" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                        <span>Developer Plan (PDF)</span>
                                    </label>
                                    @php
                                        $currentDevPlan = $flat->developer_plan ?: ($project->developer_plan ?? null);
                                    @endphp
                                    @if(!empty($currentDevPlan))
                                        <a href="{{ asset('storage/' . $currentDevPlan) }}" target="_blank" 
                                            class="px-1.5 py-0.2 rounded text-[8px] font-extrabold bg-sky-100 text-sky-800 border border-sky-200 inline-flex items-center gap-0.5">
                                            <i class="bi bi-file-earmark-pdf"></i> View Current PDF
                                        </a>
                                    @else
                                        <span class="px-1.5 py-0.2 rounded text-[8px] font-extrabold bg-amber-100 text-amber-800">Max 500 KB</span>
                                    @endif
                                </div>
                                <input type="file" id="developer_plan" name="developer_plan" accept=".pdf"
                                    class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[9.5px] file:font-black file:uppercase file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 file:cursor-pointer border border-slate-250 rounded-lg bg-white p-1">
                                <p class="text-[8px] text-slate-400 font-mono">Leave empty to keep existing document attached.</p>
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

                            @php
                                $planApprovedVal = old('approved_building_plan', $flat->approved_building_plan ?: ($project->approved_building_plan ?? 'yes'));
                            @endphp
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Dropdown: Approved of Building Plan (Yes/No) -->
                                <div class="space-y-1">
                                    <label for="approved_building_plan" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                        Approved of Building Plan <span class="text-red-500">*</span>
                                    </label>
                                    <select id="approved_building_plan" name="approved_building_plan" onchange="handleApprovedBuildingPlanChange()" required
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                        <option value="yes" {{ strtolower($planApprovedVal) === 'yes' ? 'selected' : '' }}>Yes (Approved)</option>
                                        <option value="no" {{ strtolower($planApprovedVal) === 'no' ? 'selected' : '' }}>No (Not Approved)</option>
                                    </select>
                                </div>

                                <!-- Date of Approved -->
                                <div id="building_plan_date_wrapper" class="space-y-1 {{ strtolower($planApprovedVal) === 'yes' ? '' : 'hidden' }}">
                                    <label for="building_plan_approval_date" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                        Date of Approved
                                    </label>
                                    <input type="date" id="building_plan_approval_date" name="building_plan_approval_date" 
                                        value="{{ old('building_plan_approval_date', $flat->building_plan_approval_date ?: ($project->building_plan_approval_date ?? '')) }}"
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none font-mono">
                                </div>

                                <!-- Building Plan Copy -->
                                <div id="building_plan_copy_wrapper" class="space-y-1 {{ strtolower($planApprovedVal) === 'yes' ? '' : 'hidden' }}">
                                    <div class="flex items-center justify-between">
                                        <label for="building_plan_copy" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                            <span>Building Plan Copy (PDF)</span>
                                        </label>
                                        @php
                                            $currentBldgPlan = $flat->building_plan_copy ?: ($project->building_plan_copy ?? null);
                                        @endphp
                                        <span id="existing_bldg_plan_preview" class="{{ (!empty($currentBldgPlan) && strtolower($planApprovedVal) === 'yes') ? '' : 'hidden' }}">
                                            @if(!empty($currentBldgPlan))
                                                <a href="{{ asset('storage/' . $currentBldgPlan) }}" target="_blank" 
                                                    class="px-1.5 py-0.2 rounded text-[8px] font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-200 inline-flex items-center gap-0.5">
                                                    <i class="bi bi-file-earmark-pdf"></i> View Current PDF
                                                </a>
                                            @endif
                                        </span>
                                    </div>
                                    <input type="file" id="building_plan_copy" name="building_plan_copy" accept=".pdf"
                                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[9.5px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer border border-slate-250 rounded-lg bg-white p-1">
                                    <p class="text-[8px] text-slate-400 font-mono">Leave empty to keep existing sanction document.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Row 7: Status of OC Verification Section -->
                        <div class="border border-teal-200/90 rounded-2xl p-4 bg-gradient-to-br from-white via-teal-50/20 to-slate-50/50 space-y-3 shadow-xs">
                            <div class="flex items-center justify-between border-b border-teal-150 pb-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-teal-500/15 text-teal-700 flex items-center justify-center font-bold text-xs shadow-xs">
                                        <i class="bi bi-file-earmark-check-fill text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-teal-100 text-teal-800 border border-teal-200">Step 11</span>
                                            <span>Status of OC (Occupation Certificate)</span>
                                        </h4>
                                        <p class="text-[9px] text-slate-400 font-mono">Statutory OC categorization and unit draw allotment limits</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black text-teal-800 bg-teal-100 border border-teal-200 px-2.5 py-0.5 rounded-full uppercase font-mono shadow-2xs">
                                    Clearance Matrix
                                </span>
                            </div>

                            @php
                                $ocYnVal = old('oc_status_yn', $flat->oc_status_yn ?: ($project->oc_status_yn ?? 'yes'));
                                $ocCatVal = old('oc_status', $flat->oc_status ?: 'part');
                            @endphp

                            <!-- Status of OC & Category Selector (2 Columns) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label for="oc_status_yn" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                        <span>Status of OC <span class="text-red-500">*</span></span>
                                        <span class="text-[8.5px] font-mono text-slate-400">Yes / No</span>
                                    </label>
                                    <select id="oc_status_yn" name="oc_status_yn" onchange="handleOcStatusYnChange(true)" required
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                                        <option value="yes" {{ strtolower($ocYnVal) === 'yes' ? 'selected' : '' }}>Yes (OC Received)</option>
                                        <option value="no" {{ strtolower($ocYnVal) === 'no' ? 'selected' : '' }}>No (OC Not Received)</option>
                                    </select>
                                </div>

                                <!-- Part/Complete Dropdown -->
                                <div id="oc_category_wrapper" class="space-y-1 {{ strtolower($ocYnVal) === 'yes' ? '' : 'hidden' }}">
                                    <label for="oc_status" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                        <span>OC Category (Part / Complete) <span class="text-red-500">*</span></span>
                                        <span class="text-[8.5px] font-mono text-teal-700">Allotment Scope</span>
                                    </label>
                                    <select id="oc_status" name="oc_status" onchange="handleOcStatusChange()"
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
                                        <option value="part" {{ strtolower($ocCatVal) === 'part' ? 'selected' : '' }}>Part (Partial Allotment)</option>
                                        <option value="complete" {{ strtolower($ocCatVal) === 'complete' ? 'selected' : '' }}>Complete (Full Project Completion)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Dynamic OC Fields Container (Grid 4 Symmetrical Columns) -->
                            <div id="oc_fields_container" class="{{ strtolower($ocYnVal) === 'yes' ? '' : 'hidden' }} space-y-3 pt-2 border-t border-slate-200">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5">
                                    <!-- 1. Date of OC -->
                                    <div class="space-y-1">
                                        <label for="oc_date" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                            Date of OC
                                        </label>
                                        <input type="date" id="oc_date" name="oc_date" 
                                            value="{{ old('oc_date', $flat->oc_date ?? '') }}"
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-medium focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none font-mono">
                                    </div>

                                    <!-- 2. Copy Attached OC (PDF) -->
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between">
                                            <label for="oc_copy" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                                <span>Copy OC (PDF)</span>
                                            </label>
                                            <span id="existing_oc_preview" class="{{ (!empty($flat->oc_copy_file) && strtolower($ocYnVal) === 'yes') ? '' : 'hidden' }}">
                                                @if(!empty($flat->oc_copy_file))
                                                    <a href="{{ asset('storage/' . $flat->oc_copy_file) }}" target="_blank" 
                                                        class="px-1.5 py-0.2 rounded text-[8px] font-extrabold bg-teal-100 text-teal-800 border border-teal-200 inline-flex items-center gap-0.5 hover:bg-teal-200 transition-all">
                                                        <i class="bi bi-file-earmark-pdf"></i> View PDF
                                                    </a>
                                                @endif
                                            </span>
                                        </div>
                                        <input type="file" id="oc_copy" name="oc_copy" accept=".pdf"
                                            class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 file:cursor-pointer border border-slate-250 rounded-lg bg-white p-1">
                                    </div>

                                    <!-- 3. Total Flats in Project -->
                                    <div class="space-y-1">
                                        <label for="total_flats_in_project" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                            <span>Total Flats in Project</span>
                                            <span class="text-[8px] font-mono text-slate-400">Total Units</span>
                                        </label>
                                        <input type="number" min="1" id="total_flats_in_project" name="total_flats_in_project" 
                                            value="{{ old('total_flats_in_project', $flat->total_flats_in_project ?: ($project->total_flats_in_project ?? '')) }}" 
                                            placeholder="e.g. 500"
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                    </div>

                                    <!-- 4. Total EWS Flats in Project -->
                                    <div class="space-y-1">
                                        <label for="total_ews_flats_in_project" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                            <span>Total EWS Flats</span>
                                            <span class="text-[8px] font-mono text-teal-700 font-bold">EWS Quota</span>
                                        </label>
                                        <input type="number" min="1" id="total_ews_flats_in_project" name="total_ews_flats_in_project" 
                                            value="{{ old('total_ews_flats_in_project', $flat->total_ews_flats_in_project ?: ($project->total_ews_flats_in_project ?? '')) }}" 
                                            placeholder="e.g. 100"
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                    </div>
                                </div>

                                <!-- Part OC Specific Breakdown (Clean Symmetrical Grid) -->
                                <div id="part_oc_breakdown_section" class="{{ (strtolower($ocYnVal) === 'yes' && strtolower($ocCatVal) === 'part') ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-slate-200">
                                    <!-- Flats Offered for Draw -->
                                    <div class="space-y-1">
                                        <label for="flats_offered_for_draw" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                            Flats Offered for Draw <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" min="0" id="flats_offered_for_draw" name="flats_offered_for_draw" 
                                            value="{{ old('flats_offered_for_draw', $flat->flats_offered_for_draw ?? '') }}" 
                                            placeholder="Enter flats offered for draw"
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                    </div>

                                    <!-- Remaining Flats for OC -->
                                    <div class="space-y-1">
                                        <label for="remaining_flats_for_oc" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                                            Remaining Flats for OC
                                        </label>
                                        <input type="number" min="0" id="remaining_flats_for_oc" name="remaining_flats_for_oc" 
                                            value="{{ old('remaining_flats_for_oc', $flat->remaining_flats_for_oc ?? '') }}" 
                                            placeholder="Enter remaining flats for OC"
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 12, 13 & 14: Unit Identification Strip (3 Harmonious Columns) -->
                        <div class="border border-slate-200 rounded-2xl p-4 bg-gradient-to-br from-white to-slate-50 space-y-3 shadow-xs">
                            <div class="flex items-center justify-between border-b border-slate-150 pb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-500/15 text-indigo-700 flex items-center justify-center font-bold text-xs shadow-xs">
                                        <i class="bi bi-geo-alt-fill text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-indigo-100 text-indigo-800 border border-indigo-200">Steps 12 - 14</span>
                                            <span>Unit Coordinates & Physical Identification</span>
                                        </h4>
                                        <p class="text-[9px] text-slate-400 font-mono">Block / Tower, Floor Position & Flat Registry Number</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black text-indigo-800 bg-indigo-100 border border-indigo-200 px-2.5 py-0.5 rounded-full uppercase font-mono shadow-2xs">
                                    Unit Matrix
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Col 1: Step 12: Block / Tower No. Selection -->
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <label for="block_id" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 12</span>
                                            <span>Block / Tower</span> <span class="text-red-500 ms-0.5">*</span>
                                        </label>
                                        <button type="button" onclick="openAddBlockInline()" class="inline-flex items-center gap-1 text-[9px] font-bold text-sky-700 hover:text-sky-900 bg-sky-50 hover:bg-sky-100 border border-sky-200 px-2 py-0.5 rounded shadow-2xs transition-all cursor-pointer">
                                            <i class="bi bi-plus-circle-fill text-[9.5px]"></i>
                                            <span>Add Block</span>
                                        </button>
                                    </div>
                                    <select id="block_id" name="block_id" required
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                        <option value="" disabled>Choose a block/tower...</option>
                                        @foreach($blocks as $blk)
                                            <option value="{{ $blk->id }}" {{ old('block_id', $flat->block_id) == $blk->id ? 'selected' : '' }}>
                                                {{ strtoupper($blk->name) }}
                                            </option>
                                        @endforeach
                                        <option value="new">+ Add New Block/Tower</option>
                                    </select>
                                    
                                    <!-- Inline Add Block Input -->
                                    <div id="new_block_container" class="hidden mt-2">
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="new_block_name" name="new_block_name" placeholder="e.g. T-02"
                                                class="w-full bg-white border border-sky-400 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                            <button type="button" onclick="saveNewBlockAjax()"
                                                class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-[10px] font-black uppercase whitespace-nowrap flex items-center gap-1 shadow-sm transition-all shrink-0 cursor-pointer">
                                                <i class="bi bi-plus-circle-fill"></i>
                                                <span>Save</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Col 2: Step 13: Floor Level -->
                                @php
                                    $floorStr = strtolower(trim($flat->floor ?? '0'));
                                    $selectedFloor = 0;
                                    if (str_contains($floorStr, 'ground') || $floorStr === '0') {
                                        $selectedFloor = 0;
                                    } elseif (str_contains($floorStr, 'first') || $floorStr === '1') {
                                        $selectedFloor = 1;
                                    } elseif (str_contains($floorStr, 'second') || $floorStr === '2') {
                                        $selectedFloor = 2;
                                    } elseif (str_contains($floorStr, 'third') || $floorStr === '3') {
                                        $selectedFloor = 3;
                                    } elseif (preg_match('/(\d+)/', $floorStr, $m)) {
                                        $selectedFloor = (int)$m[1];
                                    }
                                @endphp
                                <div class="space-y-1">
                                    <label for="floor_number" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-indigo-100 text-indigo-800 me-1.5">Step 13</span>
                                        <span>Floor Level</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <select id="floor_number" name="floor_number" required
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                        <option value="0" {{ old('floor_number', $selectedFloor) == 0 ? 'selected' : '' }}>Ground Floor (0)</option>
                                        @for($f = 1; $f <= 100; $f++)
                                            <option value="{{ $f }}" {{ old('floor_number', $selectedFloor) == $f ? 'selected' : '' }}>Floor {{ $f }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Col 3: Step 14: Flat / Unit Number -->
                                <div class="space-y-1">
                                    <label for="flat_number" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                        <span class="flex items-center">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-violet-100 text-violet-800 me-1.5">Step 14</span>
                                            <span>Flat / Unit No.</span> <span class="text-red-500 ms-0.5">*</span>
                                        </span>
                                        <span class="text-[8.5px] font-mono text-violet-700 font-bold">Numeric / Code</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="flat_number" name="flat_number" 
                                            value="{{ old('flat_number', $flat->flat_number) }}" required
                                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-900 font-black focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none font-mono tracking-wider">
                                        <span class="absolute right-3 top-2.5 text-violet-500 font-bold text-xs">
                                            #
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Flat Code Preview Banner -->
                            <div class="flex items-center justify-between px-3 py-2 bg-slate-900 text-white rounded-xl border border-slate-800 shadow-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-md bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">
                                        <i class="bi bi-fingerprint"></i>
                                    </span>
                                    <span class="text-[9.5px] font-black uppercase tracking-wider text-slate-300">System Unique Flat Code</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span id="preview_flat_code" class="px-2.5 py-0.5 rounded-md text-[10.5px] font-black font-mono bg-cyan-950 text-cyan-300 border border-cyan-800/80 tracking-wider">
                                        {{ $flat->flat_code ?: 'AUTO-GENERATED' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions Bar (Refined & Balanced) -->
                        <div class="pt-3 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <a href="{{ route('ews.stp.flats.show', $flat->secure_id) }}"
                                    class="px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 font-black uppercase tracking-wider rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 border border-slate-300 shadow-2xs w-full sm:w-auto">
                                    <i class="bi bi-arrow-left"></i>
                                    <span>Cancel & Return</span>
                                </a>
                                <span class="hidden md:inline-flex items-center gap-1.5 text-[9px] font-bold text-slate-500 bg-slate-100 px-3 py-2 rounded-xl border border-slate-200">
                                    <i class="bi bi-shield-check text-emerald-600"></i> Audit Log Matrix Active
                                </span>
                            </div>

                            <div class="flex items-center gap-2.5 w-full sm:w-auto flex-wrap justify-end">
                                @if(!empty($flat->is_aproved_by_dtp))
                                    <div class="w-full sm:w-auto px-5 py-2.5 bg-emerald-50 text-emerald-800 font-bold uppercase tracking-wider rounded-xl text-xs flex items-center justify-center gap-2 border border-emerald-300 shadow-2xs">
                                        <i class="bi bi-lock-fill text-emerald-600 text-sm"></i>
                                        <span>Dossier Approved & Locked (Read-Only)</span>
                                    </div>
                                    <a href="{{ route('ews.stp.flats.show', $flat->secure_id) }}"
                                        class="w-full sm:w-auto px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-black uppercase tracking-wider rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 shadow-md">
                                        <i class="bi bi-eye"></i>
                                        <span>View Verified Dossier</span>
                                    </a>
                                @else
                                    <!-- Regular Save Button (Approval only from View page) -->
                                    <button type="submit" id="btn_submit_changes"
                                        class="w-full sm:w-auto px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-black uppercase tracking-wider rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 shadow-md hover:shadow-lg active:scale-95 cursor-pointer">
                                        <i class="bi bi-save2"></i>
                                        <span>Save Changes</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <!-- Modal: Add New Town Popup -->
    <div id="modal_add_town" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all duration-300" onclick="if(event.target === this) closeAddTownModal()">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 flex flex-col overflow-hidden" id="modal_add_town_content">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-150 flex items-center justify-between bg-gradient-to-r from-sky-50 via-white to-slate-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shadow-xs shrink-0">
                        <i class="bi bi-geo-alt-fill text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Add New Town</h3>
                        <p class="text-[10px] text-slate-500 font-medium">Link new municipality to selected district</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddTownModal()" class="w-7 h-7 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-3.5">
                <div class="space-y-1">
                    <label for="modal_new_town_name" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                        Town Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modal_new_town_name" placeholder="Enter town name (e.g. Kharkhoda, Samalkha)"
                        class="w-full bg-slate-50 border border-slate-300 focus:border-sky-500 focus:bg-white rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:outline-none transition-all">
                </div>

                <div class="space-y-1">
                    <label for="modal_new_town_type" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                        Town Type (Municipality) <span class="text-red-500">*</span>
                    </label>
                    <select id="modal_new_town_type" onchange="toggleCustomTownTypeModal(this.value)"
                        class="w-full bg-slate-50 border border-slate-300 focus:border-sky-500 focus:bg-white rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:outline-none transition-all cursor-pointer">
                        <option value="" disabled selected>Select Municipality Type *</option>
                        @if(isset($townTypes))
                            @foreach($townTypes as $tType)
                                <option value="{{ $tType }}">{{ $tType }}</option>
                            @endforeach
                        @endif
                        <option value="other">+ Add New Type / Other</option>
                    </select>
                </div>

                <div id="modal_custom_town_type_container" class="hidden space-y-1 bg-sky-50/60 p-2.5 rounded-lg border border-sky-200">
                    <label for="modal_custom_town_type" class="block text-[9px] font-black uppercase text-sky-800 tracking-wider">
                        Specify Municipality Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modal_custom_town_type" placeholder="e.g. Special Development Area"
                        class="w-full bg-white border border-sky-400 focus:border-sky-600 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:outline-none">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-150 flex items-center justify-end gap-2">
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

    <script>
        // Town Modal Actions
        function openAddTownModal() {
            $('#modal_add_town').removeClass('hidden').addClass('flex');
            $('#modal_new_town_name').val('').focus();
            $('#modal_new_town_type').val('');
            $('#modal_custom_town_type_container').addClass('hidden');
        }

        function closeAddTownModal() {
            $('#modal_add_town').addClass('hidden').removeClass('flex');
        }

        function toggleCustomTownTypeModal(val) {
            if (val === 'other') {
                $('#modal_custom_town_type_container').removeClass('hidden');
            } else {
                $('#modal_custom_town_type_container').addClass('hidden');
            }
        }

        function saveNewTownAjax() {
            const districtId = $('#district_id').val();
            const townName = $('#modal_new_town_name').val().trim();
            let townType = $('#modal_new_town_type').val();
            if (townType === 'other') {
                townType = $('#modal_custom_town_type').val().trim();
            }

            if (!townName) {
                Swal.fire('Required', 'Please enter a town name.', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('ews.stp.towns.store-ajax') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    district_id: districtId,
                    name: townName,
                    type: townType
                },
                success: function(resp) {
                    if (resp.success && resp.town) {
                        const newOpt = new Option(resp.town.name.toUpperCase(), resp.town.id, true, true);
                        $('#town_id').append(newOpt).trigger('change');
                        closeAddTownModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Town Added',
                            text: resp.message || 'New town registered successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', resp.message || 'Could not save town.', 'error');
                    }
                },
                error: function(err) {
                    Swal.fire('Error', err.responseJSON?.message || 'Server error while saving town.', 'error');
                }
            });
        }

        // Inline Project Toggle
        function toggleNewProjectInline() {
            $('#new_project_container').toggleClass('hidden');
            if (!$('#new_project_container').hasClass('hidden')) {
                $('#new_project_name').focus();
            }
        }

        function saveNewProjectAjax() {
            const districtId = $('#district_id').val();
            const townId = $('#town_id').val();
            const projName = $('#new_project_name').val().trim();

            if (!projName) {
                Swal.fire('Required', 'Please enter project name.', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('ews.stp.projects.store-ajax') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    district_id: districtId,
                    town_id: townId,
                    name: projName,
                    developer_name: $('#developer_name').val(),
                    license_no: $('#license_no').val(),
                    sector_no: $('#sector_no').val(),
                    address: $('#address_of_project').val(),
                    area_of_project: $('#area_of_project').val()
                },
                success: function(resp) {
                    if (resp.success && resp.project) {
                        const opt = new Option(resp.project.name.toUpperCase(), resp.project.id, true, true);
                        $('#project_id').append(opt).trigger('change');
                        $('#new_project_container').addClass('hidden');
                        Swal.fire({
                            icon: 'success',
                            title: 'Project Saved',
                            text: resp.message || 'Project registered successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', resp.message || 'Could not save project.', 'error');
                    }
                },
                error: function(err) {
                    Swal.fire('Error', err.responseJSON?.message || 'Server error while saving project.', 'error');
                }
            });
        }

        // Inline Block Toggle
        function openAddBlockInline() {
            $('#new_block_container').toggleClass('hidden');
            if (!$('#new_block_container').hasClass('hidden')) {
                $('#new_block_name').focus();
            }
        }

        function saveNewBlockAjax() {
            const projId = $('#project_id').val();
            const blockName = $('#new_block_name').val().trim();

            if (!blockName) {
                Swal.fire('Required', 'Please enter block / tower name.', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('ews.stp.blocks.store-ajax') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    project_id: projId,
                    name: blockName
                },
                success: function(resp) {
                    if (resp.success && resp.block) {
                        const opt = new Option(resp.block.name.toUpperCase(), resp.block.id, true, true);
                        $('#block_id').append(opt).trigger('change');
                        $('#new_block_container').addClass('hidden');
                        Swal.fire({
                            icon: 'success',
                            title: 'Block Saved',
                            text: resp.message || 'Block registered successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', resp.message || 'Could not save block.', 'error');
                    }
                },
                error: function(err) {
                    Swal.fire('Error', err.responseJSON?.message || 'Server error while saving block.', 'error');
                }
            });
        }

        // Cascading District Change
        function onDistrictChanged() {
            const districtId = $('#district_id').val();
            if (!districtId) return;

            // Fetch Towns
            $.getJSON("{{ route('ews.stp.towns') }}?district_id=" + districtId, function(towns) {
                let opts = '<option value="" disabled selected>Choose a town...</option>';
                $.each(towns, function(i, t) {
                    opts += `<option value="${t.id}">${t.name.toUpperCase()}</option>`;
                });
                opts += '<option value="new">+ Add New Town</option>';
                $('#town_id').html(opts);
            });

            // Fetch Projects
            $.getJSON("{{ route('ews.stp.projects') }}?district_id=" + districtId, function(projs) {
                let opts = '<option value="" disabled selected>Choose a project...</option>';
                $.each(projs, function(i, p) {
                    opts += `<option value="${p.id}" data-developer="${p.developer_name || ''}" data-sector="${p.sector_no || ''}" data-address="${p.address || ''}" data-license="${p.license_no || ''}" data-area="${p.area_of_project || ''}" data-approved="${p.approved_building_plan || ''}" data-approval-date="${p.building_plan_approval_date || ''}" data-oc-yn="${p.oc_status_yn || ''}">${p.name.toUpperCase()}</option>`;
                });
                opts += '<option value="new">+ Add New Project</option>';
                $('#project_id').html(opts);
                $('#block_id').html('<option value="" disabled selected>Choose a block/tower...</option><option value="new">+ Add New Block/Tower</option>');
            });
        }

        // Cascading Town Change
        function onTownChanged() {
            const districtId = $('#district_id').val();
            const townId = $('#town_id').val();
            if (townId === 'new') {
                openAddTownModal();
                return;
            }
            if (!districtId) return;

            let url = "{{ route('ews.stp.projects') }}?district_id=" + districtId;
            if (townId) url += "&town_id=" + townId;

            $.getJSON(url, function(projs) {
                let opts = '<option value="" disabled selected>Choose a project...</option>';
                $.each(projs, function(i, p) {
                    opts += `<option value="${p.id}" data-developer="${p.developer_name || ''}" data-sector="${p.sector_no || ''}" data-address="${p.address || ''}" data-license="${p.license_no || ''}" data-area="${p.area_of_project || ''}" data-approved="${p.approved_building_plan || ''}" data-approval-date="${p.building_plan_approval_date || ''}" data-oc-yn="${p.oc_status_yn || ''}">${p.name.toUpperCase()}</option>`;
                });
                opts += '<option value="new">+ Add New Project</option>';
                $('#project_id').html(opts);
                $('#block_id').html('<option value="" disabled selected>Choose a block/tower...</option><option value="new">+ Add New Block/Tower</option>');
            });
        }

        // Cascading Project Change -> auto-populate metadata
        function onProjectChanged() {
            const projId = $('#project_id').val();
            if (projId === 'new') {
                toggleNewProjectInline();
                return;
            }

            const selectedOption = $('#project_id option:selected');
            const devName = selectedOption.data('developer');
            const sectorNo = selectedOption.data('sector');
            const address = selectedOption.data('address');
            const licenseNo = selectedOption.data('license');
            const area = selectedOption.data('area');
            const approved = selectedOption.data('approved');
            const approvalDate = selectedOption.data('approval-date');
            const ocYn = selectedOption.data('oc-yn');

            if (devName) $('#developer_name').val(devName);
            if (sectorNo) $('#sector_no').val(sectorNo);
            if (address) $('#address_of_project').val(address);
            if (licenseNo) $('#license_no').val(licenseNo);
            if (area) $('#area_of_project').val(area);

            if (approved) {
                $('#approved_building_plan').val(approved.toLowerCase());
                handleApprovedBuildingPlanChange(false);
            }
            if (approvalDate && approved && approved.toLowerCase() === 'yes') {
                $('#building_plan_approval_date').val(approvalDate);
            }
            if (ocYn) {
                $('#oc_status_yn').val(ocYn.toLowerCase());
                handleOcStatusYnChange(false);
            }

            // Fetch Blocks for this project
            $.getJSON("{{ route('ews.stp.blocks') }}?project_id=" + projId, function(blocks) {
                let opts = '<option value="" disabled selected>Choose a block/tower...</option>';
                $.each(blocks, function(i, b) {
                    opts += `<option value="${b.id}">${b.name.toUpperCase()}</option>`;
                });
                opts += '<option value="new">+ Add New Block/Tower</option>';
                $('#block_id').html(opts);
            });
        }

        // Building Plan Approved Toggle (Mandatory - Reverts to Yes on 'no')
        function handleApprovedBuildingPlanChange(showAlert = true) {
            const val = $('#approved_building_plan').val();
            if (val === 'no') {
                if (showAlert) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Approved Building Plan Mandatory',
                        html: 'Approved Building Plan is <strong>mandatory</strong> for this EWS flat registration.<br><br>Flats cannot be saved or submitted without an Approved Building Plan.',
                        confirmButtonText: 'Keep Approved (Yes)',
                        confirmButtonColor: '#4f46e5',
                        allowOutsideClick: false
                    }).then(() => {
                        $('#approved_building_plan').val('yes');
                        $('#building_plan_date_wrapper').removeClass('hidden');
                        $('#building_plan_copy_wrapper').removeClass('hidden');
                        $('#existing_bldg_plan_preview').removeClass('hidden');
                    });
                }
            } else if (val === 'yes') {
                $('#building_plan_date_wrapper').removeClass('hidden');
                $('#building_plan_copy_wrapper').removeClass('hidden');
                $('#existing_bldg_plan_preview').removeClass('hidden');
            }
        }

        // OC Status YN Toggle (Mandatory - Reverts to Yes on 'no')
        function handleOcStatusYnChange(showAlert = true) {
            const val = $('#oc_status_yn').val();
            if (val === 'no') {
                if (showAlert) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Occupation Certificate (OC) Mandatory',
                        html: 'Valid Occupation Certificate (OC) is <strong>mandatory</strong> to offer flats for allotment.<br><br>Flats cannot be saved or submitted without an Occupation Certificate.',
                        confirmButtonText: 'Keep OC Received (Yes)',
                        confirmButtonColor: '#0d9488',
                        allowOutsideClick: false
                    }).then(() => {
                        $('#oc_status_yn').val('yes');
                        $('#oc_category_wrapper').removeClass('hidden');
                        $('#oc_fields_container').removeClass('hidden');
                        $('#existing_oc_preview').removeClass('hidden');
                    });
                }
            } else if (val === 'yes') {
                $('#oc_category_wrapper').removeClass('hidden');
                if (!$('#oc_status').val()) {
                    $('#oc_status').val('part');
                }
                $('#oc_fields_container').removeClass('hidden');
                $('#existing_oc_preview').removeClass('hidden');
                handleOcStatusChange();
            }
        }

        // OC Category Change (Part vs Complete)
        function handleOcStatusChange() {
            const ocCat = $('#oc_status').val();
            if (ocCat === 'complete') {
                $('#part_oc_breakdown_section').addClass('hidden');
                $('#flats_offered_for_draw').val('');
                $('#remaining_flats_for_oc').val('');
            } else if (ocCat === 'part') {
                $('#part_oc_breakdown_section').removeClass('hidden');
            }
        }

        // Form Submit Guard (Blocks submission on 'no')
        $('#stpEditForm').on('submit', function(e) {
            const planVal = $('#approved_building_plan').val();
            if (planVal !== 'yes') {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Blocked',
                    html: 'Approved Building Plan is <strong>mandatory</strong>.<br><br>Please select <strong>Yes (Approved)</strong> to proceed.',
                    confirmButtonColor: '#ef4444'
                });
                $('#approved_building_plan').val('yes');
                $('#building_plan_date_wrapper').removeClass('hidden');
                $('#building_plan_copy_wrapper').removeClass('hidden');
                return false;
            }

            const ocYnVal = $('#oc_status_yn').val();
            if (ocYnVal !== 'yes') {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Blocked',
                    html: 'Valid Occupation Certificate (OC) is <strong>mandatory</strong>.<br><br>Please select <strong>Yes (OC Received)</strong> to proceed.',
                    confirmButtonColor: '#ef4444'
                });
                $('#oc_status_yn').val('yes');
                $('#oc_category_wrapper').removeClass('hidden');
                $('#oc_fields_container').removeClass('hidden');
                return false;
            }

            if (!$('#building_plan_approval_date').val()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Building Plan Approval Date Required',
                    text: 'Please enter Date of Approved for Building Plan.',
                    confirmButtonColor: '#f59e0b'
                });
                $('#building_plan_approval_date').focus();
                return false;
            }

            if (!$('#oc_status').val()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'OC Category Required',
                    text: 'Please select OC Category (Part / Complete).',
                    confirmButtonColor: '#f59e0b'
                });
                $('#oc_status').focus();
                return false;
            }

            if ($('#oc_status').val() === 'part') {
                if ($('#flats_offered_for_draw').val() === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Draw Quota Required',
                        text: 'Please specify the number of flats offered for draw for Part OC.',
                        confirmButtonColor: '#f59e0b'
                    });
                    $('#flats_offered_for_draw').focus();
                    return false;
                }
            } else if ($('#oc_status').val() === 'complete') {
                $('#flats_offered_for_draw').val('');
                $('#remaining_flats_for_oc').val('');
            }
        });

        function fetchTcpLicenceForEditForm() {
            const licNo = $('#license_no').val().trim();
            if (!licNo) {
                Swal.fire({ icon: 'warning', title: 'Licence Number Required', text: 'Please enter a valid licence number (e.g. 242 OF 2007).' });
                return;
            }

            Swal.fire({
                title: 'Querying TCP Haryana Portal...',
                text: 'Fetching official licence specifications for ' + licNo,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('ews.stp.query-tcp-licence') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    licence_no: licNo
                },
                success: function(resp) {
                    if (resp.success && resp.data) {
                        const d = resp.data;
                        if (d.Developer) $('#developer_name').val(d.Developer);
                        if (d.AreaInAcre) $('#area_of_project').val(d.AreaInAcre + ' Acres');
                        if (d.Sector) $('#sector_no').val(d.Sector);
                        if (d.DevelopmentPlan) $('#address_of_project').val(d.DevelopmentPlan);
                        if (d.LicenceNo) $('#license_no').val(d.LicenceNo);

                        Swal.fire({
                            icon: 'success',
                            title: 'TCP Licence Data Applied!',
                            html: `Successfully retrieved and pre-filled specifications for <strong>${d.LicenceNo}</strong>:<br><div class="text-left p-2 mt-2 bg-emerald-50 rounded text-xs font-mono border border-emerald-200"><div>• Developer: ${d.Developer}</div><div>• Colony: ${d.ColonyName}</div><div>• Area: ${d.AreaInAcre} Acres</div><div>• Sector: ${d.Sector}</div><div>• Status: ${d.Status}</div></div>`,
                            timer: 4000
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Matching Record',
                            text: resp.message || 'No licence records found on TCP Haryana portal.'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'API Query Failed',
                        text: xhr.responseJSON?.message || 'Could not connect to TCP Haryana portal.'
                    });
                }
            });
        }
    </script>
</body>
</html>
