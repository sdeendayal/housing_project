@extends('layouts.mmsayDepartmentAuth')

@section('title', 'Physical Verification Report')

@section('content')

    <main class="ml-52 min-h-screen bg-slate-50 px-5 pb-6 pt-20">
        <div class="mx-auto max-w-7xl space-y-4">

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center
                    lg:justify-between">

                <div>
                    <h1 class="text-[26px] font-bold tracking-tight text-slate-800">
                        Physical Verification Report
                    </h1>

                    <p class="mt-1 text-[15px] text-slate-500">
                        Mukhyamantri Shehri Awas Yojana (MMSAY) - Phase-1
                    </p>
                </div>


                {{-- ACTIONS --}}
                <div class="flex items-center gap-2">

                    <a href="{{ route('physical-verification.report.print', request()->query()) }}" target="_blank"
                        class="inline-flex h-11 items-center gap-2 rounded-xl
                          bg-red-600 px-5 text-sm font-semibold text-white
                          shadow-sm transition hover:bg-red-700">

                        <span class="material-symbols-outlined text-[18px]">
                            print
                        </span>

                        Print

                    </a>


                    <a href="{{ route('physical-verification.report.csv', request()->query()) }}"
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

        </div>
        <br>



        {{-- ============ FILTERS============================== --}}

        <div class="mb-5 w-full rounded-2xl border border-slate-200 bg-white
            px-6 py-5 shadow-sm">

            <form method="GET" action="{{ route('physical-verification.report') }}"
                class="grid grid-cols-1 items-end gap-4
                 lg:grid-cols-12">


                {{-- =====================================================
             PHASE
        ====================================================== --}}
                <div class="lg:col-span-3">

                    <label for="phase" class="mb-2 block text-sm font-semibold text-slate-700">
                        PHASE
                    </label>

                    <div class="relative">

                        {{-- LEFT ICON --}}
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

                {{-- =====================================================
             DISTRICT
        ====================================================== --}}
                <div class="lg:col-span-3">

                    <label for="district_id" class="mb-2 block text-sm font-semibold text-slate-700">
                        DISTRICT
                    </label>

                    <div class="relative">

                        {{-- LEFT ICON --}}
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
                                <option value="{{ $district->DistrictId }}" @selected(request('district_id') == $district->DistrictId)>

                                    {{ $district->DistrictName }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- =====================================================
             TOWN / ULB
        ====================================================== --}}
                <div class="lg:col-span-3">

                    <label for="city_id" class="mb-2 block text-sm font-semibold text-slate-700">
                        TOWN / ULB
                    </label>

                    <div class="relative">

                        {{-- LEFT ICON --}}
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


                {{-- =====================================================
             ACTIONS
        ====================================================== --}}
                <div class="flex items-center gap-2 lg:col-span-3">

                    {{-- APPLY --}}
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

                        Apply

                    </button>


                    {{-- RESET --}}
                    <a href="{{ route('physical-verification.report') }}" title="Reset Filters"
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
        <div class="w-full overflow-hidden rounded-2xl border border-slate-200
                bg-white shadow-sm">


            {{-- REPORT CARD HEADER --}}
            <div
                class="flex flex-col gap-4 border-b border-slate-200
                    px-6 py-5 sm:flex-row sm:items-center
                    sm:justify-between">

                <div>

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center
                                rounded-xl bg-indigo-50 text-indigo-600">

                            <span class="material-symbols-outlined text-[21px]">
                                fact_check
                            </span>

                        </div>


                        <div>

                            <h2 class="text-lg font-bold text-slate-800">
                                Physical Verification Report
                            </h2>

                            <p class="mt-0.5 text-sm text-slate-500">
                                Category-wise plots allotted
                            </p>

                        </div>

                    </div>

                </div>


                {{-- TOTAL --}}
                <div class="rounded-xl bg-slate-50 px-5 py-3 text-right">

                    <p class="text-xs font-medium uppercase tracking-wide
                          text-slate-500">
                        Total Applicants
                    </p>

                    <p class="mt-0.5 text-2xl font-bold text-slate-800">
                        {{ number_format($grandTotal->total) }}
                    </p>

                </div>

            </div>


            {{-- =====================================================
             TABLE
        ====================================================== --}}
            <div class="w-full overflow-x-auto">

                <table class="w-full min-w-[1000px] border-collapse">


                    {{-- =================================================
                     TABLE HEADER
                ================================================== --}}
                    <thead>

                        {{-- TOP HEADER --}}
                        <tr class="bg-slate-50">

                            {{-- S.NO --}}
                            <th rowspan="2"
                                class="w-[75px] border-b border-r border-slate-200
                                   px-4 py-4 text-center align-middle
                                   text-[12px] font-bold uppercase
                                   tracking-wide text-slate-600">

                                S.<br>N.

                            </th>


                            {{-- TOWN --}}
                            <th rowspan="2"
                                class="w-[250px] border-b border-r border-slate-200
                                   px-5 py-4 text-left align-middle
                                   text-[12px] font-bold uppercase
                                   tracking-wide text-slate-600">

                                Town / ULB

                            </th>


                            {{-- CATEGORY --}}
                            <th colspan="4"
                                class="border-b border-slate-200 px-4 py-3
                                   text-center text-[13px] font-bold
                                   text-slate-700">

                                Category-wise plots allotted

                            </th>


                            {{-- TOTAL --}}
                            <th rowspan="2"
                                class="w-[160px] border-b border-l border-slate-200
                                   px-4 py-4 text-center align-middle
                                   text-[12px] font-bold uppercase
                                   tracking-wide text-slate-600">

                                <span class="leading-tight">
                                    Total<br>
                                    plots<br>
                                    allotted
                                </span>

                            </th>

                        </tr>


                        {{-- CATEGORY HEADER --}}
                        <tr class="bg-slate-50">

                            {{-- GHUMANTU --}}
                            <th
                                class="w-[140px] border-b border-r border-slate-200
                                   px-3 py-3 text-center">

                                <span
                                    class="block text-sm font-semibold
                                         text-slate-700">
                                    Ghumantu
                                </span>

                                <span
                                    class="block text-sm font-semibold
                                         text-slate-700">
                                    Jati
                                </span>

                                <span
                                    class="mt-0.5 block text-xs font-normal
                                         text-slate-500">
                                    (1)
                                </span>

                            </th>


                            {{-- WIDOWS --}}
                            <th
                                class="w-[120px] border-b border-r border-slate-200
                                   px-3 py-3 text-center">

                                <span
                                    class="block text-sm font-semibold
                                         text-slate-700">
                                    Widows
                                </span>

                                <span
                                    class="mt-0.5 block text-xs font-normal
                                         text-slate-500">
                                    (2)
                                </span>

                            </th>


                            {{-- SC --}}
                            <th
                                class="w-[160px] border-b border-r border-slate-200
                                   px-3 py-3 text-center">

                                <span
                                    class="block text-sm font-semibold
                                         text-slate-700">
                                    Scheduled
                                </span>

                                <span
                                    class="block text-sm font-semibold
                                         text-slate-700">
                                    Caste
                                </span>

                                <span
                                    class="mt-0.5 block text-xs font-normal
                                         text-slate-500">
                                    (3)
                                </span>

                            </th>


                            {{-- OTHERS --}}
                            <th
                                class="w-[120px] border-b border-slate-200
                                   px-3 py-3 text-center">

                                <span
                                    class="block text-sm font-semibold
                                         text-slate-700">
                                    Others
                                </span>

                                <span
                                    class="mt-0.5 block text-xs font-normal
                                         text-slate-500">
                                    (4)
                                </span>

                            </th>

                        </tr>

                    </thead>


                    {{-- =================================================
                     TABLE BODY
                ================================================== --}}
                    <tbody>

                        @forelse($report as $index => $row)
                            <tr
                                class="group border-b border-slate-100
                                   bg-white transition hover:bg-indigo-50/40">


                                {{-- S.NO --}}
                                <td
                                    class="border-r border-slate-100 px-4 py-3
                                       text-center text-sm text-slate-600">

                                    {{ $index + 1 }}

                                </td>


                                {{-- TOWN --}}
                                <td class="border-r border-slate-100 px-5 py-3">

                                    <span
                                        class="text-sm font-semibold
                                             text-slate-800">
                                        {{ $row->town_ulb }}
                                    </span>

                                </td>


                                {{-- GHUMANTU --}}
                                <td class="px-4 py-3 text-center text-sm">
                                    @if ($row->ghumantu_jati > 0)
                                        <a href="{{ route('physical-verification.report.details', [
                                            'phase' => request('phase', '1'),
                                            'district_id' => request('district_id'),
                                            'city_id' => $row->CityId,
                                            'category' => 'ghumantu',
                                        ]) }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->ghumantu_jati) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>


                                {{-- WIDOWS --}}
                                <td class="px-4 py-3 text-center text-sm">
                                    @if ($row->widows > 0)
                                        <a href="{{ route('physical-verification.report.details', [
                                            'phase' => request('phase', '1'),
                                            'district_id' => request('district_id'),
                                            'city_id' => $row->CityId,
                                            'category' => 'widow',
                                        ]) }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->widows) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>


                                {{-- SC --}}
                                <td class="px-4 py-3 text-center text-sm">
                                    @if ($row->scheduled_caste > 0)
                                        <a href="{{ route('physical-verification.report.details', [
                                            'phase' => request('phase', '1'),
                                            'district_id' => request('district_id'),
                                            'city_id' => $row->CityId,
                                            'category' => 'scheduled_caste',
                                        ]) }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->scheduled_caste) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>


                                {{-- OTHERS --}}
                                <td class="px-4 py-3 text-center text-sm">
                                    @if ($row->others > 0)
                                        <a href="{{ route('physical-verification.report.details', [
                                            'phase' => request('phase', '1'),
                                            'district_id' => request('district_id'),
                                            'city_id' => $row->CityId,
                                            'category' => 'others',
                                        ]) }}"
                                            class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ number_format($row->others) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>


                                {{-- TOTAL --}}
                                <td
                                    class="border-l border-slate-100 px-4 py-3
                                       text-center text-sm font-bold
                                       tabular-nums text-slate-900">

                                    {{ number_format($row->total) }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-5 py-10 text-center">

                                    <div
                                        class="flex flex-col items-center
                                            justify-center">

                                        <span
                                            class="material-symbols-outlined
                                                 text-4xl text-slate-300">
                                            folder_open
                                        </span>

                                        <p
                                            class="mt-2 text-sm font-medium
                                              text-slate-500">
                                            No records found
                                        </p>

                                    </div>

                                </td>

                            </tr>
                        @endforelse


                        {{-- =================================================
                         GRAND TOTAL
                    ================================================== --}}
                        <tr class="bg-slate-50">
                            <td></td>

                            <td
                                class="border-t border-slate-300 px-5 py-4
                                    text-sm font-bold uppercase
                                   tracking-wide text-slate-700">

                                Total

                            </td>


                            {{-- GHUMANTU --}}
                            <td
                                class="border-t border-slate-300 px-4 py-4
                                   text-center text-sm font-bold
                                   tabular-nums text-slate-900">

                                {{ number_format($grandTotal->ghumantu_jati) }}

                            </td>


                            {{-- WIDOWS --}}
                            <td
                                class="border-t border-slate-300 px-4 py-4
                                   text-center text-sm font-bold
                                   tabular-nums text-slate-900">

                                {{ number_format($grandTotal->widows) }}

                            </td>


                            {{-- SC --}}
                            <td
                                class="border-t border-slate-300 px-4 py-4
                                   text-center text-sm font-bold
                                   tabular-nums text-slate-900">

                                {{ number_format($grandTotal->scheduled_caste) }}

                            </td>


                            {{-- OTHERS --}}
                            <td
                                class="border-t border-slate-300 px-4 py-4
                                   text-center text-sm font-bold
                                   tabular-nums text-slate-900">

                                {{ number_format($grandTotal->others) }}

                            </td>


                            {{-- TOTAL --}}
                            <td
                                class="border-l border-t border-slate-300
                                   px-4 py-4 text-center text-base
                                   font-extrabold tabular-nums text-slate-900">

                                {{ number_format($grandTotal->total) }}

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

@endsection
