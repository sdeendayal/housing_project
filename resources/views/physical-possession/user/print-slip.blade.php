<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Acknowledgement Slip - {{ $application->application_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        .slip-header { border-bottom: 3px solid #1e40af; padding-bottom: 15px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <a href="{{ route('pp.user.success', $application) }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="slip-header text-center mb-4">
            <h4 class="fw-bold text-primary">Physical Possession Application Acknowledgement</h4>
            <p class="mb-0">Government Housing Portal</p>
        </div>

        <table class="table table-bordered">
            <tr><th width="35%">Slip ID</th><td>{{ $application->slip_id }}</td></tr>
            <tr><th>Application Number</th><td><strong>{{ $application->application_number }}</strong></td></tr>
            <tr><th>Applicant Name</th><td>{{ $application->applicant_name }}</td></tr>
            <tr><th>Father Name</th><td>{{ $application->father_name ?? '—' }}</td></tr>
            <tr><th>Mobile</th><td>{{ $application->mobile }}</td></tr>
            <tr><th>District</th><td>{{ $application->district_name ?? '—' }}</td></tr>
            <tr><th>Submission Date</th><td>{{ $application->created_at->format('d M Y') }}</td></tr>
            <tr><th>Current Status</th><td><strong>{{ ucfirst($application->status) }}</strong></td></tr>
        </table>

        <p class="text-muted small mt-4">
            This acknowledgement slip is proof of your application. Please save this number for future reference.
        </p>
    </div>
</body>
</html>
