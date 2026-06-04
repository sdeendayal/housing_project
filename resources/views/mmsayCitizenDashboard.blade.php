@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Dashboard',
    'activeNav' => 'dashboard',
])

@section('content')
    {{-- Summary --}}
    <div class="border border-slate-100 rounded-lg p-2.5 bg-white">
        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application Status</p>
        <p class="text-[12px] font-bold text-slate-800">{{ $flatStatus }}</p>
    </div>

    {{-- Applicant details --}}
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Applicant Details</h2>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Name</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $displayName }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application ID</p>
                    <p class="text-[12px] font-bold text-slate-800 break-all">{{ $applicationId }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Category</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $category }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Flat / Unit Status</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $flatStatus }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Purchase Date</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaseDate }}</p>
                </div>
                @if ($assetName)
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50 sm:col-span-2 lg:col-span-1">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Allotted Asset</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $assetName }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment summary --}}
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Payment Summary</h2>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="rounded-lg border border-emerald-100 bg-emerald-50/60 p-2.5">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        </span>
                        <p class="text-[9px] font-bold uppercase text-emerald-700/80">Total Paid</p>
                    </div>
                    <p class="text-[13px] font-extrabold text-emerald-700">{{ $totalPaidFormatted }}</p>
                </div>
                <div class="rounded-lg border {{ $hasOutstanding ? 'border-red-100 bg-red-50/60' : 'border-slate-100 bg-slate-50' }} p-2.5">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-6 h-6 rounded-md {{ $hasOutstanding ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center">
                            <span class="material-symbols-outlined text-[14px]">pending</span>
                        </span>
                        <p class="text-[9px] font-bold uppercase {{ $hasOutstanding ? 'text-red-700/80' : 'text-slate-500' }}">Outstanding</p>
                    </div>
                    <p class="text-[13px] font-extrabold {{ $hasOutstanding ? 'text-red-600' : 'text-slate-500' }}">{{ $outstandingFormatted }}</p>
                </div>
                <div class="rounded-lg border border-indigo-100 bg-indigo-50/60 p-2.5">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[14px]">percent</span>
                        </span>
                        <p class="text-[9px] font-bold uppercase text-indigo-700/80">Completed</p>
                    </div>
                    <p class="text-[13px] font-extrabold text-indigo-700">{{ $paymentProgress }}%</p>
                </div>
            </div>

            <div class="mt-3 rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">Payment Progress</span>
                    <span class="text-[11px] font-extrabold text-indigo-600">{{ $paymentProgress }}%</span>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" style="width:{{ $paymentProgress }}%"></div>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                @if ($hasOutstanding)
                <p class="text-[10px] text-slate-500 m-0">Outstanding amount pending</p>
                <a href="{{ route('citizen.payment-status') }}" class="btn-v2-primary btn-v2-sm no-underline shrink-0">
                    <span class="material-symbols-outlined text-[14px]">payment</span>
                    Pay Now
                </a>
                @else
                <p class="text-[10px] text-emerald-600 font-semibold m-0 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">verified</span>
                    All payments cleared
                </p>
                <a href="{{ route('citizen.payment-status') }}" class="text-[10px] font-bold text-indigo-600 hover:underline no-underline shrink-0">View history</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Quick Links</h2>
        </div>
        <div class="p-3 flex flex-wrap gap-2">
            <a href="{{ route('citizen.profile') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-700 no-underline hover:bg-slate-100">
                <span class="material-symbols-outlined text-[16px]">account_circle</span>
                My Profile
            </a>
            <a href="{{ route('citizen.payment-status') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-700 no-underline hover:bg-slate-100">
                <span class="material-symbols-outlined text-[16px]">payments</span>
                Payment Status
            </a>
        </div>
    </div>
@endsection
