@extends('layouts.mmgayAdmin')

@section('title', 'Super Admin Dashboard')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

        {{-- ================= Master Data ================= --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-5">

            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Dashboard Filters</h3>
                    <p class="text-[11px] text-slate-500">Filter data by Phase, District, Block and Village</p>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Reset Button -->
                    <a href="{{ route('admin.dashboard') }}" class="text-xs text-red-600 hover:text-red-700 font-medium">
                        ⟲ Reset
                    </a>

                    <!-- Export Excel Button -->
                    {{-- <a href="{{ route('admin.export.excel', request()->all()) }}"
                        class="text-xs text-green-700 hover:text-green-800 font-semibold border border-green-600 px-3 py-1 rounded-md">
                        Excel
                    </a>

                    <!-- Export PDF Button -->
                    <a href="{{ route('admin.export.pdf', request()->all()) }}"
                        class="text-xs text-blue-700 hover:text-blue-800 font-semibold border border-blue-600 px-3 py-1 rounded-md">
                        PDF
                    </a> --}}
                </div>
            </div>

            <form method="GET" id="dashboardFilter">

                <div class="grid grid-cols-12 gap-3 items-end">

                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                            Phase
                        </label>

                        <select name="phase" id="phase"
                            class="w-full h-9 text-xs rounded-lg border-gray-300 focus:ring-1 focus:ring-blue-500">

                            <option value="">All Phase</option>

                            <option value="1" {{ request('phase') == 1 ? 'selected' : '' }}>
                                Phase 1
                            </option>

                            <option value="2" {{ request('phase') == 2 ? 'selected' : '' }}>
                                Phase 2
                            </option>

                            <option value="3" {{ request('phase') == 3 ? 'selected' : '' }}>
                                Phase 3
                            </option>

                            <option value="4" {{ request('phase') == 4 ? 'selected' : '' }}>
                                Phase 4
                            </option>

                        </select>
                    </div>

                    <div class="col-span-3">
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                            District
                        </label>

                        <select name="district_id" id="district" class="w-full h-9 text-xs rounded-lg border-gray-300">
                            <option value="">All District</option>

                            @foreach ($districts as $district)
                                <option value="{{ $district->DistrictId }}"
                                    {{ request('district_id') == $district->DistrictId ? 'selected' : '' }}>

                                    {{ $district->DistrictName }}

                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-3">
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                            Block
                        </label>

                        <select name="block_id" id="block" class="w-full h-9 text-xs rounded-lg border-gray-300">

                            <option value="">All Block</option>

                            @foreach ($blocks as $block)
                                <option value="{{ $block->BlockId }}"
                                    {{ request('block_id') == $block->BlockId ? 'selected' : '' }}>

                                    {{ $block->BlockName }}

                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-span-3">
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                            Village
                        </label>

                        <select name="village_id" id="village" class="w-full h-9 text-xs rounded-lg border-gray-300">

                            <option value="">All Village</option>

                            @foreach ($villages as $village)
                                <option value="{{ $village->VillageId }}"
                                    {{ request('village_id') == $village->VillageId ? 'selected' : '' }}>

                                    {{ $village->VillageName }}

                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-span-1 flex gap-2">

                        <div class="col-span-2 flex gap-2">

                            <button type="submit"
                                class="h-9 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs">

                                Apply

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

        {{-- ================= Master Statistics ================= --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-6">

            <div class="flex items-center justify-between px-5 py-4 border-b">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Master Data
                    </h2>
                    <p class="text-xs text-gray-500">
                        Overall Project Statistics
                    </p>
                </div>

                <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                    Live
                </span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 p-5">

                {{-- District --}}
                <a href="{{ route('admin.district.report', request()->all()) }}"
                    class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-white border rounded-xl hover:shadow-md hover:-translate-y-1 transition">

                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">
                            Districts
                        </p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->TotalDistricts) }}
                        </h3>
                    </div>
                </a>

                {{-- Villages --}}
                <a href="{{ route('admin.village.report', ['phase' => request('phase'), 'district_id' => request('district_id'), 'block_id' => request('block_id'), 'village_id' => request('village_id')]) }}"
                    class="flex items-center p-4 bg-gradient-to-r from-green-50 to-white border rounded-xl hover:shadow-md hover:-translate-y-1 transition">

                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10l9-7 9 7M5 10v10h14V10M9 20v-6h6v6" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Villages
                        </p>

                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->TotalVillages) }}
                        </h3>
                    </div>

                </a>

                {{-- Registered Beneficiaries --}}
                <a href="{{ route('superadmin.applicants.index', ['phase' => request('phase'), 'district_id' => request('district_id'), 'block_id' => request('block_id'), 'village_id' => request('village_id')]) }}"
                    class="flex items-center p-4 bg-gradient-to-r from-indigo-50 to-white border rounded-xl hover:shadow-md hover:-translate-y-1 transition">

                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 10-8 0 4 4 0 008 0zm6 2a3 3 0 100-6 3 3 0 000 6z" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Applicants
                        </p>

                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->RegisteredBeneficiaries) }}
                        </h3>
                    </div>
                </a>

                {{-- Allotted Beneficiaries --}}
                <a href="{{ route('admin.allotment.report', [
                    'phase' => request('phase'),
                    'district_id' => request('district_id'),
                    'block_id' => request('block_id'),
                    'village_id' => request('village_id'),
                ]) }}"
                    class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-white border rounded-xl hover:shadow-md hover:-translate-y-1 transition">

                    <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 17v-5h8v5M3 10l9-7 9 7M5 10v10h14V10" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Allotted
                        </p>

                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->AllottedBeneficiaries) }}
                        </h3>
                    </div>

                </a>    

            </div>

        </div>

        <div class="w-full bg-white rounded-2xl shadow-lg border border-gray-200 mb-6">

            <div class="flex items-center justify-between px-5 py-4 border-b">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Allotment Status
                    </h2>
                    <p class="text-xs text-gray-500">
                        Status of Allotted Beneficiaries
                    </p>
                </div>

                <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                    {{ number_format($summary->GrossTotal) }} Records
                </span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 p-5">

                {{-- Gross Total --}}
                <div
                    class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-white border rounded-xl hover:shadow-md transition">

                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500">Total</p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->GrossTotal) }}
                        </h3>
                    </div>

                </div>

                {{-- Approved Paid --}}
                <div
                    class="flex items-center p-4 bg-gradient-to-r from-green-50 to-white border rounded-xl hover:shadow-md transition">

                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500">Approved & Paid</p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->ApprovedPaid) }}
                        </h3>
                    </div>

                </div>

                {{-- Approved Unpaid --}}
                <div
                    class="flex items-center p-4 bg-gradient-to-r from-yellow-50 to-white border rounded-xl hover:shadow-md transition">

                    <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="6" width="18" height="12" rx="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12h.01" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500">Approved & Unpaid</p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->ApprovedUnpaid) }}
                        </h3>
                    </div>

                </div>

                {{-- Pending --}}
                <div
                    class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-white border rounded-xl hover:shadow-md transition">

                    <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500">Yet to be Approved</p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->PendingApprovalPayment) }}
                        </h3>
                    </div>

                </div>

                {{-- Rejected --}}
                <div
                    class="flex items-center p-4 bg-gradient-to-r from-red-50 to-white border rounded-xl hover:shadow-md transition">

                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500">Rejected</p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->Rejected) }}
                        </h3>
                    </div>

                </div>

                {{-- Cancelled --}}
                <div
                    class="flex items-center p-4 bg-gradient-to-r from-gray-50 to-white border rounded-xl hover:shadow-md transition">

                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16l8-8" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500">Cancelled</p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ number_format($summary->AllotmentCancelled) }}
                        </h3>
                    </div>

                </div>

            </div>

        </div>

        <div class="w-full bg-white rounded-2xl shadow-lg border border-gray-200 mb-6">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Registration Statistics
                    </h2>
                    <p class="text-xs text-gray-500">
                        Registry Matching Report
                    </p>
                </div>

                <span class="text-xs bg-violet-100 text-violet-700 px-3 py-1 rounded-full font-medium">
                    Registry
                </span>
            </div>

            {{-- Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5">

                {{-- Total Registration --}}
                <a href="#" class="block">

                    <div
                        class="flex items-center p-4 bg-gradient-to-r from-violet-50 to-white border rounded-xl hover:shadow-md hover:-translate-y-1 transition cursor-pointer">

                        <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-violet-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 3h7l5 5v13H8a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                Total Registration
                            </p>

                            <h3 class="text-2xl font-bold text-gray-800">
                                {{ number_format($registration->TotalRegistration) }}
                            </h3>
                        </div>

                    </div>

                </a>

                {{-- Matched --}}
                <a href="#" class="block">

                    <div
                        class="flex items-center p-4 bg-gradient-to-r from-green-50 to-white border rounded-xl hover:shadow-md hover:-translate-y-1 transition cursor-pointer">

                        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12l3 3 5-6" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                Matched
                            </p>

                            <h3 class="text-2xl font-bold text-gray-800">
                                {{ number_format($registration->Matched) }}
                            </h3>
                        </div>

                    </div>

                </a>

                {{-- Unmatched --}}
                <a href="#" class="block">

                    <div
                        class="flex items-center p-4 bg-gradient-to-r from-red-50 to-white border rounded-xl hover:shadow-md hover:-translate-y-1 transition cursor-pointer">

                        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                Unmatched
                            </p>

                            <h3 class="text-2xl font-bold text-gray-800">
                                {{ number_format($registration->UnMatched) }}
                            </h3>
                        </div>

                    </div>

                </a>

            </div>

        </div>



    </main>
    <div id="dashboardLoader" class="hidden fixed inset-0 bg-white/80 z-50">

        <div class="flex items-center justify-center h-full">

            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600">
            </div>

            <span class="ml-4 text-lg font-semibold">

                Loading Dashboard...

            </span>

        </div>

    </div>
@endsection
