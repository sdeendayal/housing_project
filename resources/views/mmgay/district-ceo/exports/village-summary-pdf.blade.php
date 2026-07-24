<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Village Wise Summary</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1e293b;
        }

        h1 {
            margin-bottom: 4px;
            text-align: center;
            font-size: 18px;
        }

        .subtitle {
            margin-bottom: 16px;
            text-align: center;
            color: #475569;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px;
        }

        th {
            background: #2563eb;
            color: #ffffff;
            text-align: center;
        }

        td.number {
            text-align: center;
        }

        tfoot td {
            background: #e2e8f0;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>
        Village Wise Summary
    </h1>

    <div class="subtitle">
        {{ strtoupper($districtName) }} District —
        Phase {{ $phase }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Village</th>
                <th>Total Plots</th>
                <th>Applicants</th>
                <th>Approved Paid</th>
                <th>SC</th>
                <th>Ghumantu</th>
                <th>Widow</th>
                <th>Others</th>
                <th>Allotted</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($villageData as $row)
                <tr>
                    <td class="number">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $row->VillageName ?? '-' }}
                    </td>

                    <td class="number">
                        {{ number_format($row->TotalPlots ?? 0) }}
                    </td>

                    <td class="number">
                        {{ number_format($row->TotalApplicants ?? 0) }}
                    </td>

                    <td class="number">
                        {{ number_format($row->ApprovedPaid ?? 0) }}
                    </td>

                    <td class="number">
                        {{ number_format($row->SC ?? 0) }}
                    </td>

                    <td class="number">
                        {{ number_format($row->Ghumantu ?? 0) }}
                    </td>

                    <td class="number">
                        {{ number_format($row->Widow ?? 0) }}
                    </td>

                    <td class="number">
                        {{ number_format($row->Others ?? 0) }}
                    </td>

                    <td class="number">
                        {{ number_format($row->TotalAllotment ?? 0) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="number">
                        No village records found.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2">
                    Grand Total
                </td>

                <td class="number">
                    {{ number_format($totals['totalPlots']) }}
                </td>

                <td class="number">
                    {{ number_format($totals['totalApplicants']) }}
                </td>

                <td class="number">
                    {{ number_format($totals['totalPaid']) }}
                </td>

                <td class="number">
                    {{ number_format($totals['totalSC']) }}
                </td>

                <td class="number">
                    {{ number_format($totals['totalGhumantu']) }}
                </td>

                <td class="number">
                    {{ number_format($totals['totalWidow']) }}
                </td>

                <td class="number">
                    {{ number_format($totals['totalOthers']) }}
                </td>

                <td class="number">
                    {{ number_format($totals['totalAllotment']) }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>