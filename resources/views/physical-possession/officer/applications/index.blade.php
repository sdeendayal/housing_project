@extends('physical-possession.layouts.officer')

@section('page-title', ucfirst($status === 'all' ? 'All' : $status).' Applications')

@section('content')
<div class="pp-toolbar mb-2 py-2">
    <a href="{{ route('pp.officer.applications') }}" class="btn pp-btn-sm-compact {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
    <a href="{{ route('pp.officer.applications') }}?status=pending" class="btn pp-btn-sm-compact {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">Pending</a>
    <a href="{{ route('pp.officer.applications.approved') }}" class="btn pp-btn-sm-compact {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }}">Approved</a>
    <a href="{{ route('pp.officer.applications.rejected') }}" class="btn pp-btn-sm-compact {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Rejected</a>
</div>

<div class="pp-panel">
    <div class="pp-panel-body p-0">
        @if($applications->count())
            <div class="table-responsive">
                <table class="table table-hover pp-table mb-0" id="officerAppsTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:50px">S.No</th>
                            <th>Application No.</th>
                            <th>Applicant</th>
                            <th>Mobile</th>
                            <th>District</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                        <tr>
                            <td class="text-center text-muted"></td>
                            <td class="fw-semibold">{{ $app->application_number }}</td>
                            <td>{{ $app->applicant_name }}</td>
                            <td>{{ $app->mobile }}</td>
                            <td>{{ $app->district_name ?? '—' }}</td>
                            <td>{{ $app->created_at->format('d M Y') }}</td>
                            <td><span class="badge bg-{{ $app->statusBadgeClass() }}">{{ ucfirst($app->status) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('pp.officer.application.show', $app) }}" class="btn btn-primary pp-btn-sm-compact">Review</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="pp-empty">No applications in this category.</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#officerAppsTable').length) {
            $('#officerAppsTable').DataTable({
                order: [[5, 'desc']],
                pageLength: 15,
                dom: 'ftip',
                columnDefs: [{ orderable: false, searchable: false, targets: 0 }],
                drawCallback: function() {
                    var api = this.api();
                    api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1 + api.page() * api.page.len();
                    });
                }
            });
        }
    });
</script>
@endpush
