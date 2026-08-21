@extends('physical-possession.layouts.officer')

@section('title', 'Caste Eligibility List')
@section('page-title', 'Caste Eligibility List')

@section('content')
<div class="container-fluid py-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 py-2 px-3 fs-7" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem 1rem;"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3 py-2 px-3 fs-7" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem 1rem;"></button>
        </div>
    @endif

    <!-- Sleek Glassmorphic Filter & Header Panel -->
    <div class="card premium-card border-0 mb-3 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="lh-sm">
                    <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <span class="d-inline-block w-2.5 h-6 bg-warning rounded"></span>
                        Caste Category Filtering
                    </h6>
                    <span class="text-muted fs-8">Double-check eligible candidates matching both ₹60k limit & MMSAY list.</span>
                </div>
                
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <form action="{{ route('pp.officer.caste-eligibility') }}" method="GET" class="d-flex gap-2 align-items-center mb-0">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-funnel fs-8"></i></span>
                            <select name="category" class="form-select form-select-sm border-start-0 ps-1" onchange="this.form.submit()" style="min-width: 160px; font-size: 0.72rem; height: 32px; font-weight: 500;">
                                <option value="">All Categories</option>
                                <option value="GJ" {{ $selectedCategory === 'GJ' ? 'selected' : '' }}>Ghumantu Jati</option>
                                <option value="W" {{ $selectedCategory === 'W' ? 'selected' : '' }}>Widows</option>
                                <option value="SC" {{ $selectedCategory === 'SC' ? 'selected' : '' }}>Scheduled Caste</option>
                                <option value="other" {{ $selectedCategory === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        @if($selectedCategory)
                            <a href="{{ route('pp.officer.caste-eligibility') }}" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fs-8" style="height: 32px; line-height: 22px;">Reset</a>
                        @endif
                    </form>
                    
                    <a href="{{ route('pp.officer.caste-eligibility.export', ['category' => $selectedCategory]) }}" class="btn btn-sm btn-success py-1 px-3 fs-8 d-flex align-items-center gap-1.5" style="height: 32px; font-weight: 600; line-height: 22px; border-radius: 6px; box-shadow: 0 2px 4px rgba(25, 135, 84, 0.2);">
                        <i class="bi bi-file-earmark-excel-fill"></i> Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Data Table -->
    <div class="card premium-card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 pp-caste-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-center" style="width: 50px;">S.No.</th>
                            <th style="width: 140px;">Application Details</th>
                            <th style="width: 150px;">Applicant Name</th>
                            <th class="text-center" style="width: 110px;">Caste Category</th>
                            <th>Property Name</th>
                            <th style="width: 120px;">Financial Summary</th>
                            <th class="text-center" style="width: 120px;">Possession Status</th>
                            <th class="text-end pe-3" style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchasers as $p)
                            <tr>
                                <td class="ps-3 text-center fw-bold text-muted fs-8">
                                    {{ ($purchasers->currentPage() - 1) * $purchasers->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-7.5 lh-sm">{{ $p->ApplicationNo ?? '—' }}</div>
                                    <div class="text-muted fs-9 text-uppercase tracking-wider">PPP ID: {{ $p->PPPId ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark fs-7.5 lh-sm">{{ $p->PrivatePurchaserName }}</div>
                                    <div class="text-muted fs-9"><i class="bi bi-telephone me-1"></i>{{ $p->MobileNo }}</div>
                                </td>
                                <td class="text-center">
                                    @php
                                        $isGhumantu = ($p->mmsay_category === 'GJ') || 
                                                      (str_contains(strtolower($p->mmsay_caste ?? ''), 'tapriwas')) || 
                                                      (str_contains(strtolower($p->mmsay_caste ?? ''), 'ghumantu')) || 
                                                      (str_contains(strtolower($p->CasteCategoryName ?? ''), 'tapriwas')) || 
                                                      (str_contains(strtolower($p->CasteCategoryName ?? ''), 'ghumantu')) || 
                                                      (str_contains(strtolower($p->CasteCategoryName ?? ''), 'de-notified'));
                                                      
                                        $isWidow = ($p->mmsay_category === 'W') || 
                                                   (str_contains(strtolower($p->mmsay_caste ?? ''), 'widow')) || 
                                                   (str_contains(strtolower($p->CasteCategoryName ?? ''), 'widow'));
                                                   
                                        $isSC = ($p->mmsay_category === 'SC') || 
                                                (str_contains(strtolower($p->mmsay_caste ?? ''), 'scheduled')) || 
                                                (str_contains(strtolower($p->mmsay_caste ?? ''), 'sc')) || 
                                                (str_contains(strtolower($p->CasteCategoryName ?? ''), 'scheduled')) || 
                                                (str_contains(strtolower($p->CasteCategoryName ?? ''), 'sc'));
                                    @endphp

                                    @if($isGhumantu)
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-20 fs-9 px-2.5 py-1 rounded" style="font-weight: 600;">
                                            Ghumantu Jati
                                        </span>
                                    @elseif($isWidow)
                                        <span class="badge bg-purple bg-opacity-10 text-purple border border-purple border-opacity-20 fs-9 px-2.5 py-1 rounded" style="font-weight: 600;">
                                            Widows
                                        </span>
                                    @elseif($isSC)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 fs-9 px-2.5 py-1 rounded" style="font-weight: 600;">
                                            Scheduled Caste
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 fs-9 px-2.5 py-1 rounded" style="font-weight: 600;">
                                            Other ({{ $p->CasteCategoryName ?? '—' }})
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-slate-700 fs-7.5 lh-sm">{{ $p->AssetName }}</div>
                                    <div class="text-muted fs-9">Size: {{ $p->AssetSize }} {{ $p->Unit }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-success fs-7.5 lh-sm">₹ {{ number_format($p->total_paid) }}</div>
                                    <div class="text-muted fs-9">Cost: ₹ {{ number_format($p->FlatCost) }}</div>
                                </td>
                                <td class="text-center">
                                    @if($p->physical_possession_status)
                                        @php
                                            $badgeClass = match ($p->physical_possession_status) {
                                                'Eligible for Physical Possession' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-20',
                                                'Visit Scheduled' => 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-20',
                                                'Slot Selected' => 'bg-primary text-white border border-primary',
                                                'Physical Possession Submitted' => 'bg-primary text-white border border-primary',
                                                'Site Verified' => 'bg-info text-white border border-info shadow-sm',
                                                'Verified' => 'bg-success text-white border border-success shadow-sm',
                                                'Rejected' => 'bg-danger text-white border border-danger shadow-sm',
                                                default => 'bg-secondary text-white border border-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-2 py-1 rounded-2 fs-9 font-weight-600">
                                            {{ $p->physical_possession_status }}
                                        </span>
                                    @else
                                        <span class="text-muted fs-9 italic">Not Initiated</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                     @if(!$p->application_secure_id)
                                         <button class="btn btn-outline-secondary btn-action text-nowrap rounded" disabled>
                                             <i class="bi bi-slash-circle me-1"></i>Not Initiated
                                         </button>
                                     @elseif($p->physical_possession_status === 'Eligible for Physical Possession')
                                         <a href="{{ route('pp.officer.schedule-form', $p->application_secure_id) }}" class="btn btn-primary btn-action text-nowrap rounded shadow-sm">
                                             <i class="bi bi-calendar-plus me-1"></i>Schedule Visit
                                         </a>
                                     @elseif($p->physical_possession_status === 'Visit Scheduled')
                                         <a href="{{ route('pp.officer.schedule-form', $p->application_secure_id) }}" class="btn btn-outline-secondary btn-action text-nowrap rounded">
                                             <i class="bi bi-pencil-square me-1"></i>Update
                                         </a>
                                     @elseif($p->physical_possession_status === 'Slot Selected')
                                         <a href="{{ route('pp.officer.verify-form', $p->application_secure_id) }}" class="btn btn-success btn-action text-nowrap rounded text-white shadow-sm">
                                             <i class="bi bi-shield-check me-1"></i>Perform Visit
                                         </a>
                                     @elseif($p->physical_possession_status === 'Site Verified')
                                         <a href="{{ route('pp.officer.verify-form', $p->application_secure_id) }}" class="btn btn-info btn-action text-nowrap rounded text-white shadow-sm">
                                             <i class="bi bi-file-earmark-arrow-up me-1"></i>E-Verify
                                         </a>
                                     @else
                                         <a href="{{ route('pp.officer.verify-form', $p->application_secure_id) }}" class="btn btn-outline-secondary btn-action text-nowrap rounded">
                                             <i class="bi bi-eye me-1"></i>View
                                         </a>
                                     @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted fs-8">
                                    <i class="bi bi-people fs-2 mb-2 d-block text-slate-300"></i>
                                    No eligible applicants found for the selected Caste Category in your district.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($purchasers->hasPages())
                <div class="d-flex justify-content-center py-2 bg-light border-top">
                    {{ $purchasers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .premium-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.01) !important;
    }
    .pp-caste-table th {
        font-size: 0.65rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 6px 10px !important;
        border-bottom: 2px solid #e2e8f0 !important;
        background-color: #f8fafc;
    }
    .pp-caste-table td {
        font-size: 0.72rem !important;
        padding: 5px 10px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9;
    }
    .pp-caste-table tr:hover {
        background-color: rgba(30, 64, 175, 0.015) !important;
    }
    .btn-action {
        font-size: 0.65rem !important;
        padding: 3px 8px !important;
        font-weight: 600 !important;
        letter-spacing: 0.1px;
        transition: all 0.15s ease;
    }
    .btn-action:hover {
        transform: translateY(-0.5px);
        box-shadow: 0 2px 4px rgba(30, 64, 175, 0.1);
    }
    .badge-caste {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        font-weight: 500;
    }
    .text-purple {
        color: #7c3aed !important;
    }
    .bg-purple {
        background-color: #f5f3ff !important;
    }
    .border-purple {
        border-color: #ddd6fe !important;
    }
    .fs-7.5 {
        font-size: 0.75rem !important;
    }
    .fs-8 {
        font-size: 0.7rem !important;
    }
    .fs-9 {
        font-size: 0.65rem !important;
    }
    
    /* Pagination compact styling */
    .pagination {
        margin-bottom: 0 !important;
        gap: 1px;
    }
    .page-link {
        font-size: 0.68rem !important;
        padding: 3px 8px !important;
        border-radius: 4px !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
    }
    .page-item.active .page-link {
        background-color: var(--pp-primary) !important;
        border-color: var(--pp-primary) !important;
        color: #fff !important;
    }
</style>
@endpush
@endsection
