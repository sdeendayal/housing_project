@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Payment',
    'activeNav' => 'payments',
    'displayName' => $displayName,
    'applicationId' => $applicationId,
])

@section('content')
    {{-- Pay Now hero — top section only, existing dashboard below unchanged --}}
    <div class="pay-hero mb-3">
        <div class="pay-hero__glow"></div>
        <div class="pay-hero__inner">
            <div class="pay-hero__left">
                <div class="pay-hero__badge">
                    <span class="material-symbols-outlined text-[14px]">bolt</span>
                    Quick Online Payment
                </div>
                <h2 class="pay-hero__title">Pay Your Next Installment</h2>
                <p class="pay-hero__sub">Secure payment via UPI, Cards &amp; Net Banking</p>
                <div class="pay-hero__amount-box">
                    <p class="pay-hero__amount-label">Amount Due Now</p>
                    <p class="pay-hero__amount">{{ $nextInstallmentAmount }}</p>
                    <p class="pay-hero__due">Due on {{ $nextInstallmentDue }}</p>
                </div>
                <div class="pay-hero__modes">
                    <span class="pay-hero__mode"><span class="material-symbols-outlined text-[14px]">qr_code_2</span> UPI</span>
                    <span class="pay-hero__mode"><span class="material-symbols-outlined text-[14px]">credit_card</span> Cards</span>
                    <span class="pay-hero__mode"><span class="material-symbols-outlined text-[14px]">account_balance</span> Net Banking</span>
                </div>
            </div>
            <div class="pay-hero__right">
                <div class="pay-hero__card-mock">
                    <div class="pay-hero__card-chip"></div>
                    <p class="pay-hero__card-num">•••• •••• •••• 4242</p>
                    <p class="pay-hero__card-meta">MMSAY SECURE PAY</p>
                </div>
                <a href="{{ route('citizen.payment.pay') }}" class="pay-hero__btn">
                    <span class="material-symbols-outlined text-[22px]">payments</span>
                    Pay Now
                </a>
                <p class="pay-hero__secure">
                    <span class="material-symbols-outlined text-[12px]">lock</span>
                    256-bit SSL · Demo Gateway
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 mb-3 flex items-start gap-2">
        <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0">info</span>
        <p class="text-[10px] text-amber-800 m-0 leading-relaxed">
            <strong>Demo Mode:</strong> This is a preview payment screen for UI demonstration only. No real transaction will be processed.
        </p>
    </div>

    <div class="citizen-card overflow-hidden mb-3">
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-3 py-3 flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-[22px]">account_balance_wallet</span>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-white m-0">Payment Dashboard</h2>
                    <p class="text-[10px] text-white/70 m-0">{{ $applicationId }}</p>
                </div>
            </div>
            <a href="{{ route('citizen.payment.pay') }}" class="btn-v2-primary inline-flex items-center gap-1.5 px-4 py-2 text-[11px] no-underline !bg-white !text-indigo-700 hover:!bg-indigo-50">
                <span class="material-symbols-outlined text-[16px]">payments</span>
                Pay Now
            </a>
        </div>

        <div class="p-3">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-3">
                <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">home</span>
                        </span>
                        <p class="text-[9px] text-slate-500 uppercase font-bold m-0">Total Property Cost</p>
                    </div>
                    <p class="text-[15px] font-extrabold text-slate-800 m-0">{{ $totalPropertyCost }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-3 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        </span>
                        <p class="text-[9px] text-emerald-700 uppercase font-bold m-0">Amount Paid</p>
                    </div>
                    <p class="text-[15px] font-extrabold text-emerald-700 m-0">{{ $amountPaid }}</p>
                </div>
                <div class="rounded-xl border border-red-200 bg-red-50/50 p-3 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">pending</span>
                        </span>
                        <p class="text-[9px] text-red-700 uppercase font-bold m-0">Outstanding Balance</p>
                    </div>
                    <p class="text-[15px] font-extrabold text-red-600 m-0">{{ $outstandingBalance }}</p>
                </div>
                <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-3 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">event</span>
                        </span>
                        <p class="text-[9px] text-indigo-700 uppercase font-bold m-0">Next Installment Due</p>
                    </div>
                    <p class="text-[13px] font-extrabold text-indigo-700 m-0">{{ $nextInstallmentDue }}</p>
                    <p class="text-[11px] font-bold text-indigo-600 m-0 mt-0.5">{{ $nextInstallmentAmount }}</p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 mb-3">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[10px] font-bold text-slate-600 uppercase">Payment Progress</span>
                    <span class="text-[12px] font-extrabold text-indigo-600">{{ $paymentProgress }}%</span>
                </div>
                <div class="prog-bar h-2.5 rounded-full overflow-hidden bg-slate-200">
                    <div class="prog-fill h-full rounded-full" style="width:{{ $paymentProgress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h3 class="text-[11px] font-extrabold text-slate-800 m-0">Payment History (Demo)</h3>
        </div>
        <div class="p-3 overflow-x-auto">
            <table class="w-full text-[10px] text-left min-w-[600px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-2 py-1.5 font-bold text-slate-500">#</th>
                        <th class="px-2 py-1.5 font-bold text-slate-500">Transaction ID</th>
                        <th class="px-2 py-1.5 font-bold text-slate-500">Date</th>
                        <th class="px-2 py-1.5 font-bold text-slate-500">Amount</th>
                        <th class="px-2 py-1.5 font-bold text-slate-500">Payment Mode</th>
                        <th class="px-2 py-1.5 font-bold text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($paymentHistory as $row)
                        @php
                            $statusClass = match ($row['status']) {
                                'Processing' => 'bg-indigo-100 text-indigo-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-2 py-1.5 font-bold text-slate-800">{{ $loop->iteration }}</td>
                            <td class="px-2 py-1.5 font-bold text-indigo-700">{{ $row['transaction_id'] }}</td>
                            <td class="px-2 py-1.5 text-slate-700">{{ $row['date'] }}</td>
                            <td class="px-2 py-1.5 font-semibold text-emerald-700">{{ $row['amount'] }}</td>
                            <td class="px-2 py-1.5 text-slate-700">{{ $row['mode'] }}</td>
                            <td class="px-2 py-1.5">
                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold {{ $statusClass }}">{{ $row['status'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .pay-hero {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 45%, #4c1d95 100%);
        box-shadow: 0 16px 48px rgba(49, 46, 129, 0.35);
    }
    .pay-hero__glow {
        position: absolute;
        top: -40%;
        right: -10%;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(129, 140, 248, 0.45) 0%, transparent 70%);
        pointer-events: none;
    }
    .pay-hero__inner {
        position: relative;
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 18px 20px;
    }
    @media (min-width: 768px) {
        .pay-hero__inner {
            grid-template-columns: 1.2fr 1fr;
            align-items: center;
            padding: 22px 24px;
        }
    }
    .pay-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #e0e7ff;
        font-size: 10px;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 999px;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .pay-hero__title {
        font-size: 20px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 4px;
        line-height: 1.2;
    }
    .pay-hero__sub {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.65);
        margin: 0 0 14px;
    }
    .pay-hero__amount-box {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 12px;
        max-width: 220px;
    }
    .pay-hero__amount-label {
        font-size: 9px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.55);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0 0 2px;
    }
    .pay-hero__amount {
        font-size: 26px;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.1;
    }
    .pay-hero__due {
        font-size: 10px;
        color: #c4b5fd;
        font-weight: 600;
        margin: 4px 0 0;
    }
    .pay-hero__modes {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .pay-hero__mode {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 700;
        color: #e0e7ff;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 5px 10px;
    }
    .pay-hero__right {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }
    .pay-hero__card-mock {
        width: 100%;
        max-width: 260px;
        background: linear-gradient(135deg, #0f172a, #334155);
        border-radius: 14px;
        padding: 18px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }
    .pay-hero__card-chip {
        width: 38px;
        height: 28px;
        background: linear-gradient(135deg, #fbbf24, #d97706);
        border-radius: 5px;
        margin-bottom: 18px;
    }
    .pay-hero__card-num {
        font-family: ui-monospace, monospace;
        font-size: 15px;
        color: #fff;
        letter-spacing: 0.12em;
        margin: 0 0 8px;
    }
    .pay-hero__card-meta {
        font-size: 9px;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.45);
        letter-spacing: 0.15em;
        margin: 0;
    }
    .pay-hero__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        max-width: 260px;
        padding: 16px 24px;
        background: linear-gradient(135deg, #fff 0%, #eef2ff 100%);
        color: #4338ca;
        font-size: 16px;
        font-weight: 800;
        border-radius: 14px;
        text-decoration: none;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.3);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .pay-hero__btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
        color: #3730a3;
    }
    .pay-hero__secure {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 9px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.45);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endpush
