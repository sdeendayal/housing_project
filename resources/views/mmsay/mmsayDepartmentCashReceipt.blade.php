@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Property Registration')
@section('content')
    <main class="ml-64 min-h-screen flex flex-col">
        <div class="pt-20 px-4 pb-4 space-y-4 flex-1">
            <!-- Header Section -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-primary tracking-tight">
                        Cash Receipt
                    </h2>
                </div>
            </div>

            <!-- Table Container -->
            <div class="glass-card rounded-lg shadow-sm border border-outline-variant overflow-hidden">

                <!-- Table Header -->
                <div
                    class="px-4 py-3 border-b border-outline-variant flex items-center justify-between flex-wrap gap-2 bg-surface-container-lowest">

                    <div class="mb-4 flex items-center justify-between border-b pb-3">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Cash Receipt Details
                        </h2>
                    </div>

                </div>



                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1400px] text-sm">

                        <thead>
                            <tr
                                class="bg-surface-container-low text-[10px] uppercase tracking-wide text-on-surface-variant font-semibold border-b border-outline-variant">

                                <th class="px-3 py-2">Id</th>
                                <th class="px-3 py-2">Estate Manager Office</th>
                                <th class="px-3 py-2">District Office</th>
                                <th class="px-3 py-2">City Office</th>
                                <th class="px-3 py-2">Sector</th>
                                <th class="px-3 py-2">Asset Number</th>
                                <th class="px-3 py-2">Payment Date</th>
                                <th class="px-3 py-2">Payment Type</th>
                                <th class="px-3 py-2">Impact On</th>
                                <th class="px-3 py-2">Receipt Number</th>
                                <th class="px-3 py-2 text-right">GST Amount</th>
                                <th class="px-3 py-2 text-right">Total Paid Amount</th>
                                <th class="px-3 py-2 text-center">Actions</th>

                            </tr>
                        </thead>

                        <tbody>
                            @forelse($receipts as $receipt)
                                <tr class="border-b border-outline-variant hover:bg-gray-50">

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
                                        Cash
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
                                        ₹ {{ number_format($receipt->total_paid_amount, 2) }}
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <a href="#" class="text-blue-600 hover:underline">
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
                <div class="mt-4">
                    {{ $receipts->links() }}
                </div>

                <!-- Pagination -->

            </div>
        </div>
    </main>


@endsection
