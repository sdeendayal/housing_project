<!-- Left Sidebar Partial with Glassmorphic theme & Profile Card -->
<aside class="fixed left-3 top-3 bottom-3 w-[240px] flex flex-col py-4 z-40 bg-white/20 backdrop-blur-xl border border-white/30 rounded-[24px] shadow-xl shadow-slate-200/40 text-slate-700 select-none transition-all duration-300 ease-in-out">
    
    <!-- Glassmorphic Animated Background Blobs -->
    <div class="absolute inset-0 rounded-[24px] overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-12 -left-12 w-32 h-32 rounded-full bg-blue-400/20 blur-2xl animate-pulse" style="animation-duration: 6s;"></div>
        <div class="absolute top-1/3 -right-12 w-32 h-32 rounded-full bg-pink-400/15 blur-2xl animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute -bottom-12 -left-12 w-32 h-32 rounded-full bg-indigo-400/20 blur-2xl animate-pulse" style="animation-duration: 7s;"></div>
    </div>

    <!-- Floating Collapse / Expand Trigger Button -->
    <button type="button" onclick="toggleSidebarCollapse()" class="absolute -right-3 top-6 w-6 h-6 rounded-full bg-white border border-slate-200/60 flex items-center justify-center shadow-md cursor-pointer hover:bg-slate-50 hover:scale-105 active:scale-95 transition-all z-50">
        <span class="material-symbols-outlined text-[14px] font-bold text-slate-600" id="collapse-toggle-icon">chevron_left</span>
    </button>

    <!-- Header Section -->
    <div class="px-4 mb-4 relative z-10 shrink-0">
        <div class="flex items-center gap-3 px-1 py-0.5">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 via-purple-500 to-blue-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/25 shrink-0 relative overflow-hidden">
                <span class="material-symbols-outlined text-base font-bold relative z-10">home_pin</span>
                <div class="absolute inset-0 bg-white/10 rounded-full"></div>
            </div>
            <div class="flex flex-col min-w-0 sidebar-text">
                <h1 class="text-[11px] font-black text-slate-800 tracking-wider uppercase leading-none truncate">
                    Housing <span class="text-indigo-600 font-black">For All</span>
                </h1>
                <span class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-0.5 truncate">Haryana</span>
            </div>
        </div>
    </div>

    <!-- Search Input -->
    <div class="px-3.5 mb-4 relative z-10 shrink-0" id="sidebar-search-container">
        <div class="relative flex items-center" id="sidebar-search-icon-wrapper" onclick="handleSearchIconClick()">
            <span class="material-symbols-outlined absolute left-3 text-slate-400 text-[18px] pointer-events-none" id="search-icon-el">search</span>
            <input type="text" id="sidebar-search" placeholder="Search" class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-white/60 hover:bg-white/80 focus:bg-white border border-white/30 focus:border-indigo-350 focus:ring-1 focus:ring-indigo-300/20 text-slate-700 placeholder-slate-400 shadow-sm focus:outline-none transition-all" onkeyup="filterSidebar()">
        </div>
    </div>

    @php
        $currentType = $type ?? request()->route('type') ?? request()->query('type') ?? ($beneficiary->type ?? '');
        $isFunnelActive = request()->routeIs('ews.department.list') || request()->routeIs('ews.department.beneficiary.*');
        $isDevActive = request()->routeIs('ews.department.developers.*') || request()->routeIs('ews.department.developer-flats.*') || request()->routeIs('ews.department.developer-logs.*');
        $isSeederActive = request()->routeIs('ews.department.seeder.*');
    @endphp

    <!-- Structured Sidebar Navigation Links -->
    <nav class="flex-grow px-3 space-y-1 overflow-y-auto text-xs relative z-10 custom-sidebar-scroll">
        
        <!-- Dashboard Link -->
        <a href="{{ route('ews.department.dashboard') }}" class="w-full flex items-center gap-3 rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ request()->routeIs('ews.department.dashboard') ? 'bg-white/65 text-indigo-700 shadow-sm border border-white/50 backdrop-blur-md font-extrabold' : 'hover:bg-white/40 hover:text-slate-950 text-slate-600 border border-transparent' }}">
            <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('ews.department.dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-500' }}">dashboard</span>
            <span class="sidebar-text">Overview Dashboard</span>
        </a>

        <!-- 1. EWS Registry Funnel Dropdown Toggle -->
        <button type="button" onclick="toggleFunnelSubmenu()" class="w-full flex items-center justify-between rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ $isFunnelActive ? 'bg-white/40 text-slate-900 border border-white/20' : 'hover:bg-white/40 hover:text-slate-955 text-slate-600 border border-transparent' }}">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[18px] {{ $isFunnelActive ? 'text-indigo-600 font-bold' : 'text-slate-500' }}">filter_alt</span>
                <span class="sidebar-text">EWS Registry Funnel</span>
            </div>
            <span id="submenu-arrow" class="material-symbols-outlined text-sm text-slate-400">{{ $isFunnelActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
        </button>

        <!-- Funnel Submenus Wrapper -->
        <div id="funnel-submenus" class="{{ $isFunnelActive ? '' : 'hidden' }} space-y-3 pl-2 border-l border-white/20 ml-4 transition-all duration-300">
            <!-- Group 1: Registration Phase -->
            <div class="space-y-0.5 pt-1.5">
                <div class="group-header px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">1. Survey App Phase</div>
                <a href="{{ route('ews.department.list', ['type' => 'ppt_members', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'ppt_members') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'ppt_members') ? 'text-indigo-600 font-bold' : 'text-slate-455' }}">groups</span>
                        <span class="sidebar-text">Total registration</span>
                    </div>
                    @if(isset($totalRegistrationCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($totalRegistrationCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'registered') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'registered') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">list_alt</span>
                        <span class="sidebar-text">Verify in survey app</span>
                    </div>
                    @if(isset($registeredCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($registeredCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'not_in_survey', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'not_in_survey') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'not_in_survey') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">link_off</span>
                        <span class="sidebar-text">Rejected in survey app</span>
                    </div>
                    @if(isset($notInSurveyCount))
                        <span class="text-[9px] font-bold bg-rose-500 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-rose-500/10 sidebar-badge">{{ number_format($notInSurveyCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 2: Eligibility Rejections -->
            <div class="space-y-0.5">
                <div class="group-header px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">2. Eligibility Rejections</div>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'rejected_ppp') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'rejected_ppp') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">cancel</span>
                        <span class="sidebar-text">PPP Exclusion</span>
                    </div>
                    @if(isset($rejectedPppCount))
                        <span class="text-[9px] font-bold bg-rose-500 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-rose-500/10 sidebar-badge">{{ number_format($rejectedPppCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'rejected_property') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'rejected_property') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">domain_disabled</span>
                        <span class="sidebar-text">Property in India</span>
                    </div>
                    @if(isset($rejectedPropertyCount))
                        <span class="text-[9px] font-bold bg-rose-500 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-rose-500/10 sidebar-badge">{{ number_format($rejectedPropertyCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'rejected_ownership') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'rejected_ownership') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">home_work</span>
                        <span class="sidebar-text">House Ownership</span>
                    </div>
                    @if(isset($rejectedOwnershipCount))
                        <span class="text-[9px] font-bold bg-rose-500 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-rose-500/10 sidebar-badge">{{ number_format($rejectedOwnershipCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 3: Booking Amount Phase -->
            <div class="space-y-0.5">
                <div class="group-header px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">3. Booking Amount Phase</div>
                <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'eligible_draw') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'eligible_draw') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">how_to_reg</span>
                        <span class="sidebar-text">Eligible for booking</span>
                    </div>
                    @if(isset($eligibleDrawCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($eligibleDrawCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'booking') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'booking') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">verified</span>
                        <span class="sidebar-text">Booking amount received</span>
                    </div>
                    @if(isset($bookingCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($bookingCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'not_visited') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'not_visited') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">warning</span>
                        <span class="sidebar-text">Booking amount not received</span>
                    </div>
                    @if(isset($notVisitedCount))
                        <span class="text-[9px] font-bold bg-rose-50 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-rose-500/10 sidebar-badge">{{ number_format($notVisitedCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 4: ADC Verification Outcomes -->
            <div class="space-y-0.5">
                <div class="group-header px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">4. ADC Eligibility Outcomes</div>
                <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'adc_passed') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'adc_passed') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">check_circle_outline</span>
                        <span class="sidebar-text">Eligible</span>
                    </div>
                    @if(isset($adcPassedCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($adcPassedCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'adc_failed') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'adc_failed') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">error_outline</span>
                        <span class="sidebar-text">Not Eligible</span>
                    </div>
                    @if(isset($adcFailedCount))
                        <span class="text-[9px] font-bold bg-rose-50 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-rose-500/10 sidebar-badge">{{ number_format($adcFailedCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 5: Final Draw Allotment -->
            <div class="space-y-0.5">
                <div class="group-header px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">5. Final Draw Allotment</div>
                <a href="{{ route('ews.department.list', ['type' => 'all', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'all') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'all') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">groups</span>
                        <span class="sidebar-text">Total Beneficiaries</span>
                    </div>
                    @if(isset($totalCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($totalCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'allotted') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'allotted') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">check_circle</span>
                        <span class="sidebar-text">Allotted</span>
                    </div>
                    @if(isset($allottedCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($allottedCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'pending') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'pending') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">hourglass_empty</span>
                        <span class="sidebar-text">Waiting</span>
                    </div>
                    @if(isset($pendingCount))
                        <span class="text-[9px] font-bold bg-amber-550 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-amber-500/10 sidebar-badge">{{ number_format($pendingCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($currentType === 'draw_remaining') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'draw_remaining') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">hourglass_disabled</span>
                        <span class="sidebar-text">Unallotted Draw</span>
                    </div>
                    @if(isset($drawRemainingCount))
                        <span class="text-[9px] font-bold bg-slate-500 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-slate-500/10 sidebar-badge">{{ number_format($drawRemainingCount) }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 2. Developers Management Section Header & Submenu -->
        <div class="pt-1">
            <button type="button" onclick="toggleDevelopersSubmenu()" class="w-full flex items-center justify-between rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ $isDevActive ? 'bg-white/40 text-slate-900 border border-white/20 font-black' : 'hover:bg-white/40 hover:text-slate-955 text-slate-600 border border-transparent' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[18px] {{ $isDevActive ? 'text-indigo-600 font-bold' : 'text-slate-500' }}">engineering</span>
                    <span class="sidebar-text">Developers Hub</span>
                </div>
                <span id="dev-submenu-arrow" class="material-symbols-outlined text-sm text-slate-400">{{ $isDevActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
            </button>

            <!-- Developers Submenus -->
            <div id="developers-submenus" class="{{ $isDevActive ? '' : 'hidden' }} space-y-0.5 pl-2 border-l border-white/20 ml-4 transition-all duration-300 mt-1">
                <!-- Developer Accounts (CRUD) -->
                <a href="{{ route('ews.department.developers.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ request()->routeIs('ews.department.developers.*') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ request()->routeIs('ews.department.developers.*') ? 'text-indigo-600 font-bold' : 'text-slate-455' }}">manage_accounts</span>
                        <span class="sidebar-text">Developer Accounts</span>
                    </div>
                    @if(isset($developerCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ $developerCount }}</span>
                    @endif
                </a>

                <!-- Developer Flat Submissions / Form Data -->
                <a href="{{ route('ews.department.developer-flats.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ request()->routeIs('ews.department.developer-flats.*') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ request()->routeIs('ews.department.developer-flats.*') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">apartment</span>
                        <span class="sidebar-text">Flat Submissions</span>
                    </div>
                    @if(isset($developerFlatsCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($developerFlatsCount) }}</span>
                    @endif
                </a>

                <!-- Developer Activity Logs -->
                <a href="{{ route('ews.department.developer-logs.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ request()->routeIs('ews.department.developer-logs.*') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ request()->routeIs('ews.department.developer-logs.*') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">receipt_long</span>
                        <span class="sidebar-text">Activity Logs</span>
                    </div>
                    @if(isset($developerLogsCount))
                        <span class="text-[9px] font-bold bg-indigo-600 text-white px-1.5 py-0.5 rounded-full shadow-sm shadow-indigo-600/10 sidebar-badge">{{ number_format($developerLogsCount) }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 3. EWS Raw Database Files Dropdown Toggle -->
        <div class="pt-1">
            <button type="button" onclick="toggleSeederSubmenu()" class="w-full flex items-center justify-between rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ $isSeederActive ? 'bg-white/40 text-slate-900 border border-white/20 font-black' : 'hover:bg-white/40 hover:text-slate-955 text-slate-600 border border-transparent' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[18px] {{ $isSeederActive ? 'text-indigo-600 font-bold' : 'text-slate-500' }}">database</span>
                    <span class="sidebar-text">Raw Database Files</span>
                </div>
                <span id="seeder-submenu-arrow" class="material-symbols-outlined text-sm text-slate-400">{{ $isSeederActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
            </button>

            <!-- Seeder Submenus Wrapper -->
            <div id="seeder-submenus" class="{{ $isSeederActive ? '' : 'hidden' }} space-y-0.5 pl-2 border-l border-white/20 ml-4 transition-all duration-300 mt-1">
                <!-- Sonipat Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'SONIPAT']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'SONIPAT') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'SONIPAT') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">location_on</span>
                        <span class="sidebar-text">Sonipat</span>
                    </div>
                </a>
                <!-- Gurugram Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'GURUGRAM']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'GURUGRAM') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'GURUGRAM') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">location_on</span>
                        <span class="sidebar-text">Gurugram</span>
                    </div>
                </a>
                <!-- Faridabad Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'FARIDABAD']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'FARIDABAD') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'FARIDABAD') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">location_on</span>
                        <span class="sidebar-text">Faridabad</span>
                    </div>
                </a>
                <!-- Panipat Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'PANIPAT']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'PANIPAT') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'PANIPAT') ? 'text-indigo-600 font-bold' : 'text-slate-455' }}">location_on</span>
                        <span class="sidebar-text">Panipat</span>
                    </div>
                </a>
                <!-- Rewari Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'REWARI']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'REWARI') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'REWARI') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">location_on</span>
                        <span class="sidebar-text">Rewari</span>
                    </div>
                </a>
                <!-- Rohtak Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'ROHTAK']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'ROHTAK') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'ROHTAK') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">location_on</span>
                        <span class="sidebar-text">Rohtak</span>
                    </div>
                </a>
                <!-- Other Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'OTHER']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/40 hover:text-slate-955 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'OTHER') ? 'bg-white/50 text-indigo-700 font-extrabold shadow-sm border border-white/30' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'OTHER') ? 'text-indigo-600 font-bold' : 'text-slate-450' }}">folder_open</span>
                        <span class="sidebar-text">Other</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- 4. Standalone Primary Item: My Profile -->
        <a href="{{ route('ews.department.profile.show', !empty(Auth::user()->secure_id) ? Auth::user()->secure_id : \App\Helpers\EwsHelper::encodeSecureId(Auth::user()->id)) }}" class="w-full flex items-center gap-3 rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ request()->routeIs('ews.department.profile.*') ? 'bg-white/60 text-indigo-700 shadow-sm border border-white/50 backdrop-blur-md font-black' : 'hover:bg-white/40 hover:text-slate-955 text-slate-600 border border-transparent' }}">
            <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('ews.department.profile.*') ? 'text-indigo-600 font-bold' : 'text-slate-500' }}">account_circle</span>
            <span class="sidebar-text">My Profile</span>
        </a>

        <!-- Divider line -->
        <div class="border-t border-white/10 my-2"></div>

        <!-- Logout Link -->
        <a href="{{ route('ews.department.logout') }}" class="w-full flex items-center gap-3 rounded-xl px-4 py-2.5 transition-all text-left font-bold text-rose-600 hover:bg-rose-500/10 hover:text-rose-700 border border-transparent">
            <span class="material-symbols-outlined text-[18px]">logout</span>
            <span class="sidebar-text">Logout</span>
        </a>

    </nav>

    <!-- Sidebar Footer with Profile Card -->
    <div class="mt-auto pt-3 border-t border-white/20 relative z-10 px-3 shrink-0 profile-card-container">
        <!-- Active Scheme -->
        <div class="mb-3 px-2 flex items-center justify-between scheme-details">
            <div>
                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Active Scheme</p>
                <p class="text-[10px] font-black text-indigo-600 uppercase mt-0.5">EWS HOUSING</p>
            </div>
            <!-- Online status indicator -->
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 pulse-dot shadow-sm shadow-emerald-500/50"></span>
        </div>

        <!-- User profile display card -->
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/45 hover:bg-white/55 border border-white/30 backdrop-blur-md shadow-sm relative group transition-all profile-card-inner">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-orange-400 to-amber-300 flex items-center justify-center text-slate-800 shadow-inner shrink-0 overflow-hidden border border-white/30">
                @php
                    $name = Auth::user()->name ?? 'Emil Khan';
                    $initials = '';
                    $words = explode(' ', $name);
                    foreach ($words as $w) {
                        $initials .= strtoupper(substr($w, 0, 1));
                    }
                    $initials = substr($initials, 0, 2);
                @endphp
                <span class="text-xs font-bold text-slate-850 tracking-tighter">{{ $initials }}</span>
            </div>
            <div class="flex-grow min-w-0 profile-details">
                <p class="text-xs font-bold text-slate-800 truncate leading-none">{{ Auth::user()->name ?? 'Emil Khan' }}</p>
                <p class="text-[9px] text-slate-500 truncate mt-1 leading-none">{{ Auth::user()->email ?? 'emil.k@mds.com' }}</p>
            </div>
            <button type="button" onclick="toggleProfileMenu(event)" class="text-slate-500 hover:text-slate-800 transition-colors shrink-0 flex items-center justify-center profile-more-btn">
                <span class="material-symbols-outlined text-base">more_vert</span>
            </button>

            <!-- Dropdown Menu for Profile -->
            <div id="profile-dropdown" class="hidden absolute bottom-full right-0 mb-2 w-44 rounded-xl bg-white/95 backdrop-blur-lg border border-white/40 shadow-xl p-1 z-50 animate-fade-in-up">
                <a href="{{ route('ews.department.profile.show', !empty(Auth::user()->secure_id) ? Auth::user()->secure_id : \App\Helpers\EwsHelper::encodeSecureId(Auth::user()->id)) }}" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 font-bold transition-all text-left text-xs">
                    <span class="material-symbols-outlined text-sm text-indigo-500">account_circle</span>
                    <span>My Profile</span>
                </a>
                <div class="my-1 border-t border-slate-100"></div>
                <a href="{{ route('ews.department.logout') }}" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-rose-600 hover:bg-rose-50 hover:text-rose-700 font-bold transition-all text-left text-xs">
                    <span class="material-symbols-outlined text-sm text-rose-500">logout</span>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</aside>

