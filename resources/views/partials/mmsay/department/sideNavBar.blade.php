<aside
    class="h-screen w-52 fixed left-0 top-0 bg-surface-container-low dark:bg-inverse-surface shadow-sm border-r border-outline-variant dark:border-outline flex flex-col z-50">

    <!-- Brand Header -->
    <div class="px-4 pt-4 pb-4 flex items-center gap-2 border-b border-outline-variant">

        <img alt="Haryana State Emblem" class="w-8 h-8 object-contain" src="/Haryana_emblem.png" />

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

            function cmsActive()
            {
                return request()->is('mmsay-department-add-banner*') ||
                    request()->is('add-news*') ||
                    request()->is('upload-notice*') ||
                    request()->is('manage-notice*') ||
                    request()->is('upload-tender*') ||
                    request()->is('manage-tender*');
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

        {{-- <a href="{{ url('mmsay-department-cash-receipt') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-cash-receipt') }}">
            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
            <span>Cash Receipt</span>
        </a> --}}

        <a href="{{ url('mmsay-department-property-emi-calculation') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-property-emi-calculation') }}">

            <span class="material-symbols-outlined text-[18px]">calculate</span>

            <span>Property EMI Calculation</span>
        </a>

        <a href="{{ url('mmsay-department-add-district-officer') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] {{ activeMenu('mmsay-department-add-district-officer') }}">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>Add Site Engineer</span>
        </a>

        <hr class="my-2">

        <!-- CMS Menu -->
        <div x-data="{ cmsOpen: {{ cmsActive() ? 'true' : 'false' }} }">

            <button @click="cmsOpen = !cmsOpen"
                class="w-full flex items-center justify-between px-2 py-2 rounded-md text-[13px] transition-all
    {{ cmsActive()
        ? 'bg-secondary-container text-on-secondary-container font-semibold'
        : 'text-on-surface-variant hover:bg-surface-container-high' }}">

                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                    <span>CMS Management</span>
                </div>

                <span class="material-symbols-outlined text-[18px] transition-transform"
                    :class="{ 'rotate-180': cmsOpen }">
                    expand_more
                </span>
            </button>

            <!-- CMS Sub Menu -->
            <div x-show="cmsOpen" x-transition class="ml-2 mt-1 space-y-1">

                <a href="{{ url('mmsay-department-add-banner') }}"
                    class="block px-2 py-1.5 rounded-md text-[13px] {{ activeMenu('add-banner') }}">
                    Add Banner
                </a>

                <a href="{{ url('mmsay-department-add-news') }}"
                    class="block px-2 py-1.5 rounded-md text-[13px] {{ activeMenu('add-news') }}">
                    Add News
                </a>

                <!-- Notice Management -->
                {{-- <div x-data="{ noticeOpen: {{ request()->is('upload-notice*') || request()->is('manage-notice*') ? 'true' : 'false' }} }">

                    <button @click="noticeOpen = !noticeOpen"
                        class="w-full flex items-center justify-between px-2 py-1.5 rounded-md text-[13px]
                {{ request()->is('upload-notice*') || request()->is('manage-notice*')
                    ? 'bg-secondary-container text-on-secondary-container font-semibold'
                    : 'hover:bg-surface-container-high' }}">

                        <span>Notice Management</span>

                        <span class="material-symbols-outlined text-[16px] transition-transform"
                            :class="{ 'rotate-180': noticeOpen }">
                            expand_more
                        </span>
                    </button>

                    <div x-show="noticeOpen" x-transition class="ml-2 mt-1 space-y-1">

                        <a href="{{ url('upload-notice') }}"
                            class="block px-2 py-1.5 rounded-md text-[13px] {{ activeMenu('upload-notice') }}">
                            Upload Notice
                        </a>

                        <a href="{{ url('manage-notice') }}"
                            class="block px-2 py-1.5 rounded-md text-[13px] {{ activeMenu('manage-notice') }}">
                            Manage Notice
                        </a>

                    </div>
                </div> --}}

                <!-- Tender Management -->
                {{-- <div x-data="{ tenderOpen: {{ request()->is('upload-tender*') || request()->is('manage-tender*') ? 'true' : 'false' }} }">

                    <button @click="tenderOpen = !tenderOpen"
                        class="w-full flex items-center justify-between px-2 py-1.5 rounded-md text-[13px]
                {{ request()->is('upload-tender*') || request()->is('manage-tender*')
                    ? 'bg-secondary-container text-on-secondary-container font-semibold'
                    : 'hover:bg-surface-container-high' }}">

                        <span>Tender Management</span>

                        <span class="material-symbols-outlined text-[16px] transition-transform"
                            :class="{ 'rotate-180': tenderOpen }">
                            expand_more
                        </span>
                    </button>

                    <div x-show="tenderOpen" x-transition class="ml-2 mt-1 space-y-1">

                        <a href="{{ url('upload-tender') }}"
                            class="block px-2 py-1.5 rounded-md text-[13px] {{ activeMenu('upload-tender') }}">
                            Upload Tender
                        </a>

                        <a href="{{ url('manage-tender') }}"
                            class="block px-2 py-1.5 rounded-md text-[13px] {{ activeMenu('manage-tender') }}">
                            Manage Tender
                        </a>

                    </div>
                </div> --}}

            </div>
        </div>

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
