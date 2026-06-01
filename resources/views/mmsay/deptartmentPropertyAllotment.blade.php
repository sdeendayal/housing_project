@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Property Registration')
@section('content')
    <main class="ml-64 p-gutter pt-8">
        <div class="pt-20 px-0 pb-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">

            <div>
                <h3 class="text-xl font-medium text-primary mb-0.5">
                    Property Auction
                </h3>
                <p class="text-xs text-gray-500 font-normal">
                    Manage and monitor land auction details across national districts.
                </p>
            </div>

            <button
                class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-md text-sm font-normal shadow-sm hover:shadow-md hover:-translate-y-0.5 active:scale-95 transition-all"
                onclick="openModal()">

                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Allotted New Property</span>
            </button>

        </div>
        <!-- Table Filters & Search -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4 shadow-sm">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">

                <h4 class="text-base font-medium text-primary">
                    Land Auction Details
                </h4>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">

                    <div class="relative hidden" id="filterInputContainer">
                        <input
                            class="bg-white border border-gray-300 rounded-md px-3 py-2 w-60 text-sm focus:ring-1 focus:ring-primary focus:border-primary"
                            id="tableSearch" onkeyup="filterTable()" placeholder="Search record..." type="text" />
                    </div>

                    <div class="flex items-center gap-2">

                        <button
                            class="flex items-center gap-1.5 border border-primary text-primary px-3 py-2 rounded-md text-sm font-normal hover:bg-primary/5 transition-all"
                            onclick="toggleFilter()">

                            <span class="material-symbols-outlined text-[18px]">
                                filter_alt
                            </span>
                            Filter
                        </button>

                        <button
                            class="flex items-center gap-1.5 bg-primary text-white px-3 py-2 rounded-md text-sm font-normal shadow-sm hover:shadow-md transition-all"
                            onclick="downloadExcel()">

                            <span class="material-symbols-outlined text-[18px]">
                                download
                            </span>
                            Download Excel
                        </button>

                    </div>
                </div>
            </div>
        </div>
        <!-- High Density Data Table -->
        <div class="glass-card rounded-xl overflow-hidden overflow-x-auto shadow-sm">
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="w-full min-w-[1200px] text-sm text-left" id="auctionTable">

                    <!-- Table Header -->
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr class="text-[11px] font-medium uppercase tracking-wide text-gray-600">
                            <th class="px-3 py-3 whitespace-nowrap">Actions</th>
                            <th class="px-3 py-3 whitespace-nowrap">EM Office</th>
                            <th class="px-3 py-3 whitespace-nowrap">District</th>
                            <th class="px-3 py-3 whitespace-nowrap">City</th>
                            <th class="px-3 py-3 whitespace-nowrap">Sector</th>
                            <th class="px-3 py-3 whitespace-nowrap">Asset #</th>
                            <th class="px-3 py-3 whitespace-nowrap">Type</th>
                            <th class="px-3 py-3 whitespace-nowrap">Auction Date</th>
                            <th class="px-3 py-3 whitespace-nowrap">Allotment</th>
                            <th class="px-3 py-3 whitespace-nowrap">Possession</th>
                            <th class="px-3 py-3 whitespace-nowrap">ROI %</th>
                            <th class="px-3 py-3 whitespace-nowrap">Reserve Price</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">
                                Sales Amount
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-gray-100 text-[13px] text-gray-700 bg-white">

                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">

                                    <button class="p-1.5 rounded-md hover:bg-gray-100 transition"
                                        onclick="toggleExpand('row1-expand', this)">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-gray-500 transition-transform">
                                            keyboard_arrow_down
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-blue-50 text-blue-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            edit
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-green-50 text-green-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            visibility
                                        </span>
                                    </button>
                                </div>
                            </td>

                            <td class="px-3 py-3 font-medium">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">19</td>
                            <td class="px-3 py-3 font-semibold">317</td>
                            <td class="px-3 py-3">Residential</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">28-01-1987</td>
                            <td class="px-3 py-3">-</td>
                            <td class="px-3 py-3 font-medium">₹28,375</td>
                            <td class="px-3 py-3 text-right font-semibold text-primary">
                                ₹35,900
                            </td>
                        </tr>

                        <!-- Expand Row -->
                        <tr class="hidden bg-gray-50" id="row1-expand">
                            <td colspan="13" class="px-10 py-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>
                                    test user
                                </div>
                            </td>
                        </tr>

                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">

                                    <button class="p-1.5 rounded-md hover:bg-gray-100 transition"
                                        onclick="toggleExpand('row1-expand', this)">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-gray-500 transition-transform">
                                            keyboard_arrow_down
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-blue-50 text-blue-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            edit
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-green-50 text-green-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            visibility
                                        </span>
                                    </button>
                                </div>
                            </td>

                            <td class="px-3 py-3 font-medium">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">19</td>
                            <td class="px-3 py-3 font-semibold">317</td>
                            <td class="px-3 py-3">Residential</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">28-01-1987</td>
                            <td class="px-3 py-3">-</td>
                            <td class="px-3 py-3 font-medium">₹28,375</td>
                            <td class="px-3 py-3 text-right font-semibold text-primary">
                                ₹35,900
                            </td>
                        </tr>

                        <!-- Expand Row -->
                        <tr class="hidden bg-gray-50" id="row1-expand">
                            <td colspan="13" class="px-10 py-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>
                                    test user
                                </div>
                            </td>
                        </tr>

                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">

                                    <button class="p-1.5 rounded-md hover:bg-gray-100 transition"
                                        onclick="toggleExpand('row1-expand', this)">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-gray-500 transition-transform">
                                            keyboard_arrow_down
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-blue-50 text-blue-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            edit
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-green-50 text-green-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            visibility
                                        </span>
                                    </button>
                                </div>
                            </td>

                            <td class="px-3 py-3 font-medium">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">19</td>
                            <td class="px-3 py-3 font-semibold">317</td>
                            <td class="px-3 py-3">Residential</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">28-01-1987</td>
                            <td class="px-3 py-3">-</td>
                            <td class="px-3 py-3 font-medium">₹28,375</td>
                            <td class="px-3 py-3 text-right font-semibold text-primary">
                                ₹35,900
                            </td>
                        </tr>

                        <!-- Expand Row -->
                        <tr class="hidden bg-gray-50" id="row1-expand">
                            <td colspan="13" class="px-10 py-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>
                                    test user
                                </div>
                            </td>
                        </tr>

                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">

                                    <button class="p-1.5 rounded-md hover:bg-gray-100 transition"
                                        onclick="toggleExpand('row1-expand', this)">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-gray-500 transition-transform">
                                            keyboard_arrow_down
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-blue-50 text-blue-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            edit
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-green-50 text-green-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            visibility
                                        </span>
                                    </button>
                                </div>
                            </td>

                            <td class="px-3 py-3 font-medium">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">19</td>
                            <td class="px-3 py-3 font-semibold">317</td>
                            <td class="px-3 py-3">Residential</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">28-01-1987</td>
                            <td class="px-3 py-3">-</td>
                            <td class="px-3 py-3 font-medium">₹28,375</td>
                            <td class="px-3 py-3 text-right font-semibold text-primary">
                                ₹35,900
                            </td>
                        </tr>

                        <!-- Expand Row -->
                        <tr class="hidden bg-gray-50" id="row1-expand">
                            <td colspan="13" class="px-10 py-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>
                                    test user
                                </div>
                            </td>
                        </tr>

                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">

                                    <button class="p-1.5 rounded-md hover:bg-gray-100 transition"
                                        onclick="toggleExpand('row1-expand', this)">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-gray-500 transition-transform">
                                            keyboard_arrow_down
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-blue-50 text-blue-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            edit
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-green-50 text-green-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            visibility
                                        </span>
                                    </button>
                                </div>
                            </td>

                            <td class="px-3 py-3 font-medium">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">19</td>
                            <td class="px-3 py-3 font-semibold">317</td>
                            <td class="px-3 py-3">Residential</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">28-01-1987</td>
                            <td class="px-3 py-3">-</td>
                            <td class="px-3 py-3 font-medium">₹28,375</td>
                            <td class="px-3 py-3 text-right font-semibold text-primary">
                                ₹35,900
                            </td>
                        </tr>

                        <!-- Expand Row -->
                        <tr class="hidden bg-gray-50" id="row1-expand">
                            <td colspan="13" class="px-10 py-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>
                                    test user
                                </div>
                            </td>
                        </tr>

                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">

                                    <button class="p-1.5 rounded-md hover:bg-gray-100 transition"
                                        onclick="toggleExpand('row1-expand', this)">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-gray-500 transition-transform">
                                            keyboard_arrow_down
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-blue-50 text-blue-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            edit
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-green-50 text-green-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            visibility
                                        </span>
                                    </button>
                                </div>
                            </td>

                            <td class="px-3 py-3 font-medium">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">19</td>
                            <td class="px-3 py-3 font-semibold">317</td>
                            <td class="px-3 py-3">Residential</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">28-01-1987</td>
                            <td class="px-3 py-3">-</td>
                            <td class="px-3 py-3 font-medium">₹28,375</td>
                            <td class="px-3 py-3 text-right font-semibold text-primary">
                                ₹35,900
                            </td>
                        </tr>

                        <!-- Expand Row -->
                        <tr class="hidden bg-gray-50" id="row1-expand">
                            <td colspan="13" class="px-10 py-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>
                                    test user
                                </div>
                            </td>
                        </tr>

                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">

                                    <button class="p-1.5 rounded-md hover:bg-gray-100 transition"
                                        onclick="toggleExpand('row1-expand', this)">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-gray-500 transition-transform">
                                            keyboard_arrow_down
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-blue-50 text-blue-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            edit
                                        </span>
                                    </button>

                                    <button class="p-1.5 rounded-md hover:bg-green-50 text-green-600 transition">
                                        <span class="material-symbols-outlined text-[18px]">
                                            visibility
                                        </span>
                                    </button>
                                </div>
                            </td>

                            <td class="px-3 py-3 font-medium">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">Panchkula</td>
                            <td class="px-3 py-3">19</td>
                            <td class="px-3 py-3 font-semibold">317</td>
                            <td class="px-3 py-3">Residential</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">07-01-1987</td>
                            <td class="px-3 py-3">28-01-1987</td>
                            <td class="px-3 py-3">-</td>
                            <td class="px-3 py-3 font-medium">₹28,375</td>
                            <td class="px-3 py-3 text-right font-semibold text-primary">
                                ₹35,900
                            </td>
                        </tr>

                        <!-- Expand Row -->
                        <tr class="hidden bg-gray-50" id="row1-expand">
                            <td colspan="13" class="px-10 py-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>
                                    test user
                                </div>
                            </td>
                        </tr>

                        

                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <div class="flex justify-between items-center mt-6">
            <span class="text-body-sm text-on-surface-variant">Showing 1 to 3 of 10,192 entries</span>
            <div class="flex items-center gap-2">
                <button class="p-2 border border-outline-variant rounded hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-lg" data-icon="chevron_left">chevron_left</span>
                </button>
                <div class="flex items-center gap-1">
                    <button class="px-3 py-1 bg-primary text-on-primary rounded font-bold">1</button>
                    <button
                        class="px-3 py-1 hover:bg-surface-container-high rounded transition-colors text-body-sm">2</button>
                    <button
                        class="px-3 py-1 hover:bg-surface-container-high rounded transition-colors text-body-sm">3</button>
                    <span class="px-2">...</span>
                    <button
                        class="px-3 py-1 hover:bg-surface-container-high rounded transition-colors text-body-sm">340</button>
                </div>
                <button class="p-2 border border-outline-variant rounded hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-lg" data-icon="chevron_right">chevron_right</span>
                </button>
            </div>
        </div>
    </main>
    <!-- Modal Overlay -->
    <div class="fixed inset-0 bg-primary/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-gutter transition-opacity duration-300 opacity-0"
        id="modalOverlay">
        <div class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden max-h-[921px] flex flex-col scale-95 transition-transform duration-300"
            id="modalContainer">
            <!-- Modal Header -->
            <div
                class="px-gutter py-6 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <div>
                    <h2 class="font-headline-md text-headline-md text-primary">Allotted New Property</h2>
                    <p class="font-label-sm text-on-surface-variant">Complete the form below to register a new property
                        allotment.</p>
                </div>
                <button class="p-2 hover:bg-error-container hover:text-error rounded-full transition-colors"
                    onclick="closeModal()">
                    <span class="material-symbols-outlined" data-icon="close">close</span>
                </button>
            </div>
            <!-- Modal Content (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-gutter scrollbar-hide">
                <form class="space-y-12">
                    <!-- Location Details -->
                    <section>
                        <h3 class="font-label-md text-primary font-bold border-l-4 border-primary pl-3 mb-6">Location
                            Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Estate Manager Office*</label>
                                <select
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary">
                                    <option>Select EM Office</option>
                                    <option>Panchkula</option>
                                    <option>Ambala</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">District Office*</label>
                                <select
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary">
                                    <option>Select District</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">City Office*</label>
                                <select
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary">
                                    <option>Select City</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <!-- Property & Asset Details -->
                    <section>
                        <h3 class="font-label-md text-primary font-bold border-l-4 border-primary pl-3 mb-6">Property &amp;
                            Asset Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Sector*</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary"
                                    placeholder="Enter Sector" type="text" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Asset Number*</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary"
                                    placeholder="Enter Asset Number" type="text" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Property Type*</label>
                                <select
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary">
                                    <option>Residential</option>
                                    <option>Commercial</option>
                                    <option>Industrial</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Property Category*</label>
                                <select
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary">
                                    <option>General</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Property Scheme*</label>
                                <select
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary">
                                    <option>Allotment Scheme 2024</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Mode of Allotment*</label>
                                <select
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary">
                                    <option>E-Auction</option>
                                    <option>Draw of Lots</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <!-- Date & Timeline Details -->
                    <section>
                        <h3 class="font-label-md text-primary font-bold border-l-4 border-primary pl-3 mb-6">Timeline
                            Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Sale Date*</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary"
                                    type="date" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Date of Allotment*</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary"
                                    type="date" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Date of Possession*</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-3 focus:ring-primary focus:border-primary"
                                    type="date" />
                            </div>
                        </div>
                    </section>
                    <!-- Property Cost Details (Bento Grid Style) -->
                    <section class="bg-surface-container-low rounded-xl p-8 border border-outline-variant">
                        <div class="flex items-center gap-2 mb-8">
                            <span class="material-symbols-outlined text-secondary" data-icon="payments">payments</span>
                            <h3 class="font-headline-sm text-headline-sm text-primary">Property Cost Details</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="col-span-1 md:col-span-2 space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Flat Cost (In RS.)*</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-4 bg-white focus:ring-primary font-bold text-lg text-primary"
                                    placeholder="0.00" type="number" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Increased Cost</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-4 bg-white focus:ring-primary"
                                    placeholder="0" type="number" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Location Cost</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-4 bg-white focus:ring-primary"
                                    placeholder="0" type="number" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Area Cost</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-4 bg-white focus:ring-primary"
                                    placeholder="0" type="number" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Maintenance Cost</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-4 bg-white focus:ring-primary"
                                    placeholder="0" type="number" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Liability Cost</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-4 bg-white focus:ring-primary"
                                    placeholder="0" type="number" />
                            </div>
                            <div class="space-y-1">
                                <label class="font-label-sm text-on-surface-variant">Rate of Interest (%)</label>
                                <input
                                    class="w-full border border-outline-variant rounded-lg p-4 bg-white focus:ring-primary"
                                    placeholder="8.5" step="0.1" type="number" />
                            </div>
                        </div>
                    </section>
                </form>
            </div>
            <!-- Modal Footer -->
            <div class="px-gutter py-6 border-t border-outline-variant bg-surface flex justify-end gap-4">
                <button
                    class="px-6 py-3 border border-outline text-on-surface rounded-lg font-bold hover:bg-surface-container-high transition-colors"
                    onclick="closeModal()">
                    Discard Changes
                </button>
                <button
                    class="px-8 py-3 bg-primary text-on-primary rounded-lg font-bold shadow-md hover:shadow-lg transition-all">
                    Register Property
                </button>
            </div>
        </div>
    </div>

@endsection
