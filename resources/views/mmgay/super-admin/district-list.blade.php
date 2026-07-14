@extends('layouts.mmgayAdmin')

@section('title', 'District List - Super Admin')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">

        <!-- PAGE CARD -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b bg-gradient-to-r from-slate-50 to-gray-100">

                <div>
                    <h2 class="text-2xl font-bold text-gray-800">District List</h2>
                    <p class="text-sm text-gray-500 mt-1">All registered districts in system</p>
                </div>

                <div class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-semibold text-sm shadow-sm">
                    Total: {{ count($data) }}
                </div>

            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-blue-600 text-white text-xs uppercase">
                        <tr>
                            <th class="p-3 text-left">District</th>
                            <th class="p-3 text-center">Villages</th>
                            <th class="p-3 text-center">Registered Beneficiaries</th>
                            <th class="p-3 text-center">Allotted</th>
                            <th class="p-3 text-center">Approved & Paid</th>
                            <th class="p-3 text-center">Approved & Unpaid</th>
                            <th class="p-3 text-center">Pending</th>
                            <th class="p-3 text-center">Rejected</th>
                            <th class="p-3 text-center">Cancelled</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse($data as $d)
                            <tr class="hover:bg-gray-50">

                                <td class="p-3 font-semibold">
                                    {{ $d->DistrictName }}
                                </td>

                                <td class="text-center">
                                    {{ number_format($d->VillagesWithPlots) }}
                                </td>

                                <td class="text-center">
                                    {{ number_format($d->RegisteredBeneficiaries) }}
                                </td>

                                <td class="text-center font-semibold text-blue-600">
                                    {{ number_format($d->AllottedBeneficiaries) }}
                                </td>

                                <td class="text-center text-green-600 font-semibold">
                                    {{ number_format($d->ApprovedPaid) }}
                                </td>

                                <td class="text-center text-yellow-600 font-semibold">
                                    {{ number_format($d->ApprovedUnpaid) }}
                                </td>

                                <td class="text-center text-orange-600 font-semibold">
                                    {{ number_format($d->PendingApprovalPayment) }}
                                </td>

                                <td class="text-center text-red-600 font-semibold">
                                    {{ number_format($d->Rejected) }}
                                </td>

                                <td class="text-center text-gray-700 font-semibold">
                                    {{ number_format($d->AllotmentCancelled) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="9" class="p-8 text-center text-gray-500">
                                    No Records Found
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                    <tfoot class="bg-gray-100 font-bold border-t-2">
                        <tr>

                            <td class="p-3">Gross Total</td>

                            <td class="text-center">
                                {{ number_format($grossTotal->VillagesWithPlots) }}
                            </td>

                            <td class="text-center">
                                {{ number_format($grossTotal->RegisteredBeneficiaries) }}
                            </td>

                            <td class="text-center">
                                {{ number_format($grossTotal->AllottedBeneficiaries) }}
                            </td>

                            <td class="text-center text-green-600">
                                {{ number_format($grossTotal->ApprovedPaid) }}
                            </td>

                            <td class="text-center text-yellow-600">
                                {{ number_format($grossTotal->ApprovedUnpaid) }}
                            </td>

                            <td class="text-center text-orange-600">
                                {{ number_format($grossTotal->PendingApprovalPayment) }}
                            </td>

                            <td class="text-center text-red-600">
                                {{ number_format($grossTotal->Rejected) }}
                            </td>

                            <td class="text-center text-gray-700">
                                {{ number_format($grossTotal->AllotmentCancelled) }}
                            </td>

                        </tr>
                    </tfoot>

                </table>

            </div>

        </div>

    </main>

@endsection
