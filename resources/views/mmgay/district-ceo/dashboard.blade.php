@extends('layouts.mmgayCEOAuth')

@section('title', 'MMGAY District CEO Dashboard')

@section('content')

    @php
        $dashboardApplicantParams = array_filter(
            [
                'phase' => $phase ?? 'all',
                'village_id' => $villageId ?? null,
            ],
            static fn($value) => $value !== null && $value !== '',
        );

        $applicantReportUrl = route('district.dashboard.applicants', $dashboardApplicantParams);

        $possessionParams = array_filter(
            [
                'phase' => $phase ?? 'all',
                'village_id' => $villageId ?? null,
            ],
            static fn($value) => $value !== null && $value !== '',
        );

        $allottedTotal = (int) ($totals['totalAllotment'] ?? 0);
        $paidPercent = $allottedTotal > 0 ? round((($totals['totalPaid'] ?? 0) / $allottedTotal) * 100, 2) : 0;
        $unpaidPercent =
            $allottedTotal > 0 ? round((($totals['totalApprovedUnpaid'] ?? 0) / $allottedTotal) * 100, 2) : 0;
        $pendingPercent = $allottedTotal > 0 ? round((($totals['totalPending'] ?? 0) / $allottedTotal) * 100, 2) : 0;
        $rejectedPercent = $allottedTotal > 0 ? round((($totals['totalRejected'] ?? 0) / $allottedTotal) * 100, 2) : 0;
        $cancelledPercent =
            $allottedTotal > 0 ? round((($totals['totalCancelled'] ?? 0) / $allottedTotal) * 100, 2) : 0;

        $registryTotal = (int) ($totals['totalRegistryAllotted'] ?? 0);
        $registryDonePercent =
            $registryTotal > 0 ? round((($totals['totalRegistryMatched'] ?? 0) / $registryTotal) * 100, 2) : 0;
        $registryPendingPercent =
            $registryTotal > 0 ? round((($totals['totalRegistryUnmatched'] ?? 0) / $registryTotal) * 100, 2) : 0;

        $possessionTotal = (int) ($totals['totalRegisteredBeneficiaries'] ?? 0);
        $possessionGivenPercent =
            $possessionTotal > 0 ? round((($totals['totalPossessionGiven'] ?? 0) / $possessionTotal) * 100, 2) : 0;
        $possessionPendingPercent =
            $possessionTotal > 0 ? round((($totals['totalPossessionPending'] ?? 0) / $possessionTotal) * 100, 2) : 0;
    @endphp

    <style>
        .ceo-shell {
            background:
                radial-gradient(circle at 12% 0%, rgba(37, 99, 235, .055), transparent 23rem),
                radial-gradient(circle at 95% 15%, rgba(79, 70, 229, .045), transparent 26rem),
                #f8fafc;
        }

        .ceo-panel {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: rgba(255, 255, 255, .97);
            box-shadow:
                0 1px 2px rgba(15, 23, 42, .025),
                0 8px 28px rgba(15, 23, 42, .035);
        }

        .ceo-section-title {
            color: #0f172a;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .ceo-muted {
            color: #64748b;
        }

        .ceo-filter-select {
            width: 100%;
            height: 42px;
            border: 1px solid #d7e0ec;
            border-radius: 10px;
            background: #fff;
            padding: 0 38px 0 42px;
            color: #0f2747;
            font-size: 12px;
            font-weight: 700;
            outline: none;
            transition: .2s ease;
        }

        .ceo-filter-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .11);
        }

        .ceo-filter-wrap {
            position: relative;
        }

        .ceo-filter-icon {
            position: absolute;
            left: 10px;
            bottom: 8px;
            width: 26px;
            height: 26px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #eff6ff;
            color: #4f46e5;
            pointer-events: none;
        }

        .ceo-stat-card {
            position: relative;
            overflow: hidden;
            min-height: 92px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            padding: 14px 14px 13px;
            transition: .2s ease;
        }

        .ceo-stat-card:hover {
            transform: translateY(-2px);
            border-color: #c7d2fe;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .07);
        }

        .ceo-stat-card::after {
            content: "";
            position: absolute;
            right: -30px;
            bottom: -48px;
            width: 112px;
            height: 82px;
            border-radius: 999px 999px 0 0;
            opacity: .58;
        }

        .ceo-card-blue::after {
            background: #e8f1ff;
        }

        .ceo-card-green::after {
            background: #e9fbf1;
        }

        .ceo-card-purple::after {
            background: #f3ebff;
        }

        .ceo-card-orange::after {
            background: #fff0e4;
        }

        .ceo-icon-box {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            flex: 0 0 auto;
        }

        .ceo-status-card {
            min-height: 96px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            background: #fff;
            transition: .2s ease;
        }

        .ceo-status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 9px 22px rgba(15, 23, 42, .06);
        }

        .ceo-reg-card {
            min-height: 112px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            background: #fff;
            transition: .2s ease;
        }

        .ceo-reg-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 9px 22px rgba(15, 23, 42, .06);
        }

        .ceo-progress-track {
            height: 5px;
            overflow: hidden;
            border-radius: 999px;
            background: #e9eef6;
        }

        .ceo-progress-value {
            height: 100%;
            border-radius: inherit;
        }

        .ceo-donut {
            width: 126px;
            height: 126px;
            position: relative;
            border-radius: 50%;
            background:
                conic-gradient(#16a34a 0 {{ $registryDonePercent }}%,
                    #fb923c {{ $registryDonePercent }}% 100%);
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .04);
        }

        .ceo-donut::after {
            content: "";
            position: absolute;
            inset: 24px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 0 0 1px rgba(226, 232, 240, .7);
        }

        .ceo-donut-center {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: grid;
            place-content: center;
            text-align: center;
        }

        .ceo-legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex: 0 0 auto;
        }

        @media (max-width: 1280px) {
            .ceo-main-number {
                font-size: 24px !important;
            }
        }
    </style>

    <main class="ceo-shell mt-[68px] min-h-screen p-4 lg:ml-[230px] lg:w-[calc(100%-230px)] lg:p-5">

        {{-- ================================================================ --}}
        {{-- Dashboard Filters --}}
        {{-- ================================================================ --}}
        <section class="ceo-panel overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-3.5">
                <h2 class="ceo-section-title text-[15px]">
                    Dashboard Filters
                </h2>
                <p class="ceo-muted mt-0.5 text-[10px] font-medium">
                    Filter statistics by phase and village
                </p>
            </div>

            <div class="px-5 py-4">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">

                    <div class="ceo-filter-wrap md:col-span-3">
                        <label for="phase_filter"
                            class="mb-1.5 block text-[9px] font-extrabold uppercase tracking-[.08em] text-slate-600">
                            Phase
                        </label>

                        <span class="ceo-filter-icon">
                            <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                        </span>

                        <select id="phase_filter" class="ceo-filter-select">
                            <option value="all" {{ $phase === 'all' ? 'selected' : '' }}>All Phases</option>
                            <option value="1" {{ (string) $phase === '1' ? 'selected' : '' }}>Phase 1</option>
                            <option value="2" {{ (string) $phase === '2' ? 'selected' : '' }}>Phase 2</option>
                            <option value="3" {{ (string) $phase === '3' ? 'selected' : '' }}>Phase 3</option>
                        </select>
                    </div>

                    <div class="ceo-filter-wrap md:col-span-6">
                        <label for="village_filter"
                            class="mb-1.5 block text-[9px] font-extrabold uppercase tracking-[.08em] text-slate-600">
                            Village
                        </label>

                        <span class="ceo-filter-icon">
                            <span class="material-symbols-outlined text-[16px]">holiday_village</span>
                        </span>

                        <select id="village_filter" class="ceo-filter-select">
                            <option value="">All Villages</option>
                            @foreach ($villages as $village)
                                <option value="{{ $village->VillageId }}"
                                    {{ (string) $villageId === (string) $village->VillageId ? 'selected' : '' }}>
                                    {{ $village->VillageName }}
                                    @if ($phase === 'all')
                                        (Phase {{ $village->phase }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 md:col-span-3">
                        <button type="button" id="applyFilters"
                            class="inline-flex h-[42px] flex-1 items-center justify-center
           gap-2 rounded-[10px] bg-gradient-to-r from-blue-600
           to-indigo-600 px-4 text-xs font-extrabold text-white
           shadow-[0_7px_16px_rgba(37,99,235,.18)]
           transition hover:-translate-y-0.5
           disabled:cursor-not-allowed disabled:opacity-70">
                            <span class="material-symbols-outlined apply-filter-icon text-[17px]">
                                filter_alt
                            </span>

                            <svg class="apply-filter-spinner hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>

                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>

                            <span class="apply-filter-text">
                                Apply
                            </span>
                        </button>

                        <button type="button" id="resetFilters"
                            class="inline-flex h-[42px] items-center justify-center gap-1.5 rounded-[10px] border border-red-200 bg-white px-4 text-xs font-extrabold text-red-500 transition hover:bg-red-50">
                            <span class="material-symbols-outlined text-[17px]">restart_alt</span>
                            Reset
                        </button>
                    </div>

                </div>
            </div>
        </section>


        {{-- ================================================================ --}}
        {{-- Master Overview --}}
        {{-- ================================================================ --}}
        <section class="ceo-panel mt-4 overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-3.5">
                <h2 class="ceo-section-title text-[15px]">Master Overview</h2>
                <p class="ceo-muted mt-0.5 text-[10px] font-medium">Overall project statistics</p>
            </div>

            <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-4">

                <a id="villagesReportLink"
                    href="{{ route('district.dashboard.report', [
                        'type' => 'villages',
                        'phase' => $phase ?? 'all',
                        'village_id' => $villageId ?? null,
                    ]) }}"
                    class="ceo-stat-card ceo-card-blue group">
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="ceo-icon-box bg-blue-50 text-blue-600">
                            <span class="material-symbols-outlined text-[24px]">holiday_village</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-extrabold uppercase tracking-[.07em] text-slate-500">Villages</p>
                            <h3 id="totalVillages"
                                class="ceo-main-number mt-0.5 text-[27px] font-black leading-none text-slate-950">
                                {{ number_format($totals['totalVillages'] ?? 0) }}
                            </h3>
                            <p class="mt-1.5 text-[9px] font-semibold text-slate-500">Total Villages</p>
                        </div>
                    </div>
                </a>

                <div class="ceo-stat-card ceo-card-green">
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="ceo-icon-box bg-emerald-50 text-emerald-600">
                            <span class="material-symbols-outlined text-[24px]">grid_view</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-extrabold uppercase tracking-[.07em] text-slate-500">Plots</p>
                            <h3 id="totalPlots"
                                class="ceo-main-number mt-0.5 text-[27px] font-black leading-none text-slate-950">
                                {{ number_format($totals['totalPlots'] ?? 0) }}
                            </h3>
                            <p class="mt-1.5 text-[9px] font-semibold text-slate-500">Total Plots</p>
                        </div>
                    </div>
                </div>

                <a id="applicantsReportLink" data-status=""
                    href="{{ route('district.dashboard.applicants', $dashboardApplicantParams) }}"
                    class="applicant-report-link ceo-stat-card ceo-card-purple group">
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="ceo-icon-box bg-violet-50 text-violet-600">
                            <span class="material-symbols-outlined text-[24px]">groups</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-extrabold uppercase tracking-[.07em] text-slate-500">Applicants</p>
                            <h3 id="totalApplicants"
                                class="ceo-main-number mt-0.5 text-[27px] font-black leading-none text-slate-950">
                                {{ number_format($totals['totalApplicants'] ?? 0) }}
                            </h3>
                            <p class="mt-1.5 text-[9px] font-semibold text-slate-500">Total Applicants</p>
                        </div>
                    </div>
                </a>

                <a id="allottedApplicantsLink" data-status="allotted"
                    href="{{ route('district.dashboard.applicants', array_merge($dashboardApplicantParams, ['status' => 'allotted'])) }}"
                    class="applicant-report-link ceo-stat-card ceo-card-orange group">
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="ceo-icon-box bg-orange-50 text-orange-500">
                            <span class="material-symbols-outlined text-[24px]">real_estate_agent</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-extrabold uppercase tracking-[.07em] text-slate-500">Allotment</p>
                            <h3 id="totalAllotment"
                                class="ceo-main-number mt-0.5 text-[27px] font-black leading-none text-slate-950">
                                {{ number_format($totals['totalAllotment'] ?? 0) }}
                            </h3>
                            <p class="mt-1.5 text-[9px] font-semibold text-slate-500">Total Allotments</p>
                        </div>
                    </div>
                </a>

            </div>
        </section>


        {{-- ================================================================ --}}
        {{-- Allotment Status --}}
        {{-- ================================================================ --}}
        <section class="ceo-panel mt-4 overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-3.5">
                <h2 class="ceo-section-title text-[15px]">Allotment Status</h2>
                <p class="ceo-muted mt-0.5 text-[10px] font-medium">Current beneficiary approval and payment status</p>
            </div>

            <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-5">

                <a id="approvedPaidReportLink" data-status="approved_paid"
                    href="{{ route('district.dashboard.applicants', array_merge($dashboardApplicantParams, ['status' => 'approved_paid'])) }}"
                    class="applicant-report-link ceo-status-card border-emerald-100 bg-emerald-50/30">
                    <div class="flex h-full items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500">Approved & Paid</p>
                            <h3 id="approvedPaid" class="mt-1 text-[24px] font-black leading-none text-emerald-700">
                                {{ number_format($totals['totalPaid'] ?? 0) }}
                            </h3>
                            <p id="approvedPaidPercent"
                                class="status-percent-text mt-2 text-[9px] font-extrabold text-emerald-600">
                                {{ number_format($paidPercent, 2) }}% of total</p>
                        </div>
                        <div class="ceo-icon-box !h-9 !w-9 bg-emerald-100 text-emerald-600">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        </div>
                    </div>
                </a>

                <a id="approvedUnpaidReportLink" data-status="approved_unpaid"
                    href="{{ route('district.dashboard.applicants', array_merge($dashboardApplicantParams, ['status' => 'approved_unpaid'])) }}"
                    class="applicant-report-link ceo-status-card border-cyan-100 bg-cyan-50/30">
                    <div class="flex h-full items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500">Approved & Unpaid</p>
                            <h3 id="approvedUnpaid" class="mt-1 text-[24px] font-black leading-none text-cyan-700">
                                {{ number_format($totals['totalApprovedUnpaid'] ?? 0) }}
                            </h3>
                            <p id="approvedUnpaidPercent"
                                class="status-percent-text mt-2 text-[9px] font-extrabold text-cyan-600">
                                {{ number_format($unpaidPercent, 2) }}% of total</p>
                        </div>
                        <div class="ceo-icon-box !h-9 !w-9 bg-cyan-100 text-cyan-600">
                            <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                        </div>
                    </div>
                </a>

                <a id="pendingApplicantsReportLink" data-status="pending"
                    href="{{ route('district.dashboard.applicants', array_merge($dashboardApplicantParams, ['status' => 'pending'])) }}"
                    class="applicant-report-link ceo-status-card border-amber-100 bg-amber-50/40">
                    <div class="flex h-full items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500">Yet to be Approved</p>
                            <h3 id="yetToBeApproved" class="mt-1 text-[24px] font-black leading-none text-amber-700">
                                {{ number_format($totals['totalPending'] ?? 0) }}
                            </h3>
                            <p id="yetToBeApprovedPercent"
                                class="status-percent-text mt-2 text-[9px] font-extrabold text-amber-600">
                                {{ number_format($pendingPercent, 2) }}% of total</p>
                        </div>
                        <div class="ceo-icon-box !h-9 !w-9 bg-amber-100 text-amber-600">
                            <span class="material-symbols-outlined text-[20px]">hourglass_top</span>
                        </div>
                    </div>
                </a>

                <a id="rejectedApplicantsReportLink" data-status="rejected"
                    href="{{ route('district.dashboard.applicants', array_merge($dashboardApplicantParams, ['status' => 'rejected'])) }}"
                    class="applicant-report-link ceo-status-card border-rose-100 bg-rose-50/40">
                    <div class="flex h-full items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500">Rejected</p>
                            <h3 id="rejected" class="mt-1 text-[24px] font-black leading-none text-rose-700">
                                {{ number_format($totals['totalRejected'] ?? 0) }}
                            </h3>
                            <p id="rejectedPercent"
                                class="status-percent-text mt-2 text-[9px] font-extrabold text-rose-600">
                                {{ number_format($rejectedPercent, 2) }}% of total</p>
                        </div>
                        <div class="ceo-icon-box !h-9 !w-9 bg-rose-100 text-rose-600">
                            <span class="material-symbols-outlined text-[20px]">cancel</span>
                        </div>
                    </div>
                </a>

                <a id="cancelledApplicantsReportLink" data-status="cancelled"
                    href="{{ route('district.dashboard.applicants', array_merge($dashboardApplicantParams, ['status' => 'cancelled'])) }}"
                    class="applicant-report-link ceo-status-card bg-slate-50">
                    <div class="flex h-full items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500">Cancelled</p>
                            <h3 id="cancelled" class="mt-1 text-[24px] font-black leading-none text-slate-700">
                                {{ number_format($totals['totalCancelled'] ?? 0) }}
                            </h3>
                            <p id="cancelledPercent"
                                class="status-percent-text mt-2 text-[9px] font-extrabold text-slate-500">
                                {{ number_format($cancelledPercent, 2) }}% of total</p>
                        </div>
                        <div class="ceo-icon-box !h-9 !w-9 bg-slate-200 text-slate-600">
                            <span class="material-symbols-outlined text-[20px]">block</span>
                        </div>
                    </div>
                </a>

            </div>
        </section>


        {{-- ================================================================ --}}
        {{-- Registration + Registry Overview --}}
        {{-- ================================================================ --}}
        <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-12">

            {{-- ================================================================ --}}
            {{-- Registration Statistics --}}
            {{-- ================================================================ --}}
            <section class="ceo-panel overflow-hidden xl:col-span-7">

                <div class="border-b border-slate-100 px-5 py-3.5">
                    <h2 class="ceo-section-title text-[15px]">
                        Registration Statistics
                    </h2>

                    <p class="ceo-muted mt-0.5 text-[10px] font-medium">
                        Registry matching for allotted beneficiaries
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 p-4 md:grid-cols-3">

                    {{-- Eligible --}}
                    <div class="ceo-reg-card border-blue-100">
                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-[9px] font-bold text-slate-500">
                                    Registry To Be Done
                                </p>

                                <h3 id="registrationAllotted"
                                    class="mt-2 text-[25px] font-black leading-none text-blue-700">
                                    {{ number_format($totals['totalRegistryAllotted'] ?? 0) }}
                                </h3>

                                <p class="mt-2 text-[9px] font-extrabold text-slate-500">
                                    Approved & Paid
                                </p>
                            </div>

                            <div class="ceo-icon-box bg-blue-50 text-blue-600">
                                <span class="material-symbols-outlined text-[22px]">
                                    home_work
                                </span>
                            </div>

                        </div>
                    </div>


                    {{-- Done --}}
                    <div class="ceo-reg-card border-emerald-100">
                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-[9px] font-bold text-slate-500">
                                    Registry Done
                                </p>

                                <h3 id="registryMatched"
                                    class="mt-2 text-[25px] font-black leading-none text-emerald-700">
                                    {{ number_format($totals['totalRegistryMatched'] ?? 0) }}
                                </h3>

                                <p id="registryDonePercentText" class="mt-2 text-[9px] font-extrabold text-emerald-600">
                                    {{ number_format($registryDonePercent, 2) }}% of eligible
                                </p>
                            </div>

                            <div class="ceo-icon-box bg-emerald-50 text-emerald-600">
                                <span class="material-symbols-outlined text-[22px]">
                                    task_alt
                                </span>
                            </div>

                        </div>
                    </div>


                    {{-- Pending --}}
                    <div class="ceo-reg-card border-orange-100">
                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-[9px] font-bold text-slate-500">
                                    Registry Yet To Be Done
                                </p>

                                <h3 id="registryUnmatched"
                                    class="mt-2 text-[25px] font-black leading-none text-orange-600">
                                    {{ number_format($totals['totalRegistryUnmatched'] ?? 0) }}
                                </h3>

                                <p id="registryPendingPercentText" class="mt-2 text-[9px] font-extrabold text-orange-600">
                                    {{ number_format($registryPendingPercent, 2) }}% of eligible
                                </p>
                            </div>

                            <div class="ceo-icon-box bg-orange-50 text-orange-600">
                                <span class="material-symbols-outlined text-[22px]">
                                    link_off
                                </span>
                            </div>

                        </div>
                    </div>

                </div>

            </section>



            {{-- ================================================================ --}}
            {{-- Registry Match Overview --}}
            {{-- ================================================================ --}}
            <section class="ceo-panel overflow-hidden xl:col-span-5">

                <div class="border-b border-slate-100 px-5 py-3.5">
                    <h2 class="ceo-section-title text-[15px]">
                        Registry Match Overview
                    </h2>

                    <p class="ceo-muted mt-0.5 text-[10px] font-medium">
                        Registry done vs pending
                    </p>
                </div>

                <div
                    class="flex h-[218px] flex-col items-center justify-center gap-7
                px-5 py-4 sm:flex-row">

                    {{-- Donut --}}
                    <div id="registryDonut" class="relative h-[145px] w-[145px] shrink-0 rounded-full"
                        style="
                background: conic-gradient(
                    #16a34a 0 0%,
                    #fb923c 0% 100%
                );
             ">

                        <div
                            class="absolute inset-[18px] flex flex-col
                        items-center justify-center rounded-full bg-white">

                            <strong id="registryDonutTotal" class="text-[22px] font-black leading-none text-slate-900">
                                {{ number_format($totals['totalRegistryAllotted'] ?? 0) }}
                            </strong>

                            <span
                                class="mt-1 text-[8px] font-extrabold uppercase
                             tracking-[.1em] text-slate-500">
                                Total
                            </span>

                        </div>
                    </div>


                    {{-- Legend --}}
                    <div class="w-full max-w-[250px] space-y-4">

                        {{-- Done --}}
                        <div class="flex items-center justify-between gap-4">

                            <div class="flex items-center gap-2">
                                <span class="ceo-legend-dot bg-emerald-500"></span>

                                <span class="text-[10px] font-bold text-slate-600">
                                    Registry Done
                                </span>
                            </div>

                            <strong class="text-[11px] font-black text-slate-900">
                                <span id="registryDoneLegendCount">
                                    {{ number_format($totals['totalRegistryMatched'] ?? 0) }}
                                </span>

                                <span id="registryDoneLegendPercent" class="font-bold text-slate-500">
                                    ({{ number_format($registryDonePercent, 2) }}%)
                                </span>
                            </strong>

                        </div>


                        {{-- Pending --}}
                        <div class="flex items-center justify-between gap-4">

                            <div class="flex items-center gap-2">
                                <span class="ceo-legend-dot bg-orange-400"></span>

                                <span class="text-[10px] font-bold text-slate-600">
                                    Registry Yet To Be Done
                                </span>
                            </div>

                            <strong class="text-[11px] font-black text-slate-900">

                                <span id="registryPendingLegendCount">
                                    {{ number_format($totals['totalRegistryUnmatched'] ?? 0) }}
                                </span>

                                <span id="registryPendingLegendPercent" class="font-bold text-slate-500">
                                    ({{ number_format($registryPendingPercent, 2) }}%)
                                </span>

                            </strong>

                        </div>


                        {{-- Progress --}}
                        <div class="pt-1">

                            <div class="mb-2 flex items-center justify-between">

                                <span class="text-[9px] font-semibold text-slate-500">
                                    Registration Progress
                                </span>

                                <span id="registryProgressPercent" class="text-[9px] font-extrabold text-emerald-600">
                                    {{ number_format($registryDonePercent, 2) }}%
                                </span>

                            </div>

                            <div class="h-[5px] overflow-hidden rounded-full bg-slate-200">

                                <div id="registryProgressBar"
                                    class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                                    style="width:{{ min($registryDonePercent, 100) }}%">
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </section>
        </div>


        {{-- ================================================================ --}}
        {{-- Possession Overview --}}
        {{-- ================================================================ --}}
        <section class="ceo-panel mt-4 overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-3.5">
                <h2 class="ceo-section-title text-[15px]">Possession Overview</h2>
                <p class="ceo-muted mt-0.5 text-[10px] font-medium">Registered beneficiaries eligible for possession</p>
            </div>

            <div class="grid grid-cols-1 gap-3 p-4 md:grid-cols-3">

                <a id="possessionEligibleLink"
                    href="{{ route('district.possession.list', array_merge(['filter' => 'all'], $possessionParams)) }}"
                    class="ceo-reg-card group border-violet-100">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500">Possession to be given</p>
                            <h3 id="registeredBeneficiaries"
                                class="mt-2 text-[25px] font-black leading-none text-violet-700">
                                {{ number_format($totals['totalRegisteredBeneficiaries'] ?? 0) }}
                            </h3>
                            <p id="possessionEligiblePercentText" class="mt-2 text-[9px] font-extrabold text-slate-500">
                                {{ $possessionTotal > 0 ? '100.00' : '0.00' }}% of eligible</p>
                        </div>
                        <div class="ceo-icon-box bg-violet-50 text-violet-600">
                            <span class="material-symbols-outlined text-[22px]">assignment_turned_in</span>
                        </div>
                    </div>
                    <div class="ceo-progress-track mt-4">
                        <div id="possessionEligibleProgressBar" class="ceo-progress-value bg-violet-500"
                            style="width:{{ $possessionTotal > 0 ? 100 : 0 }}%"></div>
                    </div>
                </a>

                <a id="possessionGivenLink"
                    href="{{ route('district.possession.list', array_merge(['filter' => 'verified'], $possessionParams)) }}"
                    class="ceo-reg-card group border-emerald-100">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500">Possession Given</p>
                            <h3 id="possessionGiven" class="mt-2 text-[25px] font-black leading-none text-emerald-700">
                                {{ number_format($totals['totalPossessionGiven'] ?? 0) }}
                            </h3>
                            <p id="possessionGivenPercentText" class="mt-2 text-[9px] font-extrabold text-emerald-600">
                                {{ number_format($possessionGivenPercent, 2) }}% of eligible</p>
                        </div>
                        <div class="ceo-icon-box bg-emerald-50 text-emerald-600">
                            <span class="material-symbols-outlined text-[22px]">verified</span>
                        </div>
                    </div>
                    <div class="ceo-progress-track mt-4">
                        <div id="possessionGivenProgressBar" class="ceo-progress-value bg-emerald-500"
                            style="width:{{ min($possessionGivenPercent, 100) }}%"></div>
                    </div>
                </a>

                <a id="possessionPendingLink"
                    href="{{ route('district.possession.list', array_merge(['filter' => 'possession_pending'], $possessionParams)) }}"
                    class="ceo-reg-card group border-orange-100">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500">Possession Pending</p>
                            <h3 id="possessionPending" class="mt-2 text-[25px] font-black leading-none text-orange-700">
                                {{ number_format($totals['totalPossessionPending'] ?? 0) }}
                            </h3>
                            <p id="possessionPendingPercentText" class="mt-2 text-[9px] font-extrabold text-orange-600">
                                {{ number_format($possessionPendingPercent, 2) }}% of eligible</p>
                        </div>
                        <div class="ceo-icon-box bg-orange-50 text-orange-500">
                            <span class="material-symbols-outlined text-[22px]">hourglass_empty</span>
                        </div>
                    </div>
                    <div class="ceo-progress-track mt-4">
                        <div id="possessionPendingProgressBar" class="ceo-progress-value bg-orange-500"
                            style="width:{{ min($possessionPendingPercent, 100) }}%"></div>
                    </div>
                </a>

            </div>
        </section>

        {{-- ================================================================ --}}
        {{-- Village Wise Summary --}}
        {{-- ================================================================ --}}
        <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-start gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100">
                        <span class="material-symbols-outlined text-[22px] text-blue-700">
                            table_chart
                        </span>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-slate-800">
                            Village Wise Summary
                        </h3>

                        <p id="phaseTitle" class="mt-0.5 text-xs text-slate-500">
                            Phase {{ $phase }} Village Statistics
                        </p>
                    </div>

                </div>

                <div class="flex flex-wrap items-center gap-2">

                    <a id="downloadVillagePdf"
                        href="{{ route('district.dashboard.village-summary.pdf', [
                            'phase' => $phase,
                            'village_id' => $villageId,
                        ]) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700">
                        <span class="material-symbols-outlined text-[18px]">
                            picture_as_pdf
                        </span>

                        PDF
                    </a>

                    <a id="downloadVillageExcel"
                        href="{{ route('district.dashboard.village-summary.excel', [
                            'phase' => $phase,
                            'village_id' => $villageId,
                        ]) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        <span class="material-symbols-outlined text-[18px]">
                            table_view
                        </span>

                        Excel
                    </a>

                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead>
                        <tr class="bg-slate-800 text-white">

                            <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold">
                                #
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold">
                                Village
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold">
                                Total Plots
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold">
                                Applicants
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold">
                                Approved Paid
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold">
                                SC
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold">
                                Ghumantu
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold">
                                Widow
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold">
                                Others
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold">
                                Allotted
                            </th>

                        </tr>
                    </thead>

                    <tbody id="villageTableBody" class="divide-y divide-slate-100">

                        @forelse ($villageData as $row)
                            <tr class="transition hover:bg-blue-50/70">

                                <td class="whitespace-nowrap px-4 py-3 text-slate-500">
                                    {{ $loop->iteration }}
                                </td>
                                {{-- Village --}}
                                {{-- Village --}}
                                <td class="px-4 py-3">

                                    @php
                                        $mapPdfUrl = !empty($row->PdfFile)
                                            ? asset('phase1_plans_gps_map/' . ltrim($row->PdfFile, '/'))
                                            : null;

                                        $applicantUrl = route('district.dashboard.applicants', [
                                            'phase' => $row->Phase ?? 'all',
                                            'village_id' => $row->VillageId,
                                            'status' => 'all_applicants',
                                        ]);
                                    @endphp

                                    <div class="flex flex-wrap items-center gap-2">

                                        {{-- Site Development --}}
                                        <button type="button" title="Site Development"
                                            class="siteDevelopmentBtn inline-flex h-8 w-8 shrink-0
                   items-center justify-center rounded-lg bg-cyan-100
                   text-cyan-700 transition hover:bg-cyan-600
                   hover:text-white"
                                            data-village-id="{{ $row->VillageId }}"
                                            data-village-name="{{ $row->VillageName }}"
                                            data-phase="{{ $row->Phase }}">

                                            <span class="material-symbols-outlined text-[18px]">
                                                construction
                                            </span>
                                        </button>

                                        {{-- Map PDF --}}
                                        @if ($mapPdfUrl)
                                            <button type="button" title="View Village Map"
                                                class="villageMapBtn inline-flex h-8 items-center
                       justify-center gap-1 rounded-lg border
                       border-indigo-200 bg-indigo-50 px-2.5
                       text-xs font-semibold text-indigo-700
                       transition hover:border-indigo-600
                       hover:bg-indigo-600 hover:text-white"
                                                data-pdf-url="{{ $mapPdfUrl }}" data-pdf-name="{{ $row->PdfFile }}"
                                                data-village-name="{{ $row->VillageName }}"
                                                data-phase="{{ $row->Phase }}">

                                                <span class="material-symbols-outlined text-[17px]">
                                                    map
                                                </span>

                                                Map
                                            </button>
                                        @endif

                                        {{-- Village Name --}}
                                        <a href="{{ $applicantUrl }}"
                                            class="inline-flex items-center rounded-md px-2 py-1
                   font-semibold text-slate-800 transition-all
                   duration-200 hover:bg-slate-800 hover:text-white
                   hover:shadow-md">

                                            {{ $row->VillageName }}
                                        </a>

                                    </div>

                                </td>

                                {{-- Total Plots (Not Clickable) --}}
                                <td class="whitespace-nowrap px-4 py-3 text-center text-slate-700">
                                    {{ number_format($row->TotalPlots ?? 0) }}
                                </td>

                                {{-- Applicants --}}
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $phase,
                                        'village_id' => $row->VillageId,
                                        'status' => 'all_applicants',
                                    ]) }}"
                                        class="inline-flex min-w-[60px] justify-center rounded-md bg-blue-50 px-2 py-1 font-semibold text-blue-600 transition-all duration-200 hover:bg-blue-600 hover:text-white hover:shadow-md">
                                        {{ number_format($row->TotalApplicants ?? 0) }}
                                    </a>
                                </td>

                                {{-- Approved Paid --}}
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $phase,
                                        'village_id' => $row->VillageId,
                                        'status' => 'approved_paid',
                                    ]) }}"
                                        class="inline-flex min-w-[60px] justify-center rounded-md bg-emerald-50 px-2 py-1 font-semibold text-emerald-600 transition-all duration-200 hover:bg-emerald-600 hover:text-white hover:shadow-md">
                                        {{ number_format($row->ApprovedPaid ?? 0) }}
                                    </a>
                                </td>

                                {{-- SC --}}
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $phase,
                                        'village_id' => $row->VillageId,
                                        'status' => 'sc',
                                    ]) }}"
                                        class="inline-flex min-w-[60px] justify-center rounded-md bg-indigo-50 px-2 py-1 font-semibold text-indigo-600 transition-all duration-200 hover:bg-indigo-600 hover:text-white hover:shadow-md">
                                        {{ number_format($row->SC ?? 0) }}
                                    </a>
                                </td>

                                {{-- Ghumantu --}}
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $phase,
                                        'village_id' => $row->VillageId,
                                        'status' => 'ghumantu',
                                    ]) }}"
                                        class="inline-flex min-w-[60px] justify-center rounded-md bg-violet-50 px-2 py-1 font-semibold text-violet-600 transition-all duration-200 hover:bg-violet-600 hover:text-white hover:shadow-md">
                                        {{ number_format($row->Ghumantu ?? 0) }}
                                    </a>
                                </td>

                                {{-- Widow --}}
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $phase,
                                        'village_id' => $row->VillageId,
                                        'status' => 'widow',
                                    ]) }}"
                                        class="inline-flex min-w-[60px] justify-center rounded-md bg-pink-50 px-2 py-1 font-semibold text-pink-600 transition-all duration-200 hover:bg-pink-600 hover:text-white hover:shadow-md">
                                        {{ number_format($row->Widow ?? 0) }}
                                    </a>
                                </td>

                                {{-- Others --}}
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $phase,
                                        'village_id' => $row->VillageId,
                                        'status' => 'others',
                                    ]) }}"
                                        class="inline-flex min-w-[60px] justify-center rounded-md bg-amber-50 px-2 py-1 font-semibold text-amber-700 transition-all duration-200 hover:bg-amber-600 hover:text-white hover:shadow-md">
                                        {{ number_format($row->Others ?? 0) }}
                                    </a>
                                </td>

                                {{-- Allotted --}}
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $phase,
                                        'village_id' => $row->VillageId,
                                        'status' => 'allotted',
                                    ]) }}"
                                        class="inline-flex min-w-[60px] justify-center rounded-md bg-cyan-50 px-2 py-1 font-bold text-cyan-700 transition-all duration-200 hover:bg-cyan-700 hover:text-white hover:shadow-md">
                                        {{ number_format($row->TotalAllotment ?? 0) }}
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center text-sm text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">

                                        <span class="material-symbols-outlined text-[38px] text-slate-300">
                                            search_off
                                        </span>

                                        <span>No village records found.</span>

                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                    <tfoot id="grandTotalFooter"
                        class="border-t-2 border-slate-300 bg-slate-100 font-bold text-slate-800">
                        <tr>

                            <td colspan="2" class="whitespace-nowrap px-4 py-3">
                                Grand Total
                            </td>

                            <td id="gtPlots" class="whitespace-nowrap px-4 py-3 text-center">
                                {{ number_format($totals['totalPlots'] ?? 0) }}
                            </td>

                            <td id="gtApplicants" class="whitespace-nowrap px-4 py-3 text-center">
                                {{ number_format($totals['totalApplicants'] ?? 0) }}
                            </td>

                            <td id="gtPaid" class="whitespace-nowrap px-4 py-3 text-center text-emerald-700">
                                {{ number_format($totals['totalPaid'] ?? 0) }}
                            </td>

                            <td id="gtSC" class="whitespace-nowrap px-4 py-3 text-center">
                                {{ number_format($totals['totalSC'] ?? 0) }}
                            </td>

                            <td id="gtGhumantu" class="whitespace-nowrap px-4 py-3 text-center">
                                {{ number_format($totals['totalGhumantu'] ?? 0) }}
                            </td>

                            <td id="gtWidow" class="whitespace-nowrap px-4 py-3 text-center">
                                {{ number_format($totals['totalWidow'] ?? 0) }}
                            </td>

                            <td id="gtOthers" class="whitespace-nowrap px-4 py-3 text-center">
                                {{ number_format($totals['totalOthers'] ?? 0) }}
                            </td>

                            <td id="gtAllotment" class="whitespace-nowrap px-4 py-3 text-center text-blue-700">
                                {{ number_format($totals['totalAllotment'] ?? 0) }}
                            </td>

                        </tr>
                    </tfoot>

                </table>

            </div>

        </section>

    </main>
    {{-- ================================================================ --}}
    {{-- Site Development Modal --}}
    {{-- ================================================================ --}}
    <div id="siteDevelopmentModal"
    class="fixed inset-0 z-[9999] hidden bg-slate-900/70 p-3 backdrop-blur-sm">

    <div class="absolute inset-0 flex items-center justify-center">

        <div id="siteDevelopmentModalPanel"
            class="w-[95vw] max-w-[1400px] max-h-[90vh] overflow-hidden rounded-3xl bg-slate-50 shadow-2xl">

                {{-- Header --}}
                <div
                    class="flex items-center justify-between bg-gradient-to-r from-cyan-600 to-blue-700 px-5 py-4 text-white">
                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/15">
                            <span class="material-symbols-outlined text-[25px]">
                                construction
                            </span>
                        </div>

                        <div>
                            <h2 id="siteDevelopmentVillageName" class="text-lg font-bold">
                                Village
                            </h2>

                            <p id="siteDevelopmentPhase" class="mt-0.5 text-xs font-medium text-white/90">
                                Phase
                            </p>
                        </div>

                    </div>

                    <button type="button" id="closeSiteDevelopmentModal"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/15 transition hover:bg-white/25">
                        <span class="material-symbols-outlined text-[25px]">
                            close
                        </span>
                    </button>
                </div>

                {{-- Loading --}}
                <div id="siteDevelopmentLoading" class="hidden px-6 py-12 text-center">
                    <div class="mx-auto h-9 w-9 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600">
                    </div>

                    <p class="mt-3 text-sm font-semibold text-slate-600">
                        Loading site development details...
                    </p>
                </div>

                {{-- Error --}}
                <div id="siteDevelopmentError" class="hidden px-5 py-10 sm:px-8 sm:py-12">
                    <div
                        class="mx-auto max-w-md rounded-2xl border border-rose-100 bg-gradient-to-b from-rose-50/80 to-white p-6 text-center shadow-sm sm:p-7">
                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 shadow-sm">
                            <span class="material-symbols-outlined text-[30px]">
                                error_outline
                            </span>
                        </div>

                        <h3 class="mt-4 text-base font-extrabold tracking-tight text-slate-800 sm:text-lg">
                            Development details unavailable
                        </h3>

                        <p class="mx-auto mt-2 max-w-sm text-xs font-medium leading-5 text-slate-500 sm:text-sm">
                            We couldn't load the site development details for this village right now.
                        </p>

                        <div class="mt-4 rounded-xl border border-rose-100 bg-white px-3 py-2.5 text-left">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined mt-0.5 text-[18px] text-rose-500">info</span>
                                <p id="siteDevelopmentErrorMessage"
                                    class="min-w-0 text-xs font-semibold leading-5 text-rose-700">
                                    Unable to load data.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Empty --}}
                <div id="siteDevelopmentEmpty" class="hidden px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-[46px] text-slate-300">
                        construction
                    </span>

                    <h3 class="mt-2 text-base font-bold text-slate-700">
                        No Development Record
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        No Site Development data is currently available for this village.
                    </p>
                </div>

                {{-- Records --}}
                <div id="siteDevelopmentRecords" class="max-h-[70vh] space-y-4 overflow-y-auto p-4 sm:p-5"></div>

            </div>

        </div>
    </div>
    {{-- ================================================================ --}}
    {{-- Village Map PDF Modal --}}
    {{-- ================================================================ --}}
    <div id="villageMapModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/75
           p-3 backdrop-blur-sm">

        <div class="flex min-h-full items-center justify-center">

            <div
                class="flex h-[96vh] w-[98vw] max-w-none flex-col
                   overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- Header --}}
                <div
                    class="flex shrink-0 flex-col gap-3
                       bg-gradient-to-r from-indigo-700
                       via-blue-700 to-cyan-600 px-5 py-4
                       text-white sm:flex-row sm:items-center
                       sm:justify-between">

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center
                               justify-center rounded-xl bg-white/15">

                            <span class="material-symbols-outlined text-[24px]">
                                map
                            </span>
                        </div>

                        <div class="min-w-0">

                            <h2 id="villageMapTitle" class="truncate text-lg font-bold">
                                Village Map
                            </h2>

                            <p id="villageMapSubtitle"
                                class="mt-0.5 truncate text-xs
                                   font-medium text-white/90">
                                Site plan PDF
                            </p>

                        </div>

                    </div>

                    <div class="flex items-center gap-2">

                        <a id="downloadVillageMap" href="#" download
                            class="inline-flex h-10 items-center
                               justify-center gap-2 rounded-xl
                               bg-white px-4 text-sm font-semibold
                               text-blue-700 transition
                               hover:bg-blue-50">

                            <span class="material-symbols-outlined text-[19px]">
                                download
                            </span>

                            Download
                        </a>

                        <a id="openVillageMap" href="#" target="_blank" rel="noopener"
                            class="inline-flex h-10 items-center
                               justify-center gap-2 rounded-xl
                               border border-white/30 bg-white/15
                               px-4 text-sm font-semibold text-white
                               transition hover:bg-white/25">

                            <span class="material-symbols-outlined text-[19px]">
                                open_in_new
                            </span>

                            Open
                        </a>

                        <button type="button" id="closeVillageMapModal"
                            class="inline-flex h-10 w-10 items-center
                               justify-center rounded-xl bg-white/15
                               transition hover:bg-white/25">

                            <span class="material-symbols-outlined text-[24px]">
                                close
                            </span>
                        </button>

                    </div>

                </div>

                {{-- Viewer --}}
                <div class="relative min-h-0 flex-1 bg-slate-200">

                    <div id="villageMapLoader"
                        class="absolute inset-0 z-10 flex items-center
                           justify-center bg-slate-100">

                        <div class="text-center">

                            <div
                                class="mx-auto h-11 w-11 animate-spin
                                   rounded-full border-4
                                   border-indigo-200
                                   border-t-indigo-600">
                            </div>

                            <p class="mt-3 text-sm font-semibold
                                   text-slate-600">
                                Loading village map...
                            </p>

                        </div>

                    </div>

                    <iframe id="villageMapFrame" src="" title="Village Map PDF" class="h-full w-full border-0">
                    </iframe>

                </div>

            </div>

        </div>

    </div>
@endsection
