@extends('physical-possession.layouts.officer')

@section('page-title', 'Reports')

@section('content')
<div class="row g-2">
    <div class="col-lg-8">
        <div class="pp-panel">
            <div class="pp-panel-head">Monthly Report — {{ $officer->district_name }}</div>
            <div class="pp-panel-body">
                <div class="pp-chart-wrap" style="height:200px"><canvas id="reportChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="pp-panel">
            <div class="pp-panel-head">Monthly Summary</div>
            <div class="pp-panel-body p-0">
                @foreach($monthlyStats as $month)
                <div class="px-2 py-2 border-bottom small">
                    <strong>{{ $month['label'] }}</strong>
                    <div class="d-flex justify-content-between text-muted mt-1">
                        <span>Total: {{ $month['total'] }}</span>
                        <span class="text-success">✓ {{ $month['approved'] }}</span>
                        <span class="text-danger">✗ {{ $month['rejected'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('reportChart'), {
        type: 'line',
        data: {
            labels: @json(collect($monthlyStats)->pluck('label')),
            datasets: [
                { label: 'Total', data: @json(collect($monthlyStats)->pluck('total')), borderColor: '#3b82f6', tension: 0.3 },
                { label: 'Approved', data: @json(collect($monthlyStats)->pluck('approved')), borderColor: '#10b981', tension: 0.3 },
                { label: 'Rejected', data: @json(collect($monthlyStats)->pluck('rejected')), borderColor: '#ef4444', tension: 0.3 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { boxWidth: 10, font: { size: 10 } } } },
            scales: { y: { beginAtZero: true, ticks: { font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } }
        }
    });
</script>
@endpush
