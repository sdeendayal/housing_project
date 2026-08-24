{{-- ============================================================
    MODERN SUPER ADMIN SIDEBAR
============================================================ --}}

<style>
    /* ============================================================
       LIGHT SUPER ADMIN SIDEBAR
       Same functionality - only styling fixed
    ============================================================ */

    .admin-sidebar {
        background:
            radial-gradient(
                circle at 15% 5%,
                rgba(79, 70, 229, 0.055),
                transparent 24%
            ),
            linear-gradient(
                180deg,
                #ffffff 0%,
                #f8faff 55%,
                #f5f7ff 100%
            ) !important;

        border-right: 1px solid #e2e8f0 !important;

        box-shadow:
            6px 0 24px rgba(15, 23, 42, 0.045) !important;
    }


    /* ============================================================
       NAV AREA
    ============================================================ */

    .admin-sidebar-scroll {
        display: block !important;
        width: 100% !important;

        padding: 14px 12px !important;

        overflow-y: auto;
        overflow-x: hidden;
    }


    /* IMPORTANT:
       Every menu must occupy its own row
    */
    .sidebar-link {
        position: relative;

        display: flex !important;
        align-items: center !important;

        width: 100% !important;
        min-height: 42px;

        gap: 10px;

        margin: 0 0 3px 0 !important;

        padding: 6px 9px !important;

        border-radius: 10px;

        color: #52637a !important;

        font-size: 12px;
        font-weight: 600;

        line-height: 1.2;

        text-decoration: none !important;

        white-space: nowrap;

        transition:
            background-color 0.18s ease,
            color 0.18s ease,
            transform 0.18s ease,
            box-shadow 0.18s ease;
    }


    .sidebar-link:hover {
        color: #4338ca !important;

        background: #f0f3ff !important;

        transform: translateX(2px);
    }


    /* ============================================================
       MENU ICON
    ============================================================ */

    .sidebar-icon {
        display: flex !important;

        width: 30px !important;
        height: 30px !important;

        min-width: 30px !important;

        align-items: center !important;
        justify-content: center !important;

        border-radius: 8px;

        background: #f1f5f9 !important;

        color: #64748b !important;

        flex-shrink: 0 !important;

        transition: 0.18s ease;
    }


    .sidebar-icon .material-symbols-outlined {
        display: block;

        font-size: 17px !important;

        line-height: 1 !important;
    }


    .sidebar-link:hover .sidebar-icon {
        background: #e8edff !important;
        color: #4f46e5 !important;
    }


    /* ============================================================
       ACTIVE MENU
    ============================================================ */

    .sidebar-link.active {
        color: #ffffff !important;

        background:
            linear-gradient(
                135deg,
                #5265f6 0%,
                #4338ca 100%
            ) !important;

        box-shadow:
            0 7px 17px rgba(79, 70, 229, 0.22) !important;

        transform: none;
    }


    .sidebar-link.active .sidebar-icon {
        color: #ffffff !important;

        background:
            rgba(255, 255, 255, 0.16) !important;
    }


    .sidebar-link.active:hover {
        color: #ffffff !important;

        background:
            linear-gradient(
                135deg,
                #4f5fe8 0%,
                #3730a3 100%
            ) !important;
    }


    /* Active dot */
    .sidebar-link.active > span:last-child.h-2 {
        background: #ffffff !important;
    }


    /* ============================================================
       SECTION HEADING
    ============================================================ */

    .sidebar-heading {
        display: block !important;

        width: 100%;

        padding: 13px 9px 5px !important;

        color: #94a3b8 !important;

        font-size: 9px !important;
        font-weight: 800 !important;

        line-height: 1.2;

        letter-spacing: 0.14em !important;

        text-transform: uppercase;

        clear: both !important;
    }


    /* ============================================================
       LOGO AREA
    ============================================================ */

    .admin-sidebar > div:first-child {
        background: #ffffff !important;

        border-bottom: 1px solid #e8edf5 !important;
    }


    .admin-sidebar > div:first-child h1 {
        color: #172033 !important;
    }


    .admin-sidebar > div:first-child p {
        color: #6366f1 !important;
    }


    /* Keep logo gradient */
    .admin-sidebar > div:first-child > div:first-child {
        background:
            linear-gradient(
                135deg,
                #4f6df5 0%,
                #4f46e5 100%
            ) !important;

        color: #ffffff !important;

        box-shadow:
            0 8px 18px rgba(79, 70, 229, 0.24) !important;
    }


    /* ============================================================
       PROFILE AREA
    ============================================================ */

    .admin-sidebar > div:last-child {
        background:
            rgba(248, 250, 255, 0.96) !important;

        border-top:
            1px solid #e2e8f0 !important;
    }


    .admin-sidebar > div:last-child > div {
        background: #ffffff !important;

        border:
            1px solid #dbe3ef !important;

        box-shadow:
            0 4px 13px rgba(15, 23, 42, 0.05) !important;
    }


    .admin-sidebar > div:last-child h4 {
        color: #172033 !important;
    }


    .admin-sidebar > div:last-child p {
        color: #64748b !important;
    }


    /* Profile SA circle */
    .admin-sidebar > div:last-child > div > div:first-child {
        background:
            linear-gradient(
                135deg,
                #586bf7 0%,
                #3b82f6 100%
            ) !important;

        color: #ffffff !important;
    }


    /* ============================================================
       LOGOUT
    ============================================================ */

    .admin-sidebar form button {
        width: 100%;

        background: #fff7f7 !important;

        border:
            1px solid #fecaca !important;

        color: #dc2626 !important;

        box-shadow: none !important;
    }


    .admin-sidebar form button:hover {
        background: #ef4444 !important;

        border-color: #ef4444 !important;

        color: #ffffff !important;
    }


    /* ============================================================
       SCROLLBAR
    ============================================================ */

    .admin-sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }


    .admin-sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }


    .admin-sidebar-scroll::-webkit-scrollbar-thumb {
        background: #d6deea;

        border-radius: 999px;
    }


    /* ============================================================
       MOBILE
    ============================================================ */

    @media (max-width: 1279px) {
        .admin-sidebar {
            transform: translateX(-100%);
        }
    }

    /* =========================================================
   REMOVE OLD TOP HEADER COMPLETELY
========================================================= */

