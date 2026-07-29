@extends('layouts.mmsayDepartmentAuth')
@section('title', 'Physical Possession Not Eligible')

@section('content')
@php
    $filters = [
        'search' => request('search', ''),
        'district_id' => request('district_id'),
        'city_id' => request('city_id'),
        'sector_id' => request('sector_id'),
    ];
@endphp
<main class="ml-52 min-h-screen bg-slate-50 px-5 pb-6 pt-20">
    <div class="mx-auto max-w-[1800px] space-y-4">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-900">Physical Possession Not Eligible</h1>
                    <p class="mt-0.5 text-xs text-slate-500">
                        Candidates with total received amount below ₹60,000
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('physical.possession.not-eligible.csv', request()->query()) }}"
                        class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-700">
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        Excel CSV
                    </a>
                    <a href="{{ route('physical.possession.not-eligible.print', request()->query()) }}" target="_blank"
                        class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-slate-800 px-3 text-xs font-semibold text-white hover:bg-slate-900">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        Print
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('physical.possession.not-eligible') }}"
                class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-12">

                <input type="search" name="search" value="{{ $filters['search'] }}"
                    placeholder="Asset, applicant, mobile..."
                    class="h-10 rounded-lg border border-slate-200 px-3 text-xs outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 xl:col-span-4">

                <select name="district_id" id="district_id"
                    onchange="document.getElementById('city_id').value=''; document.getElementById('sector_id').value=''; this.form.submit();"
                    class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 xl:col-span-2">
                    <option value="">All Districts</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district->DistrictId }}" @selected($filters['district_id'] == $district->DistrictId)>
                            {{ $district->DistrictName }}
                        </option>
                    @endforeach
                </select>

                <select name="city_id" id="city_id"
                    onchange="document.getElementById('sector_id').value=''; this.form.submit();"
                    @disabled(!$filters['district_id'])
                    class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 xl:col-span-2">
                    <option value="">
                        {{ $filters['district_id'] ? 'All Cities' : 'Select district first' }}
                    </option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->CityId }}" @selected($filters['city_id'] == $city->CityId)>
                            {{ $city->CityName }}
                        </option>
                    @endforeach
                </select>

                <select name="sector_id" id="sector_id"
                    @disabled(!$filters['city_id'])
                    class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 xl:col-span-2">
                    <option value="">
                        {{ $filters['city_id'] ? 'All Villages / Sectors' : 'Select city first' }}
                    </option>
                    @foreach ($sectors as $sector)
                        <option value="{{ $sector->SectorId }}" @selected($filters['sector_id'] == $sector->SectorId)>
                            {{ $sector->SectorName }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2 xl:col-span-2">
                    <button class="flex h-10 flex-1 items-center justify-center rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    </button>
                    <a href="{{ route('physical.possession.not-eligible') }}"
                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Not Eligible Candidates</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">
                        {{ number_format($applications->total()) }} filtered records
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1120px] text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">ID / Application</th>
                            <th class="px-4 py-3">Applicant</th>
                            <th class="px-4 py-3">Property</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3 text-right">Total Cost</th>
                            <th class="px-4 py-3 text-right">Received</th>
                            <th class="px-4 py-3 text-right">Pending</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($applications as $application)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800">Asset #{{ $application->asset_id }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        Purchaser App: {{ $application->purchaser_application_number ?: '-' }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800">{{ $application->applicant_name ?: '-' }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">{{ $application->mobile ?: '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-700">{{ $application->asset_name ?: '-' }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        Asset #{{ $application->asset_id }} · {{ $application->asset_size }} {{ $application->asset_unit }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $application->district_name ?: '-' }}
                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        {{ $application->city_name }} / {{ $application->sector_name }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-700">
                                    ₹{{ number_format($application->flat_cost ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="font-bold text-emerald-600">
                                        ₹{{ number_format($application->received_amount ?? 0, 2) }}
                                    </p>
                                    <p class="mt-0.5 text-[9px] text-slate-400">Initial + cash receipts</p>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-rose-600">
                                    ₹{{ number_format($application->pending_amount ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center text-sm text-slate-400">
                                    No not-eligible candidates found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($applications->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $applications->onEachSide(1)->links() }}
                </div>
            @endif
        </section>
    </div>
</main>

@endsection