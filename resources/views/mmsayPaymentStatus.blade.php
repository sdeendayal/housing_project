@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Payment Status',
    'activeNav' => 'payments',
])

@section('content')
    <div class="border border-slate-100 rounded-lg p-2.5 bg-white mb-2">
        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application</p>
        <p class="text-[12px] font-bold text-slate-800 break-all">{{ $applicationId }}</p>
        @if ($assetName)
        <p class="text-[10px] text-slate-500 m-0 mt-0.5">Asset: {{ $assetName }}</p>
        @endif
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
