@extends('layouts.mmsayDepartmentAuth')

@section('title', 'Property Details')

@section('content')
    <main class="ml-52 min-h-screen bg-slate-50 px-5 pb-8 pt-20">
        <div class="mx-auto max-w-container-max space-y-5">

            {{-- Header --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-100">
                        <span class="material-symbols-outlined text-[19px]">
                            arrow_back
                        </span>
                    </a>

                    <div>
                        <h1 class="text-xl font-bold text-slate-900">
                            Property Statement
                        </h1>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Asset #{{ $property->AssetId }} · {{ $property->AssetName }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('properties.print', $property->AssetId) }}" target="_blank"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-slate-800 px-4 text-xs font-semibold text-white transition hover:bg-slate-900">
                    <span class="material-symbols-outlined text-[17px]">
                        print
                    </span>
                    Print Statement
                </a>
            </div>

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Property Cost
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-800">
                        ₹{{ number_format($flatCost ?? 0, 2) }}
                    </p>
                </div>

                <div class="rounded-xl border border-indigo-100 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Initial Received
                    </p>

                    <p class="mt-2 text-2xl font-bold text-indigo-600">
                        ₹{{ number_format($openingReceivedAmount ?? 0, 2) }}
                    </p>
                </div>

                <div class="rounded-xl border border-emerald-100 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Total Received
                    </p>

                    <p class="mt-2 text-2xl font-bold text-emerald-600">
                        ₹{{ number_format($totalReceived ?? 0, 2) }}
                    </p>

                    <p class="mt-1 text-[10px] text-slate-400">
                        Cash receipts: ₹{{ number_format($receiptTotal ?? 0, 2) }}
                    </p>
                </div>

                <div class="rounded-xl border border-rose-100 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Pending Amount
                    </p>

                    <p class="mt-2 text-2xl font-bold {{ ($pendingAmount ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        ₹{{ number_format($pendingAmount ?? 0, 2) }}
                    </p>

                    @if (($excessAmount ?? 0) > 0)
                        <p class="mt-1 text-[10px] font-medium text-violet-600">
                            Excess: ₹{{ number_format($excessAmount, 2) }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Common information --}}
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-3">
                        <span class="material-symbols-outlined text-[19px] text-indigo-600">
                            apartment
                        </span>

                        <h2 class="text-sm font-semibold text-slate-800">
                            Property Information
                        </h2>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 p-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Asset ID</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-800">#{{ $property->AssetId }}</dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Asset Name</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-700">{{ $property->AssetName ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Size</dt>
                            <dd class="mt-1 text-sm text-slate-700">
                                {{ $property->AssetSize ?? '-' }} {{ $property->Unit ?? '' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Auction ID</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->PropertyAuctionId ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">District</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->DistrictName ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">City</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->CityName ?? '-' }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Sector</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->SectorName ?? '-' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-3">
                        <span class="material-symbols-outlined text-[19px] text-emerald-600">
                            person
                        </span>

                        <h2 class="text-sm font-semibold text-slate-800">
                            Purchaser Information
                        </h2>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 p-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Purchaser</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $property->PrivatePurchaserName ?? 'Not allotted' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Father Name</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->PurchaserFatherName ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Mobile</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->MobileNo ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Application</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->ApplicationNo ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">PPP ID</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->PPPId ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Member ID</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $property->MemberID ?? '-' }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Address</dt>
                            <dd class="mt-1 text-sm leading-5 text-slate-700">{{ $property->Address ?? '-' }}</dd>
                        </div>
                    </dl>
                </section>
            </div>

            {{-- EMI summary --}}
            <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm">
                    <p class="text-[10px] font-semibold uppercase text-slate-400">Total EMI</p>
                    <p class="mt-1 text-xl font-bold text-slate-800">{{ number_format($totalEmiCount ?? 0) }}</p>
                    <p class="mt-1 text-[9px] text-slate-400">
                        Schedule: ₹{{ number_format($schedulePayableTotal ?? 0, 2) }}
                    </p>
                </div>

                <div class="rounded-xl border border-emerald-100 bg-white p-3 text-center shadow-sm">
                    <p class="text-[10px] font-semibold uppercase text-slate-400">Paid EMI</p>
                    <p class="mt-1 text-xl font-bold text-emerald-600">{{ number_format($paidEmiCount ?? 0) }}</p>
                </div>

                <div class="rounded-xl border border-amber-100 bg-white p-3 text-center shadow-sm">
                    <p class="text-[10px] font-semibold uppercase text-slate-400">Partial EMI</p>
                    <p class="mt-1 text-xl font-bold text-amber-600">{{ number_format($partiallyPaidEmiCount ?? 0) }}</p>
                </div>

                <div class="rounded-xl border border-rose-100 bg-white p-3 text-center shadow-sm">
                    <p class="text-[10px] font-semibold uppercase text-slate-400">Pending EMI</p>
                    <p class="mt-1 text-xl font-bold text-rose-600">{{ number_format($pendingEmiCount ?? 0) }}</p>
                    <p class="mt-1 text-[9px] font-medium text-rose-500">
                        ₹{{ number_format($emiPendingAmount ?? 0, 2) }}
                    </p>
                </div>
            </div>

            {{-- Cash receipts --}}
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[19px] text-emerald-600">receipt_long</span>
                        <h2 class="text-sm font-semibold text-slate-800">Cash Receipts</h2>
                    </div>

                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold text-emerald-600">
                        {{ number_format($cashReceipts->count()) }} receipts
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left text-xs">
                        <thead class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">S. No.</th>
                                <th class="px-4 py-3">Receipt Number</th>
                                <th class="px-4 py-3">Deposit Date</th>
                                <th class="px-4 py-3 text-right">Amount</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($cashReceipts as $receipt)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-slate-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-700">
                                        {{ $receipt->receipt_number ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $receipt->created_date
                                            ? \Carbon\Carbon::parse($receipt->created_date)->format('d-m-Y')
                                            : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-emerald-600">
                                        ₹{{ number_format($receipt->total_paid_amount ?? 0, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-400">
                                        No cash receipts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if ($cashReceipts->isNotEmpty())
                            <tfoot class="border-t border-slate-200 bg-slate-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-xs font-semibold text-slate-600">
                                        Cash Receipt Total
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-emerald-600">
                                        ₹{{ number_format($receiptTotal ?? 0, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </section>

            {{-- EMI details --}}
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50">
                            <span class="material-symbols-outlined text-[19px] text-amber-600">
                                payments
                            </span>
                        </div>

                        <div>
                            <h2 class="text-sm font-semibold text-slate-800">
                                EMI Payment Schedule
                            </h2>

                            <p class="mt-0.5 text-[10px] text-slate-400">
                                Actual receipt date and receipt-number information
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold text-emerald-600">
                            {{ number_format($paidEmiCount ?? 0) }} Paid
                        </span>

                        @if (($partiallyPaidEmiCount ?? 0) > 0)
                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-semibold text-amber-600">
                                {{ number_format($partiallyPaidEmiCount) }} Partial
                            </span>
                        @endif

                        <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-semibold text-rose-600">
                            {{ number_format($pendingEmiCount ?? 0) }} Pending
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1250px] text-left text-xs">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-3">EMI</th>
                                <th class="px-3 py-3">Due Date</th>
                                <th class="px-3 py-3 text-right">Payable</th>
                                <th class="px-3 py-3 text-right">Principal</th>
                                <th class="px-3 py-3 text-right">Interest</th>
                                <th class="px-3 py-3 text-right">GST</th>
                                <th class="px-3 py-3 text-right">Allocated Payment</th>
                                <th class="px-3 py-3 text-right">Pending</th>
                                <th class="px-3 py-3">Cleared On</th>
                                <th class="px-3 py-3">Receipt Allocation</th>
                                <th class="px-3 py-3 text-center">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($emiDetails as $emi)
                                @php
                                    $allocatedPayment = (float) ($emi->allocated_payment ?? 0);
                                    $emiPending = (float) ($emi->installment_pending ?? 0);
                                    $status = $emi->display_status ?? ($emi->payment_status ?? 'pending');
                                    $statusLabel = match ($status) {
                                        'advance_paid' => 'Advance Paid',
                                        'paid' => 'Paid',
                                        'partial' => 'Partial',
                                        default => 'Pending',
                                    };
                                @endphp

                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-3 py-3">
                                        <span class="inline-flex h-7 min-w-7 items-center justify-center rounded-lg bg-slate-100 px-2 text-[11px] font-bold text-slate-700">
                                            {{ $emi->InstallmentNumber }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-3 text-slate-600">
                                        {{ $emi->DueDate
                                            ? \Carbon\Carbon::parse($emi->DueDate)->format('d-m-Y')
                                            : '-' }}
                                    </td>

                                    <td class="px-3 py-3 text-right font-semibold text-slate-700">
                                        ₹{{ number_format($emi->installment_payable ?? 0, 2) }}
                                    </td>

                                    <td class="px-3 py-3 text-right text-slate-600">
                                        ₹{{ number_format($emi->PrincipleAmount ?? 0, 2) }}
                                    </td>

                                    <td class="px-3 py-3 text-right text-slate-600">
                                        ₹{{ number_format($emi->InterestAmount ?? 0, 2) }}
                                    </td>

                                    <td class="px-3 py-3 text-right text-slate-600">
                                        ₹{{ number_format($emi->GSTAmount ?? 0, 2) }}
                                    </td>

                                    <td class="px-3 py-3 text-right font-semibold {{ $allocatedPayment > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                        ₹{{ number_format($allocatedPayment, 2) }}
                                    </td>

                                    <td class="px-3 py-3 text-right font-semibold {{ $emiPending > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                        ₹{{ number_format($emiPending, 2) }}
                                    </td>

                                    <td class="px-3 py-3 text-slate-600">
                                        {{ $emi->actual_payment_date
                                            ? \Carbon\Carbon::parse($emi->actual_payment_date)->format('d-m-Y')
                                            : '-' }}
                                    </td>

                                    <td class="max-w-[300px] px-3 py-3">
                                        @if (!empty($emi->receipt_allocations))
                                            <div class="space-y-1">
                                                @foreach ($emi->receipt_allocations as $allocation)
                                                    <div class="rounded-md bg-slate-50 px-2 py-1 text-[9px] leading-4 text-slate-600">
                                                        <span class="font-medium">
                                                            {{ $allocation['receipt_number'] ?: '-' }}
                                                        </span>

                                                        <span class="text-slate-400">·</span>

                                                        <span class="font-semibold text-emerald-600">
                                                            ₹{{ number_format($allocation['allocated_amount'], 2) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3 text-center">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[9px] font-semibold
                                            {{ $status === 'paid'
                                                ? 'bg-emerald-50 text-emerald-600'
                                                : ($status === 'advance_paid'
                                                    ? 'bg-indigo-50 text-indigo-600'
                                                    : ($status === 'partial'
                                                        ? 'bg-amber-50 text-amber-600'
                                                        : 'bg-rose-50 text-rose-600')) }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-12 text-center text-sm text-slate-400">
                                        No EMI records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
@endsection