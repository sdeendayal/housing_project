@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Dashboard')
@section('content')

    @if (session('success'))
        <div id="successToast" class="success-toast">
            <span class="material-symbols-outlined me-2">
                check_circle
            </span>

            {{ session('success') }}
        </div>
    @endif
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">
            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Filter Header --}}
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50">
                            <span class="material-symbols-outlined text-violet-600">
                                filter_alt
                            </span>
                        </div>

                        <div>
                            <h2 class="text-sm font-bold text-slate-800">
                                Dashboard Filters
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Filter dashboard data by location
                            </p>
                        </div>
                    </div>

                    @if ($districtId || $cityId || $sectorId)
                        <div
                            class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600">
                            <span class="material-symbols-outlined text-[16px]">
                                check_circle
                            </span>
                            Filter applied
                        </div>
                    @endif
                </div>

                {{-- Filter Form --}}
                <form method="GET" action="{{ url()->current() }}" id="dashboardFilterForm" class="p-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

                        {{-- District --}}
                        <div>
                            <label for="district_id"
                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                                District
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                                    location_city
                                </span>

                                <select name="district_id" id="district_id"
                                    class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-9 text-sm font-medium text-slate-700 outline-none transition
                               hover:border-violet-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-100">
                                    <option value="">All Districts</option>

                                    @foreach ($districts as $district)
                                        <option value="{{ $district->DistrictId }}" @selected((string) $districtId === (string) $district->DistrictId)>
                                            {{ $district->DistrictName }}
                                        </option>
                                    @endforeach
                                </select>

                                <span
                                    class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                    expand_more
                                </span>
                            </div>
                        </div>

                        {{-- City --}}
                        <div>
                            <label for="city_id"
                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                                City
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                                    apartment
                                </span>

                                <select name="city_id" id="city_id" @disabled(!$districtId)
                                    class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-9 text-sm font-medium text-slate-700 outline-none transition
                               hover:border-violet-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-100
                               disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-100 disabled:text-slate-400">
                                    <option value="">
                                        {{ $districtId ? 'All Cities' : 'Select district first' }}
                                    </option>

                                    @foreach ($cities as $city)
                                        <option value="{{ $city->CityId }}" @selected((string) $cityId === (string) $city->CityId)>
                                            {{ $city->CityName }}
                                        </option>
                                    @endforeach
                                </select>

                                <span
                                    class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                    expand_more
                                </span>
                            </div>
                        </div>

                        {{-- Sector --}}
                        <div>
                            <label for="sector_id"
                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Sector
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                                    grid_view
                                </span>

                                <select name="sector_id" id="sector_id" @disabled(!$cityId)
                                    class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-9 text-sm font-medium text-slate-700 outline-none transition
                               hover:border-violet-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-100
                               disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-100 disabled:text-slate-400">
                                    <option value="">
                                        {{ $cityId ? 'All Sectors' : 'Select city first' }}
                                    </option>

                                    @foreach ($sectors as $sector)
                                        <option value="{{ $sector->SectorId }}" @selected((string) $sectorId === (string) $sector->SectorId)>
                                            {{ $sector->SectorName }}
                                        </option>
                                    @endforeach
                                </select>

                                <span
                                    class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                    expand_more
                                </span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 text-sm font-semibold text-white shadow-sm transition
                           hover:bg-violet-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-violet-200">
                                <span class="material-symbols-outlined text-[19px]">
                                    filter_alt
                                </span>
                                Apply
                            </button>

                            <a href="{{ url()->current() }}" title="Reset filters"
                                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition
                           hover:border-red-200 hover:bg-red-50 hover:text-red-500 focus:outline-none focus:ring-4 focus:ring-red-100">
                                <span class="material-symbols-outlined text-[20px]">
                                    restart_alt
                                </span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Section heading --}}
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 bg-gradient-to-r from-white via-slate-50/70 to-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100">
                            <span class="material-symbols-outlined text-[21px]">
                                account_tree
                            </span>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-800">
                                Property Process
                            </h3>

                            <p class="mt-0.5 text-[11px] text-slate-500">
                                Quick access to each processing stage
                            </p>
                        </div>
                    </div>

                    <span
                        class="inline-flex w-fit items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-[10px] font-semibold text-indigo-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                        6 Process Stages
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 lg:grid-cols-6 lg:p-5">

                    {{-- Registration --}}
                    <a href="{{ url('mmsay-department-property-registration') }}"
                        class="group relative overflow-hidden rounded-xl border border-emerald-100 bg-white p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.05)] transition duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-emerald-500"></span>

                        <div class="flex items-start justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-500 group-hover:text-white">
                                <span class="material-symbols-outlined text-[21px]">app_registration</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-[16px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-emerald-500">
                                arrow_outward
                            </span>
                        </div>

                        <h4 class="mt-3 text-xs font-bold text-slate-800">Registration</h4>
                        <p class="mt-1 truncate text-[10px] text-slate-400">Property entry</p>
                    </a>

                    {{-- Draw --}}
                    <a href="{{ url('/mmsay-department-draw') }}"
                        class="group relative overflow-hidden rounded-xl border border-cyan-100 bg-white p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.05)] transition duration-200 hover:-translate-y-0.5 hover:border-cyan-200 hover:shadow-md">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-cyan-500"></span>

                        <div class="flex items-start justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600 transition group-hover:bg-cyan-500 group-hover:text-white">
                                <span class="material-symbols-outlined text-[21px]">casino</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-[16px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-cyan-500">
                                arrow_outward
                            </span>
                        </div>

                        <h4 class="mt-3 text-xs font-bold text-slate-800">Draw</h4>
                        <p class="mt-1 truncate text-[10px] text-slate-400">Property selection</p>
                    </a>

                    {{-- Property Allotment --}}
                    <a href="{{ url('mmsay-department-allotted-properties') }}"
                        class="group relative overflow-hidden rounded-xl border border-orange-100 bg-white p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.05)] transition duration-200 hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-md">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-orange-500"></span>

                        <div class="flex items-start justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-600 transition group-hover:bg-orange-500 group-hover:text-white">
                                <span class="material-symbols-outlined text-[21px]">home_work</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-[16px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-orange-500">
                                arrow_outward
                            </span>
                        </div>

                        <h4 class="mt-3 text-xs font-bold text-slate-800">Allotment</h4>
                        <p class="mt-1 truncate text-[10px] text-slate-400">Plot / flat allotted</p>
                    </a>

                    {{-- Provisional Letter --}}
                    <a href="#"
                        class="group relative overflow-hidden rounded-xl border border-blue-100 bg-white p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.05)] transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-blue-500"></span>

                        <div class="flex items-start justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-500 group-hover:text-white">
                                <span class="material-symbols-outlined text-[21px]">description</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-[16px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-blue-500">
                                arrow_outward
                            </span>
                        </div>

                        <h4 class="mt-3 text-xs font-bold text-slate-800">Provisional</h4>
                        <p class="mt-1 truncate text-[10px] text-slate-400">Issued after draw</p>
                    </a>

                    {{-- EMI Payments --}}
                    <a href="{{ url('/mmsay-department-emi-payments') }}"
                        class="group relative overflow-hidden rounded-xl border border-amber-100 bg-white p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.05)] transition duration-200 hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-amber-500"></span>

                        <div class="flex items-start justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition group-hover:bg-amber-500 group-hover:text-white">
                                <span class="material-symbols-outlined text-[21px]">payments</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-[16px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-amber-500">
                                arrow_outward
                            </span>
                        </div>

                        <h4 class="mt-3 text-xs font-bold text-slate-800">EMI Payments</h4>
                        <p class="mt-1 truncate text-[10px] text-slate-400">Monthly installments</p>
                    </a>

                    {{-- Physical Letter --}}
                    <a href="{{ url('mmsay-department-physical-letter') }}"
                        class="group relative overflow-hidden rounded-xl border border-pink-100 bg-white p-3.5 shadow-[0_1px_3px_rgba(15,23,42,0.05)] transition duration-200 hover:-translate-y-0.5 hover:border-pink-200 hover:shadow-md">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-pink-500"></span>

                        <div class="flex items-start justify-between">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-50 text-pink-600 transition group-hover:bg-pink-500 group-hover:text-white">
                                <span class="material-symbols-outlined text-[21px]">approval</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-[16px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-pink-500">
                                arrow_outward
                            </span>
                        </div>

                        <h4 class="mt-3 text-xs font-bold text-slate-800">Physical Letter</h4>
                        <p class="mt-1 truncate text-[10px] text-slate-400">After payment eligibility</p>
                    </a>

                </div>
            </section>

            <!-- Bento Grid - Summary Metrics -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12">

                {{-- Registration --}}
                <a href="{{ url('mmsay-department-property-registration') .
                    '?' .
                    http_build_query(
                        array_filter([
                            'district_id' => $districtId,
                            'city_id' => $cityId,
                            'sector_id' => $sectorId,
                        ]),
                    ) }}"
                    class="group rounded-xl border border-indigo-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md lg:col-span-2">

                    <div class="flex items-start justify-between">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 transition group-hover:bg-indigo-100">
                            <span class="material-symbols-outlined text-[20px]">
                                person_add
                            </span>
                        </div>

                        <span
                            class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-400">
                            arrow_outward
                        </span>
                    </div>

                    <p class="mt-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Registration
                    </p>

                    <h3 class="mt-1 text-2xl font-bold leading-none text-slate-800">
                        {{ number_format($totalApplications ?? 0) }}
                    </h3>

                    <p class="mt-2 text-[11px] text-slate-400">
                        Total properties
                    </p>
                </a>

                {{-- Draw --}}
                <a href="{{ url('mmsay-department-draw') }}"
                    class="group rounded-xl border border-emerald-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md lg:col-span-2">

                    <div class="flex items-start justify-between">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 transition group-hover:bg-emerald-100">
                            <span class="material-symbols-outlined text-[20px]">
                                casino
                            </span>
                        </div>

                        <span
                            class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-emerald-400">
                            arrow_outward
                        </span>
                    </div>

                    <p class="mt-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Draw
                    </p>

                    <h3 class="mt-1 text-2xl font-bold leading-none text-emerald-600">
                        {{ number_format($allottedUnits ?? 0) }}
                    </h3>

                    <p class="mt-2 text-[11px] text-slate-400">
                        Draw process
                    </p>
                </a>

                {{-- Allotted --}}
                <a href="{{ url('mmsay-department-allotted-properties') }}"
                    class="group rounded-xl border border-orange-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-md lg:col-span-2">

                    <div class="flex items-start justify-between">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-50 text-orange-500 transition group-hover:bg-orange-100">
                            <span class="material-symbols-outlined text-[20px]">
                                apartment
                            </span>
                        </div>

                        <span
                            class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-orange-400">
                            arrow_outward
                        </span>
                    </div>

                    <p class="mt-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Allotted
                    </p>

                    <h3 class="mt-1 text-2xl font-bold leading-none text-orange-600">
                        {{ number_format($allottedUnits ?? 0) }}
                    </h3>

                    <p class="mt-2 text-[11px] text-slate-400">
                        Plot / flat assigned
                    </p>
                </a>

                {{-- EMI --}}
                <div class="rounded-xl border border-amber-100 bg-white p-4 shadow-sm lg:col-span-2">

                    <div class="flex items-center justify-between">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-500">
                            <span class="material-symbols-outlined text-[20px]">
                                payments
                            </span>
                        </div>

                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            EMI Payment Status
                        </p>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2">

                        {{-- Full Payment --}}
                        <a href="{{ url('full-paid-properties') .
                            '?' .
                            http_build_query(
                                array_filter([
                                    'district_id' => $districtId,
                                    'city_id' => $cityId,
                                    'sector_id' => $sectorId,
                                ]),
                            ) }}"
                            class="rounded-lg bg-emerald-50 px-2 py-2.5 text-center transition hover:bg-emerald-100">
                            <p class="text-[9px] font-medium uppercase text-slate-500">
                                Full Payment
                            </p>

                            <p class="mt-1 text-lg font-bold leading-none text-emerald-600">
                                {{ number_format($paymentStats->total_paid_properties ?? 0) }}
                            </p>
                        </a>

                        {{-- Partial Payment --}}
                        <a href="{{ url('partial-paid-properties') .
                            '?' .
                            http_build_query(
                                array_filter([
                                    'district_id' => $districtId,
                                    'city_id' => $cityId,
                                    'sector_id' => $sectorId,
                                ]),
                            ) }}"
                            class="rounded-lg bg-amber-50 px-2 py-2.5 text-center transition hover:bg-amber-100">
                            <p class="text-[9px] font-medium uppercase text-slate-500">
                                Partial Payment
                            </p>

                            <p class="mt-1 text-lg font-bold leading-none text-amber-600">
                                {{ number_format($paymentStats->pending_properties ?? 0) }}
                            </p>
                        </a>
                    </div>
                </div>

                {{-- Physical Possession --}}
                <div
                    class="overflow-hidden rounded-xl border border-violet-100 bg-white shadow-sm sm:col-span-2 lg:col-span-4">

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                                <span class="material-symbols-outlined text-[20px]">
                                    real_estate_agent
                                </span>
                            </div>

                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-600">
                                    Physical Possession
                                </p>

                                <p class="mt-0.5 text-[10px] text-slate-400">
                                    Payment eligibility
                                </p>
                            </div>
                        </div>

                        <span class="rounded-full bg-violet-50 px-2 py-1 text-[9px] font-semibold text-violet-600">
                            ₹60,000 minimum
                        </span>
                    </div>

                    {{-- Eligible and Not Eligible --}}
                    <div class="grid grid-cols-2 divide-x divide-slate-100">

                        <a href="{{ url('mmsay-department-physical-letter') .
                            '?' .
                            http_build_query(
                                array_filter([
                                    'eligibility' => 'eligible',
                                    'district_id' => $districtId,
                                    'city_id' => $cityId,
                                    'sector_id' => $sectorId,
                                ]),
                            ) }}"
                            class="group px-4 py-3 transition hover:bg-emerald-50/60">

                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[17px] text-emerald-600">
                                    task_alt
                                </span>

                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                    Eligible
                                </p>
                            </div>

                            <div class="mt-2 flex items-end justify-between gap-2">
                                <div>
                                    <h3 class="text-2xl font-bold leading-none text-emerald-600">
                                        {{ number_format($eligiblePhysicalPossession ?? 0) }}
                                    </h3>

                                    <p class="mt-1.5 text-[10px] leading-tight text-slate-400">
                                        ₹60,000 or more
                                    </p>
                                </div>

                                <span
                                    class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-emerald-500">
                                    arrow_forward
                                </span>
                            </div>
                        </a>

                        <a href="{{ url('mmsay-department-physical-letter') .
                            '?' .
                            http_build_query(
                                array_filter([
                                    'eligibility' => 'not_eligible',
                                    'district_id' => $districtId,
                                    'city_id' => $cityId,
                                    'sector_id' => $sectorId,
                                ]),
                            ) }}"
                            class="group px-4 py-3 transition hover:bg-rose-50/60">

                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[17px] text-rose-500">
                                    pending_actions
                                </span>

                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                    Not Eligible
                                </p>
                            </div>

                            <div class="mt-2 flex items-end justify-between gap-2">
                                <div>
                                    <h3 class="text-2xl font-bold leading-none text-rose-600">
                                        {{ number_format($notEligiblePhysicalPossession ?? 0) }}
                                    </h3>

                                    <p class="mt-1.5 text-[10px] leading-tight text-slate-400">
                                        Below ₹60,000
                                    </p>
                                </div>

                                <span
                                    class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-rose-500">
                                    arrow_forward
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
