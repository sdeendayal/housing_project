@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY - EMI Pending Details')
@section('content')
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">
            <div class="bg-white rounded-xl shadow">

                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-bold">
                        Pending Properties
                    </h2>

                    <span class="text-sm text-gray-500">
                        Total : {{ $properties->total() }}
                    </span>
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-100">

                            <tr>
                                <th class="p-3 text-left">Asset ID</th>
                                <th class="p-3 text-left">Application No</th>
                                <th class="p-3 text-left">Applicant Name</th>
                                <th class="p-3 text-left">Mobile</th>
                                <th class="p-3 text-left">Property</th>
                                <th class="p-3 text-left">Flat Cost</th>
                                <th class="p-3 text-left">Paid</th>
                                <th class="p-3 text-left">Pending</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($properties as $row)
                                @php
                                    $pending = $row->FlatCost - $row->total_paid;
                                @endphp

                                <tr class="border-t">

                                    <td class="p-3">
                                        {{ $row->AssetId }}
                                    </td>

                                    <td class="p-3">
                                        {{ $row->ApplicationNo }}
                                    </td>

                                    <td class="p-3">
                                        {{ $row->PrivatePurchaserName }}
                                    </td>

                                    <td class="p-3">
                                        {{ $row->MobileNo }}
                                    </td>

                                    <td class="p-3">
                                        {{ $row->AssetName }}
                                    </td>

                                    <td class="p-3">
                                        ₹{{ number_format($row->FlatCost, 2) }}
                                    </td>

                                    <td class="p-3 text-green-600 font-semibold">
                                        ₹{{ number_format($row->total_paid, 2) }}
                                    </td>

                                    <td class="p-3 text-red-600 font-semibold">
                                        ₹{{ number_format($pending, 2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="p-4 text-center text-gray-500">
                                        No pending properties found.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="p-4 border-t">
                    {{ $properties->links() }}
                </div>

            </div>
        </div>
    </main>
@endsection
