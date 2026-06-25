<nav class="bg-white border-b border-slate-200 shadow-sm relative z-40">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-wrap items-center justify-center md:justify-start space-x-1 md:space-x-4 py-2 text-center">
            <a href="/"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors
                {{ request()->is('/') ? 'text-civic-blue border-b-2 border-civic-accent bg-slate-50' : 'text-slate-600 hover:text-civic-blue hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-[18px]">
                    home
                </span>
                Home
            </a>
            <div class="relative group">
                <button
                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors
                    {{ request()->is('introduction') || request()->is('organisation-chart') || request()->is('whos-who')
                        ? 'text-civic-blue border-b-2 border-civic-accent bg-slate-50'
                        : 'text-slate-600 hover:text-civic-blue hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[18px]">
                        info
                    </span>
                    About Us
                    <span class="material-symbols-outlined text-[18px] transition-transform group-hover:rotate-180">
                        expand_more
                    </span>
                </button>
                <div
                    class="absolute left-0 mt-1 w-64 bg-white border border-slate-200 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden">
                    <!-- Introduction -->
                    <a href="/introduction"
                        class="flex items-center gap-3 px-5 py-3 text-sm border-l-4 transition
                        {{ request()->is('introduction')
                            ? 'bg-blue-50 text-civic-blue border-civic-accent font-medium'
                            : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-civic-blue' }}">
                        <span class="material-symbols-outlined text-[18px]">
                            description
                        </span>
                        Introduction
                    </a>
                    <a href="/organisation-chart"
                        class="flex items-center gap-3 px-5 py-3 text-sm border-t border-slate-100 transition
                        {{ request()->is('organisation-chart')
                            ? 'bg-blue-50 text-civic-blue font-medium'
                            : 'text-slate-700 hover:bg-slate-50 hover:text-civic-blue' }}">
                        <span class="material-symbols-outlined text-[18px]">
                            account_tree
                        </span>
                        Organisation Chart
                    </a>
                    <a href="/whos-who"
                        class="flex items-center gap-3 px-5 py-3 text-sm border-t border-slate-100 transition
                        {{ request()->is('whos-who')
                            ? 'bg-blue-50 text-civic-blue font-medium'
                            : 'text-slate-700 hover:bg-slate-50 hover:text-civic-blue' }}">
                        <span class="material-symbols-outlined text-[18px]">
                            groups
                        </span>
                        Who's Who
                    </a>
                </div>
            </div>
            <a href="#"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors">
                <span class="material-symbols-outlined text-[18px]">
                    visibility
                </span>
                Our Vision
            </a>
            <a href="#"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors">
                <span class="material-symbols-outlined text-[18px]">
                    photo_library
                </span>
                Gallery
            </a>

            {{-- Physical Possession Login Dropdown --}}
            <div class="relative group">
                <button
                    class="flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-md transition-colors text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 shadow-sm">
                    <span class="pp-scheme-new-badge-sm mr-0">🔥 NEW</span>
                    <span class="material-symbols-outlined text-[18px]">home_work</span>
                    Physical Possession
                    <span class="material-symbols-outlined text-[18px] transition-transform group-hover:rotate-180">expand_more</span>
                </button>
                <div class="absolute left-0 mt-1 w-56 bg-white border border-slate-200 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden">
                    <a href="{{ route('citizen.login') }}"
                       class="flex items-center gap-3 px-5 py-3 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-700 transition border-l-4 border-transparent hover:border-amber-500">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        User Login / Apply
                    </a>
                    <a href="{{ route('pp.department.login') }}"
                       class="flex items-center gap-3 px-5 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition border-t border-slate-100 border-l-4 border-transparent hover:border-indigo-500">
                        <span class="material-symbols-outlined text-[18px]">shield_person</span>
                        District Officer Login
                    </a>
                    {{--
                    <a href="{{ route('pp.landing') }}"
                       class="flex items-center gap-3 px-5 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition border-t border-slate-100 border-l-4 border-transparent hover:border-blue-500">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        Scheme Details
                    </a>
                    --}}
                </div>
            </div>

            <a href="/help"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-md shadow-sm transition-colors uppercase tracking-wide border border-slate-200
                {{ request()->is('help')
                    ? 'bg-white text-civic-blue ring-2 ring-civic-blue/20'
                    : 'bg-white text-civic-blue hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-[18px]">
                    help
                </span>
                Help
            </a>
        </div>
    </div>
</nav>
