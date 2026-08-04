<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>District Wise Report</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 9mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1e293b;
            background: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 14px;
        }

        .print-button {
            border: 0;
            border-radius: 7px;
            background: #1e293b;
            padding: 9px 16px;
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        .report-header {
            margin-bottom: 14px;
            text-align: center;
        }

        .report-header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
        }

        .report-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 10px;
        }

        .filters {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }

        .filter-chip {
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            background: #eff6ff;
            padding: 4px 10px;
            color: #1d4ed8;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
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
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background: #2563eb;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
        }

        td.district {
            text-align: left;
            font-weight: 700;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tfoot td {
            border-top: 2px solid #475569;
            background: #e2e8f0;
            color: #0f172a;
            font-weight: 700;
        }

        .serial {
            width: 5%;
        }

        .district-column {
            width: 14%;
        }

        .number-column {
            width: 10%;
        }

        .generated-at {
            margin-top: 8px;
            text-align: right;
            color: #64748b;
            font-size: 9px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar no-print">
        <button type="button"
            class="print-button"
            onclick="window.print()">
            Print Report
        </button>
    </div>

    <header class="report-header">
        <h1>
            District Wise Report
        </h1>

        <p>
            District-wise village, applicant and allotment status summary
        </p>

        <div class="filters">
            <span class="filter-chip">
                {{ $filters->phase
                    ? 'Phase ' . $filters->phase
                    : 'All Phases' }}
            </span>

            <span class="filter-chip">
                {{ $filters->districtName
                    ?: 'All Districts' }}
            </span>
        </div>
    </header>

    <table>
        <thead>
            <tr>
                <th class="serial">
                    Sr.
                </th>

                <th class="district-column">
                    District
                </th>

                <th class="number-column">
                    Villages
                </th>

                <th class="number-column">
                    Applicants
                </th>

                <th class="number-column">
                    Allotted
                </th>

                <th class="number-column">
                    Approved & Paid
                </th>

                <th class="number-column">
                    Approved & Unpaid
                </th>

                <th class="number-column">
                    Yet to be Approved
                </th>

                <th class="number-column">
                    Rejected
                </th>

                <th class="number-column">
                    Cancelled
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($report as $index => $row)
                <tr>
                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td class="district">
                        {{ $row->DistrictName ?? '-' }}
                    </td>

                    <td>
                        {{ number_format(
                            $row->VillagesWithPlots ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $row->RegisteredBeneficiaries ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $row->AllottedBeneficiaries ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $row->ApprovedPaid ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $row->ApprovedUnpaid ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $row->PendingApprovalPayment ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $row->Rejected ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $row->AllotmentCancelled ?? 0
                        ) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">
                        No records found for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if ($report->isNotEmpty())
            <tfoot>
                <tr>
                    <td></td>

                    <td>
                        GROSS TOTAL
                    </td>

                    <td>
                        {{ number_format(
                            $grossTotal->VillagesWithPlots ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $grossTotal->RegisteredBeneficiaries ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $grossTotal->AllottedBeneficiaries ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $grossTotal->ApprovedPaid ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $grossTotal->ApprovedUnpaid ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $grossTotal->PendingApprovalPayment ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $grossTotal->Rejected ?? 0
                        ) }}
                    </td>

                    <td>
                        {{ number_format(
                            $grossTotal->AllotmentCancelled ?? 0
                        ) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="generated-at">
        Generated on:
        {{ now()->format('d-m-Y h:i A') }}
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>

</body>

</html>