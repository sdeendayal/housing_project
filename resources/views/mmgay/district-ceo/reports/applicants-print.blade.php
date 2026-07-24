<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Applicants Print Report</title>

    <style>
        @page {
            size: A3 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            padding: 16px;
            background: #f1f5f9;
            color: #0f172a;
            font-family: Arial, sans-serif;
            font-size: 8px;
        }

        .report-container {
            width: 100%;
            margin: 0 auto;
            padding: 16px;
            border: 1px solid #dbe3ee;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
            padding: 14px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #0f172a;
            color: #ffffff;
        }

        .toolbar-bottom {
            margin-top: 18px;
            margin-bottom: 0;
        }

        .toolbar-info {
            min-width: 0;
        }

        .toolbar-title {
            margin-bottom: 5px;
            font-size: 16px;
            font-weight: 700;
        }

        .toolbar-meta {
            color: #cbd5e1;
            font-size: 11px;
            line-height: 1.5;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 8px;
        }

        .toolbar a,
        .toolbar button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #ffffff;
            padding: 8px 14px;
            color: #0f172a;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }

        .toolbar a:hover,
        .toolbar button:hover {
            border-color: #94a3b8;
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        .toolbar .print-button {
            border-color: #16a34a;
            background: #16a34a;
            color: #ffffff;
        }

        .toolbar .print-button:hover {
            border-color: #15803d;
            background: #15803d;
        }

        .toolbar .disabled-button {
            border-color: #475569;
            background: #334155;
            color: #94a3b8;
            cursor: not-allowed;
            pointer-events: none;
        }

        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 12px;
            padding: 14px 16px;
            border: 1px solid #dbe3ee;
            border-radius: 9px;
            background: #f8fafc;
        }

        .header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            line-height: 1.2;
        }

        .header p {
            margin: 6px 0 0;
            color: #475569;
            font-size: 11px;
            line-height: 1.5;
        }

        .header-summary {
            min-width: 190px;
            text-align: right;
        }

        .header-summary .label {
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .header-summary .value {
            margin-top: 4px;
            color: #0f172a;
            font-size: 18px;
            font-weight: 700;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #94a3b8;
            border-radius: 8px;
        }

        table {
            width: 100%;
            min-width: 1600px;
            border-collapse: collapse;
            table-layout: fixed;
            background: #ffffff;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 2;
            border: 1px solid #475569;
            background: #1e293b;
            padding: 6px 3px;
            color: #ffffff;
            font-size: 7px;
            font-weight: 700;
            line-height: 1.25;
            text-align: center;
            vertical-align: middle;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 5px 3px;
            color: #1e293b;
            font-size: 7px;
            line-height: 1.3;
            vertical-align: top;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:hover {
            background: #e0f2fe;
        }

        .center {
            text-align: center;
            vertical-align: middle;
        }

        .nowrap {
            white-space: nowrap;
        }

        .status {
            display: inline-block;
            border-radius: 999px;
            padding: 3px 5px;
            font-size: 6.5px;
            font-weight: 700;
            line-height: 1.2;
            text-align: center;
        }

        .status-success {
            background: #dcfce7;
            color: #166534;
        }

        .status-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-muted {
            background: #e2e8f0;
            color: #475569;
        }

        .empty-state {
            padding: 30px 12px;
            color: #64748b;
            font-size: 11px;
            text-align: center;
        }

        .page-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            border: 1px solid #475569;
            border-radius: 7px;
            background: #1e293b;
            padding: 8px 13px;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        @media screen and (max-width: 900px) {
            body {
                padding: 8px;
            }

            .report-container {
                padding: 10px;
            }

            .toolbar,
            .header {
                align-items: stretch;
                flex-direction: column;
            }

            .toolbar-actions {
                justify-content: flex-start;
            }

            .header-summary {
                text-align: left;
            }
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .report-container {
                padding: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            .header {
                margin-bottom: 7px;
                padding: 8px 10px;
                border-radius: 0;
            }

            .header h1 {
                font-size: 15px;
            }

            .header p {
                font-size: 8px;
            }

            .header-summary .value {
                font-size: 13px;
            }

            .table-wrapper {
                overflow: visible;
                border-radius: 0;
            }

            table {
                min-width: 0;
            }

            th {
                position: static;
                padding: 3px 2px;
                font-size: 6px;
            }

            td {
                padding: 2px;
                font-size: 6px;
            }

            tbody tr:hover {
                background: inherit;
            }

            .status {
                padding: 0;
                background: transparent !important;
                color: #111827 !important;
                font-size: 6px;
            }
        }
    </style>
</head>

<body>

    @php
        $firstItem = $applicants->firstItem() ?? 0;
        $lastItem = $applicants->lastItem() ?? 0;
        $totalRecords = $applicants->total();
        $currentPage = $applicants->currentPage();
        $lastPage = $applicants->lastPage();

        $statusClass = static function (?string $value): string {
            return match ($value) {
                'Approved & Paid', 'Paid', 'Registry Done', 'Allotted' => 'status-success',

                'Approved & Unpaid', 'Unpaid', 'Registry Pending', 'Yet to be Approved' => 'status-warning',

                'Rejected', 'Cancelled' => 'status-danger',

                'Not Allotted' => 'status-info',

                default => 'status-muted',
            };
        };
    @endphp

    <div class="report-container">

        {{-- Top navigation --}}
        <div class="toolbar no-print">
            <div class="toolbar-info">
                <div class="toolbar-title">
                    MMGAY Applicants Report
                </div>

                <div class="toolbar-meta">
                    Page <strong>{{ $currentPage }}</strong> of
                    <strong>{{ $lastPage }}</strong>

                    &nbsp; | &nbsp;

                    Showing
                    <strong>{{ number_format($firstItem) }}</strong>
                    to
                    <strong>{{ number_format($lastItem) }}</strong>
                    of
                    <strong>{{ number_format($totalRecords) }}</strong>
                    records
                </div>
            </div>

            <div class="toolbar-actions">
                @if ($applicants->onFirstPage())
                    <span class="disabled-button">
                        &larr; Previous Batch
                    </span>
                @else
                    <a href="{{ $applicants->previousPageUrl() }}">
                        &larr; Previous Batch
                    </a>
                @endif

                <span class="page-indicator">
                    {{ $currentPage }} / {{ $lastPage }}
                </span>

                <button type="button" class="print-button" onclick="window.print()">
                    Print Current Batch
                </button>

                @if ($applicants->hasMorePages())
                    <a href="{{ $applicants->nextPageUrl() }}">
                        Next Batch &rarr;
                    </a>
                @else
                    <span class="disabled-button">
                        Next Batch &rarr;
                    </span>
                @endif
            </div>
        </div>

        {{-- Printable report heading --}}
        <div class="header">
            <div>
                <h1>MMGAY Applicants Report</h1>

                <p>
                    District:
                    <strong>{{ $districtName }}</strong>

                    &nbsp; | &nbsp;

                    Phase:
                    <strong>
                        {{ $phase === 'all' ? 'All Phases' : 'Phase ' . $phase }}
                    </strong>

                    @if (!empty($villageId))
                        &nbsp; | &nbsp;
                        Village ID:
                        <strong>{{ $villageId }}</strong>
                    @endif

                    @if (!empty($status))
                        &nbsp; | &nbsp;
                        Status:
                        <strong>{{ ucwords(str_replace('_', ' ', $status)) }}</strong>
                    @endif

                    @if (!empty($caste))
                        &nbsp; | &nbsp;
                        Caste:
                        <strong>{{ $caste }}</strong>
                    @endif

                    @if (!empty($search))
                        &nbsp; | &nbsp;
                        Search:
                        <strong>{{ $search }}</strong>
                    @endif
                </p>
            </div>

            <div class="header-summary">
                <div class="label">Current Batch</div>

                <div class="value">
                    {{ number_format($firstItem) }}
                    -
                    {{ number_format($lastItem) }}
                </div>

                <div class="label">
                    Total {{ number_format($totalRecords) }} records
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <colgroup>
                    <col style="width: 2.5%">
                    <col style="width: 6%">
                    <col style="width: 7%">
                    <col style="width: 4%">
                    <col style="width: 7%">
                    <col style="width: 3.5%">
                    <col style="width: 4%">
                    <col style="width: 5%">
                    <col style="width: 6%">
                    <col style="width: 3%">
                    <col style="width: 6%">
                    <col style="width: 4%">
                    <col style="width: 5%">
                    <col style="width: 7%">
                    <col style="width: 5%">
                    <col style="width: 6%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 9%">
                </colgroup>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Registration No.</th>
                        <th>Applicant Name</th>
                        <th>Relation</th>
                        <th>Father / Husband</th>
                        <th>Gender</th>
                        <th>Caste</th>
                        <th>Mobile</th>
                        <th>PPP ID</th>
                        <th>Phase</th>
                        <th>Village</th>
                        <th>Plot No.</th>
                        <th>Allotment</th>
                        <th>Applicant Status</th>
                        <th>Payment</th>
                        <th>Registry</th>
                        <th>Remarks</th>
                        <th>DC Remarks</th>
                        <th>Address</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($applicants as $applicant)
                        <tr>
                            <td class="center">
                                {{ $firstItem + $loop->index }}
                            </td>

                            <td class="center">
                                {{ $applicant->RegistrationNo ?: '—' }}
                            </td>

                            <td>
                                {{ $applicant->OwnerName ?: '—' }}
                            </td>

                            <td class="center">
                                {{ $applicant->Relation ?: '—' }}
                            </td>

                            <td>
                                {{ $applicant->FatherHusbandName ?: '—' }}
                            </td>

                            <td class="center">
                                {{ $applicant->Gender ?: '—' }}
                            </td>

                            <td class="center">
                                {{ $applicant->Caste ?: 'Others' }}
                            </td>

                            <td class="center nowrap">
                                {{ $applicant->MobileNo ?: '—' }}
                            </td>

                            <td class="center">
                                {{ $applicant->PPPId ?: '—' }}
                            </td>

                            <td class="center">
                                {{ $applicant->Phase ?: '—' }}
                            </td>

                            <td>
                                {{ $applicant->VillageName ?: '—' }}
                            </td>

                            <td class="center">
                                {{ $applicant->FlatNo ?: '—' }}
                            </td>

                            <td class="center">
                                <span class="status {{ $statusClass($applicant->AllotmentStatus) }}">
                                    {{ $applicant->AllotmentStatus ?: '—' }}
                                </span>
                            </td>

                            <td class="center">
                                <span class="status {{ $statusClass($applicant->ApplicantStatus) }}">
                                    {{ $applicant->ApplicantStatus ?: '—' }}
                                </span>
                            </td>

                            <td class="center">
                                <span class="status {{ $statusClass($applicant->PaymentStatus) }}">
                                    {{ $applicant->PaymentStatus ?: '—' }}
                                </span>
                            </td>

                            <td class="center">
                                <span class="status {{ $statusClass($applicant->RegistryStatus) }}">
                                    {{ $applicant->RegistryStatus ?: '—' }}
                                </span>
                            </td>

                            <td>
                                {{ $applicant->Remarks ?: '—' }}
                            </td>

                            <td>
                                {{ $applicant->DCRemarks ?: '—' }}
                            </td>

                            <td>
                                {{ $applicant->OwnerAddress ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="19" class="empty-state">
                                No applicants found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Bottom navigation --}}
        <div class="toolbar toolbar-bottom no-print">
            <div class="toolbar-info">
                <div class="toolbar-title">
                    Batch Navigation
                </div>

                <div class="toolbar-meta">
                    Showing
                    <strong>{{ number_format($firstItem) }}</strong>
                    to
                    <strong>{{ number_format($lastItem) }}</strong>
                    of
                    <strong>{{ number_format($totalRecords) }}</strong>
                    records
                </div>
            </div>

            <div class="toolbar-actions">
                @if ($applicants->onFirstPage())
                    <span class="disabled-button">
                        &larr; Previous Batch
                    </span>
                @else
                    <a href="{{ $applicants->previousPageUrl() }}">
                        &larr; Previous Batch
                    </a>
                @endif

                <span class="page-indicator">
                    {{ $currentPage }} / {{ $lastPage }}
                </span>

                <button type="button" class="print-button" onclick="window.print()">
                    Print Current Batch
                </button>

                @if ($applicants->hasMorePages())
                    <a href="{{ $applicants->nextPageUrl() }}">
                        Next Batch &rarr;
                    </a>
                @else
                    <span class="disabled-button">
                        Next Batch &rarr;
                    </span>
                @endif
            </div>
        </div>

    </div>

</body>

</html>
