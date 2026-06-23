@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Dashboard',
    'activeNav' => 'dashboard',
])

@section('content')
    {{-- Payment status banner (top of dashboard) --}}
    <div class="citizen-payment-banner {{ $isFullyPaid ? 'citizen-payment-banner--success' : 'citizen-payment-banner--warning' }}">
        <div class="citizen-payment-banner__icon">
            <span class="material-symbols-outlined">{{ $isFullyPaid ? 'check_circle' : 'info' }}</span>
        </div>
        <div class="citizen-payment-banner__body">
            @if ($isFullyPaid)
                <p class="citizen-payment-banner__title">Full Payment Completed</p>
                <p class="citizen-payment-banner__message">
                    Your payment has been completed successfully.
                </p>
            @else
                <p class="citizen-payment-banner__title">Payment Pending</p>
                <p class="citizen-payment-banner__message">
                    Your full payment has not been completed yet. Please complete your remaining balance.
                </p>
            @endif
            <div class="citizen-payment-banner__stats">
                <span><strong>Total Due:</strong> {{ $totalAmountFormatted }}</span>
                <span><strong>Paid:</strong> {{ $totalPaidFormatted }}</span>
                <span><strong>Remaining:</strong> {{ $outstandingFormatted }}</span>
            </div>
        </div>
        @unless ($isFullyPaid)
        <a href="{{ route('citizen.payment-status') }}" class="citizen-payment-banner__action">
            Pay EMI
            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
        </a>
        @endunless
    </div>

    {{-- Summary --}}
    <div class="border border-slate-100 rounded-lg p-2.5 bg-white">
        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application Status</p>
        <p class="text-[12px] font-bold text-slate-800">{{ $flatStatus }}</p>
    </div>

    {{-- Application details --}}
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Application Details</h2>
        </div>
        <div class="p-3 space-y-3">
            <div>
                <h3 class="text-[13px] font-extrabold text-indigo-700 uppercase tracking-wider mb-2">Basic Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @include('partials.mmsay.citizen.detail-grid', ['items' => $basicDetails])
                </div>
            </div>

            @if (count($propertyDetails) > 0)
            <div class="pt-1 border-t border-slate-100">
                <h3 class="text-[13px] font-extrabold text-indigo-700 uppercase tracking-wider mb-2 mt-2">Allotted Property Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @include('partials.mmsay.citizen.detail-grid', ['items' => $propertyDetails])
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Payment summary --}}
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Payment Summary</h2>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[14px]">account_balance_wallet</span>
                        </span>
                        <p class="text-[9px] font-bold uppercase text-slate-600">Total Amount</p>
                    </div>
                    <p class="text-[13px] font-extrabold text-slate-800">{{ $totalAmountFormatted }}</p>
                </div>
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
                <a href="{{ route('citizen.payment-status') }}" class="text-[10px] font-bold text-indigo-600 hover:underline no-underline shrink-0">View full details</a>
                @endif
            </div>

            @include('partials.mmsay.citizen.payment-details')
        </div>
    </div>

    {{-- Physical Possession scheme --}}
    <div class="citizen-card" id="physical-possession">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
            <h2 class="text-[11px] font-extrabold text-slate-800">Physical Possession</h2>
            <span class="tag tag-gold !text-amber-700 !bg-amber-50 !border-amber-200">New Scheme</span>
        </div>
        <div class="p-3">
            @php
                $ppStatusLabel = 'Not Applied';
                $ppStatusClass = 'text-slate-700';
                $ppStatusBadgeClass = 'bg-slate-100 text-slate-700';

                if (!empty($ppHasDraftApplication)) {
                    $ppStatusLabel = 'Draft — In Progress';
                } elseif ($latestPpApplication) {
                    $ppStatusLabel = match ($latestPpApplication->status) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'returned' => 'Returned',
                        default => ucfirst($latestPpApplication->status),
                    };
                    [$ppStatusClass, $ppStatusBadgeClass] = match ($latestPpApplication->status) {
                        'approved' => ['text-emerald-700', 'bg-emerald-100 text-emerald-700'],
                        'rejected' => ['text-red-600', 'bg-red-100 text-red-700'],
                        'returned' => ['text-blue-700', 'bg-blue-100 text-blue-700'],
                        default => ['text-amber-700', 'bg-amber-100 text-amber-700'],
                    };
                } elseif (!$isPpEligible) {
                    $ppStatusLabel = 'Not Eligible';
                    $ppStatusClass = 'text-amber-700';
                    $ppStatusBadgeClass = 'bg-amber-100 text-amber-700';
                }
            @endphp
            <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5 mb-3 flex items-center justify-between gap-3">
                <div>
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application Status</p>
                    <p class="text-[12px] font-bold {{ $ppStatusClass }} m-0">{{ $ppStatusLabel }}</p>
                    @if ($latestPpApplication)
                    <p class="text-[10px] text-slate-400 m-0 mt-0.5">{{ $latestPpApplication->application_number }} · {{ $latestPpApplication->created_at->format('d M Y') }}</p>
                    @endif
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase shrink-0 {{ $ppStatusBadgeClass }}">{{ $ppStatusLabel }}</span>
            </div>

            <div class="flex flex-wrap gap-2 mb-3">
                @unless($ppHasApplication)
                @if($isPpEligible)
                <a href="{{ route('pp.user.apply') }}" class="btn-v2-primary btn-v2-sm no-underline">
                    <span class="material-symbols-outlined text-[14px]">edit_document</span>
                    {{ !empty($ppHasDraftApplication) ? 'Continue Application' : 'Apply for Physical Possession' }}
                </a>
                <a href="{{ route('pp.user.view-form') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 text-[11px] font-bold text-emerald-700 no-underline hover:bg-emerald-100">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    View Application Request Form
                </a>
                @else
                <div class="w-full rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                    <p class="text-[10px] font-bold text-amber-900 m-0 mb-0.5">Physical Possession — Not eligible yet</p>
                    <p class="text-[10px] text-amber-800 m-0 leading-relaxed">
                        Your total payments (initial registration deposit + installments) must be at least <strong>{{ $ppMinTotalPaidRequiredFormatted }}</strong>.
                        Your total paid so far: <strong>{{ $ppTotalPaidFormatted }}</strong>.
                    </p>
                </div>
                @endif
                @else
                <a href="{{ route('pp.user.application.show', $latestPpApplication) }}" class="btn-v2-primary btn-v2-sm no-underline">
                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                    View My Application
                </a>
                @endunless
                <a href="{{ route('pp.user.applications') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-700 no-underline hover:bg-slate-100">
                    <span class="material-symbols-outlined text-[16px]">folder_open</span>
                    My Applications
                </a>
            </div>

            @if (!$ppHasApplication && empty($ppHasDraftApplication))
            <p class="text-[10px] text-slate-500 m-0">Download the possession application request form, sign it, then apply and upload documents.</p>
            @endif
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
            @if ($ppHasApplication)
            <a href="{{ route('pp.user.application.show', $latestPpApplication) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-[11px] font-bold text-indigo-700 no-underline hover:bg-indigo-100">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                My Application
            </a>
            @elseif($isPpEligible)
            <a href="{{ route('pp.user.apply') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-[11px] font-bold text-indigo-700 no-underline hover:bg-indigo-100">
                <span class="material-symbols-outlined text-[16px]">edit_document</span>
                Physical Possession
            </a>
            @endif
        </div>
    </div>
@endsection
