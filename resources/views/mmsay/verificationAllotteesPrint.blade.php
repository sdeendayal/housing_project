<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Allottees</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 18px;
            color: #0f172a;
            background: #f8fafc;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .page {
            max-width: 1500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #dbe3ee;
            border-radius: 12px;
            background: #fff;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .title {
            margin: 0;
            font-size: 20px;
        }

        .subtitle {
            margin: 5px 0 0;
            color: #64748b;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .button {
            display: inline-flex;
            min-height: 36px;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #334155;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            border-color: #4f46e5;
            color: #fff;
            background: #4f46e5;
        }

        .button-print {
            border-color: #172033;
            color: #fff;
            background: #172033;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .summary strong {
            color: #334155;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            padding: 8px 7px;
            border: 1px solid #dbe3ee;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        th {
            color: #475569;
            background: #f1f5f9;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .serial {
            width: 42px;
            text-align: center;
        }

        .amount {
            color: #059669;
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .eligible {
            color: #047857;
            background: #d1fae5;
        }

        .not-eligible {
            color: #be123c;
            background: #ffe4e6;
        }

        .muted {
            margin-top: 3px;
            color: #94a3b8;
            font-size: 9px;
        }

        .bottom-toolbar {
            margin-top: 16px;
            margin-bottom: 0;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
        }

        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
                font-size: 8px;
            }

            .page {
                max-width: none;
                padding: 0;
                border: 0;
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }

            .title {
                font-size: 16px;
            }

            th,
            td {
                padding: 4px;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    @php
        $records = $applications ?? ($allottees ?? collect());

        // Current URL se eligible/not-eligible automatically capture hoga
        $currentEligibility = request()->route('eligibility') ?? ($eligibility ?? 'eligible');

        $eligibilityLabel = in_array($currentEligibility, ['not-eligible', 'not_eligible'], true)
            ? 'Not-Eligible Allottees'
            : 'Eligible Allottees';

        $nextParameters = array_merge(request()->except(['after_id', 'page', 'eligibility']), [
            'eligibility' => $currentEligibility,
            'after_id' => $nextAfterId ?? null,
        ]);
    @endphp

    <div class="page">

        {{-- Top controls --}}
        <div class="toolbar">
            <div>
                <h1 class="title">Physical Verification – {{ $eligibilityLabel }}</h1>

                <p class="subtitle">
                    Showing {{ number_format($records->count()) }} records in this batch
                </p>
            </div>

            <div class="actions no-print">
                <button type="button" onclick="window.print()" class="button button-print">
                    Print
                </button>

                @if (($hasMore ?? false) && ($nextAfterId ?? null))
                    <a href="{{ route('verification-allottees.print', $nextParameters) }}"
                        class="button button-primary">
                        Next 1,000 Records →
                    </a>
                @endif
            </div>
        </div>

        <div class="summary">
            <span>
                Report:
                <strong>{{ $eligibilityLabel }}</strong>
            </span>

            <span>
                Generated:
                <strong>{{ now()->format('d M Y, h:i A') }}</strong>
            </span>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th class="serial">S.No.</th>
                        <th>Asset / Application</th>
                        <th>Applicant</th>
                        <th>Property</th>
                        <th>Location</th>
                        <th>Family / Member</th>
                        <th>Category</th>
                        <th>Verification Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($records as $row)
                        <tr>
                            <td class="serial">
                                {{ number_format(($serialStart ?? 0) + $loop->iteration) }}
                            </td>

                            <td>
                                <strong>Asset #{{ $row->asset_id ?? '-' }}</strong>

                                <div class="muted">
                                    App:
                                    {{ $row->application_number ?? ($row->purchaser_application_number ?? '-') }}
                                </div>
                            </td>

                            <td>
                                <strong>{{ $row->applicant_name ?? '-' }}</strong>

                                <div class="muted">
                                    {{ $row->mobile ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <strong>{{ $row->asset_name ?? '-' }}</strong>

                                <div class="muted">
                                    {{ $row->asset_size ?? '-' }}
                                    {{ $row->asset_unit ?? '' }}
                                </div>
                            </td>

                            <td>
                                <strong>{{ $row->district_name ?? '-' }}</strong>

                                <div class="muted">
                                    {{ $row->city_name ?? '-' }}

                                    @if (!empty($row->sector_name))
                                        / {{ $row->sector_name }}
                                    @endif
                                </div>
                            </td>

                            <td>
                                <strong>
                                    {{ $row->ppp_id ?? ($row->family_id ?? '-') }}
                                </strong>

                                <div class="muted">
                                    Member:
                                    {{ $row->member_id ?? '-' }}
                                </div>
                            </td>

                            <td>
                                {{ $row->caste_category ?? ($row->category ?? '-') }}
                            </td>

                            <td>
                                @if ($currentEligibility === 'not_eligible')
                                    <span class="status not-eligible">
                                        Not Eligible
                                    </span>
                                @else
                                    <span class="status eligible">
                                        Eligible
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 30px; text-align: center; color: #64748b;">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Bottom controls --}}
        <div class="toolbar bottom-toolbar no-print">
            <span class="subtitle">
                {{ number_format($records->count()) }} records displayed
            </span>

            <div class="actions">
                <button type="button" onclick="window.print()" class="button button-print">
                    Print
                </button>

                @if (($hasMore ?? false) && ($nextAfterId ?? null))
                    <a href="{{ route('verification-allottees.print', $nextParameters) }}"
                        class="button button-primary">
                        Next 1,000 Records →
                    </a>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
