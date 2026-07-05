<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MMGAY Physical Possession Report</title>
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
            border-bottom: 2px solid #0058bc;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #0058bc;
            font-size: 16px;
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
            border-left: 3px solid #0058bc;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 11px;
            color: #0058bc;
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
        .footer-signatures {
            margin-top: 50px;
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
            margin-top: 50px;
            font-size: 8px;
            color: #a0aec0;
            border-top: 1px dashed #e2e8f0;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>MUKHYAMANTRI GRAMIN AWAS YOJANA (MMGAY)</h2>
        <p>PHYSICAL POSSESSION APPLICATION & SITE VERIFICATION REPORT</p>
    </div>

    <!-- Section 1: Applicant Information -->
    <div class="section-title">1. Applicant Personal Details</div>
    <table class="info-table">
        <tr>
            <td>
                <span class="label">Applicant Name</span>
                <span class="value">{{ strtoupper($application->applicant_name) }}</span>
            </td>
            <td>
                <span class="label">Father's / Husband's Name</span>
                <span class="value">{{ strtoupper($owner->FatherHusbandName ?? '—') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Mobile Number</span>
                <span class="value">{{ $application->mobile }}</span>
            </td>
            <td>
                <span class="label">Correspondence Address</span>
                <span class="value">{{ $owner->OwnerAddress ?? '—' }}</span>
            </td>
        </tr>
    </table>

    <!-- Section 2: Property details -->
    <div class="section-title">2. Property Allotment Details</div>
    <table class="info-table">
        <tr>
            <td>
                <span class="label">Application Number</span>
                <span class="value">{{ $application->application_number }}</span>
            </td>
            <td>
                <span class="label">PPP ID (Family ID)</span>
                <span class="value">{{ $owner->PPPId ?? '—' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Plot No / Flat No</span>
                <span class="value">{{ $owner->FlatNo ?? '—' }}</span>
            </td>
            <td>
                <span class="label">Village Name</span>
                <span class="value">{{ $owner->VillageName ?? '—' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Block Name</span>
                <span class="value">{{ $owner->BlockName ?? '—' }}</span>
            </td>
            <td>
                <span class="label">District Name</span>
                <span class="value">{{ $owner->DistrictName ?? '—' }}</span>
            </td>
        </tr>
    </table>

    <!-- Section 3: Site Verification Details -->
    <div class="section-title">3. Site Verification Details</div>
    <table class="info-table">
        <tr>
            <td>
                <span class="label">Verification Date & Time</span>
                <span class="value">{{ $verified_at }}</span>
            </td>
            <td>
                <span class="label">Verifying Authority (BDO)</span>
                <span class="value">{{ $bdoName }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Physical Possession Status</span>
                <span class="value" style="color: #2f855a;">VERIFIED / COMPLETED</span>
            </td>
            <td>
                <span class="label">Scheme Name</span>
                <span class="value">MMGAY (MUKHYAMANTRI GRAMIN AWAS YOJANA)</span>
            </td>
        </tr>
    </table>

    <!-- Section 4: Signatures -->
    <table class="footer-signatures">
        <tr>
            <td class="sig-col">
                <div class="sig-line">Signature of Applicant / Beneficiary</div>
                <div style="font-size: 8px; color: #718096; margin-top: 4px;">Name: {{ strtoupper($application->applicant_name) }}</div>
            </td>
            <td class="sig-col">
                <div class="sig-line">Signature of Block Development Officer</div>
                <div style="font-size: 8px; color: #718096; margin-top: 4px;">Name: {{ strtoupper($bdoName) }}</div>
                <div style="font-size: 8px; color: #718096; margin-top: 2px;">Date: {{ $verified_at }}</div>
            </td>
        </tr>
    </table>

    <div class="meta-info">
        Generated electronically via MMGAY Portal on {{ now()->format('d M Y, h:i A') }} | Secure Verification Document
    </div>
</body>
</html>
