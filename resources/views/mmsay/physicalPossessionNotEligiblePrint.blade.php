<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Not Eligible Physical Possession</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font: 9px Arial, sans-serif; }
        .toolbar { display: flex; justify-content: space-between; margin-bottom: 12px; }
        .toolbar a, .toolbar button { border: 0; border-radius: 6px; padding: 8px 12px; color: #fff; background: #334155; text-decoration: none; }
        h1 { margin: 0; font-size: 18px; }
        p { margin: 3px 0 0; color: #64748b; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #dbe2ea; vertical-align: top; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; }
        .right { text-align: right; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h1>Physical Possession Not Eligible</h1>
            <p>{{ $applications->count() }} records in this print chunk</p>
        </div>
        <div>
            @if ($hasMore)
                <a href="{{ request()->fullUrlWithQuery(['after_id' => $nextAfterId]) }}">Next 500 Records</a>
            @endif
            <button onclick="window.print()">Print This Chunk</button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No.</th><th>Asset</th><th>Application</th><th>Applicant</th>
                <th>Mobile</th><th>Location</th><th class="right">Received</th>
                <th class="right">Required to ₹60,000</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applications as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>#{{ $row->asset_id }}<br>{{ $row->asset_name ?: '-' }}</td>
                    <td>{{ $row->application_number ?: '-' }}</td>
                    <td>{{ $row->applicant_name ?: 'Not allotted' }}</td>
                    <td>{{ $row->mobile ?: '-' }}</td>
                    <td>{{ $row->district_name ?: '-' }}<br>{{ $row->city_name ?: '-' }} / {{ $row->sector_name ?: '-' }}</td>
                    <td class="right">₹{{ number_format($row->received_amount, 2) }}</td>
                    <td class="right">₹{{ number_format($row->eligibility_shortfall, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>