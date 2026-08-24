@extends('layouts.mmsayDepartmentAuth')

@section('title',
    $pageTitle ??
    (($eligibility ?? 'eligible') === 'eligible'
    ? 'Eligible Allottees'
    : 'Not Eligible
    Allottees'))

@section('content')
    <main class="ml-52 min-h-screen px-5 pb-5 pt-20">

        <div class="mx-auto space-y-4">

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Header --}}
                @php
                    $currentEligibility = str_replace(
                        '_',
                        '-',
                        request()->route('eligibility') ?? ($eligibility ?? 'eligible'),
                    );

                    $isEligible = $currentEligibility === 'eligible';

                    $exportParameters = array_merge(request()->except(['page', 'after_id', 'eligibility']), [
                        'eligibility' => $currentEligibility,
                    ]);
                @endphp

                <div
                    class="flex flex-col gap-4 border-b border-slate-100 bg-white px-5 py-4
           lg:flex-row lg:items-center lg:justify-between">

                    {{-- Page heading --}}
                    <div class="min-w-0">
                        <div class="flex items-center gap-3">
                            <div @class([
                                'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
                                'bg-emerald-50 text-emerald-600' => $isEligible,
                                'bg-rose-50 text-rose-600' => !$isEligible,
                            ])>
                                <span class="material-symbols-outlined text-[21px]">
                                    {{ $isEligible ? 'verified_user' : 'person_alert' }}
                                </span>
                            </div>

                            <div class="min-w-0">
                                <h1 class="truncate text-xl font-bold text-slate-900">
                                    {{ $pageTitle ?? ($isEligible ? 'Eligible Allottees' : 'Not Eligible Allottees') }}
                                </h1>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $pageDescription ?? 'Physical verification allottee records' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2 pl-0 lg:pl-[52px]">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-lg
                       border border-slate-200 bg-slate-50 px-2.5 py-1
                       text-[11px] font-medium text-slate-500">

                                <span class="material-symbols-outlined text-[15px] text-slate-400">
                                    database
                                </span>

                                <strong class="font-semibold text-slate-700">
                                    {{ number_format($applications->total()) }}
                                </strong>

                                records found
                            </span>

                            <span @class([
                                'inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-semibold',
                                'bg-emerald-50 text-emerald-700' => $isEligible,
                                'bg-rose-50 text-rose-700' => !$isEligible,
                            ])>
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                {{ $isEligible ? 'Eligible' : 'Not Eligible' }}
                            </span>
                        </div>
                    </div>

                    {{-- Export actions --}}
                    <div class="flex shrink-0 flex-wrap items-center gap-2">

                        <a href="{{ route('verification-allottees.csv', $exportParameters) }}"
                            class="inline-flex h-10 flex-1 items-center justify-center gap-2
                   rounded-xl bg-emerald-600 px-4 text-xs font-semibold
                   text-white shadow-sm transition
                   hover:bg-emerald-700
                   sm:flex-none">

                            <span class="material-symbols-outlined text-[17px]">
                                download
                            </span>

                            Excel CSV
                        </a>

                        <a href="{{ route('verification-allottees.print', $exportParameters) }}"
                            target="_blank" rel="noopener"
                            class="inline-flex h-10 flex-1 items-center justify-center gap-2
                   rounded-xl bg-slate-800 px-4 text-xs font-semibold
                   text-white shadow-sm transition
                   hover:bg-slate-900
                   sm:flex-none">

                            <span class="material-symbols-outlined text-[17px]">
                                print
                            </span>

                            Print PDF
                        </a>
                    </div>
                </div>

                {{-- Filters --}}
                <form id="verificationAllotteesFilterForm" method="GET"
                    action="{{ route('verification-allottees.index', [
                        'eligibility' => $eligibility,
                    ]) }}"
                    class="grid grid-cols-1 gap-3 border-b border-slate-100 p-4 md:grid-cols-2 xl:grid-cols-5">

                    {{-- Search --}}
                    <input type="search" name="search" value="{{ $search ?? '' }}"
                        placeholder="Application, applicant, mobile..."
                        class="h-10 rounded-xl border border-slate-200 px-3 text-xs outline-none
               focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                    {{-- District --}}
                    <select id="district_id" name="district_id"
                        class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs
               outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                        <option value="">All Districts</option>

                        @foreach ($districts as $district)
                            <option value="{{ $district->DistrictId }}" @selected(($districtId ?? null) == $district->DistrictId)>
                                {{ $district->DistrictName }}
                            </option>
                        @endforeach
                    </select>

                    {{-- City --}}
                    <select id="city_id" name="city_id" @disabled(empty($districtId))
                        class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs
               outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100
               disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">

                        <option value="">
                            {{ !empty($districtId) ? 'All Cities' : 'Select district first' }}
                        </option>

                        @foreach ($cities as $city)
                            <option value="{{ $city->CityId }}" @selected(($cityId ?? null) == $city->CityId)>
                                {{ $city->CityName }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Sector --}}
                    <select id="sector_id" name="sector_id" @disabled(empty($cityId))
                        class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs
               outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100
               disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">

                        <option value="">
                            {{ !empty($cityId) ? 'All Sectors' : 'Select city first' }}
                        </option>

                        @foreach ($sectors as $sector)
                            <option value="{{ $sector->SectorId }}" @selected(($sectorId ?? null) == $sector->SectorId)>
                                {{ $sector->SectorName }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <button type="submit"
                            class="inline-flex h-10 flex-1 items-center justify-center gap-1.5
                   rounded-xl bg-indigo-600 px-4 text-xs font-semibold text-white
                   transition hover:bg-indigo-700">

                            <span class="material-symbols-outlined text-[17px]">
                                filter_alt
                            </span>

                            Filter
                        </button>

                        <a href="{{ route('verification-allottees.index', [
                            'eligibility' => $eligibility,
                        ]) }}"
                            title="Reset filters"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center
                   rounded-xl border border-slate-200 text-slate-500 transition
                   hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                            <span class="material-symbols-outlined text-[18px]">
                                restart_alt
                            </span>
                        </a>
                    </div>
                </form>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1150px] text-left text-xs">

                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                <th class="px-5 py-3">S.No.</th>
                                <th class="px-5 py-3">Application</th>
                                <th class="px-5 py-3">Applicant</th>
                                <th class="px-5 py-3">Property</th>
                                <th class="px-5 py-3">Location</th>
                                <th class="px-5 py-3">Category</th>
                                <th class="px-5 py-3">Verification</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($applications as $row)
                                <tr class="transition hover:bg-slate-50/70">

                                    <td class="px-5 py-3.5 font-semibold text-slate-500">
                                        {{ $applications->firstItem() + $loop->index }}
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <p class="font-bold text-slate-800">
                                            {{ $row->application_number ?: '-' }}
                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            Asset #{{ $row->asset_id }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-800">
                                            {{ $row->applicant_name ?: '-' }}
                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            {{ $row->mobile ?: '-' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-slate-700">
                                            {{ $row->asset_name ?: '-' }}
                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            {{ $row->asset_size ?: '-' }}
                                            {{ $row->asset_unit ?: '' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-slate-700">
                                            {{ $row->district_name ?: '-' }}
                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            {{ collect([$row->city_name, $row->sector_name])->filter()->implode(' / ') ?:
                                                '-' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-slate-700">
                                            {{ $row->caste_category ?: '-' }}
                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            {{ $row->marital_status ?: '-' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <span @class([
                                            'inline-flex rounded-full px-2.5 py-1 text-[9px] font-bold uppercase',
                                            'bg-emerald-50 text-emerald-600' => $eligibility === 'eligible',
                                            'bg-rose-50 text-rose-600' => $eligibility === 'not-eligible',
                                        ])>
                                            {{ $eligibility === 'eligible' ? ($row->physical_verification ?: 'Eligible') : 'Not Eligible' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-3.5 text-right">
                                        {{-- Existing common property view --}}
                                        <a href="{{ url('mmsay-department-property-registration/' . $row->asset_id) }}"
                                            class="inline-flex h-8 items-center justify-center gap-1
           rounded-lg bg-indigo-50 px-3 text-[10px]
           font-semibold text-indigo-600 transition
           hover:bg-indigo-100">

                                            <span class="material-symbols-outlined text-[15px]">
                                                visibility
                                            </span>

                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center text-slate-500">
                                        No records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($applications->hasPages())
                    <div
                        class="flex flex-col gap-3 border-t border-slate-100 bg-white px-5 py-4
               sm:flex-row sm:items-center sm:justify-between">

                        {{-- Record information --}}
                        <p class="text-xs text-slate-500">
                            Showing

                            <span class="font-semibold text-slate-800">
                                {{ number_format($applications->firstItem()) }}
                            </span>

                            to

                            <span class="font-semibold text-slate-800">
                                {{ number_format($applications->lastItem()) }}
                            </span>

                            of

                            <span class="font-semibold text-slate-800">
                                {{ number_format($applications->total()) }}
                            </span>

                            records
                        </p>

                        {{-- Pagination controls --}}
                        <nav class="flex flex-wrap items-center gap-1" aria-label="Pagination">

                            {{-- Previous --}}
                            @if ($applications->onFirstPage())
                                <span
                                    class="inline-flex h-9 cursor-not-allowed items-center gap-1
                           rounded-lg border border-slate-200 bg-slate-50 px-3
                           text-xs font-medium text-slate-300">

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>

                                    <span class="hidden sm:inline">Previous</span>
                                </span>
                            @else
                                <a href="{{ $applications->previousPageUrl() }}" rel="prev"
                                    class="inline-flex h-9 items-center gap-1 rounded-lg
                           border border-slate-200 bg-white px-3 text-xs
                           font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50
                           hover:text-indigo-600">

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>

                                    <span class="hidden sm:inline">Previous</span>
                                </a>
                            @endif

                            {{-- First page --}}
                            @if ($applications->currentPage() > 3)
                                <a href="{{ $applications->url(1) }}"
                                    class="inline-flex h-9 min-w-9 items-center justify-center
                           rounded-lg border border-slate-200 bg-white px-2
                           text-xs font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50
                           hover:text-indigo-600">
                                    1
                                </a>

                                @if ($applications->currentPage() > 4)
                                    <span
                                        class="inline-flex h-9 min-w-7 items-center justify-center
                               text-xs text-slate-400">
                                        …
                                    </span>
                                @endif
                            @endif

                            {{-- Nearby page numbers --}}
                            @foreach ($applications->getUrlRange(max(1, $applications->currentPage() - 2), min($applications->lastPage(), $applications->currentPage() + 2)) as $page => $url)
                                @if ($page === $applications->currentPage())
                                    <span aria-current="page"
                                        class="inline-flex h-9 min-w-9 items-center justify-center
                               rounded-lg bg-indigo-600 px-2 text-xs font-semibold
                               text-white shadow-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex h-9 min-w-9 items-center justify-center
                               rounded-lg border border-slate-200 bg-white px-2
                               text-xs font-medium text-slate-600 transition
                               hover:border-indigo-200 hover:bg-indigo-50
                               hover:text-indigo-600">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Last page --}}
                            @if ($applications->currentPage() < $applications->lastPage() - 2)
                                @if ($applications->currentPage() < $applications->lastPage() - 3)
                                    <span
                                        class="inline-flex h-9 min-w-7 items-center justify-center
                               text-xs text-slate-400">
                                        …
                                    </span>
                                @endif

                                <a href="{{ $applications->url($applications->lastPage()) }}"
                                    class="inline-flex h-9 min-w-9 items-center justify-center
                           rounded-lg border border-slate-200 bg-white px-2
                           text-xs font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50
                           hover:text-indigo-600">
                                    {{ $applications->lastPage() }}
                                </a>
                            @endif

                            {{-- Next --}}
                            @if ($applications->hasMorePages())
                                <a href="{{ $applications->nextPageUrl() }}" rel="next"
                                    class="inline-flex h-9 items-center gap-1 rounded-lg
                           border border-slate-200 bg-white px-3 text-xs
                           font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50
                           hover:text-indigo-600">

                                    <span class="hidden sm:inline">Next</span>

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </a>
                            @else
                                <span
                                    class="inline-flex h-9 cursor-not-allowed items-center gap-1
                           rounded-lg border border-slate-200 bg-slate-50 px-3
                           text-xs font-medium text-slate-300">

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
