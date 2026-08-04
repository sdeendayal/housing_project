@extends('layouts.mmgayAdmin')

@section('title', 'Village Wise Report')

@section('content')
    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">

        <div class="space-y-6">

            {{-- Page Heading --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Village Wise Report
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Only villages having assigned plots are displayed.
                    </p>
                </div>

                <a href="{{ route('admin.village.report') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">

                    <span class="material-symbols-outlined text-[19px]">
                        arrow_back
                    </span>

                    Village Report
                </a>

            </div>


            {{-- Filter Card --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Header --}}
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                            <span class="material-symbols-outlined text-[22px]">
                                filter_alt
                            </span>
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-slate-800">
                                Report Filters
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Filter villages by phase, district and village.
                            </p>
                        </div>

                    </div>

                </div>

                <form id="villageReportFilterForm" method="GET" action="{{ route('admin.village.report') }}"
                    class="space-y-4 p-4">

                    <div class="grid grid-cols-12 gap-4 items-end">

                        {{-- Filters --}}
                        <div class="col-span-5">

                            <div class="grid grid-cols-3 gap-4">

                                {{-- Phase --}}
                                <div>
                                    <label for="phase"
                                        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">
                                        Phase
                                    </label>

                                    <select id="phase" name="phase"
                                        class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm">
                                        <option value="">All Phases</option>
                                        <option value="1" {{ request('phase') == '1' ? 'selected' : '' }}>Phase 1
                                        </option>
                                        <option value="2" {{ request('phase') == '2' ? 'selected' : '' }}>Phase 2
                                        </option>
                                        <option value="3" {{ request('phase') == '3' ? 'selected' : '' }}>Phase 3
                                        </option>
                                    </select>
                                </div>

                                {{-- District --}}
                                <div>
                                    <label for="district_id"
                                        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">
                                        District
                                    </label>

                                    <select id="district_id" name="district_id"
                                        class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm">

                                        <option value="">All Districts</option>

                                        @foreach ($districts as $district)
                                            <option value="{{ $district->DistrictId }}"
                                                {{ request('district_id') == $district->DistrictId ? 'selected' : '' }}>
                                                {{ $district->DistrictName }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Village --}}
                                <div>
                                    <label for="village_id"
                                        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">
                                        Village
                                    </label>

                                    <select id="village_id" name="village_id"
                                        class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm">

                                        <option value="">All Villages</option>

                                        @foreach ($villages as $village)
                                            <option value="{{ $village->VillageId }}"
                                                {{ request('village_id') == $village->VillageId ? 'selected' : '' }}>
                                                {{ $village->VillageName }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>

                        </div>

                        {{-- Apply Button --}}
                        <div class="col-span-3 flex gap-2">

                            <button type="submit"
                                class="inline-flex h-11 w-52 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">

                                <span class="material-symbols-outlined">
                                    search
                                </span>

                                Apply
                            </button>

                            <a href="{{ route('admin.village.report') }}"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-300 bg-white hover:bg-red-50">

                                <span class="material-symbols-outlined">
                                    restart_alt
                                </span>

                            </a>

                        </div>

                        {{-- Export Buttons --}}
                        <div class="col-span-4 flex justify-end gap-2">

                            <a href="{{ route('admin.village.report.excel', request()->query()) }}"
                                class="h-11 rounded-xl bg-emerald-600 px-4 text-white font-semibold inline-flex items-center gap-2 whitespace-nowrap">
                                <span class="material-symbols-outlined">table_view</span>
                                Excel
                            </a>

                            <a href="{{ route('admin.village.report.csv', request()->query()) }}"
                                class="h-11 rounded-xl bg-teal-600 px-4 text-white font-semibold inline-flex items-center gap-2 whitespace-nowrap">
                                <span class="material-symbols-outlined">csv</span>
                                CSV
                            </a>

                            <a href="{{ route('admin.village.report.pdf', request()->query()) }}"
                                class="h-11 rounded-xl bg-rose-600 px-4 text-white font-semibold inline-flex items-center gap-2 whitespace-nowrap">
                                <span class="material-symbols-outlined">picture_as_pdf</span>
                                PDF
                            </a>

                            <a href="{{ route('admin.village.report.print', request()->query()) }}" target="_blank"
                                class="h-11 rounded-xl bg-slate-700 px-4 text-white font-semibold inline-flex items-center gap-2 whitespace-nowrap">
                                <span class="material-symbols-outlined">print</span>
                                Print
                            </a>

                        </div>

                    </div>

                    {{-- Active Filters --}}
                    @if (request('phase') || request('district_id') || request('village_id'))

                        <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">

                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Active Filters
                            </span>

                            @if (request('phase'))
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    <span class="material-symbols-outlined text-[15px]">
                                        layers
                                    </span>
                                    Phase {{ request('phase') }}
                                </span>
                            @endif

                            @if (request('district_id'))
                                @php
                                    $selectedDistrict = $districts->firstWhere('DistrictId', request('district_id'));
                                @endphp

                                @if ($selectedDistrict)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        <span class="material-symbols-outlined text-[15px]">
                                            location_on
                                        </span>
                                        {{ $selectedDistrict->DistrictName }}
                                    </span>
                                @endif
                            @endif

                            @if (request('village_id'))
                                @php
                                    $selectedVillage = $villages->firstWhere('VillageId', request('village_id'));
                                @endphp

                                @if ($selectedVillage)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                                        <span class="material-symbols-outlined text-[15px]">
                                            holiday_village
                                        </span>
                                        {{ $selectedVillage->VillageName }}
                                    </span>
                                @endif
                            @endif

                        </div>

                    @endif

                </form>

            </div>


            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Villages With Plots
                            </p>

                            <h3 class="mt-2 text-2xl font-bold text-slate-800">
                                {{ number_format($grossTotal->TotalVillages ?? 0) }}
                            </h3>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                            <span class="material-symbols-outlined">
                                holiday_village
                            </span>
                        </div>

                    </div>

                </div>


                <div class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Total Plots
                            </p>

                            <h3 class="mt-2 text-2xl font-bold text-slate-800">
                                {{ number_format($grossTotal->TotalPlots ?? 0) }}
                            </h3>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                            <span class="material-symbols-outlined">
                                grid_view
                            </span>
                        </div>

                    </div>

                </div>


                <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Registered Beneficiaries
                            </p>

                            <h3 class="mt-2 text-2xl font-bold text-slate-800">
                                {{ number_format($grossTotal->RegisteredBeneficiaries ?? 0) }}
                            </h3>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <span class="material-symbols-outlined">
                                groups
                            </span>
                        </div>

                    </div>

                </div>


                <div class="rounded-2xl border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Allotted Beneficiaries
                            </p>

                            <h3 class="mt-2 text-2xl font-bold text-slate-800">
                                {{ number_format($grossTotal->AllottedBeneficiaries ?? 0) }}
                            </h3>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                            <span class="material-symbols-outlined">
                                real_estate_agent
                            </span>
                        </div>

                    </div>

                </div>

            </div>


            {{-- Report Table --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div
                    class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h2 class="font-semibold text-slate-800">
                            Village Details
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Showing {{ $report->count() }} village records.
                        </p>
                    </div>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50">

                            <tr>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Sr. No.
                                </th>
                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Site Development
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Village
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Phase
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Total Plots
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Applicants
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Approved Paid
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Approved Unpaid
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Yet to be Approved
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Rejected
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Cancelled
                                </th>

                                <th
                                    class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Allotted
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">

                            @forelse ($report as $index => $row)
                                @php
                                    $commonFilters = array_filter([
                                        'phase' => $row->Phase ?? request('phase'),

                                        'district_id' => $row->DistrictId ?? request('district_id'),

                                        'village_id' => $row->VillageId,
                                    ]);
                                @endphp

                                <tr class="transition hover:bg-slate-50">

                                    {{-- Serial Number --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">
                                        {{ $report->firstItem() + $index }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm">

                                        <button type="button"
                                            data-url="{{ route('admin.village.site-development', ['villageId' => $row->VillageId]) }}"
                                            data-village-name="{{ $row->VillageName }}" data-phase="{{ $row->Phase }}"
                                            class="site-development-button inline-flex items-center justify-center gap-2 rounded-lg border border-cyan-300 bg-cyan-50 px-3 py-1.5 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-600 hover:text-white">
                                            <span class="material-symbols-outlined text-[17px]">
                                                construction
                                            </span>

                                            View
                                        </button>

                                    </td>

                                    {{-- Village Name --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold">
                                        <a href="{{ route('superadmin.applicants.index', $commonFilters) }}"
                                            class="inline-flex items-center gap-1 text-indigo-700 transition hover:text-indigo-900 hover:underline">

                                            {{ $row->VillageName }}

                                            <span class="material-symbols-outlined text-[16px]">
                                                open_in_new
                                            </span>
                                        </a>
                                    </td>

                                    {{-- Phase --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm text-slate-600">
                                        {{ $row->Phase ?? '-' }}
                                    </td>

                                    {{-- Total Plots --}}
                                    <td
                                        class="whitespace-nowrap px-4 py-3 text-center text-sm font-semibold text-slate-700">
                                        {{ number_format($row->TotalPlots ?? 0) }}
                                    </td>

                                    {{-- Applicants --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                        <a href="{{ route('superadmin.applicants.index', $commonFilters) }}"
                                            class="inline-flex min-w-12 items-center justify-center rounded-lg bg-indigo-50 px-3 py-1.5 font-semibold text-indigo-700 transition hover:bg-indigo-100 hover:shadow-sm">

                                            {{ number_format($row->RegisteredBeneficiaries ?? 0) }}
                                        </a>
                                    </td>

                                    {{-- Approved Paid --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                        <a href="{{ route(
                                            'admin.allotment.report',
                                            array_merge($commonFilters, [
                                                'status' => 'approved_paid',
                                            ]),
                                        ) }}"
                                            class="inline-flex min-w-12 items-center justify-center rounded-lg bg-emerald-50 px-3 py-1.5 font-semibold text-emerald-700 transition hover:bg-emerald-100 hover:shadow-sm">

                                            {{ number_format($row->ApprovedPaid ?? 0) }}
                                        </a>
                                    </td>

                                    {{-- Approved Unpaid --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                        <a href="{{ route(
                                            'admin.allotment.report',
                                            array_merge($commonFilters, [
                                                'status' => 'approved_unpaid',
                                            ]),
                                        ) }}"
                                            class="inline-flex min-w-12 items-center justify-center rounded-lg bg-amber-50 px-3 py-1.5 font-semibold text-amber-700 transition hover:bg-amber-100 hover:shadow-sm">

                                            {{ number_format($row->ApprovedUnpaid ?? 0) }}
                                        </a>
                                    </td>

                                    {{-- Yet to be Approved --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                        <a href="{{ route(
                                            'admin.allotment.report',
                                            array_merge($commonFilters, [
                                                'status' => 'pending',
                                            ]),
                                        ) }}"
                                            class="inline-flex min-w-12 items-center justify-center rounded-lg bg-blue-50 px-3 py-1.5 font-semibold text-blue-700 transition hover:bg-blue-100 hover:shadow-sm">

                                            {{ number_format($row->PendingApprovalPayment ?? 0) }}
                                        </a>
                                    </td>

                                    {{-- Rejected --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                        <a href="{{ route(
                                            'admin.allotment.report',
                                            array_merge($commonFilters, [
                                                'status' => 'rejected',
                                            ]),
                                        ) }}"
                                            class="inline-flex min-w-12 items-center justify-center rounded-lg bg-rose-50 px-3 py-1.5 font-semibold text-rose-700 transition hover:bg-rose-100 hover:shadow-sm">

                                            {{ number_format($row->Rejected ?? 0) }}
                                        </a>
                                    </td>

                                    {{-- Cancelled --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                        <a href="{{ route(
                                            'admin.allotment.report',
                                            array_merge($commonFilters, [
                                                'status' => 'cancelled',
                                            ]),
                                        ) }}"
                                            class="inline-flex min-w-12 items-center justify-center rounded-lg bg-slate-100 px-3 py-1.5 font-semibold text-slate-700 transition hover:bg-slate-200 hover:shadow-sm">

                                            {{ number_format($row->AllotmentCancelled ?? 0) }}
                                        </a>
                                    </td>

                                    {{-- Allotted --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                        <a href="{{ route('admin.allotment.report', $commonFilters) }}"
                                            class="inline-flex min-w-12 items-center justify-center rounded-lg bg-violet-50 px-3 py-1.5 font-semibold text-violet-700 transition hover:bg-violet-100 hover:shadow-sm">

                                            {{ number_format($row->AllottedBeneficiaries ?? 0) }}
                                        </a>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="12" class="px-6 py-12 text-center">

                                        <div class="flex flex-col items-center">

                                            <div
                                                class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                                <span class="material-symbols-outlined text-3xl">
                                                    holiday_village
                                                </span>
                                            </div>

                                            <h3 class="mt-4 font-semibold text-slate-700">
                                                No village records found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Try changing the selected filters.
                                            </p>

                                        </div>

                                    </td>
                                </tr>
                            @endforelse

                        </tbody>


                        @if ($report->isNotEmpty())
                            <tfoot class="bg-slate-100 border-t-2 border-slate-300">

                                <tr class="font-bold text-slate-800">
                                    <td class="px-4 py-3 text-center">

                                    </td>
                                    <td class="px-4 py-3 text-center">

                                    </td>

                                    {{-- Sr + Site Development + Village + Phase --}}
                                    <td class="px-4 py-3 text-lg font-bold whitespace-nowrap">
                                        Gross Total
                                    </td>
                                    <td class="px-4 py-3 text-center">

                                    </td>


                                    <td class="px-4 py-3 text-center">
                                        {{ number_format($grossTotal->TotalPlots ?? 0) }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        {{ number_format($grossTotal->RegisteredBeneficiaries ?? 0) }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-emerald-700">
                                        {{ number_format($grossTotal->ApprovedPaid ?? 0) }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-amber-700">
                                        {{ number_format($grossTotal->ApprovedUnpaid ?? 0) }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-blue-700">
                                        {{ number_format($grossTotal->PendingApprovalPayment ?? 0) }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-red-600">
                                        {{ number_format($grossTotal->Rejected ?? 0) }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-rose-600">
                                        {{ number_format($grossTotal->AllotmentCancelled ?? 0) }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-indigo-700">
                                        {{ number_format($grossTotal->AllottedBeneficiaries ?? 0) }}
                                    </td>

                                </tr>

                            </tfoot>
                        @endif

                    </table>

                </div>
                @if ($report->hasPages())
                    <div class="border-t border-slate-200 px-4 py-3">
                        {{ $report->onEachSide(1)->links('pagination::tailwind') }}
                    </div>
                @endif

            </div>

        </div>

    </main>

    <div id="siteDevelopmentModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
        <div class="flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">

            {{-- Header --}}
            <div
                class="flex items-center justify-between border-b border-slate-200 bg-gradient-to-r from-cyan-600 to-blue-700 px-6 py-5 text-white">
                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15">
                        <span class="material-symbols-outlined">
                            construction
                        </span>
                    </div>

                    <div>
                        <h2 id="siteDevelopmentTitle" class="text-lg font-bold">
                            Site Development
                        </h2>

                        <p id="siteDevelopmentSubtitle" class="mt-0.5 text-sm text-cyan-100">
                            Village development details
                        </p>
                    </div>

                </div>

                {{-- Close Button --}}
                <button type="button" id="closeSiteDevelopmentModal" aria-label="Close modal"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/15 text-white transition hover:rotate-90 hover:bg-white/25 focus:outline-none focus:ring-4 focus:ring-white/30">
                    <span class="material-symbols-outlined text-[28px]">
                        close
                    </span>
                </button>
            </div>

            {{-- Dynamic Content --}}
            <div id="siteDevelopmentContent" class="flex-1 overflow-y-auto bg-slate-50 p-6">
                <div class="flex min-h-[280px] items-center justify-center">

                    <div class="text-center">

                        <svg class="mx-auto h-12 w-12 animate-spin text-cyan-600" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>

                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        <p class="mt-4 text-sm font-medium text-slate-600">
                            Loading development records...
                        </p>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <div id="excelDownloadPopup"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="w-[340px] rounded-2xl bg-white p-6 text-center shadow-2xl">

            <svg class="mx-auto h-12 w-12 animate-spin text-emerald-600" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>

                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>

            <h3 class="mt-5 text-lg font-bold text-slate-800">
                Preparing Excel Report
            </h3>

            <p class="mt-2 text-sm text-slate-500">
                Please wait, your Excel file is being generated...
            </p>

        </div>
    </div>

    <div id="pdfDownloadPopup"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="rounded-2xl bg-white p-6 shadow-xl text-center w-80">

            <svg class="mx-auto h-10 w-10 animate-spin text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">

                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>

                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>

            </svg>

            <h3 class="mt-4 text-lg font-semibold">
                Preparing PDF...
            </h3>

            <p class="mt-2 text-sm text-gray-500">
                Please wait while your PDF is being generated.
            </p>

        </div>
    </div>
@endsection
