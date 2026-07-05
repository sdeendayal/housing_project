<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Physical Possession Appointment Slip - MMGAY</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.4;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0058bc;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .logo-cell {
            width: 15%;
            text-align: left;
        }
        .title-cell {
            width: 70%;
            text-align: center;
        }
        .info-cell {
            width: 15%;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
        .title-main {
            font-size: 16px;
            font-weight: bold;
            color: #0058bc;
            margin: 0;
            text-transform: uppercase;
        }
        .title-sub {
            font-size: 11px;
            color: #555555;
            margin: 2px 0 0 0;
            font-weight: bold;
        }
        .slip-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            background-color: #f0f7ff;
            color: #0058bc;
            padding: 6px;
            border: 1px solid #c8e1ff;
            border-radius: 4px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #0058bc;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 3px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #555555;
            width: 30%;
        }
        .value {
            color: #111111;
            width: 70%;
        }
        .two-col-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .two-col-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px 0 0;
        }
        .instructions-box {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .instructions-box ol {
            margin: 5px 0 0 15px;
            padding: 0;
        }
        .instructions-box li {
            margin-bottom: 4px;
            font-size: 10px;
            color: #444444;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #666;
            margin-bottom: 5px;
        }
        .signature-text {
            font-size: 10px;
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <span style="font-size: 24px; font-weight: bold; color: #0058bc;">HRY</span>
            </td>
            <td class="title-cell">
                <div class="title-main">Department of Housing For All</div>
                <div class="title-sub">Government of Haryana</div>
            </td>
            <td class="info-cell">
                Date: {{ date('d-m-Y') }}
            </td>
        </tr>
    </table>

    <div class="slip-title">
        Physical Possession Appointment Slip
    </div>

    <table class="two-col-table">
        <tr>
            <td>
                <div class="section-title">Beneficiary Details</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Slip ID:</td>
                        <td class="value" style="font-weight: bold; color: #0058bc;">{{ $application->slip_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">App Number:</td>
                        <td class="value" style="font-weight: bold;">{{ $application->application_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Name:</td>
                        <td class="value">{{ $owner->OwnerName }}</td>
                    </tr>
                    <tr>
                        <td class="label">Father Name:</td>
                        <td class="value">{{ $owner->FatherHusbandName ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Mobile:</td>
                        <td class="value">{{ $owner->MobileNo }}</td>
                    </tr>
                    <tr>
                        <td class="label">PPP Family ID:</td>
                        <td class="value">{{ $owner->PPPId ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <div class="section-title">Allotted Property Details</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Flat/Plot ID:</td>
                        <td class="value">{{ $owner->FlatId }}</td>
                    </tr>
                    <tr>
                        <td class="label">Flat Number:</td>
                        <td class="value" style="font-weight: bold;">{{ $owner->FlatNo }}</td>
                    </tr>
                    <tr>
                        <td class="label">Village Name:</td>
                        <td class="value">{{ $owner->VillageName }}</td>
                    </tr>
                    <tr>
                        <td class="label">Block Name:</td>
                        <td class="value">{{ $owner->BlockName }}</td>
                    </tr>
                    <tr>
                        <td class="label">District:</td>
                        <td class="value">{{ $owner->DistrictName }}</td>
                    </tr>
                    <tr>
                        <td class="label">Scheme:</td>
                        <td class="value" style="font-weight: bold; color: #0058bc;">MMGAY (Mukhyamantri Gramin Awas Yojana)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Appointment Schedule & Location Details</div>
    <table class="info-table">
        <tr>
            <td class="label" style="width: 25%;">Visit Status:</td>
            <td class="value" style="width: 75%; font-weight: bold; color: #d97706;">
                {{ $application->physical_possession_status }}
            </td>
        </tr>
        @if($application->physical_possession_status === 'Visit Scheduled')
            <tr>
                <td class="label" style="width: 25%; vertical-align: top;">Offered Slots:</td>
                <td class="value" style="width: 75%; font-weight: bold; color: #0058bc; line-height: 1.5;">
                    <div style="margin-bottom: 5px; color: #dc2626; font-size: 9px; font-weight: bold; text-transform: uppercase;">[CITIZEN SLOT CONFIRMATION PENDING]</div>
                    @if($application->visit_slot_1)
                        Slot 1: {{ date('d F Y, h:i A (l)', strtotime($application->visit_slot_1)) }}<br>
                    @endif
                    @if($application->visit_slot_2)
                        Slot 2: {{ date('d F Y, h:i A (l)', strtotime($application->visit_slot_2)) }}<br>
                    @endif
                    @if($application->visit_slot_3)
                        Slot 3: {{ date('d F Y, h:i A (l)', strtotime($application->visit_slot_3)) }}
                    @endif
                </td>
            </tr>
        @else
            <tr>
                <td class="label" style="width: 25%;">Scheduled Date:</td>
                <td class="value" style="width: 75%; font-weight: bold; font-size: 11px; color: #0058bc;">
                    @if($application->citizen_visit_date)
                        {{ date('d F Y (l)', strtotime($application->citizen_visit_date)) }}
                    @else
                        Awaiting confirmation / scheduling
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label" style="width: 25%;">Scheduled Time:</td>
                <td class="value" style="width: 75%; font-weight: bold; font-size: 11px; color: #0058bc;">
                    @if($application->citizen_visit_date)
                        {{ date('h:i A', strtotime($application->citizen_visit_date)) }}
                    @else
                        Awaiting confirmation / scheduling
                    @endif
                </td>
            </tr>
        @endif
    </table>

    <div class="instructions-box">
        <div style="font-weight: bold; color: #0058bc; font-size: 11px; text-transform: uppercase;">Instructions for Beneficiary:</div>
        <ol>
            <li>Please arrive at the allotted site 15 minutes before the scheduled time slot.</li>
            <li>Carry a printed copy of this **Appointment Slip** along with a valid Identity proof (Aadhaar Card / PPP Family ID).</li>
            <li>Ensure you have completed all pending payments for the flat and carry copies of payment receipts.</li>
            <li>The Block Development Officer (BDO) or physical possession verification officer will meet you at the site to verify boundaries and record GPS coordinates.</li>
            @if(!empty($application->visit_instructions))
                <li style="font-weight: bold; color: #0058bc;">Additional BDO Guidelines: "{{ $application->visit_instructions }}"</li>
            @endif
        </ol>
    </div>

    <table class="footer-table">
        <tr>
            <td style="width: 50%; text-align: left;">
                <div class="signature-line" style="margin-top: 30px;"></div>
                <div class="signature-text">Signature of Beneficiary</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="signature-line" style="margin-top: 30px; margin-left: auto;"></div>
                <div class="signature-text" style="padding-right: 20px;">For Department of Housing For All</div>
            </td>
        </tr>
    </table>

</body>
</html>
