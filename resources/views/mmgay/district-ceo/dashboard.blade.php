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

            <button class="phase-tab activePhase px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold shadow"
                data-phase="1">
                Phase 1
            </button>

            <button class="phase-tab px-6 py-2 rounded-xl bg-white border hover:bg-slate-50 font-semibold" data-phase="2">
                Phase 2
            </button>

            <button class="phase-tab px-6 py-2 rounded-xl bg-white border hover:bg-slate-50 font-semibold" data-phase="3">
                Phase 3
            </button>

        </div>

        <!-- ===================== KPI CARDS ===================== -->

        <!-- ===================== KPI CARDS ===================== -->

        <div class="grid xl:grid-cols-6 lg:grid-cols-3 md:grid-cols-2 gap-3 mt-6">

            <!-- Total -->
            <div onclick="openList('total')"
                class="cursor-pointer bg-white border border-gray-200 rounded-2xl px-4 py-3 hover:shadow-md transition">

                <div class="flex items-center gap-3">

                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-indigo-600">
                            inventory_2
                        </span>
                    </div>

                    <div class="flex-1">
                        <p class="text-[11px] uppercase font-semibold tracking-wide text-gray-500">
                            Total Plots
                        </p>

                        <h2 id="total" class="text-3xl font-bold leading-none text-slate-900 mt-1">
                            0
                        </h2>
                    </div>

                </div>

            </div>

            <!-- Paid -->
            <div onclick="openList('paid')"
                class="cursor-pointer bg-white border border-gray-200 rounded-2xl px-4 py-3 hover:shadow-md transition">

                <div class="flex items-center gap-3">

                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600">
                            payments
                        </span>
                    </div>

                    <div class="flex-1">
                        <p class="text-[11px] uppercase font-semibold tracking-wide text-gray-500">
                            Paid
                        </p>

                        <h2 id="paid" class="text-3xl font-bold leading-none text-green-700 mt-1">
                            0
                        </h2>
                    </div>

                </div>

            </div>

            <!-- Approved -->
            <div onclick="openList('approved')"
                class="cursor-pointer bg-white border border-gray-200 rounded-2xl px-4 py-3 hover:shadow-md transition">

                <div class="flex items-center gap-3">

                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600">
                            verified
                        </span>
                    </div>

                    <div class="flex-1">
                        <p class="text-[11px] uppercase font-semibold tracking-wide text-gray-500">
                            Approved
                        </p>

                        <h2 id="approved" class="text-3xl font-bold leading-none text-blue-700 mt-1">
                            0
                        </h2>
                    </div>

                </div>

            </div>

            <!-- Pending -->
            <div onclick="openList('pending')"
                class="cursor-pointer bg-white border border-gray-200 rounded-2xl px-4 py-3 hover:shadow-md transition">

                <div class="flex items-center gap-3">

                    <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-orange-600">
                            pending_actions
                        </span>
                    </div>

                    <div class="flex-1">
                        <p class="text-[11px] uppercase font-semibold tracking-wide text-gray-500">
                            Pending
                        </p>

                        <h2 id="pending" class="text-3xl font-bold leading-none text-orange-600 mt-1">
                            0
                        </h2>
                    </div>

                </div>

            </div>

            <!-- Rejected -->
            <div onclick="openList('rejected')"
                class="cursor-pointer bg-white border border-gray-200 rounded-2xl px-4 py-3 hover:shadow-md transition">

                <div class="flex items-center gap-3">

                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-red-600">
                            cancel
                        </span>
                    </div>

                    <div class="flex-1">
                        <p class="text-[11px] uppercase font-semibold tracking-wide text-gray-500">
                            Rejected
                        </p>

                        <h2 id="rejected" class="text-3xl font-bold leading-none text-red-600 mt-1">
                            0
                        </h2>
                    </div>

                </div>

            </div>

            <!-- In Process -->
            <div onclick="openList('inprocess')"
                class="cursor-pointer bg-white border border-gray-200 rounded-2xl px-4 py-3 hover:shadow-md transition">

                <div class="flex items-center gap-3">

                    <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-yellow-600">
                            sync
                        </span>
                    </div>

                    <div class="flex-1">
                        <p class="text-[11px] uppercase font-semibold tracking-wide text-gray-500">
                            In Process
                        </p>

                        <h2 id="inprocess" class="text-3xl font-bold leading-none text-yellow-600 mt-1">
                            0
                        </h2>
                    </div>

                </div>

            </div>

        </div>

        

       
