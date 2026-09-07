<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Physical Verification Report
    </title>

    <style>

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #ffffff;
        }

        .report-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .department {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .title {
            margin-top: 5px;
            font-size: 17px;
            font-weight: 700;
        }

        .subtitle {
            margin-top: 4px;
            font-size: 13px;
        }

        .total-applicants {
            margin: 10px 0;
            text-align: right;
            font-size: 13px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 7px 6px;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
        }

        thead th {
            background: #f1f5f9;
            font-weight: 700;
        }

        .town {
            text-align: left;
        }

        .total-row {
            font-weight: 700;
            background: #f1f5f9;
        }

        .print-button {
            position: fixed;
            top: 15px;
            right: 15px;
            padding: 8px 14px;
            border: 0;
            border-radius: 5px;
            background: #111827;
            color: white;
            cursor: pointer;
        }

        @media print {

            .print-button {
                display: none;
            }

        }

    </style>

</head>


<body>

    <button
        class="print-button"
        onclick="window.print()">

        Print

    </button>


    {{-- =====================================================
         REPORT HEADER
    ====================================================== --}}

    <div class="report-header">

        <div class="department">
            DEPARTMENT OF HOUSING FOR ALL, HARYANA
        </div>

        <div class="title">
            PHYSICAL VERIFICATION REPORT
        </div>

        <div class="subtitle">
            Mukhyamantri Shehri Awas Yojana (MMSAY) - Phase-1
        </div>

    </div>


    {{-- TOTAL --}}
    <div class="total-applicants">

        Total Applicants:
        {{ number_format($grandTotal->total) }}

    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <table>

        <thead>

            <tr>

                <th rowspan="2" style="width: 7%;">
                    S.<br>N.
                </th>

                <th rowspan="2" style="width: 23%;">
                    Town / ULB
                </th>

                <th colspan="4">
                    Category-wise plots allotted
                </th>

                <th rowspan="2" style="width: 12%;">
                    Total<br>
                    plots<br>
                    allotted
                </th>

            </tr>


            <tr>

                <th>
                    Ghumantu<br>
                    Jati<br>
                    <span style="font-weight: normal;">
                        (1)
                    </span>
                </th>

                <th>
                    Widows<br>
                    <span style="font-weight: normal;">
                        (2)
                    </span>
                </th>

                <th>
                    Scheduled<br>
                    Caste<br>
                    <span style="font-weight: normal;">
                        (3)
                    </span>
                </th>

                <th>
                    Others<br>
                    <span style="font-weight: normal;">
                        (4)
                    </span>
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($report as $index => $row)

                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td class="town">
                        {{ $row->town_ulb }}
                    </td>

                    <td>
                        {{ number_format($row->ghumantu_jati) }}
                    </td>

                    <td>
                        {{ number_format($row->widows) }}
                    </td>

                    <td>
                        {{ number_format($row->scheduled_caste) }}
                    </td>

                    <td>
                        {{ number_format($row->others) }}
                    </td>

                    <td>
                        <strong>
                            {{ number_format($row->total) }}
                        </strong>
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7">
                        No records found.
                    </td>

                </tr>

            @endforelse


            {{-- GRAND TOTAL --}}

            <tr class="total-row">

                <td colspan="2">
                    Total
                </td>

                <td>
                    {{ number_format($grandTotal->ghumantu_jati) }}
                </td>

                <td>
                    {{ number_format($grandTotal->widows) }}
                </td>

                <td>
                    {{ number_format($grandTotal->scheduled_caste) }}
                </td>

                <td>
                    {{ number_format($grandTotal->others) }}
                </td>

                <td>
                    {{ number_format($grandTotal->total) }}
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