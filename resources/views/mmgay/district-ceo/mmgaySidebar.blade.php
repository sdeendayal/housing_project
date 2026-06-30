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
                href="{{ url('/district-ceo/dashboard') }}">
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
        class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-lg bg-surface-container-lowest shadow-sm border-b border-outline-variant">
        <div class="flex items-center gap-md">
            <h2 class="text-headline-md font-headline-md font-bold text-primary">Dashboard</h2>
            <div class="h-6 w-[1px] bg-outline-variant"></div>

        </div>
        <div class="flex items-center gap-md">
            <!-- Branch Selector -->


            <!-- User Profile -->
            <div class="flex items-center gap-sm pl-md border-l border-outline-variant">
                {{-- <div class="text-right hidden xl:block">
                    <p class="text-body-md font-body-md font-bold text-on-surface">Rajesh Kumar</p>
                    <p class="text-[11px] text-on-surface-variant">Admin Coordinator</p>
                </div> --}}
                {{-- <div class="w-10 h-10 rounded-full bg-secondary-container overflow-hidden">
                    <img class="w-full h-full object-cover"
                        data-alt="A professional headshot of a middle-aged South Asian male government official wearing a crisp white formal shirt, set against a blurred office background with subtle blue and gray tones. The lighting is soft and even, creating a professional and trustworthy executive portrait suitable for a corporate dashboard interface."
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuACVV_IXWKJ1auij7WlDx2Ex2mYP4fpdH_lLbCuY7QFjqZqybcecm2GZIuMi9ahEgHjUqK2SV6eHcoVhusuQPUOFX1y_3YFV1qG8JV2MOZsftHgMkvU8xHDahtIrIyWvDpiUF6Wy4Fkm_dqbGD6hCWz6AGejtIafkyD_6vbUd5_xxARDOLlWCDiDWYrta5gDp01MQ6anCIhzaKkjlcZ7sITn7X-s5ZRJ00WD93g48JZ5mHJh-uk8DoEo3_pjz51z5iFRKNBLAGODtar" />
                </div> --}}
            </div>
        </div>
    </header>