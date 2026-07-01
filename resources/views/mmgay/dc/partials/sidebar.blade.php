<aside class="sidebar-gradient w-64 flex-shrink-0 flex flex-col text-slate-300">
    <!-- Sidebar Header -->
    <div class="p-6 flex items-center space-x-3">
        <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center">
            <i class="text-white w-6 h-6" data-lucide="shield-check"></i>
        </div>
        <div>
            <h1 class="font-bold text-white tracking-tight">MMGAY</h1>
            <p class="text-xs opacity-70">Deputy Commissioner</p>
        </div>
    </div>
    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto custom-scrollbar">
        <a href="{{ route('dc.dashboard') }}"
            class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors
    {{ request()->routeIs('dc.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-white/5 text-slate-300' }}">

            <i class="w-5 h-5" data-lucide="layout-dashboard"></i>
            <span class="font-medium">Dashboard</span>
        </a>
        {{-- <a class="flex items-center space-x-3 px-4 py-3 hover:bg-white/5 rounded-lg transition-colors" href="#">
            <i class="w-5 h-5 text-slate-400" data-lucide="users"></i>
            <span>Owner Applications</span>
        </a>
        <a class="flex items-center justify-between px-4 py-3 hover:bg-white/5 rounded-lg transition-colors group"
            href="#">
            <div class="flex items-center space-x-3">
                <i class="w-5 h-5 text-slate-400" data-lucide="hourglass"></i>
                <span>Pending Cases</span>
            </div>
            <span class="bg-blue-600/20 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded-full">24</span>
        </a>
        <a class="flex items-center justify-between px-4 py-3 hover:bg-white/5 rounded-lg transition-colors group"
            href="#">
            <div class="flex items-center space-x-3">
                <i class="w-5 h-5 text-slate-400" data-lucide="check-circle"></i>
                <span>Approved Cases</span>
            </div>
            <span class="bg-emerald-600/20 text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded-full">142</span>
        </a>
        <a class="flex items-center justify-between px-4 py-3 hover:bg-white/5 rounded-lg transition-colors group"
            href="#">
            <div class="flex items-center space-x-3">
                <i class="w-5 h-5 text-slate-400" data-lucide="x-circle"></i>
                <span>Rejected Cases</span>
            </div>
            <span class="bg-rose-600/20 text-rose-400 text-[10px] font-bold px-2 py-0.5 rounded-full">18</span>
        </a>
        <a class="flex items-center justify-between px-4 py-3 hover:bg-white/5 rounded-lg transition-colors group"
            href="#">
            <div class="flex items-center space-x-3">
                <i class="w-5 h-5 text-slate-400" data-lucide="rotate-ccw"></i>
                <span>Reconsidered Cases</span>
            </div>
            <span class="bg-indigo-600/20 text-indigo-400 text-[10px] font-bold px-2 py-0.5 rounded-full">11</span>
        </a> --}}





    </nav>
    <!-- Sidebar Footer (User Profile & Logout) -->
    <div class="p-4 border-t border-slate-700 bg-slate-900/50">

        <!-- User Info -->
        <div class="flex items-center space-x-3 mb-4">

            <div class="relative">
                <img class="w-10 h-10 rounded-full border border-slate-600"
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8ABC&color=fff"
                    alt="{{ auth()->user()->name }}" />

                <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full">
                </span>
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">
                    {{ auth()->user()->name }}
                </p>

                <p class="text-xs text-slate-400 truncate">
                    {{ ucfirst(auth()->user()->roleSlug() ?? 'User') }}
                </p>
            </div>

        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('dc.logout') }}">
            @csrf

            <button type="submit"
                class="w-full flex items-center justify-center space-x-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 py-2 rounded-lg transition-colors border border-rose-500/30">

                <i class="w-4 h-4" data-lucide="log-out"></i>
                <span class="text-sm font-medium">Logout</span>

            </button>
        </form>

    </div>
</aside>
