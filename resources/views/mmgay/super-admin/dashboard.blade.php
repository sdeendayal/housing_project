@extends('layouts.mmgayAdmin')

@section('title', 'Super Admin Dashboard')

@section('content')

    @php
        $grossTotal = max((int) ($summary->GrossTotal ?? 0), 1);

        $approvedPaidPct = (($summary->ApprovedPaid ?? 0) / $grossTotal) * 100;

        $approvedUnpaidPct = (($summary->ApprovedUnpaid ?? 0) / $grossTotal) * 100;

        $pendingPct = (($summary->PendingApprovalPayment ?? 0) / $grossTotal) * 100;

        $rejectedPct = (($summary->Rejected ?? 0) / $grossTotal) * 100;

        $cancelledPct = (($summary->AllotmentCancelled ?? 0) / $grossTotal) * 100;

        $registrationTotal = max((int) ($registration->TotalRegistration ?? 0), 1);

        $matchedPct = (($registration->Matched ?? 0) / $registrationTotal) * 100;

        $unmatchedPct = (($registration->UnMatched ?? 0) / $registrationTotal) * 100;
    @endphp


    <style>
        .ai-dashboard {
            background:
                radial-gradient(circle at 75% 0%,
                    rgba(79, 70, 229, .06),
                    transparent 24%),
                #f6f8fc;
        }

        .ai-panel {
            background: rgba(255, 255, 255, .98);
            border: 1px solid #e3e9f2;
            border-radius: 18px;

            box-shadow:
                0 10px 35px rgba(15, 23, 42, .055),
                0 1px 2px rgba(15, 23, 42, .03);
        }

        .ai-filter-select {
            width: 100%;
            height: 44px;

            border: 1px solid #dbe3ef;
            border-radius: 11px;

            background: #ffffff;

            padding-left: 13px;
            padding-right: 32px;

            color: #0f172a;

            font-size: 12px;
            font-weight: 600;

            outline: none;

            transition: .2s ease;
        }

        .ai-filter-select:focus {
            border-color: #6366f1;

            box-shadow:
                0 0 0 4px rgba(99, 102, 241, .09);
        }

        .ai-label {
            display: block;

            margin-bottom: 6px;

            color: #475569;

            font-size: 10px;
            font-weight: 800;

            letter-spacing: .06em;

            text-transform: uppercase;
        }

        .ai-apply {
            height: 44px;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            width: 100%;

            border-radius: 11px;

            color: white;

            font-size: 12px;
            font-weight: 800;

            background:
                linear-gradient(135deg,
                    #2563eb 0%,
                    #4f46e5 100%);

            box-shadow:
                0 8px 18px rgba(37, 99, 235, .22);

            transition: .2s ease;
        }

        .ai-apply:hover {
            transform: translateY(-1px);

            box-shadow:
                0 12px 25px rgba(37, 99, 235, .30);
        }


        /* ============================
                               MASTER CARD
                            ============================ */

        .master-card {
            position: relative;

            overflow: hidden;

            min-height: 112px;

            background: white;

            border: 1px solid #e2e8f0;

            border-radius: 17px;

            box-shadow:
                0 6px 20px rgba(15, 23, 42, .045);

            transition: .22s ease;
        }

        .master-card:hover {
            transform: translateY(-3px);

            box-shadow:
                0 15px 30px rgba(15, 23, 42, .09);
        }

        .master-card::before {
            content: "";

            position: absolute;

            left: 0;
            top: 0;

            height: 3px;
            width: 54px;

            border-radius: 0 0 10px 0;
        }

        .master-card::after {
            content: "";

            position: absolute;

            width: 150px;
            height: 90px;

            border-radius: 50%;

            right: -35px;
            bottom: -42px;

            opacity: .75;
        }

        .master-blue::before {
            background: #3b82f6;
        }

        .master-blue::after {
            background:
                linear-gradient(135deg,
                    rgba(59, 130, 246, .16),
                    rgba(59, 130, 246, .01));
        }

        .master-green::before {
            background: #22c55e;
        }

        .master-green::after {
            background:
                linear-gradient(135deg,
                    rgba(34, 197, 94, .15),
                    transparent);
        }

        .master-purple::before {
            background: #7c3aed;
        }

        .master-purple::after {
            background:
                linear-gradient(135deg,
                    rgba(124, 58, 237, .15),
                    transparent);
        }

        .master-orange::before {
            background: #f97316;
        }

        .master-orange::after {
            background:
                linear-gradient(135deg,
                    rgba(249, 115, 22, .17),
                    transparent);
        }


        /* ============================
                               ICON
                            ============================ */

        .ai-icon {
            display: flex;

            width: 46px;
            height: 46px;

            align-items: center;
            justify-content: center;

            border-radius: 14px;

            flex-shrink: 0;
        }


        /* ============================
                               STATUS CARD
                            ============================ */

        .status-card {
            min-height: 102px;

            border: 1px solid #e5eaf2;

            border-radius: 15px;

            padding: 14px;

            transition: .2s ease;
        }

        .status-card:hover {
            transform: translateY(-2px);

            box-shadow:
                0 10px 25px rgba(15, 23, 42, .07);
        }

        .status-total {
            background:
                linear-gradient(135deg, #ffffff, #f2f7ff);
        }

        .status-paid {
            background:
                linear-gradient(135deg, #ffffff, #effcf4);
        }

        .status-unpaid {
            background:
                linear-gradient(135deg, #ffffff, #fff9e7);
        }

        .status-pending {
            background:
                linear-gradient(135deg, #ffffff, #fff5e8);
        }

        .status-rejected {
            background:
                linear-gradient(135deg, #ffffff, #fff0f2);
        }

        .status-cancelled {
            background:
                linear-gradient(135deg, #ffffff, #f5f7fa);
        }


        /* ============================
                               DONUT
                            ============================ */

        .registry-donut {
            width: 138px;
            height: 138px;

            border-radius: 50%;

            display: grid;
            place-items: center;

            position: relative;

            background:
                conic-gradient(#22c55e 0 calc(var(--matched) * 1%),
                    #ef4444 calc(var(--matched) * 1%) 100%);
        }

        .registry-donut::after {
            content: "";

            position: absolute;

            width: 90px;
            height: 90px;

            border-radius: 50%;

            background: white;

            box-shadow:
                0 0 0 1px #edf2f7;
        }

        .registry-donut-value {
            position: relative;

            z-index: 2;

            text-align: center;
        }


        @media(max-width: 1279px) {

            .ai-dashboard {
                margin-left: 0 !important;

                width: 100% !important;
            }
        }

        /* =================================
               FILTER DROPDOWN - SAME THEME
            ================================= */

        .filter-field {
            position: relative;
        }

        /* left icon */
        .filter-field-icon {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);

            width: 29px;
            height: 29px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 8px;

            background: linear-gradient(135deg,
                    #eff6ff 0%,
                    #eef2ff 100%);

            color: #4f46e5;

            pointer-events: none;
            z-index: 2;

            border: 1px solid #e0e7ff;
        }

        .filter-field-icon svg {
            width: 15px;
            height: 15px;
        }


        /* dropdown */
        .ai-filter-select {
            width: 100%;
            height: 44px;

            border: 1px solid #dbe3ef;
            border-radius: 11px;

            background-color: #ffffff;

            color: #334155;

            font-size: 12px;
            font-weight: 600;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;

            cursor: pointer;
        }


        /* space for icon */
        .ai-filter-select-icon {
            padding-left: 46px;
            padding-right: 35px;
        }


        /* hover */
        .ai-filter-select:hover {
            border-color: #a5b4fc;

            background-color: #fafbff;
        }


        /* selected/focus */
        .ai-filter-select:focus {
            border-color: #6366f1;

            background-color: #ffffff;

            box-shadow:
                0 0 0 3px rgba(99, 102, 241, .10);
        }


        /* icon also highlighted when dropdown focus */
        .filter-field:focus-within .filter-field-icon {
            color: #ffffff;

            border-color: transparent;

            background: linear-gradient(135deg,
                    #2563eb 0%,
                    #4f46e5 100%);

            box-shadow:
                0 5px 12px rgba(79, 70, 229, .20);
        }


        /* label */
        .ai-label {
            display: block;

            margin-bottom: 6px;

            color: #475569;

            font-size: 10px;
            font-weight: 800;

            letter-spacing: .06em;

            text-transform: uppercase;
        }


        /* =================================
               APPLY BUTTON
            ================================= */

        .ai-apply {
            width: 100%;
            height: 44px;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 6px;

            border-radius: 11px;

            background: linear-gradient(135deg,
                    #2563eb 0%,
                    #4f46e5 100%);

            color: #ffffff;

            font-size: 12px;
            font-weight: 800;

            box-shadow:
                0 7px 16px rgba(37, 99, 235, .20);

            transition: all .2s ease;
        }

        .ai-apply:hover {
            background: linear-gradient(135deg,
                    #1d4ed8 0%,
                    #4338ca 100%);

            transform: translateY(-1px);

            box-shadow:
                0 10px 22px rgba(37, 99, 235, .28);
        }

        .ai-apply svg {
            width: 16px;
            height: 16px;
        }

        /* ============================================================
                   REFERENCE IMAGE ICON FIX
                   Visual-only change: routes, filters, values and JS untouched
                ============================================================ */

        /* Existing master / possession icon boxes */
        .ai-icon {
            width: 46px !important;
            height: 46px !important;
            min-width: 46px !important;
            border-radius: 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow:
                inset 0 0 0 1px rgba(15, 23, 42, .035),
                0 7px 16px rgba(15, 23, 42, .06) !important;
        }

        .ai-icon svg {
            width: 24px !important;
            height: 24px !important;
            display: block !important;
            stroke-width: 2.65 !important;
            stroke-linecap: round !important;
            stroke-linejoin: round !important;
        }

        .ai-icon .material-symbols-outlined {
            font-size: 23px !important;
            line-height: 1 !important;
            font-variation-settings:
                'FILL' 1,
                'wght' 700,
                'GRAD' 0,
                'opsz' 24;
        }

        /* Stronger reference-image colors for existing boxes */
        .master-blue .ai-icon {
            color: #2563eb !important;
            background: #e9f2ff !important;
            border: 1px solid #d8e7ff !important;
        }

        .master-green .ai-icon {
            color: #16a34a !important;
            background: #dcfce7 !important;
            border: 1px solid #c6f2d3 !important;
        }

        .master-purple .ai-icon {
            color: #6d28d9 !important;
            background: #f1e8ff !important;
            border: 1px solid #e7d8ff !important;
        }

        .master-orange .ai-icon {
            color: #f97316 !important;
            background: #fff0df !important;
            border: 1px solid #ffe0bd !important;
        }

        /* New icon holder added to cards which previously had no icon */
        .card-ref-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);

            width: 42px;
            height: 42px;
            min-width: 42px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            z-index: 2;

            box-shadow:
                inset 0 0 0 1px rgba(15, 23, 42, .035),
                0 6px 14px rgba(15, 23, 42, .055);
        }

        .card-ref-icon svg {
            width: 21px;
            height: 21px;
            display: block;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.55;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .card-ref-blue {
            color: #2563eb;
            background: #e9f2ff;
        }

        .card-ref-green {
            color: #16a34a;
            background: #dcfce7;
        }

        .card-ref-purple {
            color: #6d28d9;
            background: #f1e8ff;
        }

        .card-ref-orange {
            color: #ea580c;
            background: #ffedd5;
        }

        .card-ref-amber {
            color: #d97706;
            background: #fef3c7;
        }

        .card-ref-red {
            color: #dc2626;
            background: #fee2e2;
        }

        .card-ref-slate {
            color: #475569;
            background: #e8eef5;
        }

        /* Status cards: keep all existing content, only reserve icon space */
        .status-card {
            position: relative !important;
            padding-left: 68px !important;
        }

        /* Registration cards: keep existing content and add same left icon style */
        .registration-icon-card {
            position: relative !important;
            padding-left: 72px !important;
        }

        .registration-icon-card .card-ref-icon {
            left: 16px;
        }

        /* Make all reference icons visually crisp */
        .card-ref-icon,
        .ai-icon {
            -webkit-font-smoothing: antialiased;
            text-rendering: geometricPrecision;
        }
    </style>



    <main
        class="
            ai-dashboard
            ml-[260px]
            min-h-screen
            w-[calc(100%-260px)]
            overflow-x-hidden
            px-5
            pb-8
            pt-20
        ">


        {{-- ===========================================
             DASHBOARD FILTER
        ============================================ --}}

        <section class="ai-panel mb-5">

            <div
                class="
                    flex
                    flex-col
                    gap-4
                    px-5
                    py-4
                    lg:flex-row
                    lg:items-center
                    lg:justify-between
                ">

                <div class="flex items-center gap-3">

                    <div
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center
                            rounded-xl
                            bg-indigo-50
                            text-indigo-600
                        ">

                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round" d="
                                                        M3 4h18
                                                        M6 8h12
                                                        l-4.5 5v5
                                                        l-3 2v-7
                                                        L6 8z
                                                    " />

                        </svg>

                    </div>


                    <div>

                        <h2
                            class="
                                text-[16px]
                                font-extrabold
                                text-slate-900
                            ">

                            Dashboard Filters

                        </h2>


                        <p
                            class="
                                mt-0.5
                                text-[11px]
                                font-medium
                                text-slate-500
                            ">

                            Filter data by Phase, District,
                            Block and Village

                        </p>

                    </div>

                </div>


                <a href="{{ route('admin.dashboard') }}"
                    class="
                        inline-flex
                        h-9
                        items-center
                        gap-2
                        self-start
                        rounded-xl
                        border
                        border-red-100
                        bg-red-50
                        px-4
                        text-xs
                        font-bold
                        text-red-600
                        transition
                        hover:bg-red-100
                        lg:self-auto
                    ">

                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                        <path stroke-linecap="round" stroke-linejoin="round" d="
                                                    M4 4v6h6
                                                    M20 20v-6h-6
                                                    M5.6 18.4A8 8 0 0018.4 5.6
                                                " />

                    </svg>

                    Reset Filters

                </a>

            </div>



            <div
                class="
                    border-t
                    border-slate-100
                    px-5
                    pb-5
                    pt-4
                ">

                <form method="GET" id="dashboardFilter">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12 xl:items-end">

                        {{-- Phase --}}
                        <div class="xl:col-span-2">
                            <label for="phase" class="ai-label">Phase</label>

                            <div class="filter-field">
                                <div class="filter-field-icon">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M10 18h4" />
                                    </svg>
                                </div>

                                <select name="phase" id="phase" class="ai-filter-select ai-filter-select-icon">

                                    <option value="">All Phase</option>

                                    <option value="1" {{ request('phase') == 1 ? 'selected' : '' }}>
                                        Phase 1
                                    </option>

                                    <option value="2" {{ request('phase') == 2 ? 'selected' : '' }}>
                                        Phase 2
                                    </option>

                                    <option value="3" {{ request('phase') == 3 ? 'selected' : '' }}>
                                        Phase 3
                                    </option>

                                    <option value="4" {{ request('phase') == 4 ? 'selected' : '' }}>
                                        Phase 4
                                    </option>

                                </select>
                            </div>
                        </div>


                        {{-- District --}}
                        <div class="xl:col-span-3">
                            <label for="district" class="ai-label">District</label>

                            <div class="filter-field">
                                <div class="filter-field-icon">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 21h18M5 21V7l7-4 7 4v14" />
                                    </svg>
                                </div>

                                <select name="district_id" id="district" class="ai-filter-select ai-filter-select-icon">

                                    <option value="">All District</option>

                                    @foreach ($districts as $district)
                                        <option value="{{ $district->DistrictId }}"
                                            {{ request('district_id') == $district->DistrictId ? 'selected' : '' }}>
                                            {{ $district->DistrictName }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>


                        {{-- Block --}}
                        <div class="xl:col-span-3">
                            <label for="block" class="ai-label">Block</label>

                            <div class="filter-field">
                                <div class="filter-field-icon">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" />
                                    </svg>
                                </div>

                                <select name="block_id" id="block" class="ai-filter-select ai-filter-select-icon">

                                    <option value="">All Block</option>

                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->BlockId }}"
                                            {{ request('block_id') == $block->BlockId ? 'selected' : '' }}>
                                            {{ $block->BlockName }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>


                        {{-- Village --}}
                        <div class="xl:col-span-3">
                            <label for="village" class="ai-label">Village</label>

                            <div class="filter-field">
                                <div class="filter-field-icon">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 10l9-7 9 7M5 10v10h14V10M9 20v-6h6v6" />
                                    </svg>
                                </div>

                                <select name="village_id" id="village" class="ai-filter-select ai-filter-select-icon">

                                    <option value="">All Village</option>

                                    @foreach ($villages as $village)
                                        <option value="{{ $village->VillageId }}"
                                            {{ request('village_id') == $village->VillageId ? 'selected' : '' }}>
                                            {{ $village->VillageName }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>


                        {{-- Apply --}}
                        <div class="xl:col-span-1">
                            <button type="submit" class="ai-apply">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 4h18M6 8h12l-4.5 5v5l-3 2v-7L6 8z" />
                                </svg>

                                Apply
                            </button>
                        </div>

                    </div>

                </form>

            </div>

        </section>



        {{-- ===========================================
             MASTER DATA
        ============================================ --}}

        <div
            class="
                mb-5
                grid
                grid-cols-1
                gap-4
                sm:grid-cols-2
                xl:grid-cols-4
            ">


            {{-- District --}}

            <a href="{{ route('admin.district.report', request()->all()) }}"
                class="
                    master-card
                    master-blue
                    block
                    p-4
                ">

                <div
                    class="
                        relative
                        z-10
                        flex
                        items-center
                        gap-4
                    ">

                    <div
                        class="
                            ai-icon
                            bg-blue-50
                            text-blue-600
                        ">

                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round" d="
                                                        M3 21h18
                                                        M5 21V7l7-4 7 4v14
                                                        M9 9h.01
                                                        M15 9h.01
                                                        M9 13h.01
                                                        M15 13h.01
                                                    " />

                        </svg>

                    </div>


                    <div>

                        <p
                            class="
                                text-[10px]
                                font-extrabold
                                uppercase
                                tracking-[.08em]
                                text-slate-500
                            ">

                            Districts

                        </p>


                        <h3
                            class="
                                mt-1
                                text-[26px]
                                font-black
                                leading-none
                                text-slate-900
                            ">

                            {{ number_format($summary->TotalDistricts) }}

                        </h3>


                        <p
                            class="
                                mt-2
                                text-[10px]
                                font-medium
                                text-slate-500
                            ">

                            Total Districts

                        </p>

                    </div>

                </div>

            </a>



            {{-- Village --}}

            <a href="{{ route('admin.village.report', [
                'phase' => request('phase'),
                'district_id' => request('district_id'),
                'block_id' => request('block_id'),
                'village_id' => request('village_id'),
            ]) }}"
                class="
                    master-card
                    master-green
                    block
                    p-4
                ">

                <div
                    class="
                        relative
                        z-10
                        flex
                        items-center
                        gap-4
                    ">

                    <div
                        class="
                            ai-icon
                            bg-green-50
                            text-green-600
                        ">

                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round" d="
                                                        M3 10l9-7 9 7
                                                        M5 10v10h14V10
                                                        M9 20v-6h6v6
                                                    " />

                        </svg>

                    </div>


                    <div>

                        <p
                            class="
                                text-[10px]
                                font-extrabold
                                uppercase
                                tracking-[.08em]
                                text-slate-500
                            ">

                            Villages

                        </p>


                        <h3
                            class="
                                mt-1
                                text-[26px]
                                font-black
                                leading-none
                                text-slate-900
                            ">

                            {{ number_format($summary->TotalVillages) }}

                        </h3>


                        <p
                            class="
                                mt-2
                                text-[10px]
                                font-medium
                                text-slate-500
                            ">

                            Total Villages

                        </p>

                    </div>

                </div>

            </a>



            {{-- Applicant --}}

            <a href="{{ route('superadmin.applicants.index', [
                'phase' => request('phase'),
                'district_id' => request('district_id'),
                'block_id' => request('block_id'),
                'village_id' => request('village_id'),
            ]) }}"
                class="
                    master-card
                    master-purple
                    block
                    p-4
                ">

                <div
                    class="
                        relative
                        z-10
                        flex
                        items-center
                        gap-4
                    ">

                    <div
                        class="
                            ai-icon
                            bg-violet-50
                            text-violet-600
                        ">

                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round" d="
                                                        M17 20h5v-2a4 4 0 00-4-4h-1
                                                        M9 20H4v-2a4 4 0 014-4h1
                                                        m4-4a4 4 0 10-8 0
                                                        4 4 0 008 0z
                                                    " />

                        </svg>

                    </div>


                    <div>

                        <p
                            class="
                                text-[10px]
                                font-extrabold
                                uppercase
                                tracking-[.08em]
                                text-slate-500
                            ">

                            Applicants

                        </p>


                        <h3
                            class="
                                mt-1
                                text-[26px]
                                font-black
                                leading-none
                                text-slate-900
                            ">

                            {{ number_format($summary->RegisteredBeneficiaries) }}

                        </h3>


                        <p
                            class="
                                mt-2
                                text-[10px]
                                font-medium
                                text-slate-500
                            ">

                            Total Applicants

                        </p>

                    </div>

                </div>

            </a>



            {{-- Allotted --}}

            <a href="{{ route('admin.allotment.report', [
                'phase' => request('phase'),
                'district_id' => request('district_id'),
                'block_id' => request('block_id'),
                'village_id' => request('village_id'),
            ]) }}"
                class="
                    master-card
                    master-orange
                    block
                    p-4
                ">

                <div
                    class="
                        relative
                        z-10
                        flex
                        items-center
                        gap-4
                    ">

                    <div
                        class="
                            ai-icon
                            bg-orange-50
                            text-orange-600
                        ">

                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round" d="
                                                        M8 17v-5h8v5
                                                        M3 10l9-7 9 7
                                                        M5 10v10h14V10
                                                    " />

                        </svg>

                    </div>


                    <div>

                        <p
                            class="
                                text-[10px]
                                font-extrabold
                                uppercase
                                tracking-[.08em]
                                text-slate-500
                            ">

                            Allotted

                        </p>


                        <h3
                            class="
                                mt-1
                                text-[26px]
                                font-black
                                leading-none
                                text-slate-900
                            ">

                            {{ number_format($summary->AllottedBeneficiaries) }}

                        </h3>


                        <p
                            class="
                                mt-2
                                text-[10px]
                                font-medium
                                text-slate-500
                            ">

                            Total Allotted

                        </p>

                    </div>

                </div>

            </a>

        </div>



        {{-- ===========================================
             ALLOTMENT
        ============================================ --}}

        <section class="ai-panel mb-5">

            <div
                class="
                    flex
                    items-center
                    justify-between
                    border-b
                    border-slate-100
                    px-5
                    py-4
                ">

                <div>

                    <h2
                        class="
                            text-[16px]
                            font-extrabold
                            text-slate-900
                        ">

                        Allotment Status

                    </h2>

                    <p
                        class="
                            mt-0.5
                            text-[11px]
                            text-slate-500
                        ">

                        Status of Allotted Beneficiaries

                    </p>

                </div>


                <span
                    class="
                        rounded-full
                        bg-blue-50
                        px-3
                        py-1.5
                        text-[10px]
                        font-extrabold
                        text-blue-700
                    ">

                    {{ number_format($summary->GrossTotal) }}
                    Records

                </span>

            </div>



            <div
                class="
                    grid
                    grid-cols-1
                    gap-3
                    p-4
                    sm:grid-cols-2
                    lg:grid-cols-3
                    2xl:grid-cols-6
                ">


                {{-- Total --}}

                <a href="{{ route('admin.allotment.report', request()->only(['phase', 'district_id', 'block_id', 'village_id'])) }}"
                    class="
                        status-card
                        status-total
                    ">
                    <span class="card-ref-icon card-ref-blue" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M8 7h12M8 12h12M8 17h12" />
                            <path d="M4 7h.01M4 12h.01M4 17h.01" />
                        </svg>
                    </span>

                    <p
                        class="
                            text-[10px]
                            font-extrabold
                            uppercase
                            text-slate-500
                        ">

                        Total

                    </p>


                    <h3
                        class="
                            mt-1
                            text-[23px]
                            font-black
                            text-slate-900
                        ">

                        {{ number_format($summary->GrossTotal) }}

                    </h3>


                    <p
                        class="
                            mt-2
                            text-[10px]
                            text-slate-500
                        ">

                        All Records

                    </p>

                </a>



                {{-- Paid --}}

                <a href="{{ route(
                    'admin.allotment.report',
                    array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                        'status' => 'approved_paid',
                    ]),
                ) }}"
                    class="
                        status-card
                        status-paid
                    ">
                    <span class="card-ref-icon card-ref-green" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="8" />
                            <path d="m8.5 12 2.2 2.2 4.8-5" />
                        </svg>
                    </span>

                    <p
                        class="
                            text-[10px]
                            font-extrabold
                            uppercase
                            text-slate-500
                        ">

                        Approved & Paid

                    </p>


                    <h3
                        class="
                            mt-1
                            text-[23px]
                            font-black
                            text-slate-900
                        ">

                        {{ number_format($summary->ApprovedPaid) }}

                    </h3>


                    <p
                        class="
                            mt-2
                            text-[10px]
                            font-bold
                            text-emerald-600
                        ">

                        {{ number_format($approvedPaidPct, 2) }}%
                        of total

                    </p>

                </a>



                {{-- Unpaid --}}

                <a href="{{ route(
                    'admin.allotment.report',
                    array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                        'status' => 'approved_unpaid',
                    ]),
                ) }}"
                    class="
                        status-card
                        status-unpaid
                    ">
                    <span class="card-ref-icon card-ref-amber" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <rect x="4" y="6" width="16" height="12" rx="2" />
                            <path d="M16 12h.01M7 10h5M7 14h4" />
                        </svg>
                    </span>

                    <p
                        class="
                            text-[10px]
                            font-extrabold
                            uppercase
                            text-slate-500
                        ">

                        Approved & Unpaid

                    </p>


                    <h3
                        class="
                            mt-1
                            text-[23px]
                            font-black
                            text-slate-900
                        ">

                        {{ number_format($summary->ApprovedUnpaid) }}

                    </h3>


                    <p
                        class="
                            mt-2
                            text-[10px]
                            font-bold
                            text-amber-600
                        ">

                        {{ number_format($approvedUnpaidPct, 2) }}%
                        of total

                    </p>

                </a>



                {{-- Pending --}}

                <a href="{{ route(
                    'admin.allotment.report',
                    array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                        'status' => 'pending',
                    ]),
                ) }}"
                    class="
                        status-card
                        status-pending
                    ">
                    <span class="card-ref-icon card-ref-orange" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="8" />
                            <path d="M12 8v4l2.5 2" />
                        </svg>
                    </span>

                    <p
                        class="
                            text-[10px]
                            font-extrabold
                            uppercase
                            text-slate-500
                        ">

                        Yet to be Approved

                    </p>


                    <h3
                        class="
                            mt-1
                            text-[23px]
                            font-black
                            text-slate-900
                        ">

                        {{ number_format($summary->PendingApprovalPayment) }}

                    </h3>


                    <p
                        class="
                            mt-2
                            text-[10px]
                            font-bold
                            text-orange-600
                        ">

                        {{ number_format($pendingPct, 2) }}%
                        of total

                    </p>

                </a>



                {{-- Rejected --}}

                <a href="{{ route(
                    'admin.allotment.report',
                    array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                        'status' => 'rejected',
                    ]),
                ) }}"
                    class="
                        status-card
                        status-rejected
                    ">
                    <span class="card-ref-icon card-ref-red" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="8" />
                            <path d="m9 9 6 6M15 9l-6 6" />
                        </svg>
                    </span>

                    <p
                        class="
                            text-[10px]
                            font-extrabold
                            uppercase
                            text-slate-500
                        ">

                        Rejected

                    </p>


                    <h3
                        class="
                            mt-1
                            text-[23px]
                            font-black
                            text-slate-900
                        ">

                        {{ number_format($summary->Rejected) }}

                    </h3>


                    <p
                        class="
                            mt-2
                            text-[10px]
                            font-bold
                            text-red-600
                        ">

                        {{ number_format($rejectedPct, 2) }}%
                        of total

                    </p>

                </a>



                {{-- Cancelled --}}

                <a href="{{ route(
                    'admin.allotment.report',
                    array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), [
                        'status' => 'cancelled',
                    ]),
                ) }}"
                    class="
                        status-card
                        status-cancelled
                    ">
                    <span class="card-ref-icon card-ref-slate" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="8" />
                            <path d="m8 16 8-8" />
                        </svg>
                    </span>

                    <p
                        class="
                            text-[10px]
                            font-extrabold
                            uppercase
                            text-slate-500
                        ">

                        Cancelled

                    </p>


                    <h3
                        class="
                            mt-1
                            text-[23px]
                            font-black
                            text-slate-900
                        ">

                        {{ number_format($summary->AllotmentCancelled) }}

                    </h3>


                    <p
                        class="
                            mt-2
                            text-[10px]
                            font-bold
                            text-slate-500
                        ">

                        {{ number_format($cancelledPct, 2) }}%
                        of total

                    </p>

                </a>

            </div>

        </section>



        {{-- ===========================================
             REGISTRATION + CHART
        ============================================ --}}

        <div
            class="
                mb-5
                grid
                grid-cols-1
                gap-4
                xl:grid-cols-12
            ">


            {{-- Registration Cards --}}

            <section class="
                    ai-panel
                    xl:col-span-7
                ">

                <div
                    class="
                        border-b
                        border-slate-100
                        px-5
                        py-4
                    ">

                    <h2
                        class="
                            text-[16px]
                            font-extrabold
                            text-slate-900
                        ">

                        Registration Statistics

                    </h2>


                    <p
                        class="
                            mt-0.5
                            text-[11px]
                            text-slate-500
                        ">

                        Registry Matching Report

                    </p>

                </div>


                <div
                    class="
                        grid
                        grid-cols-1
                        gap-3
                        p-4
                        md:grid-cols-3
                    ">


                    {{-- Total Eligible --}}

                    <a href="{{ route('admin.registration', request()->only(['phase', 'district_id', 'block_id', 'village_id'])) }}"
                        class="
                            master-card
                            master-purple
                            block
                            min-h-[130px]
                            p-4
                        
                            registration-icon-card">
                        <span class="card-ref-icon card-ref-purple" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M7 3h7l4 4v14H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                                <path d="M14 3v5h5" />
                                <path d="M9 12h6M9 16h6" />
                            </svg>
                        </span>

                        <p
                            class="
                                text-[10px]
                                font-extrabold
                                uppercase
                                text-slate-500
                            ">

                            Registry to be done

                        </p>


                        <h3
                            class="
                                mt-2
                                text-[25px]
                                font-black
                                text-slate-900
                            ">

                            {{ number_format($registration->TotalRegistration) }}

                        </h3>


                        <p
                            class="
                                mt-2
                                text-[10px]
                                font-medium
                                text-slate-500
                            ">

                            Approved & Paid

                        </p>

                    </a>



                    {{-- Matched --}}

                    <a href="{{ route('superadmin.registry_done.index') }}"
                        class="
        master-card
        master-green
        block
        min-h-[130px]
        p-4
        registration-icon-card">

                        <span class="card-ref-icon card-ref-green" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="8" />
                                <path d="m8.5 12 2.2 2.2 4.8-5" />
                            </svg>
                        </span>

                        <p
                            class="
            text-[10px]
            font-extrabold
            uppercase
            text-slate-500
        ">

                            Registry done

                        </p>

                        <h3
                            class="
            mt-2
            text-[25px]
            font-black
            text-slate-900
        ">

                            {{ number_format($registration->Matched) }}

                        </h3>

                        <p
                            class="
            mt-2
            text-[10px]
            font-extrabold
            text-emerald-600
        ">

                            {{ number_format($matchedPct, 2) }}%
                            of total

                        </p>

                    </a>



                    {{-- Unmatched --}}

                    <a href="#"
                        class="
                            master-card
                            block
                            min-h-[130px]
                            border-red-100
                            bg-gradient-to-br
                            from-white
                            to-red-50
                            p-4
                        
                            registration-icon-card">
                        <span class="card-ref-icon card-ref-red" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="8" />
                                <path d="m9 9 6 6M15 9l-6 6" />
                            </svg>
                        </span>

                        <p
                            class="
                                text-[10px]
                                font-extrabold
                                uppercase
                                text-slate-500
                            ">

                            Registry yet to be done

                        </p>


                        <h3
                            class="
                                mt-2
                                text-[25px]
                                font-black
                                text-slate-900
                            ">

                            {{ number_format($registration->UnMatched) }}

                        </h3>


                        <p
                            class="
                                mt-2
                                text-[10px]
                                font-extrabold
                                text-red-600
                            ">

                            {{ number_format($unmatchedPct, 2) }}%
                            of total

                        </p>

                    </a>

                </div>

            </section>



            {{-- Registry Donut --}}

            <section class="
                    ai-panel
                    xl:col-span-5
                ">

                <div
                    class="
                        border-b
                        border-slate-100
                        px-5
                        py-4
                    ">

                    <h2
                        class="
                            text-[16px]
                            font-extrabold
                            text-slate-900
                        ">

                        Registration Overview

                    </h2>


                    <p
                        class="
                            mt-0.5
                            text-[11px]
                            text-slate-500
                        ">

                        Registration done vs pending

                    </p>

                </div>


                <div
                    class="
                        flex
                        min-h-[205px]
                        flex-col
                        items-center
                        justify-center
                        gap-7
                        p-5
                        sm:flex-row
                    ">


                    <div class="registry-donut"
                        style="
                            --matched:
                            {{ round($matchedPct, 2) }};
                        ">

                        <div class="
                                registry-donut-value
                            ">

                            <p
                                class="
                                    text-[19px]
                                    font-black
                                    text-slate-900
                                ">

                                {{ number_format($registration->TotalRegistration) }}

                            </p>


                            <p
                                class="
                                    text-[9px]
                                    font-bold
                                    uppercase
                                    text-slate-500
                                ">

                                Total

                            </p>

                        </div>

                    </div>



                    <div class="min-w-[180px] space-y-4">

                        <div
                            class="
                                flex
                                items-center
                                justify-between
                                gap-5
                            ">

                            <div
                                class="
                                    flex
                                    items-center
                                    gap-2
                                ">

                                <span
                                    class="
                                        h-2.5
                                        w-2.5
                                        rounded-full
                                        bg-emerald-500
                                    ">
                                </span>

                                <span
                                    class="
                                        text-xs
                                        font-bold
                                        text-slate-600
                                    ">

                                    Done

                                </span>

                            </div>


                            <span
                                class="
                                    text-xs
                                    font-black
                                    text-slate-900
                                ">

                                {{ number_format($registration->Matched) }}

                            </span>

                        </div>



                        <div
                            class="
                                flex
                                items-center
                                justify-between
                                gap-5
                            ">

                            <div
                                class="
                                    flex
                                    items-center
                                    gap-2
                                ">

                                <span
                                    class="
                                        h-2.5
                                        w-2.5
                                        rounded-full
                                        bg-red-500
                                    ">
                                </span>

                                <span
                                    class="
                                        text-xs
                                        font-bold
                                        text-slate-600
                                    ">

                                    Pending

                                </span>

                            </div>


                            <span
                                class="
                                    text-xs
                                    font-black
                                    text-slate-900
                                ">

                                {{ number_format($registration->UnMatched) }}

                            </span>

                        </div>

                    </div>

                </div>

            </section>

        </div>



        {{-- ===========================================
             POSSESSION
        ============================================ --}}

        {{-- ===========================================
             PHYSICAL POSSESSION
        ============================================ --}}

        <section class="ai-panel mb-5">

            <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                    <span class="material-symbols-outlined text-[21px]">
                        key
                    </span>
                </div>

                <div>
                    <h2 class="text-[16px] font-extrabold text-slate-900">
                        Possession
                    </h2>

                    <p class="mt-0.5 text-[11px] text-slate-500">
                        Registered beneficiaries eligible for possession
                    </p>
                </div>

            </div>


            <div
                class="grid grid-cols-1 gap-4 bg-gradient-to-r from-slate-50/70 via-white to-slate-50/70 p-4 md:grid-cols-3">

                {{-- Possession to be given --}}
                <a href="{{ route('admin.possession.list', array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['filter' => 'all'])) }}"
                    class="master-card master-purple block min-h-[118px] p-4">

                    <div class="relative z-10 flex items-center justify-between gap-3">

                        <div>
                            <p class="text-[10px] font-extrabold uppercase text-slate-500">
                                Possession to be given
                            </p>

                            <h3 class="mt-2 text-[27px] font-black text-violet-700">
                                {{ number_format($possession->TotalEligible ?? 0) }}
                            </h3>

                            <p class="mt-1 text-[9px] font-semibold text-slate-500">
                                Unique registry done beneficiaries
                            </p>
                        </div>

                        <div class="ai-icon bg-violet-50 text-violet-600">
                            <span class="material-symbols-outlined">
                                assignment_turned_in
                            </span>
                        </div>

                    </div>

                </a>


                {{-- Possession Given --}}
                <a href="{{ route('admin.possession.list', array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['filter' => 'verified'])) }}"
                    class="master-card master-green block min-h-[118px] p-4">

                    <div class="relative z-10 flex items-center justify-between gap-3">

                        <div>
                            <p class="text-[10px] font-extrabold uppercase text-slate-500">
                                Possession Given
                            </p>

                            <h3 class="mt-2 text-[27px] font-black text-emerald-700">
                                {{ number_format($possession->Given ?? 0) }}
                            </h3>

                            <p class="mt-1 text-[9px] font-semibold text-slate-500">
                                Final verified
                            </p>
                        </div>

                        <div class="ai-icon bg-emerald-50 text-emerald-600">
                            <span class="material-symbols-outlined">
                                verified
                            </span>
                        </div>

                    </div>

                </a>


                {{-- Possession Pending --}}
                <a href="{{ route('admin.possession.list', array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['filter' => 'schedule_pending'])) }}"
                    class="master-card master-orange block min-h-[118px] p-4">

                    <div class="relative z-10 flex items-center justify-between gap-3">

                        <div>
                            <p class="text-[10px] font-extrabold uppercase text-slate-500">
                                Possession Pending
                            </p>

                            <h3 class="mt-2 text-[27px] font-black text-amber-700">
                                {{ number_format($possession->Pending ?? 0) }}
                            </h3>

                            <p class="mt-1 text-[9px] font-semibold text-slate-500">
                                Eligible minus possession given
                            </p>
                        </div>

                        <div class="ai-icon bg-amber-50 text-amber-600">
                            <span class="material-symbols-outlined">
                                hourglass_empty
                            </span>
                        </div>

                    </div>

                </a>

            </div>

        </section>

    </main>



    {{-- ===========================================
         LOADER
    ============================================ --}}

    <div id="dashboardLoader"
        class="
            fixed
            inset-0
            z-50
            hidden
            bg-slate-950/10
            backdrop-blur-[2px]
        ">

        <div
            class="
                flex
                h-full
                items-center
                justify-center
            ">

            <div
                class="
                    rounded-2xl
                    border
                    border-white
                    bg-white
                    px-7
                    py-5
                    shadow-2xl
                ">

                <div
                    class="
                        flex
                        items-center
                        gap-4
                    ">

                    <div
                        class="
                            h-9
                            w-9
                            animate-spin
                            rounded-full
                            border-4
                            border-blue-100
                            border-t-blue-600
                        ">
                    </div>


                    <div>

                        <p
                            class="
                                text-sm
                                font-extrabold
                                text-slate-900
                            ">

                            Loading Dashboard...

                        </p>


                        <p
                            class="
                                mt-0.5
                                text-[10px]
                                font-medium
                                text-slate-500
                            ">

                            Please wait while data is refreshed.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
