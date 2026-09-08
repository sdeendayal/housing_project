<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Physical Verification - Caste Wise
    </title>

    <style>

        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            font-size: 9px;
        }

        h1 {
            margin: 0 0 4px;
            font-size: 18px;
        }

        p {
            margin: 0;
        }

        .sub {
            color: #475569;
            font-size: 11px;
        }

        .meta {
            margin: 10px 0;
            color: #475569;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #94a3b8;
            padding: 5px 4px;
            text-align: center;
            vertical-align: middle;
            overflow-wrap: anywhere;
        }

        th {
            background: #f1f5f9;
            font-weight: 700;
            font-size: 8px;
        }

        td {
            font-size: 8px;
        }

        th:nth-child(2),
        td:nth-child(2) {
            text-align: left;
        }

        .total td {
            background: #e2e8f0;
            font-weight: 700;
        }

    </style>

</head>

<body>

    <h1>
        Physical Verification Report
    </h1>

    <p class="sub">
        MMSAY (1st Phase) - One Marla Plots Allotment done in June 2024
    </p>

    <div class="meta">

        Status after verification of
        <strong>{{ number_format($grandTotalPlots) }}</strong>
        plots

        &nbsp; | &nbsp;

        Total Eligible:
        <strong>{{ number_format($grandEligible) }}</strong>

        &nbsp; | &nbsp;

        Not Eligible:
        <strong>{{ number_format($grandNotEligible) }}</strong>

    </div>


    <table>

        <thead>

            <tr>

                <th rowspan="2">
                    Sr. No.
                </th>

                <th rowspan="2">
                    Towns
                </th>

                <th rowspan="2">
                    Plots allotted<br>Year 2024
                </th>

                <th colspan="5">
                    Eligible Allottees - Caste Wise
                </th>

                <th rowspan="2">
                    Not Eligible
                </th>

            </tr>

            <tr>

                <th>Ghumantu</th>
                <th>Widow</th>
                <th>SC</th>
                <th>Others</th>
                <th>Total Eligible</th>

            </tr>

        </thead>


        <tbody>

            @forelse ($rows as $index => $row)

                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $row->CityName }}
                    </td>

                    <td>
                        {{ number_format($row->plots_allotted) }}
                    </td>

                    <td>
                        {{ number_format($row->ghumantu) }}
                    </td>

                    <td>
                        {{ number_format($row->widow) }}
                    </td>

                    <td>
                        {{ number_format($row->scheduled_caste) }}
                    </td>

                    <td>
                        {{ number_format($row->others) }}
                    </td>

                    <td>
                        {{ number_format($row->eligible_total) }}
                    </td>

                    <td>
                        {{ number_format($row->not_eligible) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="9">
                        No records found.
                    </td>

                </tr>

            @endforelse


            <tr class="total">

                <td colspan="2">
                    TOTAL
                </td>

                <td>
                    {{ number_format($grandTotalPlots) }}
                </td>

                <td>
                    {{ number_format($grandGhumantu) }}
                </td>

                <td>
                    {{ number_format($grandWidow) }}
                </td>

                <td>
                    {{ number_format($grandScheduledCaste) }}
                </td>

                <td>
                    {{ number_format($grandOthers) }}
                </td>

                <td>
                    {{ number_format($grandEligible) }}
                </td>

                <td>
                    {{ number_format($grandNotEligible) }}
                </td>

            </tr>

        </tbody>

    </table>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>

</html>
Reply to I The first one is the one that was used in The I just think it's a very significant thing. I have a... I have a... I Still need I don't know.