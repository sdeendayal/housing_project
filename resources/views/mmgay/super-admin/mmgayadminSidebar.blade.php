<aside
    class="fixed left-0 top-0 h-full w-[260px] flex flex-col z-40 bg-white border-r border-slate-200 shadow-sm transition-all duration-300">

    <div class="px-6 h-20 flex items-center gap-3 border-b border-slate-100">
        <div
            class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-indigo-200">
            <span class="material-symbols-outlined font-icon text-xl">home_work</span>
        </div>
        <div>
            <h1 class="text-base font-bold text-slate-800 leading-tight">MMGAY Admin</h1>
            <p class="text-[10px] uppercase tracking-wider text-indigo-600 font-bold">Management Portal</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="text-[13px] font-medium">Dashboard</span>

        </a>

        {{-- Section --}}
        <div class="px-3 pt-3 pb-1">
            <p class="text-[10px] uppercase tracking-[2px] text-slate-400 font-bold">
                Master
            </p>
        </div>

        <a href="{{ route('superadmin.possession.dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.possession.*') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px]">real_estate_agent</span>
            <span class="text-[13px] font-medium">Physical Possession</span>

        </a>

        <a href="{{ route('superadmin.districts') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.districts') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px]">map</span>
            <span class="text-[13px] font-medium">Districts</span>

        </a>

        <a href="{{ route('superadmin.all-villages') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.all-villages') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px]">holiday_village</span>
            <span class="text-[13px] font-medium">Villages</span>

        </a>

        {{-- Beneficiary --}}
        <div class="px-3 pt-4 pb-1">
            <p class="text-[10px] uppercase tracking-[2px] text-slate-400 font-bold">
                Beneficiary
            </p>
        </div>

        <a href="{{ route('superadmin.beneficiaries.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.beneficiaries.*') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px]">group</span>
            <span class="text-[13px] font-medium">Beneficiaries</span>

        </a>

        <a href="{{ route('superadmin.paid.beneficiaries') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.paid.beneficiaries') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px] text-green-600">check_circle</span>
            <span class="text-[13px] font-medium">Paid Beneficiaries</span>

        </a>

        <a href="{{ route('superadmin.allotment.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.allotment.*') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px]">assignment</span>
            <span class="text-[13px] font-medium">Allotments</span>

        </a>

        <a href="{{ route('superadmin.assigned.flats') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.assigned.flats') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px]">vpn_key</span>
            <span class="text-[13px] font-medium">Assigned Flats</span>

        </a>

        {{-- Registration --}}
        <div class="px-3 pt-4 pb-1">
            <p class="text-[10px] uppercase tracking-[2px] text-slate-400 font-bold">
                Registration
            </p>
        </div>

        <a href="{{ route('superadmin.total.registration') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.total.registration') ? 'bg-blue-50 border-l-4 border-blue-600 text-blue-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px] text-blue-600">description</span>
            <span class="text-[13px] font-medium">Total Registration</span>

        </a>

        <a href="{{ route('superadmin.matched.registration') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.matched.registration') ? 'bg-green-50 border-l-4 border-green-600 text-green-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px] text-green-600">task_alt</span>
            <span class="text-[13px] font-medium">Matched Registration</span>

        </a>

        <a href="{{ route('superadmin.unmatched.registration') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        {{ request()->routeIs('superadmin.unmatched.registration') ? 'bg-red-50 border-l-4 border-red-600 text-red-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4' }}">

            <span class="material-symbols-outlined text-[20px] text-red-600">highlight_off</span>
            <span class="text-[13px] font-medium">Unmatched Registration</span>

        </a>

    </nav>

    <!-- Bottom Profile & Logout -->

    <!-- Bottom Profile & Logout -->

    <div class="border-t border-slate-200 bg-gradient-to-r from-slate-50 to-white p-3">

        <!-- Profile -->

        <div class="flex items-center gap-2.5 mb-3 p-2.5 rounded-xl bg-white border border-slate-200 shadow-sm">

            <div
                class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 flex items-center justify-center text-white text-sm font-bold shadow">

                SA

            </div>

            <div>

                <h4 class="text-[12px] font-semibold text-slate-800 tracking-wide">
                    Super Admin
                </h4>

                <p class="text-[10px] text-slate-500">
                    MMGAY Management Portal
                </p>

            </div>

        </div>

        <!-- Logout -->

        <form action="{{ route('mmgay.logout') }}" method="POST">
            @csrf

            <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
            bg-red-50 border border-red-200 text-red-600 text-[12px] font-semibold
            hover:bg-red-600 hover:text-white hover:border-red-600
            transition-all duration-300">

                <span class="material-symbols-outlined text-[18px]">
                    logout
                </span>

                Logout

            </button>

        </form>

    </div>

</aside>

<header
    class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 bg-gradient-to-r from-indigo-700 to-blue-600 shadow-lg">

    <div class="flex items-center justify-between h-full px-6">

        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white">
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

        <div class="flex items-center gap-4">

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
