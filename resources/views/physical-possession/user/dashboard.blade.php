@extends('physical-possession.layouts.user')

@section('page-title', 'Dashboard')

@section('content')
<div class="pp-toolbar">
    <span class="pp-info-item"><i class="bi bi-person"></i> {{ $profile['name'] }}</span>
    <span class="pp-info-item"><i class="bi bi-geo-alt"></i> {{ $profile['district'] }}</span>
    <span class="pp-info-item"><i class="bi bi-phone"></i> {{ $profile['mobile'] }}</span>
    <div class="ms-auto d-flex flex-wrap gap-1">
        <a href="{{ route('pp.user.apply') }}" class="btn pp-btn-primary pp-btn-sm-compact"><i class="bi bi-plus-lg"></i> Apply</a>
        <a href="{{ route('pp.user.applications') }}" class="btn btn-outline-primary pp-btn-sm-compact">Applications</a>
        <a href="{{ route('pp.user.profile') }}" class="btn btn-outline-secondary pp-btn-sm-compact">Profile</a>
    </div>
</div>

<div class="pp-stat-strip">
    <span class="pp-stat-chip blue">Total <strong class="pp-counter" data-target="{{ $stats['total'] }}">0</strong></span>
    <span class="pp-stat-chip orange">Pending <strong class="pp-counter" data-target="{{ $stats['pending'] }}">0</strong></span>
    <span class="pp-stat-chip green">Approved <strong class="pp-counter" data-target="{{ $stats['approved'] }}">0</strong></span>
    <span class="pp-stat-chip red">Rejected <strong class="pp-counter" data-target="{{ $stats['rejected'] }}">0</strong></span>
</div>

<div class="pp-panel">
    <div class="pp-panel-head">
        <span>Recent Applications</span>
        <a href="{{ route('pp.user.apply') }}" class="btn pp-btn-primary pp-btn-sm-compact">+ New</a>
    </div>
    <div class="pp-panel-body p-0">
        @if($applications->count())
            <div class="table-responsive">
                <table class="table table-hover pp-table mb-0">
                    <thead>
                        <tr>
                            <th>Application No.</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                        <tr>
                            <td class="fw-semibold">{{ $app->application_number }}</td>
                            <td>{{ $app->created_at->format('d M Y') }}</td>
                            <td><span class="badge bg-{{ $app->statusBadgeClass() }}">{{ ucfirst($app->status) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('pp.user.application.show', $app) }}" class="btn btn-outline-primary pp-btn-sm-compact">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="pp-empty">
                <i class="bi bi-inbox"></i>
                <p class="mb-2">No applications yet.</p>
                <a href="{{ route('pp.user.apply') }}" class="btn pp-btn-primary pp-btn-sm-compact">Submit First Application</a>
            </div>
        @endif
    </div>
</div>
@endsection
