@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Property Registration')
@section('content')
    <main class="ml-64 min-h-screen flex flex-col">

        <div class="pt-20 px-4 pb-4 space-y-4 flex-1">

            <!-- Header Section -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-primary tracking-tight">
                        Assets List
                    </h2>
                    <p class="text-sm text-on-surface-variant mt-0.5">
                        Manage and register property inventory across all
                        municipal sectors.
                    </p>
                </div>

                <button
                    class="flex items-center gap-1.5 bg-primary text-on-primary px-4 py-2 rounded-md text-sm font-medium shadow-sm hover:shadow-md transition-all"
                    onclick="regopenModal()">

                    <span class="material-symbols-outlined text-[18px]">
                        add_circle
                    </span>
                    Add New Asset
                </button>
            </div>

            <!-- Table Container -->
            <div class="glass-card rounded-lg shadow-sm border border-outline-variant overflow-hidden">

                <!-- Table Header -->
                <div
                    class="px-4 py-3 border-b border-outline-variant flex items-center justify-between flex-wrap gap-2 bg-surface-container-lowest">

                    <h3 class="text-sm font-semibold text-primary">
                        Assets List Details
                    </h3>
                    <form method="GET" class="mb-3 flex flex-wrap gap-2">

                        <!-- EM OFFICE -->
                        <select name="em_office" id="emOffice">
                            <option value="">EM Office</option>
                            @foreach ($emOffices as $em)
                                <option value="{{ $em->BranchName }}">{{ $em->BranchName }}</option>
                            @endforeach
                        </select>
                        <!-- DISTRICT -->
                        <select name="district" id="district" class="border p-2 text-sm rounded">
                            <option value="">District</option>
                        </select>

                        <!-- CITY -->
                        <select name="city" id="city" class="border p-2 text-sm rounded">
                            <option value="">City</option>
                        </select>

                        <!-- SECTOR -->
                        <select name="sector" id="sector" class="border p-2 text-sm rounded">
                            <option value="">Sector</option>
                        </select>

                        <!-- STATUS -->
                        <select name="status" class="border p-2 text-sm rounded">
                            <option value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>

                        <button class="bg-blue-600 text-white px-3 py-2 rounded text-sm">
                            Filter
                        </button>

                        <a href="{{ route('properties.export', request()->all()) }}"
                            class="bg-green-600 text-white px-3 py-2 rounded text-sm">
                            Export Excel
                        </a>

                    </form>
                </div>



                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1200px] text-sm">

                        <thead>
                            <tr
                                class="bg-surface-container-low text-[10px] uppercase tracking-wide text-on-surface-variant font-semibold border-b border-outline-variant">

                                <th class="px-3 py-2 text-center">Actions</th>
                                <th class="px-3 py-2">EM Office</th>
                                <th class="px-3 py-2">District Office</th>
                                <th class="px-3 py-2">City Office</th>
                                <th class="px-3 py-2">Sector Name</th>
                                <th class="px-3 py-2">Category</th>
                                <th class="px-3 py-2">Asset Id</th>
                                <th class="px-3 py-2">Asset Number</th>
                                <th class="px-3 py-2">Size</th>
                                <th class="px-3 py-2 text-center">Verified</th>
                                <th class="px-3 py-2 text-center">Auction</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Ledger</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($properties as $item)
                                <tr class="hover:bg-primary/5 transition-colors border-b border-outline-variant">

                                    <!-- ACTION + STATUS -->
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-center gap-1">

                                            <button
                                                class="asset-toggle w-7 h-7 rounded border border-outline-variant flex items-center justify-center hover:bg-surface-container-high transition-transform duration-200"
                                                data-target="detail-{{ $item->AssetId }}">

                                                <span class="material-symbols-outlined text-[16px]">
                                                    keyboard_arrow_down
                                                </span>
                                            </button>

                                            <!-- ACTIVE / INACTIVE ICON -->
                                            @if ($item->IsActive)
                                                <span class="material-symbols-outlined text-green-600 text-[18px]">
                                                    check_circle
                                                </span>
                                            @else
                                                <span class="material-symbols-outlined text-red-500 text-[18px]">
                                                    close
                                                </span>
                                            @endif

                                            <!-- VERIFIED BADGE (example logic) -->
                                            <span
                                                class="px-2 py-1 rounded-md bg-green-100 text-green-700 text-[10px] font-medium border border-green-200">
                                                Verified
                                            </span>

                                        </div>
                                    </td>

                                    <!-- EM OFFICE -->
                                    <td class="px-3 py-2 font-medium">
                                        {{ $item->em_office ?? '-' }}
                                    </td>

                                    <!-- DISTRICT -->
                                    <td class="px-3 py-2">
                                        {{ $item->district ?? '-' }}
                                    </td>

                                    <!-- CITY -->
                                    <td class="px-3 py-2">
                                        {{ $item->city ?? '-' }}
                                    </td>

                                    <!-- SECTOR -->
                                    <td class="px-3 py-2">
                                        {{ $item->sector ?? '-' }}
                                    </td>

                                    <!-- CATEGORY (static or later DB add kar sakte ho) -->
                                    <td class="px-3 py-2">
                                        <span class="bg-surface-container-high px-2 py-1 rounded text-[10px]">
                                            RESIDENTIAL
                                        </span>
                                    </td>

                                    <!-- ASSET ID -->
                                    <td class="px-3 py-2 font-mono text-xs">
                                        {{ $item->AssetId }}
                                    </td>

                                    <!-- ASSET NAME -->
                                    <td class="px-3 py-2 font-mono text-xs">
                                        {{ $item->AssetName }}
                                    </td>

                                    <!-- SIZE -->
                                    <td class="px-3 py-2">
                                        {{ $item->AssetSize }}
                                    </td>

                                    <!-- STATUS ICON -->
                                    <td class="px-3 py-2 text-center">
                                        @if ($item->IsActive)
                                            <span class="material-symbols-outlined text-green-600 text-[18px]">
                                                check_circle
                                            </span>
                                        @else
                                            <span class="material-symbols-outlined text-red-500 text-[18px]">
                                                cancel
                                            </span>
                                        @endif
                                    </td>

                                    <!-- VERIFIED ICON (demo) -->
                                    <td class="px-3 py-2 text-center">
                                        <span class="material-symbols-outlined text-green-600 text-[18px]">
                                            check_circle
                                        </span>
                                    </td>

                                    <!-- PAYMENT STATUS (demo static) -->
                                    <td class="px-3 py-2">
                                        <span
                                            class="px-2 py-1 rounded-md text-[10px] font-medium bg-green-100 text-green-700 border border-green-200">
                                            PAID
                                        </span>
                                    </td>

                                    <!-- DELETE / ACTION -->
                                    <td class="px-3 py-2 text-center">
                                        <span class="material-symbols-outlined text-red-500 text-[18px]">
                                            cancel
                                        </span>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $properties->links() }}
                </div>
            </div>
        </div>
    </main>
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 bg-primary/40 backdrop-blur-sm z-[100] hidden items-center justify-center overflow-y-auto p-4 md:p-8"
        id="modalOverlay">
        <!-- Modal Content -->
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl border border-outline-variant overflow-hidden transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-full"
            id="modalContent">
            <!-- Modal Header -->
            <div class="bg-primary p-6 flex items-center justify-between text-white shrink-0">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[28px]">add_business</span>
                    <h2 class="font-headline-md text-headline-md">Add New Asset</h2>
                </div>
                <button class="w-10 h-10 flex items-center justify-center hover:bg-white/10 rounded-full transition-colors"
                    onclick="regcloseModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-8 space-y-8 bg-surface">
                <!-- Location Details Section -->
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-6 bg-secondary rounded-full"></span>
                        <h3 class="text-secondary font-bold uppercase tracking-wider text-[12px]">Location Details</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Estate Manager Office
                                <span class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select EM</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">District Office <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select District</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">City Office <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select City</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Sector <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Sector</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Scheme <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Scheme</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Location Status <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Location</option>
                            </select>
                        </div>
                    </div>
                </section>
                <!-- Property Details Section -->
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-6 bg-secondary rounded-full"></span>
                        <h3 class="text-secondary font-bold uppercase tracking-wider text-[12px]">Property Details</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Property type <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Property Type</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Property Category
                                <span class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Category</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Property Status <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Status</option>
                            </select>
                        </div>
                    </div>
                </section>
                <!-- Assets Details Section -->
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-6 bg-secondary rounded-full"></span>
                        <h3 class="text-secondary font-bold uppercase tracking-wider text-[12px]">Assets Details</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Asset Size Unit <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Unit</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Asset Size <span
                                    class="text-error">*</span></label>
                            <input class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                placeholder="Enter size" type="number" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Asset Number <span
                                    class="text-error">*</span></label>
                            <input class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                placeholder="Enter number" type="text" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Floor Status <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Status</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Floor <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option>Select Floor</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Current Area
                                (Optional)</label>
                            <input class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                placeholder="Area details" type="text" />
                        </div>
                    </div>
                </section>
            </div>
            <!-- Modal Footer -->
            <div
                class="bg-surface-container-low p-6 border-t border-outline-variant flex items-center justify-end gap-3 shrink-0">
                <button
                    class="px-6 py-2.5 rounded-lg font-bold text-primary hover:bg-surface-container-high transition-colors"
                    onclick="closeModal()">Cancel</button>
                <button
                    class="px-8 py-2.5 bg-primary text-on-primary rounded-lg font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">Register
                    Asset</button>
            </div>
        </div>
    </div>
    <!-- PDF FAB -->

@endsection
