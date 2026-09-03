<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Physical Possession Report - {{ $application_no }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 9mm 8mm 9mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .outer-border {
            border: 2px solid #0f2b5c;
            padding: 10px 14px;
            background: #ffffff;
        }
        
        /* Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 7px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .header-logo-left {
            width: 58px;
            text-align: left;
        }
        .header-logo-right {
            width: 58px;
            text-align: right;
        }
        .header-center {
            text-align: center;
        }
        .govt-title {
            font-size: 15.5px;
            font-weight: 900;
            color: #0f2b5c;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0;
            line-height: 1.2;
        }
        .dept-title {
            font-size: 9px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 2px;
            margin-bottom: 4px;
        }
        .report-badge {
            display: inline-block;
            background-color: #0f2b5c;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: 800;
            padding: 3px 16px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        
        /* Meta Strip */
        .meta-strip {
            width: 100%;
            border-collapse: collapse;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            margin-bottom: 8px;
        }
        .meta-strip td {
            padding: 5px 8px;
            font-size: 9px;
            color: #334155;
            vertical-align: middle;
        }
        .meta-strip .meta-label {
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            font-size: 8px;
        }
        .meta-strip .meta-val {
            font-weight: 800;
            color: #0f172a;
            font-size: 9.5px;
        }

        /* Section Headings */
        .sec-header {
            background-color: #0f2b5c;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3.5px 8px;
            margin-bottom: 4px;
        }

        /* Tables & Layout */
        .w-full {
            width: 100%;
            border-collapse: collapse;
        }
        .two-col-layout {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .two-col-layout > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }
        .col-left {
            width: 50%;
            padding-right: 5px !important;
        }
        .col-right {
            width: 50%;
            padding-left: 5px !important;
        }
        
        .data-box {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 5px 7px;
            min-height: 116px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 3px 4px;
            vertical-align: top;
            font-size: 9px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }
        .lbl {
            width: 44%;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }
        .val {
            width: 56%;
            color: #0f172a;
            font-weight: 800;
            font-size: 9px;
        }

        /* Financial Ledger Grid */
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
            border: 1px solid #cbd5e1;
        }
        .ledger-table th {
            background-color: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            text-align: center;
        }
        .ledger-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9.5px;
            vertical-align: middle;
        }
        .ledger-subnote {
            font-size: 8px;
            color: #64748b;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 3px 8px;
            margin-bottom: 8px;
        }

        /* Site Verification & Plot Photo */
        .verify-layout {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .verify-layout > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }
        .verify-col-left {
            width: 63%;
            padding-right: 5px !important;
        }
        .verify-col-right {
            width: 37%;
            padding-left: 5px !important;
            text-align: center;
        }

        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            margin-bottom: 3px;
        }
        .checklist-table td {
            font-size: 8px;
            padding: 1.5px 0;
            color: #334155;
        }
        .check-icon {
            color: #166534;
            font-weight: bold;
        }

        .remarks-container {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            margin-top: 2px;
            font-style: italic;
            color: #334155;
            font-size: 8px;
            min-height: 24px;
            line-height: 1.25;
        }

        .photo-box {
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            padding: 3px;
            text-align: center;
        }
        .photo-box img {
            max-height: 100px;
            max-width: 175px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* Declaration Box */
        .declaration-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            margin-bottom: 8px;
        }
        .declaration-title {
            font-size: 8px;
            font-weight: bold;
            color: #0f2b5c;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .declaration-text {
            font-size: 7.8px;
            color: #334155;
            line-height: 1.25;
            text-align: justify;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 4px;
        }
        .sig-cell {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 12px;
        }
        .sig-space {
            height: 38px;
        }
        .sig-line {
            border-top: 1.5px solid #334155;
            padding-top: 3px;
            font-size: 8.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .sig-sub {
            font-size: 8px;
            color: #64748b;
            margin-top: 1px;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* Official Footer */
        .footer-note {
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
            margin-top: 5px;
            font-size: 7.2px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="outer-border">
        <!-- Official Government Header -->
        <table class="header-table">
            <tr>
                <td class="header-logo-left">
                    @if(!empty($haryana_emblem_base64))
                        <img src="{{ $haryana_emblem_base64 }}" style="max-height: 52px; max-width: 52px;" alt="Emblem of Haryana">
                    @endif
                </td>
                <td class="header-center">
                    <h1 class="govt-title">HARYANA SHEHRI VIKAS PRADHIKARAN</h1>
                    <div class="dept-title">Department of Housing for All &bull; Government of Haryana</div>
                    <div>
                        <span class="report-badge">PHYSICAL POSSESSION APPLICATION & SITE VERIFICATION REPORT</span>
                    </div>
                </td>
                <td class="header-logo-right">
                    @if(!empty($hsvp_logo_base64))
                        <img src="{{ $hsvp_logo_base64 }}" style="max-height: 52px; max-width: 52px;" alt="HSVP Logo">
                    @endif
                </td>
            </tr>
        </table>

        <!-- Metadata Strip -->
        <table class="meta-strip">
            <tr>
                <td>
                    <span class="meta-label">App No:</span> 
                    <span class="meta-val">{{ $application_no }}</span>
                </td>
                <td>
                    <span class="meta-label">PPP (Family ID):</span> 
                    <span class="meta-val">{{ $ppp_id }}</span>
                </td>
                <td>
                    <span class="meta-label">District/Estate:</span> 
                    <span class="meta-val">{{ $office_location }}</span>
                </td>
                <td>
                    <span class="meta-label">Inspection Date:</span> 
                    <span class="meta-val">{{ $verified_at !== '—' ? $verified_at : now()->format('d M Y') }}</span>
                </td>
                <td style="text-align: right;">
                    <span class="badge badge-success">Site Verified</span>
                </td>
            </tr>
        </table>

        <!-- Section 1 & 2: Side-by-Side 2 Column Tables -->
        <table class="two-col-layout">
            <tr>
                <!-- Section 1: Applicant Details -->
                <td class="col-left">
                    <div class="sec-header">1. Applicant Personal Details</div>
                    <div class="data-box">
                        <table class="data-table">
                            <tr>
                                <td class="lbl">Applicant Name:</td>
                                <td class="val">{{ strtoupper($name) }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Father / Husband:</td>
                                <td class="val">{{ strtoupper($father_name) }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Mobile Number:</td>
                                <td class="val">{{ $mobile }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Caste Category:</td>
                                <td class="val">{{ $purchaser?->CasteCategoryName ?? 'General' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Member ID:</td>
                                <td class="val">{{ $member_id }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Address:</td>
                                <td class="val" style="font-size: 8px;">{{ $address }}</td>
                            </tr>
                        </table>
                    </div>
                </td>

                <!-- Section 2: Property & Allotment Details -->
                <td class="col-right">
                    <div class="sec-header">2. Property & Allotment Details</div>
                    <div class="data-box">
                        <table class="data-table">
                            <tr>
                                <td class="lbl">Plot / Asset ID:</td>
                                <td class="val">{{ $plot_no }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Asset Name:</td>
                                <td class="val">{{ $asset_name }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Asset Size:</td>
                                <td class="val">{{ $asset_size }} {{ $asset_unit }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Allotment Date:</td>
                                <td class="val">{{ $allotment_date }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Sector / Ward:</td>
                                <td class="val">{{ $sector }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Urban Estate / City:</td>
                                <td class="val">{{ $urban_estate }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Section 3: Financial Ledger Summary -->
        <div class="sec-header">3. Financial Ledger & Cost Summary</div>
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Total Property Cost</th>
                    <th style="width: 25%;">Total Received (Paid)</th>
                    <th style="width: 25%;">Balance Outstanding</th>
                    <th style="width: 25%;">Ledger Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; font-weight: 800; font-size: 10px;">
                        Rs. {{ number_format($flat_cost, 2) }}
                    </td>
                    <td style="text-align: center; font-weight: 800; color: #166534; font-size: 10px;">
                        Rs. {{ number_format($total_paid, 2) }}
                    </td>
                    <td style="text-align: center; font-weight: 800; color: {{ $pending_amount > 0 ? '#b91c1c' : '#166534' }}; font-size: 10px;">
                        Rs. {{ number_format($pending_amount, 2) }}
                    </td>
                    <td style="text-align: center;">
                        @if($pending_amount <= 0)
                            <span class="badge badge-success">Fully Paid (Eligible)</span>
                        @else
                            <span class="badge badge-warning">Partial Outstanding</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="ledger-subnote">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33%;"><strong>Initial Deposit:</strong> Rs. {{ number_format($initial_deposit, 2) }}</td>
                    <td style="width: 33%; text-align: center;"><strong>Installments Realized:</strong> Rs. {{ number_format($installment_paid, 2) }}</td>
                    <td style="width: 34%; text-align: right;"><strong>Total Paid:</strong> Rs. {{ number_format($total_paid, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Section 4: Site Verification Report & Plot Photo -->
        <table class="verify-layout">
            <tr>
                <!-- Left: Site Verification details -->
                <td class="verify-col-left">
                    <div class="sec-header">4. Site Engineer Verification Report</div>
                    <div class="data-box" style="min-height: 125px;">
                        <table class="data-table">
                            <tr>
                                <td class="lbl" style="width: 35%;">Verified Officer:</td>
                                <td class="val" style="width: 65%;">{{ strtoupper($site_engineer_name) }} (Site Engineer)</td>
                            </tr>
                            <tr>
                                <td class="lbl">Inspection Date/Time:</td>
                                <td class="val">{{ $verified_at }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">GPS Coordinates:</td>
                                <td class="val" style="font-family: monospace; font-size: 8.5px;">
                                    Lat: {{ $latitude }} | Long: {{ $longitude }}
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Verification Checklist -->
                        <table class="checklist-table">
                            <tr>
                                <td style="width: 50%;"><span class="check-icon">&#10003;</span> Boundary Demarcation Verified</td>
                                <td style="width: 50%;"><span class="check-icon">&#10003;</span> Approach Road Access Clear</td>
                            </tr>
                            <tr>
                                <td><span class="check-icon">&#10003;</span> Site Free from Encroachment</td>
                                <td><span class="check-icon">&#10003;</span> Allottee Present On-Site</td>
                            </tr>
                        </table>

                        <div style="font-size: 8px; font-weight: bold; color: #475569; text-transform: uppercase; margin-top: 2px;">
                            Site Verification Remarks / Comments:
                        </div>
                        <div class="remarks-container">
                            "{{ $remarks }}"
                        </div>
                    </div>
                </td>

                <!-- Right: Plot Photo -->
                <td class="verify-col-right">
                    <div class="sec-header">On-Site Plot Photo</div>
                    <div class="photo-box" style="min-height: 125px;">
                        @if(!empty($plot_image_base64))
                            <img src="{{ $plot_image_base64 }}" alt="Plot Photo with Applicant">
                            <div style="font-size: 7.5px; color: #64748b; margin-top: 3px; font-weight: bold;">
                                Applicant with Plot @ GPS Coordinates
                            </div>
                        @else
                            <div style="color: #94a3b8; padding-top: 40px; font-size: 8.5px;">
                                [ Plot Photo Uploaded on Portal ]
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Section 5: Allottee Acceptance & Handover Declaration -->
        <div class="declaration-box">
            <div class="declaration-title">&#9733; Allottee Handover & Physical Possession Declaration</div>
            <div class="declaration-text">
                I hereby declare that I have physically visited and inspected the allotted plot/property on-site in the presence of the HSVP Site Engineer. The boundary demarcations and physical condition of the site have been identified and handed over to my complete satisfaction. I undertake to abide by all the terms, conditions, and building bylaws of HSVP and the Department of Housing for All, Government of Haryana.
            </div>
        </div>

        <!-- Section 6: Signature Blocks -->
        <table class="signature-table">
            <tr>
                <td class="sig-cell">
                    <div class="sig-space"></div>
                    <div class="sig-line">Signature / Thumb Impression of Allottee</div>
                    <div class="sig-sub">Name: {{ strtoupper($name) }}</div>
                    <div class="sig-sub">Applicant / Legal Allottee</div>
                </td>
                <td class="sig-cell">
                    <div class="sig-space"></div>
                    <div class="sig-line">Signature & Official Seal of Site Engineer</div>
                    <div class="sig-sub">Name: {{ strtoupper($site_engineer_name) }}</div>
                    <div class="sig-sub">Date: {{ $verified_at !== '—' ? $verified_at : now()->format('d M Y') }}</div>
                </td>
            </tr>
        </table>

        <!-- Official Footer Note -->
        <div class="footer-note">
            This Physical Possession Application & Verification Report is generated electronically via Haryana Shehri Vikas Pradhikaran (HSVP) Portal. &bull; Page 1 of 1 &bull; Document Ref: PP-{{ $application->application_number }}-{{ strtoupper(substr($application->secure_id, 0, 8)) }} &bull; {{ now()->format('d/m/Y, h:i A') }}
        </div>
    </div>
</body>
</html>
