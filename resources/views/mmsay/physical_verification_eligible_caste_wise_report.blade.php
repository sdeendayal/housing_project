@extends('layouts.mmsayDepartmentAuth')

@section('title', 'Physical Verification - Caste Wise')

@section('content')

    <main class="ml-52 min-h-screen bg-slate-50 px-5 pb-8 pt-20">

        <div class="mx-auto max-w-7xl space-y-5">

            {{-- HEADER --}}
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <h1 class="text-[26px] font-bold tracking-tight text-slate-800">
                        Physical Verification Report
                    </h1>

                    <p class="mt-1 text-[15px] text-slate-500">
                        MMSAY (1st Phase) - One Marla Plots Allotment
                        done in June 2024
                    </p>
                </div>

                <div class="flex items-center gap-2">

                    <a href="{{ route('physical-verification.eligible-caste-wise.print', request()->query()) }}"
                        target="_blank"
                        class="inline-flex h-11 items-center gap-2 rounded-xl
                          bg-red-600 px-5 text-sm font-semibold text-white
                          shadow-sm transition hover:bg-red-700">

                        <span class="material-symbols-outlined text-[18px]">
                            print
                        </span>

                        Print
                    </a>

                    <a href="{{ route('physical-verification.eligible-caste-wise.csv', request()->query()) }}"
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


            {{-- FILTERS --}}
            <div class="w-full rounded-2xl border border-slate-200
                    bg-white px-6 py-5 shadow-sm">

                <form method="GET" action="{{ route('physical-verification.eligible-caste-wise') }}"
                    class="grid grid-cols-1 items-end gap-4 lg:grid-cols-12">

                    {{-- PHASE --}}
                    <div class="lg:col-span-3">

                        <label for="phase" class="mb-2 block text-sm font-semibold text-slate-700">
                            PHASE
                        </label>

                        <div class="relative">

                            <span
                                class="material-symbols-outlined pointer-events-none
                                   absolute left-4 top-1/2 z-10
                                   -translate-y-1/2 text-[20px]
                                   text-indigo-500">
                                layers
                            </span>

                            <select name="phase" id="phase"
                                class="h-12 w-full rounded-xl border
                                   border-slate-200 bg-slate-50
                                   pl-11 pr-10 text-sm font-medium
                                   text-slate-700 outline-none
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


                    {{-- DISTRICT --}}
                    <div class="lg:col-span-3">

                        <label for="district_id" class="mb-2 block text-sm font-semibold text-slate-700">
                            DISTRICT
                        </label>

                        <div class="relative">

                            <span
                                class="material-symbols-outlined pointer-events-none
                                   absolute left-4 top-1/2 z-10
                                   -translate-y-1/2 text-[20px]
                                   text-indigo-500">
                                location_on
                            </span>

                            <select name="district_id" id="district_id"
                                class="h-12 w-full rounded-xl border
                                   border-slate-200 bg-slate-50
                                   pl-11 pr-10 text-sm font-medium
                                   text-slate-700 outline-none
                                   focus:border-indigo-500
                                   focus:bg-white
                                   focus:ring-2 focus:ring-indigo-100">

                                <option value="">
                                    All Districts
                                </option>

                                @foreach ($districts as $district)
                                    <option value="{{ $district->DistrictId }}" @selected(request('district_id') == $district->DistrictId)>
                                        {{ $district->DistrictName }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- TOWN / ULB - DEPENDENT ON DISTRICT --}}
                    <div class="lg:col-span-3">

                        <label for="city_id" class="mb-2 block text-sm font-semibold text-slate-700">
                            TOWN / ULB
                        </label>

                        <div class="relative">

                            <span
                                class="material-symbols-outlined pointer-events-none
                                   absolute left-4 top-1/2 z-10
                                   -translate-y-1/2 text-[20px]
                                   text-indigo-500">
                                location_city
                            </span>

                            <select name="city_id" id="city_id"
                                class="h-12 w-full rounded-xl border
                                   border-slate-200 bg-slate-50
                                   pl-11 pr-10 text-sm font-medium
                                   text-slate-700 outline-none
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


                    {{-- ACTIONS --}}
                    <div class="flex items-center gap-2 lg:col-span-3">

                        <button type="submit"
                            class="inline-flex h-12 flex-1 items-center
                               justify-center gap-2 rounded-xl
                               bg-orange-600 px-5 text-sm font-semibold
                               text-white shadow-sm transition
                               hover:bg-orange-700">

                            <span class="material-symbols-outlined text-[19px]">
                                filter_alt
                            </span>

                            Apply Filters

                        </button>


                        <a href="{{ route('physical-verification.eligible-caste-wise') }}" title="Reset Filters"
                            class="inline-flex h-12 w-12 shrink-0
                               items-center justify-center rounded-xl
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


            {{-- REPORT --}}
            <div class="overflow-hidden rounded-2xl border
                    border-slate-200 bg-white shadow-sm">

                {{-- REPORT HEADER --}}
                <div
                    class="flex flex-col gap-4 border-b border-slate-200
                        px-6 py-5 sm:flex-row sm:items-center
                        sm:justify-between">

                    <div>

                        <h2 class="text-lg font-bold text-slate-800">
                            Status after verification of
                            {{ number_format($grandTotalPlots) }} plots
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Eligible caste-wise count and final not eligible count
                        </p>

                    </div>


                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">

                        <div class="rounded-xl bg-slate-50 px-4 py-3 text-center">

                            <p
                                class="text-[10px] font-semibold uppercase
                                  tracking-wide text-slate-500">
                                Total Plots
                            </p>

                            <p class="mt-1 text-xl font-bold text-slate-800">
                                {{ number_format($grandTotalPlots) }}
                            </p>

                        </div>


                        <div class="rounded-xl bg-slate-50 px-4 py-3 text-center">

                            <p
                                class="text-[10px] font-semibold uppercase
                                  tracking-wide text-slate-500">
                                Total Eligible
                            </p>

                            <p class="mt-1 text-xl font-bold text-slate-800">
                                {{ number_format($grandEligible) }}
                            </p>

                        </div>


                        <div class="rounded-xl bg-slate-50 px-4 py-3 text-center">

                            <p
                                class="text-[10px] font-semibold uppercase
                                  tracking-wide text-slate-500">
                                Not Eligible
                            </p>

                            <p class="mt-1 text-xl font-bold text-slate-800">
                                {{ number_format($grandNotEligible) }}
                            </p>

                        </div>

                    </div>

                </div>


                {{-- TABLE --}}
                <div class="w-full overflow-x-auto">

                    <table class="w-full min-w-[1050px] border-collapse text-sm">

                        <thead>

                            <tr class="bg-slate-50">

                                <th rowspan="2"
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-bold uppercase text-slate-700">
                                    Sr. No.
                                </th>

                                <th rowspan="2"
                                    class="border border-slate-300 px-3 py-3 text-left
                                       text-xs font-bold uppercase text-slate-700">
                                    Towns
                                </th>

                                <th rowspan="2"
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-bold uppercase text-slate-700">
                                    Plots allotted<br>Year 2024
                                </th>

                                <th colspan="5"
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-bold uppercase text-slate-700">
                                    Eligible Allottees - Caste Wise
                                </th>

                                <th rowspan="2"
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-bold uppercase text-slate-700">
                                    Not Eligible
                                </th>

                            </tr>


                            <tr class="bg-slate-50">

                                <th
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-semibold text-slate-700">
                                    Ghumantu
                                </th>

                                <th
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-semibold text-slate-700">
                                    Widow
                                </th>

                                <th
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-semibold text-slate-700">
                                    SC
                                </th>

                                <th
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-semibold text-slate-700">
                                    Others
                                </th>

                                <th
                                    class="border border-slate-300 px-3 py-3 text-center
                                       text-xs font-semibold text-slate-700">
                                    Total Eligible
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($rows as $index => $row)
                                @php
                                    /*
            |--------------------------------------------------------------------------
            | Common parameters for this Town
            |--------------------------------------------------------------------------
            */
                                    $townParams = [
                                        'phase' => request('phase', '1'),
                                        'district_id' => $row->DistrictId,
                                        'city_id' => $row->CityId,
                                    ];

                                    /*
            |--------------------------------------------------------------------------
            | Category URL Helper
            |--------------------------------------------------------------------------
            */
                                    $detailUrl = function ($category) use ($townParams) {
                                        return route(
                                            'physical-verification.report.details',
                                            array_merge($townParams, [
                                                'category' => $category,
                                            ]),
                                        );
                                    };
                                @endphp


                                <tr class="hover:bg-slate-50">

                                    {{-- =========================================================
                 S.NO
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 text-center">
                                        {{ $index + 1 }}
                                    </td>


                                    {{-- =========================================================
                 TOWN
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 font-medium text-slate-800">

                                        <a href="{{ $detailUrl('all') }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ $row->CityName }}
                                        </a>

                                    </td>


                                    {{-- =========================================================
                 PLOTS ALLOTTED
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 text-center font-semibold">

                                        <a href="{{ $detailUrl('all') }}"
                                            class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->plots_allotted) }}
                                        </a>

                                    </td>


                                    {{-- =========================================================
                 GHUMANTU
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 text-center">

                                        <a href="{{ $detailUrl('ghumantu') }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->ghumantu) }}
                                        </a>

                                    </td>


                                    {{-- =========================================================
                 WIDOW
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 text-center">

                                        <a href="{{ $detailUrl('widow') }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->widow) }}
                                        </a>

                                    </td>


                                    {{-- =========================================================
                 SC
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 text-center">

                                        <a href="{{ $detailUrl('scheduled_caste') }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->scheduled_caste) }}
                                        </a>

                                    </td>


                                    {{-- =========================================================
                 OTHERS
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 text-center">

                                        <a href="{{ $detailUrl('others') }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->others) }}
                                        </a>

                                    </td>


                                    {{-- =========================================================
                 TOTAL ELIGIBLE
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 text-center font-bold">

                                        <a href="{{ $detailUrl('eligible') }}"
                                            class="font-bold text-emerald-600 hover:text-emerald-800 hover:underline">
                                            {{ number_format($row->eligible_total) }}
                                        </a>

                                    </td>


                                    {{-- =========================================================
                 NOT ELIGIBLE
            ========================================================== --}}
                                    <td class="border border-slate-300 px-3 py-3 text-center font-bold">

                                        <a href="{{ $detailUrl('not_eligible') }}"
                                            class="font-bold text-red-600 hover:text-red-800 hover:underline">
                                            {{ number_format($row->not_eligible) }}
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="9"
                                        class="border border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                                        No records found.
                                    </td>

                                </tr>
                            @endforelse


                            {{-- ================================================================
         GRAND TOTAL
    ================================================================= --}}
                            <tr class="bg-slate-100 font-bold">

                                <td colspan="2" class="border border-slate-300 px-3 py-3 text-center">
                                    Total
                                </td>


                                {{-- TOTAL PLOTS --}}
                                <td class="border border-slate-300 px-3 py-3 text-center">

                                    <a href="{{ route('physical-verification.report.details', array_merge(request()->query(), ['category' => 'all'])) }}"
                                        class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ number_format($grandTotalPlots) }}
                                    </a>

                                </td>


                                {{-- GHUMANTU --}}
                                <td class="border border-slate-300 px-3 py-3 text-center">

                                    <a href="{{ route('physical-verification.report.details', array_merge(request()->query(), ['category' => 'ghumantu'])) }}"
                                        class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ number_format($grandGhumantu) }}
                                    </a>

                                </td>


                                {{-- WIDOW --}}
                                <td class="border border-slate-300 px-3 py-3 text-center">

                                    <a href="{{ route('physical-verification.report.details', array_merge(request()->query(), ['category' => 'widow'])) }}"
                                        class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ number_format($grandWidow) }}
                                    </a>

                                </td>


                                {{-- SC --}}
                                <td class="border border-slate-300 px-3 py-3 text-center">

                                    <a href="{{ route(
                                        'physical-verification.report.details',
                                        array_merge(request()->query(), ['category' => 'scheduled_caste']),
                                    ) }}"
                                        class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ number_format($grandScheduledCaste) }}
                                    </a>

                                </td>


                                {{-- OTHERS --}}
                                <td class="border border-slate-300 px-3 py-3 text-center">

                                    <a href="{{ route('physical-verification.report.details', array_merge(request()->query(), ['category' => 'others'])) }}"
                                        class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ number_format($grandOthers) }}
                                    </a>

                                </td>


                                {{-- TOTAL ELIGIBLE --}}
                                <td class="border border-slate-300 px-3 py-3 text-center">

                                    <a href="{{ route('physical-verification.report.details', array_merge(request()->query(), ['category' => 'eligible'])) }}"
                                        class="font-bold text-emerald-600 hover:text-emerald-800 hover:underline">
                                        {{ number_format($grandEligible) }}
                                    </a>

                                </td>


                                {{-- NOT ELIGIBLE --}}
                                <td class="border border-slate-300 px-3 py-3 text-center">

                                    <a href="{{ route(
                                        'physical-verification.report.details',
                                        array_merge(request()->query(), ['category' => 'not_eligible']),
                                    ) }}"
                                        class="font-bold text-red-600 hover:text-red-800 hover:underline">
                                        {{ number_format($grandNotEligible) }}
                                    </a>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>


    {{-- DISTRICT -> CITY DEPENDENCY --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const district = document.getElementById('district_id');
            const city = document.getElementById('city_id');

            if (!district || !city) {
                return;
            }

            const selectedCity =
                @json((string) request('city_id'));

            function filterCities() {

                const districtId = district.value;

                let selectedStillValid = false;

                Array.from(city.options).forEach(function(option) {

                    if (option.value === '') {
                        option.hidden = false;
                        return;
                    }

                    const optionDistrict =
                        option.dataset.district || '';

                    const visible =
                        districtId === '' ||
                        optionDistrict === districtId;

                    option.hidden = !visible;

                    if (
                        visible &&
                        option.value === selectedCity
                    ) {
                        selectedStillValid = true;
                    }

                });

                if (
                    districtId !== '' &&
                    city.value !== '' &&
                    !selectedStillValid
                ) {
                    city.value = '';
                }
            }

            filterCities();

            district.addEventListener(
                'change',
                function() {
                    filterCities();
                }
            );

        });
    </script>

@endsection
