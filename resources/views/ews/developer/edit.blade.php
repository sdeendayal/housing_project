<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer - Edit Flat Registry</title>
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
                <p class="text-[8px] text-slate-500 font-mono tracking-widest uppercase">Developer Hub</p>
            </div>
        </div>

        <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">
            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Registry Matrix</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-building text-sky-400"></i>
                        <span>{{ !empty($user->district_name) ? strtoupper($user->district_name) : 'My District' }} Flats</span>
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
                        <span>Developer Logs</span>
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
                    @if(!empty($user->district_name))
                        <span class="text-[9px] bg-sky-100 text-sky-800 font-extrabold uppercase px-1.5 py-0.5 rounded border border-sky-200">({{ strtoupper($user->district_name) }})</span>
                    @endif
                </div>
                <div class="text-[8.5px] text-slate-500 font-mono">District: <span class="font-bold text-slate-700 uppercase">{{ $user->district_name ?? 'N/A' }}</span> | Mobile: {{ $user->mobile }}</div>
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

                        <!-- Session Alert Notifications -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- District -->
                            <div class="space-y-1">
                                <label for="district_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Select District <span class="text-red-500">*</span></label>
                                <select id="district_id" name="district_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    @if(count($districts) > 1)
                                        <option value="" disabled>Choose a district...</option>
                                    @endif
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ (count($districts) === 1 || $flat->district_id == $district->id) ? 'selected' : '' }}>
                                            {{ strtoupper($district->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Town -->
                            <div class="space-y-1">
                                <label for="town_id" class="block text-[10px] font-black uppercase text-slate-505 tracking-wider">Name of Town <span class="text-red-500">*</span></label>
                                <select id="town_id" name="town_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a town...</option>
                                    @foreach($towns as $town)
                                        <option value="{{ $town->id }}" {{ $flat->town_id == $town->id ? 'selected' : '' }}>
                                            {{ strtoupper($town->name) }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Add New Town</option>
                                </select>
                                
                                <!-- New Town Input -->
                                <div id="new_town_container" class="hidden mt-2">
                                    <input type="text" id="new_town_name" name="new_town_name" placeholder="Enter new town name (e.g. Kundli)"
                                        class="w-full bg-white border border-sky-400 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Project Selection -->
                            <div class="space-y-1">
                                <label for="project_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Name of Project <span class="text-red-500">*</span></label>
                                <select id="project_id" name="project_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a project...</option>
                                    @foreach($projects as $proj)
                                        <option value="{{ $proj->id }}" {{ $flat->project_id == $proj->id ? 'selected' : '' }}>
                                            {{ strtoupper($proj->name) }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Add New Project</option>
                                </select>
                                
                                <!-- New Project Input -->
                                <div id="new_project_container" class="hidden mt-2">
                                    <input type="text" id="new_project_name" name="new_project_name" placeholder="Enter new project name (e.g. TDI City Kingsbury)"
                                        class="w-full bg-white border border-sky-400 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                </div>
                            </div>

                            <!-- Block / Tower No. Selection -->
                            <div class="space-y-1">
                                <label for="block_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Block / Tower No. <span class="text-red-500">*</span></label>
                                <select id="block_id" name="block_id" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-bold">
                                    <option value="" disabled>Choose a block/tower...</option>
                                    @foreach($blocks as $blk)
                                        <option value="{{ $blk->id }}" {{ $flat->block_id == $blk->id ? 'selected' : '' }}>
                                            {{ strtoupper($blk->name) }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Add New Block/Tower</option>
                                </select>
                                
                                <!-- New Block Input -->
                                <div id="new_block_container" class="hidden mt-2">
                                    <input type="text" id="new_block_name" name="new_block_name" placeholder="Enter new block/tower number (e.g. T-02)"
                                        class="w-full bg-white border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Floor (Number) -->
                            <div class="space-y-1">
                                <label for="floor_number" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Floor (Number) <span class="text-red-500">*</span></label>
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

                            <!-- Flat Number -->
                            <div class="space-y-1">
                                <label for="flat_number" class="block text-[10px] font-black uppercase text-slate-505 tracking-wider">Flat Number <span class="text-red-500">*</span></label>
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

    <script>
        // AJAX and Dynamic Fields Logic for Towns, Projects, and Blocks
        const districtSelect = document.getElementById('district_id');
        
        const townSelect = document.getElementById('town_id');
        const newTownContainer = document.getElementById('new_town_container');
        const newTownInput = document.getElementById('new_town_name');

        const projectSelect = document.getElementById('project_id');
        const newProjectContainer = document.getElementById('new_project_container');
        const newProjectInput = document.getElementById('new_project_name');
        
        const blockSelect = document.getElementById('block_id');
        const newBlockContainer = document.getElementById('new_block_container');
        const newBlockInput = document.getElementById('new_block_name');

        $(document).ready(function() {
            // Initialize Select2 search elements
            $('#district_id').select2();
            $('#town_id').select2();
            $('#project_id').select2();
            $('#block_id').select2();

            // Sync Select2 select/clear triggers with our vanilla events
            $('#district_id').on('select2:select select2:unselect', function() {
                districtSelect.dispatchEvent(new Event('change'));
            });
            $('#town_id').on('select2:select select2:unselect', function() {
                townSelect.dispatchEvent(new Event('change'));
            });
            $('#project_id').on('select2:select select2:unselect', function() {
                projectSelect.dispatchEvent(new Event('change'));
            });
            $('#block_id').on('select2:select select2:unselect', function() {
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
        });

        function fetchTowns(districtId, selectedTownId = null) {
            if (!districtId) {
                townSelect.innerHTML = '<option value="" disabled selected>Choose a town...</option><option value="new">+ Add New Town</option>';
                $(townSelect).trigger('change.select2');
                return;
            }
            
            townSelect.innerHTML = '<option value="" disabled selected>Loading towns...</option>';
            $(townSelect).trigger('change.select2');
            
            fetch(`/ews/developer/towns?district_id=${districtId}`)
                .then(res => res.json())
                .then(data => {
                    townSelect.innerHTML = '<option value="" disabled selected>Choose a town...</option>';
                    data.forEach(t => {
                        const isSel = selectedTownId && selectedTownId == t.id ? 'selected' : '';
                        townSelect.innerHTML += `<option value="${t.id}" ${isSel}>${t.name.toUpperCase()}</option>`;
                    });
                    townSelect.innerHTML += '<option value="new">+ Add New Town</option>';
                    
                    $(townSelect).trigger('change.select2');
                    
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

        function fetchProjects(districtId, selectedProjectId = null) {
            if (!districtId) {
                projectSelect.innerHTML = '<option value="" disabled selected>Choose a project...</option><option value="new">+ Add New Project</option>';
                $(projectSelect).trigger('change.select2');
                clearBlocks();
                return;
            }
            
            projectSelect.innerHTML = '<option value="" disabled selected>Loading projects...</option>';
            $(projectSelect).trigger('change.select2');
            
            fetch(`/ews/developer/projects?district_id=${districtId}`)
                .then(res => res.json())
                .then(data => {
                    projectSelect.innerHTML = '<option value="" disabled selected>Choose a project...</option>';
                    data.forEach(proj => {
                        const isSel = selectedProjectId && selectedProjectId == proj.id ? 'selected' : '';
                        projectSelect.innerHTML += `<option value="${proj.id}" ${isSel}>${proj.name.toUpperCase()}</option>`;
                    });
                    projectSelect.innerHTML += '<option value="new">+ Add New Project</option>';
                    
                    $(projectSelect).trigger('change.select2');
                    
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
                    $(projectSelect).trigger('change.select2');
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
            
            fetch(`/ews/developer/blocks?project_id=${projectId}`)
                .then(res => res.json())
                .then(data => {
                    blockSelect.innerHTML = '<option value="" disabled selected>Choose a block/tower...</option>';
                    data.forEach(blk => {
                        const isSel = selectedBlockId && selectedBlockId == blk.id ? 'selected' : '';
                        blockSelect.innerHTML += `<option value="${blk.id}" ${isSel}>${blk.name.toUpperCase()}</option>`;
                    });
                    blockSelect.innerHTML += '<option value="new">+ Add New Block/Tower</option>';
                    
                    $(blockSelect).trigger('change.select2');
                    
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

        function clearBlocks() {
            blockSelect.innerHTML = '<option value="" disabled selected>Choose a block/tower...</option><option value="new">+ Add New Block/Tower</option>';
            $(blockSelect).val('').trigger('change.select2');
            handleBlockChange();
        }

        function handleTownChange() {
            const val = townSelect.value;
            if (val === 'new') {
                newTownContainer.classList.remove('hidden');
                newTownInput.required = true;
            } else {
                newTownContainer.classList.add('hidden');
                newTownInput.required = false;
                newTownInput.value = '';
            }
        }

        function handleProjectChange() {
            const val = projectSelect.value;
            if (val === 'new') {
                newProjectContainer.classList.remove('hidden');
                newProjectInput.required = true;
                
                $(blockSelect).val('new').trigger('change.select2');
                blockSelect.dispatchEvent(new Event('change'));
            } else {
                newProjectContainer.classList.add('hidden');
                newProjectInput.required = false;
                newProjectInput.value = '';
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
        }

        districtSelect.addEventListener('change', function() {
            fetchTowns(this.value);
            fetchProjects(this.value);
        });

        townSelect.addEventListener('change', handleTownChange);
        
        projectSelect.addEventListener('change', function() {
            const val = projectSelect.value;
            if (val === 'new') {
                newProjectContainer.classList.remove('hidden');
                newProjectInput.required = true;
                $(blockSelect).val('new').trigger('change.select2');
                blockSelect.dispatchEvent(new Event('change'));
            } else {
                newProjectContainer.classList.add('hidden');
                newProjectInput.required = false;
                newProjectInput.value = '';
                if (val) {
                    fetchBlocks(val);
                } else {
                    clearBlocks();
                }
            }
        });
        
        blockSelect.addEventListener('change', handleBlockChange);

        // Pre-run visibility setup for 'new' selection if already selected
        if (townSelect.value === 'new') {
            newTownContainer.classList.remove('hidden');
            newTownInput.required = true;
        }
        if (projectSelect.value === 'new') {
            newProjectContainer.classList.remove('hidden');
            newProjectInput.required = true;
        }
        if (blockSelect.value === 'new') {
            newBlockContainer.classList.remove('hidden');
            newBlockInput.required = true;
        }

        document.getElementById('devEditForm').addEventListener('submit', function (e) {
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
