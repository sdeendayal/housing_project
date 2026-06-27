@extends('physical-possession.layouts.officer')

@section('page-title', 'Dashboard')

@php
    $decided = $stats['verified'] + $stats['rejected'];
    $approvalRate = $decided > 0 ? round(($stats['verified'] / $decided) * 100) : 0;
    $weekTotal = array_sum($chartData);
@endphp

@push('styles')
<style>
.pp-dash-welcome {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.625rem 0.75rem;
    margin-bottom: 0.625rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e40af 0%, #3730a3 55%, #4f46e5 100%);
    color: #fff;
    box-shadow: 0 4px 14px rgba(30, 64, 175, 0.25);
}
.pp-dash-welcome h2 {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.25;
}
.pp-dash-welcome p {
    margin: 0.1rem 0 0;
    font-size: 0.7rem;
    opacity: 0.88;
}
.pp-dash-welcome .pp-dash-date {
    font-size: 0.68rem;
    padding: 0.2rem 0.5rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.15);
    white-space: nowrap;
}

.pp-dash-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.4rem;
    margin-bottom: 0.625rem;
}
@media (max-width: 575px) {
    .pp-dash-stats { grid-template-columns: 1fr; }
}
@media (min-width: 576px) and (max-width: 767px) {
    .pp-dash-stats { grid-template-columns: repeat(2, 1fr); }
    .pp-dash-stats .pp-dash-stat:last-child { grid-column: span 2; }
}
@media (min-width: 768px) and (max-width: 1199px) {
    .pp-dash-stats { grid-template-columns: repeat(3, 1fr); }
}

