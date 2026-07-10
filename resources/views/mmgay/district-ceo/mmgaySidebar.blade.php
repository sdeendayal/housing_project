<!-- SIDE NAVIGATION (SideNavBar) -->
<aside
    class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-lg z-40 bg-surface-container shadow-none border-r border-outline-variant">
    <!-- Logo/Header -->
    <div class="px-md mb-xl flex items-center gap-sm">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-on-primary">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">home_work</span>
        </div>
        <div>
            <h1 class="text-headline-md font-headline-md text-on-surface font-bold leading-tight">MMGAY CEO</h1>
            <p class="text-[10px] uppercase tracking-wider text-on-surface-variant font-bold">Management Portal</p>
        </div>
    </div>
    <!-- Navigation Links -->
    <nav class="flex-1 px-sm space-y-base">
        <!-- Dashboard (Active) -->
        <!-- Dashboard -->
        <a href="{{ url('/district-ceo/dashboard') }}"
            class="flex items-center gap-md px-md py-sm rounded-lg transition-all duration-200
    {{ request()->routeIs('district.dashboard')
        ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary'
        : 'text-on-surface-variant hover:bg-surface-variant hover:text-on-surface' }}">

            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-md text-label-md">Dashboard</span>

        </a>

        <!-- Physical Possession -->
        <a href="{{ route('district.possession.dashboard') }}"
            class="flex items-center gap-md px-md py-sm rounded-lg transition-all duration-200
    {{ request()->routeIs('district.possession.*')
        ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary'
        : 'text-on-surface-variant hover:bg-surface-variant hover:text-on-surface' }}">

            <span class="material-symbols-outlined">
                home_pin
            </span>

            <span class="font-label-md text-label-md">
                Physical Possession
            </span>

        </a>
        <!-- Phases -->
        {{-- <a class="flex items-center gap-md text-on-surface-variant px-md py-sm hover:bg-surface-variant hover:text-on-surface transition-all duration-200 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">layers</span>
                <span class="font-label-md text-label-md">Phases</span>
            </a>
            <!-- Reports -->
            <a class="flex items-center gap-md text-on-surface-variant px-md py-sm hover:bg-surface-variant hover:text-on-surface transition-all duration-200 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">assessment</span>
                <span class="font-label-md text-label-md">Reports</span>
            </a>
            <!-- Settings -->
            <a class="flex items-center gap-md text-on-surface-variant px-md py-sm hover:bg-surface-variant hover:text-on-surface transition-all duration-200 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-label-md text-label-md">Settings</span>
            </a> --}}
    </nav>
    <!-- Footer / Support -->
    <div class="mt-auto px-md pt-lg border-t border-outline-variant">

        <form action="{{ route('mmgay.logout') }}" method="POST">
            @csrf

            <button type="submit"
                class="w-full flex items-center gap-md px-md py-sm rounded-lg
                   text-red-600 hover:bg-red-50 hover:text-red-700
                   transition-all duration-200">

                <span class="material-symbols-outlined">
                    logout
                </span>

                <span class="font-semibold">
                    Logout
                </span>

            </button>

        </form>

    </div>
</aside>
<!-- TOP HEADER (TopNavBar Mapping) -->
<header
    class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-6 bg-white shadow-xs border-b border-slate-100">

    {{-- LEFT SIDE: Scheme & Subtitle --}}
    <div class="flex items-center gap-4 min-w-0">
        <div class="min-w-0">
            <h2 class="text-sm md:text-base font-bold text-indigo-700 tracking-tight truncate uppercase">
                Mukhyamantri Gramin Awas Yojana
            </h2>
            <p class="text-[11px] font-semibold text-slate-400 tracking-wider uppercase mt-0.5">
                CEO Dashboard
            </p>
        </div>

        <div class="hidden md:block h-6 w-[1px] bg-slate-200"></div>

        {{-- Dynamic Application Counter Badge --}}
        {{-- <div
            class="hidden lg:flex items-center gap-2 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full shrink-0">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
            <span class="text-xs font-bold text-blue-700">
                Recent Applications: <span class="bg-blue-600 text-white px-1.5 py-0.5 rounded-md text-[10px] ml-1">12
                    New</span>
            </span>
        </div> --}}
    </div>

    {{-- RIGHT SIDE: Notifications & CEO Profile --}}
    <div class="flex items-center gap-5 shrink-0">

        {{-- Notification Icon with Active Indicator --}}
        <button
            class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-50 rounded-xl transition-colors active:scale-95 focus:outline-hidden cursor-pointer">
            <span class="material-symbols-outlined text-[24px] block">
                notifications
            </span>
            {{-- Pulse dot if new logs/applications arrive --}}
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
        </button>

        <div class="h-6 w-[1px] bg-slate-200"></div>

        {{-- User Profile (CEO Details) --}}
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                {{-- Dynamic Auth variables can be put here e.g., Auth::user()->name --}}
                {{-- <p class="text-xs md:text-sm font-bold text-slate-800 leading-tight">
                    {{ Auth::user()->name ?? 'Rajesh Kumar, IAS' }}
                </p> --}}
                <p
                    class="text-[10px] font-semibold font-bold  text-indigo-600 tracking-wide uppercase mt-0.5 flex items-center justify-end gap-1">
                    <span class="material-symbols-outlined text-[12px]">location_on</span>
                    {{ Auth::user()->name ?? 'CEO, Sonipat' }}
                </p>
            </div>

            {{-- CEO Initials Avatar (Fallbacks cleanly if image isn't configured) --}}
            <div
                class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-xs shadow-indigo-500/20 tracking-wider">
                CEO
            </div>
        </div>

    </div>
</header>
