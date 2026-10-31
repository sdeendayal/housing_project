<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>EWS Builder Flats Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            color: #1e3a8a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 10px;
            color: #666;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #1e3a8a;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Housing For All, Haryana</h1>
        <p>EWS Builder Flats Allotment Registry Database Report | Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">S.No.</th>
                <th style="width: 15%;">District Name</th>
                <th style="width: 15%;">Town Name</th>
                <th style="width: 25%;">Project Name</th>
                <th style="width: 15%;">Block / Tower No.</th>
                <th style="width: 15%;">Floor Details</th>
                <th style="width: 10%;">Flat No.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($flats as $index => $flat)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $flat->district_name }}</strong></td>
                    <td>{{ $flat->town_name }}</td>
                    <td>{{ $flat->project_name }}</td>
                    <td>{{ $flat->block_tower_number }}</td>
                    <td>{{ $flat->floor }}</td>
                    <td style="color: #4f46e5; font-weight: bold;">{{ $flat->flat_number }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #666;">No registered EWS flat records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        © 2026 Housing For All Department, Haryana. Generated securely in Developer Sandbox.
    </div>

</body>
</html>
