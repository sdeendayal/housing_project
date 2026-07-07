<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Physical Possession Report</title>
    <style>
        @page { margin: 40px 45px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.45;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #1a365d;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 10px;
            color: #4a5568;
            font-weight: bold;
        }
        .section-title {
            background-color: #f7fafc;
            border-left: 3px solid #1a365d;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 11px;
            color: #1a365d;
            margin-top: 15px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        .info-table td {
            width: 50%;
            border-bottom: 1px solid #edf2f7;
        }
        .label {
            color: #718096;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }
        .value {
            font-weight: bold;
            color: #2d3748;
            font-size: 11px;
        }
        .grid-table {
            border: 1px solid #cbd5e0;
        }
        .grid-table th {
            background-color: #edf2f7;
            font-weight: bold;
            color: #4a5568;
            border: 1px solid #cbd5e0;
            font-size: 9px;
            text-transform: uppercase;
        }
        .grid-table td {
            border: 1px solid #cbd5e0;
        }
        .remarks-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            min-height: 40px;
            font-style: italic;
            color: #4a5568;
        }
        .photo-container {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .photo-container img {
            border: 1px solid #cbd5e0;
            padding: 4px;
            background-color: #fff;
            max-height: 180px;
            max-width: 320px;
            object-fit: contain;
        }
        .footer-signatures {
            margin-top: 40px;
            width: 100%;
        }
        .sig-col {
            width: 50%;
            text-align: center;
        }
        .sig-line {
            margin-top: 50px;
            border-top: 1px solid #4a5568;
            display: inline-block;
            width: 200px;
            padding-top: 5px;
            font-weight: bold;
            color: #2d3748;
            font-size: 10px;
        }
        .meta-info {
            margin-top: 30px;
            font-size: 8px;
            color: #a0aec0;
            border-top: 1px dashed #e2e8f0;
            padding-top: 5px;
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #c6f6d5;
            color: #22543d;
        }
        .badge-warning {
            background-color: #feebc8;
            color: #744210;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Haryana Shehri Vikas Pradhikaran</h2>
        <p>PHYSICAL POSSESSION APPLICATION & SITE VERIFICATION REPORT</p>
    </div>

    <!-- Section 1: Applicant Information -->
    <div class="section-title">1. Applicant Personal Details</div>
    <table class="info-table">
        <tr>
            <td>
                <span class="label">Applicant Name</span>
                <span class="value">{{ strtoupper($name) }}</span>
            </td>
            <td>
                <span class="label">Father's / Husband's Name</span>
                <span class="value">{{ strtoupper($father_name) }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Mobile Number</span>
                <span class="value">{{ $mobile }}</span>
            </td>
            <td>
                <span class="label">Correspondence Address</span>
                <span class="value">{{ $address }}</span>
            </td>
        </tr>
    </table>

    <!-- Section 2: Property details -->
    <div class="section-title">2. Property Allotment Details</div>
    <table class="info-table">
        <tr>
            <td>
                <span class="label">Application Number</span>
                <span class="value">{{ $application_no }}</span>
            </td>
            <td>
                <span class="label">PPP ID</span>
                <span class="value">{{ $ppp_id }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Plot No / Asset ID</span>
                <span class="value">{{ $plot_no }}</span>
            </td>
            <td>
                <span class="label">Sector Name</span>
                <span class="value">{{ $sector }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Asset Details</span>
                <span class="value">{{ $asset_name }} ({{ $asset_size }} {{ $asset_unit }})</span>
            </td>
            <td>
                <span class="label">Urban Estate / City</span>
                <span class="value">{{ $urban_estate }}</span>
            </td>
        </tr>
    </table>

    <!-- Section 3: Financial Summary -->
    <div class="section-title">3. Financial Ledger Summary</div>
    <table class="grid-table">
        <thead>
            <tr>
                <th>Total Property Cost</th>
                <th>Total Amount Received (Paid)</th>
                <th>Balance Outstanding</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold; text-align: right; font-size: 12px;">₹ {{ number_format($flat_cost, 2) }}</td>
                <td style="font-weight: bold; text-align: right; color: #2f855a; font-size: 12px;">₹ {{ number_format($total_paid, 2) }}</td>
                <td style="font-weight: bold; text-align: right; color: #c53030; font-size: 12px;">₹ {{ number_format($pending_amount, 2) }}</td>
                <td style="text-align: center;">
                    @if($pending_amount <= 0)
                        <span class="badge badge-success">Fully Paid</span>
                    @else
                        <span class="badge badge-warning">Partially Paid</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Section 4: Site Engineer Verification details -->
    <div class="section-title">4. Site Engineer Verification Report</div>
    <table class="info-table">
        <tr>
            <td>
                <span class="label">Verification Date & Time</span>
                <span class="value">{{ $verified_at }}</span>
            </td>
            <td>
                <span class="label">GPS Location Coordinates</span>
                <span class="value">Lat: {{ $latitude }} &middot; Lng: {{ $longitude }}</span>
            </td>
        </tr>
    </table>

    <div class="label" style="margin-left: 8px;">Site Verification Comments & Remarks</div>
    <div class="remarks-box" style="margin: 0 8px 15px 8px;">
        "{{ $remarks }}"
    </div>

    @if($plot_image_base64)
        <div class="label" style="margin-left: 8px;">On-Site Plot Photo with Applicant</div>
        <div class="photo-container">
            <img src="{{ $plot_image_base64 }}" alt="Plot Photo">
        </div>
    @endif

    <!-- Section 5: Signatures -->
    <table class="footer-signatures">
        <tr>
            <td class="sig-col"></td>
            <td class="sig-col">
                <div class="sig-line">Signature of Site Engineer</div>
                <div style="font-size: 8px; color: #718096; margin-top: 4px;">Name: {{ strtoupper($site_engineer_name) }}</div>
                <div style="font-size: 8px; color: #718096; margin-top: 2px;">Date: {{ $verified_at }}</div>
            </td>
        </tr>
    </table>

    <div class="meta-info">
        Generated electronically via HSVP Portal on {{ now()->format('d M Y, h:i A') }} | Secure Verification Document
    </div>
</body>
</html>
