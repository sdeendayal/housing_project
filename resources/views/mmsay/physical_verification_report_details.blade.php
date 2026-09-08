@extends('layouts.mmsayDepartmentAuth')

@section('title', 'Physical Verification Beneficiary Details')

@section('content')

    <main class="ml-52 min-h-screen bg-slate-50 px-5 pb-6 pt-20">

        <div class="mx-auto max-w-7xl space-y-4">

            {{-- =========================================================
                HEADER
            ========================================================== --}}
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <div>

                    <h1 class="text-[26px] font-bold tracking-tight text-slate-800">
                        Physical Verification Details
                    </h1>

                    <p class="mt-1 text-[15px] text-slate-500">
                        Mukhyamantri Shehri Awas Yojana (MMSAY) - Phase-1
                    </p>

                </div>


                {{-- ACTIONS --}}
                <div class="flex items-center gap-2">

                    {{-- BACK --}}
                    <a href="{{ route('physical-verification.report', request()->except('category')) }}"
                        class="inline-flex h-11 items-center gap-2 rounded-xl
                               border border-slate-200 bg-white px-5
                               text-sm font-semibold text-slate-700
                               shadow-sm transition hover:bg-slate-50">

                        <span class="material-symbols-outlined text-[18px]">
                            arrow_back
                        </span>

                        Back

                    </a>


                    {{-- PRINT --}}
                    <a href="{{ route('physical-verification.report.details.print', request()->query()) }}" target="_blank"
                        class="inline-flex h-11 items-center gap-2 rounded-xl
                               bg-red-600 px-5 text-sm font-semibold text-white
                               shadow-sm transition hover:bg-red-700">

                        <span class="material-symbols-outlined text-[18px]">
                            print
                        </span>

                        Print

                    </a>


                    {{-- CSV --}}
                    <a href="{{ route('physical-verification.report.details.csv', request()->query()) }}"
                        class="inline-flex h-11 items-center gap-2 rounded-xl
                               bg-emerald-600 px-5 text-sm font-semibold text-white
                               shadow-sm transition hover:bg-emerald-700">

                        <span class="material-symbols-outlined text-[18px]">
                            download
                        </span>

                        CSV

                    </a>

                </div>

            </div>


            {{-- =========================================================
                FILTERS
            ========================================================== --}}
            <div
                class="mb-5 w-full rounded-2xl border border-slate-200
                        bg-white px-6 py-5 shadow-sm">

                <form method="GET" action="{{ route('physical-verification.report.details') }}"
                    class="grid grid-cols-1 items-end gap-4 lg:grid-cols-12">


                    {{-- CATEGORY --}}
                    <input type="hidden" name="category" value="{{ request('category') }}">


                    {{-- =================================================
                        PHASE
                    ================================================== --}}
                    <div class="lg:col-span-3">

                        <label for="phase" class="mb-2 block text-sm font-semibold text-slate-700">

                            PHASE

                        </label>

                        <div class="relative">

                            <span
                                class="material-symbols-outlined pointer-events-none
                                         absolute left-4 top-1/2 z-10
                                         -translate-y-1/2 text-[20px] text-indigo-500">

                                layers

                            </span>


                            <select name="phase" id="phase"
                                class="h-12 w-full rounded-xl
                                       border border-slate-200
                                       bg-slate-50
                                       pl-11 pr-10
                                       text-sm font-medium text-slate-700
                                       outline-none transition
                                       hover:border-slate-300
                                       focus:border-indigo-500
                                       focus:bg-white
                                       focus:ring-2 focus:ring-indigo-100">

                                <option value="">
                                    All Phases
                                </option>

                                @foreach ($phases as $phase)
                                    <option value="{{ $phase }}" @selected(request('phase') == $phase)>

                                        {{ $phase }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- =================================================
                        DISTRICT
                    ================================================== --}}
                    <div class="lg:col-span-3">

                        <label for="district_id" class="mb-2 block text-sm font-semibold text-slate-700">

                            DISTRICT

                        </label>

                        <div class="relative">

                            <span
                                class="material-symbols-outlined pointer-events-none
                                         absolute left-4 top-1/2 z-10
                                         -translate-y-1/2 text-[20px] text-indigo-500">

                                location_on

                            </span>


                            <select name="district_id" id="district_id"
                                class="h-12 w-full rounded-xl
                                       border border-slate-200
                                       bg-slate-50
                                       pl-11 pr-10
                                       text-sm font-medium text-slate-700
                                       outline-none transition
                                       hover:border-slate-300
                                       focus:border-indigo-500
                                       focus:bg-white
                                       focus:ring-2 focus:ring-indigo-100">

                                <option value="">
                                    All Districts
                                </option>

                                @foreach ($districts as $district)
                                    <option value="{{ $district->DistrictId }}"
                                        @selected(request('district_id') == $district->DistrictId)>

                                        {{ $district->DistrictName }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- =================================================
                        TOWN / ULB
                    ================================================== --}}
                    <div class="lg:col-span-3">

                        <label for="city_id" class="mb-2 block text-sm font-semibold text-slate-700">

                            TOWN / ULB

                        </label>

                        <div class="relative">

                            <span
                                class="material-symbols-outlined pointer-events-none
                                         absolute left-4 top-1/2 z-10
                                         -translate-y-1/2 text-[20px] text-indigo-500">

                                location_city

                            </span>


                            <select name="city_id" id="city_id"
                                class="h-12 w-full rounded-xl
                                       border border-slate-200
                                       bg-slate-50
                                       pl-11 pr-10
                                       text-sm font-medium text-slate-700
                                       outline-none transition
                                       hover:border-slate-300
                                       focus:border-indigo-500
                                       focus:bg-white
                                       focus:ring-2 focus:ring-indigo-100">

                                <option value="">
                                    All Towns / ULBs
                                </option>

                                @foreach ($cities as $city)
                                    <option value="{{ $city->CityId }}" data-district="{{ $city->DistrictId }}"
                                        @selected(request('city_id') == $city->CityId)>

                                        {{ $city->CityName }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- =================================================
                        SECTOR
                    ================================================== --}}
                    <div class="lg:col-span-3">

                        <label for="sector_id" class="mb-2 block text-sm font-semibold text-slate-700">

                            SECTOR

                        </label>

                        <div class="relative">

                            <span
                                class="material-symbols-outlined pointer-events-none
                                         absolute left-4 top-1/2 z-10
                                         -translate-y-1/2 text-[20px] text-indigo-500">

                                apartment

                            </span>


                            <select name="sector_id" id="sector_id"
                                class="h-12 w-full rounded-xl
                                       border border-slate-200
                                       bg-slate-50
                                       pl-11 pr-10
                                       text-sm font-medium text-slate-700
                                       outline-none transition
                                       hover:border-slate-300
                                       focus:border-indigo-500
                                       focus:bg-white
                                       focus:ring-2 focus:ring-indigo-100">

                                <option value="">
                                    All Sectors
                                </option>

                                @foreach ($sectors as $sector)
                                    <option value="{{ $sector->SectorId }}"
                                        @selected(request('sector_id') == $sector->SectorId)>

                                        {{ $sector->SectorName }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- =================================================
                        ACTIONS
                    ================================================== --}}
                    <div class="flex items-center gap-2 lg:col-span-12">

                        <button type="submit"
                            class="inline-flex h-12 flex-1 items-center
                                   justify-center gap-2 rounded-xl
                                   bg-orange-600 px-5 text-sm font-semibold
                                   text-white shadow-sm transition
                                   hover:bg-orange-700
                                   focus:outline-none
                                   focus:ring-2 focus:ring-orange-200">

                            <span class="material-symbols-outlined text-[19px]">
                                filter_alt
                            </span>

                            Apply Filters

                        </button>


                        <a href="{{ route('physical-verification.report.details', [
                            'category' => request('category'),
                        ]) }}"
                            title="Reset Filters"
                            class="inline-flex h-12 w-12 shrink-0 items-center
                                   justify-center rounded-xl
                                   border border-slate-200 bg-white
                                   text-slate-500 transition
                                   hover:border-red-200 hover:bg-red-50
                                   hover:text-red-500">

                            <span class="material-symbols-outlined text-[20px]">
                                restart_alt
                            </span>

                        </a>

                    </div>

                </form>

            </div>


            {{-- =========================================================
                REPORT CARD
            ========================================================== --}}
            <div
                class="w-full overflow-hidden rounded-2xl
                        border border-slate-200 bg-white shadow-sm">


                {{-- HEADER --}}
                <div
                    class="flex flex-col gap-4 border-b border-slate-200
                            px-6 py-5 sm:flex-row sm:items-center
                            sm:justify-between">

                    <div>

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center
                                        justify-center rounded-xl
                                        bg-indigo-50 text-indigo-600">

                                <span class="material-symbols-outlined text-[21px]">
                                    fact_check
                                </span>

                            </div>


                            <div>

                                <h2 class="text-lg font-bold text-slate-800">

                                    Beneficiary Details

                                </h2>

                                <p class="mt-0.5 text-sm text-slate-500">

                                    @php
                                        $categoryLabels = [
                                            'ghumantu' => 'Ghumantu Jati',
                                            'widow' => 'Widows',
                                            'scheduled_caste' => 'Scheduled Caste',
                                            'others' => 'Others',
                                        ];
                                    @endphp

                                    {{ $categoryLabels[$category] ?? 'All Categories' }}

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- TOTAL --}}
                    <div class="rounded-xl bg-slate-50 px-5 py-3 text-right">

                        <p
                            class="text-xs font-medium uppercase
                                  tracking-wide text-slate-500">

                            Total Beneficiaries

                        </p>

                        <p class="mt-0.5 text-2xl font-bold text-slate-800">

                            {{ number_format(
                                $beneficiaries instanceof \Illuminate\Pagination\LengthAwarePaginator
                                    ? $beneficiaries->total()
                                    : $beneficiaries->count()
                            ) }}

                        </p>

                    </div>

                </div>


                {{-- =====================================================
                    TABLE
                ====================================================== --}}
                <div class="w-full overflow-hidden">

                    <table class="w-full table-fixed border-collapse text-[10px]">

                        <thead>
                            <tr class="bg-slate-50">

                                <th class="w-[3%] border-b border-r border-slate-200 px-2 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    S.No
                                </th>

                                <th class="w-[9%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Name
                                </th>

                                <th class="w-[9%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Father Name
                                </th>

                                <th class="w-[8%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Registration No.
                                </th>

                                <th class="w-[7%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Caste
                                </th>

                                <th class="w-[8%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Mobile
                                </th>

                                <th class="w-[12%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Address
                                </th>

                                <th class="w-[8%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Asset Name
                                </th>

                                <th class="w-[8%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    District
                                </th>

                                <th class="w-[7%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    City / ULB
                                </th>

                                <th class="w-[6%] border-b border-r border-slate-200 px-2 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Sector
                                </th>

                                <th class="w-[6%] border-b border-r border-slate-200 px-2 py-3 text-right text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Flat Cost
                                </th>

                                <th class="w-[9%] border-b border-slate-200 px-2 py-3 text-right text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    Total Paid
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($beneficiaries as $index => $beneficiary)

                                <tr class="border-b border-slate-100 bg-white transition hover:bg-indigo-50/40">

                                    <td class="border-r border-slate-100 px-2 py-2 text-center text-[10px] text-slate-600">
                                        {{ $beneficiaries instanceof \Illuminate\Pagination\LengthAwarePaginator
    ? (int) $beneficiaries->firstItem() + $loop->index
    : $loop->iteration }}
                                    </td>

                                    <td class="border-r border-slate-100 px-2 py-2 text-[10px] font-semibold text-slate-800">
                                        <div class="break-words">
                                            {{ $beneficiary->PrivatePurchaserName ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="border-r border-slate-100 px-2 py-2 text-[10px] text-slate-700">
                                        <div class="break-words">
                                            {{ $beneficiary->PurchaserFatherName ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="border-r border-slate-100 px-2 py-2 text-[10px] font-medium text-slate-700">
                                        <div class="break-words">
                                            {{ $beneficiary->ApplicationNo ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- CASTE --}}
                                    <td class="border-r border-slate-100 px-2 py-2 text-[10px] text-slate-700">
                                        <div class="break-words">
                                            {{ $beneficiary->CasteCategoryName ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="border-r border-slate-100 px-2 py-2 text-[10px] text-slate-700">
                                        {{ $beneficiary->MobileNo ?? '-' }}
                                    </td>

                                    <td class="border-r border-slate-100 px-2 py-2 text-[10px] text-slate-700">
                                        <div class="break-words">
                                            {{ $beneficiary->Address ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="border-r border-slate-100 px-2 py-2 text-[10px] font-medium text-slate-800">
                                        <div class="break-words">
                                            {{ $beneficiary->AssetName ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- DISTRICT --}}
                                    <td class="border-r border-slate-100 px-2 py-2 text-[9px] text-slate-700 align-top">
                                        <div class="break-words">
                                            {{ $beneficiary->DistrictName ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- CITY / ULB --}}
                                    <td class="border-r border-slate-100 px-2 py-2 text-[9px] text-slate-700 align-top">
                                        <div class="break-words">
                                            {{ $beneficiary->CityName ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- SECTOR --}}
                                    <td class="border-r border-slate-100 px-2 py-2 text-[9px] text-slate-700 align-top">
                                        <div class="break-words">
                                            {{ $beneficiary->SectorName ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="border-r border-slate-100 px-2 py-2 text-right text-[10px] font-semibold tabular-nums text-slate-800 whitespace-nowrap">
                                        ₹{{ number_format((float) ($beneficiary->FlatCost ?? 0), 2) }}
                                    </td>

                                    <td class="px-2 py-2 text-right text-[10px] font-bold tabular-nums text-emerald-600 whitespace-nowrap">
                                        ₹{{ number_format((float) ($beneficiary->TotalPaid ?? 0), 2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="13" class="px-5 py-12 text-center">

                                        <div class="flex flex-col items-center justify-center">

                                            <span class="material-symbols-outlined text-5xl text-slate-300">
                                                folder_open
                                            </span>

                                            <p class="mt-2 text-sm font-medium text-slate-500">
                                                No beneficiaries found
                                            </p>

                                            <p class="mt-1 text-xs text-slate-400">
                                                Try changing the selected filters.
                                            </p>

                                        </div>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

        </div>


        {{-- =========================================================
            PAGINATION
        ========================================================== --}}
        @if ($beneficiaries instanceof \Illuminate\Pagination\LengthAwarePaginator)

            <div class="flex flex-col gap-3 border-t border-slate-200
                        bg-white px-5 py-4 sm:flex-row sm:items-center
                        sm:justify-between">

                <div class="text-sm text-slate-500">
                    Showing
                    <span class="font-semibold text-slate-700">
                        {{ number_format($beneficiaries->firstItem() ?? 0) }}
                    </span>
                    to
                    <span class="font-semibold text-slate-700">
                        {{ number_format($beneficiaries->lastItem() ?? 0) }}
                    </span>
                    of
                    <span class="font-semibold text-slate-700">
                        {{ number_format($beneficiaries->total()) }}
                    </span>
                    records
                </div>

                @if ($beneficiaries->hasPages())

                    <div class="flex items-center gap-1.5">

                        @if ($beneficiaries->onFirstPage())
                            <span class="inline-flex h-10 items-center gap-1
                                         rounded-xl border border-slate-200
                                         bg-slate-50 px-3 text-sm font-medium
                                         text-slate-300">
                                <span class="material-symbols-outlined text-[18px]">
                                    chevron_left
                                </span>
                                Previous
                            </span>
                        @else
                            <a href="{{ $beneficiaries->previousPageUrl() }}"
                               class="inline-flex h-10 items-center gap-1
                                      rounded-xl border border-slate-200
                                      bg-white px-3 text-sm font-medium
                                      text-slate-600 transition hover:bg-slate-50">
                                <span class="material-symbols-outlined text-[18px]">
                                    chevron_left
                                </span>
                                Previous
                            </a>
                        @endif

                        @php
                            $currentPage = $beneficiaries->currentPage();
                            $lastPage = $beneficiaries->lastPage();

                            $startPage = max(1, $currentPage - 1);
                            $endPage = min($lastPage, $currentPage + 1);

                            if ($currentPage === 1) {
                                $endPage = min($lastPage, 3);
                            }

                            if ($currentPage === $lastPage) {
                                $startPage = max(1, $lastPage - 2);
                            }
                        @endphp

                        @for ($page = $startPage; $page <= $endPage; $page++)

                            @if ($page == $currentPage)
                                <span class="inline-flex h-10 min-w-10
                                             items-center justify-center
                                             rounded-xl bg-indigo-600 px-3
                                             text-sm font-semibold text-white shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $beneficiaries->url($page) }}"
                                   class="inline-flex h-10 min-w-10
                                          items-center justify-center
                                          rounded-xl border border-slate-200
                                          bg-white px-3 text-sm font-medium
                                          text-slate-600 transition hover:bg-slate-50">
                                    {{ $page }}
                                </a>
                            @endif

                        @endfor

                        @if ($beneficiaries->hasMorePages())
                            <a href="{{ $beneficiaries->nextPageUrl() }}"
                               class="inline-flex h-10 items-center gap-1
                                      rounded-xl border border-slate-200
                                      bg-white px-3 text-sm font-medium
                                      text-slate-600 transition hover:bg-slate-50">
                                Next
                                <span class="material-symbols-outlined text-[18px]">
                                    chevron_right
                                </span>
                            </a>
                        @else
                            <span class="inline-flex h-10 items-center gap-1
                                         rounded-xl border border-slate-200
                                         bg-slate-50 px-3 text-sm font-medium
                                         text-slate-300">
                                Next
                                <span class="material-symbols-outlined text-[18px]">
                                    chevron_right
                                </span>
                            </span>
                        @endif

                    </div>
                @endif

            </div>

        @endif

    </main>


    {{-- =========================================================
        CITY FILTER SCRIPT
    ========================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const districtSelect = document.getElementById('district_id');
            const citySelect = document.getElementById('city_id');

            if (!districtSelect || !citySelect) {
                return;
            }

            function filterCities() {

                const districtId = districtSelect.value;

                Array.from(citySelect.options).forEach(function(option) {

                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    const optionDistrict =
                        option.getAttribute('data-district');

                    option.hidden =
                        districtId !== '' &&
                        optionDistrict !== districtId;

                });


                const selectedOption =
                    citySelect.options[citySelect.selectedIndex];

                if (
                    selectedOption &&
                    selectedOption.hidden
                ) {
                    citySelect.value = '';
                }

            }


            districtSelect.addEventListener(
                'change',
                filterCities
            );


            filterCities();

        });
    </script>

@endsection
