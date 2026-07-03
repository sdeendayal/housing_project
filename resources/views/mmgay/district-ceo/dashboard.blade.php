@extends('layouts.mmgayCEOAuth')
@section('title', 'MMGAY District CEO Dashboard')

@section('content')

    <main class="ml-[260px] mt-16 min-h-screen bg-slate-100 p-6">

        <!-- ===================== DASHBOARD HEADER ===================== -->

        <!-- ===================== DASHBOARD HEADER ===================== -->

        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-800 via-indigo-700 to-violet-700 shadow-xl">

            <!-- Background Circle -->
            <div class="absolute -right-10 -top-10 w-72 h-72 bg-white/10 rounded-full blur-2xl">
            </div>

            <div class="absolute right-20 bottom-0 opacity-10">
                <span class="material-symbols-outlined text-[170px]">
                    account_balance
                </span>
            </div>

            <div class="relative px-8 py-6">

                <div class="flex items-center justify-between">

                    <!-- Left -->

                    <div>

                        <h2 class="flex items-center gap-2 text-4xl font-bold text-white">

                            <span class="material-symbols-outlined text-[34px]">
                                location_on
                            </span>

                            {{ strtoupper(auth()->user()->district_name) }} District

                        </h2>

                        <p class="mt-2 text-lg text-blue-100">

                            District CEO • Mukhyamantri Gramin Awas Yojana

                        </p>

                    </div>

                    <!-- Right -->

                    <div
                        class="flex items-center gap-2 bg-white/15 backdrop-blur-md border border-white/20 rounded-xl px-5 py-3 text-white shadow">

                        <span class="material-symbols-outlined">
                            calendar_month
                        </span>

                        <span class="font-semibold text-lg">

                            {{ now()->format('d M Y') }}

                        </span>

                    </div>

                </div>

            </div>

        </div>



        <!-- ===================== PHASE TAB ===================== -->

        <div class="mt-6 flex gap-3">

            <button class="phase-tab px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold" data-phase="1">
                Phase 1
            </button>

            <button class="phase-tab px-6 py-2 rounded-xl bg-white border" data-phase="2">
                Phase 2
            </button>

            <button class="phase-tab px-6 py-2 rounded-xl bg-white border" data-phase="3">
                Phase 3
            </button>

        </div>



        <!-- ===================== KPI CARDS ===================== -->

        <!-- ===================== KPI CARDS ===================== -->

        <!-- Total Villages -->
        <!-- ===================== KPI CARDS ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-5 mt-6">

            <!-- Total Villages -->
            <div class="bg-white rounded-2xl shadow border p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Villages</p>
                        <h2 id="totalVillages" class="text-3xl font-bold text-blue-700">
                            {{ $totals['totalVillages'] }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-700 text-3xl">
                            location_city
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total Plots -->
            <div class="bg-white rounded-2xl shadow border p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Plots</p>
                        <h2 id="totalPlots" class="text-3xl font-bold text-green-700">
                            {{ $totals['totalPlots'] }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-700 text-3xl">
                            grid_view
                        </span>
                    </div>
                </div>
            </div>

            <!-- Applicants -->
            <div class="bg-white rounded-2xl shadow border p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Applicants</p>
                        <h2 id="totalApplicants" class="text-3xl font-bold text-indigo-700">
                            {{ $totals['totalApplicants'] }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-indigo-700 text-3xl">
                            groups
                        </span>
                    </div>
                </div>
            </div>

            <!-- Allotment -->
            <div class="bg-white rounded-2xl shadow border p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Allotment</p>
                        <h2 id="totalAllotment" class="text-3xl font-bold text-orange-700">
                            {{ $totals['totalAllotment'] }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-orange-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-orange-700 text-3xl">
                            home_work
                        </span>
                    </div>
                </div>
            </div>

            <!-- Paid -->
            <div class="bg-white rounded-2xl shadow border p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Paid</p>
                        <h2 id="totalPaid" class="text-3xl font-bold text-emerald-700">
                            {{ $totals['totalPaid'] }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-700 text-3xl">
                            payments
                        </span>
                    </div>
                </div>
            </div>



            <!-- Possession -->
            <div class="bg-white rounded-2xl shadow border p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Possession</p>
                        <h2 id="totalPossession" class="text-3xl font-bold text-purple-700">
                            {{-- {{ $totals['totalPossession'] }} --}}--
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-700 text-3xl">
                            key
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Village Wise Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mt-8">

            <div class="px-6 py-4 border-b flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">
                        Village Wise Summary
                    </h3>
                    <p class="text-sm text-gray-500" id="phaseTitle">
                        Phase {{ $phase }} Village Statistics
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="bg-blue-600 text-white sticky top-0">

                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Village</th>
                            <th class="px-4 py-3 text-center">Total Plots</th>
                            <th class="px-4 py-3 text-center">Applicants</th>
                            <th class="px-4 py-3 text-center">Paid</th>
                            <th class="px-4 py-3 text-center">SC</th>
                            <th class="px-4 py-3 text-center">Ghumantu</th>
                            <th class="px-4 py-3 text-center">Widow</th>
                            <th class="px-4 py-3 text-center">Others</th>
                            <th class="px-4 py-3 text-center">Allotted</th>
                        </tr>

                    </thead>

                    <tbody id="villageTableBody">

                        @php
                            $plots = 0;
                            $applicants = 0;
                            $paid = 0;
                            $allotted = 0;
                            $sc = 0;
                            $ghumantu = 0;
                            $widow = 0;
                            $others = 0;
                        @endphp

                        @foreach ($villageData as $row)
                            @php
                                $plots += $row->TotalPlots;
                                $applicants += $row->TotalApplicants;
                                $paid += $row->Paid;
                                $allotted += $row->TotalAllotment;
                                $sc += $row->SC;
                                $ghumantu += $row->Ghumantu;
                                $widow += $row->Widow;
                                $others += $row->Others;
                            @endphp

                            <tr class="border-b hover:bg-blue-50">

                                <td class="px-4 py-3">{{ $loop->iteration }}</td>

                                <td class="px-4 py-3 font-medium">{{ $row->VillageName }}</td>

                                <td class="px-4 py-3 text-center">{{ $row->TotalPlots }}</td>

                                <td class="px-4 py-3 text-center">{{ $row->TotalApplicants }}</td>

                                <td class="px-4 py-3 text-center text-green-600 font-semibold">
                                    {{ $row->Paid }}
                                </td>

                                <td class="px-4 py-3 text-center">{{ $row->SC }}</td>

                                <td class="px-4 py-3 text-center">{{ $row->Ghumantu }}</td>

                                <td class="px-4 py-3 text-center">{{ $row->Widow }}</td>

                                <td class="px-4 py-3 text-center">{{ $row->Others }}</td>

                                <td class="px-4 py-3 text-center text-blue-600 font-semibold">
                                    {{ $row->TotalAllotment }}
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                    <tfoot id="grandTotalFooter" class="bg-slate-100 font-bold">

                        <tr>

                            <td colspan="2" class="px-4 py-3">
                                Grand Total
                            </td>

                            <td class="text-center" id="gtPlots">{{ $plots }}</td>

                            <td class="text-center" id="gtApplicants">{{ $applicants }}</td>

                            <td class="text-center" id="gtPaid">{{ $paid }}</td>

                            

                            <td class="text-center" id="gtSC">{{ $sc }}</td>

                            <td class="text-center" id="gtGhumantu">{{ $ghumantu }}</td>

                            <td class="text-center" id="gtWidow">{{ $widow }}</td>

                            <td class="text-center" id="gtOthers">{{ $others }}</td>
                             <td class="text-center" id="gtAllotment">{{ $allotted }}</td>
                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>
