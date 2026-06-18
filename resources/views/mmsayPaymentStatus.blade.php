@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Payment Status',
    'activeNav' => 'payments',
])

@section('content')
    {{-- Pay Now — gateway link (installment details below unchanged) --}}
    <div class="mb-3 rounded-2xl overflow-hidden border border-indigo-200 bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-700 p-3 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-lg shadow-indigo-200/50">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-white text-[26px]">account_balance_wallet</span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-white/70 uppercase tracking-wider m-0">Online Payment</p>
                <p class="text-sm font-extrabold text-white m-0">Pay installment via secure gateway</p>
                @if (!empty($hasOutstanding) && $hasOutstanding)
                    <p class="text-[10px] text-amber-200 font-bold m-0 mt-0.5">Outstanding: {{ $outstandingFormatted }}</p>
                @endif
            </div>
        </div>
        <a href="{{ route('citizen.payment.pay') }}"
           class="shrink-0 inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-indigo-700 text-[12px] font-extrabold no-underline shadow-md hover:bg-indigo-50 hover:scale-[1.02] transition-transform">
            <span class="material-symbols-outlined text-[20px]">payments</span>
            Pay Now
        </a>
    </div>

    <div class="border border-slate-100 rounded-lg p-2.5 bg-white mb-2">
        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application</p>
        <p class="text-[12px] font-bold text-slate-800 break-all">{{ $applicationId }}</p>
    </div>

    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Payment Overview</h2>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 mb-3">
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                    <p class="text-[9px] text-slate-400 uppercase font-bold mb-0.5">Purchase Date</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaseDate }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-2.5">
                    <p class="text-[9px] text-slate-600 uppercase font-bold mb-0.5">Total Amount</p>
                    <p class="text-[12px] font-extrabold text-slate-800">{{ $totalAmountFormatted }}</p>
                </div>
                <div class="rounded-lg border border-emerald-100 bg-emerald-50/60 p-2.5">
                    <p class="text-[9px] text-emerald-700/80 uppercase font-bold mb-0.5">Total Paid</p>
                    <p class="text-[12px] font-extrabold text-emerald-700">{{ $totalPaidFormatted }}</p>
                </div>
                <div class="rounded-lg border {{ $hasOutstanding ? 'border-red-100 bg-red-50/60' : 'border-slate-100 bg-slate-50' }} p-2.5">
                    <p class="text-[9px] {{ $hasOutstanding ? 'text-red-700/80' : 'text-slate-500' }} uppercase font-bold mb-0.5">Outstanding</p>
                    <p class="text-[12px] font-extrabold {{ $hasOutstanding ? 'text-red-600' : 'text-slate-500' }}">{{ $outstandingFormatted }}</p>
                </div>
                <div class="rounded-lg border border-indigo-100 bg-indigo-50/60 p-2.5">
                    <p class="text-[9px] text-indigo-700/80 uppercase font-bold mb-0.5">Completed</p>
                    <p class="text-[12px] font-extrabold text-indigo-700">{{ $paymentProgress }}%</p>
                </div>
            </div>

            <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5 mb-3">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">Payment Progress</span>
                    <span class="text-[11px] font-extrabold text-indigo-600">{{ $paymentProgress }}%</span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" style="width:{{ $paymentProgress }}%"></div>
                </div>
            </div>

            @include('partials.mmsay.citizen.payment-details')
        </div>
    </div>

    <div class="mt-2">
        <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-700 no-underline hover:bg-slate-50">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Back to Dashboard
        </a>
    </div>
@endsection
