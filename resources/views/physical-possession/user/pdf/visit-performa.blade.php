<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { color: #1e40af; margin: 0 0 4px; font-size: 18px; }
        .header p { margin: 0; font-size: 11px; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td, th { border: 1px solid #333; padding: 8px; vertical-align: top; }
        th { background: #e8eef9; width: 38%; text-align: left; }
        .visit-box {
            margin-top: 18px;
            padding: 14px;
            border: 2px solid #1e40af;
            background: #eff6ff;
            text-align: center;
        }
        .visit-box h3 { margin: 0 0 6px; color: #1e40af; font-size: 14px; }
        .visit-date { font-size: 20px; font-weight: bold; color: #0f172a; margin: 6px 0; }
        .visit-time { font-size: 16px; font-weight: bold; color: #1e40af; margin: 4px 0; }
        .visit-instructions {
            margin: 12px 0 0;
            padding-top: 10px;
            border-top: 1px solid #93c5fd;
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.6;
            text-align: center;
        }
        .visit-instructions-label {
            display: block;
            font-size: 13px;
            color: #1e40af;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .note { margin-top: 18px; font-size: 10px; color: #475569; line-height: 1.5; }
        .status-approved { color: #059669; font-weight: bold; }
        .status-rejected { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Citizen Visit Performa</h2>
        <p>Physical Possession — Mukhya Mantri Shehri Awas Yojana (MMSAY)</p>
        <p>Government of Haryana</p>
    </div>

    <table>
        <tr><th>Application Number</th><td><strong>{{ $application->application_number }}</strong></td></tr>
        <tr><th>Slip ID</th><td>{{ $application->slip_id }}</td></tr>
        <tr><th>Applicant Name</th><td>{{ $application->applicant_name }}</td></tr>
        <tr><th>Father Name</th><td>{{ $application->father_name ?? '—' }}</td></tr>
        <tr><th>Mobile</th><td>{{ $application->mobile }}</td></tr>
        <tr><th>District</th><td>{{ $application->district_name ?? '—' }}</td></tr>
        <tr><th>Application Status</th>
            <td class="{{ $application->status === 'approved' ? 'status-approved' : 'status-rejected' }}">
                {{ ucfirst($application->status) }}
            </td>
        </tr>
        <tr><th>Officer</th><td>{{ $application->officerAction?->officer?->name ?? '—' }}</td></tr>
        <tr><th>Officer Remarks</th><td>{{ $application->remarks ?? '—' }}</td></tr>
    </table>

    <div class="visit-box">
        <h3>Scheduled Visit — Please Report On</h3>
        <div class="visit-date">{{ $application->citizen_visit_date->format('d M Y') }}</div>
        <div class="visit-time">{{ $application->citizen_visit_date->format('h:i A') }}</div>
        @if($application->visit_instructions)
        <p class="visit-instructions">
            <span class="visit-instructions-label">Instructions</span>
            {{ $application->visit_instructions }}
        </p>
        @else
        <p class="visit-instructions">
            <span class="visit-instructions-label">Instructions</span>
            Please visit the Municipal Office / concerned department on the above date with your original documents and a copy of this performa.
        </p>
        @endif
    </div>

    <p class="note">
        This is a computer-generated visit performa. The applicant is advised to report on the scheduled date with valid identity proof and application-related documents.
        For queries, contact the district municipal office.
    </p>

    <p style="margin-top:24px; font-size:10px;">Generated on {{ now()->format('d M Y') }}</p>
</body>
</html>