.admin-header,
.dashboard-header,
header.admin-header {
    display: none !important;
}

/* Keep the premium Super Admin top header visible */
.top-admin-header,
header.top-admin-header {
    display: block !important;
    background: linear-gradient(100deg, #4338ca 0%, #3f46df 38%, #2563eb 72%, #1d4ed8 100%) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    box-shadow: 0 5px 18px rgba(37, 99, 235, 0.18) !important;
}

@media (max-width: 1279px) {
    .top-admin-header,
    header.top-admin-header {
        left: 0 !important;
        right: auto !important;
        width: 100% !important;
    }
}
</style>



<aside
    class="
        admin-sidebar
        fixed
        left-0
        top-0
        z-40
        flex
        h-full
        w-[260px]
        flex-col
    ">

    {{-- ============================================================
        LOGO
    ============================================================ --}}

    <div
        class="
            flex
            h-20
            shrink-0
            items-center
            gap-3
            border-b
            border-white/10
            px-5
        ">

        <div
            class="
                flex
                h-11
                w-11
                items-center
                justify-center
                rounded-[14px]
                bg-gradient-to-br
                from-blue-500
                to-indigo-600
                text-white
                shadow-lg
                shadow-indigo-950/30
            ">

            <span
                class="
                    material-symbols-outlined
                    text-[23px]
                ">

                home_work

            </span>

        </div>


        <div class="min-w-0">

            <h1
                class="
                    truncate
                    text-[16px]
                    font-black
                    tracking-tight
                    text-white
                ">

                MMGAY Admin

            </h1>


            <p
                class="
                    mt-0.5
                    truncate
                    text-[9px]
                    font-bold
                    uppercase
                    tracking-[.14em]
                    text-blue-300
                ">

                Management Portal

            </p>

        </div>

    </div>



    {{-- ============================================================
        NAVIGATION
    ============================================================ --}}

    <nav
        class="
            admin-sidebar-scroll
            flex-1
            overflow-y-auto
            px-3
            py-4
        ">


        {{-- Dashboard --}}

        <a href="{{ route('admin.dashboard') }}"
            class="
                sidebar-link
                {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span
                    class="
                        material-symbols-outlined
                        text-[19px]
                    ">

                    dashboard

                </span>

            </span>


            <span class="flex-1">

                Dashboard

            </span>


            @if (request()->routeIs('admin.dashboard'))
                <span
                    class="
                        h-2
                        w-2
                        rounded-full
                        bg-white
                        shadow
                    ">
                </span>
            @endif

        </a>



        {{-- ============================================================
            MASTER
        ============================================================ --}}

        <div class="sidebar-heading">

            Master

        </div>



        {{-- District --}}

        <a href="{{ route('admin.district.report') }}"
            class="
                sidebar-link
                {{ request()->routeIs('admin.district.report') ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    assessment

                </span>

            </span>


            <span class="flex-1 truncate">

                District Report

            </span>

        </a>



        {{-- Village --}}

        <a href="{{ route('admin.village.report') }}"
            class="
                sidebar-link
                {{ request()->routeIs('admin.village.report*') ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    holiday_village

                </span>

            </span>


            <span class="flex-1 truncate">

                Village Report

            </span>

        </a>



        {{-- Applicants --}}

        <a href="{{ route('superadmin.applicants.index') }}"
            class="
                sidebar-link
                {{ request()->routeIs('superadmin.applicants*') ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    groups

                </span>

            </span>


            <span class="flex-1 truncate">

                Applicants

            </span>

        </a>



        {{-- ============================================================
            BENEFICIARY
        ============================================================ --}}

        <div class="sidebar-heading">

            Beneficiary

        </div>



        {{-- Allotment Report --}}

        <a href="{{ route('admin.allotment.report') }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.allotment.report') && !request('status') ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    real_estate_agent

                </span>

            </span>


            <span class="flex-1 truncate">

                Allotment Report

            </span>

        </a>



        {{-- Approved Paid --}}

        <a href="{{ route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                'status' => 'approved_paid',
            ]),
        ) }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.allotment.report') && request('status') === 'approved_paid' ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    verified

                </span>

            </span>


            <span class="flex-1 truncate">

                Approved & Paid

            </span>

        </a>



        {{-- Approved Unpaid --}}

        <a href="{{ route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                'status' => 'approved_unpaid',
            ]),
        ) }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.allotment.report') && request('status') === 'approved_unpaid' ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    account_balance_wallet

                </span>

            </span>


            <span class="flex-1 truncate">

                Approved & Unpaid

            </span>

        </a>



        {{-- Pending --}}

        <a href="{{ route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                'status' => 'pending',
            ]),
        ) }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.allotment.report') && request('status') === 'pending' ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    pending_actions

                </span>

            </span>


            <span class="flex-1 truncate">

                Yet to be Approved

            </span>

        </a>



        {{-- Rejected --}}

        <a href="{{ route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                'status' => 'rejected',
            ]),
        ) }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.allotment.report') && request('status') === 'rejected' ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    cancel

                </span>

            </span>


            <span class="flex-1 truncate">

                Rejected

            </span>

        </a>



        {{-- Cancelled --}}

        <a href="{{ route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                'status' => 'cancelled',
            ]),
        ) }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.allotment.report') && request('status') === 'cancelled' ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    block

                </span>

            </span>


            <span class="flex-1 truncate">

                Cancelled

            </span>

        </a>



        {{-- ============================================================
            REGISTRATION
        ============================================================ --}}

        <div class="sidebar-heading">

            Registration

        </div>



        {{-- Total Registry --}}

        <a href="{{ route(
            'admin.registration',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                'type' => 'all',
            ]),
        ) }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.registration') && request('type', 'all') === 'all' ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    description

                </span>

            </span>


            <span class="flex-1 truncate">

                Total Registration

            </span>

        </a>



        {{-- Matched --}}

        <a href="{{ route(
            'admin.registration',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                'type' => 'matched',
            ]),
        ) }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.registration') && request('type') === 'matched' ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    task_alt

                </span>

            </span>


            <span class="flex-1 truncate">

                Matched

            </span>

        </a>



        {{-- Unmatched --}}

        <a href="{{ route(
            'admin.registration',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                'type' => 'unmatched',
            ]),
        ) }}"
            class="
                sidebar-link

                {{ request()->routeIs('admin.registration') && request('type') === 'unmatched' ? 'active' : '' }}
            ">

            <span class="sidebar-icon">

                <span class="material-symbols-outlined text-[19px]">

                    problem

                </span>

            </span>


            <span class="flex-1 truncate">

                Unmatched

            </span>

        </a>

    </nav>



    {{-- ============================================================
        BOTTOM PROFILE
    ============================================================ --}}

    <div
        class="
            shrink-0
            border-t
            border-white/10
            bg-black/10
            p-3
        ">


        <div
            class="
                mb-2.5
                flex
                items-center
                gap-3
                rounded-xl
                border
                border-white/10
                bg-white/[.06]
                p-2.5
            ">


            <div
                class="
                    flex
                    h-10
                    w-10
                    shrink-0
                    items-center
                    justify-center
                    rounded-full
                    bg-gradient-to-br
                    from-indigo-500
                    to-blue-500
                    text-xs
                    font-black
                    text-white
                    shadow-lg
                    shadow-black/10
                ">

                SA

            </div>


            <div class="min-w-0">

                <h4
                    class="
                        truncate
                        text-[12px]
                        font-extrabold
                        text-white
                    ">

                    Super Admin

                </h4>


                <p
                    class="
                        mt-0.5
                        truncate
                        text-[9px]
                        font-medium
                        text-slate-400
                    ">

                    MMGAY Management Portal

                </p>

            </div>

        </div>



        <form action="{{ route('mmgay.logout') }}" method="POST">

            @csrf


            <button type="submit"
                class="
                    flex
                    h-10
                    w-full
                    items-center
                    justify-center
                    gap-2
                    rounded-xl
                    border
                    border-red-400/20
                    bg-red-500/10
                    text-[11px]
                    font-extrabold
                    text-red-300
                    transition
                    hover:border-red-500
                    hover:bg-red-500
                    hover:text-white
                ">

                <span class="material-symbols-outlined text-[17px]">

                    logout

                </span>

                Logout

            </button>

        </form>

    </div>

