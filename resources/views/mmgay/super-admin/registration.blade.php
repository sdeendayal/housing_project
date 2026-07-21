@extends('layouts.mmgayAdmin')

@section('title', 'Registry Report')

@section('content')

     @php
        $exportFilters = [
            'type' => request('type', $type ?? 'matched'),
            'phase' => request('phase'),
            'district_id' => request('district_id'),
            'block_id' => request('block_id'),
            'village_id' => request('village_id'),
            'search' => request('search'),
        ];
    @endphp

    @php
        $activeType = request('type', $type ?? 'matched');

        $dashboardFilters = request()->only(['phase', 'district_id', 'block_id', 'village_id']);

        $currentFilters = request()->only(['type', 'phase', 'district_id', 'block_id', 'village_id', 'search']);

        $cardUrl = function (string $cardType) use ($dashboardFilters) {
            return route(
                'admin.registration',
                array_merge($dashboardFilters, [
                    'type' => $cardType,
                ]),
            );
        };

        $typeLabels = [
            'all' => 'All Registry Records',
            'unique_registry' => 'Unique Registry Records',
            'duplicate_registry' => 'Duplicate Registry Records',
            'blank_registry' => 'Records with Missing Registry Numbers',
            'matched' => 'Matched Registry Records',
            'unmatched' => 'Unmatched Registry Records',
            'unique_matched_mobile' => 'Unique Matched Mobile Records',
            'repeated_matched_mobile' => 'Repeated Matched-Mobile Records',
        ];

        $activeLabel = $typeLabels[$activeType] ?? 'Registry Records';

        $resetSearchUrl = route('admin.registration', array_merge($dashboardFilters, ['type' => $activeType]));
    @endphp   

    <main class="min-h-screen bg-slate-100 p-5 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

        {{-- Header --}}
        <div class="mb-5 rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm">

            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">
                        Registry Report
                    </h1>

                    <p class="mt-1 text-xs text-slate-500">
                        Review registry numbers, duplicate entries and mobile matches with OwnerMaster.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">

                    @if (request()->filled('phase'))
                        <span class="rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700">
                            Phase: {{ request('phase') }}
                        </span>
                    @endif

                    @if (request()->filled('district_id'))
                        <span class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">
                            District ID: {{ request('district_id') }}
                        </span>
                    @endif

                    @if (request()->filled('block_id'))
                        <span class="rounded-lg bg-cyan-50 px-3 py-2 text-xs font-semibold text-cyan-700">
                            Block ID: {{ request('block_id') }}
                        </span>
                    @endif

                    @if (request()->filled('village_id'))
                        <span class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                            Village ID: {{ request('village_id') }}
                        </span>
                    @endif

                    <a href="{{ route('admin.registration.export.excel', $exportFilters) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700">

                        <span class="material-symbols-outlined text-[18px]">
                            table_view
                        </span>

                        Export Excel
                    </a>

                    <a href="{{ route('admin.registration.export.pdf', $exportFilters) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700">

                        <span class="material-symbols-outlined text-[18px]">
                            picture_as_pdf
                        </span>

                        Export PDF
                    </a>

                    <a href="{{ route('admin.registration') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">

                        <span class="material-symbols-outlined text-[18px]">
                            restart_alt
                        </span>

                        Reset
                    </a>

                </div>

            </div>

        </div>

        {{-- Registry Number Summary --}}
        <div class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

            <div>
                <h2 class="text-base font-bold text-slate-800">
                    Registry Number Summary
                </h2>

                <p class="mt-0.5 text-xs text-slate-500">
                    Total rows are divided into unique, duplicate and missing registry-number records.
                </p>
            </div>

            <div
                class="inline-flex w-fit items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">

                <span class="material-symbols-outlined text-[16px]">
                    calculate
                </span>

                {{ number_format($totalRegistrations ?? 0) }}
                =
                {{ number_format($uniqueRegistrations ?? 0) }}
                +
                {{ number_format($duplicateRegistrations ?? 0) }}
                +
                {{ number_format($blankRegistryNumbers ?? 0) }}

            </div>

        </div>

        {{-- Registry Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

            {{-- All Records --}}
            <a href="{{ $cardUrl('all') }}"
                class="group rounded-xl border bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
            {{ $activeType === 'all' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200' }}">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-slate-500">
                            Total Registry Rows
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-900">
                            {{ number_format($totalRegistrations ?? 0) }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-500">
                            All physical registry records
                        </p>
                    </div>

                    <span class="material-symbols-outlined rounded-lg bg-blue-100 p-2 text-[20px] text-blue-700">
                        description
                    </span>

                </div>

                <div class="mt-2 flex items-center gap-1 text-[11px] font-semibold text-blue-600">
                    View records

                    <span class="material-symbols-outlined text-[14px]">
                        arrow_forward
                    </span>
                </div>

            </a>

            {{-- Unique Registry --}}
            <a href="{{ $cardUrl('unique_registry') }}"
                class="group rounded-xl border bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
            {{ $activeType === 'unique_registry' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200' }}">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-slate-500">
                            Unique Registry Numbers
                        </p>

                        <p class="mt-1 text-2xl font-bold text-emerald-600">
                            {{ number_format($uniqueRegistrations ?? 0) }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-500">
                            One row per registry number
                        </p>
                    </div>

                    <span class="material-symbols-outlined rounded-lg bg-emerald-100 p-2 text-[20px] text-emerald-700">
                        verified
                    </span>

                </div>

                <div class="mt-2 flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    View unique records

                    <span class="material-symbols-outlined text-[14px]">
                        arrow_forward
                    </span>
                </div>

            </a>

            {{-- Duplicate Registry --}}
            <a href="{{ $cardUrl('duplicate_registry') }}"
                class="group rounded-xl border bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
            {{ $activeType === 'duplicate_registry' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200' }}">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-slate-500">
                            Duplicate Registry Rows
                        </p>

                        <p class="mt-1 text-2xl font-bold text-amber-600">
                            {{ number_format($duplicateRegistrations ?? 0) }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-500">
                            Repeated registry-number rows
                        </p>
                    </div>

                    <span class="material-symbols-outlined rounded-lg bg-amber-100 p-2 text-[20px] text-amber-700">
                        content_copy
                    </span>

                </div>

                <div class="mt-2 flex items-center gap-1 text-[11px] font-semibold text-amber-600">
                    Review duplicates

                    <span class="material-symbols-outlined text-[14px]">
                        arrow_forward
                    </span>
                </div>

            </a>

            {{-- Blank Registry --}}
            <a href="{{ $cardUrl('blank_registry') }}"
                class="group rounded-xl border bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
            {{ $activeType === 'blank_registry' ? 'border-slate-500 ring-2 ring-slate-200' : 'border-slate-200' }}">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-slate-500">
                            Missing Registry Numbers
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-700">
                            {{ number_format($blankRegistryNumbers ?? 0) }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-500">
                            Null or empty registry numbers
                        </p>
                    </div>

                    <span class="material-symbols-outlined rounded-lg bg-slate-100 p-2 text-[20px] text-slate-600">
                        unknown_document
                    </span>

                </div>

                <div class="mt-2 flex items-center gap-1 text-[11px] font-semibold text-slate-600">
                    View missing records

                    <span class="material-symbols-outlined text-[14px]">
                        arrow_forward
                    </span>
                </div>

            </a>

        </div>

        {{-- Mobile Match Summary --}}
        <div class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

            <div>
                <h2 class="text-base font-bold text-slate-800">
                    Mobile Matching Summary
                </h2>

                <p class="mt-0.5 text-xs text-slate-500">
                    Registry rows are classified by mobile-number availability in OwnerMaster.
                </p>
            </div>

            <div
                class="inline-flex w-fit items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">

                <span class="material-symbols-outlined text-[16px]">
                    account_tree
                </span>

                {{ number_format($totalRegistrations ?? 0) }}
                =
                {{ number_format($matchedRegistrations ?? 0) }}
                +
                {{ number_format($unmatchedRegistrations ?? 0) }}

            </div>

        </div>

        {{-- Mobile Match Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Matched --}}
            <a href="{{ $cardUrl('matched') }}"
                class="rounded-xl border bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
            {{ $activeType === 'matched' ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200' }}">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Matched Registry Rows
                        </p>

                        <p class="mt-1 text-2xl font-bold text-indigo-600">
                            {{ number_format($matchedRegistrations ?? 0) }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-500">
                            Mobile found in OwnerMaster
                        </p>
                    </div>

                    <span class="material-symbols-outlined rounded-lg bg-indigo-100 p-2 text-[20px] text-indigo-700">
                        person_check
                    </span>

                </div>

                <div class="mt-2 text-[11px] font-semibold text-indigo-600">
                    View matched records →
                </div>

            </a>

            {{-- Unmatched --}}
            <a href="{{ $cardUrl('unmatched') }}"
                class="rounded-xl border bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
            {{ $activeType === 'unmatched' ? 'border-rose-500 ring-2 ring-rose-100' : 'border-slate-200' }}">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Unmatched Registry Rows
                        </p>

                        <p class="mt-1 text-2xl font-bold text-rose-600">
                            {{ number_format($unmatchedRegistrations ?? 0) }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-500">
                            Mobile not found in OwnerMaster
                        </p>
                    </div>

                    <span class="material-symbols-outlined rounded-lg bg-rose-100 p-2 text-[20px] text-rose-700">
                        person_off
                    </span>

                </div>

                <div class="mt-2 text-[11px] font-semibold text-rose-600">
                    View unmatched records →
                </div>

            </a>

            {{-- Unique Matched Mobile --}}
            <a href="{{ $cardUrl('unique_matched_mobile') }}"
                class="rounded-xl border bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
            {{ $activeType === 'unique_matched_mobile' ? 'border-cyan-500 ring-2 ring-cyan-100' : 'border-slate-200' }}">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Unique Matched Mobile Numbers
                        </p>

                        <p class="mt-1 text-2xl font-bold text-cyan-600">
                            {{ number_format($uniqueMatchedMobiles ?? 0) }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-500">
                            One row per matched mobile
                        </p>
                    </div>

                    <span class="material-symbols-outlined rounded-lg bg-cyan-100 p-2 text-[20px] text-cyan-700">
                        phone_in_talk
                    </span>

                </div>

                <div class="mt-2 text-[11px] font-semibold text-cyan-600">
                    View unique mobiles →
                </div>

            </a>

            {{-- Repeated Matched Mobile --}}
            <a href="{{ $cardUrl('repeated_matched_mobile') }}"
                class="rounded-xl border bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
            {{ $activeType === 'repeated_matched_mobile' ? 'border-orange-500 ring-2 ring-orange-100' : 'border-slate-200' }}">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Repeated Matched-Mobile Rows
                        </p>

                        <p class="mt-1 text-2xl font-bold text-orange-600">
                            {{ number_format($repeatedMatchedMobileRows ?? 0) }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-500">
                            Additional rows for repeated mobiles
                        </p>
                    </div>

                    <span class="material-symbols-outlined rounded-lg bg-orange-100 p-2 text-[20px] text-orange-700">
                        repeat
                    </span>

                </div>

                <div class="mt-2 text-[11px] font-semibold text-orange-600">
                    Review repeated mobiles →
                </div>

            </a>

        </div>

        {{-- Filter and Search --}}
        <div class="mb-5 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

            <form method="GET" action="{{ route('admin.registration') }}"
                class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-6">

                <input type="hidden" name="type" value="{{ $activeType }}">

                @if (request()->filled('phase'))
                    <input type="hidden" name="phase" value="{{ request('phase') }}">
                @endif

                @if (request()->filled('district_id'))
                    <input type="hidden" name="district_id" value="{{ request('district_id') }}">
                @endif

                @if (request()->filled('block_id'))
                    <input type="hidden" name="block_id" value="{{ request('block_id') }}">
                @endif

                @if (request()->filled('village_id'))
                    <input type="hidden" name="village_id" value="{{ request('village_id') }}">
                @endif

                <div class="md:col-span-2 xl:col-span-4">

                    <label for="search"
                        class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                        Search Records
                    </label>

                    <div class="relative">

                        <span
                            class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                            search
                        </span>

                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Owner name, mobile, registry number or token..."
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100">

                    </div>

                </div>

                <div class="flex items-end">

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">

                        <span class="material-symbols-outlined text-[19px]">
                            filter_alt
                        </span>

                        Apply
                    </button>

                </div>

                <div class="flex items-end">

                    <a href="{{ $resetSearchUrl }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">

                        <span class="material-symbols-outlined text-[19px]">
                            restart_alt
                        </span>

                        Clear Search
                    </a>

                </div>

            </form>

            @if (request()->filled('phase') ||
                    request()->filled('district_id') ||
                    request()->filled('block_id') ||
                    request()->filled('village_id'))
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">

                    <p class="text-xs text-slate-500">
                        Dashboard filters are currently applied to this report.
                    </p>

                    <a href="{{ route('admin.registration', ['type' => $activeType]) }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-800">

                        <span class="material-symbols-outlined text-[17px]">
                            filter_alt_off
                        </span>

                        Clear Dashboard Filters
                    </a>

                </div>
            @endif

        </div>

        {{-- Current Result Summary --}}
        <div class="mb-5 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3">

                    <span
                        class="material-symbols-outlined flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-[20px] text-orange-700">
                        filter_list
                    </span>

                    <div>
                        <p class="text-sm font-semibold text-orange-900">
                            {{ $activeLabel }}
                        </p>

                        <p class="mt-0.5 text-xs text-orange-700">
                            {{ number_format($filteredRegistrations ?? $registrations->total()) }}
                            records match the current card, dashboard and search filters.
                        </p>
                    </div>

                </div>

                @if (request()->filled('search'))
                    <div class="rounded-lg bg-white px-3 py-2 text-xs font-semibold text-orange-700 shadow-sm">
                        Search: “{{ request('search') }}”
                    </div>
                @endif

            </div>

        </div>

        {{-- Information --}}
        <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3">

            <div class="flex items-start gap-3">

                <span class="material-symbols-outlined mt-0.5 text-[20px] text-blue-700">
                    info
                </span>

                <div>
                    <p class="text-sm font-semibold text-blue-900">
                        Record matching information
                    </p>

                    <p class="mt-1 text-xs leading-5 text-blue-700">
                        Registry mobile numbers are compared using
                        <strong>SecondPartyMobile</strong> from the registry table and
                        <strong>MobileNo</strong> from OwnerMaster. When multiple owners share
                        the same mobile number, the record with the lowest OwnerId is displayed.
                        Owner details will remain empty for unmatched records.
                    </p>
                </div>

            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            {{-- Table Header --}}
            <div
                class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        {{ $activeLabel }}
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Showing {{ $registrations->firstItem() ?? 0 }}
                        to {{ $registrations->lastItem() ?? 0 }}
                        of {{ number_format($registrations->total()) }} records
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">

                    <span
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                        <span class="material-symbols-outlined text-[16px]">
                            database
                        </span>

                        {{ number_format($registrations->total()) }} rows
                    </span>

                    <a href="{{ route('admin.registration.export.excel', $currentFilters) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">

                        <span class="material-symbols-outlined text-[16px]">
                            download
                        </span>

                        Excel
                    </a>

                    <a href="{{ route('admin.registration.export.pdf', $currentFilters) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">

                        <span class="material-symbols-outlined text-[16px]">
                            picture_as_pdf
                        </span>

                        PDF
                    </a>

                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                No.
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Application
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Owner Details
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Registry Parties
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Mobile
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Registry
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Token / Khewat
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Area
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Location
                            </th>

                            <th
                                class="whitespace-nowrap px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($registrations as $registration)

                            <tr class="transition hover:bg-slate-50">

                                {{-- Serial --}}
                                <td class="whitespace-nowrap px-3 py-3 text-sm text-slate-600">
                                    {{ ($registrations->firstItem() ?? 1) + $loop->index }}
                                </td>

                                {{-- Application --}}
                                <td class="whitespace-nowrap px-3 py-3">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->RegistrationNo ?? '-' }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-slate-500">
                                        Owner ID: {{ $registration->OwnerId ?? '-' }}
                                    </p>

                                </td>

                                {{-- Owner --}}
                                <td class="min-w-[210px] px-3 py-3">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->OwnerName ?? 'Owner not matched' }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ $registration->FatherHusbandName ?? '-' }}
                                    </p>

                                    @if (!empty($registration->PPPId))
                                        <p class="mt-0.5 text-[11px] text-slate-400">
                                            PPP ID: {{ $registration->PPPId }}
                                        </p>
                                    @endif

                                    @if (!empty($registration->MemberId))
                                        <p class="mt-0.5 text-[11px] text-slate-400">
                                            Member ID: {{ $registration->MemberId }}
                                        </p>
                                    @endif

                                </td>

                                {{-- Registry Parties --}}
                                <td class="min-w-[220px] px-3 py-3">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->SecondParty ?? '-' }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        First party: {{ $registration->FirstParty ?? '-' }}
                                    </p>

                                </td>

                                {{-- Mobile --}}
                                <td class="whitespace-nowrap px-3 py-3">

                                    @if (!empty($registration->SecondPartyMobile))
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-2 py-1 text-xs font-semibold text-green-700">

                                            <span class="material-symbols-outlined text-[15px]">
                                                call
                                            </span>

                                            {{ $registration->SecondPartyMobile }}

                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">
                                            Not available
                                        </span>
                                    @endif

                                </td>

                                {{-- Registry --}}
                                <td class="whitespace-nowrap px-3 py-3">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ filled($registration->RegistaryNumber) ? $registration->RegistaryNumber : 'Missing' }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-slate-500">

                                        @if (!empty($registration->RegistaryDate))
                                            {{ \Carbon\Carbon::parse($registration->RegistaryDate)->format('d-m-Y') }}
                                        @else
                                            Date not available
                                        @endif

                                    </p>

                                    @if (($registration->registry_group_count ?? 0) > 1)
                                        <span
                                            class="mt-1 inline-flex rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                            {{ $registration->registry_group_count }} registry rows
                                        </span>
                                    @endif

                                </td>

                                {{-- Token / Khewat --}}
                                <td class="whitespace-nowrap px-3 py-3">

                                    <p class="text-xs font-semibold text-slate-700">
                                        Token: {{ $registration->Token ?? '-' }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-slate-500">
                                        Khewat: {{ $registration->Khewat ?? '-' }}
                                    </p>

                                </td>

                                {{-- Area --}}
                                <td class="whitespace-nowrap px-3 py-3">

                                    <p class="text-xs font-semibold text-slate-800">
                                        Transfer: {{ $registration->TransferArea ?? '-' }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-slate-500">
                                        Total: {{ $registration->TotalArea ?? '-' }}
                                    </p>

                                    @if (!empty($registration->Bhag))
                                        <p class="mt-0.5 text-[11px] text-slate-400">
                                            Share: {{ $registration->Bhag }}
                                        </p>
                                    @endif

                                </td>

                                {{-- Location --}}
                                <td class="min-w-[190px] px-3 py-3">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->Village ?? '-' }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ $registration->TehsilName ?? '-' }},
                                        {{ $registration->District ?? '-' }}
                                    </p>

                                </td>

                                {{-- Status --}}
                                <td class="whitespace-nowrap px-3 py-3">

                                    @if (!empty($registration->OwnerId))
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">

                                            <span class="material-symbols-outlined text-[14px]">
                                                check_circle
                                            </span>

                                            Matched
                                        </span>

                                        @if (!empty($registration->Phase))
                                            <p class="mt-1 text-center text-[10px] font-semibold text-indigo-600">
                                                Phase {{ $registration->Phase }}
                                            </p>
                                        @endif
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-1 text-[11px] font-semibold text-rose-700">

                                            <span class="material-symbols-outlined text-[14px]">
                                                cancel
                                            </span>

                                            Unmatched
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10" class="px-6 py-14 text-center">

                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">

                                        <span class="material-symbols-outlined text-[30px] text-slate-400">
                                            folder_off
                                        </span>

                                    </div>

                                    <h3 class="mt-4 font-bold text-slate-700">
                                        No Registry Records Found
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        No records match the selected card and filters.
                                    </p>

                                    <a href="{{ route('admin.registration') }}"
                                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-700">

                                        <span class="material-symbols-outlined text-[18px]">
                                            restart_alt
                                        </span>

                                        Reset Report
                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @if ($registrations->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $registrations->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            @endif

        </div>

    </main>

@endsection
