@php
    $currentStatus = request()->query('status', 'all_applicants');

    $isDashboardPage =
        request()->routeIs('district.dashboard') &&
        !request()->routeIs('district.dashboard.applicants') &&
        !request()->routeIs('district.dashboard.report');

    $isApplicantsPage = request()->routeIs('district.dashboard.applicants');

    $isVillageReportPage = request()->routeIs('district.dashboard.report') && request()->route('type') === 'villages';

    $sidebarCommonFilters = array_filter(
        [
            'phase' => request()->query('phase', 'all'),
            'village_id' => request()->query('village_id'),
        ],
        static fn($value) => $value !== null && $value !== '',
    );

    $sidebarApplicantUrl = static function (string $status) use ($sidebarCommonFilters) {
        return route('district.dashboard.applicants', array_merge($sidebarCommonFilters, ['status' => $status]));
    };

    $sidebarItemClass = static function (
        bool $active,
        string $activeClasses = 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-100 shadow-sm',
    ) {
        return $active ? $activeClasses : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    };

    $sidebarIconClass = static function (bool $active, string $activeClasses = 'bg-indigo-600 text-white') {
        return $active
            ? $activeClasses
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700';
    };
@endphp
<aside class="fixed left-0 top-0 z-40 flex h-full w-[230px] flex-col border-r border-slate-200 bg-white">
    <!-- Sidebar Brand -->
    <div class="border-b border-slate-100 px-4 py-4">
        <div class="flex items-center gap-3">

            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-sm">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">
                    home_work
                </span>
            </div>

            <div class="min-w-0">
                <h1 class="truncate text-base font-bold leading-tight text-slate-900">
                    MMGAY CEO
                </h1>

                <p class="mt-0.5 truncate text-[9px] font-bold uppercase tracking-wider text-slate-500">
                    Management Portal
                </p>
            </div>

        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4">

        {{-- ================= Master ================= --}}
        <div class="mb-5">
            <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Master
            </p>

            <div class="space-y-1">

                {{-- Dashboard --}}
                @php
                    $dashboardActive = $isDashboardPage;
                @endphp

                <a href="{{ route('district.dashboard') }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
                    {{ $sidebarItemClass($dashboardActive) }}">

                    <span
                        class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
                        {{ $sidebarIconClass($dashboardActive) }}">
                        dashboard
                    </span>

                    <span class="flex-1 font-medium">
                        Dashboard
                    </span>

                    @if ($dashboardActive)
                        <span class="h-2 w-2 shrink-0 rounded-full bg-indigo-600"></span>
                    @endif
                </a>

                {{-- Village Report --}}
                @php
                    $villageReportActive = $isVillageReportPage;
                @endphp

                <a href="{{ route('district.dashboard.report', array_merge($sidebarCommonFilters, ['type' => 'villages'])) }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
                    {{ $sidebarItemClass(
                        $villageReportActive,
                        'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-100 shadow-sm',
                    ) }}">

                    <span
                        class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
                        {{ $sidebarIconClass($villageReportActive, 'bg-blue-600 text-white') }}">
                        holiday_village
                    </span>

                    <span class="flex-1 font-medium">
                        Village Report
                    </span>

                    @if ($villageReportActive)
                        <span class="h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                    @endif
                </a>

                {{-- Applicants Report --}}
                @php
                    $allApplicantsActive = $isApplicantsPage && $currentStatus === 'all_applicants';
                @endphp

                <a href="{{ $sidebarApplicantUrl('all_applicants') }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
                    {{ $sidebarItemClass(
                        $allApplicantsActive,
                        'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-100 shadow-sm',
                    ) }}">

                    <span
                        class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
                        {{ $sidebarIconClass($allApplicantsActive, 'bg-indigo-600 text-white') }}">
                        groups
                    </span>

                    <span class="flex-1 font-medium">
                        Applicants Report
                    </span>

                    @if ($allApplicantsActive)
                        <span class="h-2 w-2 shrink-0 rounded-full bg-indigo-600"></span>
                    @endif
                </a>

            </div>
        </div>

        {{-- ================= Allotment ================= --}}
        <div class="mb-5">
            <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Allotment
            </p>

            <div class="space-y-1">

                @php
                    $allotmentLinks = [
                        [
                            'status' => 'allotted',
                            'label' => 'Allotted Applicants',
                            'icon' => 'home_work',
                            'active_item' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-100 shadow-sm',
                            'active_icon' => 'bg-blue-600 text-white',
                            'dot' => 'bg-blue-600',
                        ],
                        [
                            'status' => 'approved_paid',
                            'label' => 'Approved & Paid',
                            'icon' => 'paid',
                            'active_item' =>
                                'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-100 shadow-sm',
                            'active_icon' => 'bg-emerald-600 text-white',
                            'dot' => 'bg-emerald-600',
                        ],
                        [
                            'status' => 'approved_unpaid',
                            'label' => 'Approved & Unpaid',
                            'icon' => 'pending_actions',
                            'active_item' => 'bg-cyan-50 text-cyan-700 ring-1 ring-inset ring-cyan-100 shadow-sm',
                            'active_icon' => 'bg-cyan-600 text-white',
                            'dot' => 'bg-cyan-600',
                        ],
                        [
                            'status' => 'pending',
                            'label' => 'Yet to be Approved',
                            'icon' => 'hourglass_top',
                            'active_item' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-100 shadow-sm',
                            'active_icon' => 'bg-amber-500 text-white',
                            'dot' => 'bg-amber-500',
                        ],
                        [
                            'status' => 'rejected',
                            'label' => 'Rejected',
                            'icon' => 'cancel',
                            'active_item' => 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-100 shadow-sm',
                            'active_icon' => 'bg-rose-600 text-white',
                            'dot' => 'bg-rose-600',
                        ],
                        [
                            'status' => 'cancelled',
                            'label' => 'Cancelled',
                            'icon' => 'block',
                            'active_item' => 'bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-200 shadow-sm',
                            'active_icon' => 'bg-slate-700 text-white',
                            'dot' => 'bg-slate-700',
                        ],
                    ];
                @endphp

                @foreach ($allotmentLinks as $item)
                    @php
                        $active = $isApplicantsPage && $currentStatus === $item['status'];
                    @endphp

                    <a href="{{ $sidebarApplicantUrl($item['status']) }}"
                        class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
                        {{ $sidebarItemClass($active, $item['active_item']) }}">

                        <span
                            class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
                            {{ $sidebarIconClass($active, $item['active_icon']) }}">
                            {{ $item['icon'] }}
                        </span>

                        <span class="flex-1 font-medium">
                            {{ $item['label'] }}
                        </span>

                        @if ($active)
                            <span class="h-2 w-2 shrink-0 rounded-full {{ $item['dot'] }}"></span>
                        @endif
                    </a>
                @endforeach

            </div>
        </div>

        {{-- ================= Registration ================= --}}
        <div class="mb-5">
            <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Registration
            </p>

            <div class="space-y-1">

                @php
                    $registrationLinks = [
                        [
                            'status' => 'registry_allotted',
                            'label' => 'Registry To Be Done',
                            'icon' => 'assignment',
                            'active_item' => 'bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-100 shadow-sm',
                            'active_icon' => 'bg-violet-600 text-white',
                            'dot' => 'bg-violet-600',
                        ],
                        [
                            'status' => 'registry_done',
                            'label' => 'Registry Done',
                            'icon' => 'task_alt',
                            'active_item' => 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-100 shadow-sm',
                            'active_icon' => 'bg-green-600 text-white',
                            'dot' => 'bg-green-600',
                        ],
                        [
                            'status' => 'registry_pending',
                            'label' => 'Registry Yet to Be Done',
                            'icon' => 'link_off',
                            'active_item' => 'bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-100 shadow-sm',
                            'active_icon' => 'bg-orange-600 text-white',
                            'dot' => 'bg-orange-600',
                        ],
                    ];
                @endphp

                @foreach ($registrationLinks as $item)
                    @php
                        $active = $isApplicantsPage && $currentStatus === $item['status'];
                    @endphp

                    <a href="{{ $sidebarApplicantUrl($item['status']) }}"
                        class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
                        {{ $sidebarItemClass($active, $item['active_item']) }}">

                        <span
                            class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
                            {{ $sidebarIconClass($active, $item['active_icon']) }}">
                            {{ $item['icon'] }}
                        </span>

                        <span class="flex-1 font-medium">
                            {{ $item['label'] }}
                        </span>

                        @if ($active)
                            <span class="h-2 w-2 shrink-0 rounded-full {{ $item['dot'] }}"></span>
                        @endif
                    </a>
                @endforeach

            </div>
        </div>

        {{-- ================= Possession ================= --}}
        <div>
            <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Possession
            </p>

            <div class="space-y-1">
                <div class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-400">

                    <span
                        class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[20px] text-slate-400">
                        key
                    </span>

                    <span class="flex-1 font-medium">
                        Possession Report
                    </span>

                    <span class="rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-600">
                        Soon
                    </span>
                </div>
            </div>
        </div>

    </nav>

    <!-- Footer -->
    <div class="mt-auto border-t border-slate-200 p-3">

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">

            <div class="flex items-center gap-3">

                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-blue-600 text-[12px] font-bold text-white">
                    CEO
                </div>

                <div class="min-w-0">

                    <p class="truncate text-sm font-bold text-slate-800">
                        {{ Auth::user()->name ?? 'District CEO' }}
                    </p>

                    <p class="mt-0.5 flex items-center gap-1 truncate text-[10px] font-medium text-slate-500">
                        <span class="material-symbols-outlined text-[12px]">
                            location_on
                        </span>

                        {{ Auth::user()->district_name ?? 'District Office' }}
                    </p>

                </div>

            </div>

        </div>

        <form action="{{ route('mmgay.logout') }}" method="POST" class="mt-2.5">
            @csrf

            <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                <span class="material-symbols-outlined text-[19px]">
                    logout
                </span>

                Logout
            </button>
        </form>

    </div>
