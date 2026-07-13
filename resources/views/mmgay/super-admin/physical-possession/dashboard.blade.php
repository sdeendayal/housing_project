@extends('layouts.mmgayAdmin')

@section('title', 'Physical Possession Dashboard - Super Admin ')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

        {{-- 🏠 NEW: PHYSICAL POSSESSION SECTION --}}
        <div class="w-full mb-8">
            <h1 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="p-1.5 bg-blue-600 text-white rounded-lg shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </span>
                Physical Possession Portal Summary
            </h1>
            <hr><br>

            {{-- 1. Possession KPI Mini Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Eligible</p>
                        <h3 class="text-2xl font-bold text-gray-700 mt-1">{{ $totalEligible }}</h3>
                    </div>
                    <div class="p-2 bg-slate-100 rounded-xl text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-3.13a4 4 0 10-8 0 4 4 0 008 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Awaiting Citizen</p>
                        <h3 class="text-2xl font-bold text-orange-600 mt-1">{{ $awaitingCitizen }}</h3>
                    </div>
                    <div class="p-2 bg-orange-50 rounded-xl text-orange-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Field Visit Pending</p>
                        <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ $fieldVisitPending }}</h3>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-xl text-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">E-Possession</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $ePossessionPending }}</h3>
                    </div>
                    <div class="p-2 bg-amber-50 rounded-xl text-amber-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6M9 16h6M9 8h6M7 3h10l2 2v16H5V5l2-2z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Verified</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $verified }}</h3>
                    </div>
                    <div class="p-2 bg-emerald-50 rounded-xl text-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 2. Dual Column Layout (Recent Log & Quick Actions) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Log Column -->
                <div
                    class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-5 py-4 border-b flex items-center justify-between bg-slate-50">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Recent Activity Log
                            </h3>
                            <a href="#" class="text-xs font-semibold text-blue-600 hover:underline">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-slate-100 text-gray-600 uppercase tracking-wider border-b">
                                    <tr>
                                        <th class="p-3 font-semibold">Sr.No.</th>
                                        <th class="p-3 font-semibold">App Number</th>
                                        <th class="p-3 font-semibold">Applicant</th>
                                        <th class="p-3 font-semibold">Mobile</th>
                                        <th class="p-3 font-semibold">Status</th>
                                        <th class="p-3 font-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentApplications as $row)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">

                                            <td class="p-4 font-semibold text-slate-500">
                                                {{ $loop->iteration }}
                                            </td>

                                            <td class="p-4">
                                                <div class="font-bold text-blue-700">
                                                    {{ $row->application_number }}
                                                </div>
                                            </td>

                                            <td class="p-4">
                                                <div class="flex items-center gap-2">

                                                    <div
                                                        class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center">

                                                        <span class="material-symbols-outlined text-[18px] text-blue-600">
                                                            person
                                                        </span>

                                                    </div>

                                                    <div>

                                                        <p class="font-semibold text-slate-800">
                                                            {{ $row->applicant_name }}
                                                        </p>

                                                    </div>

                                                </div>
                                            </td>

                                            <td class="p-4">

                                                <div class="flex items-center gap-2">

                                                    <span class="material-symbols-outlined text-[18px] text-slate-500">
                                                        call
                                                    </span>

                                                    <span class="font-medium text-slate-700">
                                                        {{ $row->mobile }}
                                                    </span>

                                                </div>

                                            </td>

                                            <td>
                                                @php
                                                    $badge = [
                                                        'Visit Scheduled' => 'bg-orange-100 text-orange-700',
                                                        'Slot Selected' => 'bg-blue-100 text-blue-700',
                                                        'Site Verified' => 'bg-green-100 text-green-700',
                                                        'Verified' => 'bg-emerald-100 text-emerald-700',
                                                    ];
                                                @endphp

                                                <span
                                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $badge[$row->physical_possession_status] ?? 'bg-gray-100 text-gray-700' }}">
                                                    <span class="material-symbols-outlined text-[16px]">
                                                        info
                                                    </span>
                                                    {{ $row->physical_possession_status }}
                                                </span>
                                            </td>

                                            <td class="p-4">

                                                <a href="{{ route('superadmin.possession.application.view', $row->secure_id) }}"
                                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">

                                                    <span class="material-symbols-outlined text-[18px]">
                                                        visibility
                                                    </span>

                                                    Detail

                                                </a>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="6" class="py-12 text-center text-slate-400">

                                                <span class="material-symbols-outlined text-5xl mb-3 block">
                                                    inbox
                                                </span>

                                                <p class="text-base font-semibold">
                                                    No Recent Applications Found
                                                </p>

                                            </td>

                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        {{-- 📌 DISTRICT WISE SUMMARY (COMMENTED OUT PREVIOUSLY) --}}
        {{-- ... Aapka pura tables code ... --}}

    </main>

@endsection
