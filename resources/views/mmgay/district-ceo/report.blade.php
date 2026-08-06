@extends('layouts.mmgayCEOAuth')

@section('title', 'Village Wise Report')

@section('content')

    <main class="mt-[68px] min-h-screen bg-slate-50 p-4 lg:ml-[230px] lg:w-[calc(100%-230px)] lg:p-5">

        {{-- Page Header --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100">

                        <span class="material-symbols-outlined text-[24px] text-blue-700">
                            table_view
                        </span>

                    </div>

                    <div>
                        <h1 class="text-xl font-bold text-slate-800">
                            Village Wise Consolidated Report
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $phase === 'all' ? 'All Phases' : 'Phase ' . $phase }}
                            village status report
                        </p>
                    </div>

                </div>

                <a href="{{ route('district.dashboard', ['phase' => $phase]) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">

                    <span class="material-symbols-outlined text-[19px]">
                        arrow_back
                    </span>

                    Back to Dashboard
                </a>

            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('district.dashboard.report', ['type' => 'villages']) }}"
                class="bg-slate-50/70 p-5">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">

                    {{-- Phase --}}
                    <div class="xl:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Phase
                        </label>

                        <select name="phase"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

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
                    <div class="xl:col-span-3">

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Village
                        </label>

                        <select name="village_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

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

                    {{-- Status --}}
                    <div class="xl:col-span-3">

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Status
                        </label>

                        <select name="status"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <option value="">
                                All Statuses
                            </option>

                            <option value="approved_paid" {{ $status === 'approved_paid' ? 'selected' : '' }}>
                                Approved & Paid
                            </option>

                            <option value="approved_unpaid" {{ $status === 'approved_unpaid' ? 'selected' : '' }}>
                                Approved & Unpaid
                            </option>

                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>
                                Yet to be Approved
                            </option>

                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                            <option value="registry_done" {{ $status === 'registry_done' ? 'selected' : '' }}>
                                Registry Done
                            </option>

                            <option value="registry_pending" {{ $status === 'registry_pending' ? 'selected' : '' }}>
                                Registry Yet to be Done
                            </option>

                        </select>

                    </div>

                    {{-- Caste --}}
                    <div class="xl:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Caste
                        </label>

                        <select name="caste"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <option value="">
                                All Castes
                            </option>

                            <option value="SC" {{ $caste === 'SC' ? 'selected' : '' }}>
                                SC
                            </option>

                            <option value="Ghumantu" {{ $caste === 'Ghumantu' ? 'selected' : '' }}>
                                Ghumantu
                            </option>

                            <option value="Widow" {{ $caste === 'Widow' ? 'selected' : '' }}>
                                Widow
                            </option>

                            <option value="General" {{ $caste === 'General' ? 'selected' : '' }}>
                                General
                            </option>

                            <option value="Others" {{ $caste === 'Others' ? 'selected' : '' }}>
                                Others
                            </option>

                        </select>

                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-end gap-2 xl:col-span-2">

                        <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">

                            <span class="material-symbols-outlined text-[19px]">
                                filter_alt
                            </span>

                            Apply
                        </button>

                        <a href="{{ route('district.dashboard.report', ['type' => 'villages']) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-600 transition hover:bg-slate-100"
                            title="Reset Filters">

                            <span class="material-symbols-outlined text-[19px]">
                                restart_alt
                            </span>

                        </a>

                    </div>

                </div>

            </form>

        </div>

        {{-- Report Table --}}
        {{-- Report Table --}}
        <section class="mt-5 overflow-hidden rounded-2xl border
           border-slate-200 bg-white shadow-sm">

            {{-- Header --}}
            <div
                class="flex flex-col gap-4 border-b border-slate-200
               px-5 py-4 sm:flex-row sm:items-center
               sm:justify-between">

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center
                       rounded-xl bg-blue-100 text-blue-700">

                        <span class="material-symbols-outlined text-[24px]">
                            table_chart
                        </span>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-800">
                            Village Wise Summary
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ $phase === 'all' ? 'All Phases Village Statistics' : 'Phase ' . $phase . ' Village Statistics' }}
                        </p>
                    </div>

                </div>

                <span
                    class="inline-flex w-fit items-center rounded-full
                   bg-blue-100 px-3 py-1 text-xs font-semibold
                   text-blue-700">

                    {{ number_format($reportData->count()) }} Records
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-[1750px] w-full text-sm">

                    <thead>
                        <tr class="bg-slate-800 text-white">

                            <th class="w-14 px-3 py-3 text-left
                               text-xs font-semibold">
                                #
                            </th>

                            <th
                                class="min-w-[250px] px-3 py-3 text-left
                               text-xs font-semibold">
                                Village
                            </th>

                            <th
                                class="min-w-[85px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Phase
                            </th>

                            <th
                                class="min-w-[90px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Total<br>Plots
                            </th>

                            <th
                                class="min-w-[105px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Applicants
                            </th>

                            <th
                                class="min-w-[100px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Allotted
                            </th>

                            <th
                                class="min-w-[115px] px-3 py-3 text-center
                               text-xs font-semibold leading-4">
                                Approved<br>Paid
                            </th>

                            <th
                                class="min-w-[120px] px-3 py-3 text-center
                               text-xs font-semibold leading-4">
                                Approved<br>Unpaid
                            </th>

                            <th
                                class="min-w-[130px] px-3 py-3 text-center
                               text-xs font-semibold leading-4">
                                Yet to be<br>Approved
                            </th>

                            <th
                                class="min-w-[95px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Rejected
                            </th>

                            <th
                                class="min-w-[95px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Cancelled
                            </th>

                            <th
                                class="min-w-[110px] px-3 py-3 text-center
                               text-xs font-semibold leading-4">
                                Registry<br>Done
                            </th>

                            <th
                                class="min-w-[130px] px-3 py-3 text-center
                               text-xs font-semibold leading-4">
                                Registry<br>Pending
                            </th>

                            <th
                                class="min-w-[125px] px-3 py-3 text-center
                               text-xs font-semibold leading-4">
                                Possession<br>Eligible
                            </th>

                            <th
                                class="min-w-[75px] px-3 py-3 text-center
                               text-xs font-semibold">
                                SC
                            </th>

                            <th
                                class="min-w-[90px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Ghumantu
                            </th>

                            <th
                                class="min-w-[80px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Widow
                            </th>

                            <th
                                class="min-w-[80px] px-3 py-3 text-center
                               text-xs font-semibold">
                                Others
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">

                        @forelse ($reportData as $row)
                            @php
                                /*
                        |--------------------------------------------------------------------------
                        | Common row parameters
                        |--------------------------------------------------------------------------
                        */
                                $rowParams = [
                                    'phase' => $row->Phase ?? 'all',
                                    'village_id' => $row->VillageId,
                                ];

                                /*
                        |--------------------------------------------------------------------------
                        | Village map PDF
                        |--------------------------------------------------------------------------
                        */
                                $mapPdfUrl = !empty($row->PdfFile)
                                    ? asset('phase1_plans_gps_map/' . ltrim($row->PdfFile, '/'))
                                    : null;

                                /*
                        |--------------------------------------------------------------------------
                        | Applicants links
                        |--------------------------------------------------------------------------
                        */
                                $allApplicantsUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'all_applicants',
                                    ]),
                                );

                                $allottedUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'allotted',
                                    ]),
                                );

                                $approvedPaidUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'approved_paid',
                                    ]),
                                );

                                $approvedUnpaidUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'approved_unpaid',
                                    ]),
                                );

                                $pendingUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'pending',
                                    ]),
                                );

                                $rejectedUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'rejected',
                                    ]),
                                );

                                $cancelledUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'cancelled',
                                    ]),
                                );

                                $registryDoneUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'registry_done',
                                    ]),
                                );

                                $registryPendingUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'registry_pending',
                                    ]),
                                );

                                $scUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'sc',
                                    ]),
                                );

                                $ghumantuUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'ghumantu',
                                    ]),
                                );

                                $widowUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'widow',
                                    ]),
                                );

                                $othersUrl = route(
                                    'district.dashboard.applicants',
                                    array_merge($rowParams, [
                                        'status' => 'others',
                                    ]),
                                );

                                /*
                        |--------------------------------------------------------------------------
                        | Possession link
                        |--------------------------------------------------------------------------
                        */
                                $possessionUrl = route(
                                    'district.possession.list',
                                    array_merge(
                                        [
                                            'filter' => 'all',
                                        ],
                                        $rowParams,
                                    ),
                                );

                                /*
                        |--------------------------------------------------------------------------
                        | Village report link for plots
                        |--------------------------------------------------------------------------
                        */
                                $plotsUrl = route('district.dashboard.report', [
                                    'type' => 'villages',
                                    'phase' => $row->Phase ?? 'all',
                                    'village_id' => $row->VillageId,
                                ]);
                            @endphp

                            <tr class="transition hover:bg-blue-50/70">

                                {{-- Sr. No. --}}
                                <td class="px-3 py-3 text-slate-500">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Village + Site Development + Map --}}
                                <td class="px-3 py-3">

                                    <div class="flex items-center gap-2">

                                        {{-- Site Development --}}
                                        <button type="button" title="Site Development"
                                            class="siteDevelopmentBtn inline-flex
                                           h-9 w-9 shrink-0 items-center
                                           justify-center rounded-lg
                                           bg-cyan-100 text-cyan-700
                                           transition hover:bg-cyan-600
                                           hover:text-white"
                                            data-village-id="{{ $row->VillageId }}"
                                            data-village-name="{{ $row->VillageName }}" data-phase="{{ $row->Phase }}">

                                            <span
                                                class="material-symbols-outlined
                                               text-[18px]">
                                                construction
                                            </span>
                                        </button>

                                        {{-- Map --}}
                                        @if ($mapPdfUrl)
                                            <button type="button" title="View Village Map"
                                                class="villageMapBtn inline-flex h-9
                                               shrink-0 items-center
                                               justify-center gap-1
                                               rounded-lg border
                                               border-indigo-200
                                               bg-indigo-50 px-2.5
                                               text-xs font-semibold
                                               text-indigo-700 transition
                                               hover:border-indigo-600
                                               hover:bg-indigo-600
                                               hover:text-white"
                                                data-pdf-url="{{ $mapPdfUrl }}" data-pdf-name="{{ $row->PdfFile }}"
                                                data-village-name="{{ $row->VillageName }}"
                                                data-phase="{{ $row->Phase }}">

                                                <span
                                                    class="material-symbols-outlined
                                                   text-[17px]">
                                                    map
                                                </span>

                                                Map
                                            </button>
                                        @endif

                                        {{-- Village name --}}
                                        <a href="{{ $allApplicantsUrl }}"
                                            class="min-w-0 rounded-md px-2 py-1
                                           font-bold text-slate-800
                                           transition hover:bg-slate-800
                                           hover:text-white">

                                            {{ $row->VillageName ?? '-' }}
                                        </a>

                                    </div>

                                </td>

                                {{-- Phase --}}
                                <td
                                    class="px-3 py-3 text-center
                                   font-medium text-slate-600">
                                    {{ $row->Phase ?? '-' }}
                                </td>

                                {{-- Total Plots --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $plotsUrl }}"
                                        class="inline-flex min-w-[58px]
                                       justify-center rounded-md
                                       bg-slate-100 px-2 py-1
                                       font-semibold text-slate-700
                                       transition hover:bg-slate-700
                                       hover:text-white">

                                        {{ number_format($row->TotalPlots ?? 0) }}
                                    </a>

                                </td>

                                {{-- Applicants --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $allApplicantsUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-blue-50 px-2 py-1
                                       font-semibold text-blue-600
                                       transition hover:bg-blue-600
                                       hover:text-white">

                                        {{ number_format($row->TotalApplicants ?? 0) }}
                                    </a>

                                </td>

                                {{-- Allotted --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $allottedUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-cyan-50 px-2 py-1
                                       font-bold text-cyan-700
                                       transition hover:bg-cyan-700
                                       hover:text-white">

                                        {{ number_format($row->TotalAllotment ?? 0) }}
                                    </a>

                                </td>

                                {{-- Approved Paid --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $approvedPaidUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-emerald-50 px-2 py-1
                                       font-semibold text-emerald-700
                                       transition hover:bg-emerald-600
                                       hover:text-white">

                                        {{ number_format($row->ApprovedPaid ?? 0) }}
                                    </a>

                                </td>

                                {{-- Approved Unpaid --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $approvedUnpaidUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-sky-50 px-2 py-1
                                       font-semibold text-sky-700
                                       transition hover:bg-sky-600
                                       hover:text-white">

                                        {{ number_format($row->ApprovedUnpaid ?? 0) }}
                                    </a>

                                </td>

                                {{-- Pending --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $pendingUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-amber-50 px-2 py-1
                                       font-semibold text-amber-700
                                       transition hover:bg-amber-600
                                       hover:text-white">

                                        {{ number_format($row->PendingApproval ?? 0) }}
                                    </a>

                                </td>

                                {{-- Rejected --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $rejectedUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-rose-50 px-2 py-1
                                       font-semibold text-rose-700
                                       transition hover:bg-rose-600
                                       hover:text-white">

                                        {{ number_format($row->Rejected ?? 0) }}
                                    </a>

                                </td>

                                {{-- Cancelled --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $cancelledUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-slate-100 px-2 py-1
                                       font-semibold text-slate-700
                                       transition hover:bg-slate-700
                                       hover:text-white">

                                        {{ number_format($row->Cancelled ?? 0) }}
                                    </a>

                                </td>

                                {{-- Registry Done --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $registryDoneUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-green-50 px-2 py-1
                                       font-semibold text-green-700
                                       transition hover:bg-green-600
                                       hover:text-white">

                                        {{ number_format($row->RegistryDone ?? 0) }}
                                    </a>

                                </td>

                                {{-- Registry Pending --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $registryPendingUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-orange-50 px-2 py-1
                                       font-semibold text-orange-700
                                       transition hover:bg-orange-600
                                       hover:text-white">

                                        {{ number_format($row->RegistryPending ?? 0) }}
                                    </a>

                                </td>

                                {{-- Possession --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ $possessionUrl }}"
                                        class="inline-flex min-w-[60px]
                                       justify-center rounded-md
                                       bg-violet-50 px-2 py-1
                                       font-semibold text-violet-700
                                       transition hover:bg-violet-600
                                       hover:text-white">

                                        {{ number_format($row->Possession ?? 0) }}
                                    </a>

                                </td>

                                {{-- SC --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $row->Phase ?? 'all',
                                        'village_id' => $row->VillageId,
                                        'status' => 'all_applicants',
                                        'caste' => 'SC',
                                    ]) }}"
                                        class="inline-flex min-w-[50px]
                                       justify-center rounded-md
                                       bg-indigo-50 px-2 py-1
                                       font-semibold text-indigo-700
                                       transition hover:bg-indigo-600
                                       hover:text-white">

                                        {{ number_format($row->SC ?? 0) }}
                                    </a>

                                </td>

                                {{-- Ghumantu --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $row->Phase ?? 'all',
                                        'village_id' => $row->VillageId,
                                        'status' => 'all_applicants',
                                        'caste' => 'Ghumantu',
                                    ]) }}"
                                        class="inline-flex min-w-[50px]
                                       justify-center rounded-md
                                       bg-purple-50 px-2 py-1
                                       font-semibold text-purple-700
                                       transition hover:bg-purple-600
                                       hover:text-white">

                                        {{ number_format($row->Ghumantu ?? 0) }}
                                    </a>

                                </td>

                                {{-- Widow --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $row->Phase ?? 'all',
                                        'village_id' => $row->VillageId,
                                        'status' => 'all_applicants',
                                        'caste' => 'Widow',
                                    ]) }}"
                                        class="inline-flex min-w-[50px]
                                       justify-center rounded-md
                                       bg-pink-50 px-2 py-1
                                       font-semibold text-pink-700
                                       transition hover:bg-pink-600
                                       hover:text-white">

                                        {{ number_format($row->Widow ?? 0) }}
                                    </a>

                                </td>

                                {{-- Others --}}
                                <td class="px-3 py-3 text-center">

                                    <a href="{{ route('district.dashboard.applicants', [
                                        'phase' => $row->Phase ?? 'all',
                                        'village_id' => $row->VillageId,
                                        'status' => 'all_applicants',
                                        'caste' => 'Others',
                                    ]) }}"
                                        class="inline-flex min-w-[50px] justify-center rounded-md
                                                bg-yellow-50 px-2 py-1
                                                font-semibold text-yellow-700
                                                transition hover:bg-yellow-600
                                                hover:text-white">

                                        {{ number_format($row->Others ?? 0) }}
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="18" class="px-6 py-14 text-center">

                                    <div
                                        class="mx-auto flex h-14 w-14
                                       items-center justify-center
                                       rounded-full bg-slate-100">

                                        <span
                                            class="material-symbols-outlined
                                           text-[30px] text-slate-400">
                                            folder_off
                                        </span>
                                    </div>

                                    <p class="mt-3 font-semibold
                                       text-slate-700">
                                        No village records found
                                    </p>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                    @if ($reportData->isNotEmpty())
                        <tfoot
                            class="border-t-2 border-slate-300
                           bg-slate-100 font-bold text-slate-800">

                            <tr>

                                <td colspan="3" class="px-3 py-3">
                                    Grand Total
                                </td>

                                <td class="px-3 py-3 text-center">
                                    {{ number_format($totals['totalPlots'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center">
                                    {{ number_format($totals['totalApplicants'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center text-cyan-700">
                                    {{ number_format($totals['totalAllotment'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center
                                   text-emerald-700">
                                    {{ number_format($totals['approvedPaid'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center text-sky-700">
                                    {{ number_format($totals['approvedUnpaid'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center
                                   text-amber-700">
                                    {{ number_format($totals['pending'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center
                                   text-rose-700">
                                    {{ number_format($totals['rejected'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center">
                                    {{ number_format($totals['cancelled'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center
                                   text-green-700">
                                    {{ number_format($totals['registryDone'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center
                                   text-orange-700">
                                    {{ number_format($totals['registryPending'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center
                                   text-violet-700">
                                    {{ number_format($totals['totalPossession'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center">
                                    {{ number_format($totals['sc'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center">
                                    {{ number_format($totals['ghumantu'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center">
                                    {{ number_format($totals['widow'] ?? 0) }}
                                </td>

                                <td class="px-3 py-3 text-center">
                                    {{ number_format($totals['others'] ?? 0) }}
                                </td>

                            </tr>

                        </tfoot>
                    @endif

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
