@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY - Full Paid Properties')

@section('content')
    <main class="ml-52 min-h-screen bg-slate-50 px-5 pb-6 pt-20">
        <div class="mx-auto max-w-[1800px] space-y-4">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-slate-900">Full Paid Properties</h1>
                        <p class="mt-0.5 text-xs text-slate-500">
                            Properties where total received amount is equal to or greater than flat cost
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('full-paid-properties.csv', request()->query()) }}"
                            class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white transition hover:bg-emerald-700">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            Excel CSV
                        </a>

                        <a href="{{ route('full-paid-properties.print', request()->query()) }}" target="_blank"
                            class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-slate-800 px-3 text-xs font-semibold text-white transition hover:bg-slate-900">
                            <span class="material-symbols-outlined text-[16px]">print</span>
                            Print
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('full-paid-properties') }}"
                    class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-12">
                    <input type="search" name="search" value="{{ request('search', '') }}"
                        placeholder="Asset, applicant, application, mobile..."
                        class="h-10 rounded-lg border border-slate-200 px-3 text-xs outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 xl:col-span-4">

                    <select name="district_id" id="district_id"
                        onchange="document.getElementById('city_id').value=''; document.getElementById('sector_id').value=''; this.form.submit();"
                        class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 xl:col-span-2">
                        <option value="">All Districts</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->DistrictId }}" @selected((string) request('district_id') === (string) $district->DistrictId)>
                                {{ $district->DistrictName }}
                            </option>
                        @endforeach
                    </select>

                    <select name="city_id" id="city_id"
                        onchange="document.getElementById('sector_id').value=''; this.form.submit();"
                        @disabled(!request()->filled('district_id'))
                        class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 xl:col-span-2">
                        <option value="">
                            {{ request()->filled('district_id') ? 'All Cities' : 'Select district first' }}
                        </option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->CityId }}" @selected((string) request('city_id') === (string) $city->CityId)>
                                {{ $city->CityName }}
                            </option>
                        @endforeach
                    </select>

                    <select name="sector_id" id="sector_id" @disabled(!request()->filled('city_id'))
                        class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 xl:col-span-2">
                        <option value="">
                            {{ request()->filled('city_id') ? 'All Villages / Sectors' : 'Select city first' }}
                        </option>
                        @foreach ($sectors as $sector)
                            <option value="{{ $sector->SectorId }}" @selected((string) request('sector_id') === (string) $sector->SectorId)>
                                {{ $sector->SectorName }}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2 xl:col-span-2">
                        <button type="submit"
                            class="inline-flex h-10 flex-1 items-center justify-center gap-1 rounded-lg bg-indigo-600 px-3 text-xs font-semibold text-white transition hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[17px]">filter_alt</span>
                            Apply
                        </button>
                        <a href="{{ route('full-paid-properties') }}"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50">
                            <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                        </a>
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-bold text-slate-800">Full Payment Records</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">
                        {{ number_format($properties->total()) }} filtered records
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1250px] text-left">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">ID / Application</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Property</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3 text-right">Flat Cost</th>
                                <th class="px-4 py-3 text-right">Total Paid</th>
                                <th class="px-4 py-3 text-right">Excess</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($properties as $row)
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-slate-800">Asset #{{ $row->asset_id }}</p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            App: {{ $row->application_number ?: '-' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-slate-800">
                                            {{ $row->applicant_name ?: 'Not allotted' }}</p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">{{ $row->mobile ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-700">{{ $row->asset_name ?: '-' }}</p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $row->asset_size }} {{ $row->asset_unit }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $row->district_name ?: '-' }}
                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $row->city_name ?: '-' }} / {{ $row->sector_name ?: '-' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-700">
                                        ₹{{ number_format($row->flat_cost ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <p class="font-bold text-emerald-600">
                                            ₹{{ number_format($row->total_paid ?? 0, 2) }}
                                        </p>
                                        <p class="mt-0.5 text-[9px] text-slate-400">Initial + cash receipts</p>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-indigo-600">
                                        ₹{{ number_format($row->excess_amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('properties.show', $row->asset_id) }}"
                                            class="inline-flex h-8 items-center gap-1 rounded-lg bg-indigo-50 px-3 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-14 text-center text-sm text-slate-400">
                                        No full paid property found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($properties->hasPages())
                    <div
                        class="flex flex-col gap-3 border-t border-slate-100 bg-white px-4 py-3
               sm:flex-row sm:items-center sm:justify-between">

                        <p class="text-xs text-slate-500">
                            Showing
                            <span class="font-semibold text-slate-800">
                                {{ number_format($properties->firstItem()) }}
                            </span>
                            to
                            <span class="font-semibold text-slate-800">
                                {{ number_format($properties->lastItem()) }}
                            </span>
                            of
                            <span class="font-semibold text-slate-800">
                                {{ number_format($properties->total()) }}
                            </span>
                            records
                        </p>

                        <nav class="flex flex-wrap items-center gap-1" aria-label="Pagination">

                            {{-- Previous --}}
                            @if ($properties->onFirstPage())
                                <span
                                    class="inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-lg
                           border border-slate-200 bg-slate-50 px-3 text-xs font-medium text-slate-300">
                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>
                                    <span class="hidden sm:inline">Previous</span>
                                </span>
                            @else
                                <a href="{{ $properties->previousPageUrl() }}"
                                    class="inline-flex h-9 items-center gap-1 rounded-lg border border-slate-200
                           bg-white px-3 text-xs font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>
                                    <span class="hidden sm:inline">Previous</span>
                                </a>
                            @endif

                            {{-- First page --}}
                            @if ($properties->currentPage() > 3)
                                <a href="{{ $properties->url(1) }}"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg
                           border border-slate-200 bg-white px-2 text-xs font-medium
                           text-slate-600 transition hover:border-indigo-200
                           hover:bg-indigo-50 hover:text-indigo-600">
                                    1
                                </a>

                                @if ($properties->currentPage() > 4)
                                    <span
                                        class="inline-flex h-9 min-w-7 items-center justify-center text-xs text-slate-400">
                                        &hellip;
                                    </span>
                                @endif
                            @endif

                            {{-- Nearby pages --}}
                            @foreach ($properties->getUrlRange(max(1, $properties->currentPage() - 2), min($properties->lastPage(), $properties->currentPage() + 2)) as $page => $url)
                                @if ($page === $properties->currentPage())
                                    <span aria-current="page"
                                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg
                               bg-indigo-600 px-2 text-xs font-semibold text-white shadow-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg
                               border border-slate-200 bg-white px-2 text-xs font-medium
                               text-slate-600 transition hover:border-indigo-200
                               hover:bg-indigo-50 hover:text-indigo-600">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Last page --}}
                            @if ($properties->currentPage() < $properties->lastPage() - 2)
                                @if ($properties->currentPage() < $properties->lastPage() - 3)
                                    <span
                                        class="inline-flex h-9 min-w-7 items-center justify-center text-xs text-slate-400">
                                        &hellip;
                                    </span>
                                @endif

                                <a href="{{ $properties->url($properties->lastPage()) }}"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg
                           border border-slate-200 bg-white px-2 text-xs font-medium
                           text-slate-600 transition hover:border-indigo-200
                           hover:bg-indigo-50 hover:text-indigo-600">
                                    {{ $properties->lastPage() }}
                                </a>
                            @endif

                            {{-- Next --}}
                            @if ($properties->hasMorePages())
                                <a href="{{ $properties->nextPageUrl() }}"
                                    class="inline-flex h-9 items-center gap-1 rounded-lg border border-slate-200
                           bg-white px-3 text-xs font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                    <span class="hidden sm:inline">Next</span>
                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </a>
                            @else
                                <span
                                    class="inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-lg
                           border border-slate-200 bg-slate-50 px-3 text-xs font-medium text-slate-300">
                                    <span class="hidden sm:inline">Next</span>
                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </span>
                            @endif
                        </nav>
                    </div>
                @endif
            </section>
        </div>
    </main>
@endsection
