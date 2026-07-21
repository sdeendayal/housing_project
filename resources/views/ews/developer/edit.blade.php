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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- District -->
                            <div class="space-y-1">
                                <label for="district_id" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Select District</label>
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
                                <label for="town_name" class="block text-[10px] font-black uppercase text-slate-505 tracking-wider">Name of Town</label>
                                <input type="text" id="town_name" name="town_name" value="{{ $flat->town_name }}" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                            </div>
                        </div>

                        <!-- Project Name -->
                        <div class="space-y-1">
                            <label for="project_name" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Name of Project</label>
                            <input type="text" id="project_name" name="project_name" value="{{ $flat->project_name }}" required
                                class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Block / Tower -->
                            <div class="space-y-1 md:col-span-1">
                                <label for="block_tower_number" class="block text-[10px] font-black uppercase text-slate-505 tracking-wider">Block / Tower No.</label>
                                <input type="text" id="block_tower_number" name="block_tower_number" value="{{ $flat->block_tower_number }}" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                            </div>

                            <!-- Floor -->
                            <div class="space-y-1 md:col-span-1">
                                <label for="floor" class="block text-[10px] font-black uppercase text-slate-505 tracking-wider">Floor Details</label>
                                <input type="text" id="floor" name="floor" value="{{ $flat->floor }}" required
                                    class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none font-medium">
                            </div>

                            <!-- Flat Number -->
                            <div class="space-y-1 md:col-span-1">
                                <label for="flat_number" class="block text-[10px] font-black uppercase text-slate-505 tracking-wider">Flat Number</label>
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
