<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 16px; }
        .header h2 { color: #1e40af; margin: 0 0 4px; font-size: 18px; }
        .header p { margin: 0; font-size: 11px; color: #475569; }
        .badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 12px;
            background: #059669;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td, th { border: 1px solid #cbd5e1; padding: 8px; vertical-align: top; }
        th { background: #f1f5f9; width: 38%; text-align: left; font-weight: bold; color: #1e3a8a; }
        .amount-box {
            margin-top: 18px;
            padding: 12px;
            border: 2px solid #059669;
            background: #ecfdf5;
            text-align: center;
        }
        .amount-box .label { font-size: 11px; color: #047857; text-transform: uppercase; letter-spacing: 0.05em; }
        .amount-box .value { font-size: 22px; font-weight: bold; color: #065f46; margin-top: 4px; }
        .footer { margin-top: 28px; font-size: 10px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Housing For All Department, Haryana</h2>
        <p>Mukhyamantri Shahri Awas Yojana (MMSAY)</p>
        <span class="badge">Cash Receipt</span>
    </div>

    <table>
        <tr><th>Receipt No.</th><td><strong>{{ $receipt['receipt_number'] }}</strong></td></tr>
        <tr><th>Payment Date</th><td>{{ $receipt['payment_date'] }}</td></tr>
        <tr><th>Payment Mode</th><td>{{ $receipt['mode'] }}</td></tr>
        <tr><th>Applicant Name</th><td>{{ $receipt['purchaser_name'] }}</td></tr>
        <tr><th>Father Name</th><td>{{ $receipt['father_name'] }}</td></tr>
        <tr><th>Application No.</th><td>{{ $receipt['application_no'] }}</td></tr>
        <tr><th>Mobile</th><td>{{ $receipt['mobile'] }}</td></tr>
        <tr><th>Asset / Property</th><td>{{ $receipt['asset_name'] }} ({{ $receipt['asset_number'] }})</td></tr>
        <tr><th>Estate Manager Office</th><td>{{ $receipt['em_office'] }}</td></tr>
        <tr><th>District</th><td>{{ $receipt['district'] }}</td></tr>
        <tr><th>City</th><td>{{ $receipt['city'] }}</td></tr>
        <tr><th>Sector</th><td>{{ $receipt['sector'] }}</td></tr>
    </table>

    <div class="amount-box">
        <div class="label">Amount Received</div>
        <div class="value">{{ $receipt['amount'] }}</div>
        <div style="font-size:10px;color:#047857;margin-top:4px;">(₹ {{ $receipt['amount_numeric'] }})</div>
    </div>

    <p class="footer">This is a computer generated cash receipt. No signature required.</p>
</body>
</html>
