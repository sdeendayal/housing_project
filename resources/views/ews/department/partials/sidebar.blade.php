<!-- Left Sidebar Partial -->
<aside class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-5 z-40 bg-[#1e293b] text-slate-300 shadow-xl border-r border-slate-800">
    <!-- Logo -->
    <div class="px-6 mb-5 flex items-center gap-3">
        <div class="w-9 h-9 bg-gradient-to-tr from-orange-600 to-amber-500 rounded-lg flex items-center justify-center text-white">
            <span class="material-symbols-outlined text-lg font-bold">business</span>
        </div>
        <div>
            <a href="{{ route('ews.department.dashboard') }}">
                <h1 class="text-sm font-extrabold text-white leading-tight">EWS DEPT</h1>
                <p class="text-[8px] uppercase tracking-wider text-orange-400 font-bold">Housing Haryana</p>
            </a>
        </div>
    </div>

    @php
        $currentType = $type ?? request()->route('type') ?? request()->query('type') ?? ($beneficiary->type ?? '');
        $isFunnelActive = request()->routeIs('ews.department.list') || request()->routeIs('ews.department.beneficiary.*');
        $isDevActive = request()->routeIs('ews.department.developers.*') || request()->routeIs('ews.department.developer-flats.*') || request()->routeIs('ews.department.developer-logs.*');
    @endphp

    <!-- Structured Sidebar Navigation Links -->
    <nav class="flex-grow px-3 space-y-2 overflow-y-auto text-xs">
        
        <!-- Dashboard Link -->
        <a href="{{ route('ews.department.dashboard') }}" class="w-full flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all text-left font-bold {{ request()->routeIs('ews.department.dashboard') ? 'bg-orange-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <span class="material-symbols-outlined text-base text-orange-400">dashboard</span>
            <span>Overview Dashboard</span>
        </a>

        <!-- 1. EWS Registry Funnel Dropdown Toggle -->
        <button type="button" onclick="toggleFunnelSubmenu()" class="w-full flex items-center justify-between rounded-lg px-4 py-2.5 transition-all text-left font-bold {{ $isFunnelActive ? 'bg-slate-800 text-white border-l-2 border-orange-500' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-base text-orange-400">filter_alt</span>
                <span>EWS Registry Funnel</span>
            </div>
            <span id="submenu-arrow" class="material-symbols-outlined text-sm">{{ $isFunnelActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
        </button>

        <!-- Funnel Submenus Wrapper -->
        <div id="funnel-submenus" class="{{ $isFunnelActive ? '' : 'hidden' }} space-y-3 pl-2 border-l border-slate-700/60 ml-4 transition-all duration-300">
            <!-- Group 1: Registration Phase -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">1. Survey App Phase</div>
                <a href="{{ route('ews.department.list', ['type' => 'ppt_members', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'ppt_members') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">groups</span>
                        <span>Total registration</span>
                    </div>
                    @if(isset($totalRegistrationCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($totalRegistrationCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'registered', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'registered') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">list_alt</span>
                        <span>Verify in survey app</span>
                    </div>
                    @if(isset($registeredCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($registeredCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'not_in_survey', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'not_in_survey') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">link_off</span>
                        <span>Rejected in survey app</span>
                    </div>
                    @if(isset($notInSurveyCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($notInSurveyCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 2: Eligibility Rejections -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">2. Eligibility Rejections</div>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_ppp', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'rejected_ppp') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">cancel</span>
                        <span>PPP Exclusion</span>
                    </div>
                    @if(isset($rejectedPppCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedPppCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_property', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'rejected_property') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">domain_disabled</span>
                        <span>Property in India</span>
                    </div>
                    @if(isset($rejectedPropertyCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedPropertyCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'rejected_ownership', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'rejected_ownership') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">home_work</span>
                        <span>House Ownership</span>
                    </div>
                    @if(isset($rejectedOwnershipCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($rejectedOwnershipCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 3: Verification Visited/Absent -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">3. Booking Amount Phase</div>
                <a href="{{ route('ews.department.list', ['type' => 'eligible_draw', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'eligible_draw') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">how_to_reg</span>
                        <span>Eligible for booking</span>
                    </div>
                    @if(isset($eligibleDrawCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($eligibleDrawCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'booking', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'booking') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span>Booking amount received</span>
                    </div>
                    @if(isset($bookingCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($bookingCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'not_visited', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'not_visited') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        <span>Booking amount not received</span>
                    </div>
                    @if(isset($notVisitedCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($notVisitedCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 4: ADC Verification Outcomes -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">4. ADC Eligibility Outcomes</div>
                <a href="{{ route('ews.department.list', ['type' => 'adc_passed', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'adc_passed') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">check_circle_outline</span>
                        <span>Eligible</span>
                    </div>
                    @if(isset($adcPassedCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($adcPassedCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'adc_failed', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'adc_failed') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">error_outline</span>
                        <span>Not Eligible</span>
                    </div>
                    @if(isset($adcFailedCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($adcFailedCount) }}</span>
                    @endif
                </a>
            </div>

            <!-- Group 5: Final Draw Allotment -->
            <div class="space-y-1">
                <div class="px-2 py-0.5 text-[8px] uppercase font-black tracking-wider text-slate-500">5. Final Draw Allotment</div>
                <a href="{{ route('ews.department.list', ['type' => 'all', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'all') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">groups</span>
                        <span>Total Beneficiaries</span>
                    </div>
                    @if(isset($totalCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($totalCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'allotted', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'allotted') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Allotted</span>
                    </div>
                    @if(isset($allottedCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($allottedCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'pending', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'pending') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">hourglass_empty</span>
                        <span>Waiting</span>
                    </div>
                    @if(isset($pendingCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($pendingCount) }}</span>
                    @endif
                </a>
                <a href="{{ route('ews.department.list', ['type' => 'draw_remaining', 'district_id' => $districtId ?? '']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($currentType === 'draw_remaining') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">hourglass_disabled</span>
                        <span>Unallotted Draw</span>
                    </div>
                    @if(isset($drawRemainingCount))
                        <span class="text-[9px] font-mono opacity-80">{{ number_format($drawRemainingCount) }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 2. Developers Management Section Header & Submenu -->
        <div class="pt-2">
            <button type="button" onclick="toggleDevelopersSubmenu()" class="w-full flex items-center justify-between rounded-lg px-4 py-2.5 transition-all text-left font-bold {{ $isDevActive ? 'bg-slate-800 text-white border-l-2 border-amber-500' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-base text-amber-400">engineering</span>
                    <span>Developers Hub</span>
                </div>
                <span id="dev-submenu-arrow" class="material-symbols-outlined text-sm">{{ $isDevActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
            </button>

            <!-- Developers Submenus -->
            <div id="developers-submenus" class="{{ $isDevActive ? '' : 'hidden' }} space-y-1 pl-2 border-l border-slate-700/60 ml-4 transition-all duration-300 mt-1">
                <!-- Developer Accounts (CRUD) -->
                <a href="{{ route('ews.department.developers.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 hover:bg-slate-800 hover:text-white transition-all text-left {{ request()->routeIs('ews.department.developers.*') ? 'bg-amber-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">manage_accounts</span>
                        <span>Developer Accounts</span>
                    </div>
                    @if(isset($developerCount))
                        <span class="text-[9px] font-mono bg-slate-800 text-amber-400 px-1.5 py-0.5 rounded font-bold">{{ $developerCount }}</span>
                    @endif
                </a>

                <!-- Developer Flat Submissions / Form Data -->
                <a href="{{ route('ews.department.developer-flats.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 hover:bg-slate-800 hover:text-white transition-all text-left {{ request()->routeIs('ews.department.developer-flats.*') ? 'bg-amber-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">apartment</span>
                        <span>Flat Submissions</span>
                    </div>
                    @if(isset($developerFlatsCount))
                        <span class="text-[9px] font-mono bg-slate-800 text-amber-400 px-1.5 py-0.5 rounded font-bold">{{ number_format($developerFlatsCount) }}</span>
                    @endif
                </a>

                <!-- Developer Activity Logs -->
                <a href="{{ route('ews.department.developer-logs.index') }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 hover:bg-slate-800 hover:text-white transition-all text-left {{ request()->routeIs('ews.department.developer-logs.*') ? 'bg-amber-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">receipt_long</span>
                        <span>Activity Logs</span>
                    </div>
                    @if(isset($developerLogsCount))
                        <span class="text-[9px] font-mono bg-slate-800 text-amber-400 px-1.5 py-0.5 rounded font-bold">{{ number_format($developerLogsCount) }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 3. EWS Raw Database Files Dropdown Toggle -->
        @php
            $isSeederActive = request()->routeIs('ews.department.seeder.*');
        @endphp
        <div class="pt-1">
            <button type="button" onclick="toggleSeederSubmenu()" class="w-full flex items-center justify-between rounded-lg px-4 py-2.5 transition-all text-left font-bold {{ $isSeederActive ? 'bg-slate-800 text-white border-l-2 border-orange-500' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-base text-orange-400">database</span>
                    <span>Raw Database Files</span>
                </div>
                <span id="seeder-submenu-arrow" class="material-symbols-outlined text-sm">{{ $isSeederActive ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</span>
            </button>

            <!-- Seeder Submenus Wrapper -->
            <div id="seeder-submenus" class="{{ $isSeederActive ? '' : 'hidden' }} space-y-1 pl-2 border-l border-slate-700/60 ml-4 transition-all duration-300 mt-1">
                <!-- Sonipat Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'SONIPAT']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($isSeederActive && request()->query('district') === 'SONIPAT') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        <span>Sonipat</span>
                    </div>
                </a>
                <!-- Gurugram Files -->
                <a href="{{ route('ews.department.seeder.index', ['district' => 'GURUGRAM']) }}" class="w-full flex items-center justify-between rounded-lg px-3 py-1.5 hover:bg-slate-800 hover:text-white transition-all text-left {{ ($isSeederActive && request()->query('district') === 'GURUGRAM') ? 'bg-orange-600 text-white font-bold' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        <span>Gurugram</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- 3. Standalone Primary Item: My Profile -->
        <a href="{{ route('ews.department.profile.show', Auth::user()->secure_id) }}" class="w-full flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all text-left font-bold {{ request()->routeIs('ews.department.profile.*') ? 'bg-orange-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <span class="material-symbols-outlined text-base text-orange-400">account_circle</span>
            <span>My Profile</span>
        </a>

    </nav>

    <!-- Sidebar Footer -->
    <div class="mt-auto px-6 pt-4 border-t border-slate-800">
        <div class="mb-4 px-2">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Active Scheme</p>
            <p class="text-xs font-bold text-orange-400 uppercase mt-0.5">EWS HOUSING</p>
        </div>
        <a href="{{ route('ews.department.logout') }}" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-rose-500 hover:bg-rose-950/30 hover:text-rose-400 transition-all font-bold">
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
        const activeSubmenuItem = document.querySelector('aside nav a.bg-orange-600, aside nav a.bg-amber-600');
        if (activeSubmenuItem) {
            activeSubmenuItem.scrollIntoView({ block: 'center', behavior: 'instant' });
        }
    });
</script>
