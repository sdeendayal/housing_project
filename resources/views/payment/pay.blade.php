@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Secure Payment',
    'activeNav' => 'payments',
    'displayName' => $displayName,
    'applicationId' => $applicationId,
])

@push('styles')
<style>
    .pg-wrap { max-width: 920px; margin: 0 auto; }
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
        padding: 12px 16px;
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
        .pg-body { grid-template-columns: 280px 1fr; }
    }
    .pg-summary {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px;
    }
    @media (min-width: 768px) {
        .pg-summary { border-bottom: none; border-right: 1px solid #e2e8f0; }
    }
    .pg-amount {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .pg-methods { padding: 16px; }
    .pg-method-tabs {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 14px;
    }
    @media (min-width: 640px) {
        .pg-method-tabs { grid-template-columns: repeat(4, 1fr); }
    }
    .pg-method-tab {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 8px;
        text-align: center;
        cursor: pointer;
        background: #fff;
        transition: all .15s ease;
    }
    .pg-method-tab:hover { border-color: #a5b4fc; }
    .pg-method-tab.active {
        border-color: #4f46e5;
        background: #eef2ff;
        box-shadow: 0 0 0 1px #4f46e5;
    }
    .pg-method-tab input { display: none; }
    .pg-panel { display: none; }
    .pg-panel.active { display: block; }
    .pg-upi-apps {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .pg-upi-app {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 6px;
        text-align: center;
        font-size: 10px;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        background: #fff;
    }
    .pg-upi-app:hover, .pg-upi-app.selected {
        border-color: #4f46e5;
        background: #eef2ff;
        color: #4338ca;
    }
    .pg-field {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12px;
        outline: none;
    }
    .pg-field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
    .pg-card-preview {
        background: linear-gradient(135deg, #1e293b, #334155);
        border-radius: 12px;
        padding: 16px;
        color: #fff;
        margin-bottom: 12px;
        min-height: 120px;
        position: relative;
    }
    .pg-card-chip {
        width: 36px; height: 26px;
        background: linear-gradient(135deg, #fbbf24, #d97706);
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .pg-secure {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
        padding: 12px 16px;
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
        margin-top: 14px;
        box-shadow: 0 8px 20px rgba(79,70,229,.35);
    }
    .pg-pay-btn:hover { opacity: .95; }
</style>
@endpush

@section('content')
<div class="pg-wrap">
    <div class="mb-2 flex items-center justify-between gap-2 flex-wrap">
        <a href="{{ route('citizen.payment-status') }}" class="text-[10px] font-bold text-slate-500 no-underline hover:text-indigo-600 inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">arrow_back</span> Cancel Payment
        </a>
        <span class="text-[9px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">DEMO GATEWAY</span>
    </div>

    <form action="{{ route('citizen.payment.pay.submit') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="applicant_name" value="{{ $applicantName }}">
        <input type="hidden" name="payment_mode" id="paymentMode" value="UPI">

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
                    Complete in <span id="pgCountdown">10:00</span>
                </div>
            </div>

            <div class="pg-body">
                {{-- Order summary --}}
                <div class="pg-summary">
                    <p class="text-[9px] font-bold text-slate-400 uppercase m-0 mb-1">Order Summary</p>
                    <p class="text-[10px] text-slate-600 m-0 mb-3 break-all">{{ $merchantOrderId }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase m-0 mb-0.5">Amount Payable</p>
                    <p class="pg-amount m-0 mb-3">₹ {{ $amountToPay }}</p>
                    <div class="space-y-2 text-[10px]">
                        <div class="flex justify-between gap-2">
                            <span class="text-slate-500">Applicant</span>
                            <span class="font-bold text-slate-800 text-right">{{ $applicantName }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-slate-500">Plot No.</span>
                            <span class="font-bold text-slate-800">{{ $plotNumber }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-slate-500">Application</span>
                            <span class="font-bold text-slate-800 text-right break-all">{{ $applicationId }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-slate-500">Mobile</span>
                            <span class="font-bold text-slate-800">{{ $mobile }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment methods --}}
                <div class="pg-methods">
                    <p class="text-[11px] font-extrabold text-slate-800 m-0 mb-3">Select Payment Method</p>

                    <div class="pg-method-tabs">
                        <label class="pg-method-tab active" data-mode="UPI">
                            <input type="radio" name="mode_pick" value="UPI" checked>
                            <span class="material-symbols-outlined text-[20px] text-indigo-600 block mx-auto mb-1">qr_code_2</span>
                            <span class="text-[10px] font-bold">UPI</span>
                        </label>
                        <label class="pg-method-tab" data-mode="Debit Card">
                            <input type="radio" name="mode_pick" value="Debit Card">
                            <span class="material-symbols-outlined text-[20px] text-indigo-600 block mx-auto mb-1">credit_card</span>
                            <span class="text-[10px] font-bold">Debit Card</span>
                        </label>
                        <label class="pg-method-tab" data-mode="Credit Card">
                            <input type="radio" name="mode_pick" value="Credit Card">
                            <span class="material-symbols-outlined text-[20px] text-indigo-600 block mx-auto mb-1">payment</span>
                            <span class="text-[10px] font-bold">Credit Card</span>
                        </label>
                        <label class="pg-method-tab" data-mode="Net Banking">
                            <input type="radio" name="mode_pick" value="Net Banking">
                            <span class="material-symbols-outlined text-[20px] text-indigo-600 block mx-auto mb-1">account_balance</span>
                            <span class="text-[10px] font-bold">Net Banking</span>
                        </label>
                    </div>

                    {{-- UPI Panel --}}
                    <div class="pg-panel active" id="panel-upi">
                        <p class="text-[10px] font-bold text-slate-600 mb-2">Pay via UPI App</p>
                        <div class="pg-upi-apps">
                            <div class="pg-upi-app selected" data-app="Google Pay">GPay</div>
                            <div class="pg-upi-app" data-app="PhonePe">PhonePe</div>
                            <div class="pg-upi-app" data-app="Paytm">Paytm</div>
                        </div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">UPI ID / VPA</label>
                        <input type="text" class="pg-field mb-2" placeholder="yourname@upi" value="citizen@oksbi">
                        <p class="text-[9px] text-slate-400 m-0">You will receive a collect request on your UPI app.</p>
                    </div>

                    {{-- Card Panel --}}
                    <div class="pg-panel" id="panel-card">
                        <div class="pg-card-preview">
                            <div class="pg-card-chip"></div>
                            <p class="text-[14px] font-mono tracking-widest m-0 mb-3" id="cardDisplay">4111 •••• •••• 4242</p>
                            <div class="flex justify-between text-[10px] text-white/80">
                                <span id="cardNameDisplay">SANDEEP KUMAR</span>
                                <span>12/28</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <input type="text" class="pg-field" id="cardNumber" placeholder="Card Number" maxlength="19" value="4111 1111 1111 4242">
                            <input type="text" class="pg-field" id="cardName" placeholder="Name on Card" value="{{ strtoupper($applicantName) }}">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" class="pg-field" placeholder="MM/YY" value="12/28">
                                <input type="password" class="pg-field" placeholder="CVV" value="123" maxlength="3">
                            </div>
                        </div>
                    </div>

                    {{-- Net Banking Panel --}}
                    <div class="pg-panel" id="panel-netbanking">
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Select Your Bank</label>
                        <select class="pg-field mb-2">
                            <option>State Bank of India</option>
                            <option>Punjab National Bank</option>
                            <option>HDFC Bank</option>
                            <option>ICICI Bank</option>
                            <option>Axis Bank</option>
                            <option>Bank of Baroda</option>
                        </select>
                        <p class="text-[9px] text-slate-400 m-0">You will be redirected to your bank's secure login page.</p>
                    </div>

                    <div class="mt-3">
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Transaction Remark (Optional)</label>
                        <input type="text" name="remark" class="pg-field" placeholder="EMI Installment Payment">
                    </div>

                    <button type="submit" class="pg-pay-btn">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                        Pay ₹ {{ $amountToPay }} Securely
                    </button>
                </div>
            </div>

            <div class="pg-secure">
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">lock</span> 256-bit SSL</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">verified</span> PCI DSS Compliant</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">shield</span> Secured by MMSAY Pay</span>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const tabs = document.querySelectorAll('.pg-method-tab');
    const modeInput = document.getElementById('paymentMode');
    const panelUpi = document.getElementById('panel-upi');
    const panelCard = document.getElementById('panel-card');
    const panelNet = document.getElementById('panel-netbanking');

    function showPanel(mode) {
        panelUpi.classList.remove('active');
        panelCard.classList.remove('active');
        panelNet.classList.remove('active');
        if (mode === 'UPI') panelUpi.classList.add('active');
        else if (mode === 'Net Banking') panelNet.classList.add('active');
        else panelCard.classList.add('active');
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            const mode = tab.dataset.mode;
            modeInput.value = mode;
            showPanel(mode);
        });
    });

    document.querySelectorAll('.pg-upi-app').forEach(function (app) {
        app.addEventListener('click', function () {
            document.querySelectorAll('.pg-upi-app').forEach(function (a) { a.classList.remove('selected'); });
            app.classList.add('selected');
        });
    });

    const cardNum = document.getElementById('cardNumber');
    const cardDisplay = document.getElementById('cardDisplay');
    if (cardNum && cardDisplay) {
        cardNum.addEventListener('input', function () {
            const v = cardNum.value.replace(/\D/g, '').slice(0, 16);
            const parts = v.match(/.{1,4}/g) || [];
            cardDisplay.textContent = (parts.join(' ') || '•••• •••• •••• ••••').padEnd(19, '•');
        });
    }

    const cardName = document.getElementById('cardName');
    const cardNameDisplay = document.getElementById('cardNameDisplay');
    if (cardName && cardNameDisplay) {
        cardName.addEventListener('input', function () {
            cardNameDisplay.textContent = cardName.value.toUpperCase() || 'YOUR NAME';
        });
    }

    let seconds = 600;
    const countdownEl = document.getElementById('pgCountdown');
    setInterval(function () {
        if (seconds <= 0) return;
        seconds--;
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        if (countdownEl) countdownEl.textContent = m + ':' + s;
    }, 1000);
})();
</script>
@endpush
