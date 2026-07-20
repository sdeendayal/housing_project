<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #eeeeee;
        }
    </style>
</head>

<body>

    <h2 style="text-align: center;">
        Village Wise Report
    </h2>

    <table>
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Village</th>
                <th>Phase</th>
                <th>Total Plots</th>
                <th>Registered</th>
                <th>Allotted</th>
                <th>Approved Paid</th>
                <th>Approved Unpaid</th>
                <th>Pending</th>
                <th>Rejected</th>
                <th>Cancelled</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($report as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->VillageName ?? '-' }}</td>
                    <td>{{ $row->Phase ?? '-' }}</td>
                    <td>{{ $row->TotalPlots ?? 0 }}</td>
                    <td>{{ $row->RegisteredBeneficiaries ?? 0 }}</td>
                    <td>{{ $row->AllottedBeneficiaries ?? 0 }}</td>
                    <td>{{ $row->ApprovedPaid ?? 0 }}</td>
                    <td>{{ $row->ApprovedUnpaid ?? 0 }}</td>
                    <td>{{ $row->PendingApprovalPayment ?? 0 }}</td>
                    <td>{{ $row->Rejected ?? 0 }}</td>
                    <td>{{ $row->AllotmentCancelled ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">
                        No records found
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <th colspan="3">Gross Total</th>
                <th>{{ $grossTotal->TotalPlots ?? 0 }}</th>
                <th>{{ $grossTotal->RegisteredBeneficiaries ?? 0 }}</th>
                <th>{{ $grossTotal->AllottedBeneficiaries ?? 0 }}</th>
                <th>{{ $grossTotal->ApprovedPaid ?? 0 }}</th>
                <th>{{ $grossTotal->ApprovedUnpaid ?? 0 }}</th>
                <th>{{ $grossTotal->PendingApprovalPayment ?? 0 }}</th>
                <th>{{ $grossTotal->Rejected ?? 0 }}</th>
                <th>{{ $grossTotal->AllotmentCancelled ?? 0 }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>