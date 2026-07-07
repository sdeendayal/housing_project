<!-- SIDE NAVIGATION (SideNavBar) -->
<aside
    class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-lg z-40 bg-surface-container shadow-none border-r border-outline-variant">
    <!-- Logo/Header -->
    <div class="px-md mb-xl flex items-center gap-sm">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-on-primary">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">home_work</span>
        </div>
        <div>
            <h1 class="text-headline-md font-headline-md text-on-surface font-bold leading-tight">MMGAY Admin</h1>
            <p class="text-[10px] uppercase tracking-wider text-on-surface-variant font-bold">Management Portal</p>
        </div>
    </div>
    <!-- Navigation Links -->
    <nav class="flex-1 px-sm space-y-base">
        <!-- Dashboard (Active) -->
        <a class="flex items-center gap-md bg-secondary-container text-on-secondary-container rounded-lg px-md py-sm border-l-4 border-primary transition-all duration-200 ease-in-out"
            href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-md text-label-md">Dashboard</span>
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
    class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 bg-gradient-to-r from-indigo-700 to-blue-600 shadow-lg">

    <div class="flex items-center justify-between h-full px-6">

        <!-- Left -->
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white">
                <!-- Dashboard Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-10h8V3h-8v8z" />
                </svg>
            </div>

            <div>
                <h2 class="text-xl font-bold text-white">
                    Super Admin Dashboard
                </h2>
                <p class="text-xs text-blue-100">
                    MMGAY Monitoring System
                </p>
            </div>
        </div>

        <!-- Right -->
        <div class="flex items-center gap-4">

            <!-- Date -->
            <div
                class="hidden md:flex items-center gap-2 bg-white/15 backdrop-blur px-4 py-2 rounded-xl border border-white/20">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-300" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10m-12 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                </svg>

                <span class="text-sm font-medium text-white">
                    {{ date('d M Y') }}
                </span>
            </div>

            <!-- Admin Badge -->
            <div class="flex items-center gap-2 bg-white/15 backdrop-blur px-4 py-2 rounded-xl border border-white/20">

                <div class="w-9 h-9 rounded-full bg-white text-indigo-700 flex items-center justify-center font-bold">
                    A
                </div>

                <div class="hidden lg:block text-left">
                    <p class="text-sm font-semibold text-white">
                        Super Admin
                    </p>
                    <p class="text-xs text-blue-100">
                        MMGAY Portal
                    </p>
                </div>

            </div>

        </div>

    </div>

</header>
