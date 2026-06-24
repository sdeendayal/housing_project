@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Dashboard')
@section('content')


    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">
            <div class="max-w-7xl mx-auto p-6">

                {{-- Header --}}
                <div class="mb-6">

                    <h1 class="text-2xl font-bold text-gray-800">
                        Property Payment Dashboard
                    </h1>

                    <p class="text-gray-500 text-sm">
                        Asset ID : {{ $details->AssetId }}
                    </p>

                </div>

                {{-- Payment Alert --}}
                @if ($outstanding > 0)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6">

                        <div class="flex items-start gap-3">

                            <span class="material-symbols-outlined text-red-600">
                                warning
                            </span>

                            <div>

                                <h3 class="font-semibold text-red-700">
                                    Payment Pending
                                </h3>

                                <p class="text-red-600 text-sm mt-1">
                                    Your full payment has not been completed yet.
                                    Please complete your remaining balance.
                                </p>

                                <div class="grid md:grid-cols-3 gap-4 mt-4">

                                    <div>
                                        <p class="text-xs text-gray-500">
                                            Total Due
                                        </p>

                                        <p class="font-bold">
                                            ₹{{ number_format($details->FlatCost, 2) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">
                                            Paid
                                        </p>

                                        <p class="font-bold text-green-600">
                                            ₹{{ number_format($totalPaid, 2) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">
                                            Remaining
                                        </p>

                                        <p class="font-bold text-red-600">
                                            ₹{{ number_format($outstanding, 2) }}
                                        </p>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                @endif



                {{-- Applicant + Property --}}
                <div class="grid lg:grid-cols-2 gap-6 mb-6">

                    <div class="bg-white rounded-xl shadow p-5">

                        <h3 class="font-semibold text-lg mb-4">
                            Applicant Details
                        </h3>

                        <div class="space-y-3">

                            <div>
                                <span class="text-gray-500">
                                    Name
                                </span>
                                <p class="font-medium">
                                    {{ $details->PrivatePurchaserName }}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-500">
                                    Mobile
                                </span>
                                <p>
                                    {{ $details->MobileNo }}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-500">
                                    Application No
                                </span>
                                <p>
                                    {{ $details->ApplicationNo }}
                                </p>
                            </div>

                        </div>

                    </div>

                    <div class="bg-white rounded-xl shadow p-5">

                        <h3 class="font-semibold text-lg mb-4">
                            Property Details
                        </h3>

                        <div class="space-y-3">

                            <div>
                                <span class="text-gray-500">
                                    Property
                                </span>
                                <p>
                                    {{ $details->AssetName }}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-500">
                                    Size
                                </span>
                                <p>
                                    {{ $details->AssetSize }}
                                    {{ $details->Unit }}
                                </p>
                            </div>

                            <div>
                                <span class="text-gray-500">
                                    Status
                                </span>

                                <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs">
                                    Allotted
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Summary Cards --}}
                <div class="grid md:grid-cols-4 gap-5 mb-6">

                    <div class="bg-white shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">
                            Total Amount
                        </p>
                        <h3 class="text-2xl font-bold">
                            ₹{{ number_format($property->FlatCost, 2) }}
                        </h3>
                    </div>

                    <div class="bg-white shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">
                            Total Paid
                        </p>
                        <h3 class="text-2xl font-bold text-green-600">
                            ₹{{ number_format($totalPaid, 2) }}
                        </h3>
                    </div>

                    <div class="bg-white shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">
                            Outstanding
                        </p>
                        <h3 class="text-2xl font-bold text-red-600">
                            ₹{{ number_format($remainingAmount, 2) }}
                        </h3>
                    </div>

                    <div class="bg-white shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">
                            Completed
                        </p>
                        <h3 class="text-2xl font-bold text-blue-600">
                            {{ $completionPercent }}%
                        </h3>
                    </div>

                </div>

                {{-- Installment Statistics --}}
                <div class="grid md:grid-cols-4 gap-5 mb-6">

                    <div class="bg-white rounded-xl shadow p-5">
                        <h4 class="text-3xl font-bold">
                            {{ $installments->count() }}
                        </h4>
                        <p>Total Installments</p>
                    </div>

                    <div class="bg-white rounded-xl shadow p-5">
                        <h4 class="text-3xl font-bold text-green-600">
                            {{ $paidInstallments }}
                        </h4>
                        <p>Paid</p>
                    </div>

                    <div class="bg-white rounded-xl shadow p-5">
                        <h4 class="text-3xl font-bold text-red-600">
                            {{ $overdueInstallments }}
                        </h4>
                        <p>Overdue</p>
                    </div>

                    <div class="bg-white rounded-xl shadow p-5">
                        <h4 class="text-3xl font-bold text-yellow-600">
                            {{ $upcomingInstallments }}
                        </h4>
                        <p>Balance</p>
                    </div>

                </div>

                {{-- EMI Schedule --}}
                <div class="bg-white rounded-xl shadow mb-6">

                    <div class="p-5 border-b">
                        <h3 class="font-semibold">
                            Installment Schedule
                        </h3>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="p-3 text-left">#</th>
                                    <th class="p-3 text-left">Due Date</th>
                                    <th class="p-3 text-left">EMI</th>
                                    <th class="p-3 text-left">Principal</th>
                                    <th class="p-3 text-left">Interest</th>
                                    <th class="p-3 text-left">Total Due</th>
                                    <th class="p-3 text-left">Receipt No.</th>
                                    <th class="p-3 text-left">Paid On</th>
                                    <th class="p-3 text-left">Status</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($installments as $emi)
                                    <tr class="border-t">

                                        <td class="p-3">
                                            {{ $emi->InstallmentNumber }}
                                        </td>

                                        <td class="p-3">
                                            {{ \Carbon\Carbon::parse($emi->DueDate)->format('d M Y') }}
                                        </td>

                                        <td class="p-3">
                                            ₹{{ number_format($emi->EMIAmount) }}
                                        </td>

                                        <td class="p-3">
                                            ₹{{ number_format($emi->PrincipleAmount) }}
                                        </td>

                                        <td class="p-3">
                                            ₹{{ number_format($emi->InterestAmount) }}
                                        </td>

                                        <td class="p-3">
                                            ₹{{ number_format($emi->DueAmount) }}
                                        </td>

                                        <td class="p-3">
                                            {{ $emi->receipt_number ?? '-' }}
                                        </td>

                                        <td class="p-3">
                                            {{ $emi->PaidOn != '-' ? \Carbon\Carbon::parse($emi->PaidOn)->format('d M Y') : '-' }}
                                        </td>

                                        <td class="p-3">

                                            @if ($emi->status == 'Paid')
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                                                    Paid
                                                </span>
                                            @elseif($emi->status == 'Partially Paid')
                                                <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">
                                                    Partially Paid
                                                </span>
                                            @elseif($emi->status == 'Overdue')
                                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">
                                                    Overdue
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">
                                                    Upcoming
                                                </span>
                                            @endif

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>
    </main>
@endsection
