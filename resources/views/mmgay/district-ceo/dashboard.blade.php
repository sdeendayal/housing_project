@extends('layouts.mmgayCEOAuth')

@section('title', 'MMGAY District CEO Dashboard')

@section('content')

    <main class="mt-[68px] min-h-screen bg-slate-50 p-4
           lg:ml-[230px] lg:w-[calc(100%-230px)] lg:p-5">

        {{-- Filters --}}
        {{-- ================================================================ --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-5 py-3.5">
                <h2 class="text-base font-bold text-slate-800">
                    Dashboard Filters
                </h2>

                <p class="mt-0.5 text-xs text-slate-500">
                    Filter statistics by phase and village
                </p>
            </div>

            <div class="p-4">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">

                    {{-- Phase --}}
                    <div class="xl:col-span-3">

                        <label for="phase_filter"
                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                            Phase
                        </label>

                        <select id="phase_filter"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <option value="all" {{ $phase === 'all' ? 'selected' : '' }}>
                                All Phases
                            </option>

                            <option value="1" {{ (string) $phase === '1' ? 'selected' : '' }}>
                                Phase 1
                            </option>

                            <option value="2" {{ (string) $phase === '2' ? 'selected' : '' }}>
                                Phase 2
                            </option>

                            <option value="3" {{ (string) $phase === '3' ? 'selected' : '' }}>
                                Phase 3
                            </option>

                        </select>

                    </div>

                    {{-- Village --}}
                    <div class="xl:col-span-6">

                        <label for="village_filter"
                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                            Village
                        </label>

                        <select id="village_filter"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <option value="">
                                All Villages
                            </option>

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

                    {{-- Action Buttons --}}
                    <div class="flex items-end gap-2 xl:col-span-3">

                        <button type="button" id="applyFilters"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-70">

                            <span class="material-symbols-outlined text-[18px]">
                                filter_alt
                            </span>

                            Apply
                        </button>

                        <button type="button" id="resetFilters"
                            class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-100 hover:text-red-700">

                            <span class="material-symbols-outlined text-[18px]">
                                restart_alt
                            </span>

                            Reset
                        </button>

                    </div>

                </div>

            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- 1. Master Data --}}
        {{-- ================================================================ --}}
        @php
            $dashboardApplicantParams = array_filter(
                [
                    'phase' => $phase ?? 'all',
                    'village_id' => $villageId ?? null,
                ],
                static fn($value) => $value !== null && $value !== '',
            );

            $applicantReportUrl = route('district.dashboard.applicants', $dashboardApplicantParams);
        @endphp


        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Section Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100">
                        <span class="material-symbols-outlined text-[22px] text-blue-700">
                            database
                        </span>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-800">
                            Master Data
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Overall project statistics
                        </p>
                    </div>

                </div>

            </div>

            {{-- Master Data Cards --}}
            <div class="bg-slate-50/70 p-4">

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

                    {{-- Villages --}}
                    <a id="villagesReportLink"
                        href="{{ route('district.dashboard.report', [
                            'type' => 'villages',
                            'phase' => $phase ?? 'all',
                            'village_id' => $villageId ?? null,
                        ]) }}"
                        class="group block rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">

                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Villages
                                </p>

                                <h3 id="totalVillages" class="mt-1.5 text-2xl font-bold tracking-tight text-slate-800">
                                    {{ number_format($totals['totalVillages'] ?? 0) }}
                                </h3>
                            </div>

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 transition group-hover:bg-blue-100">

                                <span class="material-symbols-outlined text-[22px] text-blue-600">
                                    holiday_village
                                </span>

                            </div>

                        </div>

                    </a>

                    {{-- Plots --}}
                    <a href="#"
                        class="group block rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md">

                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Plots
                                </p>

                                <h3 id="totalPlots" class="mt-1.5 text-2xl font-bold tracking-tight text-slate-800">
                                    {{ number_format($totals['totalPlots'] ?? 0) }}
                                </h3>
                            </div>

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 transition group-hover:bg-emerald-100">

                                <span class="material-symbols-outlined text-[22px] text-emerald-600">
                                    grid_view
                                </span>

                            </div>

                        </div>

                    </a>

                    {{-- Applicants --}}
                    {{-- Applicants --}}
                    <a id="applicantsReportLink" data-status=""
                        href="{{ route('district.dashboard.applicants', $dashboardApplicantParams) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">

                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Applicants
                                </p>

                                <h3 id="totalApplicants" class="mt-1.5 text-2xl font-bold tracking-tight text-slate-800">
                                    {{ number_format($totals['totalApplicants'] ?? 0) }}
                                </h3>
                            </div>

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 transition group-hover:bg-indigo-100">

                                <span class="material-symbols-outlined text-[22px] text-indigo-600">
                                    groups
                                </span>
                            </div>

                        </div>
                    </a>

                    {{-- Allotment --}}
                    {{-- Allotment --}}
                    <a id="allottedApplicantsLink" data-status="allotted"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'allotted',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md">

                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Allotment
                                </p>

                                <h3 id="totalAllotment" class="mt-1.5 text-2xl font-bold tracking-tight text-slate-800">
                                    {{ number_format($totals['totalAllotment'] ?? 0) }}
                                </h3>
                            </div>

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 transition group-hover:bg-amber-100">

                                <span class="material-symbols-outlined text-[22px] text-amber-600">
                                    home_work
                                </span>
                            </div>

                        </div>
                    </a>

                </div>

            </div>

        </section>


        {{-- ================================================================ --}}
        {{-- 2. Allotment Status --}}
        {{-- ================================================================ --}}
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Section Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                        <span class="material-symbols-outlined text-[22px] text-emerald-700">
                            fact_check
                        </span>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-800">
                            Allotment Status
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Current beneficiary approval and payment status
                        </p>
                    </div>

                </div>

            </div>

            {{-- Allotment Cards --}}
            <div class="bg-slate-50/70 p-4">

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">

                    {{-- Approved & Paid --}}
                    <a id="approvedPaidReportLink" data-status="approved_paid"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'approved_paid',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 border-l-4 border-l-emerald-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">

                        <div class="flex items-center justify-between gap-2">

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Approved & Paid
                                </p>

                                <h3 id="approvedPaid" class="mt-1 text-2xl font-bold text-emerald-700">
                                    {{ number_format($totals['totalPaid'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50">

                                <span class="material-symbols-outlined text-[21px] text-emerald-600">
                                    paid
                                </span>
                            </div>

                        </div>
                    </a>

                    {{-- Approved & Unpaid --}}
                    <a id="approvedUnpaidReportLink" data-status="approved_unpaid"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'approved_unpaid',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 border-l-4 border-l-cyan-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">

                        <div class="flex items-center justify-between gap-2">

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Approved & Unpaid
                                </p>

                                <h3 id="approvedUnpaid" class="mt-1 text-2xl font-bold text-cyan-700">
                                    {{ number_format($totals['totalApprovedUnpaid'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-cyan-50">

                                <span class="material-symbols-outlined text-[21px] text-cyan-600">
                                    pending_actions
                                </span>
                            </div>

                        </div>
                    </a>

                    {{-- Yet to be Approved --}}
                    <a id="pendingApplicantsReportLink" data-status="pending"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'pending',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 border-l-4 border-l-amber-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">

                        <div class="flex items-center justify-between gap-2">

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Yet to be Approved
                                </p>

                                <h3 id="yetToBeApproved" class="mt-1 text-2xl font-bold text-amber-700">
                                    {{ number_format($totals['totalPending'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50">

                                <span class="material-symbols-outlined text-[21px] text-amber-600">
                                    hourglass_top
                                </span>
                            </div>

                        </div>
                    </a>

                    {{-- Rejected --}}
                    <a id="rejectedApplicantsReportLink" data-status="rejected"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'rejected',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 border-l-4 border-l-rose-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">

                        <div class="flex items-center justify-between gap-2">

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Rejected
                                </p>

                                <h3 id="rejected" class="mt-1 text-2xl font-bold text-rose-700">
                                    {{ number_format($totals['totalRejected'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50">

                                <span class="material-symbols-outlined text-[21px] text-rose-600">
                                    cancel
                                </span>
                            </div>

                        </div>
                    </a>

                    {{-- Cancelled --}}
                    <a id="cancelledApplicantsReportLink" data-status="cancelled"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'cancelled',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 border-l-4 border-l-slate-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md sm:col-span-2 md:col-span-1">

                        <div class="flex items-center justify-between gap-2">

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Cancelled
                                </p>

                                <h3 id="cancelled" class="mt-1 text-2xl font-bold text-slate-700">
                                    {{ number_format($totals['totalCancelled'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100">

                                <span class="material-symbols-outlined text-[21px] text-slate-600">
                                    block
                                </span>
                            </div>

                        </div>
                    </a>

                </div>

            </div>

        </section>


        {{-- ================================================================ --}}
        {{-- 3. Registration Statistics --}}
        {{-- ================================================================ --}}
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Section Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100">
                        <span class="material-symbols-outlined text-[22px] text-violet-700">
                            contract
                        </span>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-800">
                            Registration Statistics
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Registry matching for allotted beneficiaries
                        </p>
                    </div>

                </div>

            </div>

            {{-- Registration Cards --}}
            <div class="bg-slate-50/70 p-4">

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                    {{-- Registry to be done --}}
                    <a id="registryAllottedReportLink" data-status="allotted"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'allotted',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 border-l-4 border-l-blue-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">


                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Registry to be done
                                </p>

                                <h3 id="registrationAllotted" class="mt-1 text-2xl font-bold text-blue-700">
                                    {{ number_format($totals['totalRegistryAllotted'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                                <span class="material-symbols-outlined text-[23px] text-blue-600">
                                    home_work
                                </span>
                            </div>



                        </div>
                    </a>
                    {{-- Registry Done --}}
                    <a id="registryDoneReportLink" data-status="registry_done"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'registry_done',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 border-l-4 border-l-emerald-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">



                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Registry Done
                                </p>

                                <h3 id="registryMatched" class="mt-1 text-2xl font-bold text-emerald-700">
                                    {{ number_format($totals['totalRegistryMatched'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50">
                                <span class="material-symbols-outlined text-[23px] text-emerald-600">
                                    task_alt
                                </span>
                            </div>

                        </div>



                    </a>

                    {{-- Registry yet to be done --}}
                    <a id="registryPendingReportLink" data-status="registry_pending"
                        href="{{ route(
                            'district.dashboard.applicants',
                            array_merge($dashboardApplicantParams, [
                                'status' => 'registry_pending',
                            ]),
                        ) }}"
                        class="applicant-report-link group block rounded-xl border border-slate-200 border-l-4 border-l-orange-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">



                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Registry yet to be done
                                </p>

                                <h3 id="registryUnmatched" class="mt-1 text-2xl font-bold text-orange-700">
                                    {{ number_format($totals['totalRegistryUnmatched'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50">
                                <span class="material-symbols-outlined text-[23px] text-orange-600">
                                    link_off
                                </span>
                            </div>

                        </div>


                    </a>

                </div>

            </div>

        </section>


        {{-- ================================================================ --}}
        {{-- 4. Possession --}}
        {{-- ================================================================ --}}
        @php
            $possessionParams = array_filter(
                [
                    'phase' => $phase ?? 'all',
                    'village_id' => $villageId ?? null,
                ],
                static fn($value) => $value !== null && $value !== '',
            );
        @endphp

        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100">
                        <span class="material-symbols-outlined text-[22px] text-purple-700">
                            key
                        </span>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-800">
                            Possession
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Registered beneficiaries eligible for possession
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50/70 p-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                    <a id="possessionEligibleLink"
                        href="{{ route('district.possession.list', array_merge(['filter' => 'all'], $possessionParams)) }}"
                        class="group rounded-xl border border-slate-200 border-l-4 border-l-violet-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Possession to be given
                                </p>

                                <h3 id="registeredBeneficiaries" class="mt-1 text-2xl font-bold text-violet-700">
                                    {{ number_format($totals['totalRegisteredBeneficiaries'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50">
                                <span class="material-symbols-outlined text-[23px] text-violet-600">
                                    assignment_turned_in
                                </span>
                            </div>
                        </div>
                    </a>

                    <a id="possessionGivenLink"
                        href="{{ route('district.possession.list', array_merge(['filter' => 'verified'], $possessionParams)) }}"
                        class="group rounded-xl border border-slate-200 border-l-4 border-l-emerald-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Possession Given
                                </p>

                                <h3 id="possessionGiven" class="mt-1 text-2xl font-bold text-emerald-700">
                                    {{ number_format($totals['totalPossessionGiven'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50">
                                <span class="material-symbols-outlined text-[23px] text-emerald-600">
                                    verified
                                </span>
                            </div>
                        </div>
                    </a>

                    <a id="possessionPendingLink"
                        href="{{ route('district.possession.list', array_merge(['filter' => 'possession_pending'], $possessionParams)) }}"
                        class="group rounded-xl border border-slate-200 border-l-4 border-l-amber-500 bg-white px-4 py-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    Possession Pending
                                </p>

                                <h3 id="possessionPending" class="mt-1 text-2xl font-bold text-amber-700">
                                    {{ number_format($totals['totalPossessionPending'] ?? 0) }}
                                </h3>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50">
                                <span class="material-symbols-outlined text-[23px] text-amber-600">
                                    hourglass_empty
                                </span>
                            </div>
                        </div>
                    </a>

                </div>
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
    <div id="siteDevelopmentModal" class="fixed inset-0 z-[9999] hidden bg-slate-900/70 p-3 backdrop-blur-sm">
        <div class="flex min-h-full items-center justify-center">

            <div id="siteDevelopmentModalPanel"
                class="w-full max-w-5xl overflow-hidden rounded-3xl bg-slate-50 shadow-2xl">

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
                <div id="siteDevelopmentError" class="hidden px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-[42px] text-red-400">
                        error
                    </span>

                    <p id="siteDevelopmentErrorMessage" class="mt-2 text-sm font-semibold text-red-600">
                        Unable to load data.
                    </p>
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
                <div id="siteDevelopmentRecords" class="space-y-4 p-4 sm:p-5"></div>

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
                class="flex h-[94vh] w-full max-w-7xl flex-col
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
