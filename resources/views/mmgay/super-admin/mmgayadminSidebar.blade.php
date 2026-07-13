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

    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">

        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
           {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}">dashboard</span>
            <span class="text-sm">Dashboard</span>
        </a>

        <a href="{{ route('superadmin.possession.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
    {{ request()->routeIs('superadmin.possession.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

            <span
                class="material-symbols-outlined text-[22px]
    {{ request()->routeIs('superadmin.possession.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}">
                real_estate_agent
            </span>

            <span class="text-sm">Physical Possession</span>
        </a>

        <a href="{{ route('superadmin.districts') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
           {{ request()->routeIs('superadmin.districts') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->routeIs('superadmin.districts') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}">map</span>
            <span class="text-sm">Districts</span>
        </a>

        <a href="{{ route('superadmin.all-villages') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
           {{ request()->routeIs('superadmin.all-villages') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->routeIs('superadmin.all-villages') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}">holiday_village</span>
            <span class="text-sm">Villages</span>
        </a>

        <a href="{{ route('superadmin.beneficiaries.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
           {{ request()->routeIs('superadmin.beneficiaries.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->routeIs('superadmin.beneficiaries.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}">group</span>
            <span class="text-sm">Beneficiaries</span>
        </a>

        <a href="{{ route('superadmin.paid.beneficiaries') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
           {{ request()->routeIs('superadmin.paid.beneficiaries') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->routeIs('superadmin.paid.beneficiaries') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}">check_circle</span>
            <span class="text-sm">Paid Beneficiaries</span>
        </a>

        <a href="{{ route('superadmin.allotment.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
           {{ request()->routeIs('superadmin.allotment.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->routeIs('superadmin.allotment.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}">assignment</span>
            <span class="text-sm">Allotments</span>
        </a>

        <a href="{{ route('superadmin.assigned.flats') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
           {{ request()->routeIs('superadmin.assigned.flats') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->routeIs('superadmin.assigned.flats') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}">vpn_key</span>
            <span class="text-sm">Assigned Flats</span>
        </a>
    </nav>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <form action="{{ route('mmgay.logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 group">
                <span class="material-symbols-outlined text-[22px] text-red-400 group-hover:text-red-600">logout</span>
                <span>Logout</span>
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
