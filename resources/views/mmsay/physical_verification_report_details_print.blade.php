<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Physical Verification Details - Print</title>

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

        .print-header {
            margin-bottom: 12px;
        }

        .print-header h1 {
            margin: 0 0 4px;
            font-size: 18px;
        }

        .print-header p {
            margin: 0;
            color: #475569;
            font-size: 11px;
        }

        .meta {
            margin: 8px 0 10px;
            font-size: 9px;
            color: #475569;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: top;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        th {
            background: #f1f5f9;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8px;
            text-align: left;
        }

        td {
            font-size: 8px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
            white-space: nowrap;
        }

        .total {
            font-weight: 700;
            background: #f8fafc;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="print-header">
        <h1>Physical Verification Details</h1>
        <p>Mukhyamantri Shehri Awas Yojana (MMSAY) - Phase-1</p>
    </div>

    <div class="meta">
        Category:
        <strong>{{ $category ? ucfirst(str_replace('_', ' ', $category)) : 'All Categories' }}</strong>
        &nbsp;&nbsp; | &nbsp;&nbsp;
        Total Beneficiaries:
        <strong>{{ number_format($beneficiaries->count()) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:3%">S.No</th>
                <th style="width:8%">Name</th>
                <th style="width:8%">Father Name</th>
                <th style="width:7%">Registration No.</th>
                <th style="width:6%">Caste</th>
                <th style="width:7%">Mobile</th>
                <th style="width:12%">Address</th>
                <th style="width:8%">Asset Name</th>
                <th style="width:8%">District</th>
                <th style="width:7%">City / ULB</th>
                <th style="width:6%">Sector</th>
                <th style="width:7%">Flat Cost</th>
                <th style="width:7%">Total Paid</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($beneficiaries as $index => $beneficiary)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>

                    <td>{{ $beneficiary->PrivatePurchaserName ?? '-' }}</td>

                    <td>{{ $beneficiary->PurchaserFatherName ?? '-' }}</td>

                    <td>{{ $beneficiary->ApplicationNo ?? '-' }}</td>

                    <td>{{ $beneficiary->CasteCategoryName ?? '-' }}</td>

                    <td>{{ $beneficiary->MobileNo ?? '-' }}</td>

                    <td>{{ $beneficiary->Address ?? '-' }}</td>

                    <td>{{ $beneficiary->AssetName ?? '-' }}</td>

                    <td>{{ $beneficiary->DistrictName ?? '-' }}</td>

                    <td>{{ $beneficiary->CityName ?? '-' }}</td>

                    <td>{{ $beneficiary->SectorName ?? '-' }}</td>

                    <td class="right">
                        ₹{{ number_format((float) ($beneficiary->FlatCost ?? 0), 2) }}
                    </td>

                    <td class="right">
                        ₹{{ number_format((float) ($beneficiary->TotalPaid ?? 0), 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="center">
                        No beneficiaries found.
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if ($beneficiaries->isNotEmpty())
            <tfoot>
                <tr class="total">
                    <td colspan="11" class="right">TOTAL</td>

                    <td class="right">
                        ₹{{ number_format((float) $beneficiaries->sum('FlatCost'), 2) }}
                    </td>

                    <td class="right">
                        ₹{{ number_format((float) $beneficiaries->sum('TotalPaid'), 2) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>
</html>
