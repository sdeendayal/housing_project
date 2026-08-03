<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Applicants Print</title>

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
            gap: 10px;
            margin-bottom: 12px;
            padding: 10px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .button {
            display: inline-block;
            border: 0;
            border-radius: 5px;
            background: #1e293b;
            padding: 7px 12px;
            color: #fff;
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
            overflow-wrap: anywhere;
            border: 1px solid #94a3b8;
            padding: 4px;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            text-align: center;
            font-weight: 700;
        }

        .center {
            text-align: center;
        }

        .w-sr {
            width: 4%;
        }

        .w-name {
            width: 14%;
        }

        .w-application {
            width: 13%;
        }

        .w-mobile {
            width: 9%;
        }

        .w-village {
            width: 12%;
        }

        .w-phase {
            width: 6%;
        }

        .w-flat {
            width: 7%;
        }

        .w-status {
            width: 12%;
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
            'superadmin.applicants.print',
            array_merge(
                $queryParams,
                [
                    'print_page' => max(1, $printPage - 1),
                    'print_limit' => $perBatch,
                ],
            ),
        );

        $nextUrl = route(
            'superadmin.applicants.print',
            array_merge(
                $queryParams,
                [
                    'print_page' => min(
                        $totalPrintPages,
                        $printPage + 1,
                    ),
                    'print_limit' => $perBatch,
                ],
            ),
        );
    @endphp

    <div class="toolbar no-print">

        <div>
            Batch {{ $printPage }} of {{ $totalPrintPages }}

            <br>

            Records:
            {{ number_format($startSerial) }}
            –
            {{ number_format(
                min(
                    $startSerial + $records->count() - 1,
                    $totalRecords,
                )
            ) }}

            of {{ number_format($totalRecords) }}
        </div>

        <div class="toolbar-actions">

            <a
                href="{{ $previousUrl }}"
                class="button secondary {{ $printPage <= 1 ? 'disabled' : '' }}"
            >
                Previous Batch
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
                class="button secondary {{ $printPage >= $totalPrintPages ? 'disabled' : '' }}"
            >
                Next Batch
            </a>

        </div>

    </div>

    <div class="header">
        <h1>Applicants Report</h1>

        <p>
            Batch {{ $printPage }} of {{ $totalPrintPages }}
            |
            Total Records: {{ number_format($totalRecords) }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="w-sr">Sr.</th>
                <th class="w-application">Application No.</th>
                <th class="w-name">Applicant</th>
                <th class="w-name">Father / Husband</th>
                <th class="w-mobile">Mobile</th>
                <th class="w-village">Village</th>
                <th class="w-phase">Phase</th>
                <th class="w-flat">Flat</th>
                <th class="w-status">Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($records as $index => $row)
                <tr>
                    <td class="center">
                        {{ $startSerial + $index }}
                    </td>

                    <td>
                        {{ $row->RegistrationNo ?? '-' }}
                    </td>

                    <td>
                        {{ $row->OwnerName ?? '-' }}
                    </td>

                    <td>
                        {{ $row->FatherHusbandName ?? '-' }}
                    </td>

                    <td>
                        {{ $row->MobileNo ?? '-' }}
                    </td>

                    <td>
                        {{ $row->VillageName ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $row->Phase ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $row->FlatNo ?? '-' }}
                    </td>

                    <td>
                        {{ $row->ApplicantStatus ?? 'Allotted' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">
                        No applicants found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>