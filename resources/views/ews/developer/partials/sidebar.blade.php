@php
    $sidebarUser = $user ?? auth()->user();
    
    // Resolve Zone Title
    $rawZone = $displayZoneName ?? (!empty($sidebarUser->district_name) ? $sidebarUser->district_name : (!empty($sidebarUser->zone_name) ? $sidebarUser->zone_name : 'Zone'));
    $zoneTitle = str_contains(strtoupper($rawZone), 'ZONE') ? strtoupper($rawZone) : strtoupper($rawZone) . ' Zone';

    // Current route & view checks
    $isDashboardRoute = request()->routeIs('ews.developer.dashboard') || request()->routeIs('ews.developer.flats.show');
    $currentViewParam = request('view') ?? ($currentView ?? null);
    $ownershipScopeParam = request('ownership_scope') ?? null;

    $isDashboardActive = $isDashboardRoute && (!$currentViewParam || $currentViewParam === 'dashboard');
    $isDistrictActive = ($isDashboardRoute && $currentViewParam === 'district') || request()->routeIs('ews.developer.districts-stats');
    $isMyFlatsActive = $isDashboardRoute && ($currentViewParam === 'my_flats' || $ownershipScopeParam === 'my_flats');
    $isRegisterFlatActive = request()->routeIs('ews.developer.flats.create') || request()->routeIs('ews.developer.flats.edit');
    $isDeveloperLogsActive = request()->routeIs('ews.developer.logs');
@endphp

<!-- DEEP NAVY / SLATE SIDEBAR -->
<aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 shrink-0 h-full shadow-xl z-20 select-none">
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
    <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto custom-scroll">
        <div>
            <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Navigation Console</span>
            <div class="space-y-1">
                <!-- 1. Dashboard -->
                <a href="{{ route('ews.developer.dashboard') }}" id="nav-dashboard"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs {{ $isDashboardActive ? 'bg-slate-800 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all">
                    <i class="bi bi-speedometer2 text-sky-400"></i>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Register Flat -->
                <a href="{{ route('ews.developer.flats.create') }}" id="nav-register-flat"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs {{ $isRegisterFlatActive ? 'bg-slate-800 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all">
                    <i class="bi bi-plus-circle {{ $isRegisterFlatActive ? 'text-sky-400' : 'text-slate-400' }}"></i>
                    <span>Register Flat</span>
                </a>
            </div>
        </div>

        <!-- Audit & Activity (Hidden for now) -->
        <div class="d-none hidden" style="display: none;">
            <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Audit & Activity</span>
            <div class="space-y-1">

                <!-- 7. Developer Logs -->
                <a href="{{ route('ews.developer.logs') }}" id="nav-developer-logs"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs {{ $isDeveloperLogsActive ? 'bg-slate-800 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium' }} transition-all">
                    <i class="bi bi-journal-text {{ $isDeveloperLogsActive ? 'text-sky-400' : 'text-slate-400' }}"></i>
                    <span>Developer Logs</span>
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
