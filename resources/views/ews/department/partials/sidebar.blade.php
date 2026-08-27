<!-- Left Sidebar Partial with Breeze Icy Blue Theme & Profile Card -->
<aside class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-5 z-40 bg-gradient-to-b from-[#e3eefd] via-[#f1f6fe] to-[#e1edf9] text-slate-700 shadow-xl border-r border-slate-200/50">
    
    <!-- Subtle white dot matrix background -->
    <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image: radial-gradient(rgba(255, 255, 255, 0.5) 1px, transparent 0); background-size: 16px 16px;"></div>

    <!-- Unified EWS Header -->
    <div class="mx-3.5 mb-5 relative z-10">
        <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/70 border border-white/80 shadow-sm">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
                <span class="material-symbols-outlined text-xs font-bold">home_pin</span>
            </div>
            <h1 class="text-[10px] font-black text-slate-800 tracking-widest uppercase truncate leading-none">
                Housing <span class="text-blue-650">for all</span>
            </h1>
        </div>
    </div>

    @php
        $currentType = $type ?? request()->route('type') ?? request()->query('type') ?? ($beneficiary->type ?? '');
        $isFunnelActive = request()->routeIs('ews.department.list') || request()->routeIs('ews.department.beneficiary.*');
        $isDevActive = request()->routeIs('ews.department.developers.*') || request()->routeIs('ews.department.developer-flats.*') || request()->routeIs('ews.department.developer-logs.*');
    @endphp

    <!-- Structured Sidebar Navigation Links -->
    <nav class="flex-grow px-3 space-y-1.5 overflow-y-auto text-xs relative z-10">
        
        <!-- Dashboard Link -->
        <a href="{{ route('ews.department.dashboard') }}" class="w-full flex items-center gap-3 rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ request()->routeIs('ews.department.dashboard') ? 'bg-white text-blue-700 shadow-md border-l-4 border-blue-600 font-black' : 'hover:bg-white/60 hover:text-slate-900 text-slate-600' }}">
            <span class="material-symbols-outlined text-base {{ request()->routeIs('ews.department.dashboard') ? 'text-blue-650' : 'text-slate-400' }}">dashboard</span>
            <span>Overview Dashboard</span>
        </a>

        <!-- 1. EWS Registry Funnel Dropdown Toggle -->
        <button type="button" onclick="toggleFunnelSubmenu()" class="w-full flex items-center justify-between rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ $isFunnelActive ? 'bg-white/40 text-slate-900 border-l-4 border-blue-400' : 'hover:bg-white/60 hover:text-slate-900 text-slate-600' }}">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-base {{ $isFunnelActive ? 'text-blue-600' : 'text-slate-400' }}">filter_alt</span>
                <span>EWS Registry Funnel</span>
            </div>
            <span id="submenu-arrow" class="material-symbols-outlined text-sm text-slate-450">{{ $isFunnelActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
        </button>

        <!-- Funnel Submenus Wrapper -->
        <div id="funnel-submenus" class="{{ $isFunnelActive ? '' : 'hidden' }} space-y-3 pl-2 border-l border-blue-200/50 ml-4 transition-all duration-300">
            <!-- Group 1: Registration Phase -->
            <div class="space-y-1 pt-1.5">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">1. Survey App Phase</div>
                <a href="{{ route('ews.department.list', ['type' => 'ppt_members', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'ppt_members') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'ppt_members') ? 'text-blue-600' : 'text-slate-400' }}">groups</span>
                        <span>Total registration</span>
                    </div>
                    @if(isset($totalRegistrationCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1 py-0.5 rounded font-bold">{{ number_format($totalRegistrationCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'registered') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'registered') ? 'text-blue-600' : 'text-slate-400' }}">list_alt</span>
                        <span>Verify in survey app</span>
                    </div>
                    @if(isset($registeredCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1 py-0.5 rounded font-bold">{{ number_format($registeredCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'not_in_survey', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'not_in_survey') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'not_in_survey') ? 'text-blue-600' : 'text-slate-400' }}">link_off</span>
                        <span>Rejected in survey app</span>
                    </div>
                    @if(isset($notInSurveyCount))
                        <span class="text-[9px] font-mono bg-rose-50 text-rose-700 border border-rose-100 px-1 py-0.5 rounded font-bold">{{ number_format($notInSurveyCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 2: Eligibility Rejections -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">2. Eligibility Rejections</div>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'rejected_ppp') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'rejected_ppp') ? 'text-blue-600' : 'text-slate-400' }}">cancel</span>
                        <span>PPP Exclusion</span>
                    </div>
                    @if(isset($rejectedPppCount))
                        <span class="text-[9px] font-mono bg-rose-50 text-rose-700 border border-rose-100 px-1 py-0.5 rounded font-bold">{{ number_format($rejectedPppCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'rejected_property') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'rejected_property') ? 'text-blue-600' : 'text-slate-400' }}">domain_disabled</span>
                        <span>Property in India</span>
                    </div>
                    @if(isset($rejectedPropertyCount))
                        <span class="text-[9px] font-mono bg-rose-50 text-rose-700 border border-rose-100 px-1 py-0.5 rounded font-bold">{{ number_format($rejectedPropertyCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'rejected_ownership') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'rejected_ownership') ? 'text-blue-600' : 'text-slate-400' }}">home_work</span>
                        <span>House Ownership</span>
                    </div>
                    @if(isset($rejectedOwnershipCount))
                        <span class="text-[9px] font-mono bg-rose-50 text-rose-700 border border-rose-100 px-1 py-0.5 rounded font-bold">{{ number_format($rejectedOwnershipCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 3: Verification Visited/Absent -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">3. Booking Amount Phase</div>
                <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'eligible_draw') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'eligible_draw') ? 'text-blue-600' : 'text-slate-400' }}">how_to_reg</span>
                        <span>Eligible for booking</span>
                    </div>
                    @if(isset($eligibleDrawCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1 py-0.5 rounded font-bold">{{ number_format($eligibleDrawCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'booking') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'booking') ? 'text-blue-600' : 'text-slate-400' }}">verified</span>
                        <span>Booking amount received</span>
                    </div>
                    @if(isset($bookingCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1 py-0.5 rounded font-bold">{{ number_format($bookingCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'not_visited') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'not_visited') ? 'text-blue-600' : 'text-slate-400' }}">warning</span>
                        <span>Booking amount not received</span>
                    </div>
                    @if(isset($notVisitedCount))
                        <span class="text-[9px] font-mono bg-rose-50 text-rose-700 border border-rose-100 px-1 py-0.5 rounded font-bold">{{ number_format($notVisitedCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 4: ADC Verification Outcomes -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">4. ADC Eligibility Outcomes</div>
                <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'adc_passed') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'adc_passed') ? 'text-blue-600' : 'text-slate-400' }}">check_circle_outline</span>
                        <span>Eligible</span>
                    </div>
                    @if(isset($adcPassedCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1 py-0.5 rounded font-bold">{{ number_format($adcPassedCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'adc_failed') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'adc_failed') ? 'text-blue-600' : 'text-slate-400' }}">error_outline</span>
                        <span>Not Eligible</span>
                    </div>
                    @if(isset($adcFailedCount))
                        <span class="text-[9px] font-mono bg-rose-50 text-rose-700 border border-rose-100 px-1 py-0.5 rounded font-bold">{{ number_format($adcFailedCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 5: Final Draw Allotment -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-400">5. Final Draw Allotment</div>
                <a href="{{ route('ews.department.list', ['type' => 'all', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'all') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'all') ? 'text-blue-600' : 'text-slate-400' }}">groups</span>
                        <span>Total Beneficiaries</span>
                    </div>
                    @if(isset($totalCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1 py-0.5 rounded font-bold">{{ number_format($totalCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'allotted') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'allotted') ? 'text-blue-600' : 'text-slate-400' }}">check_circle</span>
                        <span>Allotted</span>
                    </div>
                    @if(isset($allottedCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1 py-0.5 rounded font-bold">{{ number_format($allottedCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'pending') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'pending') ? 'text-blue-600' : 'text-slate-400' }}">hourglass_empty</span>
                        <span>Waiting</span>
                    </div>
                    @if(isset($pendingCount))
                        <span class="text-[9px] font-mono bg-amber-50 text-amber-700 border border-amber-100 px-1 py-0.5 rounded font-bold">{{ number_format($pendingCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($currentType === 'draw_remaining') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($currentType === 'draw_remaining') ? 'text-blue-600' : 'text-slate-400' }}">hourglass_disabled</span>
                        <span>Unallotted Draw</span>
                    </div>
                    @if(isset($drawRemainingCount))
                        <span class="text-[9px] font-mono bg-slate-100 text-slate-700 border border-slate-200 px-1 py-0.5 rounded font-bold">{{ number_format($drawRemainingCount) }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 2. Developers Management Section Header & Submenu -->
        <div class="pt-2">
            <button type="button" onclick="toggleDevelopersSubmenu()" class="w-full flex items-center justify-between rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ $isDevActive ? 'bg-white text-blue-700 shadow-md border-l-4 border-blue-600 font-black' : 'hover:bg-white/60 hover:text-slate-900 text-slate-600' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-base {{ $isDevActive ? 'text-blue-600' : 'text-slate-400' }}">engineering</span>
                    <span>Developers Hub</span>
                </div>
                <span id="dev-submenu-arrow" class="material-symbols-outlined text-sm text-slate-450">{{ $isDevActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
            </button>

            <!-- Developers Submenus -->
            <div id="developers-submenus" class="{{ $isDevActive ? '' : 'hidden' }} space-y-1 pl-2 border-l border-blue-200/50 ml-4 transition-all duration-300 mt-1">
                <!-- Developer Accounts (CRUD) -->
                <a href="{{ route('ews.department.developers.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ request()->routeIs('ews.department.developers.*') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ request()->routeIs('ews.department.developers.*') ? 'text-blue-600' : 'text-slate-400' }}">manage_accounts</span>
                        <span>Developer Accounts</span>
                    </div>
                    @if(isset($developerCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1.5 py-0.5 rounded font-bold font-mono">{{ $developerCount }}</span>
                    @endif
                </a>

                <!-- Developer Flat Submissions / Form Data -->
                <a href="{{ route('ews.department.developer-flats.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ request()->routeIs('ews.department.developer-flats.*') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ request()->routeIs('ews.department.developer-flats.*') ? 'text-blue-600' : 'text-slate-400' }}">apartment</span>
                        <span>Flat Submissions</span>
                    </div>
                    @if(isset($developerFlatsCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1.5 py-0.5 rounded font-bold font-mono">{{ number_format($developerFlatsCount) }}</span>
                    @endif
                </a>

                <!-- Developer Activity Logs -->
                <a href="{{ route('ews.department.developer-logs.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ request()->routeIs('ews.department.developer-logs.*') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ request()->routeIs('ews.department.developer-logs.*') ? 'text-blue-600' : 'text-slate-400' }}">receipt_long</span>
                        <span>Activity Logs</span>
                    </div>
                    @if(isset($developerLogsCount))
                        <span class="text-[9px] font-mono bg-blue-50 text-blue-700 border border-blue-100 px-1.5 py-0.5 rounded font-bold font-mono">{{ number_format($developerLogsCount) }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 3. EWS Raw Database Files Dropdown Toggle -->
        @php
            $isSeederActive = request()->routeIs('ews.department.seeder.*');
        @endphp
        <div class="pt-1">
            <button type="button" onclick="toggleSeederSubmenu()" class="w-full flex items-center justify-between rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ $isSeederActive ? 'bg-white text-blue-700 shadow-md border-l-4 border-blue-600 font-black' : 'hover:bg-white/60 hover:text-slate-900 text-slate-600' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-base {{ $isSeederActive ? 'text-blue-600' : 'text-slate-400' }}">database</span>
                    <span>Raw Database Files</span>
                </div>
                <span id="seeder-submenu-arrow" class="material-symbols-outlined text-sm text-slate-455">{{ $isSeederActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
            </button>

            <!-- Seeder Submenus Wrapper -->
            <div id="seeder-submenus" class="{{ $isSeederActive ? '' : 'hidden' }} space-y-1 pl-2 border-l border-blue-200/50 ml-4 transition-all duration-300 mt-1">
                <!-- Sonipat Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'SONIPAT']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'SONIPAT') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'SONIPAT') ? 'text-blue-600' : 'text-slate-400' }}">location_on</span>
                        <span>Sonipat</span>
                    </div>
                </a>
                <!-- Gurugram Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'GURUGRAM']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'GURUGRAM') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'GURUGRAM') ? 'text-blue-600' : 'text-slate-400' }}">location_on</span>
                        <span>Gurugram</span>
                    </div>
                </a>
                <!-- Faridabad Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'FARIDABAD']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'FARIDABAD') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'FARIDABAD') ? 'text-blue-600' : 'text-slate-400' }}">location_on</span>
                        <span>Faridabad</span>
                    </div>
                </a>
                <!-- Panipat Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'PANIPAT']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'PANIPAT') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'PANIPAT') ? 'text-blue-600' : 'text-slate-400' }}">location_on</span>
                        <span>Panipat</span>
                    </div>
                </a>
                <!-- Rewari Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'REWARI']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'REWARI') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'REWARI') ? 'text-blue-600' : 'text-slate-400' }}">location_on</span>
                        <span>Rewari</span>
                    </div>
                </a>
                <!-- Rohtak Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'ROHTAK']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'ROHTAK') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'ROHTAK') ? 'text-blue-600' : 'text-slate-400' }}">location_on</span>
                        <span>Rohtak</span>
                    </div>
                </a>
                <!-- Other Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'OTHER']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-white/50 hover:text-slate-900 transition-all text-left {{ ($isSeederActive && request()->query('district') === 'OTHER') ? 'bg-white border-l border-blue-500 text-blue-700 font-extrabold shadow-sm' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ ($isSeederActive && request()->query('district') === 'OTHER') ? 'text-blue-600' : 'text-slate-400' }}">folder_open</span>
                        <span>Other</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- 3. Standalone Primary Item: My Profile -->
        <a href="{{ route('ews.department.profile.show', !empty(Auth::user()->secure_id) ? Auth::user()->secure_id : \App\Helpers\EwsHelper::encodeSecureId(Auth::user()->id)) }}" class="w-full flex items-center gap-3 rounded-xl px-4 py-2.5 transition-all text-left font-bold {{ request()->routeIs('ews.department.profile.*') ? 'bg-white text-blue-700 shadow-md border-l-4 border-blue-600 font-black' : 'hover:bg-white/60 hover:text-slate-900 text-slate-600' }}">
            <span class="material-symbols-outlined text-base {{ request()->routeIs('ews.department.profile.*') ? 'text-blue-605' : 'text-slate-400' }}">account_circle</span>
            <span>My Profile</span>
        </a>

    </nav>

    <!-- Sidebar Footer -->
    <div class="mt-auto px-6 pt-4 border-t border-slate-300 relative z-10">
        <div class="mb-4 px-2">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Active Scheme</p>
            <p class="text-xs font-bold text-blue-650 uppercase mt-0.5">EWS HOUSING</p>
        </div>
        <a href="{{ route('ews.department.logout') }}" class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-all font-bold">
            <span class="material-symbols-outlined text-base">logout</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<script>
    function toggleFunnelSubmenu() {
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

    document.addEventListener('DOMContentLoaded', function() {
        const activeSubmenuItem = document.querySelector('aside nav a[class*="bg-white"]');
        if (activeSubmenuItem) {
            activeSubmenuItem.scrollIntoView({ block: 'center', behavior: 'instant' });
        }
    });
</script>
