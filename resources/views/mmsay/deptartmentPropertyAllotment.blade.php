@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Property Registration')
@section('content')
    <style>
        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper svg {
            width: 18px;
            height: 18px;
        }

        .pagination-wrapper span,
        .pagination-wrapper a {
            font-size: 14px;
        }
    </style>
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">
            <div class="px-0 pb-1 flex flezx-col lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <h3 class="text-xl font-medium text-primary mb-0.5">
                        Property Allotee Details
                    </h3>
                    <p class="text-xs text-gray-500 font-normal">
                        Monitor of allotee details.
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
            {{-- <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4 shadow-sm">

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
            </div> --}}
            <!-- High Density Data Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                <!-- Table -->
                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1000px]">

                        <thead class="bg-slate-50">
                            <tr class="text-[11px] uppercase tracking-wider text-slate-500">

                                <th class="px-4 py-4 text-left font-semibold">
                                    District
                                </th>

                                <th class="px-4 py-4 text-left font-semibold">
                                    Asset Name
                                </th>

                                <th class="px-4 py-4 text-left font-semibold">
                                    Purchaser
                                </th>

                                <th class="px-4 py-4 text-left font-semibold">
                                    Mobile
                                </th>

                                <th class="px-4 py-4 font-semibold">
                                    Balance
                                </th>

                                <th class="px-4 py-4  font-semibold">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @foreach ($properties as $property)
                                <tr class="hover:bg-slate-50 transition">

                                    <td class="px-4 py-4 font-medium text-slate-700">
                                        {{ $property->district }}
                                    </td>

                                    <td class="px-4 py-4 font-medium text-slate-800">
                                        {{ Str::limit($property->AssetName, 30) }}
                                    </td>

                                    <td class="px-4 py-4">
                                        {{ $property->PrivatePurchaserName }}
                                    </td>

                                    <td class="px-4 py-4">
                                        {{ $property->MobileNo }}
                                    </td>

                                    <td class="px-4 py-4 text-right">
                                        <span
                                            class="inline-flex px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                            ₹{{ number_format($property->BalanceAmount, 2) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-center">

                                        <div class="flex items-center justify-center gap-2">

                                            <!-- View Button -->
                                            <button
                                                onclick="openPropertyModal(
                '{{ $property->district }}',
                '{{ $property->city }}',
                '{{ $property->sector }}',
                '{{ $property->AssetId }}',
                '{{ $property->AssetName }}',
                '{{ $property->AssetSize }}',
                '{{ $property->PrivatePurchaserName }}',
                '{{ $property->MobileNo }}',
                '{{ $property->ApplicationNo }}',
                '{{ $property->FlatCost }}',
                '{{ $property->ReceivedAmount }}',
                '{{ $property->BalanceAmount }}'
            )"
                                                class="inline-flex items-center gap-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">

                                                <span class="material-symbols-outlined text-[18px]">
                                                    visibility
                                                </span>

                                                View

                                            </button>

                                            <!-- Allotment Letter Button -->
                                            @if ($property->BalanceAmount < 100000)
                                                <a href="{{ route('allotment.letter', $property->PropertyAuctionId) }}"
                                                    class="inline-flex items-center gap-1 px-3 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100"
                                                    target="_blank">

                                                    <span class="material-symbols-outlined text-[18px]">
                                                        description
                                                    </span>

                                                    Allotment Letter
                                                </a>
                                            @endif

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>


                </div>
                <div class="mt-6">
                    <div
                        class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white border border-slate-200 rounded-2xl shadow-sm px-6 py-4">
                        <div class="text-sm text-slate-600">
                        </div>

                        <div class="pagination-wrapper">
                            {{ $properties->links() }}
                        </div>

                    </div>

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
                                <label class="font-label-sm text-on-surface-variant">Plot Cost (In RS.)*</label>
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

    <div id="propertyModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl mx-4">

            <div class="flex items-center justify-between p-6 border-b">

                <h3 class="text-xl font-bold text-slate-800">
                    Property Details
                </h3>

                <button onclick="closePropertyModal()">
                    <span class="material-symbols-outlined">
                        close
                    </span>
                </button>

            </div>

            <div class="p-6">

                <div class="grid grid-cols-2 md:grid-cols-3 gap-5">

                    <div>
                        <p class="text-xs text-slate-500">District</p>
                        <p id="mDistrict" class="font-semibold"></p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">City</p>
                        <p id="mCity" class="font-semibold"></p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Sector</p>
                        <p id="mSector" class="font-semibold"></p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Asset ID</p>
                        <p id="mAssetId" class="font-semibold"></p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Asset Name</p>
                        <p id="mAssetName" class="font-semibold"></p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Asset Size</p>
                        <p id="mAssetSize" class="font-semibold"></p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Purchaser</p>
                        <p id="mPurchaser" class="font-semibold"></p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Mobile</p>
                        <p id="mMobile" class="font-semibold"></p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Application No</p>
                        <p id="mApplication" class="font-semibold"></p>
                    </div>

                </div>

                <div class="grid grid-cols-3 gap-4 mt-8">

                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-slate-500">Plot Cost</p>
                        <h4 id="mFlatCost" class="font-bold text-blue-600"></h4>
                    </div>

                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-slate-500">Received</p>
                        <h4 id="mReceived" class="font-bold text-green-600"></h4>
                    </div>

                    <div class="bg-red-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-slate-500">Balance</p>
                        <h4 id="mBalance" class="font-bold text-red-600"></h4>
                    </div>

                </div>

            </div>

        </div>

    </div>
    <script>
        function openPropertyModal(
            district,
            city,
            sector,
            assetId,
            assetName,
            assetSize,
            purchaser,
            mobile,
            application,
            flatCost,
            received,
            balance
        ) {

            document.getElementById('mDistrict').innerText = district;
            document.getElementById('mCity').innerText = city;
            document.getElementById('mSector').innerText = sector;
            document.getElementById('mAssetId').innerText = assetId;
            document.getElementById('mAssetName').innerText = assetName;
            document.getElementById('mAssetSize').innerText = assetSize;
            document.getElementById('mPurchaser').innerText = purchaser;
            document.getElementById('mMobile').innerText = mobile;
            document.getElementById('mApplication').innerText = application;

            document.getElementById('mFlatCost').innerText =
                '₹ ' + Number(flatCost).toLocaleString();

            document.getElementById('mReceived').innerText =
                '₹ ' + Number(received).toLocaleString();

            document.getElementById('mBalance').innerText =
                '₹ ' + Number(balance).toLocaleString();

            document.getElementById('propertyModal')
                .classList.remove('hidden');

            document.getElementById('propertyModal')
                .classList.add('flex');
        }

        function closePropertyModal() {

            document.getElementById('propertyModal')
                .classList.remove('flex');

            document.getElementById('propertyModal')
                .classList.add('hidden');
        }
    </script>
@endsection
