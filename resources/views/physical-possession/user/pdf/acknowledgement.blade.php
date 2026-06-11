<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
        .header h2 { color: #1e40af; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        td, th { border: 1px solid #333; padding: 8px; }
        th { background: #e8eef9; width: 35%; }
        .status { font-size: 14px; font-weight: bold; color: #d97706; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Acknowledgement Slip</h2>
        <p>Physical Possession Application</p>
    </div>

    <table>
        <tr><th>Slip ID</th><td>{{ $application->slip_id }}</td></tr>
        <tr><th>Application Number</th><td><strong>{{ $application->application_number }}</strong></td></tr>
        <tr><th>Applicant Name</th><td>{{ $application->applicant_name }}</td></tr>
        <tr><th>District</th><td>{{ $application->district_name ?? '—' }}</td></tr>
        <tr><th>Mobile</th><td>{{ $application->mobile }}</td></tr>
        <tr><th>Submission Date</th><td>{{ $application->created_at->format('d M Y') }}</td></tr>
        <tr><th>Current Status</th><td class="status">{{ ucfirst($application->status) }}</td></tr>
    </table>

    <p style="margin-top:30px;font-size:10px;">This is a computer generated acknowledgement slip. No signature required.</p>
</body>
</html>
