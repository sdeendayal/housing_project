@if ($installmentStats['total'] > 0)
    {{-- Installment stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3">
        <div class="rounded-lg border border-slate-100 bg-slate-50 p-2 text-center">
            <p class="text-[9px] text-slate-400 uppercase font-bold mb-0.5">Total Installments</p>
            <p class="text-[14px] font-extrabold text-slate-800">{{ $installmentStats['total'] }}</p>
        </div>
        <div class="rounded-lg border border-emerald-100 bg-emerald-50/60 p-2 text-center">
            <p class="text-[9px] text-emerald-700/80 uppercase font-bold mb-0.5">Paid</p>
            <p class="text-[14px] font-extrabold text-emerald-700">{{ $installmentStats['paid'] }}</p>
        </div>
        <div class="rounded-lg border border-red-100 bg-red-50/60 p-2 text-center">
            <p class="text-[9px] text-red-700/80 uppercase font-bold mb-0.5">Overdue</p>
            <p class="text-[14px] font-extrabold text-red-600">{{ $installmentStats['overdue'] }}</p>
        </div>
        <div class="rounded-lg border border-indigo-100 bg-indigo-50/60 p-2 text-center">
            <p class="text-[9px] text-indigo-700/80 uppercase font-bold mb-0.5">Balance</p>
            <p class="text-[14px] font-extrabold text-indigo-700">{{ $installmentStats['upcoming'] }}</p>
        </div>
    </div>

    {{-- Installment schedule --}}
    <div class="mt-3 rounded-lg border border-slate-100 overflow-hidden">
        <div class="px-2.5 py-1.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-2">
            <p class="text-[9px] font-bold uppercase text-slate-500 m-0">Installment Schedule</p>
            <p class="text-[9px] text-slate-400 m-0">Due date · amount · status</p>
        </div>
        <div class="overflow-x-auto max-h-[320px] overflow-y-auto">
            <table class="w-full text-[10px]">
                <thead class="bg-white sticky top-0 z-10 border-b border-slate-100">
                    <tr>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">#</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Due Date</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">EMI</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500 hidden sm:table-cell">Principal</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500 hidden md:table-cell">Interest</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Total Due</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500 hidden lg:table-cell">Paid On</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($installments as $inst)
                    @php
                        $statusClass = match($inst->status) {
                            'paid' => 'bg-emerald-100 text-emerald-700',
                            'overdue' => 'bg-red-100 text-red-700',
                            'partial' => 'bg-amber-100 text-amber-700',
                            default => 'bg-indigo-100 text-indigo-700',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-2 py-1.5 font-bold text-slate-800">{{ $inst->installment_number }}</td>
                        <td class="px-2 py-1.5 text-slate-700 whitespace-nowrap">{{ $inst->due_date_formatted }}</td>
                        <td class="px-2 py-1.5 font-semibold text-slate-800 whitespace-nowrap">{{ $inst->emi_formatted }}</td>
                        <td class="px-2 py-1.5 text-slate-600 hidden sm:table-cell">{{ $inst->principal > 0 ? '₹ '.number_format($inst->principal) : '—' }}</td>
                        <td class="px-2 py-1.5 text-slate-600 hidden md:table-cell">{{ $inst->interest > 0 ? '₹ '.number_format($inst->interest) : '—' }}</td>
                        <td class="px-2 py-1.5 font-semibold text-slate-800 whitespace-nowrap">{{ $inst->total_due_formatted }}</td>
                        <td class="px-2 py-1.5 text-slate-600 hidden lg:table-cell whitespace-nowrap">{{ $inst->paid_on_formatted }}</td>
                        <td class="px-2 py-1.5">
                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $statusClass }}">{{ $inst->status_label }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payment receipts --}}
    @if ($paymentReceipts->isNotEmpty())
    @php
        $receiptsScrollable = $paymentReceipts->count() > 5;
    @endphp
    <div class="mt-3 rounded-lg border border-slate-100 overflow-hidden">
        <div class="px-2.5 py-1.5 bg-slate-50 border-b border-slate-100">
            <p class="text-[9px] font-bold uppercase text-slate-500 m-0">Payment History (Receipts)</p>
        </div>
        <div @class([
            'overflow-x-auto',
            'max-h-[11.25rem] overflow-y-auto' => $receiptsScrollable,
        ])>
            <table class="w-full text-[10px]">
                <thead @class([
                    'bg-white border-b border-slate-100',
                    'sticky top-0 z-10' => $receiptsScrollable,
                ])>
                    <tr>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">#</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Receipt No.</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Date</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Amount</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500 hidden sm:table-cell">Mode</th>
                        <th class="px-2 py-1.5 text-center font-bold text-slate-500">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($paymentReceipts as $receipt)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-2 py-1.5 font-bold text-slate-800">{{ $loop->iteration }}</td>
                        <td class="px-2 py-1.5 font-bold text-indigo-700 break-all">{{ $receipt->receipt_number }}</td>
                        <td class="px-2 py-1.5 text-slate-700 whitespace-nowrap">{{ $receipt->date_formatted }}</td>
                        <td class="px-2 py-1.5 font-semibold text-emerald-700 whitespace-nowrap">{{ $receipt->amount_formatted }}</td>
                        <td class="px-2 py-1.5 text-slate-600 hidden sm:table-cell">{{ $receipt->mode }}</td>
                        <td class="px-2 py-1.5 text-center whitespace-nowrap">
                            <a href="{{ route('citizen.cash-receipt.download', $receipt->id) }}"
                                class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded border border-indigo-200 bg-indigo-50 text-[9px] font-bold text-indigo-700 no-underline hover:bg-indigo-100"
                                title="Download cash receipt">
                                <span class="material-symbols-outlined text-[12px]">download</span>
                                PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Online Payment Attempts --}}
    @if (isset($onlineTransactions) && $onlineTransactions->isNotEmpty())
    @php
        $txScrollable = $onlineTransactions->count() > 5;
    @endphp
    <div class="mt-4 rounded-lg border border-slate-100 overflow-hidden">
        <div class="px-2.5 py-1.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-2">
            <p class="text-[9px] font-bold uppercase text-slate-500 m-0">Online Transaction Logs (Gateway Attempts)</p>
            <p class="text-[9px] text-slate-400 m-0">Track payments, status checks, and failures</p>
        </div>
        <div @class([
            'overflow-x-auto',
            'max-h-[11.25rem] overflow-y-auto' => $txScrollable,
        ])>
            <table class="w-full text-[10px]">
                <thead @class([
                    'bg-white border-b border-slate-100',
                    'sticky top-0 z-10' => $txScrollable,
                ])>
                    <tr>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Ref. Number</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Date & Time</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Amount</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Gateway Tx ID</th>
                        <th class="px-2 py-1.5 text-left font-bold text-slate-500">Gateway Status</th>
                        <th class="px-2 py-1.5 text-center font-bold text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($onlineTransactions as $tx)
                    @php
                        $statusClass = match($tx->status) {
                            'SUCCESS' => 'bg-emerald-100 text-emerald-700',
                            'FAIL' => 'bg-red-100 text-red-700',
                            default => 'bg-amber-100 text-amber-700', // PENDING
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-2 py-1.5 font-bold text-slate-800 break-all">{{ $tx->merchant_txn_no }}</td>
                        <td class="px-2 py-1.5 text-slate-600 whitespace-nowrap">
                            {{ $tx->created_at ? \Carbon\Carbon::parse($tx->created_at)->format('d M Y h:i A') : '—' }}
                        </td>
                        <td class="px-2 py-1.5 font-bold text-slate-800">₹ {{ number_format($tx->amount, 2) }}</td>
                        <td class="px-2 py-1.5 text-slate-600 font-semibold">{{ $tx->gateway_txn_id ?: ($tx->payment_id ?: '—') }}</td>
                        <td class="px-2 py-1.5 whitespace-nowrap">
                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $statusClass }}">
                                {{ $tx->status }}
                            </span>
                            @if ($tx->response_description && $tx->status !== 'SUCCESS')
                                <p class="text-[8px] text-slate-400 m-0 mt-0.5 break-all max-w-[150px] leading-tight">
                                    {{ $tx->response_description }}
                                </p>
                            @endif
                        </td>
                        <td class="px-2 py-1.5 text-center whitespace-nowrap">
                            @if ($tx->status === 'PENDING')
                                <a href="{{ route('citizen.payment.reconcile', $tx->id) }}"
                                    class="inline-flex items-center gap-0.5 px-2 py-1 rounded bg-indigo-600 text-[9px] font-bold text-white no-underline hover:bg-indigo-700"
                                    title="Verify Status with Gateway">
                                    <span class="material-symbols-outlined text-[10px]">sync</span>
                                    Verify
                                </a>
                            @else
                                <span class="text-slate-400 font-bold">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@else
    <p class="text-[10px] text-slate-500 m-0 mt-3">No installment records found for your allotted property.</p>
@endif
