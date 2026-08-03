<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Allotment Report Print</title>

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
            color: #0f172a;
            font-family: Arial, sans-serif;
            font-size: 9px;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
            padding: 10px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .actions {
            display: flex;
            gap: 7px;
        }

        .button {
            display: inline-block;
            border: 0;
            border-radius: 6px;
            background: #1e293b;
            padding: 8px 13px;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .button.secondary {
            background: #475569;
        }

        .button.disabled {
            opacity: 0.45;
            pointer-events: none;
        }

        .header {
            margin-bottom: 10px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 17px;
        }

        .header p {
            margin: 4px 0 0;
            color: #475569;
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
            padding: 4px;
            overflow-wrap: anywhere;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 8px;
            }
        }
    </style>
</head>

<body>

    @php
        $queryParams = request()->except([
            'print_page',
            'page',
        ]);

        $previousUrl = route(
            'admin.allotment.print',
            array_merge(
                $queryParams,
                [
                    'print_page' => max(
                        1,
                        $printPage - 1,
                    ),
                    'print_limit' => $printLimit,
                ],
            ),
        );

        $nextUrl = route(
            'admin.allotment.print',
            array_merge(
                $queryParams,
                [
                    'print_page' => min(
                        $totalPrintPages,
                        $printPage + 1,
                    ),
                    'print_limit' => $printLimit,
                ],
            ),
        );
    @endphp

    <div class="toolbar no-print">

        <div>
            Batch {{ $printPage }}
            of {{ $totalPrintPages }}

            <br>

            Records:
            {{ number_format($startSerial) }}
            -
            {{ number_format(
                min(
                    $startSerial
                    + $records->count()
                    - 1,
                    $totalRecords,
                )
            ) }}

            of {{ number_format($totalRecords) }}
        </div>

        <div class="actions">

            <a
                href="{{ $previousUrl }}"
                class="button secondary
                    {{ $printPage <= 1
                        ? 'disabled'
                        : '' }}"
            >
                Previous
            </a>

            <button
                type="button"
                class="button"
                onclick="window.print()"
            >
                Print Current Batch
            </button>

            <a
                href="{{ $nextUrl }}"
                class="button secondary
                    {{ $printPage >= $totalPrintPages
                        ? 'disabled'
                        : '' }}"
            >
                Next
            </a>

        </div>

    </div>

    <div class="header">
        <h1>Allotment Report</h1>

        <p>
            Batch {{ $printPage }}
            of {{ $totalPrintPages }}
            |
            Total Records:
            {{ number_format($totalRecords) }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Application</th>
                <th>Applicant</th>
                <th>Father / Husband</th>
                <th>Mobile</th>
                <th>District</th>
                <th>Block</th>
                <th>Village</th>
                <th>Phase</th>
                <th>Plot</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($records as $index => $record)
                <tr>
                    <td class="center">
                        {{ $startSerial + $index }}
                    </td>

                    <td>
                        {{ $record->RegistrationNo ?? '-' }}

                        <br>

                        Owner ID:
                        {{ $record->OwnerId ?? '-' }}
                    </td>

                    <td>
                        {{ $record->OwnerName ?? '-' }}
                    </td>

                    <td>
                        {{ $record->FatherHusbandName ?? '-' }}
                    </td>

                    <td>
                        {{ $record->MobileNo ?? '-' }}
                    </td>

                    <td>
                        {{ $record->DistrictName ?? '-' }}
                    </td>

                    <td>
                        {{ $record->BlockName ?? '-' }}
                    </td>

                    <td>
                        {{ $record->VillageName ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $record->Phase ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $record->FlatNo ?? '-' }}

                        <br>

                        ID:
                        {{ $record->FlatId ?? '-' }}
                    </td>

                    <td>
                        {{ $record->AllotmentStatus ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="11"
                        class="center"
                    >
                        No allotment records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>