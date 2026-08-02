<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>
        Possession Applications Print
    </title>

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
            color: #0f172a;
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .header {
            margin-bottom: 12px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0 0;
            color: #475569;
            font-size: 11px;
        }

        .no-print {
            margin-bottom: 12px;
            text-align: right;
        }

        .print-button {
            border: 0;
            border-radius: 6px;
            background: #1e293b;
            padding: 8px 16px;
            color: #fff;
            cursor: pointer;
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
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            font-weight: 700;
            text-align: center;
        }

        td.center {
            text-align: center;
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

    <div class="header">
        <h1>
            Possession Applications
        </h1>

        <p>
            {{ $filterLabels[$filter] ?? 'Total Eligible' }}
            |
            Total Records:
            {{ number_format($applications->count()) }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Applicant</th>
                <th>Mobile</th>
                <th>Registration No.</th>
                <th>Application No.</th>
                <th>Flat No.</th>
                <th>Village</th>
                <th>Phase</th>
                <th>Status</th>
                <th>Visit / Meeting</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($applications as $index => $application)
                <tr>
                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        <strong>
                            {{ $application->OwnerName ?? '-' }}
                        </strong>

                        <br>

                        {{ $application->FatherHusbandName ?? '-' }}
                    </td>

                    <td>
                        {{ $application->MobileNo ?? '-' }}
                    </td>

                    <td>
                        {{ $application->RegistrationNo ?? '-' }}
                    </td>

                    <td>
                        {{ $application->application_number ?? 'Not Created' }}
                    </td>

                    <td class="center">
                        {{ $application->FlatNo ?? '-' }}
                    </td>

                    <td>
                        {{ $application->VillageName ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $application->Phase ?? '-' }}
                    </td>

                    <td>
                        {{ $application->physical_possession_status
                            ?: 'Schedule Pending' }}
                    </td>

                    <td>
                        {{ $application->meeting_slot
                            ?? $application->citizen_visit_date
                            ?? $application->possession_date
                            ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="10"
                        class="center"
                    >
                        No possession applications found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.addEventListener(
            'load',
            function () {
                window.print();
            }
        );
    </script>

</body>

</html>