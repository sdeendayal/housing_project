<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Physical Possession Records</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font-family: Arial, sans-serif; font-size: 9px; }
        .toolbar { display: flex; justify-content: space-between; margin-bottom: 12px; }
        .toolbar a, .toolbar button { border: 0; border-radius: 6px; padding: 8px 12px; color: white; background: #334155; text-decoration: none; cursor: pointer; }
        h1 { margin: 0; font-size: 18px; }
        p { margin: 3px 0 0; color: #64748b; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; color: #475569; }
        th, td { padding: 6px; border: 1px solid #dbe2ea; vertical-align: top; }
        .right { text-align: right; }
        .status { font-weight: bold; text-transform: capitalize; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h1>Physical Possession Records</h1>
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
                <th>S.No.</th><th>Possession ID</th><th>Asset</th>
                <th>Applicant</th><th>Mobile</th><th>Location</th>
                <th class="right">Received Amount</th><th>Schedule</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applications as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ ($row->physical_application_number ?? null)
                            ?: (($row->possession_id ?? null) ?: '-') }}
                    </td>
                    <td>
                        #{{ $row->asset_id ?? '-' }}<br>
                        {{ $row->asset_name ?? '-' }}
                    </td>
                    <td>
                        {{ $row->applicant_name ?? '-' }}<br>
                        App: {{ ($row->purchaser_application_number ?? null) ?: '-' }}
                    </td>
                    <td>{{ ($row->mobile ?? null) ?: '-' }}</td>
                    <td>
                        {{ ($row->district_name ?? null) ?: '-' }}<br>
                        {{ ($row->city_name ?? null) ?: '-' }} /
                        {{ ($row->sector_name ?? null) ?: '-' }}
                    </td>
                    <td class="right">₹{{ number_format($row->received_amount ?? 0, 2) }}</td>
                    <td>
                        {{ ($row->possession_date ?? null) ?: '-' }}<br>
                        {{ ($row->meeting_slot ?? null) ?: '-' }}
                    </td>
                    <td class="status">
                        {{ str_replace('_', ' ', $row->workflow_status ?? 'awaiting_schedule') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>