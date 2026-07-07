@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Payment Processing',
    'activeNav' => 'payments',
    'displayName' => $displayName,
    'applicationId' => $applicationId,
])

@push('styles')
<style>
    .pg-result-wrap { max-width: 520px; margin: 0 auto; }
    .pg-result-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
    }
    .pg-result-top {
        background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%);
        color: #fff;
        padding: 14px 18px;
        text-align: center;
    }
    .pg-step {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        opacity: .4;
        transition: opacity .3s ease;
    }
    .pg-step.active { opacity: 1; background: #f8fafc; }
    .pg-step.done { opacity: 1; }
    .pg-step-icon {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #e2e8f0;
        color: #64748b;
        flex-shrink: 0;
    }
    .pg-step.active .pg-step-icon {
        background: #eef2ff;
        color: #4f46e5;
        animation: pgPulse 1s infinite;
    }
    .pg-step.done .pg-step-icon {
        background: #d1fae5;
        color: #059669;
    }
    @keyframes pgPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(79,70,229,.4); }
        50% { box-shadow: 0 0 0 8px rgba(79,70,229,0); }
    }
    .pg-spinner {
        width: 18px; height: 18px;
        border: 2px solid #c7d2fe;
        border-top-color: #4f46e5;
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="pg-result-wrap">
    <div class="pg-result-card" id="processingCard">
        <div class="pg-result-top">
            <span class="material-symbols-outlined text-[28px] mb-1">account_balance</span>
            <p class="text-[12px] font-extrabold m-0">MMSAY Payment Gateway</p>
            <p class="text-[9px] text-white/70 m-0 mt-0.5">Transaction ID: {{ $payment['txn_id'] }}</p>
        </div>

        <div id="stepsContainer">
            <div class="pg-step active" id="step1">
                <div class="pg-step-icon"><div class="pg-spinner"></div></div>
                <div>
                    <p class="text-[11px] font-bold text-slate-800 m-0">Connecting to Payment Gateway</p>
                    <p class="text-[9px] text-slate-500 m-0">Establishing secure connection...</p>
                </div>
            </div>
            <div class="pg-step" id="step2">
                <div class="pg-step-icon"><span class="material-symbols-outlined text-[18px]">pin</span></div>
                <div>
                    <p class="text-[11px] font-bold text-slate-800 m-0">Verifying Payment Details</p>
                    <p class="text-[9px] text-slate-500 m-0">Validating {{ $payment['mode'] }} transaction...</p>
                </div>
            </div>
            <div class="pg-step" id="step3">
                <div class="pg-step-icon"><span class="material-symbols-outlined text-[18px]">sync</span></div>
                <div>
                    <p class="text-[11px] font-bold text-slate-800 m-0">Processing Payment</p>
                    <p class="text-[9px] text-slate-500 m-0">Amount: {{ $payment['amount'] }} · Please do not refresh</p>
                </div>
            </div>
            <div class="pg-step" id="step4">
                <div class="pg-step-icon"><span class="material-symbols-outlined text-[18px]">receipt_long</span></div>
                <div>
                    <p class="text-[11px] font-bold text-slate-800 m-0">Generating Receipt</p>
                    <p class="text-[9px] text-slate-500 m-0">Finalizing transaction record...</p>
                </div>
            </div>
        </div>

        <div class="p-4 text-center border-t border-slate-100">
            <p class="text-[10px] text-slate-400 m-0">
                <span class="material-symbols-outlined text-[12px] align-middle">lock</span>
                Secured 256-bit encrypted transaction
            </p>
        </div>
    </div>

    <div class="pg-result-card hidden mt-3" id="finalResult">
        <div class="p-5 sm:p-6 text-center">
            @if ($payment['status'] === 'SUCCESS')
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[36px]">check_circle</span>
                </div>
                <h2 class="text-base font-extrabold text-slate-800 m-0 mb-1">Payment Successful!</h2>
                <p class="text-[11px] text-slate-500 m-0 mb-4">Your transaction has been processed successfully.</p>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-left text-[10px] space-y-2 mb-4">
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Transaction ID</span>
                        <span class="font-bold text-indigo-700">{{ $payment['txn_id'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Order ID</span>
                        <span class="font-bold text-slate-800">{{ $payment['order_id'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Amount Paid</span>
                        <span class="font-bold text-emerald-700">{{ $payment['amount'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Payment Mode</span>
                        <span class="font-bold text-slate-800">{{ $payment['mode'] }}</span>
                    </div>
                    @if (isset($payment['date']))
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Date & Time</span>
                        <span class="font-bold text-slate-800">{{ $payment['date'] }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Status</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[9px]">SUCCESS</span>
                    </div>
                </div>

                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 mb-4">
                    <p class="text-[11px] font-bold text-emerald-800 m-0">
                        Cash Receipt has been automatically generated. You can download it from your dashboard.
                    </p>
                </div>
            @elseif ($payment['status'] === 'FAIL')
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[36px]">cancel</span>
                </div>
                <h2 class="text-base font-extrabold text-slate-800 m-0 mb-1">Payment Failed!</h2>
                <p class="text-[11px] text-slate-500 m-0 mb-4">{{ $payment['message'] ?? 'Transaction was declined by the gateway.' }}</p>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-left text-[10px] space-y-2 mb-4">
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Transaction ID</span>
                        <span class="font-bold text-indigo-700">{{ $payment['txn_id'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Order ID</span>
                        <span class="font-bold text-slate-800">{{ $payment['order_id'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Amount</span>
                        <span class="font-bold text-slate-800">{{ $payment['amount'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Payment Mode</span>
                        <span class="font-bold text-slate-800">{{ $payment['mode'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Status</span>
                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-bold text-[9px]">FAILED</span>
                    </div>
                </div>

                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 mb-4">
                    <p class="text-[11px] font-bold text-red-800 m-0">
                        Please try making the payment again or choose another payment method.
                    </p>
                </div>
            @else
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[36px]">hourglass_top</span>
                </div>
                <h2 class="text-base font-extrabold text-slate-800 m-0 mb-1">Payment Gateway Integration Under Process</h2>
                <p class="text-[11px] text-slate-500 m-0 mb-4">Online Payment Facility Will Be Available Soon</p>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-left text-[10px] space-y-2 mb-4">
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Transaction ID</span>
                        <span class="font-bold text-indigo-700">{{ $payment['txn_id'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Order ID</span>
                        <span class="font-bold text-slate-800">{{ $payment['order_id'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Amount</span>
                        <span class="font-bold text-emerald-700">{{ $payment['amount'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Payment Mode</span>
                        <span class="font-bold text-slate-800">{{ $payment['mode'] }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Status</span>
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-bold text-[9px]">RECORDED — PENDING GATEWAY</span>
                    </div>
                </div>

                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 mb-4">
                    <p class="text-[11px] font-bold text-emerald-800 m-0">
                        Your payment request has been recorded successfully.
                    </p>
                </div>

                <p class="text-[9px] text-slate-400 m-0 mb-4">Demo transaction — no real amount deducted.</p>
            @endif

            <div class="flex flex-wrap justify-center gap-2">
                @if ($payment['status'] === 'SUCCESS')
                    <a href="{{ route('citizen.dashboard') }}" class="btn-v2-primary inline-flex items-center gap-1.5 px-6 py-2.5 text-[12px] font-extrabold no-underline shadow-lg shadow-indigo-200">
                        <span class="material-symbols-outlined text-[16px]">dashboard</span>
                        Back to Dashboard
                    </a>
                @elseif ($payment['status'] === 'FAIL')
                    <a href="{{ route('citizen.payment.pay') }}" class="btn-v2-primary inline-flex items-center gap-1.5 px-4 py-2 text-[11px] no-underline">
                        <span class="material-symbols-outlined text-[16px]">replay</span>
                        Retry Payment
                    </a>
                    <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg text-[11px] font-bold text-slate-600 no-underline hover:bg-slate-100">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('citizen.payment-status') }}" class="btn-v2-primary inline-flex items-center gap-1.5 px-4 py-2 text-[11px] no-underline">
                        Back to Payment
                    </a>
                    <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg text-[11px] font-bold text-slate-600 no-underline hover:bg-slate-100">
                        Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const steps = ['step1', 'step2', 'step3', 'step4'];
    let current = 0;

    function markDone(id) {
        const el = document.getElementById(id);
        el.classList.remove('active');
        el.classList.add('done');
        el.querySelector('.pg-step-icon').innerHTML = '<span class="material-symbols-outlined text-[18px]">check</span>';
    }

    function activateStep(id) {
        document.getElementById(id).classList.add('active');
        const icon = document.getElementById(id).querySelector('.pg-step-icon');
        icon.innerHTML = '<div class="pg-spinner"></div>';
    }

    const interval = setInterval(function () {
        if (current > 0) markDone(steps[current - 1]);
        if (current < steps.length) {
            activateStep(steps[current]);
            current++;
        } else {
            clearInterval(interval);
            setTimeout(function () {
                markDone(steps[steps.length - 1]);
                document.getElementById('processingCard').classList.add('hidden');
                document.getElementById('finalResult').classList.remove('hidden');

                @if ($payment['status'] === 'SUCCESS')
                    setTimeout(function() {
                        Swal.fire({
                            title: 'Payment Successful!',
                            text: 'Your transaction has been processed successfully and the receipt is generated. You can now review your receipt details below.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4f46e5',
                            allowOutsideClick: true,
                            allowEscapeKey: true
                        });
                    }, 300);
                @endif
            }, 800);
        }
    }, 1200);
})();
</script>
@endpush
