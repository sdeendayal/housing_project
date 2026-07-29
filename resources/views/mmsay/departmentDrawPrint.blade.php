<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>District Draw Summary</title>

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
            color: #0f172a;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .header {
            margin-bottom: 16px;
            text-align: center;
        }

        .header h1 {
            margin: 0 0 5px;
            font-size: 20px;
        }

        .header p {
            margin: 0;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 9px;
            border: 1px solid #cbd5e1;
        }

        th {
            background: #f1f5f9;
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .total {
            background: #f8fafc;
            font-weight: bold;
        }

        .no-print {
            margin-bottom: 12px;
            text-align: right;
        }

        button {
            padding: 8px 16px;
            border: 0;
            border-radius: 5px;
            background: #1e293b;
            color: white;
            cursor: pointer;
        }

        @media print {
            .no-print {
                display: none;
            }

            thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button type="button" onclick="window.print()">
            Print
        </button>
    </div>

    <div class="header">
        <h1>Lucky Draw – District Summary</h1>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="center">S.No.</th>
                <th>District</th>
                <th class="center">Total Assets</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($drawDistricts as $district)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $district->DistrictName }}</td>
                    <td class="center">
                        {{ number_format($district->total_assets) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="center">
                        No records found.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr class="total">
                <td colspan="2">Grand Total</td>
                <td class="center">{{ number_format($grandTotal) }}</td>
            </tr>
        </tfoot>
    </table>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>