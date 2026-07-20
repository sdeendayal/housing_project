<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer - District Wise Summary</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;950&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .outfit-font {
            font-family: 'Outfit', sans-serif;
        }
        .dev-shadow {
            box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08);
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    <!-- DEEP NAVY / SLATE SIDEBAR -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 shrink-0 h-full shadow-xl z-20">
        <!-- Brand logo -->
        <div class="h-16 px-6 border-b border-slate-800 flex items-center gap-2.5 shrink-0 bg-slate-950">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="bi bi-shield-fill-check text-white text-sm"></i>
            </div>
            <div>
                <h1 class="text-xs font-black tracking-tight text-white uppercase">EWS Portal</h1>
                <p class="text-[8px] text-slate-500 font-mono tracking-widest uppercase">Developer Hub</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">
            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Registry Matrix</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-folder text-slate-400"></i>
                        <span>Flats Registry</span>
                    </a>
                    <a href="{{ route('ews.developer.flats.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-plus-circle text-slate-400"></i>
                        <span>Register Flat</span>
                    </a>
                </div>
            </div>

            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Audit & Summary</span>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                        <i class="bi bi-map text-sky-400"></i>
                        <span>District Stats</span>
                    </a>
                    <a href="{{ route('ews.developer.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-activity text-slate-400"></i>
                        <span>Developer logs</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Session Details -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex flex-col gap-2 shrink-0">
            <div class="flex items-center justify-between text-[9px] text-slate-500 font-mono">
                <span>VERSION: 2.4-stable</span>
            </div>
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
                <div class="md:hidden w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600">
                    <i class="bi bi-map"></i>
                </div>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase outfit-font">District Wise Flat counts</h2>
                    <p class="text-[8px] text-slate-455 font-mono uppercase">Geographical Summary Statistics</p>
                </div>
            </div>

            <!-- Profile context details -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-[10px] text-slate-650 font-bold flex items-center gap-1 justify-end">
                        <span>{{ $user->name }}</span>
                        @if(!empty($user->district_name))
                            <span class="text-[9px] bg-sky-100 text-sky-800 font-extrabold uppercase px-1.5 py-0.5 rounded border border-sky-200">({{ strtoupper($user->district_name) }})</span>
                        @endif
                    </div>
                    <div class="text-[8.5px] text-slate-500 font-mono">District: <span class="font-bold text-slate-700 uppercase">{{ $user->district_name ?? 'N/A' }}</span> | Mobile: {{ $user->mobile }}</div>
                </div>
            </div>
        </header>

        <!-- Main Content Workspace -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            
            <section class="bg-white border border-slate-200/80 rounded-xl shadow-sm dev-shadow overflow-hidden max-w-4xl mx-auto">
                <div class="px-5 py-4 border-b border-slate-150 bg-slate-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <i class="bi bi-map text-sky-500"></i>
                            District Statistics
                        </h3>
                        <p class="text-[8px] text-slate-400 font-mono mt-0.5 uppercase">Flats counts by district (alphabetical)</p>
                    </div>
                    <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded">
                        Total Districts: 23
                    </span>
                </div>

                <!-- Table Content -->
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($districts as $district)
                            <a href="{{ route('ews.developer.dashboard') }}?district_id={{ $district->id }}" class="block transition-all hover:-translate-y-0.5">
                                <div class="flex items-center justify-between p-3.5 bg-slate-50 hover:bg-indigo-50/20 hover:border-indigo-200 border border-slate-150 rounded-xl transition-all shadow-sm group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center font-mono text-[10px] font-black text-slate-500 shadow-sm group-hover:border-indigo-300 transition-all">
                                            {{ str_pad($district->id, 2, '0', STR_PAD_LEFT) }}
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-black text-slate-850 uppercase tracking-wide group-hover:text-indigo-605 transition-all">{{ $district->name }}</h4>
                                            <p class="text-[8px] text-slate-400 font-mono uppercase">State Code: HR</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($district->flats_count > 0)
                                            <span class="inline-block bg-indigo-50 border border-indigo-100 rounded-lg px-2.5 py-1 text-xs font-black text-indigo-700 font-mono shadow-sm group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all">
                                                {{ $district->flats_count }} {{ $district->flats_count == 1 ? 'Flat' : 'Flats' }}
                                            </span>
                                        @else
                                            <span class="inline-block bg-slate-100 border border-slate-200/50 rounded-lg px-2.5 py-1 text-xs font-bold text-slate-400 font-mono">
                                                0 Flats
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50 text-right">
                    <a href="{{ route('ews.developer.dashboard') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-250 text-slate-700 font-bold uppercase rounded text-[9px] shadow-sm">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </section>

        </div>
    </div>

</body>
</html>
