<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Registration Report</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; color: #0f172a; font-family: Arial, sans-serif; font-size: 10px; }
        .actions { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin: 12px 0; }
        .action-group { display: flex; align-items: center; gap: 8px; }
        .button { border: 0; border-radius: 6px; background: #1e293b; color: #fff; padding: 8px 14px; text-decoration: none; cursor: pointer; }
        h1 { margin: 0 0 4px; font-size: 18px; }
        .meta { margin-bottom: 12px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; vertical-align: top; word-break: break-word; }
        th { background: #f1f5f9; font-size: 9px; text-transform: uppercase; }
        .amount { text-align: right; white-space: nowrap; }
        .next { margin-top: 12px; text-align: right; }
        @page { size: A4 landscape; margin: 8mm; }
        @media print { .no-print { display: none !important; } body { font-size: 8px; } th, td { padding: 4px; } }
    </style>
</head>
<body>
    @php
        $propertyDetailsText = static function ($value): string {
            $decoded = json_decode((string) $value, true);
            if (!is_array($decoded)) return trim((string) $value) ?: '-';

            $parts = [];
            foreach ($decoded as $key => $item) {
                if ($key === 'id' || $item === null || $item === '') continue;
                $label = ucwords(str_replace(['_', '-'], ' ', (string) $key));
                $displayValue = is_array($item)
                    ? implode(', ', array_filter(array_map('strval', $item)))
                    : (string) $item;
                if ($displayValue !== '') $parts[] = $label . ': ' . $displayValue;
            }
            return $parts ? implode(' | ', $parts) : '-';
        };

        $nextPrintUrl = $hasMore && $nextAfterId
            ? route('old-registrations.print', array_merge(
                request()->except('after_id'),
                ['after_id' => $nextAfterId]
            ))
            : null;
    @endphp

    <div class="actions no-print">
        <a class="button" href="{{ route('old-registrations.index', request()->except('after_id')) }}">Back</a>
        <div class="action-group">
            <button class="button" onclick="window.print()">Print This 1,000</button>
            @if ($nextPrintUrl)
                <a class="button" href="{{ $nextPrintUrl }}">Next 1,000 Records</a>
            @endif
        </div>
    </div>

    <h1>Property Registration Report</h1>
    <div class="meta">Generated: {{ now()->format('d M Y, h:i A') }} · Records in this chunk: {{ $registrations->count() }}</div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">S.No.</th>
                <th style="width:10%">Application</th>
                <th style="width:15%">Applicant</th>                
                <th style="width:15%">Location</th>
                <th style="width:10%">Family/Member</th>
                <th style="width:10%">Profile</th>
                <th style="width:10%">Occupation</th>
                <th style="width:13%">Registration Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registrations as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->application_number ?: '-' }}<br>Record #{{ $row->id }}</td>
                    <td><strong>{{ $row->fullName ?: '-' }}</strong><br>{{ $row->mobileNo ?: '-' }}<br>Father: {{ $row->fatherFullName ?: '-' }}</td>
                   
                    <td>{{ $row->districtName ?: '-' }}<br>{{ $row->btName ?: '-' }} / {{ $row->wvName ?: '-' }}</td>
                    <td>Family: {{ $row->family_id ?: '-' }}<br>Member: {{ $row->memberID ?: '-' }}</td>
                    <td>{{ $row->gender ?: '-' }} · Age {{ $row->age ?: '-' }}<br>{{ $row->casteCategoryName ?: '-' }}</td>
                    <td>{{ $row->occupationName ?: '-' }}<br>Income: {{ $row->familyIncome ?: '-' }}</td>
                    <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="actions no-print">
        <a class="button" href="{{ route('old-registrations.index', request()->except('after_id')) }}">Back</a>
        <div class="action-group">
            <button class="button" onclick="window.print()">Print This 1,000</button>
            @if ($nextPrintUrl)
                <a class="button" href="{{ $nextPrintUrl }}">Next 1,000 Records</a>
            @endif
        </div>
    </div>
</body>
</html>