</aside>



{{-- ============================================================
    PREMIUM TOP HEADER
============================================================ --}}

<header
    class="
        top-admin-header
        fixed
        right-0
        top-0
        z-50
        h-16
        w-[calc(100%-260px)]
    ">

    <div
        class="
            flex
            h-full
            items-center
            justify-between
            px-5
            xl:px-6
        ">


        {{-- Left --}}

        <div
            class="
                flex
                min-w-0
                items-center
                gap-3
            ">


            <div
                class="
                    flex
                    h-10
                    w-10
                    shrink-0
                    items-center
                    justify-center
                    rounded-xl
                    border
                    border-white/15
                    bg-white/15
                    text-white
                    backdrop-blur
                ">

                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                    <path stroke-linecap="round" stroke-linejoin="round" d="
                            M3 13h8V3H3v10
                            m10 8h8V11h-8v10
                            M3 21h8v-6H3v6
                            m10-10h8V3h-8v8
                        " />

                </svg>

            </div>


            <div class="min-w-0">

                <h2
                    class="
                        truncate
                        text-[18px]
                        font-black
                        tracking-tight
                        text-white
                    ">

                    Super Admin Dashboard

                </h2>


                <p
                    class="
                        truncate
                        text-[10px]
                        font-medium
                        text-blue-100
                    ">

                    MMGAY Monitoring System

                </p>

            </div>

        </div>



        {{-- Right --}}

        <div class="
                flex
                items-center
                gap-3
            ">


            {{-- Date --}}

            <div
                class="
                    hidden
                    h-10
                    items-center
                    gap-2
                    rounded-xl
                    border
                    border-white/15
                    bg-white/10
                    px-4
                    text-white
                    backdrop-blur
                    md:flex
                ">

                <span
                    class="
                        material-symbols-outlined
                        text-[18px]
                        text-yellow-300
                    ">

                    calendar_month

                </span>


                <span
                    class="
                        text-[11px]
                        font-extrabold
                    ">

                    {{ date('d M Y') }}

                </span>

            </div>



            {{-- Notification --}}

            <button type="button"
                class="
                    relative
                    flex
                    h-10
                    w-10
                    items-center
                    justify-center
                    rounded-xl
                    border
                    border-white/15
                    bg-white/10
                    text-white
                    transition
                    hover:bg-white/20
                ">

                <span class="material-symbols-outlined text-[20px]">

                    notifications

                </span>


                <span
                    class="
                        absolute
                        right-1.5
                        top-1.5
                        h-2
                        w-2
                        rounded-full
                        bg-red-400
                        ring-2
                        ring-blue-600
                    ">
                </span>

            </button>



            {{-- Profile --}}

            <div
                class="
                    flex
                    h-11
                    items-center
                    gap-2.5
                    rounded-xl
                    border
                    border-white/15
                    bg-white/10
                    px-2.5
                    backdrop-blur
                ">


                <div
                    class="
                        flex
                        h-8
                        w-8
                        items-center
                        justify-center
                        rounded-full
                        bg-white
                        text-[11px]
                        font-black
                        text-indigo-700
                        shadow
                    ">

                    SA

                </div>


                <div
                    class="
                        hidden
                        min-w-[82px]
                        lg:block
                    ">

                    <p
                        class="
                            text-[11px]
                            font-extrabold
                            leading-none
                            text-white
                        ">

                        Super Admin

                    </p>


                    <p
                        class="
                            mt-1
                            text-[9px]
                            font-medium
                            leading-none
                            text-blue-100
                        ">

                        MMGAY Portal

                    </p>

                </div>


                <span
                    class="
                        material-symbols-outlined
                        hidden
                        text-[16px]
                        text-blue-100
                        lg:block
                    ">

                    expand_more

                </span>

            </div>

        </div>

    </div>

</header>