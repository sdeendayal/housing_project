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

            <div
                class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900
            rounded-3xl p-8 border border-slate-700 shadow-2xl shadow-black/30">

                <div class="relative grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">

                    <!-- Property Registration -->
                    <a href="{{ url('mmsay-department-property-registration') }}"
                        class="flex flex-col items-center text-center group cursor-pointer">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-full flex items-center justify-center border-4 border-green-300 shadow-lg shadow-green-500/30 z-10 transition-all duration-300 group-hover:scale-110">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h4 class="mt-3 text-sm font-semibold text-white group-hover:text-green-400">
                            Registration
                        </h4>
                    </a>

                    <a href="{{ url('/mmsay-department-draw') }}" class="flex flex-col items-center text-center group">

                        <div
                            class="w-14 h-14 bg-slate-800 text-cyan-400 rounded-full flex items-center justify-center border-2 border-cyan-500/30 shadow-md hover:shadow-cyan-500/20 transition-all duration-300 group-hover:scale-105 z-10">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>

                        <h4 class="mt-3 text-sm font-semibold text-white">Lucky Draw</h4>

                    </a>



                    <!-- Property Allotment -->
                    <a href="{{ url('mmsay-department-allotted-properties') }}"
                        class="flex flex-col items-center text-center group cursor-pointer">
                        <div
                            class="w-14 h-14 bg-slate-800 text-orange-400 rounded-full flex items-center justify-center border-2 border-orange-500/30 transition-all duration-300 group-hover:scale-105 z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <h4 class="mt-3 text-sm font-semibold text-white group-hover:text-orange-400">
                            Property Allotment
                        </h4>
                        <p class="text-xs text-slate-400">Plot / Flat Allotted</p>
                    </a>

                    <!-- Provisional Letter -->
                    <a href="#" class="flex flex-col items-center text-center group cursor-pointer">
                        <div
                            class="w-14 h-14 bg-slate-800 text-blue-400 rounded-full flex items-center justify-center border-2 border-blue-500/30 transition-all duration-300 group-hover:scale-105 z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                </path>
                            </svg>
                        </div>
                        <h4 class="mt-3 text-sm font-semibold text-white group-hover:text-blue-400">
                            Provisional Letter
                        </h4>
                        <p class="text-xs text-slate-400">Issued After Draw</p>
                    </a>

                    <!-- EMI Payments -->
                    <a href="{{ url('/mmsay-department-emi-payments') }}"
                        class="flex flex-col items-center text-center group cursor-pointer">
                        <div
                            class="w-14 h-14 bg-slate-800 text-yellow-400 rounded-full flex items-center justify-center border-2 border-yellow-500/30 transition-all duration-300 group-hover:scale-105 z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                </path>
                            </svg>
                        </div>
                        <h4 class="mt-3 text-sm font-semibold text-white group-hover:text-yellow-400">
                            EMI Payments
                        </h4>
                        <p class="text-xs text-slate-400">Monthly Installments</p>
                    </a>

                    <!-- Physical Letter -->
                    <a href="{{ url('mmsay-department-physical-letter') }}"
                        class="flex flex-col items-center text-center group cursor-pointer">
                        <div
                            class="w-14 h-14 bg-slate-800 text-pink-400 rounded-full flex items-center justify-center border-2 border-pink-500/30 transition-all duration-300 group-hover:scale-105 z-10">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>
                        </div>
                        <h4 class="mt-3 text-sm font-semibold text-white group-hover:text-pink-400">
                            Physical Letter
                        </h4>
                        <p class="text-xs text-slate-400">After All EMIs Cleared</p>
                    </a>

                </div>
            </div>

            <!-- Bento Grid - Summary Metrics -->






            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">

                <!-- Registration -->
                <a href="{{ url('mmsay-department-property-registration') }}"
                    class="block bg-white rounded-2xl border border-indigo-100 p-5 shadow-sm hover:shadow-lg transition-all duration-300">

                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-indigo-500">
                                person_add
                            </span>
                        </div>
                    </div>

                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        Registration
                    </p>

                    <h3 class="text-3xl font-bold text-slate-800 mt-2">
                        2,89,893
                    </h3>
                </a>

                <!-- Draw -->
                <a href="{{ url('mmsay-department-draw') }}"
                    class="block bg-white rounded-2xl border border-emerald-100 p-5 shadow-sm hover:shadow-lg transition-all duration-300">

                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-emerald-500">
                                casino
                            </span>
                        </div>
                    </div>

                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        Draw
                    </p>

                    <h3 class="text-3xl font-bold text-emerald-600 mt-2">
                        {{ number_format($allottedUnits) }}
                    </h3>

                    <p class="text-xs text-slate-400 mt-1">
                        Lucky Draw Process
                    </p>
                </a>

                <!-- Allotted -->
                <a href="{{ url('mmsay-department-allotted-properties') }}"
                    class="block bg-white rounded-2xl border border-orange-100 p-5 shadow-sm hover:shadow-lg transition-all duration-300">

                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-orange-500">
                                apartment
                            </span>
                        </div>
                    </div>

                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        Allotted
                    </p>

                    <h3 class="text-3xl font-bold text-orange-600 mt-2">
                        {{ number_format($allottedUnits) }}
                    </h3>

                    <p class="text-xs text-slate-400 mt-1">
                        Plot / Flat Assigned
                    </p>
                </a>

                <!-- EMI -->
                <a href="{{ url('/mmsay-department-emi-payments') }}"
                    class="block bg-white rounded-2xl border border-amber-100 p-5 shadow-sm hover:shadow-lg transition-all duration-300">

                    <!-- Icon -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-amber-500">
                                payments
                            </span>
                        </div>
                    </div>

                    <!-- TOTAL EMI (Main Highlight) -->
                    <div class="mt-2">
                        <p class="text-xs text-slate-500">Total EMI</p>
                        <p class="text-2xl font-bold text-amber-600">
                            {{ $emiData->total_emi }}
                        </p>
                    </div>

                    <!-- Paid + Pending (Side by Side) -->
                    <div class="mt-3 flex justify-between gap-4">

                        <div class="flex-1 bg-green-50 rounded-lg p-2 text-center">
                            <p class="text-[10px] text-slate-500">Paid</p>
                            <p class="text-sm font-bold text-green-600">
                                {{ $emiData->paid_emi }}
                            </p>
                        </div>

                        <div class="flex-1 bg-red-50 rounded-lg p-2 text-center">
                            <p class="text-[10px] text-slate-500">Pending</p>
                            <p class="text-sm font-bold text-red-500">
                                {{ $emiData->pending_emi }}
                            </p>
                        </div>

                    </div>

                </a>

                <!-- Revenue -->
                <a href="{{ url('mmsay-department-physical-letter') }}"
                    class="block bg-white rounded-2xl border border-violet-100 p-5 shadow-sm hover:shadow-lg transition-all duration-300">

                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-violet-500">
                                account_balance_wallet
                            </span>
                        </div>
                    </div>

                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        physical possession letter
                    </p>

                    <h3 class="text-3xl font-bold text-violet-600 mt-2">

                    </h3>

                    <p class="text-xs text-emerald-500 mt-1">
                        Collections Stable
                    </p>
                </a>

            </div>

            <!-- Recent Activity Table Section -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">

                <!-- Header -->
                <div class="px-4 py-3 flex items-center justify-between border-b border-gray-200">
                    <h5 class="text-sm font-semibold text-primary">
                        Latest Applications
                    </h5>

                    <a href="{{ url('mmsay-department-physical-letter') }}" class="text-xs text-primary flex items-center gap-1 hover:underline font-medium">
                        View All
                        <span class="material-symbols-outlined text-[16px]">
                            chevron_right
                        </span>
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">

                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr class="text-[11px] uppercase tracking-wide text-gray-500">

                                <th class="px-4 py-3 font-medium">
                                    Applicant Name
                                </th>

                                <th class="px-4 py-3 font-medium">
                                    Property Type
                                </th>

                                <th class="px-4 py-3 font-medium">
                                    Application ID
                                </th>

                                <th class="px-4 py-3 font-medium">
                                    Status
                                </th>

                                <th class="px-4 py-3 font-medium text-right">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($latestPhysicalApplications as $app)
                                <tr class="hover:bg-gray-50 transition">

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">

                                            <div
                                                class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-xs font-semibold">
                                                {{ strtoupper(substr($app->applicant_name, 0, 1)) }}
                                            </div>

                                            <div>
                                                <p class="text-sm font-medium text-gray-800">
                                                    {{ $app->applicant_name }}
                                                </p>

                                                <p class="text-xs text-gray-500">
                                                    {{ $app->mobile }}
                                                </p>
                                            </div>

                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $app->asset_name }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $app->application_number }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded-full text-[10px] font-medium bg-blue-100 text-blue-700">
                                            Submitted
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        <a href="#"
                                            class="w-8 h-8 rounded-md hover:bg-gray-100 transition inline-flex items-center justify-center">

                                            <span class="material-symbols-outlined text-[18px] text-primary">
                                                visibility
                                            </span>

                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-500">
                                        No Physical Possession Applications Found
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </main>

@endsection
