@extends('layouts.mmsayDepartmentAuth')

@section('title', 'MMSAY Department Property Registration')

@section('content')
    <main class="ml-52 min-h-screen px-5 pb-5 pt-20">
        <div class="mx-auto max-w-container-max space-y-md">

            {{-- Page Header --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-primary">
                        Assets List
                    </h2>

                    <p class="mt-0.5 text-sm text-on-surface-variant">
                        View property, purchaser and payment information.
                    </p>
                </div>

                <a href="{{ url('mmsay-department-dashboard') }}"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[17px]">
                        arrow_back
                    </span>
                    Dashboard
                </a>
            </div>

            {{-- Listing --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                {{-- Filters --}}
                <div class="w-full border-b border-slate-100 bg-white p-4">

                    {{-- Heading --}}
                    <div class="mb-3">
                        <h3 class="text-base font-semibold text-slate-800">
                            Property Registration
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-400">
                            {{ number_format($properties->total()) }} records found
                        </p>
                    </div>

                    {{-- Full-width filter toolbar --}}
                    <form method="GET" action="{{ url()->current() }}"
                        class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-12">

                        {{-- Search --}}
                        <div class="relative sm:col-span-2 lg:col-span-3">
                            <span
                                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[17px] text-slate-400">
                                search
                            </span>

                            <input type="search" name="search" value="{{ $search ?? '' }}"
                                placeholder="Asset, purchaser, mobile..."
                                class="h-10 w-full rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-3 text-xs text-slate-700 outline-none transition
                       placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        </div>

                        {{-- District --}}
                        <div class="relative lg:col-span-2">
                            <select name="district_id" id="district_id"
                                class="h-10 w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 pr-8 text-xs text-slate-700 outline-none transition
                       focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                                <option value="">All Districts</option>

                                @foreach ($districts as $district)
                                    <option value="{{ $district->DistrictId }}" @selected(($districtId ?? null) == $district->DistrictId)>
                                        {{ $district->DistrictName }}
                                    </option>
                                @endforeach
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[17px] text-slate-400">
                                expand_more
                            </span>
                        </div>

                        {{-- City --}}
                        <div class="relative lg:col-span-2">
                            <select name="city_id" id="city_id"
                                class="h-10 w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 pr-8 text-xs text-slate-700 outline-none transition
                       focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                                <option value="">All Cities</option>

                                @foreach ($cities as $city)
                                    <option value="{{ $city->CityId }}" @selected(($cityId ?? null) == $city->CityId)>
                                        {{ $city->CityName }}
                                    </option>
                                @endforeach
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[17px] text-slate-400">
                                expand_more
                            </span>
                        </div>

                        {{-- Sector --}}
                        <div class="relative lg:col-span-2">
                            <select name="sector_id" id="sector_id"
                                class="h-10 w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 pr-8 text-xs text-slate-700 outline-none transition
                       focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                                <option value="">All Sectors</option>

                                @foreach ($sectors as $sector)
                                    <option value="{{ $sector->SectorId }}" @selected(($sectorId ?? null) == $sector->SectorId)>
                                        {{ $sector->SectorName }}
                                    </option>
                                @endforeach
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[17px] text-slate-400">
                                expand_more
                            </span>
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-3">

                            <button type="submit"
                                class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-lg bg-indigo-600 px-3 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                <span class="material-symbols-outlined text-[16px]">
                                    filter_alt
                                </span>
                                Filter
                            </button>

                            <a href="{{ url()->current() }}" title="Reset"
                                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-500">
                                <span class="material-symbols-outlined text-[17px]">
                                    restart_alt
                                </span>
                            </a>

                            <a href="{{ route('properties.export.csv', request()->query()) }}" title="Download CSV"
                                class="inline-flex h-10 items-center justify-center gap-1.5 rounded-lg bg-teal-600 px-3 text-xs font-semibold text-white transition hover:bg-teal-700">
                                <span class="material-symbols-outlined text-[16px]">
                                    download
                                </span>
                                <span class="hidden xl:inline">CSV</span>
                            </a>

                            <a href="{{ route('properties.records.print', request()->query()) }}" target="_blank"
                                title="Print records"
                                class="inline-flex h-10 items-center justify-center gap-1.5 rounded-lg bg-slate-700 px-3 text-xs font-semibold text-white transition hover:bg-slate-800">
                                <span class="material-symbols-outlined text-[16px]">
                                    print
                                </span>
                                <span class="hidden xl:inline">Print</span>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1150px] text-left text-xs">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-3">Asset</th>
                                <th class="px-4 py-3">Property</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3">Purchaser</th>
                                <th class="px-4 py-3">Mobile</th>
                                <th class="px-4 py-3 text-right">Total Cost</th>
                                <th class="px-4 py-3 text-right">Received</th>
                                <th class="px-4 py-3 text-right">Pending</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($properties as $item)
                                <tr class="transition hover:bg-indigo-50/40">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-slate-800">
                                            #{{ $item->AssetId }}
                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            App: {{ $item->application_number ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-700">
                                            {{ $item->AssetName }}
                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $item->AssetSize }} {{ $item->Unit }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-3">
                                        <p class="text-slate-700">
                                            {{ $item->district ?? '-' }}
                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $item->city ?? '-' }} /
                                            {{ $item->sector ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-3 font-medium text-slate-700">
                                        {{ $item->purchaser_name ?? 'Not allotted' }}
                                    </td>

                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $item->mobile ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-medium text-slate-700">
                                        ₹{{ number_format($item->FlatCost ?? 0, 2) }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-semibold text-emerald-600">
                                        ₹{{ number_format($item->total_received ?? 0, 2) }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <span
                                            class="{{ ($item->pending_amount ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }} font-semibold">
                                            ₹{{ number_format($item->pending_amount ?? 0, 2) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('properties.show', $item->AssetId) }}"
                                                class="inline-flex h-8 items-center gap-1 rounded-lg bg-indigo-50 px-3 text-[11px] font-semibold text-indigo-600 transition hover:bg-indigo-600 hover:text-white">
                                                <span class="material-symbols-outlined text-[16px]">
                                                    visibility
                                                </span>
                                                Details
                                            </a>

                                            <a href="{{ route('properties.print', $item->AssetId) }}" target="_blank"
                                                title="Print statement"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-700 hover:text-white">
                                                <span class="material-symbols-outlined text-[16px]">
                                                    print
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-sm text-slate-400">
                                        No property records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($properties->hasPages())
                    <div
                        class="flex flex-col gap-3 border-t border-slate-100 bg-white px-4 py-3 sm:flex-row sm:items-center sm:justify-between">

                        {{-- Record information --}}
                        <p class="text-xs text-slate-500">
                            Showing
                            <span class="font-semibold text-slate-700">
                                {{ number_format($properties->firstItem()) }}
                            </span>
                            to
                            <span class="font-semibold text-slate-700">
                                {{ number_format($properties->lastItem()) }}
                            </span>
                            of
                            <span class="font-semibold text-slate-700">
                                {{ number_format($properties->total()) }}
                            </span>
                            records
                        </p>

                        {{-- Pagination --}}
                        <nav class="flex items-center gap-1" aria-label="Pagination">

                            {{-- Previous --}}
                            @if ($properties->onFirstPage())
                                <span
                                    class="inline-flex h-8 cursor-not-allowed items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium text-slate-300">

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>

                                    Previous
                                </span>
                            @else
                                <a href="{{ $properties->previousPageUrl() }}"
                                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-medium text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>

                                    Previous
                                </a>
                            @endif

                            {{-- Page numbers --}}
                            @foreach ($properties->getUrlRange(max(1, $properties->currentPage() - 2), min($properties->lastPage(), $properties->currentPage() + 2)) as $page => $url)
                                @if ($page == $properties->currentPage())
                                    <span aria-current="page"
                                        class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-indigo-600 px-2 text-xs font-semibold text-white shadow-sm">

                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg border border-slate-200 bg-white px-2 text-xs font-medium text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if ($properties->hasMorePages())
                                <a href="{{ $properties->nextPageUrl() }}"
                                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-medium text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                    Next

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </a>
                            @else
                                <span
                                    class="inline-flex h-8 cursor-not-allowed items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium text-slate-300">

                                    Next

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </span>
                            @endif
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection
