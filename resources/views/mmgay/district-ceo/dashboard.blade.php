@extends('layouts.mmgayCEOAuth')
@section('title', 'MMGAY Department Dashboard')

<!-- MAIN CONTENT AREA -->
<main class="ml-[260px] mt-16 flex-1 h-[calc(100vh-64px)] overflow-y-auto p-lg bg-background">
    <!-- Tab Sub-Navigation -->
    <div class="mb-lg border-b border-outline-variant flex items-center gap-xl">

        <button class="phase-tab px-xs pb-sm text-primary font-bold border-b-2 border-primary transition-colors"
            data-phase="1">
            <span class="text-body-md">Phase 1</span>
        </button>

        <button class="phase-tab px-xs pb-sm text-on-surface-variant hover:text-primary transition-colors" data-phase="2">
            <span class="text-body-md">Phase 2</span>
        </button>

        <button class="phase-tab px-xs pb-sm text-on-surface-variant hover:text-primary transition-colors" data-phase="3">
            <span class="text-body-md">Phase 3</span>
        </button>

    </div>

    <!-- KPI Grid (Bento Style) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        <!-- KPI 1 -->
        <div class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-blue-50 hover:border-blue-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer"
            onclick="openList('total')">
            <div
                class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                    inventory_2
                </span>
            </div>

            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Total Plots
            </h4>

            <p class="text-2xl font-bold text-blue-700 mt-1" id="total">
                0
            </p>

            
        </div>

        <!-- KPI 2 -->
        <div class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-green-50 hover:border-green-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer"
            onclick="openList('paid')">

            <div
                class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                    payments
                </span>
            </div>

            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Paid
            </h4>

            <p class="text-2xl font-bold text-green-700 mt-1" id="paid">
                0
            </p>

            

            
        </div>

        <!-- KPI 3 -->
        <div class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-blue-50 hover:border-blue-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer"
            onclick="openList('approved')">

            <div
                class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                    verified
                </span>
            </div>

            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Approved
            </h4>

            <p class="text-2xl font-bold text-blue-700 mt-1" id="approved">
                0
            </p>

            <span class="mt-2 text-[10px] px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                Verified
            </span>

        </div>

        <!-- KPI 4 -->
        <div class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-yellow-50 hover:border-yellow-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer"
            onclick="openList('inprocess')">

            <div
                class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                    cycle
                </span>
            </div>

            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                In Process
            </h4>

            <p class="text-2xl font-bold text-yellow-700 mt-1" id="inprocess">
                0
            </p>

            <div class="flex items-center gap-1 mt-2">
                <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                <span class="text-[10px] text-gray-500">
                    Processing
                </span>
            </div>

        </div>

        <!-- KPI 5 -->
        <div class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-red-50 hover:border-red-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer"
            onclick="openList('rejected')">

            <div
                class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                    cancel
                </span>
            </div>

            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Rejected
            </h4>

            <p class="text-2xl font-bold text-red-600 mt-1" id="rejected">
                0
            </p>

            <span class="mt-2 text-[10px] font-medium text-red-600">
                View List
            </span>

        </div>

        <!-- KPI 6 -->
        <div class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-orange-50 hover:border-orange-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 min-h-[150px] flex flex-col items-center justify-center text-center cursor-pointer"
            onclick="openList('pending')">

            <div
                class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mb-2 transition-transform duration-300 group-hover:scale-110">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">
                    pending_actions
                </span>
            </div>

            <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Pending Approval
            </h4>

            <p class="text-2xl font-bold text-orange-600 mt-1" id="pending">
                0
            </p>

            <div class="mt-2 px-2 py-1 rounded-full bg-orange-100">
                <span class="text-[10px] font-medium text-orange-700">
                    Needs Review
                </span>
            </div>

        </div>

    </div>
    <!-- Data Visualization Area (Asymmetric Component) -->

</main>