<style>
    /* Collapsed Sidebar CSS overrides */
    aside.collapsed {
        width: 72px !important;
    }

    aside.collapsed .sidebar-text {
        display: none !important;
    }
    
    aside.collapsed nav a, 
    aside.collapsed nav button {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        gap: 0 !important;
    }
    
    aside.collapsed nav a span.material-symbols-outlined, 
    aside.collapsed nav button span.material-symbols-outlined {
        margin-right: 0 !important;
    }
    
    /* Hide submenus when collapsed */
    aside.collapsed #funnel-submenus,
    aside.collapsed #developers-submenus,
    aside.collapsed #seeder-submenus {
        display: none !important;
    }
    
    /* Hide submenu arrows when collapsed */
    aside.collapsed #submenu-arrow,
    aside.collapsed #dev-submenu-arrow,
    aside.collapsed #seeder-submenu-arrow {
        display: none !important;
    }
    
    /* Search box styling for collapsed view */
    aside.collapsed #sidebar-search {
        display: none !important;
    }
    
    aside.collapsed #sidebar-search-container {
        padding-left: 0 !important;
        padding-right: 0 !important;
        display: flex !important;
        justify-content: center !important;
    }
    
    aside.collapsed #sidebar-search-icon-wrapper {
        position: static !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 32px !important;
        height: 32px !important;
        background-color: rgba(255, 255, 255, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 0.75rem !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
    }
    
    aside.collapsed #sidebar-search-icon-wrapper:hover {
        background-color: rgba(255, 255, 255, 0.8) !important;
    }

    aside.collapsed #search-icon-el {
        position: static !important;
        pointer-events: auto !important;
    }
    
    /* Active scheme text hidden */
    aside.collapsed .scheme-details {
        display: none !important;
    }
    
    /* Profile card overrides */
    aside.collapsed .profile-card-container {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    aside.collapsed .profile-card-inner {
        justify-content: center !important;
        padding: 0.5rem 0 !important;
    }
    
    aside.collapsed .profile-details,
    aside.collapsed .profile-more-btn {
        display: none !important;
    }

    aside.collapsed .sidebar-badge {
        display: none !important;
    }

    /* Premium scrollbar logic for custom-sidebar-scroll */
    .custom-sidebar-scroll::-webkit-scrollbar {
        width: 3px;
    }
    .custom-sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-sidebar-scroll::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.08);
        border-radius: 9px;
    }
    .custom-sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.15);
    }
    /* Pulse indicator styling */
    .pulse-dot {
        position: relative;
    }
    .pulse-dot::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: inherit;
        border-radius: inherit;
        animation: pulse 1.6s infinite;
        opacity: 0.6;
    }
    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.6;
        }
        100% {
            transform: scale(2.4);
            opacity: 0;
        }
    }
