@extends('physical-possession.layouts.officer')

@section('page-title', 'Users')

@section('content')
<div class="pp-panel">
    <div class="pp-panel-head">Users — {{ $officer->district_name }} District</div>
    <div class="pp-panel-body p-0">
        @if($users->count())
            <div class="table-responsive">
                <table class="table table-hover pp-table mb-0" id="usersTable">
                    <thead><tr><th class="text-center" style="width:50px">S.No</th><th>Name</th><th>Mobile</th></tr></thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="text-center text-muted"></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->mobile ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="pp-empty">No users found.</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#usersTable').length) {
            $('#usersTable').DataTable({
                pageLength: 20,
                dom: 'ftip',
                columnDefs: [
                    { orderable: false, searchable: false, targets: 0 }
                ],
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
