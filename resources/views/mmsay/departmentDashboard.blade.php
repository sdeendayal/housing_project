@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Dashboard')
@section('content')
    <style>
        .success-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #16a34a;
            color: white;
            padding: 14px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 9999;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .2);
            animation: slideIn .4s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
    @if (session('success'))
        <div id="successToast" class="success-toast">
            <span class="material-symbols-outlined me-2">
                check_circle
            </span>

            {{ session('success') }}
        </div>
    @endif
    <main class="ml-64 pt-20 p-md min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">
            <!-- Dashboard Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-primary">
                        Dashboard
                    </h3>

                    <p class="text-sm text-on-surface-variant">
                        Real-time overview of housing portal operations
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-white border border-outline-variant rounded-lg px-3 py-2 flex items-center gap-2 shadow-sm cursor-pointer hover:bg-surface-container transition-colors text-sm">

                        <span class="font-medium text-gray-600">
                            Branch:
                        </span>

                        <span class="font-medium text-primary">
                            All Branch
                        </span>

                        <span class="material-symbols-outlined text-[18px]">
                            expand_more
                        </span>
                    </div>

                    <!-- Generate Report Button -->
                    <button
                        class="bg-primary text-on-primary px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 hover:shadow-md transition-all">

                        <span class="material-symbols-outlined text-[18px]">
                            add
                        </span>

                        Generate Report
                    </button>
                </div>
            </div>
            <!-- Bento Grid - Summary Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                <!-- Total Applications -->
                <div class="bg-white rounded-2xl shadow-sm border p-5 hover:shadow-lg transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 text-sm">Total Properties</p>
                            <h3 class="text-3xl font-bold text-blue-600 mt-2">
                                {{ number_format($totalApplications) }}
                            </h3>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600 text-3xl">
                                home
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Allotted -->
                <div class="bg-white rounded-2xl shadow-sm border p-5 hover:shadow-lg transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 text-sm">Allotted Units</p>
                            <h3 class="text-3xl font-bold text-green-600 mt-2">
                                {{ number_format($allottedUnits) }}
                            </h3>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-600 text-3xl">
                                apartment
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white rounded-2xl shadow-sm border p-5 hover:shadow-lg transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 text-sm">Pending Installments</p>
                            <h3 class="text-3xl font-bold text-orange-600 mt-2">
                                {{ number_format($pendingInstallments) }}
                            </h3>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-orange-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-orange-600 text-3xl">
                                pending_actions
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-white rounded-2xl shadow-sm border p-5 hover:shadow-lg transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 text-sm">Total Revenue</p>
                            <h3 class="text-3xl font-bold text-purple-600 mt-2">
                                ₹ {{ 34555.7 }}
                            </h3>
                        </div>

                        <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-purple-600 text-3xl">
                                payments
                            </span>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Asymmetric Layout: Chart and Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Analytics Section -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                    <div class="flex items-center justify-between mb-6">

                        <div>
                            <h5 class="text-lg font-semibold text-gray-800">
                                Registration Trends
                            </h5>

                            <p class="text-xs text-gray-500">
                                Monthly & Weekly Property Registrations
                            </p>
                        </div>

                        <div class="flex gap-2">

                            <button id="monthlyBtn" class="px-4 py-2 text-xs rounded-lg bg-blue-600 text-white">
                                Monthly
                            </button>

                            <button id="weeklyBtn" class="px-4 py-2 text-xs rounded-lg border border-gray-300">
                                Weekly
                            </button>

                        </div>

                    </div>

                    <div style="height:350px;">
                        <canvas id="registrationChart"></canvas>
                    </div>

                </div>

                <!-- Right Side -->
                <div class="space-y-4">

                    <!-- Quick Actions -->
                    <div
                        class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02]">
                        <h5 class="text-sm font-semibold text-primary mb-3">
                            Quick Actions
                        </h5>

                        <div class="grid grid-cols-2 gap-3">

                            <button
                                class="flex flex-col items-center justify-center py-4 rounded-xl bg-blue-50 border border-blue-100 hover:bg-blue-100 hover:scale-105 active:scale-95 transition-all duration-300 shadow-sm">
                                <span class="material-symbols-outlined text-[22px] mb-1 text-blue-600">
                                    add_home
                                </span>
                                <span class="text-xs font-medium text-gray-700">
                                    New Unit
                                </span>
                            </button>

                            <button
                                class="flex flex-col items-center justify-center py-4 rounded-xl bg-green-50 border border-green-100 hover:bg-green-100 hover:scale-105 active:scale-95 transition-all duration-300 shadow-sm">
                                <span class="material-symbols-outlined text-[22px] mb-1 text-green-600">
                                    person_add
                                </span>
                                <span class="text-xs font-medium text-gray-700">
                                    New Applicant
                                </span>
                            </button>

                            <button
                                class="flex flex-col items-center justify-center py-4 rounded-xl bg-orange-50 border border-orange-100 hover:bg-orange-100 hover:scale-105 active:scale-95 transition-all duration-300 shadow-sm">
                                <span class="material-symbols-outlined text-[22px] mb-1 text-orange-600">
                                    receipt
                                </span>
                                <span class="text-xs font-medium text-gray-700">
                                    Receipt
                                </span>
                            </button>

                            <button
                                class="flex flex-col items-center justify-center py-4 rounded-xl bg-purple-50 border border-purple-100 hover:bg-purple-100 hover:scale-105 active:scale-95 transition-all duration-300 shadow-sm">
                                <span class="material-symbols-outlined text-[22px] mb-1 text-purple-600">
                                    print
                                </span>
                                <span class="text-xs font-medium text-gray-700">
                                    Report
                                </span>
                            </button>

                        </div>
                    </div>

                    <!-- System Alerts -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">

                        <h5 class="text-sm font-semibold text-primary mb-3">
                            System Alerts
                        </h5>

                        <div class="space-y-3">

                            <div class="flex gap-3">
                                <div class="w-1 rounded-full bg-red-500"></div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-800">
                                        Verification Overdue
                                    </p>

                                    <p class="text-[11px] text-gray-500">
                                        14 applications pending for &gt; 7 days
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div class="w-1 rounded-full bg-green-500"></div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-800">
                                        Payment Success
                                    </p>

                                    <p class="text-[11px] text-gray-500">
                                        Batch payment successful for Zone 4
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <!-- Recent Activity Table Section -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">

                <!-- Header -->
                <div class="px-4 py-3 flex items-center justify-between border-b border-gray-200">
                    <h5 class="text-sm font-semibold text-primary">
                        Latest Applications
                    </h5>

                    <button class="text-xs text-primary flex items-center gap-1 hover:underline font-medium">
                        View All
                        <span class="material-symbols-outlined text-[16px]">
                            chevron_right
                        </span>
                    </button>
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

                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-xs font-semibold">
                                            AS
                                        </div>

                                        <div>
                                            <p class="text-sm font-medium text-gray-800">
                                                Amit Sharma
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                amit.s@example.gov
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    Residential - Type A
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    #HSG-2023-0045
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 rounded-full text-[10px] font-medium bg-yellow-100 text-yellow-700">
                                        Pending
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button
                                        class="w-8 h-8 rounded-md hover:bg-gray-100 transition inline-flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[18px] text-primary">
                                            visibility
                                        </span>
                                    </button>
                                </td>

                            </tr>

                            <!-- Row 2 -->
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-8 h-8 rounded-full bg-tertiary-fixed flex items-center justify-center text-xs font-semibold">
                                            PK
                                        </div>

                                        <div>
                                            <p class="text-sm font-medium text-gray-800">
                                                Priya Kumari
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                priya.k@example.gov
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    Commercial - Plaza 2
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    #HSG-2023-0042
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 rounded-full text-[10px] font-medium bg-green-100 text-green-700">
                                        Approved
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button
                                        class="w-8 h-8 rounded-md hover:bg-gray-100 transition inline-flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[18px] text-primary">
                                            visibility
                                        </span>
                                    </button>
                                </td>

                            </tr>

                            <!-- Row 3 -->
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-semibold">
                                            RK
                                        </div>

                                        <div>
                                            <p class="text-sm font-medium text-gray-800">
                                                Rajesh Kumar
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                rajesh.k@example.gov
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    Residential - Type B
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    #HSG-2023-0041
                                </td>

                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-medium bg-gray-100 text-gray-700">
                                        Archived
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button
                                        class="w-8 h-8 rounded-md hover:bg-gray-100 transition inline-flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[18px] text-primary">
                                            visibility
                                        </span>
                                    </button>
                                </td>

                            </tr>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </main>

@endsection
