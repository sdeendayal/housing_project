@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Dashboard')
@section('content')
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Card 1 -->
                <div
                    class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex flex-col gap-2 shadow-sm transition-all duration-300 hover:scale-[1.03] hover:shadow-md active:scale-[0.98] cursor-pointer">

                    <div class="flex items-center justify-between">

                        <div class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-[20px]">
                                description
                            </span>
                        </div>

                        <span class="text-green-600 text-xs font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">
                                trending_up
                            </span>
                            +12%
                        </span>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-500">
                            Total Applications
                        </p>

                        <h4 class="text-xl font-semibold text-primary">
                            12,845
                        </h4>
                    </div>
                </div>

                <!-- Card 2 -->
                <div
                    class="bg-yellow-50 border border-yellow-100 p-4 rounded-xl flex flex-col gap-2 shadow-sm transition-all duration-300 hover:scale-[1.03] hover:shadow-md active:scale-[0.98] cursor-pointer">

                    <div class="flex items-center justify-between">

                        <div class="w-9 h-9 bg-yellow-500 rounded-lg flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-[20px]">
                                pending_actions
                            </span>
                        </div>

                        <span class="text-red-500 text-xs font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">
                                trending_down
                            </span>
                            -4%
                        </span>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-500">
                            Pending Verifications
                        </p>

                        <h4 class="text-xl font-semibold text-primary">
                            2,412
                        </h4>
                    </div>
                </div>

                <!-- Card 3 -->
                <div
                    class="bg-green-50 border border-green-100 p-4 rounded-xl flex flex-col gap-2 shadow-sm transition-all duration-300 hover:scale-[1.03] hover:shadow-md active:scale-[0.98] cursor-pointer">

                    <div class="flex items-center justify-between">

                        <div class="w-9 h-9 bg-green-500 rounded-lg flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-[20px]">
                                holiday_village
                            </span>
                        </div>

                        <span class="text-green-600 text-xs font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">
                                trending_up
                            </span>
                            +8%
                        </span>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-500">
                            Allotted Units
                        </p>

                        <h4 class="text-xl font-semibold text-primary">
                            8,102
                        </h4>
                    </div>
                </div>

                <!-- Card 4 -->
                <div
                    class="bg-purple-50 border border-purple-100 p-4 rounded-xl flex flex-col gap-2 shadow-sm transition-all duration-300 hover:scale-[1.03] hover:shadow-md active:scale-[0.98] cursor-pointer">

                    <div class="flex items-center justify-between">

                        <div class="w-9 h-9 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-[20px]">
                                account_balance_wallet
                            </span>
                        </div>

                        <span class="text-green-600 text-xs font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">
                                trending_up
                            </span>
                            +15%
                        </span>
                    </div>

                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-500">
                            Total Revenue
                        </p>

                        <h4 class="text-xl font-semibold text-primary">
                            ₹4.2M
                        </h4>
                    </div>
                </div>

            </div>
            <!-- Asymmetric Layout: Chart and Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Analytics Section -->
                <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg p-4 shadow-sm">

                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-sm font-semibold text-primary">
                            Registration Trends
                        </h5>

                        <div class="flex gap-2">
                            <button
                                class="px-3 py-1 text-xs rounded-md bg-surface-container-high text-on-surface border border-outline-variant">
                                Monthly
                            </button>

                            <button
                                class="px-3 py-1 text-xs rounded-md text-on-surface-variant hover:bg-surface-container border border-outline-variant transition">
                                Weekly
                            </button>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="h-[220px] w-full flex items-end justify-between gap-2 pb-2">
                        <div class="flex-1 bg-secondary-container/20 rounded-t-md hover:bg-secondary-container transition"
                            style="height:45%;"></div>
                        <div class="flex-1 bg-secondary-container/20 rounded-t-md hover:bg-secondary-container transition"
                            style="height:60%;"></div>
                        <div class="flex-1 bg-secondary-container/20 rounded-t-md hover:bg-secondary-container transition"
                            style="height:35%;"></div>
                        <div class="flex-1 bg-secondary-container/20 rounded-t-md hover:bg-secondary-container transition"
                            style="height:75%;"></div>
                        <div class="flex-1 bg-secondary-container/20 rounded-t-md hover:bg-secondary-container transition"
                            style="height:90%;"></div>
                        <div class="flex-1 bg-secondary-container/20 rounded-t-md hover:bg-secondary-container transition"
                            style="height:55%;"></div>
                        <div class="flex-1 bg-secondary-container/20 rounded-t-md hover:bg-secondary-container transition"
                            style="height:85%;"></div>
                    </div>

                    <!-- Month Labels -->
                    <div class="flex justify-between text-[11px] text-gray-500 mt-2">
                        <span>Jan</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Apr</span>
                        <span>May</span>
                        <span>Jun</span>
                        <span>Jul</span>
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
