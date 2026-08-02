<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Village Report Print</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1e293b;
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .print-page {
            page-break-after: always;
        }

        .print-page:last-child {
            page-break-after: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #94a3b8;
            padding: 5px;
            text-align: center;
        }

        th {
            background: #e2e8f0;
            font-weight: 700;
        }

        td.village {
            text-align: left;
        }

        .no-print {
            margin-bottom: 14px;
            text-align: right;
        }

        .print-button {
            border: 0;
            border-radius: 6px;
            background: #334155;
            padding: 8px 14px;
            color: white;
            cursor: pointer;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button
            type="button"
            class="print-button"
            onclick="window.print()"
        >
            Print Report
        </button>
    </div>

   @foreach ($report->chunk(40) as $chunkIndex => $rows)
    <section class="print-page">

        <table>
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Village</th>
                    <th>Phase</th>
                    <th>Total Plots</th>
                    <th>Applicants</th>
                    <th>Allotted</th>
                    <th>Approved Paid</th>
                    <th>Approved Unpaid</th>
                    <th>Pending</th>
                    <th>Rejected</th>
                    <th>Cancelled</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>
                            {{ ($chunkIndex * 40) + $loop->iteration }}
                        </td>

                        <td class="village">
                            {{ $row->VillageName ?? '-' }}
                        </td>

                        <td>{{ $row->Phase ?? '-' }}</td>

                        <td>
                            {{ number_format($row->TotalPlots ?? 0) }}
                        </td>

                        <td>
                            {{ number_format($row->RegisteredBeneficiaries ?? 0) }}
                        </td>

                        <td>
                            {{ number_format($row->AllottedBeneficiaries ?? 0) }}
                        </td>

                        <td>
                            {{ number_format($row->ApprovedPaid ?? 0) }}
                        </td>

                        <td>
                            {{ number_format($row->ApprovedUnpaid ?? 0) }}
                        </td>

                        <td>
                            {{ number_format($row->PendingApprovalPayment ?? 0) }}
                        </td>

                        <td>
                            {{ number_format($row->Rejected ?? 0) }}
                        </td>

                        <td>
                            {{ number_format($row->AllotmentCancelled ?? 0) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            @if ($loop->last)
                <tfoot>
                    <tr>
                        <th colspan="3">Gross Total</th>
                        <th>{{ number_format($grossTotal->TotalPlots ?? 0) }}</th>
                        <th>{{ number_format($grossTotal->RegisteredBeneficiaries ?? 0) }}</th>
                        <th>{{ number_format($grossTotal->AllottedBeneficiaries ?? 0) }}</th>
                        <th>{{ number_format($grossTotal->ApprovedPaid ?? 0) }}</th>
                        <th>{{ number_format($grossTotal->ApprovedUnpaid ?? 0) }}</th>
                        <th>{{ number_format($grossTotal->PendingApprovalPayment ?? 0) }}</th>
                        <th>{{ number_format($grossTotal->Rejected ?? 0) }}</th>
                        <th>{{ number_format($grossTotal->AllotmentCancelled ?? 0) }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>

    </section>
@endforeach

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>

</body>

</html>