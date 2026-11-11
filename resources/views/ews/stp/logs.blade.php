<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS STP - Activity Logs</title>
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

    @include('ews.stp.partials.sidebar')

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
                    <p class="text-[8px] text-slate-450 font-mono uppercase">STP Action Logger Console</p>
                </div>
            </div>

            <!-- Profile context details -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-[10px] text-slate-650 font-bold flex items-center gap-1 justify-end">
                        <span>{{ $user->name }}</span>
                        @if(!empty($displayZoneName))
                            <span class="text-[9px] bg-sky-100 text-sky-800 font-extrabold uppercase px-1.5 py-0.5 rounded border border-sky-200">({{ $displayZoneName }})</span>
                        @endif
                    </div>
                    <div class="text-[8.5px] text-slate-500 font-mono">Zone: <span class="font-bold text-slate-700 uppercase">{{ $displayZoneName ?? 'N/A' }}</span> | Mobile: {{ $user->mobile }}</div>
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

                            <div class="flex-1 space-y-1">
                                <div class="flex flex-wrap items-center justify-between gap-1">
                                    <div class="font-bold text-slate-800 flex items-center gap-2 flex-wrap">
                                        @if($log->action === 'CREATED')
                                            <span class="text-emerald-700 font-extrabold uppercase text-[9px] bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded font-mono">CREATED</span>
                                        @elseif(str_contains($log->action, 'UPDATE'))
                                            <span class="text-amber-700 font-extrabold uppercase text-[9px] bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded font-mono">MODIFIED</span>
                                        @elseif($log->action === 'DELETED')
                                            <span class="text-red-700 font-extrabold uppercase text-[9px] bg-red-50 border border-red-100 px-1.5 py-0.5 rounded font-mono">DELETED</span>
                                        @else
                                            <span class="text-slate-650 font-extrabold uppercase text-[9px] bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded font-mono">{{ $log->action }}</span>
                                        @endif
                                        <span class="text-slate-700 font-bold text-xs flex items-center gap-1">
                                            <i class="bi bi-person-fill text-slate-400"></i>
                                            <span>{{ $log->developer->name ?? 'System User' }}</span>
                                            @if(!empty($log->developer->role))
                                                <span class="text-[8.5px] uppercase font-mono px-1 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">({{ $log->developer->role }})</span>
                                            @endif
                                        </span>
                                        <span class="text-slate-400 font-mono text-[9.5px]">IP: {{ $log->ip_address ?: '127.0.0.1' }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono flex items-center gap-1">
                                        <i class="bi bi-clock text-[9px]"></i> {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </span>
                                </div>
                                <div class="text-slate-700 text-xs bg-slate-50/80 rounded-lg p-2.5 border border-slate-150 leading-relaxed font-medium">
                                    {{ $log->details }}
                                </div>
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
