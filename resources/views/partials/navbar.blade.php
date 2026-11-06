<nav class="bg-white border-b border-slate-200 shadow-sm relative z-40">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-wrap items-center justify-center md:justify-start space-x-1 md:space-x-4 py-2 text-center">

            {{-- Home --}}
            <a href="{{ route('home') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors
                {{ request()->routeIs('home') ? 'text-civic-blue border-b-2 border-civic-accent bg-slate-50' : 'text-slate-600 hover:text-civic-blue hover:bg-slate-50' }}">
                <span class="material-symbols-outlined notranslate text-[18px]" translate="no">home</span>
                <span class="nav-home-text">Home</span>
            </a>

            {{-- About Us --}}
            <div class="relative group">
                <button
                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors
                    {{ request()->routeIs('introduction','organisation.chart','whos.who')
                        ? 'text-civic-blue border-b-2 border-civic-accent bg-slate-50'
                        : 'text-slate-600 hover:text-civic-blue hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined notranslate text-[18px]" translate="no">info</span>
                    About Us
                    <span class="material-symbols-outlined notranslate text-[18px] transition-transform group-hover:rotate-180" translate="no">
                        expand_more
                    </span>
                </button>

                <div class="absolute left-0 mt-1 w-64 bg-white border border-slate-200 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden">

                    <a href="{{ route('introduction') }}"
                        class="flex items-center gap-3 px-5 py-3 text-sm border-l-4 transition
                        {{ request()->routeIs('introduction')
                            ? 'bg-blue-50 text-civic-blue border-civic-accent font-medium'
                            : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-civic-blue' }}">
                        <span class="material-symbols-outlined notranslate text-[18px]" translate="no">description</span>
                        Introduction
                    </a>

                    <a href="{{ route('organisation.chart') }}"
                        class="flex items-center gap-3 px-5 py-3 text-sm border-t border-slate-100 transition
                        {{ request()->routeIs('organisation.chart')
                            ? 'bg-blue-50 text-civic-blue font-medium'
                            : 'text-slate-700 hover:bg-slate-50 hover:text-civic-blue' }}">
                        <span class="material-symbols-outlined notranslate text-[18px]" translate="no">account_tree</span>
                        Organisation Chart
                    </a>

                    <a href="{{ route('whos.who') }}"
                        class="flex items-center gap-3 px-5 py-3 text-sm border-t border-slate-100 transition
                        {{ request()->routeIs('whos.who')
                            ? 'bg-blue-50 text-civic-blue font-medium'
                            : 'text-slate-700 hover:bg-slate-50 hover:text-civic-blue' }}">
                        <span class="material-symbols-outlined notranslate text-[18px]" translate="no">groups</span>
                        Who's Who
                    </a>
                </div>
            </div>

            {{-- Our Vision --}}
            <a href="{{ route('vision') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors">
                <span class="material-symbols-outlined notranslate text-[18px]" translate="no">visibility</span>
                Our Vision
            </a>

            {{-- Gallery --}}
            <a href="{{ route('gallery') }}"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors">
                <span class="material-symbols-outlined notranslate text-[18px]" translate="no">photo_library</span>
                Gallery
            </a>

            {{-- Help --}}
            <a href="{{ route('help') }}"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-md shadow-sm transition-colors uppercase tracking-wide border border-slate-200
                {{ request()->routeIs('help')
                    ? 'bg-white text-civic-blue ring-2 ring-civic-blue/20'
                    : 'bg-white text-civic-blue hover:bg-slate-50' }}">
                <span class="material-symbols-outlined notranslate text-[18px]" translate="no">help</span>
                Help
            </a>

            {{-- PMAY Portals (Central Schemes) Dropdown --}}
            <div class="relative group">
                <button
                    class="flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-md transition-colors text-sky-800 bg-sky-50 hover:bg-sky-100 border border-sky-200 shadow-xs">
                    <span class="material-symbols-outlined notranslate text-[18px] text-sky-600" translate="no">apartment</span>
                    <span class="notranslate" translate="no">PMAY Portals</span>
                    <span class="material-symbols-outlined notranslate text-[18px] transition-transform group-hover:rotate-180 text-sky-600" translate="no">
                        expand_more
                    </span>
                </button>

                <div class="absolute left-0 sm:right-0 sm:left-auto mt-1 w-72 bg-white border border-slate-200 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden text-left">
                    <div class="px-4 py-2.5 bg-gradient-to-r from-sky-600 to-blue-700 text-white text-[11px] font-bold uppercase tracking-wider flex items-center justify-between">
                        <span>PMAY Central Portals</span>
                        <span class="text-[9px] bg-white/20 px-1.5 py-0.5 rounded font-semibold">MoHUA / MoRD</span>
                    </div>

                    <a href="https://pmaymis.gov.in/pmaymis2_2024/PMAY_SURVEY/Applicant_Login.aspx"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-between px-4 py-3 hover:bg-sky-50 text-slate-800 transition-colors border-b border-slate-100 group/u">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px] notranslate" translate="no">apartment</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800 group-hover/u:text-sky-700 notranslate" translate="no">PMAY - Urban (PMAY-U 2.0)</div>
                                <div class="text-[10.5px] text-slate-500">Applicant Login &amp; Survey</div>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-[15px] text-slate-400 group-hover/u:text-sky-600 notranslate" translate="no">open_in_new</span>
                    </a>

                    <a href="https://pmayg.dord.gov.in/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-between px-4 py-3 hover:bg-emerald-50 text-slate-800 transition-colors group/g">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px] notranslate" translate="no">cottage</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800 group-hover/g:text-emerald-700 notranslate" translate="no">PMAY - Gramin (PMAY-G)</div>
                                <div class="text-[10.5px] text-slate-500">Official Portal (DoRD)</div>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-[15px] text-slate-400 group-hover/g:text-emerald-600 notranslate" translate="no">open_in_new</span>
                    </a>
                </div>
            </div>

            {{-- Logout --}}
            @if(Auth::check())
                <a href="{{ route('citizen.logout', ['redirect_to' => route('home')]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-md shadow-sm transition-colors uppercase tracking-wide border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700">
                    <span class="material-symbols-outlined notranslate text-[18px]" translate="no">logout</span>
                    Logout
                </a>
            @endif

        </div>
    </div>
</nav>