@php
    $sidebarUser = $user ?? auth()->user();
    $flatsCount = $stats['total_flats'] ?? \Illuminate\Support\Facades\DB::table('ews_builder_flats')->count();

    $isDashboardRoute = request()->routeIs('ews.tcp.dashboard');
    $isPossessionRoute = request()->routeIs('ews.tcp.possession*');
    $currentScope = request('scope', '');
    $isDashboardActive = $isDashboardRoute && empty($currentScope);
    $isAllFlatsActive = $isDashboardRoute && $currentScope === 'all';
    $isApprovedActive = $isDashboardRoute && $currentScope === 'dtp_approved';
    $isPendingActive = $isDashboardRoute && $currentScope === 'dtp_pending';
    $isAllottedActive = $isPossessionRoute && (empty($currentScope) || $currentScope === 'all');
    $isGivenActive = $isPossessionRoute && $currentScope === 'possession_given';
    $isPendingPossessionActive = $isPossessionRoute && $currentScope === 'possession_pending';
@endphp

<!-- DEEP NAVY / SLATE TCP SIDEBAR -->
<aside class="hidden md:flex flex-col w-60 bg-slate-900 text-slate-300 shrink-0 h-full shadow-xl z-20 select-none">
    <!-- Brand logo -->
    <div class="h-12 px-3.5 border-b border-slate-800 flex items-center gap-2.5 shrink-0 bg-slate-950">
        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center shadow-md shadow-cyan-500/20 p-1 shrink-0">
            <img src="{{ asset('Haryana_emblem.png') }}" class="w-5 h-5 object-contain brightness-0 invert" alt="Haryana Emblem">
        </div>
        <div class="overflow-hidden">
            <h1 class="text-xs font-black tracking-tight text-white uppercase truncate leading-tight">TCP Haryana</h1>
            <p class="text-[7.5px] text-cyan-400 font-mono tracking-wider uppercase truncate">Town & Country Planning</p>
        </div>
    </div>

    <!-- Menu Navigation -->
    <div class="flex-1 px-3 py-3 space-y-4 overflow-y-auto custom-scroll">
        <!-- Section: Builder Flats Monitoring -->
        <div>
            <span class="block px-2 text-[8.5px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Builder Flats Oversight</span>
            <div class="space-y-1">
                <!-- 1. Dashboard -->
                <a href="{{ route('ews.tcp.dashboard') }}" onclick="handleTcpNav('dashboard', event)" id="sidebar-nav-dashboard"
                    class="tcp-nav-item flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ $isDashboardActive ? 'bg-slate-800 text-white font-bold shadow-xs ring-1 ring-cyan-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all cursor-pointer">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-speedometer2 text-cyan-400 text-sm"></i>
                        <span>Dashboard</span>
                    </div>
                    <span class="tcp-nav-dot {{ $isDashboardActive ? '' : 'hidden' }} w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                </a>

                <!-- 2. Total Offered Flats -->
                <a href="{{ route('ews.tcp.dashboard', ['scope' => 'all']) }}" onclick="handleTcpNav('all', event)" id="sidebar-nav-all"
                    class="tcp-nav-item flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ $isAllFlatsActive ? 'bg-slate-800 text-white font-bold shadow-xs ring-1 ring-sky-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all cursor-pointer">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-building text-sky-400 text-sm"></i>
                        <span>Total Offered Flats</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-slate-800 text-sky-300 border border-slate-700">{{ number_format($flatsCount) }}</span>
                        <span class="tcp-nav-dot {{ $isAllFlatsActive ? '' : 'hidden' }} w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                    </div>
                </a>

                <!-- 3. Approved Units -->
                <a href="{{ route('ews.tcp.dashboard', ['scope' => 'dtp_approved']) }}" onclick="handleTcpNav('dtp_approved', event)" id="sidebar-nav-dtp_approved"
                    class="tcp-nav-item flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ $isApprovedActive ? 'bg-slate-800 text-white font-bold shadow-xs ring-1 ring-emerald-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all cursor-pointer">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-patch-check-fill text-emerald-400 text-sm"></i>
                        <span>Approved (STP)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-slate-800 text-emerald-300 border border-slate-700">{{ number_format($stats['dtp_approved_flats'] ?? 0) }}</span>
                        <span class="tcp-nav-dot {{ $isApprovedActive ? '' : 'hidden' }} w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                </a>

                <!-- 4. Pending Approval -->
                <a href="{{ route('ews.tcp.dashboard', ['scope' => 'dtp_pending']) }}" onclick="handleTcpNav('dtp_pending', event)" id="sidebar-nav-dtp_pending"
                    class="tcp-nav-item flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ $isPendingActive ? 'bg-slate-800 text-white font-bold shadow-xs ring-1 ring-amber-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all cursor-pointer">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-hourglass-split text-amber-400 text-sm"></i>
                        <span>Pending Approval</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-slate-800 text-amber-300 border border-slate-700">{{ number_format($stats['dtp_pending_flats'] ?? 0) }}</span>
                        <span class="tcp-nav-dot {{ $isPendingActive ? '' : 'hidden' }} w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section: Physical Possession (4,211 Master Pool - Dedicated Fresh Page) -->
        <div>
            <div class="flex items-center justify-between px-2 mb-1.5">
                <span class="text-[8.5px] font-black uppercase tracking-wider text-slate-400">Physical Possession</span>
                <span class="px-1 py-0.2 rounded text-[7.5px] font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">4,211 POOL</span>
            </div>
            <div class="space-y-1">
                <!-- 5. Total Allotted Pool (Opens New Fresh Page) -->
                <a href="{{ route('ews.tcp.possession') }}" id="sidebar-nav-possession-all"
                    class="tcp-nav-item flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ $isAllottedActive ? 'bg-slate-800 text-white font-bold shadow-xs ring-1 ring-indigo-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all cursor-pointer">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-houses-fill text-indigo-400 text-sm"></i>
                        <span>Total Allotted (4,211)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-slate-800 text-indigo-300 border border-slate-700">{{ number_format($stats['total_allotted'] ?? 4211) }}</span>
                        <span class="tcp-nav-dot {{ $isAllottedActive ? '' : 'hidden' }} w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    </div>
                </a>

                <!-- 6. Possession Given (Opens New Fresh Page with Given Filter) -->
                <a href="{{ route('ews.tcp.possession', ['scope' => 'possession_given']) }}" id="sidebar-nav-possession-given"
                    class="tcp-nav-item flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ $isGivenActive ? 'bg-slate-800 text-white font-bold shadow-xs ring-1 ring-emerald-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all cursor-pointer">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-patch-check-fill text-emerald-400 text-sm"></i>
                        <span>Possession Given</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-slate-800 text-emerald-300 border border-slate-700">{{ number_format($stats['possession_given'] ?? 1) }}</span>
                        <span class="tcp-nav-dot {{ $isGivenActive ? '' : 'hidden' }} w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                </a>

                <!-- 7. Pending Possession (Opens New Fresh Page with Pending Filter) -->
                <a href="{{ route('ews.tcp.possession', ['scope' => 'possession_pending']) }}" id="sidebar-nav-possession-pending"
                    class="tcp-nav-item flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs {{ $isPendingPossessionActive ? 'bg-slate-800 text-white font-bold shadow-xs ring-1 ring-rose-500/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all cursor-pointer">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-clock-history text-rose-400 text-sm"></i>
                        <span>Pending Possession</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-mono font-bold bg-slate-800 text-rose-300 border border-slate-700">{{ number_format($stats['possession_pending'] ?? 4210) }}</span>
                        <span class="tcp-nav-dot {{ $isPendingPossessionActive ? '' : 'hidden' }} w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Session & Logout Details -->
    <div class="p-3 border-t border-slate-800 bg-slate-950 flex flex-col gap-1.5 shrink-0">
        <div class="flex items-center justify-between text-[8px] text-slate-500 font-mono">
            <span>VERSION: 2.4-TCP</span>
            <span class="text-cyan-400 font-bold">STATE NODE</span>
        </div>
        <a href="{{ route('ews.tcp.logout') }}" class="w-full py-1 bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded-md text-[8.5px] font-black uppercase transition-all flex items-center justify-center gap-1 border border-rose-500/30">
            <i class="bi bi-power"></i>
            <span>Logout Session</span>
        </a>
    </div>
</aside>

<script>
function handleTcpNav(target, event) {
    if (typeof activateTcpNav === 'function' && window.location.pathname.indexOf('/ews/tcp/dashboard') !== -1) {
        if (event) {
            event.preventDefault();
        }
        activateTcpNav(target);
    }
}
</script>
