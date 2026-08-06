@extends('layouts.mmgayAdmin')

@section('title', 'District Wise Report - Super Admin')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">

        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        District Report Filters
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Select phase and district to filter the report.
                    </p>
                </div>

                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
                    Report Ready
                </div>
            </div>

            <form id="districtReportFilterForm" method="GET" action="{{ route('admin.district.report') }}" class="p-5">

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-12">

                    {{-- Phase --}}
                    <div class="lg:col-span-3">
                        <label for="phase" class="mb-2 block text-sm font-semibold text-slate-700">
                            Phase
                        </label>

                        <select id="phase" name="phase"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">All Phases</option>

                            <option value="1" {{ request('phase') == '1' ? 'selected' : '' }}>
                                Phase 1
                            </option>

                            <option value="2" {{ request('phase') == '2' ? 'selected' : '' }}>
                                Phase 2
                            </option>

                            <option value="3" {{ request('phase') == '3' ? 'selected' : '' }}>
                                Phase 3
                            </option>
                        </select>
                    </div>

                    {{-- District --}}
                    <div class="lg:col-span-3">
                        <label for="district_id" class="mb-2 block text-sm font-semibold text-slate-700">
                            District
                        </label>

                        <select id="district_id" name="district_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">All Districts</option>

                            @foreach ($districts as $district)
                                <option value="{{ $district->DistrictId }}"
                                    {{ request('district_id') == $district->DistrictId ? 'selected' : '' }}>
                                    {{ $district->DistrictName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Buttons --}}
                    <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-3">

                        <button id="districtReportApplyButton" type="submit"
                            class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl
        bg-gradient-to-r from-blue-600 to-indigo-600
        px-4 text-sm font-semibold text-white
        shadow-lg shadow-blue-200/50
        transition-all duration-200
        hover:-translate-y-0.5 hover:shadow-xl hover:from-blue-700 hover:to-indigo-700">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 01.8 1.6L14 13.5V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.5L3.2 4.6A1 1 0 013 4z" />

                            </svg>

                            Apply
                        </button>

                        <a href="{{ route('admin.district.report') }}"
                            class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl
        border border-slate-300/70
        bg-white/60 backdrop-blur-md
        px-4 text-sm font-semibold text-slate-700
        shadow-md transition-all duration-200
        hover:-translate-y-0.5 hover:bg-white hover:shadow-lg">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />

                            </svg>

                            Reset
                        </a>

                    </div>

                    {{-- Export Buttons --}}
                    <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-3">

                        {{-- Excel --}}
                        <a id="districtCsvExportButton" href="{{ route('admin.district.report.csv', request()->query()) }}"
                            class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl
        border border-emerald-200
        bg-emerald-500/10 backdrop-blur-md
        px-4 text-sm font-semibold text-emerald-700
        shadow-md transition-all duration-200
        hover:-translate-y-0.5
        hover:bg-emerald-600 hover:text-white hover:border-emerald-600 hover:shadow-xl">

                            <span class="material-symbols-outlined text-[19px]">
                                table_view
                            </span>

                            Excel
                        </a>

                        {{-- PDF --}}
                        <a href="{{ route('admin.district.report.print', request()->query()) }}" target="_blank"
                            rel="noopener"
                            class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl
        border border-rose-200
        bg-rose-500/10 backdrop-blur-md
        px-4 text-sm font-semibold text-rose-700
        shadow-md transition-all duration-200
        hover:-translate-y-0.5
        hover:bg-rose-600 hover:text-white hover:border-rose-600 hover:shadow-xl">

                            <span class="material-symbols-outlined text-[19px]">
                                picture_as_pdf
                            </span>

                            PDF
                        </a>

                    </div>

                </div>

                {{-- Active Filters --}}
                @if (request('phase') || request('district_id'))
                    <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">

                        <span class="text-sm font-medium text-slate-500">
                            Active Filters:
                        </span>

                        @if (request('phase'))
                            <span
                                class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                Phase {{ request('phase') }}
                            </span>
                        @endif

                        @if (request('district_id'))
                            @php
                                $selectedDistrict = $districts->firstWhere('DistrictId', request('district_id'));
                            @endphp

                            @if ($selectedDistrict)
                                <span
                                    class="inline-flex items-center rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                                    {{ $selectedDistrict->DistrictName }}
                                </span>
                            @endif
                        @endif

                    </div>
                @endif

            </form>
        </div>



        <!-- TABLE CARD -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-blue-600 text-white text-xs uppercase">
                        <tr>
                            <th class="w-16 p-3 text-center">S. No.</th>
                            <th class="p-3 text-left">District</th>
                            <th class="p-3 text-center">Villages</th>
                            <th class="p-3 text-center">Applicants</th>
                            <th class="p-3 text-center">Allotted</th>
                            <th class="p-3 text-center">Approved & Paid</th>
                            <th class="p-3 text-center">Approved & Unpaid</th>
                            <th class="p-3 text-center">Yet to be Approved</th>
                            <th class="p-3 text-center">Rejected</th>
                            <th class="p-3 text-center">Cancelled</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse ($report as $index => $d)
                            @php

                                $commonFilters = array_filter([
                                    'phase' => request('phase'),
                                    'district_id' => $d->DistrictId,
                                ]);

                                $districtUrl = route('admin.district.report', $commonFilters);

                                $allottedUrl = route('admin.allotment.report', array_merge($commonFilters));

                                $approvedPaidUrl = route(
                                    'admin.allotment.report',
                                    array_merge($commonFilters, [
                                        'status' => 'approved_paid',
                                    ]),
                                );

                                $approvedUnpaidUrl = route(
                                    'admin.allotment.report',
                                    array_merge($commonFilters, [
                                        'status' => 'approved_unpaid',
                                    ]),
                                );

                                $pendingUrl = route(
                                    'admin.allotment.report',
                                    array_merge($commonFilters, [
                                        'status' => 'pending',
                                    ]),
                                );

                                $rejectedUrl = route(
                                    'admin.allotment.report',
                                    array_merge($commonFilters, [
                                        'status' => 'rejected',
                                    ]),
                                );

                                $cancelledUrl = route(
                                    'admin.allotment.report',
                                    array_merge($commonFilters, [
                                        'status' => 'cancelled',
                                    ]),
                                );
                            @endphp

                            <tr class="transition-colors hover:bg-blue-50/50">
                                <td class="p-3 text-center font-semibold text-slate-700">
                                    {{ $index + 1 }}
                                </td>

                                {{-- District --}}
                                <td class="p-3">
                                    <a href="{{ $districtUrl }}"
                                        class="inline-flex items-center gap-2 font-semibold text-blue-700 hover:text-blue-900 hover:underline">

                                        <span class="material-symbols-outlined text-[18px]">
                                            location_on
                                        </span>

                                        {{ $d->DistrictName }}
                                    </a>
                                </td>

                                {{-- Villages --}}
                                <td class="p-3 text-center">
                                    <a href="{{ route(
                                        'admin.village.report',
                                        array_merge($commonFilters, [
                                            'status' => 'all',
                                        ]),
                                    ) }}"
                                        class="inline-flex min-w-12 items-center justify-center rounded-lg bg-violet-50 px-3 py-1.5 font-semibold text-violet-700 transition hover:bg-violet-100 hover:shadow-sm">

                                        {{ number_format($d->VillagesWithPlots) }}
                                    </a>
                                </td>

                                {{-- Applicants --}}
                                <td class="p-3 text-center">
                                    <a href="{{ route(
                                        'superadmin.applicants.index',
                                        array_merge($commonFilters, [
                                            'status' => 'all',
                                        ]),
                                    ) }}"
                                        class="inline-flex min-w-12 items-center justify-center rounded-lg bg-cyan-50 px-3 py-1.5 font-semibold text-cyan-700 transition hover:bg-cyan-100 hover:shadow-sm">

                                        {{ number_format($d->RegisteredBeneficiaries) }}
                                    </a>
                                </td>

                                {{-- Allotted --}}
                                <td class="p-3 text-center">
                                    <a href="{{ $allottedUrl }}"
                                        class="inline-flex min-w-12 items-center justify-center rounded-lg bg-blue-50 px-3 py-1.5 font-semibold text-blue-700 transition hover:bg-blue-100 hover:shadow-sm">

                                        {{ number_format($d->AllottedBeneficiaries) }}
                                    </a>
                                </td>

                                {{-- Approved & Paid --}}
                                <td class="p-3 text-center">
                                    <a href="{{ $approvedPaidUrl }}"
                                        class="inline-flex min-w-12 items-center justify-center rounded-lg bg-green-50 px-3 py-1.5 font-semibold text-green-700 transition hover:bg-green-100 hover:shadow-sm">

                                        {{ number_format($d->ApprovedPaid) }}
                                    </a>
                                </td>

                                {{-- Approved & Unpaid --}}
                                <td class="p-3 text-center">
                                    <a href="{{ $approvedUnpaidUrl }}"
                                        class="inline-flex min-w-12 items-center justify-center rounded-lg bg-yellow-50 px-3 py-1.5 font-semibold text-yellow-700 transition hover:bg-yellow-100 hover:shadow-sm">

                                        {{ number_format($d->ApprovedUnpaid) }}
                                    </a>
                                </td>

                                {{-- Yet to be Approved --}}
                                <td class="p-3 text-center">
                                    <a href="{{ $pendingUrl }}"
                                        class="inline-flex min-w-12 items-center justify-center rounded-lg bg-orange-50 px-3 py-1.5 font-semibold text-orange-700 transition hover:bg-orange-100 hover:shadow-sm">

                                        {{ number_format($d->PendingApprovalPayment) }}
                                    </a>
                                </td>

                                {{-- Rejected --}}
                                <td class="p-3 text-center">
                                    <a href="{{ $rejectedUrl }}"
                                        class="inline-flex min-w-12 items-center justify-center rounded-lg bg-red-50 px-3 py-1.5 font-semibold text-red-700 transition hover:bg-red-100 hover:shadow-sm">

                                        {{ number_format($d->Rejected) }}
                                    </a>
                                </td>

                                {{-- Cancelled --}}
                                <td class="p-3 text-center">
                                    <a href="{{ $cancelledUrl }}"
                                        class="inline-flex min-w-12 items-center justify-center rounded-lg bg-slate-100 px-3 py-1.5 font-semibold text-slate-700 transition hover:bg-slate-200 hover:shadow-sm">

                                        {{ number_format($d->AllotmentCancelled) }}
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-gray-500">
                                    No records found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-gray-100 font-bold border-t-2">
                        <tr>
                            <td></td>
                            <td class="p-3">GROSS TOTAL</td>
                            <td class="text-center">{{ number_format($grossTotal->VillagesWithPlots) }}</td>
                            <td class="text-center">{{ number_format($grossTotal->RegisteredBeneficiaries) }}</td>
                            <td class="text-center">{{ number_format($grossTotal->AllottedBeneficiaries) }}</td>
                            <td class="text-center text-green-600">{{ number_format($grossTotal->ApprovedPaid) }}</td>
                            <td class="text-center text-yellow-600">{{ number_format($grossTotal->ApprovedUnpaid) }}</td>
                            <td class="text-center text-orange-600">
                                {{ number_format($grossTotal->PendingApprovalPayment) }}</td>
                            <td class="text-center text-red-600">{{ number_format($grossTotal->Rejected) }}</td>
                            <td class="text-center text-gray-700">{{ number_format($grossTotal->AllotmentCancelled) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>
    <div id="excelDownloadPopup"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="w-80 rounded-2xl bg-white p-6 text-center shadow-xl">

            <svg class="mx-auto h-10 w-10 animate-spin text-emerald-600" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">

                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>

                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>

            </svg>

            <h3 class="mt-4 text-lg font-semibold">
                Preparing Excel...
            </h3>

            <p class="mt-2 text-sm text-gray-500">
                Please wait while your report is being generated.
            </p>

        </div>
    </div>

    <div id="pdfDownloadPopup"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="w-80 rounded-2xl bg-white p-6 text-center shadow-xl">

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
