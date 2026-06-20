@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Property Registration')
@section('content')
    <main class="ml-64 min-h-screen flex flex-col">
        <div class="pt-20 px-4 pb-4 space-y-4 flex-1">
            <!-- Header Section -->


            <!-- Table Container -->
            <div class="rounded-2xl bg-white shadow-lg border border-gray-100 overflow-hidden">

                <!-- Table Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white">
                                Cash Receipt Management
                            </h2>
                            <p class="text-blue-100 text-sm mt-1">
                                View and manage all cash receipt transactions
                            </p>
                        </div>

                        <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                            <span class="text-white text-sm font-medium">
                                Total Records:
                                {{ $receipts->total() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 border-b">
                    <form method="GET" action="{{ url('/cash-receipt') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    EM Office
                                </label>

                                <select id="cashEmOffice" name="em_office"
                                    class="w-full h-11 rounded-xl border border-gray-300 bg-white px-4 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition">

                                    <option value="">All Offices</option>

                                    @foreach ($emOffices as $office)
                                        <option value="{{ $office->BranchName }}"
                                            {{ request('em_office') == $office->BranchName ? 'selected' : '' }}>
                                            {{ $office->BranchName }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    District
                                </label>

                                <select id="cashDistrict" name="district" data-selected="{{ request('district') }}"
                                    class="w-full h-11 rounded-xl border border-gray-300 bg-white px-4 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                    <option value="">All Districts</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    City
                                </label>

                                <select id="cashCity" name="city" data-selected="{{ request('city') }}"
                                    class="w-full h-11 rounded-xl border border-gray-300 bg-white px-4 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                    <option value="">All Cities</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Sector
                                </label>

                                <select id="cashSector" name="sector" data-selected="{{ request('sector') }}"
                                    class="w-full h-11 rounded-xl border border-gray-300 bg-white px-4 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                    <option value="">All Sectors</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full h-11 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-md">

                                    Search
                                </button>
                            </div>

                            <div class="flex items-end">
                                <a href="{{ url('/cash-receipt') }}"
                                    class="w-full h-11 rounded-xl bg-gray-600 text-white font-semibold flex items-center justify-center hover:bg-gray-700 transition shadow-md">

                                    Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>



                <!-- Table -->
                <div class="overflow-x-auto">

                    <table class="w-full text-left border-collapse min-w-[1400px] text-sm">

                        <thead>
                            <tr class="bg-gray-900 text-white text-xs uppercase tracking-wider">

                                <th class="px-4 py-4">ID</th>
                                <th class="px-4 py-4">EM Office</th>
                                <th class="px-4 py-4">District</th>
                                <th class="px-4 py-4">City</th>
                                <th class="px-4 py-4">Sector</th>
                                <th class="px-4 py-4">Asset No.</th>
                                <th class="px-4 py-4">Payment Date</th>
                                <th class="px-4 py-4">Payment Type</th>
                                <th class="px-4 py-4">Impact On</th>
                                <th class="px-4 py-4">Receipt No.</th>
                                <th class="px-4 py-4 text-right">GST</th>
                                <th class="px-4 py-4 text-right">Amount</th>
                                <th class="px-4 py-4 text-center">Action</th>

                            </tr>
                        </thead>

                        <tbody>
                            @forelse($receipts as $receipt)
                                <tr class="border-b border-gray-100 hover:bg-blue-50 transition duration-200">

                                    <td class="px-3 py-2">{{ $receipt->id }}</td>

                                    <td class="px-3 py-2">
                                        {{ $receipt->em_office }}
                                    </td>

                                    <td class="px-3 py-2">
                                        {{ $receipt->district_office }}
                                    </td>

                                    <td class="px-3 py-2">
                                        {{ $receipt->city_office }}
                                    </td>

                                    <td class="px-3 py-2">
                                        {{ $receipt->sector }}
                                    </td>

                                    <td class="px-3 py-2">
                                        {{ $receipt->asset_number }}
                                    </td>

                                    <td class="px-3 py-2">
                                        {{ \Carbon\Carbon::parse($receipt->payment_date)->format('d-m-Y') }}
                                    </td>

                                    <td class="px-3 py-2">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Bank
                                        </span>
                                    </td>

                                    <td class="px-3 py-2">
                                        --
                                    </td>

                                    <td class="px-3 py-2">
                                        {{ $receipt->receipt_number }}
                                    </td>

                                    <td class="px-3 py-2 text-right">
                                        0.00
                                    </td>

                                    <td class="px-3 py-2 text-right">
                                        <span class="font-bold text-green-600">
                                            ₹ {{ number_format($receipt->total_paid_amount, 2) }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <a href="#"
                                            class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">
                                            View
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4">
                                        No records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $receipts->links() }}
                </div>

                <!-- Pagination -->

            </div>
        </div>
    </main>


@endsection
