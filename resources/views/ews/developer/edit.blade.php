<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS STP - Edit Flat Registry</title>
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
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    <!-- DEEP NAVY / SLATE SIDEBAR -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-355 shrink-0 h-full shadow-xl z-20">
        <div class="h-16 px-6 border-b border-slate-800 flex items-center gap-2.5 shrink-0 bg-slate-950">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-indigo-655 flex items-center justify-center shadow-md">
                <i class="bi bi-shield-fill-check text-white text-sm"></i>
            </div>
            <div>
                <h1 class="text-xs font-black tracking-tight text-white uppercase">EWS Portal</h1>
                <p class="text-[8px] text-slate-500 font-mono tracking-widest uppercase">STP Hub</p>
            </div>
        </div>

        <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">
            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Registry Matrix</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-building text-sky-400"></i>
                        <span>{{ $displayZoneName ?? 'Zone' }} Flats</span>
                    </a>
                    <a href="{{ route('ews.developer.dashboard', ['ownership_scope' => 'my_flats']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-person-check-fill text-emerald-400"></i>
                        <span>Flats Added By Me</span>
                    </a>
                    <a href="{{ route('ews.developer.flats.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-plus-circle text-slate-400"></i>
                        <span>Register Flat</span>
                    </a>
                </div>
            </div>

            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Audit & Logs</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-journal-text text-slate-400"></i>
                        <span>STP Logs</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-slate-800 bg-slate-950 flex flex-col gap-2 shrink-0">
            <a href="{{ route('ews.developer.logout') }}" class="w-full py-1.5 bg-red-500/20 hover:bg-red-600 text-red-300 rounded-lg text-[9px] font-black uppercase transition-all flex items-center justify-center gap-1 border border-red-500/30">
                <i class="bi bi-power"></i>
                <span>Logout Session</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTAINER -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.developer.dashboard') }}" class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 hover:bg-sky-600 hover:text-white transition-all shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase">Edit EWS Flat Details</h2>
                    <p class="text-[8px] text-slate-400 font-mono uppercase">Modify Registered Registry Entry</p>
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
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full max-w-5xl mx-auto">
                
                <!-- Left Column: Edit Form Card (lg:col-span-7) -->
                <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl shadow-sm dev-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-150 bg-slate-50/50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                                <i class="bi bi-pencil-square text-sky-500 text-sm"></i>
                                Edit Flat Registry Entry
                            </h3>
                            <p class="text-[8px] text-slate-400 font-mono mt-0.5 uppercase">Modify EWS Allotment Record</p>
                        </div>
                        <span class="text-[8px] text-slate-400 font-mono font-bold">SECURE ID: {{ $secureId ?? \App\Helpers\EwsHelper::encodeSecureId($flat->id) }}</span>
                    </div>

                    <!-- Form Content -->
                    <form method="POST" action="{{ route('ews.developer.flats.update', $secureId ?? \App\Helpers\EwsHelper::encodeSecureId($flat->id)) }}" class="p-6 space-y-4" id="devEditForm">
                        @csrf
                        @method('PUT')

                        <!-- Row 1: Zone (Locked) & District (Filtered by Zone) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Assigned Zone (Frozen/Locked) -->
                            @php
                                $displayZoneName = $zone ? $zone->name : ($flat->zone_name ?? ($flat->district_name ?? ($user->district_name ?? '')));
                                $displayZoneName = strtoupper(trim(str_replace(' ZONE', '', $displayZoneName))) . ' ZONE';
                                $selectedDistrictId = $flat->district_id ?? ($districts->first()->id ?? $user->district_id);
                                $selectedZoneId = $zone->id ?? ($flat->zone_id ?? ($user->zone_id ?? ''));
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                        Assigned Zone <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-[9px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[11px]">lock</span> Locked
                                    </span>
                                </div>
                                <input type="hidden" name="zone_id" id="zone_id" value="{{ $selectedZoneId }}">
                                <input type="hidden" name="zone_name" id="zone_name" value="{{ $displayZoneName }}">
                                <div class="relative">
                                    <input type="text" readonly disabled
                                        value="{{ $displayZoneName }}"
                                        class="w-full bg-slate-100 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-700 font-extrabold cursor-not-allowed uppercase shadow-inner" />
                                    <span class="absolute right-3 top-2 text-slate-400">
                                        <span class="material-symbols-outlined text-base">lock</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Select District (Only districts of this zone) -->
                            <div class="space-y-1">
                                <label for="district_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 1</span>
                                    <span>Select District</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <select id="district_id" name="district_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    @if(count($districts) > 1)
                                        <option value="" disabled>Choose a district in {{ $displayZoneName }}...</option>
                                    @endif
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ (old('district_id', $selectedDistrictId) == $district->id) ? 'selected' : '' }}>
                                            {{ strtoupper($district->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Town & Project -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Town -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <label for="town_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 2</span>
                                        <span>Name of Town</span> <span class="text-red-500 ms-0.5">*</span>
                                    </label>
                                    <button type="button" onclick="openAddTownModal()" class="inline-flex items-center gap-1 text-[9.5px] font-bold text-sky-600 hover:text-sky-800 bg-sky-50 hover:bg-sky-100 border border-sky-200 hover:border-sky-300 px-2 py-0.5 rounded shadow-2xs transition-all cursor-pointer">
                                        <i class="bi bi-plus-circle-fill text-[10px]"></i>
                                        <span>Add Town</span>
                                    </button>
                                </div>
                                <select id="town_id" name="town_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a town...</option>
                                    @foreach($towns as $town)
                                        <option value="{{ $town->id }}" {{ old('town_id', $flat->town_id) == $town->id ? 'selected' : '' }}>
                                            {{ strtoupper($town->name) }}{{ !empty($town->type) ? ' (' . strtoupper($town->type) . ')' : '' }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Add New Town</option>
                                </select>
                            </div>

                            <!-- Project Selection -->
                            <div class="space-y-1">
                                <label for="project_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 3</span>
                                    <span>Name of Project</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <select id="project_id" name="project_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a project...</option>
                                    @foreach($projects as $proj)
                                        <option value="{{ $proj->id }}" {{ old('project_id', $flat->project_id) == $proj->id ? 'selected' : '' }}>
                                            {{ strtoupper($proj->name) }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Add New Project</option>
                                </select>
                                
                                <!-- New Project Input -->
                                <div id="new_project_container" class="hidden mt-2">
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="new_project_name" name="new_project_name" placeholder="Enter new project name (e.g. TDI City Kingsbury)"
                                            class="w-full bg-white border border-sky-400 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                        <button type="button" id="btn_save_project_ajax" onclick="saveNewProjectAjax()"
                                            class="px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-[11px] font-black uppercase tracking-wider whitespace-nowrap flex items-center gap-1 shadow-sm transition-all shrink-0">
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

                        <!-- Row 3: Block & Floor -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Block / Tower No. Selection -->
                            <div class="space-y-1">
                                <label for="block_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 4</span>
                                    <span>Block / Tower No.</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <select id="block_id" name="block_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a block/tower...</option>
                                    @foreach($blocks as $blk)
                                        <option value="{{ $blk->id }}" {{ old('block_id', $flat->block_id) == $blk->id ? 'selected' : '' }}>
                                            {{ strtoupper($blk->name) }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Add New Block/Tower</option>
                                </select>
                                
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
                                        <div>
                                            <span id="block_similarity_msg"></span>
                                        </div>
                                    </div>
                                    <p class="text-[8.5px] text-slate-400 mt-1 italic">Click 'Save Block' to instantly save into ews_blocks, or auto-saves on form submit.</p>
                                </div>
                            </div>

                            <!-- Floor Details -->
                            <div class="space-y-1">
                                <label for="floor_number" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 5</span>
                                    <span>Floor (Number)</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <select id="floor_number" name="floor_number" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:outline-none font-bold">
                                    @php
                                        $floorStr = strtolower(trim($flat->floor));
                                        $selectedFloor = 0;
                                        if ($floorStr === 'ground floor' || $floorStr === 'ground' || $floorStr === '0') {
                                            $selectedFloor = 0;
                                        } elseif ($floorStr === 'first floor' || $floorStr === 'first' || $floorStr === '1') {
                                            $selectedFloor = 1;
                                        } elseif ($floorStr === 'second floor' || $floorStr === 'second' || $floorStr === '2') {
                                            $selectedFloor = 2;
                                        } elseif ($floorStr === 'third floor' || $floorStr === 'third' || $floorStr === '3') {
                                            $selectedFloor = 3;
                                        } elseif (preg_match('/(\d+)/', $floorStr, $matches)) {
                                            $selectedFloor = (int)$matches[1];
                                        }
                                    @endphp
                                    <option value="0" {{ $selectedFloor === 0 ? 'selected' : '' }}>Ground Floor (0)</option>
                                    @for($f = 1; $f <= 100; $f++)
                                        <option value="{{ $f }}" {{ $selectedFloor === $f ? 'selected' : '' }}>Floor {{ $f }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Row 4: Flat Number -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="flat_number" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-sky-100 text-sky-800 me-1.5">Step 5</span>
                                    <span>Flat Number</span> <span class="text-red-500 ms-0.5">*</span>
                                </label>
                                <input type="text" id="flat_number" name="flat_number" value="{{ $flat->flat_number }}" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                            </div>
                        </div>

                        <div class="border-b border-slate-100 pt-3"></div>

                        <!-- Actions -->
                        <div class="pt-2 flex gap-3">
                            <a href="{{ route('ews.developer.dashboard') }}"
                                class="w-1/2 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-655 font-bold uppercase tracking-wider rounded-lg text-[9px] transition-all text-center">
                                Cancel & Go Back
                            </a>
                            <button type="submit"
                                class="w-1/2 py-2.5 bg-gradient-to-r from-sky-500 to-indigo-650 hover:from-sky-600 hover:to-indigo-700 text-white font-black uppercase tracking-wider rounded-lg text-[9px] transition-all flex items-center justify-center gap-1 shadow-md">
                                <i class="bi bi-save-fill"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Guidelines & Sandbox Parameters (lg:col-span-5) -->
                <div class="lg:col-span-5 space-y-4">
                    
                    <!-- Sandbox Info Card -->
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm dev-shadow space-y-3">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <i class="bi bi-info-circle text-sky-500"></i>
                            Editing Guidelines
                        </h4>
                        <ul class="space-y-2.5 text-xs text-slate-600 font-medium">
                            <li class="flex gap-2">
                                <i class="bi bi-check-circle-fill text-sky-500 text-sm shrink-0"></i>
                                <span><strong>Audit Trail Tracking:</strong> Updates generate an <code>UPDATED</code> log entry with the old details and new details stored.</span>
                            </li>
                            <li class="flex gap-2">
                                <i class="bi bi-check-circle-fill text-sky-500 text-sm shrink-0"></i>
                                <span><strong>Floor Designation:</strong> Indicar floor description i.e. Ground floor, First floor, and so on.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Sandbox Database Status -->
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-sm text-slate-350 space-y-3 font-mono text-[10px]">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-200 border-b border-slate-800 pb-2 flex items-center gap-2">
                            <i class="bi bi-hdd-network text-sky-400"></i>
                            Node Telemetry
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-slate-500">DB CONFLICT STATUS:</span>
                                <span class="text-emerald-400 font-bold">BYPASS (LOCAL)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">PPP FAMILY LOCK:</span>
                                <span class="text-emerald-400 font-bold">DISABLED</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-505 font-bold">RECORD STATE:</span>
                                <span class="text-sky-400 font-bold font-mono">ID: #{{ $flat->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">LATENCY TIME:</span>
                                <span class="text-slate-200">1.4ms</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Modal: Add New Town Popup -->
    <div id="modal_add_town" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 transition-all duration-300" onclick="if(event.target === this) closeAddTownModal()">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 flex flex-col overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modal_add_town_content">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-sky-50 via-white to-slate-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shadow-xs shrink-0">
                        <i class="bi bi-geo-alt-fill text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Add New Town</h3>
                        <p class="text-[10px] text-slate-500 font-medium" id="modal_town_context_info">
                            District: <span class="font-bold text-slate-700" id="modal_town_dist_name">-</span> &bull; Zone: <span class="font-bold text-sky-700" id="modal_town_zone_name">-</span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeAddTownModal()" class="w-7 h-7 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-3.5">
                <!-- Town Name Input -->
                <div class="space-y-1">
                    <label for="modal_new_town_name" class="block text-[10px] font-black uppercase text-slate-600 tracking-wider">
                        Town Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modal_new_town_name" placeholder="Enter town name (e.g. Kharkhoda, Samalkha)"
                        class="w-full bg-slate-50 border border-slate-300 focus:border-sky-500 focus:bg-white rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:outline-none transition-all">
                    <div id="modal_town_similarity_alert" class="hidden text-[10.5px] font-bold text-amber-800 bg-amber-50 border border-amber-300 rounded-lg p-2.5 flex items-start gap-2 shadow-xs transition-all mt-1">
                        <i class="bi bi-exclamation-triangle-fill text-amber-600 text-sm mt-0.5 shrink-0"></i>
                        <div>
                            <span id="modal_town_similarity_msg"></span>
                        </div>
                    </div>
                </div>

                <!-- Town Type (Municipality) Selection -->
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

                <!-- Custom Municipality Type (if 'other') -->
                <div id="modal_custom_town_type_container" class="hidden space-y-1 bg-sky-50/60 p-2.5 rounded-lg border border-sky-200">
                    <label for="modal_custom_town_type" class="block text-[9px] font-black uppercase text-sky-800 tracking-wider">
                        Specify Municipality Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modal_custom_town_type" placeholder="e.g. Nagar Panchayat / Special Area"
                        class="w-full bg-white border border-sky-400 focus:border-sky-600 rounded-lg px-3 py-1.5 text-xs text-slate-800 font-bold focus:outline-none">
                </div>

                <!-- Auto Zone & District Notice -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-start gap-2.5">
                    <i class="bi bi-info-circle-fill text-sky-600 text-sm mt-0.5 shrink-0"></i>
                    <div class="text-[10px] text-slate-600 leading-relaxed">
                        This town will be linked to <strong class="text-slate-800 font-bold" id="modal_notice_dist">-</strong> and assigned to <strong class="text-sky-700 font-bold" id="modal_notice_zone">-</strong> in the master database.
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

    <script>
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
            if ($('#district_id').is('select')) {
                $('#district_id').select2();
                $('#district_id').on('select2:select select2:unselect', function() {
                    districtSelect.dispatchEvent(new Event('change'));
                });
            }
            $('#town_id').select2();
            $('#project_id').select2();
            $('#block_id').select2();

            $('#town_id').on('select2:select select2:unselect change', function() {
                townSelect.dispatchEvent(new Event('change'));
            });
            $('#project_id').on('select2:select select2:unselect change', function() {
                projectSelect.dispatchEvent(new Event('change'));
            });
            $('#block_id').on('select2:select select2:unselect change', function() {
                blockSelect.dispatchEvent(new Event('change'));
            });

            // SweetAlert2 notifications
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

            @if($errors->any())
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

            // Initialize sequential locking based on initial values
            initSequentialLockState();
        });

        function updateSelectLock(selectEl, enabled, placeholderText = null, clearOptions = true) {
            if (!selectEl) return;
            const $el = $(selectEl);
            const $s2 = $el.next('.select2-container');

            if (enabled) {
                $el.prop('disabled', false);
                selectEl.disabled = false;
                selectEl.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                if ($s2.length) {
                    $s2.removeClass('opacity-60 cursor-not-allowed pointer-events-none select2-container--disabled');
                    $s2.find('.select2-selection').removeClass('bg-slate-100 cursor-not-allowed opacity-60');
                    $s2.find('.select2-selection').attr('tabindex', '0').attr('aria-disabled', 'false');
                }
                if (placeholderText && selectEl.options && selectEl.options.length > 0) {
                    selectEl.options[0].textContent = placeholderText;
                }
            } else {
                $el.prop('disabled', true);
                selectEl.disabled = true;
                selectEl.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                if ($s2.length) {
                    $s2.addClass('opacity-60 cursor-not-allowed pointer-events-none select2-container--disabled');
                    $s2.find('.select2-selection').addClass('bg-slate-100 cursor-not-allowed opacity-60');
                    $s2.find('.select2-selection').attr('tabindex', '-1').attr('aria-disabled', 'true');
                }
                if (placeholderText) {
                    if (clearOptions) {
                        selectEl.innerHTML = `<option value="" disabled selected>${placeholderText}</option>`;
                    } else if (selectEl.options && selectEl.options.length > 0) {
                        selectEl.options[0].textContent = placeholderText;
                    }
                }
            }

            // Immediately update Select2 visible text
            const select2Container = document.getElementById(`select2-${selectEl.id}-container`);
            if (select2Container && placeholderText) {
                const currentText = selectEl.value && selectEl.selectedIndex >= 0 
                    ? selectEl.options[selectEl.selectedIndex].textContent 
                    : placeholderText;
                select2Container.textContent = currentText;
                select2Container.title = currentText;
            }

            $el.trigger('change.select2');
        }

        function setFlatInputsLock(locked) {
            const floorEl = document.getElementById('floor_number');
            const flatNumEl = document.getElementById('flat_number');
            const submitBtn = document.querySelector('button[type="submit"]');

            const inputs = [floorEl, flatNumEl];
            inputs.forEach(el => {
                if (!el) return;
                el.disabled = locked;
                if (locked) {
                    el.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                } else {
                    el.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-60');
                }
            });

            if (submitBtn) {
                if (locked) {
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        function initSequentialLockState() {
            const hasDist = districtSelect && districtSelect.value;
            const hasTown = townSelect && townSelect.value;
            const hasProj = projectSelect && projectSelect.value;
            const hasBlock = blockSelect && blockSelect.value;

            if (!hasDist) {
                updateSelectLock(townSelect, false, "🔒 Step 1: Select District First...", true);
                updateSelectLock(projectSelect, false, "🔒 Step 1: Select District First...", true);
                updateSelectLock(blockSelect, false, "🔒 Step 3: Select Project First...", true);
                setFlatInputsLock(true);
            } else if (!hasProj) {
                updateSelectLock(townSelect, true);
                updateSelectLock(projectSelect, true, "Choose a project...", false);
                updateSelectLock(blockSelect, false, "🔒 Step 3: Select Project First...", true);
                setFlatInputsLock(true);
            } else if (!hasBlock) {
                updateSelectLock(townSelect, true);
                updateSelectLock(projectSelect, true);
                updateSelectLock(blockSelect, true, "Choose a block/tower...", false);
                setFlatInputsLock(true);
            } else {
                updateSelectLock(townSelect, true);
                updateSelectLock(projectSelect, true);
                updateSelectLock(blockSelect, true);
                setFlatInputsLock(false);
            }
            if (projectSelect && projectSelect.value === 'new') {
                newProjectContainer.classList.remove('hidden');
                newProjectInput.required = true;
            }
            if (blockSelect && blockSelect.value === 'new') {
                newBlockContainer.classList.remove('hidden');
                newBlockInput.required = true;
            }
        }

        function fetchTowns(districtId, selectedTownId = null) {
            if (!districtId) {
                updateSelectLock(townSelect, false, "🔒 Step 1: Select District First...", true);
                return;
            }
            
            townSelect.innerHTML = '<option value="" disabled selected>Loading towns...</option>';
            $(townSelect).trigger('change.select2');
            
            fetch(`{{ route('ews.developer.towns') }}?district_id=${districtId}`)
                .then(res => res.json())
                .then(data => {
                    townSelect.innerHTML = '<option value="" disabled selected>Choose a town...</option>';
                    data.forEach(t => {
                        const isSel = selectedTownId && selectedTownId == t.id ? 'selected' : '';
                        const typeBadge = t.type ? ` (${t.type.toUpperCase()})` : '';
                        townSelect.innerHTML += `<option value="${t.id}" ${isSel}>${t.name.toUpperCase()}${typeBadge}</option>`;
                    });
                    townSelect.innerHTML += '<option value="new">+ Add New Town</option>';
                    
                    updateSelectLock(townSelect, true);
                    
                    if (selectedTownId) {
                        $(townSelect).val(selectedTownId).trigger('change.select2');
                        townSelect.dispatchEvent(new Event('change'));
                    } else {
                        handleTownChange();
                    }
                })
                .catch(err => {
                    console.error('Error fetching towns:', err);
                    townSelect.innerHTML = '<option value="" disabled selected>Choose a town...</option><option value="new">+ Add New Town</option>';
                    $(townSelect).trigger('change.select2');
                });
        }

        function fetchProjects(districtId, townId = null, selectedProjectId = null) {
            if (!districtId) {
                updateSelectLock(projectSelect, false, "🔒 Step 1: Select District First...", true);
                clearBlocks();
                return;
            }

            if (!townId || townId === 'new') {
                updateSelectLock(projectSelect, false, "🔒 Step 2: Select Town First...", true);
                clearBlocks();
                return;
            }
            
            projectSelect.innerHTML = '<option value="" disabled selected>Loading projects...</option>';
            $(projectSelect).trigger('change.select2');
            
            const zoneInput = document.getElementById('zone_id');
            const zoneParam = zoneInput && zoneInput.value ? `&zone_id=${zoneInput.value}` : '';

            fetch(`{{ route('ews.developer.projects') }}?district_id=${districtId}&town_id=${townId}${zoneParam}`)
                .then(res => res.json())
                .then(data => {
                    projectSelect.innerHTML = '<option value="" disabled selected>Choose a project...</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(proj => {
                            const isSel = selectedProjectId && selectedProjectId == proj.id ? 'selected' : '';
                            const abbrTag = proj.project_abbr ? ` [${proj.project_abbr}]` : '';
                            projectSelect.innerHTML += `<option value="${proj.id}" ${isSel}>${proj.name.toUpperCase()}${abbrTag}</option>`;
                        });
                    }
                    projectSelect.innerHTML += '<option value="new">+ Add New Project</option>';
                    
                    updateSelectLock(projectSelect, true, "Choose a project...", false);
                    $(projectSelect).select2();

                    if (selectedProjectId) {
                        $(projectSelect).val(selectedProjectId).trigger('change.select2');
                        projectSelect.dispatchEvent(new Event('change'));
                    } else {
                        handleProjectChange();
                    }
                })
                .catch(err => {
                    console.error('Error fetching projects:', err);
                    projectSelect.innerHTML = '<option value="" disabled selected>Choose a project...</option><option value="new">+ Add New Project</option>';
                    $(projectSelect).select2();
                    updateSelectLock(projectSelect, true, "Choose a project...", false);
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

        let previousTownValue = '{{ old("town_id", $flat->town_id) }}';

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
            if (townInput) townInput.value = '';
            if (typeSelect) typeSelect.value = '';
            if (customTypeInput) customTypeInput.value = '';
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
                    text: 'Please select a district first before creating a project.',
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
                    
                    newProjectContainer.classList.add('hidden');
                    newProjectInput.required = false;
                    newProjectInput.value = '';
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
                $(blockSelect).trigger('change.select2');
                handleBlockChange();
                return;
            }
            
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
                    blockSelect.innerHTML = '<option value="" disabled selected>Choose a block/tower...</option>';
                    data.forEach(blk => {
                        const isSel = selectedBlockId && selectedBlockId == blk.id ? 'selected' : '';
                        blockSelect.innerHTML += `<option value="${blk.id}" ${isSel}>${blk.name.toUpperCase()}</option>`;
                    });
                    blockSelect.innerHTML += '<option value="new">+ Add New Block/Tower</option>';
                    
                    updateSelectLock(blockSelect, true);
                    
                    if (selectedBlockId) {
                        $(blockSelect).val(selectedBlockId).trigger('change.select2');
                        blockSelect.dispatchEvent(new Event('change'));
                    } else {
                        handleBlockChange();
                    }
                })
                .catch(err => {
                    console.error('Error fetching blocks:', err);
                    blockSelect.innerHTML = '<option value="" disabled selected>Choose a block/tower...</option><option value="new">+ Add New Block/Tower</option>';
                    $(blockSelect).trigger('change.select2');
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
                    
                    newBlockContainer.classList.add('hidden');
                    newBlockInput.required = false;
                    newBlockInput.value = '';
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
            blockSelect.innerHTML = '<option value="" disabled selected>🔒 Step 3: Select Project First...</option>';
            $(blockSelect).val('').trigger('change.select2');
            updateSelectLock(blockSelect, false, "🔒 Step 3: Select Project First...");
            handleBlockChange();
        }

        function handleTownChange() {
            const townVal = townSelect ? townSelect.value : '';

            if (townVal === 'new') {
                openAddTownModal();
                return;
            }

            // Step 3 (Project) unlocks ONLY when Town (Step 2) is selected
            if (townVal && townVal !== '') {
                previousTownValue = townVal;
                const distId = districtSelect ? districtSelect.value : '';
                updateSelectLock(projectSelect, true, "Choose a project...", false);
                fetchProjects(distId, townVal, projectSelect.value);
            } else {
                previousTownValue = '';
                // Town is not selected -> Keep Step 3 locked (values remain preserved)
                updateSelectLock(projectSelect, false, "🔒 Step 2: Select Town First...", true);
                clearBlocks();
                setFlatInputsLock(true);
            }
        }

        function handleProjectChange() {
            const val = projectSelect.value;
            if (val === 'new') {
                newProjectContainer.classList.remove('hidden');
                newProjectInput.required = true;
                
                updateSelectLock(blockSelect, true, "Choose a block/tower...", false);
                blockSelect.innerHTML = '<option value="new" selected>+ Add New Block/Tower</option>';
                $(blockSelect).trigger('change.select2');
                handleBlockChange();
            } else {
                newProjectContainer.classList.add('hidden');
                newProjectInput.required = false;
                newProjectInput.value = '';
                if (val) {
                    updateSelectLock(blockSelect, true, "Choose a block/tower...", false);
                    fetchBlocks(val);
                } else {
                    clearBlocks();
                }
                setFlatInputsLock(true);
            }
        }

        function handleBlockChange() {
            const val = blockSelect.value;
            if (val === 'new') {
                newBlockContainer.classList.remove('hidden');
                newBlockInput.required = true;
            } else {
                newBlockContainer.classList.add('hidden');
                newBlockInput.required = false;
                newBlockInput.value = '';
            }

            if (val) {
                // Block selected -> Unlock Floor and Flat fields!
                setFlatInputsLock(false);
            } else {
                setFlatInputsLock(true);
            }
        }

        districtSelect.addEventListener('change', function() {
            const distId = this.value;
            if (distId) {
                updateSelectLock(townSelect, true, "Choose a town...", true);
                fetchTowns(distId);
                updateSelectLock(projectSelect, false, "🔒 Step 2: Select Town First...", true);
            } else {
                updateSelectLock(townSelect, false, "🔒 Step 1: Select District First...", true);
                updateSelectLock(projectSelect, false, "🔒 Step 1: Select District First...", true);
            }
            clearBlocks();
            setFlatInputsLock(true);
        });

        townSelect.addEventListener('change', handleTownChange);
        projectSelect.addEventListener('change', handleProjectChange);
        blockSelect.addEventListener('change', handleBlockChange);

        // Live Input Similarity Checkers
        const townModalInput = document.getElementById('modal_new_town_name');
        if (townModalInput) {
            townModalInput.addEventListener('input', function() {
                const alertBox = document.getElementById('modal_town_similarity_alert');
                const alertMsg = document.getElementById('modal_town_similarity_msg');
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

        document.getElementById('devEditForm').addEventListener('submit', function (e) {
            // Strict Sequential Validation on Form Submission
            const distVal = districtSelect ? districtSelect.value : '';
            const townVal = townSelect ? townSelect.value : '';
            const projVal = projectSelect ? projectSelect.value : '';
            const blockVal = blockSelect ? blockSelect.value : '';
            const floorVal = document.getElementById('floor_number').value;
            const flatNumVal = document.getElementById('flat_number').value.trim();

            if (!distVal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Step 1 Incomplete',
                    text: 'Please select a District first.',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }

            if (!townVal || townVal === 'new') {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Step 2 Incomplete',
                    text: 'Please select or add a Town from the list.',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }

            if (!projVal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Step 3 Incomplete',
                    text: 'Please select a Project before proceeding.',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }
            if (projVal === 'new' && !document.getElementById('new_project_name').value.trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Step 3 Incomplete',
                    text: 'Please enter the new Project name.',
                    confirmButtonColor: '#3b82f6'
                });
                document.getElementById('new_project_name').focus();
                return false;
            }

            if (!blockVal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Step 4 Incomplete',
                    text: 'Please select a Block / Tower number.',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }
            if (blockVal === 'new' && !document.getElementById('new_block_name').value.trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Step 4 Incomplete',
                    text: 'Please enter the new Block / Tower number.',
                    confirmButtonColor: '#3b82f6'
                });
                document.getElementById('new_block_name').focus();
                return false;
            }

            if (floorVal === '' || !flatNumVal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Step 5 Incomplete',
                    text: 'Please provide both Floor and Flat Number.',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }

            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Saving Changes...';
                btn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        });
    </script>
</body>
</html>
