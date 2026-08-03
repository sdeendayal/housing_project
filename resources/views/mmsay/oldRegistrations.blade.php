@extends('layouts.mmsayDepartmentAuth')

@section('title', 'MMSAY - Property Registrations')

@section('content')
    @php
        $propertyDetails = static function ($value): array {
            $decoded = json_decode((string) $value, true);

            if (!is_array($decoded)) {
                return trim((string) $value) !== '' ? [['label' => 'Details', 'value' => trim((string) $value)]] : [];
            }

            $details = [];
            foreach ($decoded as $key => $item) {
                if ($key === 'id' || $item === null || $item === '') {
                    continue;
                }

                $displayValue = is_array($item)
                    ? implode(', ', array_filter(array_map('strval', $item)))
                    : (string) $item;

                if ($displayValue !== '') {
                    $details[] = [
                        'label' => ucwords(str_replace(['_', '-'], ' ', (string) $key)),
                        'value' => $displayValue,
                    ];
                }
            }

            return $details;
        };
    @endphp

    <main class="ml-52 min-h-screen bg-slate-50 px-4 pb-6 pt-20">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 p-5">
                <div class="space-y-4">
                    <div class="shrink-0">
                        <h1 class="text-xl font-bold text-slate-900">Property Registration</h1>
                        <p class="mt-1 text-sm text-slate-400">
                            {{ number_format($registrations->total()) }} records found
                        </p>
                    </div>

                    <form method="GET" action="{{ route('old-registrations.index') }}"
                        class="flex w-full flex-wrap items-center gap-2">

                        <div class="relative min-w-[220px] flex-[1.4]">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">search</span>
                            <input type="search" name="search" value="{{ $filters['search'] }}"
                                placeholder="Application, name, mobile..."
                                class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        </div>

                        <select name="district_id" id="district_id"
                            class="h-11 min-w-[160px] flex-1 rounded-xl border border-slate-200 bg-white px-3 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            <option value="">All Districts</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->DistrictId }}" @selected($filters['district_id'] == $district->DistrictId)>
                                    {{ $district->DistrictName }}
                                </option>
                            @endforeach
                        </select>

                        <select name="city_id" id="city_id" @disabled(!$filters['district_id'])
                            class="h-11 min-w-[160px] flex-1 rounded-xl border border-slate-200 bg-white px-3 text-sm outline-none disabled:bg-slate-100 disabled:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            <option value="">{{ $filters['district_id'] ? 'All Cities' : 'Select district first' }}
                            </option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->CityId }}" @selected($filters['city_id'] == $city->CityId)>
                                    {{ $city->CityName }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sector_id" id="sector_id" @disabled(!$filters['city_id'])
                            class="h-11 min-w-[160px] flex-1 rounded-xl border border-slate-200 bg-white px-3 text-sm outline-none disabled:bg-slate-100 disabled:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            <option value="">{{ $filters['city_id'] ? 'All Sectors' : 'Select city first' }}</option>
                            @foreach ($sectors as $sector)
                                <option value="{{ $sector->SectorId }}" @selected($filters['sector_id'] == $sector->SectorId)>
                                    {{ $sector->SectorName }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="inline-flex h-11 items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span> Filter
                        </button>

                        <a href="{{ route('old-registrations.index') }}" title="Reset"
                            class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-3 text-slate-500 transition hover:bg-slate-50">
                            <span class="material-symbols-outlined text-[19px]">restart_alt</span>
                        </a>

                        <a href="{{ route('old-registrations.csv', request()->query()) }}"
                            class="inline-flex h-11 items-center justify-center gap-1.5 rounded-xl bg-teal-600 px-4 text-sm font-semibold text-white transition hover:bg-teal-700">
                            <span class="material-symbols-outlined text-[18px]">download</span> CSV
                        </a>

                        <a href="{{ route('old-registrations.print', request()->query()) }}" target="_blank"
                            class="inline-flex h-11 items-center justify-center gap-1.5 rounded-xl bg-slate-800 px-4 text-sm font-semibold text-white transition hover:bg-slate-900">
                            <span class="material-symbols-outlined text-[18px]">print</span> Print
                        </a>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[1120px] w-full text-left text-sm">
                    <thead
                        class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="w-16 px-3 py-3 text-center">S.No.</th>
                            <th class="px-5 py-3">ID / Application</th>
                            <th class="px-4 py-3">Applicant</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Family / Member</th>
                            <th class="px-4 py-3">Profile</th>
                            <th class="px-5 py-3">Registration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($registrations as $row)
                            <tr class="transition hover:bg-indigo-50/30">
                                <td class="px-3 py-3.5 text-center font-semibold text-slate-500">
                                    {{ number_format(($registrations->firstItem() ?? 1) + $loop->index) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-900">{{ $row->application_number ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">Record #{{ $row->id }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-slate-900">{{ $row->fullName ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $row->mobileNo ?: '-' }}</p>
                                    <p class="mt-1 text-[11px] text-slate-400">Father: {{ $row->fatherFullName ?: '-' }}
                                    </p>
                                </td>

                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-slate-700">{{ $row->districtName ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $row->btName ?: '-' }} / {{ $row->wvName ?: '-' }}
                                    </p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-slate-700">Family: {{ $row->family_id ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">Member: {{ $row->memberID ?: '-' }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-slate-700">
                                        {{ $row->gender ?: '-' }} · Age {{ $row->age ?: '-' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $row->casteCategoryName ?: '-' }} · {{ $row->occupationName ?: '-' }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-slate-700">
                                        {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d M Y') : '-' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-400">PIN: {{ $row->pinCode ?: '-' }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-14 text-center text-sm text-slate-400">
                                    No registration record found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($registrations->hasPages())
                <div
                    class="flex flex-col gap-3 border-t border-slate-100 bg-white px-5 py-4
               sm:flex-row sm:items-center sm:justify-between">

                    {{-- Record count --}}
                    <p class="text-xs text-slate-500">
                        Showing
                        <span class="font-semibold text-slate-800">
                            {{ number_format($registrations->firstItem()) }}
                        </span>
                        to
                        <span class="font-semibold text-slate-800">
                            {{ number_format($registrations->lastItem()) }}
                        </span>
                        of
                        <span class="font-semibold text-slate-800">
                            {{ number_format($registrations->total()) }}
                        </span>
                        records
                    </p>

                    <nav class="flex flex-wrap items-center gap-1" aria-label="Pagination">

                        {{-- Previous --}}
                        @if ($registrations->onFirstPage())
                            <span
                                class="inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-lg
                           border border-slate-200 bg-slate-50 px-3 text-xs font-medium
                           text-slate-300">
                                <span class="material-symbols-outlined text-[16px]">
                                    chevron_left
                                </span>
                                Previous
                            </span>
                        @else
                            <a href="{{ $registrations->previousPageUrl() }}"
                                class="inline-flex h-9 items-center gap-1 rounded-lg border border-slate-200
                           bg-white px-3 text-xs font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                <span class="material-symbols-outlined text-[16px]">
                                    chevron_left
                                </span>
                                Previous
                            </a>
                        @endif

                        {{-- First page --}}
                        @if ($registrations->currentPage() > 3)
                            <a href="{{ $registrations->url(1) }}"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg
                           border border-slate-200 bg-white px-2 text-xs font-medium
                           text-slate-600 hover:border-indigo-200 hover:bg-indigo-50">
                                1
                            </a>

                            @if ($registrations->currentPage() > 4)
                                <span class="inline-flex h-9 min-w-7 items-center justify-center text-slate-400">
                                    &hellip;
                                </span>
                            @endif
                        @endif

                        {{-- Nearby pages --}}
                        @foreach ($registrations->getUrlRange(max(1, $registrations->currentPage() - 2), min($registrations->lastPage(), $registrations->currentPage() + 2)) as $page => $url)
                            @if ($page === $registrations->currentPage())
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
                        @if ($registrations->currentPage() < $registrations->lastPage() - 2)
                            @if ($registrations->currentPage() < $registrations->lastPage() - 3)
                                <span class="inline-flex h-9 min-w-7 items-center justify-center text-slate-400">
                                    &hellip;
                                </span>
                            @endif

                            <a href="{{ $registrations->url($registrations->lastPage()) }}"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg
                           border border-slate-200 bg-white px-2 text-xs font-medium
                           text-slate-600 hover:border-indigo-200 hover:bg-indigo-50">
                                {{ $registrations->lastPage() }}
                            </a>
                        @endif

                        {{-- Next --}}
                        @if ($registrations->hasMorePages())
                            <a href="{{ $registrations->nextPageUrl() }}"
                                class="inline-flex h-9 items-center gap-1 rounded-lg border border-slate-200
                           bg-white px-3 text-xs font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                Next
                                <span class="material-symbols-outlined text-[16px]">
                                    chevron_right
                                </span>
                            </a>
                        @else
                            <span
                                class="inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-lg
                           border border-slate-200 bg-slate-50 px-3 text-xs font-medium
                           text-slate-300">
                                Next
                                <span class="material-symbols-outlined text-[16px]">
                                    chevron_right
                                </span>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </section>
    </main>


@endsection
