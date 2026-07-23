<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer - Activity Logs</title>
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
        <!-- Brand logo -->
        <div class="h-16 px-6 border-b border-slate-800 flex items-center gap-2.5 shrink-0 bg-slate-950">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-indigo-655 flex items-center justify-center shadow-md">
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
                    <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                        <i class="bi bi-journal-text text-sky-400"></i>
                        <span>Developer Logs</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Session Details -->
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
                <div class="md:hidden w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase">System Audit Trail Logs</h2>
                    <p class="text-[8px] text-slate-450 font-mono uppercase">Developer Action Logger Console</p>
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
                            <i class="bi bi-activity text-sky-500"></i>
                            System Audit Logs
                        </h3>
                        <p class="text-[8px] text-slate-400 font-mono mt-0.5 uppercase">Paginated database audit events</p>
                    </div>
                </div>

                <!-- Timeline List -->
                <div class="p-5 divide-y divide-slate-100 space-y-4">
                    @forelse($logs as $log)
                        <div class="flex gap-3 text-xs pt-4 first:pt-0">
                            <div class="flex flex-col items-center shrink-0">
                                @if($log->action === 'CREATED')
                                    <span class="w-6 h-6 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm shrink-0">
                                        <i class="bi bi-plus-lg text-[10px]"></i>
                                    </span>
                                @elseif($log->action === 'UPDATED')
                                    <span class="w-6 h-6 rounded-full bg-sky-50 border border-sky-200 flex items-center justify-center text-sky-600 shadow-sm shrink-0">
                                        <i class="bi bi-pencil text-[10px]"></i>
                                    </span>
                                @elseif($log->action === 'DELETED')
                                    <span class="w-6 h-6 rounded-full bg-red-50 border border-red-200 flex items-center justify-center text-red-500 shadow-sm shrink-0">
                                        <i class="bi bi-trash text-[10px]"></i>
                                    </span>
                                @else
                                    <span class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-650 shadow-sm shrink-0">
                                        <i class="bi bi-gear text-[10px]"></i>
                                    </span>
                                @endif
                            </div>

                            <div class="flex-1 space-y-0.5">
                                <div class="flex flex-wrap items-center justify-between gap-1">
                                    <div class="font-bold text-slate-850">
                                        @if($log->action === 'CREATED')
                                            <span class="text-emerald-700 font-extrabold uppercase text-[9px] bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded font-mono mr-1.5">CREATED</span>
                                        @elseif($log->action === 'UPDATED')
                                            <span class="text-sky-750 font-extrabold uppercase text-[9px] bg-sky-50 border border-sky-100 px-1.5 py-0.5 rounded font-mono mr-1.5">UPDATED</span>
                                        @elseif($log->action === 'DELETED')
                                            <span class="text-red-700 font-extrabold uppercase text-[9px] bg-red-50 border border-red-100 px-1.5 py-0.5 rounded font-mono mr-1.5">DELETED</span>
                                        @else
                                            <span class="text-slate-650 font-extrabold uppercase text-[9px] bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded font-mono mr-1.5">{{ $log->action }}</span>
                                        @endif
                                        <span class="text-slate-450 font-mono text-[10px]">IP: {{ $log->ip_address }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                </div>
                                <p class="text-slate-600 text-[11px] leading-relaxed">{{ $log->details }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-slate-400 italic text-center py-6 font-mono text-[10px]">
                            No logs registered in database sandbox.
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Footer -->
                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            </section>

        </div>
    </div>

</body>
</html>
