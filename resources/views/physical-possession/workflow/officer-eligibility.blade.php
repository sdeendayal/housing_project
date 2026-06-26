@extends('physical-possession.layouts.officer')

@section('title', 'Eligible Applicants List')
@section('page-title', 'Possession Eligibility List')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card premium-card border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Eligible Applicants</h5>
                    <p class="text-muted small mb-0">Applicants whose total payments are at least ₹40,000 (auto-aggregated from cash receipts and installment ledger).</p>
                </div>
                <form action="{{ route('pp.officer.eligibility-list') }}" method="GET" class="d-flex gap-2 align-items-center">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Name, Mobile, Application No..." value="{{ $search }}">
                    </div>
                    <button type="submit" class="btn btn-primary px-4 btn-schedule">Search</button>
                    @if($search)
                        <a href="{{ route('pp.officer.eligibility-list') }}" class="btn btn-outline-secondary btn-schedule">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 pp-eligibility-table">
                    <thead class="table-light text-uppercase text-muted">
                        <tr>
                            <th class="ps-3" style="width: 60px;">S.No.</th>
                            <th>Application No. / ID</th>
                            <th>Applicant Details</th>
                            <th>Property Details</th>
                            <th>Total Paid</th>
                            <th>Payment Status</th>
                            <th>Possession Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchasers as $p)
                            <tr>
                                <td class="ps-3 fw-semibold text-muted">
                                    {{ ($purchasers->currentPage() - 1) * $purchasers->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-0.5">{{ $p->ApplicationNo ?? '—' }}</div>
                                    <small class="text-muted text-uppercase tracking-wider font-monospace fs-8">PPP ID: {{ $p->PPPId ?? '—' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $p->PrivatePurchaserName }}</div>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $p->MobileNo }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-slate-700">{{ $p->AssetName }}</div>
                                    <small class="text-muted">Size: {{ $p->AssetSize }} {{ $p->Unit }} | Sector: {{ $p->DistrictName }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-success">₹ {{ number_format($p->total_paid, 2) }}</div>
                                    <small class="text-muted">Cost: ₹ {{ number_format($p->FlatCost, 2) }}</small>
                                </td>
                                <td>
                                    @if($p->total_paid >= $p->FlatCost && $p->FlatCost > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1.5 rounded-pill fs-8">
                                            <i class="bi bi-check-circle-fill me-1"></i>Fully Paid
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-20 px-2.5 py-1.5 rounded-pill fs-8">
                                            <i class="bi bi-cash-stack me-1"></i>Partially Paid
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->physical_possession_status)
                                        @php
                                            $badgeClass = match ($p->physical_possession_status) {
                                                'Eligible for Physical Possession' => 'bg-info text-info',
                                                'Visit Scheduled' => 'bg-warning text-warning-emphasis',
                                                'Physical Possession Submitted' => 'bg-primary text-white',
                                                'Verified' => 'bg-success text-white',
                                                'Rejected' => 'bg-danger text-white',
                                                default => 'bg-secondary text-white'
                                            };
                                        @endphp
                                        <span class="badge {{ str_contains($badgeClass, 'text-') ? $badgeClass . ' bg-opacity-10 border border-opacity-20 border-' . explode(' ', $badgeClass)[1] : $badgeClass }} px-2.5 py-1.5 rounded-3 fs-8">
                                            {{ $p->physical_possession_status }}
                                        </span>
                                    @else
                                        <span class="text-muted small italic">Not Initiated</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    @if($p->physical_possession_status === 'Eligible for Physical Possession')
                                        <a href="{{ route('pp.officer.schedule-form', $p->application_secure_id) }}" class="btn btn-primary btn-schedule text-nowrap rounded-pill shadow-sm">
                                            <i class="bi bi-calendar-plus me-1"></i>Schedule Visit
                                        </a>
                                    @elseif($p->physical_possession_status === 'Visit Scheduled')
                                        <a href="{{ route('pp.officer.schedule-form', $p->application_secure_id) }}" class="btn btn-outline-secondary btn-schedule text-nowrap rounded-pill">
                                            <i class="bi bi-pencil-square me-1"></i>Update Schedule
                                        </a>
                                    @elseif($p->physical_possession_status === 'Slot Selected')
                                        <a href="{{ route('pp.officer.verify-form', $p->application_secure_id) }}" class="btn btn-success btn-schedule text-nowrap rounded-pill text-white shadow-sm">
                                            <i class="bi bi-shield-check me-1"></i>Perform Visit
                                        </a>
                                    @else
                                        <a href="{{ route('pp.officer.verify-form', $p->application_secure_id) }}" class="btn btn-outline-secondary btn-schedule text-nowrap rounded-pill">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 mb-3 d-block text-slate-300"></i>
                                    No eligible applicants found in your district.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($purchasers->hasPages())
                <div class="d-flex justify-content-center mt-4">
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
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
    }
    .pp-eligibility-table th {
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 10px 12px !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .pp-eligibility-table td {
        font-size: 0.78rem !important;
        padding: 10px 12px !important;
        vertical-align: middle !important;
    }
    .pp-eligibility-table tr:hover {
        background-color: rgba(30, 64, 175, 0.02) !important;
    }
    .btn-schedule {
        font-size: 0.7rem !important;
        padding: 5px 12px !important;
        font-weight: 600 !important;
        letter-spacing: 0.2px;
        transition: all 0.2s ease;
    }
    .btn-schedule:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(30, 64, 175, 0.15);
    }
    /* Pagination responsiveness */
    .pagination {
        margin-bottom: 0 !important;
        gap: 2px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .page-link {
        font-size: 0.75rem !important;
        padding: 5px 10px !important;
        border-radius: 6px !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
    }
    .page-item.active .page-link {
        background-color: var(--pp-primary) !important;
        border-color: var(--pp-primary) !important;
        color: #fff !important;
    }
    .page-link:hover {
        background-color: #f1f5f9 !important;
    }
</style>
@endpush
@endsection
