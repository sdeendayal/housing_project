<aside
    class="h-screen w-60 fixed left-0 top-0 bg-surface-container-low dark:bg-inverse-surface shadow-sm border-r border-outline-variant dark:border-outline flex flex-col z-50">

    <!-- Brand Header -->
    <div class="px-4 pt-4 pb-4 flex items-center gap-2 border-b border-outline-variant">

        <img alt="Haryana State Emblem" class="w-8 h-8 object-contain" src="Haryana_emblem.png" />

        <div>
            <h1 class="text-sm font-semibold leading-tight text-primary">
                Department of Housing For All
            </h1>

            <p class="text-[11px] text-on-surface-variant">
                Government of Haryana
            </p>
        </div>
    </div>

    <!-- Navigation Scroll Area -->
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-2 py-2 space-y-1">

        @php
            function activeMenu($route)
            {
                return request()->is($route . '*')
                    ? 'bg-secondary-container text-on-secondary-container font-semibold'
                    : 'text-on-surface-variant hover:bg-surface-container-high';
            }
        @endphp

        <a href="{{ url('mmsay-department-dashboard') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition-all duration-200 {{ activeMenu('mmsay-department-dashboard') }}">
            <span class="material-symbols-outlined text-[18px]">dashboard</span>
            <span>Dashboard</span>
        </a>

        <a href="{{ url('mmsay-department-property-registration') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-property-registration') }}">
            <span class="material-symbols-outlined text-[18px]">app_registration</span>
            <span>Property Registration</span>
        </a>

        <a href="{{ url('mmsay-department-allotted-properties') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-allotted-properties') }}">
            <span class="material-symbols-outlined text-[18px]">location_city</span>
            <span>Allotted Properties</span>
        </a>

        <a href="{{ url('mmsay-department-cash-receipt') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-cash-receipt') }}">
            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
            <span>Cash Receipt</span>
        </a>

        <a href="{{ url('mmsay-department-browse-receipt') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-browse-receipt') }}">
            <span class="material-symbols-outlined text-[18px]">search</span>
            <span>Browse Receipt</span>
        </a>

        <a href="{{ url('mmsay-department-property-emi-calculation') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-property-emi-calculation') }}">
            <span class="material-symbols-outlined text-[18px]">calculate</span>
            <span>Property EMI Calculation</span>
        </a>

        <a href="{{ url('mmsay-department-ledger-report') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-ledger-report') }}">
            <span class="material-symbols-outlined text-[18px]">assessment</span>
            <span>Ledger Report</span>
        </a>

        <a href="{{ url('mmsay-department-property-private-purchase') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-property-private-purchase') }}">
            <span class="material-symbols-outlined text-[18px]">real_estate_agent</span>
            <span>Private Purchase</span>
        </a>

        <a href="{{ url('mmsay-department-property-transfer') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-property-transfer') }}">
            <span class="material-symbols-outlined text-[18px]">move_down</span>
            <span>Property Transfer</span>
        </a>

        <a href="{{ url('mmsay-department-property-report') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-property-report') }}">
            <span class="material-symbols-outlined text-[18px]">analytics</span>
            <span>Property Report</span>
        </a>

        <a href="{{ url('mmsay-department-online-payment-report') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-online-payment-report') }}">
            <span class="material-symbols-outlined text-[18px]">payments</span>
            <span>Online Payment</span>
        </a>

        <a href="{{ url('mmsay-department-noc-receipt') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-noc-receipt') }}">
            <span class="material-symbols-outlined text-[18px]">description</span>
            <span>NOC Receipt</span>
        </a>

        <a href="{{ url('mmsay-department-noc-approval') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-noc-approval') }}">
            <span class="material-symbols-outlined text-[18px]">verified</span>
            <span>NOC Approval</span>
        </a>

    </nav>

    <!-- Sidebar Footer -->
    <div class="p-2 border-t border-outline-variant">

        <a href="{{ route('logout') }}"
            class="flex items-center gap-2 px-3 py-2 text-[13px] text-red-600 hover:bg-red-50 transition-colors rounded-md cursor-pointer">

            <span class="material-symbols-outlined text-[18px]">logout</span>
            <span>Logout</span>

        </a>

    </div>
</aside>
