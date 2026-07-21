<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Allotment Report</title>

    <style>
        @page {
            margin: 18px 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1e293b;
        }

        .header {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #334155;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            padding: 0;
        }

        .title {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }

        .subtitle {
            margin-top: 4px;
            font-size: 9px;
            color: #64748b;
        }

        .date {
            text-align: right;
            font-size: 9px;
            color: #475569;
        }

        .filter-box {
            margin-bottom: 12px;
            padding: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .filter-title {
            margin-bottom: 5px;
            font-weight: bold;
            color: #334155;
        }

        .filter-item {
            display: inline-block;
            margin-right: 12px;
            margin-bottom: 3px;
        }

        .warning {
            margin-bottom: 10px;
            padding: 7px;
            border: 1px solid #f59e0b;
            background: #fffbeb;
            color: #92400e;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.report th {
            padding: 6px 4px;
            border: 1px solid #94a3b8;
            background: #e2e8f0;
            color: #0f172a;
            font-size: 7px;
            text-align: left;
        }

        table.report td {
            padding: 5px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            word-wrap: break-word;
        }

        table.report tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .status {
            font-weight: bold;
        }

        .footer {
            margin-top: 10px;
            text-align: right;
            font-size: 7px;
            color: #64748b;
        }

        .w-sr {
            width: 4%;
        }

        .w-application {
            width: 10%;
        }

        .w-name {
            width: 13%;
        }

        .w-mobile {
            width: 8%;
        }

        .w-location {
            width: 18%;
        }

        .w-phase {
            width: 6%;
        }

        .w-plot {
            width: 8%;
        }

        .w-status {
            width: 12%;
        }
    </style>
</head>

<body>

    @php
        $statusLabels = [
            'approved_paid' => 'Approved & Paid',
            'approved_unpaid' => 'Approved & Unpaid',
            'pending' => 'Yet to be Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="title">Allotment Report</h1>

                    <div class="subtitle">
                        MMGAY Super Admin Dashboard
                    </div>
                </td>

                <td class="date">
                    Generated:
                    {{ now()->format('d-m-Y h:i A') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="filter-box">
        <div class="filter-title">Applied Filters</div>

        <span class="filter-item">
            Phase:
            <strong>{{ $filters['phase'] ?: 'All' }}</strong>
        </span>

        <span class="filter-item">
            District ID:
            <strong>{{ $filters['district_id'] ?: 'All' }}</strong>
        </span>

        <span class="filter-item">
            Block ID:
            <strong>{{ $filters['block_id'] ?: 'All' }}</strong>
        </span>

        <span class="filter-item">
            Village ID:
            <strong>{{ $filters['village_id'] ?: 'All' }}</strong>
        </span>

        <span class="filter-item">
            Status:
            <strong>
                {{ $statusLabels[$filters['status']] ?? 'All' }}
            </strong>
        </span>

        <span class="filter-item">
            Search:
            <strong>{{ $filters['search'] ?: 'None' }}</strong>
        </span>
    </div>

    @if ($totalRecords > $pdfLimit)
        <div class="warning">
            Total filtered records: {{ number_format($totalRecords) }}.
            PDF performance ke liye first
            {{ number_format($pdfLimit) }} records include kiye gaye hain.
            Complete records Excel export me available hain.
        </div>
    @endif

    <table class="report">
        <thead>
            <tr>
                <th class="w-sr">Sr.</th>
                <th class="w-application">Application No.</th>
                <th class="w-name">Applicant</th>
                <th class="w-mobile">Mobile</th>
                <th class="w-location">Location</th>
                <th class="w-phase">Phase</th>
                <th class="w-plot">Plot</th>
                <th class="w-status">Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($allotments as $allotment)

                @php
                    if ((int) ($allotment->IsAllotmentCancelled ?? 0) === 1) {
                        $status = 'Cancelled';
                    } elseif ((int) ($allotment->IsRejected ?? 0) === 1) {
                        $status = 'Rejected';
                    } elseif (
                        (int) ($allotment->IsApproved ?? 0) === 1 &&
                        (int) ($allotment->IsPaid ?? 0) === 1
                    ) {
                        $status = 'Approved & Paid';
                    } elseif ((int) ($allotment->IsApproved ?? 0) === 1) {
                        $status = 'Approved & Unpaid';
                    } else {
                        $status = 'Yet to be Approved';
                    }
                @endphp

                <tr>
                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $allotment->RegistrationNo ?? '-' }}
                    </td>

                    <td>
                        <strong>
                            {{ $allotment->OwnerName ?? '-' }}
                        </strong>

                        <br>

                        {{ $allotment->FatherHusbandName ?? '-' }}
                    </td>

                    <td>
                        {{ $allotment->MobileNo ?? '-' }}
                    </td>

                    <td>
                        {{ $allotment->VillageName ?? '-' }},
                        {{ $allotment->BlockName ?? '-' }},
                        {{ $allotment->DistrictName ?? '-' }}
                    </td>

                    <td>
                        {{ $allotment->Phase ?? '-' }}
                    </td>

                    <td>
                        {{ $allotment->FlatNo ?? '-' }}
                    </td>

                    <td class="status">
                        {{ $status }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="8" class="text-center">
                        No allotment records found.
                    </td>
                </tr>

            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total displayed records:
        {{ number_format($allotments->count()) }}
    </div>

</body>

</html>