@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Secure Payment',
    'activeNav' => 'payments',
    'displayName' => $displayName,
    'applicationId' => $applicationId,
])

@push('styles')
<style>
    .pg-wrap { max-width: 860px; margin: 0 auto; }
    .pg-gateway {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
    }
    .pg-topbar {
        background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%);
        color: #fff;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .pg-timer {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
    }
    .pg-body { display: grid; grid-template-columns: 1fr; }
    @media (min-width: 768px) {
        .pg-body { grid-template-columns: 320px 1fr; }
    }
    .pg-summary {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 20px;
    }
    @media (min-width: 768px) {
        .pg-summary { border-bottom: none; border-right: 1px solid #e2e8f0; }
    }
    .pg-methods { padding: 24px; }
    .pg-field {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12px;
        outline: none;
    }
    .pg-field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
    .pg-secure {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
        padding: 14px 18px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        font-size: 9px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .pg-pay-btn {
        width: 100%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 14px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
        box-shadow: 0 8px 20px rgba(79,70,229,.35);
        transition: all 0.2s ease;
    }
    .pg-pay-btn:hover { opacity: .95; transform: translateY(-1px); }
</style>
@endpush

@section('content')
<div class="pg-wrap">
    <div class="mb-3 flex items-center justify-between gap-2 flex-wrap">
        <a href="{{ route('citizen.payment-status') }}" class="text-[10px] font-bold text-slate-500 no-underline hover:text-indigo-600 inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">arrow_back</span> Cancel & Go Back
        </a>
        <span class="text-[9px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full">SECURE GATEWAY</span>
    </div>

    @if(session('error'))
        <div class="mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs font-bold">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('citizen.payment.pay.submit') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="applicant_name" value="{{ $applicantName }}">
        <input type="hidden" name="asset_id" value="{{ $assetId }}">

        <div class="pg-gateway">
            <div class="pg-topbar">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px]">verified_user</span>
                    <div>
                        <p class="text-[11px] font-extrabold m-0">MMSAY Secure Payment</p>
                        <p class="text-[9px] text-white/70 m-0">Housing For All · Govt. of Haryana</p>
                    </div>
                </div>
                <div class="pg-timer">
                    <span class="material-symbols-outlined text-[12px] align-middle">timer</span>
                    Session expires in <span id="pgCountdown">10:00</span>
                </div>
            </div>

            <div class="pg-body">
                {{-- Order Summary --}}
                <div class="pg-summary">
                    <p class="text-[9px] font-bold text-slate-400 uppercase m-0 mb-1">Transaction Ref.</p>
                    <p class="text-[10px] text-slate-600 m-0 mb-4 break-all">{{ $merchantOrderId }}</p>
                    
                    <p class="text-[9px] font-bold text-slate-400 uppercase m-0 mb-3">Applicant Details</p>
                    <div class="space-y-3 text-[10px]">
                        <div class="flex justify-between gap-2 border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500">Applicant Name</span>
                            <span class="font-bold text-slate-800 text-right">{{ $applicantName }}</span>
                        </div>
                        <div class="flex justify-between gap-2 border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500">Plot/Flat Number</span>
                            <span class="font-bold text-slate-800">{{ $plotNumber }}</span>
                        </div>
                        <div class="flex justify-between gap-2 border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500">Application ID</span>
                            <span class="font-bold text-slate-800 text-right break-all">{{ $applicationId }}</span>
                        </div>
                        <div class="flex justify-between gap-2 border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500">Registered Mobile</span>
                            <span class="font-bold text-slate-800">{{ $mobile }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-slate-500">Email Address</span>
                            <span class="font-bold text-slate-800">{{ $email }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Execution Column --}}
                <div class="pg-methods flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800 m-0 mb-1">Confirm Payment Details</h3>
                        <p class="text-[10px] text-slate-500 m-0 mb-4">Review the details on the left, set your desired amount, and proceed to the payment page.</p>

                        <div class="mb-4">
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1.5 font-sans">
                                Enter Amount to Pay (₹)
                                @if ($isLastInstallment)
                                    <span class="text-[9px] text-indigo-700 font-extrabold lowercase bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full ml-1">FROZEN (36th Last Installment)</span>
                                @endif
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-extrabold text-[15px]">₹</span>
                                <input type="number" name="amount_raw" id="amountInput" 
                                    class="w-full pl-8 pr-3 py-2.5 border rounded-lg font-extrabold text-[15px] outline-none {{ $isLastInstallment ? 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed' : 'bg-white border-slate-300 text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500' }}" 
                                    value="{{ $amountRaw }}" 
                                    min="1" 
                                    max="{{ $maxAllowedAmount }}" 
                                    step="0.01" 
                                    {{ $isLastInstallment ? 'readonly' : '' }} 
                                    required>
                            </div>
                            @if ($isLastInstallment)
                                <p class="text-[9px] text-indigo-700 font-bold mt-1.5">This is the 36th (last) installment. The amount is fixed at ₹{{ number_format($maxAllowedAmount, 2) }} to reach the ₹1,00,000 maximum total payment limit.</p>
                            @else
                                <p class="text-[9px] text-slate-400 mt-1.5">You can pay any custom amount up to ₹{{ number_format($maxAllowedAmount, 2) }} to stay within the ₹1,00,000 limit.</p>
                            @endif
                        </div>


                    </div>

                    <div>
                        <button type="submit" class="pg-pay-btn">
                            <span class="material-symbols-outlined text-[18px]">security</span>
                            Proceed to Pay ₹ <span id="btnAmount">{{ $amountToPay }}</span>
                        </button>
                        <p class="text-[9px] text-slate-400 text-center mt-2.5">
                            You will be redirected to the secure page of <strong>Phicommerce</strong> to choose UPI, Net Banking, or Cards.
                        </p>
                    </div>
                </div>
            </div>

            <div class="pg-secure">
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">lock</span> 256-bit SSL encryption</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">verified</span> PCI DSS Compliant</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">shield</span> Secured by MMSAY Pay Gateway</span>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const amountInput = document.getElementById('amountInput');
    const btnAmount = document.getElementById('btnAmount');
    if (amountInput && btnAmount) {
        amountInput.addEventListener('input', function () {
            const val = parseFloat(amountInput.value) || 0;
            btnAmount.textContent = val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    }

    let seconds = 600;
    const countdownEl = document.getElementById('pgCountdown');
    const interval = setInterval(function () {
        if (seconds <= 0) {
            clearInterval(interval);
            return;
        }
        seconds--;
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        if (countdownEl) countdownEl.textContent = m + ':' + s;
    }, 1000);
})();
</script>
@endpush