</aside>


<!-- TOP HEADER -->
<header
    class="fixed right-0 top-0 z-50 flex h-[68px] w-[calc(100%-230px)] items-center justify-between overflow-hidden bg-gradient-to-r from-indigo-700 via-blue-700 to-blue-600 px-5 shadow-md">
    <!-- Decorative -->
    <div class="pointer-events-none absolute -left-14 -top-20 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>

    <div class="pointer-events-none absolute -bottom-24 right-1/3 h-44 w-44 rounded-full bg-indigo-300/10 blur-3xl">
    </div>

    <!-- Left -->
    <div class="relative flex min-w-0 items-center gap-3">

        <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white backdrop-blur-sm">
            <span class="material-symbols-outlined text-[23px]" style="font-variation-settings: 'FILL' 1;">
                space_dashboard
            </span>
        </div>

        <div class="min-w-0">

            <h2 class="truncate text-lg font-bold leading-tight text-white">
                District CEO Dashboard
            </h2>

            <p class="mt-0.5 truncate text-[11px] font-medium text-blue-100">
                Mukhyamantri Gramin Awas Yojana Monitoring System
            </p>

        </div>

    </div>

    <!-- Right -->
    <div class="relative flex shrink-0 items-center gap-2.5">



        <!-- Date -->
        <div
            class="hidden items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-white backdrop-blur-sm md:flex">
            <span class="material-symbols-outlined text-[19px]">
                calendar_month
            </span>

            <span class="text-xs font-semibold">
                {{ now()->format('d M Y') }}
            </span>
        </div>

        <!-- Notification -->
        <button type="button"
            class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 bg-white/10 text-white transition hover:bg-white/20">
            <span class="material-symbols-outlined text-[21px]">
                notifications
            </span>

            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-rose-400 ring-2 ring-blue-600"></span>
        </button>

        <!-- CEO Profile -->
        <div
            class="flex items-center gap-2.5 rounded-xl border border-white/15 bg-white/10 px-2.5 py-1.5 text-white backdrop-blur-sm">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-indigo-700">
                CEO
            </div>

            <div class="hidden min-w-0 sm:block">

                <p class="max-w-[120px] truncate text-xs font-bold leading-tight">
                    {{ Auth::user()->name ?? 'District CEO' }}
                </p>

                <p class="mt-0.5 max-w-[120px] truncate text-[10px] font-medium text-blue-100">
                    {{ Auth::user()->district_name ?? 'MMGAY Portal' }}
                </p>

            </div>

        </div>

    </div>
</header>
