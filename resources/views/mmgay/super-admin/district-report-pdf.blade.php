<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>District Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th {
            background: #e9e9e9;
            text-align: center;
            padding: 6px;
        }

        td {
            padding: 5px;
            text-align: center;
        }

        tfoot td {
            font-weight: bold;
            background: #f2f2f2;
        }
    </style>
</head>

<body>

    <h2>District Wise Report</h2>

    <table>
        <thead>
            <tr>
                <th>District</th>
                <th>Villages</th>
                <th>Applicants</th>
                <th>Allotted</th>
                <th>Approved & Paid</th>
                <th>Approved & Unpaid</th>
                <th>Yet to be Approved</th>
                <th>Rejected</th>
                <th>Allotment Cancelled</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($report as $row)
                <tr>
                    <td>{{ $row->DistrictName }}</td>
                    <td>{{ $row->VillagesWithPlots }}</td>
                    <td>{{ $row->RegisteredBeneficiaries }}</td>
                    <td>{{ $row->AllottedBeneficiaries }}</td>
                    <td>{{ $row->ApprovedPaid }}</td>
                    <td>{{ $row->ApprovedUnpaid }}</td>
                    <td>{{ $row->PendingApprovalPayment }}</td>
                    <td>{{ $row->Rejected }}</td>
                    <td>{{ $row->AllotmentCancelled }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td>Gross Total</td>
                <td>{{ $grossTotal->VillagesWithPlots }}</td>
                <td>{{ $grossTotal->RegisteredBeneficiaries }}</td>
                <td>{{ $grossTotal->AllottedBeneficiaries }}</td>
                <td>{{ $grossTotal->ApprovedPaid }}</td>
                <td>{{ $grossTotal->ApprovedUnpaid }}</td>
                <td>{{ $grossTotal->PendingApprovalPayment }}</td>
                <td>{{ $grossTotal->Rejected }}</td>
                <td>{{ $grossTotal->AllotmentCancelled }}</td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
