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

            <form method="GET" action="{{ route('admin.district.report') }}" class="p-5">

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
                    <div class="flex items-end gap-3 sm:col-span-2 lg:col-span-3">

                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 01.8 1.6L14 13.5V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.5L3.2 4.6A1 1 0 013 4z" />
                            </svg>

                            Apply
                        </button>

                        <a href="{{ route('admin.district.report') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>

                            Reset
                        </a>

                    </div>

                    {{-- Export Buttons --}}
                    <div class="flex items-end gap-3 sm:col-span-2 lg:col-span-3">

                        <a id="districtExcelExportButton"
                            href="{{ route('admin.district.report.excel', request()->query()) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">

                            <!-- Default Icon -->
                            <span id="districtExcelIcon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m5 5H4a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h9a2 2 0 012 2v8a2 2 0 01-2 2z" />

                                </svg>
                            </span>

                            <!-- Loader -->
                            <svg id="districtExcelLoader" class="hidden h-5 w-5 animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">

                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>

                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>

                            </svg>

                            <!-- Button Text -->
                            <span id="districtExcelText">
                                Excel
                            </span>

                        </a>

                        <a id="districtPdfExportButton" href="{{ route('admin.district.report.pdf', request()->query()) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-200">

                            <!-- Default Icon -->
                            <span id="districtPdfIcon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m5 5H4a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h9a2 2 0 012 2v8a2 2 0 01-2 2z" />

                                </svg>
                            </span>

                            <!-- Loader -->
                            <svg id="districtPdfLoader" class="hidden h-5 w-5 animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">

                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>

                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>

                            </svg>

                            <!-- Button Text -->
                            <span id="districtPdfText">
                                PDF
                            </span>

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
                        @forelse($report as $d)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-3 font-semibold text-gray-700">{{ $d->DistrictName }}</td>
                                <td class="text-center">{{ number_format($d->VillagesWithPlots) }}</td>
                                <td class="text-center">{{ number_format($d->RegisteredBeneficiaries) }}</td>
                                <td class="text-center font-semibold text-blue-600">
                                    {{ number_format($d->AllottedBeneficiaries) }}</td>
                                <td class="text-center text-green-600 font-semibold">{{ number_format($d->ApprovedPaid) }}
                                </td>
                                <td class="text-center text-yellow-600 font-semibold">
                                    {{ number_format($d->ApprovedUnpaid) }}</td>
                                <td class="text-center text-orange-600 font-semibold">
                                    {{ number_format($d->PendingApprovalPayment) }}</td>
                                <td class="text-center text-red-600 font-semibold">{{ number_format($d->Rejected) }}</td>
                                <td class="text-center text-gray-700 font-semibold">
                                    {{ number_format($d->AllotmentCancelled) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-gray-500">No records found for the selected
                                    filters.</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-gray-100 font-bold border-t-2">
                        <tr>
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