</style>

<script>
    // Self-invoking function to prevent Layout Shift (CLS) on load
    (function() {
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            const aside = document.querySelector('aside');
            if (aside) {
                aside.classList.add('collapsed');
                aside.classList.remove('w-[240px]');
                aside.classList.add('w-[72px]');
            }
            document.addEventListener('DOMContentLoaded', function() {
                const toggleIcon = document.getElementById('collapse-toggle-icon');
                if (toggleIcon) toggleIcon.textContent = 'chevron_right';
                const mainContent = document.querySelector('.ml-\\[260px\\]');
                if (mainContent) {
                    mainContent.classList.remove('ml-[260px]');
                    mainContent.classList.add('ml-[92px]', 'transition-all', 'duration-300', 'ease-in-out');
                }
            });
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                const mainContent = document.querySelector('.ml-\\[260px\\]');
                if (mainContent) {
                    mainContent.classList.add('transition-all', 'duration-300', 'ease-in-out');
                }
            });
        }
    })();

    function toggleSidebarCollapse() {
        const aside = document.querySelector('aside');
        const toggleIcon = document.getElementById('collapse-toggle-icon');
        const mainContent = document.querySelector('.ml-\\[260px\\], .ml-\\[92px\\]');
        
        if (!aside) return;
        
        const isCurrentlyCollapsed = aside.classList.contains('collapsed');
        
        if (isCurrentlyCollapsed) {
            // Expand
            aside.classList.remove('collapsed');
            aside.classList.remove('w-[72px]');
            aside.classList.add('w-[240px]');
            if (toggleIcon) toggleIcon.textContent = 'chevron_left';
            
            if (mainContent) {
                mainContent.classList.remove('ml-[92px]');
                mainContent.classList.add('ml-[260px]');
            }
            
            localStorage.setItem('sidebar-collapsed', 'false');
        } else {
            // Collapse
            aside.classList.add('collapsed');
            aside.classList.remove('w-[240px]');
            aside.classList.add('w-[72px]');
            if (toggleIcon) toggleIcon.textContent = 'chevron_right';
            
            if (mainContent) {
                mainContent.classList.remove('ml-[260px]');
                mainContent.classList.add('ml-[92px]');
            }
            
            localStorage.setItem('sidebar-collapsed', 'true');
        }
    }

    function handleSearchIconClick() {
        const aside = document.querySelector('aside');
        if (aside && aside.classList.contains('collapsed')) {
            toggleSidebarCollapse();
            setTimeout(() => {
                const searchEl = document.getElementById('sidebar-search');
                if (searchEl) searchEl.focus();
            }, 320);
        }
    }

    function toggleFunnelSubmenu() {
        const aside = document.querySelector('aside');
        // If collapsed, expand first instead of opening submenu inside a tiny view
        if (aside && aside.classList.contains('collapsed')) {
            toggleSidebarCollapse();
            setTimeout(toggleFunnelSubmenu, 320);
            return;
        }

        const container = document.getElementById('funnel-submenus');
        const arrow = document.getElementById('submenu-arrow');
        if (!container || !arrow) return;
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            arrow.textContent = 'keyboard_arrow_down';
        } else {
            container.classList.add('hidden');
            arrow.textContent = 'keyboard_arrow_right';
        }
    }

    function toggleDevelopersSubmenu() {
        const aside = document.querySelector('aside');
        if (aside && aside.classList.contains('collapsed')) {
            toggleSidebarCollapse();
            setTimeout(toggleDevelopersSubmenu, 320);
            return;
        }

        const container = document.getElementById('developers-submenus');
        const arrow = document.getElementById('dev-submenu-arrow');
        if (!container || !arrow) return;
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            arrow.textContent = 'keyboard_arrow_down';
        } else {
            container.classList.add('hidden');
            arrow.textContent = 'keyboard_arrow_right';
        }
    }

    function toggleSeederSubmenu() {
        const aside = document.querySelector('aside');
        if (aside && aside.classList.contains('collapsed')) {
            toggleSidebarCollapse();
            setTimeout(toggleSeederSubmenu, 320);
            return;
        }

        const container = document.getElementById('seeder-submenus');
        const arrow = document.getElementById('seeder-submenu-arrow');
        if (!container || !arrow) return;
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            arrow.textContent = 'keyboard_arrow_down';
        } else {
            container.classList.add('hidden');
            arrow.textContent = 'keyboard_arrow_right';
        }
    }

    function toggleProfileMenu(event) {
        if (event) event.stopPropagation();
        const dropdown = document.getElementById('profile-dropdown');
        if (!dropdown) return;
        dropdown.classList.toggle('hidden');
    }

    // Close profile dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('profile-dropdown');
        if (!dropdown) return;
        const trigger = e.target.closest('button[onclick="toggleProfileMenu(event)"]');
        const menu = e.target.closest('#profile-dropdown');
        if (!trigger && !menu && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    });

    function filterSidebar() {
        const query = document.getElementById('sidebar-search').value.toLowerCase();
        const links = document.querySelectorAll('aside nav a');
        const buttons = document.querySelectorAll('aside nav button');
        const groupHeaders = document.querySelectorAll('aside nav .group-header');
        
        const funnel = document.getElementById('funnel-submenus');
        const devs = document.getElementById('developers-submenus');
        const seeders = document.getElementById('seeder-submenus');

        if (!query) {
            // Restore default view based on PHP variables
            const isFunnelActive = {{ $isFunnelActive ? 'true' : 'false' }};
            const isDevActive = {{ $isDevActive ? 'true' : 'false' }};
            const isSeederActive = {{ $isSeederActive ? 'true' : 'false' }};
            
            if (funnel) funnel.classList.toggle('hidden', !isFunnelActive);
            if (devs) devs.classList.toggle('hidden', !isDevActive);
            if (seeders) seeders.classList.toggle('hidden', !isSeederActive);
            
            if (document.getElementById('submenu-arrow')) document.getElementById('submenu-arrow').textContent = isFunnelActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right';
            if (document.getElementById('dev-submenu-arrow')) document.getElementById('dev-submenu-arrow').textContent = isDevActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right';
            if (document.getElementById('seeder-submenu-arrow')) document.getElementById('seeder-submenu-arrow').textContent = isSeederActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right';
            
            links.forEach(link => {
                link.style.display = '';
            });
            buttons.forEach(btn => {
                btn.style.display = '';
            });
            groupHeaders.forEach(gh => {
                gh.style.display = '';
            });
            return;
        }

        // Expand all submenus to search inside them
        if (funnel) funnel.classList.remove('hidden');
        if (devs) devs.classList.remove('hidden');
        if (seeders) seeders.classList.remove('hidden');

        links.forEach(link => {
            const text = link.textContent.toLowerCase();
            if (text.includes(query)) {
                link.style.display = '';
            } else {
                link.style.display = 'none';
            }
        });

        buttons.forEach(btn => {
            const text = btn.textContent.toLowerCase();
            let hasVisibleChild = false;
            
            if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes('Funnel')) {
                hasVisibleChild = Array.from(funnel.querySelectorAll('a')).some(a => a.style.display !== 'none');
            } else if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes('Developers')) {
                hasVisibleChild = Array.from(devs.querySelectorAll('a')).some(a => a.style.display !== 'none');
            } else if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes('Seeder')) {
                hasVisibleChild = Array.from(seeders.querySelectorAll('a')).some(a => a.style.display !== 'none');
            }

            if (text.includes(query) || hasVisibleChild) {
                btn.style.display = '';
            } else {
                btn.style.display = 'none';
            }
        });

        groupHeaders.forEach(gh => {
            gh.style.display = 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const activeSubmenuItem = document.querySelector('aside nav a[class*="bg-white/50"]');
        if (activeSubmenuItem) {
            activeSubmenuItem.scrollIntoView({ block: 'center', behavior: 'instant' });
        }
    });
</script>
