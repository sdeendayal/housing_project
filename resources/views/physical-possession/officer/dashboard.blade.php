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

@if($eligibleCount > 0)
<div class="alert alert-primary d-flex align-items-center justify-content-between p-3 border-0 shadow-sm rounded-3 mb-3 bg-primary bg-opacity-10 text-primary-emphasis border border-primary border-opacity-20">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary text-white p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-person-check fs-4"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-dark">Eligible Applicants Awaiting Schedule</h6>
            <p class="text-muted small mb-0">There are <strong>{{ $eligibleCount }}</strong> property purchasers in your district who have completed ₹40,000 payment and are eligible for physical possession.</p>
        </div>
    </div>
    <a href="{{ route('pp.officer.eligibility-list') }}" class="btn btn-primary rounded-pill px-4 btn-sm fs-8 fw-bold">View Eligibility List</a>
</div>
@endif

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
    <a href="{{ route('pp.officer.reports') }}" class="pp-dash-action"><i class="bi bi-bar-chart-line"></i> Reports</a>
</div>

<div class="pp-dash-grid">
    <div class="pp-dash-panel">
        <div class="pp-dash-panel-head">
            Last 7 Days
            <span>{{ $weekTotal }} application{{ $weekTotal !== 1 ? 's' : '' }}</span>
        </div>
        <div class="pp-dash-panel-body">
            <div class="pp-dash-chart"><canvas id="ppChart"></canvas></div>
        </div>
    </div>

    <div class="pp-dash-side-stack">
        <div class="pp-dash-panel">
            <div class="pp-dash-panel-head">Status Breakdown</div>
            <div class="pp-dash-breakdown">
                @php $maxStat = max($stats['scheduled'] + $stats['submitted'] + $stats['verified'], 1); @endphp
                <div class="pp-dash-bar-row">
                    <span class="pp-dash-bar-label">Scheduled</span>
                    <div class="pp-dash-bar-track"><div class="pp-dash-bar-fill purple" style="width:{{ round(($stats['scheduled'] / $maxStat) * 100) }}%; background: linear-gradient(90deg, #c084fc, #9333ea);"></div></div>
                    <span class="pp-dash-bar-num">{{ $stats['scheduled'] }}</span>
                </div>
                <div class="pp-dash-bar-row">
                    <span class="pp-dash-bar-label">Submitted</span>
                    <div class="pp-dash-bar-track"><div class="pp-dash-bar-fill orange" style="width:{{ round(($stats['submitted'] / $maxStat) * 100) }}%"></div></div>
                    <span class="pp-dash-bar-num">{{ $stats['submitted'] }}</span>
                </div>
                <div class="pp-dash-bar-row">
                    <span class="pp-dash-bar-label">Verified</span>
                    <div class="pp-dash-bar-track"><div class="pp-dash-bar-fill green" style="width:{{ round(($stats['verified'] / $maxStat) * 100) }}%"></div></div>
                    <span class="pp-dash-bar-num">{{ $stats['verified'] }}</span>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection

@push('scripts')
<script>
    const ppChartCtx = document.getElementById('ppChart');
    if (ppChartCtx) {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        new Chart(ppChartCtx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Applications',
                    data: @json($chartData),
                    backgroundColor: 'rgba(59, 130, 246, 0.75)',
                    hoverBackgroundColor: 'rgba(37, 99, 235, 0.9)',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 28,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#fff',
                        titleColor: isDark ? '#f1f5f9' : '#1e293b',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 8,
                        titleFont: { size: 11 },
                        bodyFont: { size: 11 },
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 9 }, color: isDark ? '#94a3b8' : '#64748b' },
                        grid: { color: isDark ? 'rgba(255,255,255,0.06)' : '#f1f5f9' },
                        border: { display: false }
                    },
                    x: {
                        ticks: { font: { size: 9 }, color: isDark ? '#94a3b8' : '#64748b' },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }
</script>
@endpush
