@extends('physical-possession.layouts.officer')

@section('page-title', 'Users')

@section('content')
<div class="pp-panel">
    <div class="pp-panel-head">Users — {{ $officer->district_name }} District</div>
    <div class="pp-panel-body p-0">
        @if($users->count())
            <div class="table-responsive">
                <table class="table table-hover pp-table mb-0" id="usersTable">
                    <thead><tr><th>Name</th><th>Mobile</th><th>Email</th></tr></thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->mobile ?? '—' }}</td>
                            <td>{{ $user->email ?? '—' }}</td>
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
        if ($('#usersTable').length) $('#usersTable').DataTable({ pageLength: 15, dom: 'ftip' });
    });
</script>
@endpush
