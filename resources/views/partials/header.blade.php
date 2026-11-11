<header class="text-on-primary docked full-width top-0 sticky border-b border-outline-variant shadow-md z-50 bg-no-repeat bg-right bg-cover" style="background-image: linear-gradient(90deg, rgba(6,127,208,1) 0%, rgba(0,51,88,1) 100%), url('{{ asset('header-tp-bg.png') }}');">
    <div class="max-w-7xl mx-auto px-4 py-3 sm:py-3.5 flex flex-col md:flex-row items-center justify-between gap-3 md:gap-4">
        
        <!-- Left: Logo & Department Name -->
        <div class="flex items-center space-x-3 sm:space-x-4 w-full md:w-auto justify-between md:justify-start">
            <a href="{{ route('home') }}" class="shrink-0 flex items-center">
                <img alt="Department Logo" class="h-14 w-14 sm:h-16 sm:w-16 object-contain drop-shadow" src="{{ asset('Haryana_emblem.png') }}" />
            </a>
            <div>
                <h1 class="text-lg sm:text-2xl font-bold leading-tight notranslate text-white tracking-tight" translate="no">Department of Housing For All</h1>
                <p class="text-xs sm:text-sm text-sky-100 font-medium">Government of Haryana</p>
            </div>
        </div>

        <!-- Right: PMAY Central Scheme Portal Quick Links + Google Translate -->
        <div class="flex items-center gap-2.5 sm:gap-3 flex-wrap md:flex-nowrap justify-center md:justify-end w-full md:w-auto">
            
            <!-- PMAY - Urban (PMAY-U 2.0) -->
            <a href="https://pmaymis.gov.in/pmaymis2_2024/PMAY_SURVEY/Applicant_Login.aspx"
                target="_blank"
                rel="noopener noreferrer"
                title="PMAY - Urban 2.0: Citizen Survey & Applicant Login"
                class="group relative flex items-center gap-2.5 bg-white/10 hover:bg-white/20 active:scale-95 border border-white/25 hover:border-white/50 backdrop-blur-md rounded-xl px-3 py-1.5 sm:py-2 text-white shadow-sm hover:shadow-lg transition-all duration-200">
                
                <div class="w-8 h-8 rounded-lg bg-sky-400/25 border border-sky-300/40 flex items-center justify-center text-sky-200 group-hover:bg-sky-400 group-hover:text-slate-900 transition-all duration-200 shrink-0">
                    <span class="material-symbols-outlined text-[19px] notranslate" translate="no">apartment</span>
                </div>

                <div class="text-left leading-tight">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[12px] font-bold text-white tracking-wide group-hover:text-yellow-200 transition-colors notranslate" translate="no">
                            PMAY - Urban
                        </span>
                        <span class="text-[8.5px] font-black uppercase bg-sky-500/80 text-white px-1.5 py-0.2 rounded shadow-xs notranslate" translate="no">
                            2.0
                        </span>
                    </div>
                    <div class="text-[10px] text-sky-100/90 font-medium flex items-center gap-1 mt-0.5">
                        <span>Applicant Login / Survey</span>
                        <span class="material-symbols-outlined text-[11px] opacity-75 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all notranslate" translate="no">open_in_new</span>
                    </div>
                </div>
            </a>

            <!-- PMAY - Gramin (PMAY-G) -->
            <a href="https://pmayg.dord.gov.in/"
                target="_blank"
                rel="noopener noreferrer"
                title="PMAY - Gramin: Department of Rural Development Official Portal"
                class="group relative flex items-center gap-2.5 bg-white/10 hover:bg-white/20 active:scale-95 border border-white/25 hover:border-white/50 backdrop-blur-md rounded-xl px-3 py-1.5 sm:py-2 text-white shadow-sm hover:shadow-lg transition-all duration-200">
                
                <div class="w-8 h-8 rounded-lg bg-emerald-400/25 border border-emerald-300/40 flex items-center justify-center text-emerald-200 group-hover:bg-emerald-400 group-hover:text-slate-900 transition-all duration-200 shrink-0">
                    <span class="material-symbols-outlined text-[19px] notranslate" translate="no">cottage</span>
                </div>

                <div class="text-left leading-tight">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[12px] font-bold text-white tracking-wide group-hover:text-emerald-200 transition-colors notranslate" translate="no">
                            PMAY - Gramin
                        </span>
                        <span class="text-[8.5px] font-black uppercase bg-emerald-500/80 text-white px-1.5 py-0.2 rounded shadow-xs notranslate" translate="no">
                            PMAY-G
                        </span>
                    </div>
                    <div class="text-[10px] text-emerald-100/90 font-medium flex items-center gap-1 mt-0.5">
                        <span>Official Portal (DoRD)</span>
                        <span class="material-symbols-outlined text-[11px] opacity-75 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all notranslate" translate="no">open_in_new</span>
                    </div>
                </div>
            </a>

            <!-- Google Translate Element -->
            <div class="flex items-center shrink-0 pl-1">
                <div id="google_translate_element"></div>
            </div>
        </div>

    </div>
</header>
