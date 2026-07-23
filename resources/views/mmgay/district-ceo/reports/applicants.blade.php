@extends('layouts.mmgayCEOAuth')

@section('title', 'Applicants Report')

@section('content')

    @php
        $currentFilters = array_filter(
            [
                'phase' => $phase ?? 'all',
                'village_id' => $villageId ?? null,
                'status' => $status ?? null,
                'caste' => $caste ?? null,
                'search' => $search ?? null,
                'per_page' => $perPage ?? 50,
            ],
            static fn($value) => $value !== null && $value !== '',
        );

        $exportFilters = array_merge($currentFilters, [
            'type' => 'applicants',
        ]);
    @endphp

    <main class="mt-[68px] min-h-screen bg-slate-50 p-4
                 lg:ml-[230px] lg:w-[calc(100%-230px)] lg:p-5">

        <div class="mx-auto max-w-[1900px]">

            {{-- Breadcrumb --}}
            <div class="mb-4 flex flex-wrap items-center gap-2 text-sm text-slate-500 print:hidden">

                <a href="{{ url('/district-ceo/dashboard/' . ($phase ?? 'all')) }}"
                    class="font-medium transition hover:text-blue-600">
                    Dashboard
                </a>

                <span class="material-symbols-outlined text-[16px]">
                    chevron_right
                </span>

                <span class="font-semibold text-slate-800">
                    Applicants Report
                </span>

            </div>

            {{-- Header --}}
            <section
                class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm print:border-0 print:shadow-none">

                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

                    <div class="flex items-start gap-4">

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 print:hidden">

                            <span class="material-symbols-outlined text-[26px] text-indigo-600">
                                groups
                            </span>

                        </div>

                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                                Applicants Report
                            </h1>

                            <p class="mt-1 text-sm text-slate-500">
                                Applicant, allotment, approval and registry details
                            </p>

                            <div class="mt-2 flex flex-wrap gap-2">

                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    {{ ($phase ?? 'all') === 'all' ? 'All Phases' : 'Phase ' . $phase }}
                                </span>

                                @if (!empty($villageId))
                                    @php
                                        $selectedVillage = collect($villages ?? [])->firstWhere(
                                            'VillageId',
                                            $villageId,
                                        );
                                    @endphp

                                    @if ($selectedVillage)
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            {{ $selectedVillage->VillageName }}
                                        </span>
                                    @endif
                                @endif

                            </div>
                        </div>

                    </div>

                    {{-- Export Buttons --}}
                    <div class="flex flex-wrap gap-2 print:hidden">

                        <a href="{{ route('district.dashboard.report', array_merge($exportFilters, ['format' => 'pdf'])) }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100">

                            <span class="material-symbols-outlined text-[19px]">
                                picture_as_pdf
                            </span>

                            PDF
                        </a>

                        <a href="{{ route('district.dashboard.report', array_merge($exportFilters, ['format' => 'excel'])) }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">

                            <span class="material-symbols-outlined text-[19px]">
                                table_view
                            </span>

                            Excel
                        </a>

                        <a href="{{ route('district.dashboard.report', array_merge($exportFilters, ['format' => 'csv'])) }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">

                            <span class="material-symbols-outlined text-[19px]">
                                csv
                            </span>

                            CSV
                        </a>

                        <a href="{{ route('district.dashboard.applicants.print', $exportFilters) }}" target="_blank"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">

                            <span class="material-symbols-outlined text-[19px]">
                                print
                            </span>

                            Print
                        </a>

                    </div>

                </div>

            </section>

            {{-- Filters --}}
            <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm print:hidden">

                <div class="mb-4 flex items-center justify-between gap-3">

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Filter Applicants
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Select filters and click Apply Filters
                        </p>
                    </div>

                    <span class="material-symbols-outlined text-slate-400">
                        filter_alt
                    </span>

                </div>

                <form id="applicantFilterForm" method="GET"
                    action="{{ route('district.dashboard.applicants', ['type' => 'applicants']) }}">

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6">

                        {{-- Phase --}}
                        <div>
                            <label for="phase_filter"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Phase
                            </label>

                            <select id="phase_filter" name="phase"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="all" @selected(($phase ?? 'all') === 'all')>
                                    All Phases
                                </option>

                                @foreach ([1, 2, 3] as $phaseOption)
                                    <option value="{{ $phaseOption }}" @selected((string) ($phase ?? 'all') === (string) $phaseOption)>
                                        Phase {{ $phaseOption }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Village --}}
                        <div>
                            <label for="village_filter"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Village
                            </label>

                            <select id="village_filter" name="village_id"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="">
                                    All Villages
                                </option>

                                @foreach ($villages ?? [] as $village)
                                    <option value="{{ $village->VillageId }}" data-phase="{{ $village->phase }}"
                                        @selected((string) ($villageId ?? '') === (string) $village->VillageId)>

                                        {{ $village->VillageName }}

                                        @if (($phase ?? 'all') === 'all')
                                            (Phase {{ $village->phase }})
                                        @endif
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status_filter"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </label>

                            <select id="status_filter" name="status"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="all_applicants" @selected(($status ?? 'allotted') === 'all_applicants')>
                                    All Applicants
                                </option>

                                <option value="allotted" @selected(($status ?? 'allotted') === 'allotted')>
                                    Allotted
                                </option>

                                <option value="approved_paid" @selected(($status ?? '') === 'approved_paid')>
                                    Approved & Paid
                                </option>

                                <option value="approved_unpaid" @selected(($status ?? '') === 'approved_unpaid')>
                                    Approved & Unpaid
                                </option>

                                <option value="pending" @selected(($status ?? '') === 'pending')>
                                    Yet to be Approved
                                </option>

                                <option value="rejected" @selected(($status ?? '') === 'rejected')>
                                    Rejected
                                </option>

                                <option value="cancelled" @selected(($status ?? '') === 'cancelled')>
                                    Cancelled
                                </option>

                                <option value="registry_allotted" @selected(($status ?? '') === 'registry_allotted')>
                                    Registry To Be Done
                                </option>

                                <option value="registry_done" @selected(($status ?? '') === 'registry_done')>
                                    Registry Done
                                </option>

                                <option value="registry_pending" @selected(($status ?? '') === 'registry_pending')>
                                    Registry Pending
                                </option>

                            </select>
                        </div>

                        {{-- Caste --}}
                        <div>
                            <label for="caste_filter"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Caste / Category
                            </label>

                            <select id="caste_filter" name="caste"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="">All Categories</option>

                                @foreach (['SC', 'Ghumantu', 'Widow', 'General', 'Others'] as $casteOption)
                                    <option value="{{ $casteOption }}" @selected(($caste ?? '') === $casteOption)>
                                        {{ $casteOption }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Search --}}
                        <div>
                            <label for="search_filter"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Search
                            </label>

                            <div class="relative">

                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                                    search
                                </span>

                                <input id="search_filter" type="text" name="search" value="{{ $search ?? '' }}"
                                    placeholder="Name, mobile, registration..."
                                    class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                            </div>
                        </div>

                        {{-- Per Page --}}
                        <div>
                            <label for="per_page_filter"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Records Per Page
                            </label>

                            <select id="per_page_filter" name="per_page"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                @foreach ([25, 50, 100, 200] as $pageSize)
                                    <option value="{{ $pageSize }}" @selected((int) ($perPage ?? 50) === $pageSize)>
                                        {{ $pageSize }} Records
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                    <div class="mt-5 flex flex-wrap justify-end gap-2">

                        <a href="{{ route('district.dashboard.report', [
                            'type' => 'applicants',
                            'phase' => 'all',
                            'status' => 'allotted',
                        ]) }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">

                            <span class="material-symbols-outlined text-[19px]">
                                restart_alt
                            </span>

                            Reset
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">

                            <span class="material-symbols-outlined text-[19px]">
                                filter_alt
                            </span>

                            Apply Filters
                        </button>

                    </div>

                </form>

            </section>

            {{-- Status Summary Cards --}}
            @php
                $summaryCards = [
                    [
                        'label' => 'Applicants',
                        'status' => 'all_applicants',
                        'count' => $statusCounts->totalApplicants ?? 0,
                        'icon' => 'groups',
                        'classes' => 'border-slate-200 text-slate-700 bg-white',
                        'icon_classes' => 'bg-slate-100 text-slate-600',
                    ],
                    [
                        'label' => 'Allotted',
                        'status' => 'allotted',
                        'count' => $statusCounts->totalAllotted ?? 0,
                        'icon' => 'home_work',
                        'classes' => 'border-blue-200 text-blue-700 bg-blue-50/40',
                        'icon_classes' => 'bg-blue-100 text-blue-700',
                    ],
                    [
                        'label' => 'Approved & Paid',
                        'status' => 'approved_paid',
                        'count' => $statusCounts->totalApprovedPaid ?? 0,
                        'icon' => 'verified',
                        'classes' => 'border-emerald-200 text-emerald-700 bg-emerald-50/40',
                        'icon_classes' => 'bg-emerald-100 text-emerald-700',
                    ],
                    [
                        'label' => 'Approved & Unpaid',
                        'status' => 'approved_unpaid',
                        'count' => $statusCounts->totalApprovedUnpaid ?? 0,
                        'icon' => 'payments',
                        'classes' => 'border-cyan-200 text-cyan-700 bg-cyan-50/40',
                        'icon_classes' => 'bg-cyan-100 text-cyan-700',
                    ],
                    [
                        'label' => 'Yet to be Approved',
                        'status' => 'pending',
                        'count' => $statusCounts->totalPending ?? 0,
                        'icon' => 'pending_actions',
                        'classes' => 'border-amber-200 text-amber-700 bg-amber-50/40',
                        'icon_classes' => 'bg-amber-100 text-amber-700',
                    ],
                    [
                        'label' => 'Rejected',
                        'status' => 'rejected',
                        'count' => $statusCounts->totalRejected ?? 0,
                        'icon' => 'cancel',
                        'classes' => 'border-rose-200 text-rose-700 bg-rose-50/40',
                        'icon_classes' => 'bg-rose-100 text-rose-700',
                    ],
                    [
                        'label' => 'Cancelled',
                        'status' => 'cancelled',
                        'count' => $statusCounts->totalCancelled ?? 0,
                        'icon' => 'block',
                        'classes' => 'border-slate-300 text-slate-700 bg-slate-50',
                        'icon_classes' => 'bg-slate-200 text-slate-700',
                    ],
                    [
                        'label' => 'Registry To Be Done',
                        'status' => 'registry_allotted',
                        'count' => $statusCounts->totalRegistryAllotted ?? 0,
                        'icon' => 'assignment',
                        'classes' => 'border-indigo-200 text-indigo-700 bg-indigo-50/40',
                        'icon_classes' => 'bg-indigo-100 text-indigo-700',
                    ],
                    [
                        'label' => 'Registry Done',
                        'status' => 'registry_done',
                        'count' => $statusCounts->totalRegistryDone ?? 0,
                        'icon' => 'task_alt',
                        'classes' => 'border-green-200 text-green-700 bg-green-50/40',
                        'icon_classes' => 'bg-green-100 text-green-700',
                    ],
                    [
                        'label' => 'Registry Pending',
                        'status' => 'registry_pending',
                        'count' => $statusCounts->totalRegistryPending ?? 0,
                        'icon' => 'schedule',
                        'classes' => 'border-orange-200 text-orange-700 bg-orange-50/40',
                        'icon_classes' => 'bg-orange-100 text-orange-700',
                    ],
                ];
            @endphp

            <section class="mb-5 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5 2xl:grid-cols-10 print:hidden">
                @foreach ($summaryCards as $card)
                    @php
                        $cardFilters = array_filter(
                            [
                                'phase' => $phase ?? 'all',
                                'village_id' => $villageId ?? null,
                                'status' => $card['status'],
                                'caste' => $caste ?? null,
                                'search' => $search ?? null,
                                'per_page' => $perPage ?? 50,
                            ],
                            static fn($value) => $value !== null && $value !== '',
                        );

                        $isActive = ($status ?? 'allotted') === $card['status'];
                    @endphp

                    <a href="{{ route('district.dashboard.applicants', $cardFilters) }}"
                        class="group rounded-xl border p-3 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md
                            {{ $card['classes'] }}
                            {{ $isActive ? 'ring-2 ring-blue-500 ring-offset-1' : '' }}">

                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="min-h-[30px] text-[10px] font-bold uppercase leading-4 tracking-wide">
                                    {{ $card['label'] }}
                                </p>

                                <p class="mt-1 text-xl font-extrabold leading-none">
                                    {{ number_format((int) $card['count']) }}
                                </p>
                            </div>

                            <span
                                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] {{ $card['icon_classes'] }}">
                                {{ $card['icon'] }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </section>

            {{-- Table Header --}}
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">

                <p class="text-sm text-slate-600">

                    Showing

                    <span class="font-semibold text-slate-900">
                        {{ number_format($applicants->firstItem() ?? 0) }}
                    </span>

                    to

                    <span class="font-semibold text-slate-900">
                        {{ number_format($applicants->lastItem() ?? 0) }}
                    </span>

                    of

                    <span class="font-semibold text-slate-900">
                        {{ number_format($applicants->total() ?? 0) }}
                    </span>

                    applicants

                </p>

                <p class="text-xs text-slate-500 print:hidden">
                    Scroll horizontally to view all columns
                </p>

            </div>

            {{-- Applicant Table --}}
            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm print:border-0 print:shadow-none">

                <div class="overflow-x-auto">

                    <table id="applicantReportTable" class="w-full min-w-[2900px] border-collapse text-sm">

                        <thead class="bg-slate-800 text-xs uppercase tracking-wide text-white">

                            <tr>
                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    #
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Registration No.
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Applicant Name
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Relation
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Father / Husband
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    Gender
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    Caste
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Mobile No.
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    PPP ID
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Member ID
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    Phase
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Village
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    Plot No.
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    Allotment
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    Applicant Status
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    Payment
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-center">
                                    Registry
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Remarks
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    DC Remarks
                                </th>

                                <th class="border-r border-slate-700 px-4 py-4 text-left">
                                    Address
                                </th>

                                <th class="px-4 py-4 text-center">
                                    Created Date
                                </th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-200">

                            @forelse ($applicants as $applicant)

                                @php
                                    $applicantStatus = $applicant->ApplicantStatus ?? 'Yet to be Approved';

                                    $statusClass = match ($applicantStatus) {
                                        'Approved & Paid' => 'bg-emerald-100 text-emerald-700',

                                        'Approved & Unpaid' => 'bg-cyan-100 text-cyan-700',

                                        'Rejected' => 'bg-red-100 text-red-700',

                                        'Cancelled' => 'bg-slate-200 text-slate-700',

                                        default => 'bg-amber-100 text-amber-700',
                                    };

                                    $registryStatus = $applicant->RegistryStatus ?? 'Not Applicable';

                                    $registryClass = match ($registryStatus) {
                                        'Registry Done' => 'bg-emerald-100 text-emerald-700',

                                        'Registry Pending' => 'bg-orange-100 text-orange-700',

                                        default => 'bg-slate-100 text-slate-600',
                                    };

                                    $paymentStatus =
                                        (int) ($applicant->IsPaid ?? 0) === 1
                                            ? 'Paid'
                                            : ((int) ($applicant->IsApproved ?? 0) === 1
                                                ? 'Unpaid'
                                                : 'Not Applicable');

                                    $paymentClass = match ($paymentStatus) {
                                        'Paid' => 'bg-emerald-100 text-emerald-700',

                                        'Unpaid' => 'bg-amber-100 text-amber-700',

                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp

                                <tr class="transition hover:bg-blue-50/70">

                                    <td
                                        class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center text-slate-500">
                                        {{ ($applicants->firstItem() ?? 1) + $loop->index }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap border-r border-slate-100 px-4 py-3 font-semibold text-slate-800">
                                        {{ $applicant->RegistrationNo ?: '—' }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap border-r border-slate-100 px-4 py-3 font-semibold text-slate-900">
                                        {{ $applicant->OwnerName ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-slate-700">
                                        {{ $applicant->Relation ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-slate-700">
                                        {{ $applicant->FatherHusbandName ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center">
                                        {{ $applicant->Gender ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center">
                                        {{ $applicant->Caste ?: 'Others' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3">
                                        {{ $applicant->MobileNo ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3">
                                        {{ $applicant->PPPId ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3">
                                        {{ $applicant->MemberId ?: '—' }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center font-semibold text-blue-700">
                                        {{ $applicant->Phase ? 'Phase ' . $applicant->Phase : '—' }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap border-r border-slate-100 px-4 py-3 font-medium text-slate-800">
                                        {{ $applicant->VillageName ?: '—' }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center font-semibold text-indigo-700">
                                        {{ $applicant->FlatNo ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center">
                                        {{ $applicant->AllotmentStatus ?? 'Not Allotted' }}
                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center">

                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ $applicantStatus }}
                                        </span>

                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center">

                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $paymentClass }}">
                                            {{ $paymentStatus }}
                                        </span>

                                    </td>

                                    <td class="whitespace-nowrap border-r border-slate-100 px-4 py-3 text-center">

                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $registryClass }}">
                                            {{ $registryStatus }}
                                        </span>

                                    </td>

                                    <td class="max-w-[320px] border-r border-slate-100 px-4 py-3 text-slate-700">

                                        <div class="line-clamp-2" title="{{ $applicant->Remarks }}">
                                            {{ $applicant->Remarks ?: '—' }}
                                        </div>

                                    </td>

                                    <td class="max-w-[320px] border-r border-slate-100 px-4 py-3 text-slate-700">

                                        <div class="line-clamp-2" title="{{ $applicant->DCRemarks }}">
                                            {{ $applicant->DCRemarks ?: '—' }}
                                        </div>

                                    </td>

                                    <td class="max-w-[360px] border-r border-slate-100 px-4 py-3 text-slate-600">

                                        <div class="line-clamp-2" title="{{ $applicant->OwnerAddress }}">
                                            {{ $applicant->OwnerAddress ?: '—' }}
                                        </div>

                                    </td>

                                    <td class="whitespace-nowrap px-4 py-3 text-center text-slate-600">
                                        {{ !empty($applicant->CreatedDate) ? \Carbon\Carbon::parse($applicant->CreatedDate)->format('d-m-Y') : '—' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="21" class="px-6 py-16 text-center">

                                        <div
                                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">

                                            <span class="material-symbols-outlined text-[28px] text-slate-400">
                                                person_search
                                            </span>

                                        </div>

                                        <h3 class="mt-4 font-semibold text-slate-800">
                                            No applicants found
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Try changing the selected filters.
                                        </p>

                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if ($applicants->hasPages())
                    <div
                        class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4
               sm:flex-row sm:items-center sm:justify-between print:hidden">

                        <p class="text-sm text-slate-600">

                        </p>

                        <div class="overflow-x-auto">
                            {{ $applicants->onEachSide(1)->links('pagination::tailwind') }}
                        </div>

                    </div>
                @endif

            </section>

        </div>

    </main>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phaseFilter = document.getElementById('phase_filter');
            const villageFilter = document.getElementById('village_filter');

            if (!phaseFilter || !villageFilter) {
                return;
            }

            phaseFilter.addEventListener('change', function() {
                villageFilter.value = '';
            });
        });

        function printApplicantReport() {
            window.print();
        }
    </script>
@endpush

@push('styles')
    <style>
        @media print {

            aside,
            header,
            nav,
            form,
            button,
            .print\:hidden {
                display: none !important;
            }

            body {
                background: #ffffff !important;
            }

            main {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            section {
                border: 0 !important;
                box-shadow: none !important;
            }

            #applicantReportTable {
                width: 100% !important;
                min-width: 100% !important;
                font-size: 7px !important;
            }

            #applicantReportTable thead {
                display: table-header-group;
            }

            #applicantReportTable tr {
                page-break-inside: avoid;
            }

            #applicantReportTable th,
            #applicantReportTable td {
                padding: 3px !important;
                white-space: normal !important;
            }

            @page {
                size: A3 landscape;
                margin: 8mm;
            }
        }
    </style>
@endpush