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

                    <div class="mb-4 flex items-center justify-between border-b pb-3">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Assets List Details
                        </h2>
                    </div>
                    <form method="GET" class="flex flex-wrap items-center justify-end gap-3 mb-4">

                        <!-- EM OFFICE -->
                        <select name="em_office" id="emOffice"
                            class="h-11 min-w-[220px] rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="">EM Office</option>
                            @foreach ($emOffices as $em)
                                <option value="{{ $em->BranchName }}">{{ $em->BranchName }}</option>
                            @endforeach
                        </select>

                        <!-- DISTRICT -->
                        <select name="district" id="district"
                            class="h-11 min-w-[150px] rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="">District</option>
                        </select>

                        <!-- CITY -->
                        <select name="city" id="city"
                            class="h-11 min-w-[150px] rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="">City</option>
                        </select>

                        <!-- SECTOR -->
                        <select name="sector" id="sector"
                            class="h-11 min-w-[150px] rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="">Sector</option>
                        </select>

                        <!-- FILTER BUTTON -->
                        <button type="submit"
                            class="h-11 rounded-lg bg-blue-600 px-5 text-sm font-medium text-white transition hover:bg-blue-700">
                            Filter
                        </button>

                        <!-- EXPORT BUTTON -->
                        <a href="{{ route('properties.export', request()->all()) }}"
                            class="flex h-11 items-center rounded-lg bg-green-600 px-5 text-sm font-medium text-white transition hover:bg-green-700">
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
        <div class="bg-white w-full max-w-6xl rounded-xl shadow-2xl border border-outline-variant overflow-hidden transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-full"
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
                            <select id="formEmOffice" name="BranchId"
                                class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option value="">Select EM Office</option>

                                @foreach ($emOffices as $em)
                                    <option value="{{ $em->BranchName }}">
                                        {{ $em->BranchName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">District Office <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                id="formDistrict" name="DistrictId">
                                <option value="">Select District</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">City Office <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                id="formCity" name="CityId">
                                <option value="">Select City</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Sector <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                id="formSector" name="SectorId">
                                <option value="">Select Sector</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Scheme <span
                                    class="text-error">*</span></label>
                            <select name="scheme" id="scheme"
                                class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option value="">HBH</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Location Status <span
                                    class="text-error">*</span></label>
                            <select name="location_status" id="location_status"
                                class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option value="">Select Status</option>
                                <option value="Yes">Yes</option>
                                <option value="Preference">Preference</option>
                                <option value="Normal">Normal</option>
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
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                name="propertType">
                                <option value="">Select Property Type</option>
                                <option value="Residential">Residential</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Property Category
                                <span class="text-error">*</span></label>
                            <select name="property_category" id="property_category"
                                class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">

                                <option value="">Select Property Category</option>

                                <option value="2MIGA">2MIGA</option>
                                <option value="A1TYP">A1TYP</option>
                                <option value="A2TYP">A2TYP</option>
                                <option value="ALIG2">ALIG2</option>
                                <option value="ASHOP">ASHOP</option>
                                <option value="ATYPE">ATYPE</option>
                                <option value="AUHIG">AUHIG</option>
                                <option value="AULII">AULII</option>
                                <option value="B1TYP">B1TYP</option>
                                <option value="B2TYP">B2TYP</option>
                                <option value="B3TYP">B3TYP</option>

                                <option value="Backword Classes Other">Backword Classes Other</option>
                                <option value="Backword Classes Women">Backword Classes Women</option>
                                <option value="BPL">BPL</option>

                                <option value="Commercial">Commercial</option>
                                <option value="EWS">EWS</option>
                                <option value="EWS BPL Other">EWS BPL Other</option>

                                <option value="General Category Other">General Category Other</option>
                                <option value="General Category Women">General Category Women</option>

                                <option value="Handicap and blind person women">
                                    Handicap and blind person women
                                </option>

                                <option value="Handicap and Blind Person-Other">
                                    Handicap and Blind Person-Other
                                </option>

                                <option value="HIG">HIG</option>
                                <option value="HIG_I">HIG_I</option>
                                <option value="HIG2">HIG2</option>
                                <option value="HIG-A">HIG-A</option>
                                <option value="HIG-L">HIG-L</option>
                                <option value="HIG-U">HIG-U</option>

                                <option value="Housing Board Haryana employee others">
                                    Housing Board Haryana employee others
                                </option>

                                <option value="Housing Board Haryana employee Women">
                                    Housing Board Haryana employee Women
                                </option>

                                <option value="LIG">LIG</option>
                                <option value="LIG-A">LIG-A</option>
                                <option value="LIG-I">LIG-I</option>
                                <option value="LIG-2">LIG-2</option>

                                <option value="MIG">MIG</option>
                                <option value="MIG-A">MIG-A</option>
                                <option value="MIG-B">MIG-B</option>
                                <option value="MIG-L">MIG-L</option>
                                <option value="MIG-U">MIG-U</option>

                                <option value="Old Person/Senior Citizens -Other">
                                    Old Person/Senior Citizens -Other
                                </option>

                                <option value="Old Person/Senior Citizens -Women">
                                    Old Person/Senior Citizens -Women
                                </option>

                                <option value="PLOT">PLOT</option>

                                <option value="Police Force Kill in Action-Other">
                                    Police Force Kill in Action-Other
                                </option>

                                <option value="Residential">Residential</option>

                                <option value="Retired Haryana Govt. Employee Other">
                                    Retired Haryana Govt. Employee Other
                                </option>

                                <option value="Scheduled Caste Other">
                                    Scheduled Caste Other
                                </option>

                                <option value="Scheduled Caste Women">
                                    Scheduled Caste Women
                                </option>

                                <option value="Serving/Ex-Defence and Para Military as Haryana">
                                    Serving/Ex-Defence and Para Military as Haryana
                                </option>

                                <option value="SHOP">SHOP</option>
                                <option value="SHOPA">SHOPA</option>
                                <option value="SHOPP">SHOPP</option>

                                <option value="War Widow Disable Soldier Other">
                                    War Widow Disable Soldier Other
                                </option>

                                <option value="Widow Other">Widow Other</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Property Status <span
                                    class="text-error">*</span></label>
                            <select name="category" id="category"
                                class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option value="">Select Category</option>
                                <option value="Surrender">Surrender</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Auction">Auction</option>
                                <option value="DRAW">DRAW</option>
                                <option value="PMAY">PMAY</option>
                                <option value="E-Auction">E-Auction</option>
                                <option value="Direct">Direct</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Category Details Section -->
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-6 bg-secondary rounded-full"></span>
                        <h3 class="text-secondary font-bold uppercase tracking-wider text-[12px]">
                            Category Details
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Applicant Type -->
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">
                                Applicant Type <span class="text-error">*</span>
                            </label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option value="">Select Applicant Type</option>
                                <option value="General">General</option>
                            </select>
                        </div>

                        <!-- Original Reservation Category -->
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">
                                Original Reservation Category <span class="text-error">*</span>
                            </label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option value="">Select Category</option>
                                <option value="2MIGA">2MIGA</option>
                                <option value="A1TYP">A1TYP</option>
                                <option value="A2TYP">A2TYP</option>
                                <option value="ALIG2">ALIG2</option>
                                <option value="ASHOP">ASHOP</option>
                                <option value="ATYPE">ATYPE</option>
                                <option value="AUHIG">AUHIG</option>
                                <option value="AULII">AULII</option>
                                <option value="B1TYP">B1TYP</option>
                                <option value="B2TYP">B2TYP</option>
                                <option value="B3TYP">B3TYP</option>
                                <option value="Backword Classes Other">Backword Classes Other</option>
                                <option value="Backword Classes Women">Backword Classes Women</option>
                                <option value="BPL">BPL</option>
                                <option value="C1TYP">C1TYP</option>
                                <option value="C2IIT">C2IIT</option>
                                <option value="C2ITY">C2ITY</option>
                                <option value="Commercial">Commercial</option>
                                <option value="COMSH">COMSH</option>
                                <option value="COREU">COREU</option>
                                <option value="DDYQ">DDYQ</option>
                                <option value="DIQY">DIQY</option>
                                <option value="DRII">DRII</option>
                                <option value="DTQY">DTQY</option>
                                <option value="DTYPE">DTYPE</option>
                                <option value="E1TYP">E1TYP</option>
                                <option value="E2TYP">E2TYP</option>
                                <option value="E3TYP">E3TYP</option>
                                <option value="EWS">EWS</option>
                                <option value="EWS BPL Other">EWS BPL Other</option>
                                <option value="EWSA">EWSA</option>
                                <option value="EWSBP">EWSBP</option>
                                <option value="EWSEH">EWSEH</option>
                                <option value="EWSHY">EWSHY</option>
                                <option value="EWSI">EWSI</option>
                                <option value="EWSII">EWSII</option>
                                <option value="EWSM">EWSM</option>
                                <option value="EWSY">EWSY</option>
                                <option value="FTPYE">FTPYE</option>
                                <option value="General Category Other">General Category Other</option>
                                <option value="General Category Women">General Category Women</option>
                                <option value="GIITP">GIITP</option>
                                <option value="GITYP">GITYP</option>
                                <option value="Handicap and blind person women">Handicap and blind person women</option>
                                <option value="Handicap and Blind Person-Other">Handicap and Blind Person-Other</option>
                                <option value="Haryana Retired Gov. employee Women">Haryana Retired Gov. employee Women
                                </option>
                                <option value="Haryana State Employee less than 5 year from retirement Other">Haryana State
                                    Employee less than 5 year from retirement Other</option>
                                <option value="Haryana State Employee less than 5 year from retirement Women">Haryana State
                                    Employee less than 5 year from retirement Women</option>
                                <option value="Haryana State Employee more than 5 year from retirement Other">Haryana State
                                    Employee more than 5 year from retirement Other</option>
                                <option value="HDGQ">HDGQ</option>
                                <option value="HE1-W">HE1-W</option>
                                <option value="HGLAM">HGLAM</option>
                                <option value="HGLAY">HGLAY</option>
                                <option value="HGQI">HGQI</option>
                                <option value="HGUAM">HGUAM</option>
                                <option value="HIG">HIG</option>
                                <option value="HIG_I">HIG_I</option>
                                <option value="HIG2">HIG2</option>
                                <option value="HIGA">HIGA</option>
                                <option value="HIG-A">HIG-A</option>
                                <option value="HIGAH">HIGAH</option>
                                <option value="HIGAL">HIGAL</option>
                                <option value="HIGAM">HIGAM</option>
                                <option value="HIGAU">HIGAU</option>
                                <option value="HIGAY">HIGAY</option>
                                <option value="HIGDI">HIGDI</option>
                                <option value="HIGDL">HIGDL</option>
                                <option value="HIGHY">HIGHY</option>
                                <option value="HIGI">HIGI</option>
                                <option value="HIGII">HIGII</option>
                                <option value="HIG-L">HIG-L</option>
                                <option value="HIGLA">HIGLA</option>
                                <option value="HIGLM">HIGLM</option>
                                <option value="HIGQU">HIGQU</option>
                                <option value="HIGSA">HIGSA</option>
                                <option value="HIGSD">HIGSD</option>
                                <option value="HIG-U">HIG-U</option>
                                <option value="HIGUA">HIGUA</option>
                                <option value="HIGY">HIGY</option>
                            </select>
                        </div>

                        <!-- Current Reservation Category -->
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">
                                Current Reservation Category <span class="text-error">*</span>
                            </label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option value="">Select Category</option>
                                <option value="2MIGA">2MIGA</option>
                                <option value="A1TYP">A1TYP</option>
                                <option value="A2TYP">A2TYP</option>
                                <option value="ALIG2">ALIG2</option>
                                <option value="ASHOP">ASHOP</option>
                                <option value="ATYPE">ATYPE</option>
                                <option value="AUHIG">AUHIG</option>
                                <option value="AULII">AULII</option>
                                <option value="B1TYP">B1TYP</option>
                                <option value="B2TYP">B2TYP</option>
                                <option value="B3TYP">B3TYP</option>
                                <option value="Backword Classes Other">Backword Classes Other</option>
                                <option value="Backword Classes Women">Backword Classes Women</option>
                                <option value="BPL">BPL</option>
                                <option value="C1TYP">C1TYP</option>
                                <option value="C2IIT">C2IIT</option>
                                <option value="C2ITY">C2ITY</option>
                                <option value="Commercial">Commercial</option>
                                <option value="COMSH">COMSH</option>
                                <option value="COREU">COREU</option>
                                <option value="DDYQ">DDYQ</option>
                                <option value="DIQY">DIQY</option>
                                <option value="DRII">DRII</option>
                                <option value="DTQY">DTQY</option>
                                <option value="DTYPE">DTYPE</option>
                                <option value="E1TYP">E1TYP</option>
                                <option value="E2TYP">E2TYP</option>
                                <option value="E3TYP">E3TYP</option>
                                <option value="EWS">EWS</option>
                                <option value="EWS BPL Other">EWS BPL Other</option>
                                <option value="EWSA">EWSA</option>
                                <option value="EWSBP">EWSBP</option>
                                <option value="EWSEH">EWSEH</option>
                                <option value="EWSHY">EWSHY</option>
                                <option value="EWSI">EWSI</option>
                                <option value="EWSII">EWSII</option>
                                <option value="EWSM">EWSM</option>
                                <option value="EWSY">EWSY</option>
                                <option value="FTPYE">FTPYE</option>
                                <option value="General Category Other">General Category Other</option>
                                <option value="General Category Women">General Category Women</option>
                                <option value="GIITP">GIITP</option>
                                <option value="GITYP">GITYP</option>
                                <option value="Handicap and blind person women">Handicap and blind person women</option>
                                <option value="Handicap and Blind Person-Other">Handicap and Blind Person-Other</option>
                                <option value="Haryana Retired Gov. employee Women">Haryana Retired Gov. employee Women
                                </option>
                                <option value="Haryana State Employee less than 5 year from retirement Other">Haryana State
                                    Employee less than 5 year from retirement Other</option>
                                <option value="Haryana State Employee less than 5 year from retirement Women">Haryana State
                                    Employee less than 5 year from retirement Women</option>
                                <option value="Haryana State Employee more than 5 year from retirement Other">Haryana State
                                    Employee more than 5 year from retirement Other</option>
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
                                <option value="">Select Unit</option>
                                <option value="Sq. Yards">Sq. Yards</option>
                                <option value="Sq. Mtr">Sq. Mtr</option>
                                <option value="Sq. feet">Sq. feet</option>
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
                                <option value="">Select Status</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Floor <span
                                    class="text-error">*</span></label>
                            <select class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary">
                                <option value="">Select Floor</option>
                                <option value="1st">1st</option>
                                <option value="2nd">2nd</option>
                                <option value="3rd">3rd</option>
                                <option value="4th">4th</option>
                                <option value="5th">5th</option>
                                <option value="6th">6th</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Current Area
                                (Optional)</label>
                            <input class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                placeholder="Area details" type="text" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">
                                Incidental Area (Corner) (Optional)
                            </label>
                            <input class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                placeholder="Enter incidental area" type="text" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant block">
                                Application Number (Optional)
                            </label>
                            <input class="w-full border-outline-variant rounded-lg bg-white text-sm focus:ring-secondary"
                                placeholder="Enter application number" type="text" />
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
