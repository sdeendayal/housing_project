@php
    $activeNav = $activeNav ?? '';
    $sidebarName = $displayName ?? ($fullName ?? (auth()->user()?->name ?? 'Citizen'));
    $sidebarAppId = $applicationId ?? ($applicationNo ?? '—');
    $ppSubmenuOpen = in_array($activeNav, ['pp-apply', 'pp-applications', 'pp-application-show', 'pp-download'], true);
    $ppHasApplication = auth()->check() && \App\Models\PhysicalPossessionApplication::where('user_id', auth()->id())->exists();
    $latestPpApplication = $ppHasApplication
        ? \App\Models\PhysicalPossessionApplication::where('user_id', auth()->id())->latest()->first()
        : null;
@endphp

<nav id="sidebar"
    class="sidebar-v2 fixed flex flex-col left-0 top-0 h-full w-[228px] z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

    <div class="px-3.5 pt-4 pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
            <div class="sidebar-brand-icon flex items-center justify-center shrink-0">
                <img alt="Haryana" class="w-6 h-6 object-contain brightness-0 invert" src="Haryana_emblem.png" />
            </div>
            <div class="min-w-0">
                <p class="text-[12px] font-extrabold text-slate-800 leading-tight truncate">Housing For All</p>
                <p class="text-[9px] font-semibold text-indigo-500">Govt. of Haryana</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-2 py-3">
        <p class="text-[8px] font-bold uppercase tracking-widest text-slate-400 px-2 mb-1.5">Menu</p>

        <a class="nav-v2 {{ $activeNav === 'dashboard' ? 'active' : '' }}" href="{{ route('citizen.dashboard') }}">
            <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]" @if($activeNav === 'dashboard') style="font-variation-settings:'FILL' 1" @endif>dashboard</span></span>
            Dashboard
        </a>
        <a class="nav-v2 {{ $activeNav === 'payments' ? 'active' : '' }}" href="{{ route('citizen.payment-status') }}">
            <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]" @if($activeNav === 'payments') style="font-variation-settings:'FILL' 1" @endif>payments</span></span>
            Payments
        </a>

        <div class="pp-nav-group mb-1">
            <button type="button"
                    id="ppNavToggle"
                    class="nav-v2 w-full border-0 cursor-pointer {{ $ppSubmenuOpen ? 'pp-nav-group-open' : '' }}"
                    aria-expanded="{{ $ppSubmenuOpen ? 'true' : 'false' }}"
                    aria-controls="ppNavSubmenu">
                <span class="nav-v2-icon">
                    <span class="material-symbols-outlined text-[15px]">home_work</span>
                </span>
                <span class="flex items-center gap-1 min-w-0 flex-1 text-left">
                    <span class="truncate">Physical Possession</span>
                    <span class="shrink-0 text-[7px] font-extrabold uppercase px-1 py-0.5 rounded bg-amber-100 text-amber-700">New</span>
                </span>
                <span id="ppNavChevron" class="material-symbols-outlined text-[18px] text-slate-400 transition-transform duration-200 shrink-0 {{ $ppSubmenuOpen ? 'rotate-180' : '' }}">expand_more</span>
            </button>

            <div id="ppNavSubmenu" class="pp-nav-submenu {{ $ppSubmenuOpen ? '' : 'hidden' }}">
                @unless($ppHasApplication)
                <a class="nav-v2 {{ $activeNav === 'pp-apply' ? 'active' : '' }} pl-7" href="{{ route('pp.user.apply') }}">
                    <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]" @if($activeNav === 'pp-apply') style="font-variation-settings:'FILL' 1" @endif>edit_document</span></span>
                    Apply Online
                </a>
                <a class="nav-v2 {{ $activeNav === 'pp-download' ? 'active' : '' }} pl-7" href="{{ route('pp.user.download-form') }}">
                    <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]" @if($activeNav === 'pp-download') style="font-variation-settings:'FILL' 1" @endif>download</span></span>
                    Download Possession Form
                </a>
                @else
                <a class="nav-v2 {{ $activeNav === 'pp-application-show' ? 'active' : '' }} pl-7" href="{{ route('pp.user.application.show', $latestPpApplication) }}">
                    <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]" @if($activeNav === 'pp-application-show') style="font-variation-settings:'FILL' 1" @endif>visibility</span></span>
                    View My Application
                </a>
                @endunless
                <a class="nav-v2 {{ $activeNav === 'pp-applications' ? 'active' : '' }} pl-7" href="{{ route('pp.user.applications') }}">
                    <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]" @if($activeNav === 'pp-applications') style="font-variation-settings:'FILL' 1" @endif>folder_open</span></span>
                    My Applications
                </a>
            </div>
        </div>

        <p class="text-[8px] font-bold uppercase tracking-widest text-slate-400 px-2 mb-1.5 mt-3">Support</p>

        <a class="nav-v2 {{ $activeNav === 'status' ? 'active' : '' }}" href="#">
            <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]">track_changes</span></span>
            App Status
        </a>
        <a class="nav-v2 {{ $activeNav === 'grievances' ? 'active' : '' }}" href="#">
            <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]">support_agent</span></span>
            Grievances
        </a>
        <a class="nav-v2 {{ $activeNav === 'profile' ? 'active' : '' }}" href="{{ route('citizen.profile') }}">
            <span class="nav-v2-icon"><span class="material-symbols-outlined text-[15px]" @if($activeNav === 'profile') style="font-variation-settings:'FILL' 1" @endif>account_circle</span></span>
            Profile
        </a>
    </div>

    <div class="p-2.5 border-t border-slate-100">
        <div class="sidebar-user-v2 p-2 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-white text-[14px]">person</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold text-slate-800 truncate">{{ $sidebarName }}</p>
                <p class="text-[8px] text-slate-400 truncate">{{ $sidebarAppId }}</p>
            </div>
            <a href="{{ route('citizen.logout') }}" class="text-slate-400 hover:text-red-500" title="Logout">
                <span class="material-symbols-outlined text-[16px]">logout</span>
            </a>
        </div>
    </div>
</nav>

<div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden"></div>
