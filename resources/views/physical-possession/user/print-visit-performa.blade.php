<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visit Performa - {{ $application->application_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        .header { border-bottom: 3px solid #1e40af; padding-bottom: 15px; }
        .visit-highlight {
            border: 2px solid #1e40af;
            background: #eff6ff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .visit-date { font-size: 1.75rem; font-weight: 800; color: #1e40af; }
        .visit-instructions {
            margin-top: 1rem;
            padding-top: 0.85rem;
            border-top: 1px solid #93c5fd;
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.6;
        }
        .visit-instructions-label {
            display: block;
            font-size: 0.95rem;
            font-weight: 800;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 0.35rem;
        }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <a href="{{ route('pp.user.application.show', $application) }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="header text-center mb-4">
            <h4 class="fw-bold text-primary mb-1">Citizen Visit Performa</h4>
            <p class="mb-0 text-muted">Physical Possession — MMSAY | Government of Haryana</p>
        </div>

        <table class="table table-bordered">
            <tr><th width="35%">Physical Possession Application No.</th><td><strong>{{ $application->application_number }}</strong></td></tr>
            <tr><th>Slip ID</th><td>{{ $application->slip_id }}</td></tr>
            <tr><th>Applicant Name</th><td>{{ $application->applicant_name }}</td></tr>
            <tr><th>Mobile</th><td>{{ $application->mobile }}</td></tr>
            <tr><th>District</th><td>{{ $application->district_name ?? '—' }}</td></tr>
            <tr><th>Status</th><td><strong>{{ ucfirst($application->status) }}</strong></td></tr>
            @if($application->officerAction?->officer?->name)
            <tr><th>Officer</th><td>{{ $application->officerAction->officer->name }}</td></tr>
            @endif
            @if($application->remarks)
            <tr><th>Officer Remarks</th><td>{{ $application->remarks }}</td></tr>
            @endif
        </table>

        <div class="visit-highlight">
            <p class="mb-1 fw-bold text-primary">You are requested to visit on</p>
            <div class="visit-date">{{ $application->citizen_visit_date->format('d M Y') }}</div>
            <div class="text-primary fw-bold">{{ $application->citizen_visit_date->format('h:i a') }} to {{ $application->citizen_visit_date->copy()->addHour()->format('h:i a') }}</div>
            @if($application->visit_instructions)
            <p class="visit-instructions mb-0">
                <span class="visit-instructions-label">Instructions</span>
                {{ $application->visit_instructions }}
            </p>
            @else
            <p class="visit-instructions mb-0">
                <span class="visit-instructions-label">Instructions</span>
                Please visit the Municipal Office with original documents and a copy of this performa.
            </p>
            @endif
        </div>

        <p class="text-muted small">Computer-generated performa. Please carry valid ID proof on the visit date.</p>
    </div>
</body>
</html>
