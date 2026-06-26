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
                $ppStatus = $latestPpApplication ? $latestPpApplication->physical_possession_status : null;
                
                $statusBadgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                $statusMessage = 'Physical Possession process is initiated by the District Officer only. Please contact the District Office for more information.';
                
                if ($ppStatus) {
                    $statusBadgeClass = match ($ppStatus) {
                        'Eligible for Physical Possession' => 'bg-info bg-opacity-10 text-info border-info border-opacity-20',
                        'Visit Scheduled' => 'bg-warning bg-opacity-10 text-warning-emphasis border-warning border-opacity-20',
                        'Slot Selected' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'Physical Possession Submitted' => 'bg-primary bg-opacity-10 text-primary border-primary border-opacity-20',
                        'Verified' => 'bg-success bg-opacity-10 text-success border-success border-opacity-20',
                        'Rejected' => 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-20',
                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                    };

                    $statusMessage = match ($ppStatus) {
                        'Eligible for Physical Possession' => 'You have been marked as Eligible for Physical Possession by the District Officer. The meeting/visit schedule will be assigned shortly.',
                        'Visit Scheduled' => 'A physical possession visit has been scheduled for your property. Please select your preferred time slot.',
                        'Slot Selected' => 'You have selected your visit slot. The District Officer will visit your plot to capture GPS coordinates, photos, and get the possession certificate signed.',
                        'Physical Possession Submitted' => 'The physical possession details have been uploaded. Your submission is currently undergoing verification by the District Officer.',
                        'Verified' => 'Congratulations! Your physical possession has been verified and approved by the District Officer.',
                        'Rejected' => 'Your physical possession submission has been rejected. Remarks: ' . ($latestPpApplication->remarks ?? 'No remarks provided.') . ' Please correct and re-submit.',
                        default => 'Current physical possession status: ' . $ppStatus
                    };
                }
            @endphp

            <div class="rounded-lg border p-3 mb-3 bg-white">
                <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
                    <div>
                        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Physical Possession Status</p>
                        <p class="text-[12px] font-bold text-slate-800 m-0">{{ $ppStatus ?? 'Not Initiated' }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border {{ $statusBadgeClass }}">
                        {{ $ppStatus ?? 'Not Initiated' }}
                    </span>
                </div>
                
                <p class="text-[10px] text-slate-600 m-0 leading-relaxed mb-3">
                    {{ $statusMessage }}
                </p>

                @if($latestPpApplication)
                    <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100 text-[10px] mb-3">
                        <div class="d-flex justify-content-between mb-1.5 pb-1 border-b border-slate-100">
                            <span class="text-slate-400 font-semibold">Application Number:</span>
                            <strong class="text-slate-700">{{ $latestPpApplication->application_number }}</strong>
                        </div>
                        
                        @if(in_array($latestPpApplication->physical_possession_status, ['Slot Selected', 'Physical Possession Submitted', 'Verified', 'Rejected']))
                            <div class="d-flex justify-content-between mb-1.5 pb-1 border-b border-slate-100">
                                <span class="text-slate-400 font-semibold">Selected Visit Slot:</span>
                                <strong class="text-emerald-700">
                                    @if(strtotime($latestPpApplication->meeting_slot))
                                        {{ \Carbon\Carbon::parse($latestPpApplication->meeting_slot)->format('d M Y - h:i A') }}
                                    @else
                                        {{ $latestPpApplication->meeting_slot }}
                                    @endif
                                </strong>
                            </div>
                        @else
                            <!-- Show Offered Slots -->
                            <div class="mb-1.5 pb-1 border-b border-slate-100">
                                <span class="text-slate-400 font-semibold block mb-1">Offered Slots:</span>
                                <div class="pl-2 border-l border-slate-200 text-slate-700 leading-normal">
                                    @if($latestPpApplication->visit_slot_1)
                                        <div>• Slot 1: <strong>{{ $latestPpApplication->visit_slot_1->format('d M Y, h:i A') }}</strong></div>
                                    @endif
                                    @if($latestPpApplication->visit_slot_2)
                                        <div>• Slot 2: <strong>{{ $latestPpApplication->visit_slot_2->format('d M Y, h:i A') }}</strong></div>
                                    @endif
                                    @if($latestPpApplication->visit_slot_3)
                                        <div>• Slot 3: <strong>{{ $latestPpApplication->visit_slot_3->format('d M Y, h:i A') }}</strong></div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($latestPpApplication->visit_instructions)
                            <div class="mt-1">
                                <span class="text-slate-400 font-semibold block mb-0.5">Instructions:</span>
                                <p class="text-slate-600 font-medium m-0 leading-relaxed bg-indigo-50/50 p-1.5 rounded border border-indigo-100/50 whitespace-pre-line">{{ $latestPpApplication->visit_instructions }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex flex-wrap gap-2">
                    @if($ppStatus === 'Visit Scheduled' || $ppStatus === 'Rejected')
                        <a href="{{ route('pp.citizen.submit') }}" class="btn-v2-primary btn-v2-sm no-underline">
                            <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                            {{ $ppStatus === 'Rejected' ? 'Re-select Visit Slot' : 'Select Visit Slot' }}
                        </a>
                    @endif
                    
                    @if($latestPpApplication && $latestPpApplication->possession_certificate)
                        <a href="{{ asset('storage/' . $latestPpApplication->possession_certificate) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 text-[10px] font-bold text-emerald-700 no-underline hover:bg-emerald-100">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            View Uploaded Certificate
                        </a>
                    @endif
                </div>
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
            @if ($latestPpApplication && in_array($latestPpApplication->physical_possession_status, ['Visit Scheduled', 'Rejected']))
            <a href="{{ route('pp.citizen.submit') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-[11px] font-bold text-indigo-700 no-underline hover:bg-indigo-100">
                <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                Select Visit Slot
            </a>
            @endif
        </div>
    </div>
@endsection