.pp-dash-stat {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.5rem 0.55rem;
    border-radius: 10px;
    background: #fff;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    color: inherit;
    transition: transform 0.15s, box-shadow 0.15s;
    min-width: 0;
}
.pp-dash-stat:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    color: inherit;
}
[data-bs-theme="dark"] .pp-dash-stat {
    background: var(--pp-card-bg);
    border-color: var(--pp-glass-border);
}
.pp-dash-stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.pp-dash-stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
.pp-dash-stat-icon.orange { background: #ffedd5; color: #c2410c; }
.pp-dash-stat-icon.green { background: #d1fae5; color: #047857; }
.pp-dash-stat-icon.red { background: #fee2e2; color: #b91c1c; }
.pp-dash-stat-icon.purple { background: #ede9fe; color: #6d28d9; }
.pp-dash-stat-label {
    font-size: 0.65rem;
    color: var(--pp-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.03em;
    font-weight: 600;
    line-height: 1.1;
}
.pp-dash-stat-value {
    font-size: 1.1rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--pp-text);
}

.pp-dash-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-bottom: 0.625rem;
}
.pp-dash-action {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.65rem;
    font-size: 0.72rem;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #1e40af;
    text-decoration: none;
    transition: background 0.15s;
}
.pp-dash-action:hover { background: #eff6ff; color: #1e3a8a; }
.pp-dash-action.warn { color: #c2410c; border-color: #fed7aa; }
.pp-dash-action.warn:hover { background: #fff7ed; }
[data-bs-theme="dark"] .pp-dash-action {
    background: var(--pp-card-bg);
    border-color: var(--pp-glass-border);
}

.pp-dash-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 0.5rem;
    align-items: start;
}
@media (max-width: 1199px) {
    .pp-dash-grid { grid-template-columns: 1fr; }
}

.pp-dash-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 0;
}
[data-bs-theme="dark"] .pp-dash-panel {
    background: var(--pp-card-bg);
    border-color: var(--pp-glass-border);
}
.pp-dash-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.45rem 0.65rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.75rem;
    font-weight: 700;
}
[data-bs-theme="dark"] .pp-dash-panel-head { background: rgba(255,255,255,0.04); }
.pp-dash-panel-head span {
    font-size: 0.65rem;
    font-weight: 600;
    color: var(--pp-text-muted);
}
.pp-dash-panel-body { padding: 0.5rem 0.65rem; }

.pp-dash-chart { height: 140px; position: relative; }

.pp-dash-side-stack { display: flex; flex-direction: column; gap: 0.5rem; }

.pp-dash-breakdown { padding: 0.5rem 0.65rem; }
.pp-dash-bar-row {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.35rem;
    font-size: 0.68rem;
}
.pp-dash-bar-row:last-child { margin-bottom: 0; }
.pp-dash-bar-label {
    width: 52px;
    font-weight: 600;
    color: var(--pp-text-muted);
    flex-shrink: 0;
}
.pp-dash-bar-track {
    flex: 1;
    height: 6px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
}
.pp-dash-bar-fill { height: 100%; border-radius: 999px; }
.pp-dash-bar-fill.orange { background: linear-gradient(90deg, #fb923c, #f97316); }
.pp-dash-bar-fill.green { background: linear-gradient(90deg, #34d399, #059669); }
.pp-dash-bar-fill.red { background: linear-gradient(90deg, #f87171, #dc2626); }
.pp-dash-bar-num {
    width: 22px;
    text-align: right;
    font-weight: 700;
    font-size: 0.7rem;
}

.pp-dash-rate {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.5rem 0.65rem;
    border-top: 1px solid #e2e8f0;
}
.pp-dash-ring {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: conic-gradient(#059669 {{ $approvalRate }}%, #e2e8f0 0);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.pp-dash-ring-inner {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.62rem;
    font-weight: 800;
    color: #059669;
}
[data-bs-theme="dark"] .pp-dash-ring-inner { background: var(--pp-card-bg); }
.pp-dash-rate-text strong { font-size: 0.75rem; display: block; }
.pp-dash-rate-text small { font-size: 0.65rem; color: var(--pp-text-muted); }

.pp-dash-list { list-style: none; margin: 0; padding: 0; }
.pp-dash-list-item {
    display: block;
    padding: 0.45rem 0.65rem;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none;
    color: inherit;
    transition: background 0.12s;
}
.pp-dash-list-item:last-child { border-bottom: none; }
.pp-dash-list-item:hover { background: #f8fafc; color: inherit; }
[data-bs-theme="dark"] .pp-dash-list-item:hover { background: rgba(255,255,255,0.04); }
.pp-dash-list-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.35rem;
    margin-bottom: 0.15rem;
}
.pp-dash-list-id {
    font-size: 0.72rem;
    font-weight: 700;
    color: #1e40af;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pp-dash-list-name {
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pp-dash-list-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem 0.65rem;
    font-size: 0.65rem;
    color: var(--pp-text-muted);
}
.pp-dash-list-meta i { font-size: 0.6rem; }
.pp-dash-badge {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.15rem 0.4rem;
    border-radius: 999px;
    flex-shrink: 0;
}
.pp-dash-badge.pending { background: #fef3c7; color: #b45309; }
.pp-dash-badge.approved { background: #d1fae5; color: #047857; }
.pp-dash-badge.rejected { background: #fee2e2; color: #b91c1c; }

.pp-dash-empty {
    text-align: center;
    padding: 1rem 0.5rem;
    color: var(--pp-text-muted);
    font-size: 0.75rem;
}
.pp-dash-empty i { font-size: 1.25rem; display: block; margin-bottom: 0.25rem; opacity: 0.45; }
.premium-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
}
.pp-eligibility-table th {
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    padding: 8px 8px !important;
    border-bottom: 2px solid #e2e8f0 !important;
}
.pp-eligibility-table td {
    font-size: 0.72rem !important;
    padding: 8px 8px !important;
    vertical-align: middle !important;
}
.pp-eligibility-table tr:hover {
    background-color: rgba(30, 64, 175, 0.02) !important;
}
.btn-schedule {
    font-size: 0.68rem !important;
    padding: 4px 10px !important;
    font-weight: 600 !important;
    letter-spacing: 0.2px;
    transition: all 0.2s ease;
}
.btn-schedule:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(30, 64, 175, 0.15);
}
</style>
@endpush

@section('content')
<div class="pp-dash-welcome">
    <div>
        <h2><i class="bi bi-geo-alt-fill me-1"></i>{{ $officer->district_name }} District</h2>
        <p>{{ $officer->name }} &middot; District Officer</p>
    </div>
    <span class="pp-dash-date"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}</span>
</div>

<div class="pp-dash-stats">
    <a href="{{ route('pp.officer.eligibility-list') }}" class="pp-dash-stat">
        <div class="pp-dash-stat-icon blue"><i class="bi bi-person-check"></i></div>
        <div class="min-w-0">
            <div class="pp-dash-stat-label">Awaiting Schedule</div>
            <div class="pp-dash-stat-value pp-counter" data-target="{{ $stats['awaiting_schedule'] }}">0</div>
        </div>
    </a>
    <a href="{{ route('pp.officer.possession-applications', ['status' => 'Visit Scheduled']) }}" class="pp-dash-stat">
        <div class="pp-dash-stat-icon purple"><i class="bi bi-calendar-event"></i></div>
        <div class="min-w-0">
            <div class="pp-dash-stat-label">Visits Scheduled</div>
            <div class="pp-dash-stat-value pp-counter" data-target="{{ $stats['scheduled'] }}">0</div>
        </div>
    </a>
    <a href="{{ route('pp.officer.possession-applications', ['status' => 'Physical Possession Submitted']) }}" class="pp-dash-stat">
        <div class="pp-dash-stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
        <div class="min-w-0">
            <div class="pp-dash-stat-label">Pending Verify</div>
            <div class="pp-dash-stat-value pp-counter" data-target="{{ $stats['submitted'] }}">0</div>
        </div>
    </a>
    <a href="{{ route('pp.officer.possession-applications', ['status' => 'Verified']) }}" class="pp-dash-stat">
        <div class="pp-dash-stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div class="min-w-0">
            <div class="pp-dash-stat-label">Verified</div>
            <div class="pp-dash-stat-value pp-counter" data-target="{{ $stats['verified'] }}">0</div>
        </div>
    </a>
</div>

<div class="pp-dash-actions">
    @if($stats['submitted'] > 0)
        <a href="{{ route('pp.officer.possession-applications', ['status' => 'Physical Possession Submitted']) }}" class="pp-dash-action warn">
            <i class="bi bi-exclamation-circle"></i> Review {{ $stats['submitted'] }} Submission{{ $stats['submitted'] !== 1 ? 's' : '' }}
        </a>
    @endif
</div>

<div class="card premium-card border-0 shadow-sm mt-3">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-person-check text-primary me-2"></i>Eligible Applicants Awaiting Schedule</h6>
                <p class="text-muted mb-0" style="font-size: 0.7rem;">Applicants with paid amount >= ₹40,000. Action required to propose slot dates.</p>
            </div>
            <form action="{{ route('pp.officer.dashboard') }}" method="GET" class="d-flex gap-2 align-items-center mb-0">
                <div class="input-group input-group-sm" style="max-width: 250px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" style="font-size: 0.72rem;" placeholder="Search name, mobile..." value="{{ $search ?? '' }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3 rounded-pill fw-bold" style="font-size: 0.7rem;">Search</button>
                @if($search)
                    <a href="{{ route('pp.officer.dashboard') }}" class="btn btn-outline-secondary btn-sm px-2 rounded-pill" style="font-size: 0.7rem;">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 pp-eligibility-table">
                <thead class="table-light text-uppercase text-muted">
                    <tr>
                        <th class="ps-3" style="width: 50px;">S.No.</th>
                        <th>Application No.</th>
                        <th>Applicant</th>
                        <th>Property</th>
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
                                <small class="text-muted text-uppercase tracking-wider font-monospace fs-9">PPP ID: {{ $p->PPPId ?? '—' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $p->PrivatePurchaserName }}</div>
                                <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $p->MobileNo }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-slate-700">{{ $p->AssetName }}</div>
                                <small class="text-muted">Size: {{ $p->AssetSize }} {{ $p->Unit }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-success">₹ {{ number_format($p->total_paid, 2) }}</div>
                                <small class="text-muted">Cost: ₹ {{ number_format($p->FlatCost, 2) }}</small>
                            </td>
                            <td>
                                @if($p->total_paid >= $p->FlatCost && $p->FlatCost > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2 py-1 rounded-pill" style="font-size: 0.65rem;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Fully Paid
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-20 px-2 py-1 rounded-pill" style="font-size: 0.65rem;">
                                        <i class="bi bi-cash-stack me-1"></i>Partially Paid
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($p->physical_possession_status)
                                    @php
                                        $badgeClass = match ($p->physical_possession_status) {
                                            'Eligible for Physical Possession' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-20',
                                            'Visit Scheduled' => 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-20',
                                            'Slot Selected' => 'bg-primary text-white border border-primary',
                                            'Physical Possession Submitted' => 'bg-primary text-white border border-primary',
                                            'Verified' => 'bg-success text-white border border-success shadow-sm',
                                            'Rejected' => 'bg-danger text-white border border-danger shadow-sm',
                                            default => 'bg-secondary text-white border border-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2 py-1 rounded-3" style="font-size: 0.65rem;">
                                        {{ $p->physical_possession_status }}
                                    </span>
                                @else
                                    <span class="text-muted small italic">Not Initiated</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                @if(!$p->application_secure_id)
                                    <button class="btn btn-outline-secondary btn-schedule text-nowrap rounded-pill" disabled>
                                        <i class="bi bi-slash-circle me-1"></i>Not Initiated
                                    </button>
                                @elseif($p->physical_possession_status === 'Eligible for Physical Possession')
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
                            <td colspan="8" class="text-center py-4 text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-people fs-2 mb-2 d-block text-slate-300"></i>
                                No eligible applicants awaiting schedule found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchasers->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $purchasers->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
