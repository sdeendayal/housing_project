<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registry Yet To Be Done</title>

    <style>
        @page {
            size: landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            margin: 0;
            background: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        h1 {
            margin: 0;
            font-size: 22px;
        }

        .subtitle {
            margin-top: 5px;
            color: #6b7280;
            font-size: 11px;
        }

        .meta {
            text-align: right;
            font-size: 11px;
            color: #6b7280;
        }

        .summary {
            margin-bottom: 14px;
            font-size: 12px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 7px 6px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #e5e7eb;
            padding: 7px 6px;
            font-size: 9px;
            vertical-align: top;
            word-break: break-word;
        }

        .pending {
            font-weight: bold;
            color: #b45309;
        }

        .muted {
            color: #6b7280;
            font-size: 8px;
            margin-top: 2px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <div>
            <h1>Registry Yet To Be Done</h1>
            <div class="subtitle">
                Applicants whose registry is still pending
            </div>
        </div>

        <div class="meta">
            Generated: {{ now()->format('d-m-Y h:i A') }}<br>
            Total: {{ number_format($registryYetDone->count()) }}
        </div>
    </div>

    <div class="summary">
        Filters:
        Phase = {{ request('phase') ?: 'All' }},
        District = {{ request('district_id') ?: 'All' }},
        Block = {{ request('block_id') ?: 'All' }},
        Village = {{ request('village_id') ?: 'All' }},
        Search = {{ request('search') ?: 'All' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:13%">Application</th>
                <th style="width:17%">Applicant</th>
                <th style="width:11%">Mobile</th>
                <th style="width:22%">Location</th>
                <th style="width:8%">Phase</th>
                <th style="width:11%">Flat</th>
                <th style="width:8%">Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($registryYetDone as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td>
                        {{ $row->RegistrationNo ?? '-' }}
                        <div class="muted">
                            Owner ID: {{ $row->OwnerId ?? '-' }}
                        </div>
                    </td>

                    <td>
                        {{ $row->OwnerName ?? '-' }}
                        <div class="muted">
                            {{ $row->FatherHusbandName ?? '-' }}
                        </div>
                    </td>

                    <td>
                        {{ $row->MobileNo ?? '-' }}
                    </td>

                    <td>
                        {{ $row->VillageName ?? '-' }}
                        <div class="muted">
                            {{ $row->BlockName ?? '-' }},
                            {{ $row->DistrictName ?? '-' }}
                        </div>
                    </td>

                    <td>{{ $row->Phase ?? '-' }}</td>

                    <td>
                        {{ $row->FlatNo ?? '-' }}
                        <div class="muted">
                            Flat ID: {{ $row->FlatId ?? '-' }}
                        </div>
                    </td>

                    <td class="pending">PENDING</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:25px;">
                        No pending registry records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
