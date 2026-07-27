@php
    $isActive = fn(array|string $patterns) => collect((array) $patterns)->contains(
        fn($pattern) => request()->is($pattern),
    );

    $menuClass = function (array|string $patterns) use ($isActive) {
        return $isActive($patterns)
            ? 'border-indigo-100 bg-indigo-50 text-indigo-600 shadow-sm'
            : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    };

    $iconClass = function (array|string $patterns) use ($isActive) {
        return $isActive($patterns)
            ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
            : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600';
    };

    $cmsRoutes = [
        'mmsay-department-add-banner*',
        'mmsay-department-add-news*',
        'add-news*',
        'upload-notice*',
        'manage-notice*',
        'upload-tender*',
        'manage-tender*',
    ];

    $cmsIsActive = $isActive($cmsRoutes);
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-52 flex-col border-r border-slate-200 bg-white shadow-[4px_0_20px_rgba(15,23,42,0.04)]">

    {{-- Brand --}}
    <div class="flex h-20 shrink-0 items-center gap-3 border-b border-slate-100 px-4">

        <div
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-200">

            <img src="/Haryana_emblem.png" alt="Haryana State Emblem" class="h-7 w-7 object-contain brightness-0 invert" />
        </div>

        <div class="min-w-0 leading-tight">
            <h1 class="text-[13px] font-bold text-slate-800">
                Housing For All
            </h1>

            <p class="mt-1 text-[9px] font-bold uppercase tracking-wider text-indigo-600">
                Department Portal
            </p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-scroll flex-1 overflow-y-auto px-3 py-4">

        <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
            Overview
        </p>

        {{-- Dashboard --}}
        <a href="{{ url('mmsay-department-dashboard') }}"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
                   {{ $menuClass('mmsay-department-dashboard*') }}">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       {{ $iconClass('mmsay-department-dashboard*') }}">
                dashboard
            </span>

            <span class="min-w-0 flex-1 truncate">
                Dashboard
            </span>

            @if ($isActive('mmsay-department-dashboard*'))
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            @endif
        </a>

        <p class="mb-2 mt-5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
            Property
        </p>

        {{-- Property Registration --}}
        <a href="{{ url('mmsay-department-property-registration') }}"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
                   {{ $menuClass('mmsay-department-property-registration*') }}">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       {{ $iconClass('mmsay-department-property-registration*') }}">
                app_registration
            </span>

            <span class="min-w-0 flex-1 truncate">
                Property Registration
            </span>

            @if ($isActive('mmsay-department-property-registration*'))
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            @endif
        </a>

        {{-- Allotted Properties --}}
        <a href="{{ url('mmsay-department-allotted-properties') }}"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
                   {{ $menuClass('mmsay-department-allotted-properties*') }}">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       {{ $iconClass('mmsay-department-allotted-properties*') }}">
                location_city
            </span>

            <span class="min-w-0 flex-1 truncate">
                Allotted Properties
            </span>

            @if ($isActive('mmsay-department-allotted-properties*'))
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            @endif
        </a>

        {{-- EMI Calculation --}}
        <a href="{{ url('mmsay-department-property-emi-calculation') }}"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
                   {{ $menuClass('mmsay-department-property-emi-calculation*') }}">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       {{ $iconClass('mmsay-department-property-emi-calculation*') }}">
                calculate
            </span>

            <span class="min-w-0 flex-1 leading-tight">
                EMI Calculation
            </span>

            @if ($isActive('mmsay-department-property-emi-calculation*'))
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            @endif
        </a>

        {{-- Site Engineer --}}
        <a href="{{ url('mmsay-department-add-district-officer') }}"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
                   {{ $menuClass('mmsay-department-add-district-officer*') }}">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       {{ $iconClass('mmsay-department-add-district-officer*') }}">
                engineering
            </span>

            <span class="min-w-0 flex-1 truncate">
                Site Engineer
            </span>

            @if ($isActive('mmsay-department-add-district-officer*'))
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            @endif
        </a>

        <p class="mb-2 mt-5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
            Management
        </p>

        {{-- CMS Management --}}
        <div x-data="{ cmsOpen: {{ $cmsIsActive ? 'true' : 'false' }} }">

            <button type="button" @click="cmsOpen = !cmsOpen"
                class="group flex w-full items-center gap-3 rounded-xl border px-3 py-2.5 text-left text-[12px] font-medium transition-all duration-200
                       {{ $cmsIsActive
                           ? 'border-indigo-100 bg-indigo-50 text-indigo-600 shadow-sm'
                           : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                <span
                    class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                           {{ $cmsIsActive
                               ? 'bg-indigo-600 text-white'
                               : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600' }}">
                    edit_square
                </span>

                <span class="min-w-0 flex-1 truncate">
                    CMS Management
                </span>

                <span class="material-symbols-outlined text-[17px] transition-transform duration-200"
                    :class="{ 'rotate-180': cmsOpen }">
                    expand_more
                </span>
            </button>

            {{-- CMS submenu --}}
            <div x-cloak x-show="cmsOpen" x-collapse class="ml-4 mt-1.5 border-l border-slate-200 pl-3">

                <a href="{{ url('mmsay-department-add-banner') }}"
                    class="mb-1 flex items-center gap-2 rounded-lg px-3 py-2 text-[11px] font-medium transition
                           {{ $isActive('mmsay-department-add-banner*')
                               ? 'bg-indigo-50 text-indigo-600'
                               : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">

                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    Add Banner
                </a>

                <a href="{{ url('mmsay-department-add-news') }}"
                    class="mb-1 flex items-center gap-2 rounded-lg px-3 py-2 text-[11px] font-medium transition
                           {{ $isActive(['mmsay-department-add-news*', 'add-news*'])
                               ? 'bg-indigo-50 text-indigo-600'
                               : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">

                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    Add News
                </a>
            </div>
        </div>
    </nav>

    {{-- Footer --}}
    <div class="shrink-0 border-t border-slate-100 bg-slate-50/60 p-3">

        {{-- User --}}
        <div class="mb-3 flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm">

            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 text-xs font-bold text-white shadow-sm">

                {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 1)) }}
            </div>

            <div class="min-w-0 leading-tight">
                <p class="truncate text-xs font-semibold text-slate-800">
                    {{ auth()->user()->name ?? 'Department Admin' }}
                </p>

                <p class="mt-0.5 truncate text-[9px] text-slate-500">
                    MMSAY Management Portal
                </p>
            </div>
        </div>

        {{-- Logout --}}
        <a href="{{ route('logout') }}"
            class="flex h-10 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-100">

            <span class="material-symbols-outlined text-[18px]">
                logout
            </span>

            Logout
        </a>
    </div>
</aside>
