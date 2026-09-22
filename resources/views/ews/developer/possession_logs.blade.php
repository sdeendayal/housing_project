<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS STP - Possession Audit Trail</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .code-font { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dev-shadow { box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08); }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    @include('ews.developer.partials.sidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.developer.possession.index') }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-200 flex items-center justify-center text-slate-600 transition-all">
                    <i class="bi bi-arrow-left text-sm"></i>
                </a>
                <div>
                    <h2 class="text-sm font-black uppercase text-slate-800 tracking-wide">Possession Audit Trail & History</h2>
                    <p class="text-[10px] text-slate-500 font-medium">Complete immutable activity track of possession submissions, status changes, and geo-coordinates.</p>
                </div>
            </div>
        </header>

        <!-- Log List Body -->
        <div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-4">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm dev-shadow overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="text-xs font-black uppercase tracking-wider text-slate-700">Audit Records ({{ $logs->total() }})</div>
                    <div class="text-[11px] text-slate-500 font-mono">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-extrabold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="py-3 px-4">#</th>
                                <th class="py-3 px-4">App No / Ben ID</th>
                                <th class="py-3 px-4">Action</th>
                                <th class="py-3 px-4">Status Transition</th>
                                <th class="py-3 px-4">Performed By</th>
                                <th class="py-3 px-4">Coordinates</th>
                                <th class="py-3 px-4">Client Info</th>
                                <th class="py-3 px-4">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-4 font-mono text-slate-400">{{ $log->id }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-mono font-bold text-slate-900">#{{ $log->application_number }}</div>
                                        <div class="text-[10px] text-slate-400">ID: {{ $log->beneficiary_id }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-1.5 text-xs font-bold">
                                            <span class="text-slate-400">{{ $log->old_status ?: 'NONE' }}</span>
                                            <i class="bi bi-arrow-right text-[10px] text-slate-400"></i>
                                            <span class="{{ $log->new_status === 'GIVEN' ? 'text-emerald-600 font-black' : 'text-amber-600' }}">{{ $log->new_status }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-800">{{ $log->stp_user_name ?: 'STP User' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">UID: {{ $log->stp_user_id }}</div>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[11px]">
                                        @if($log->latitude && $log->longitude)
                                            <a href="https://www.google.com/maps?q={{ $log->latitude }},{{ $log->longitude }}" target="_blank" class="text-sky-600 hover:underline flex items-center gap-1">
                                                <i class="bi bi-geo-alt"></i> {{ round($log->latitude, 5) }}, {{ round($log->longitude, 5) }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-[11px] text-slate-500 font-mono">
                                        <div>IP: {{ $log->ip_address ?: '-' }}</div>
                                        <div class="truncate max-w-xs text-[10px] text-slate-400">{{ $log->app_version ?: ($log->user_agent ? substr($log->user_agent, 0, 30) . '...' : '-') }}</div>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-500 text-[11px]">
                                        {{ $log->created_at ? $log->created_at->format('d M Y, h:i A') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-slate-400 font-medium">
                                        No possession audit logs recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-200">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
