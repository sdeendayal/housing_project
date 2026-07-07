<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MMGAY Physical Possession API Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #4f46e5;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
            color: #1f2937;
        }
        .subtitle {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }
        h2 {
            font-size: 13px;
            color: #4f46e5;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .endpoint-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .endpoint-header {
            margin-bottom: 8px;
        }
        .method {
            display: inline-block;
            padding: 2px 6px;
            font-weight: bold;
            border-radius: 3px;
            font-size: 9px;
            color: #fff;
            margin-right: 5px;
        }
        .method.get { background-color: #059669; }
        .method.post { background-color: #2563eb; }
        
        .path {
            font-family: "Courier New", monospace;
            font-size: 10px;
            font-weight: bold;
            color: #1f2937;
        }
        .auth-badge {
            float: right;
            font-size: 8px;
            background-color: #e5e7eb;
            color: #4b5563;
            padding: 1px 4px;
            border-radius: 2px;
            font-weight: bold;
        }
        .desc {
            margin: 5px 0;
            color: #4b5563;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
        }
        .code-block {
            background-color: #1f2937;
            color: #f9fafb;
            font-family: "Courier New", monospace;
            font-size: 8.5px;
            padding: 8px;
            border-radius: 3px;
            white-space: pre-wrap;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .section-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">MMGAY Portal</div>
        <div class="title">Physical Possession - Mobile API Specification</div>
        <div class="subtitle">Version 1.0 (Sanctum Authenticated) &bull; Base URL: /api/mmgay</div>
    </div>

    <h2>1. Authentication APIs</h2>

    <!-- CAPTCHA -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">No Auth</span>
            <span class="method get">GET</span>
            <span class="path">/refresh-captcha</span>
        </div>
        <div class="desc">Generates a stateless numeric captcha for the login pages.</div>
        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "captcha_key": "4c9472e3-d7ab-4b2a-a92c-5507d9fde99f",
  "captcha": 5831
}</div>
    </div>

    <!-- BDO LOGIN -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">No Auth</span>
            <span class="method post">POST</span>
            <span class="path">/bdo/login</span>
        </div>
        <div class="desc">Authenticates a Block Development Officer (BDO) via email and password.</div>
        
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>email</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>BDO registered email address.</td>
                </tr>
                <tr>
                    <td>password</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>BDO login password.</td>
                </tr>
                <tr>
                    <td>captcha</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>Entered captcha number value.</td>
                </tr>
                <tr>
                    <td>captcha_key</td>
                    <td>string</td>
                    <td>No</td>
                    <td>UUID key received from /refresh-captcha API.</td>
                </tr>
            </tbody>
        </table>

        <div class="response-title"><strong>Success Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "message": "Login successful! Welcome back BDO.",
  "token": "3|AbCDeFgHiJkLmNoP...",
  "user": {
    "id": 12,
    "name": "BDO Nissing",
    "email": "nissing.bdo@gmail.com",
    "mobile": "9998887771",
    "role": "mmgay_bdo",
    "block_id": 4,
    "block_name": "Nissing"
  }
}</div>
    </div>

    <!-- VILLAGER SEND OTP -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">No Auth</span>
            <span class="method post">POST</span>
            <span class="path">/villager/login/send-otp</span>
        </div>
        <div class="desc">Initiates Mobile OTP sending process for allotted Villagers.</div>
        
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>mobile</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>Registered 10-digit mobile number.</td>
                </tr>
                <tr>
                    <td>captcha</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>Captcha value.</td>
                </tr>
                <tr>
                    <td>captcha_key</td>
                    <td>string</td>
                    <td>No</td>
                    <td>Captcha UUID key.</td>
                </tr>
            </tbody>
        </table>

        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "message": "OTP has been successfully sent to your mobile number.",
  "resend_after": 60
}</div>
    </div>

    <!-- VILLAGER VERIFY OTP -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">No Auth</span>
            <span class="method post">POST</span>
            <span class="path">/villager/login/verify</span>
        </div>
        <div class="desc">Verifies the OTP sent to the villager\'s mobile. Returns the Sanctum Bearer Token.</div>
        
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>mobile</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>Mobile number.</td>
                </tr>
                <tr>
                    <td>otp</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>6-digit numeric OTP received via SMS.</td>
                </tr>
            </tbody>
        </table>

        <div class="response-title"><strong>Success Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "message": "Login successful! Welcome to MMGAY Portal.",
  "token": "4|XyZwPaSsWoRdToKeN...",
  "user": {
    "id": 85,
    "name": "Ram Lal",
    "mobile": "9876543210",
    "email": "ramlal@example.com",
    "role": "villager",
    "block_id": 4,
    "block_name": "Nissing"
  }
}</div>
    </div>

    <div class="section-break"></div>

    <h2>2. BDO Officer APIs (Token Authenticated)</h2>

    <!-- BDO DASHBOARD -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method get">GET</span>
            <span class="path">/bdo/dashboard</span>
        </div>
        <div class="desc">Returns BDO block statistics and recent application records.</div>
        
        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "stats": {
    "total_eligible": 45,
    "not_scheduled": 12,
    "awaiting_citizen": 5,
    "awaiting_coordinates": 8,
    "awaiting_bdo_doc": 4,
    "verified": 16
  },
  "recent_applications": [
    {
      "id": 1,
      "application_number": "PP-MMGAY-2026-1025",
      "applicant_name": "Sunder Singh",
      "physical_possession_status": "Visit Scheduled",
      "possession_date": "2026-07-15"
    }
  ]
}</div>
    </div>

    <!-- ELIGIBILITY LIST -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method get">GET</span>
            <span class="path">/bdo/eligibility-list</span>
        </div>
        <div class="desc">Lists all eligible beneficiaries who have completed their payments. Query params: ?search= &bull; ?all=1</div>
        
        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "applications": {
    "current_page": 1,
    "data": [
      {
        "id": 232,
        "secure_id": "8e5c123490abcde...",
        "applicant_name": "Satish Kumar",
        "father_name": "Devi Lal",
        "mobile": "9992223334",
        "district_name": "Karnal",
        "block_name": "Nissing",
        "application_number": "PP-MMGAY-2026-2342",
        "physical_possession_status": "Eligible for Physical Possession"
      }
    ],
    "total": 15
  }
}</div>
    </div>

    <!-- CAPACITY CHECK -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method get">GET</span>
            <span class="path">/bdo/schedule/capacity/check?date=YYYY-MM-DD</span>
        </div>
        <div class="desc">Returns count of scheduled visits for each hour of the selected date. Used to prevent overbooking (>10 visits/hr).</div>
        
        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "counts": {
    "9": 2,
    "10": 10,
    "11": 0,
    "12": 1,
    "13": 5,
    "14": 8,
    "15": 0,
    "16": 0
  }
}</div>
    </div>

    <!-- SCHEDULE GET -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method get">GET</span>
            <span class="path">/bdo/schedule/{secure_id}</span>
        </div>
        <div class="desc">Fetches beneficiary details and active application data prior to scheduling.</div>
        
        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "application": {
    "id": 5,
    "application_number": "PP-MMGAY-2026-1025",
    "physical_possession_status": "Eligible for Physical Possession"
  },
  "owner": {
    "OwnerId": 232,
    "OwnerName": "Satish Kumar",
    "FlatNo": "Block-C/Plot-45"
  }
}</div>
    </div>

    <!-- SCHEDULE SAVE -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method post">POST</span>
            <span class="path">/bdo/schedule/{secure_id}</span>
        </div>
        <div class="desc">Submits three optional date/time slots for physical field visits.</div>
        
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>slot_date_1</td>
                    <td>date (YYYY-MM-DD)</td>
                    <td>Yes</td>
                    <td>Proposed Date option 1. Must be after today.</td>
                </tr>
                <tr>
                    <td>slot_time_1</td>
                    <td>string (HH:MM)</td>
                    <td>Yes</td>
                    <td>Proposed Time option 1. Must be between 09:00 - 16:00.</td>
                </tr>
                <tr>
                    <td>slot_date_2</td>
                    <td>date (YYYY-MM-DD)</td>
                    <td>Yes</td>
                    <td>Proposed Date option 2.</td>
                </tr>
                <tr>
                    <td>slot_time_2</td>
                    <td>string (HH:MM)</td>
                    <td>Yes</td>
                    <td>Proposed Time option 2.</td>
                </tr>
                <tr>
                    <td>slot_date_3</td>
                    <td>date (YYYY-MM-DD)</td>
                    <td>Yes</td>
                    <td>Proposed Date option 3.</td>
                </tr>
                <tr>
                    <td>slot_time_3</td>
                    <td>string (HH:MM)</td>
                    <td>Yes</td>
                    <td>Proposed Time option 3.</td>
                </tr>
                <tr>
                    <td>visit_instructions</td>
                    <td>string</td>
                    <td>No</td>
                    <td>Special instructions for field visit.</td>
                </tr>
            </tbody>
        </table>

        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "message": "Physical Possession visit has been successfully scheduled."
}</div>
    </div>

    <div class="section-break"></div>

    <!-- BDO VERIFY GET -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method get">GET</span>
            <span class="path">/bdo/verify/{secure_id}</span>
        </div>
        <div class="desc">Retrieves verification details, beneficiary status, uploaded files, and historic remarks/logs.</div>
        
        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "application": {
    "id": 5,
    "physical_possession_status": "Slot Selected",
    "citizen_visit_date": "2026-07-15 10:00:00",
    "plot_image": null
  },
  "owner": {
    "OwnerName": "Satish Kumar",
    "FlatNo": "Plot-45"
  },
  "logs": [
    {
      "old_status": "Visit Scheduled",
      "new_status": "Slot Selected",
      "remarks": "Visit slot selected by Citizen"
    }
  ]
}</div>
    </div>

    <!-- BDO VERIFY SAVE (STAGE 1 & 2) -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method post">POST</span>
            <span class="path">/bdo/verify/{secure_id}</span>
        </div>
        <div class="desc">Processes verification stages. Form-data format is required for file uploads.</div>
        
        <p><strong>Stage 1 Parameters (When Status is "Slot Selected"):</strong></p>
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>remarks</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>Verification comments.</td>
                </tr>
                <tr>
                    <td>latitude</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>GPS Latitude of site.</td>
                </tr>
                <tr>
                    <td>longitude</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>GPS Longitude of site.</td>
                </tr>
                <tr>
                    <td>plot_image</td>
                    <td>file (image)</td>
                    <td>Yes</td>
                    <td>JPEG/PNG site photo with applicant (Max 500 KB).</td>
                </tr>
            </tbody>
        </table>

        <p><strong>Stage 2 Parameters (When Status is "Site Verified"):</strong></p>
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>site_engineer_file</td>
                    <td>file (pdf)</td>
                    <td>Yes</td>
                    <td>BDO signed report (Max 500 KB).</td>
                </tr>
                <tr>
                    <td>possession_certificate</td>
                    <td>file (pdf)</td>
                    <td>Yes</td>
                    <td>Official signed certificate (Max 500 KB).</td>
                </tr>
            </tbody>
        </table>

        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "message": "Application verified and approved successfully."
}</div>
    </div>

    <div class="section-break"></div>

    <h2>3. Villager Citizen APIs (Token Authenticated)</h2>

    <!-- VILLAGER DASHBOARD -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method get">GET</span>
            <span class="path">/villager/dashboard</span>
        </div>
        <div class="desc">Retrieves logged-in villager\'s flat information and active possession application timeline/status.</div>
        
        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "user": { "name": "Ram Lal", "mobile": "9876543210" },
  "owner_info": {
    "OwnerId": 85,
    "OwnerName": "Ram Lal",
    "BlockName": "Nissing",
    "VillageName": "Nissing Village",
    "FlatNo": "Block-C/Plot-45"
  },
  "possession_application": {
    "id": 5,
    "application_number": "PP-MMGAY-2026-1025",
    "physical_possession_status": "Visit Scheduled",
    "visit_slot_1": "2026-07-15 10:00:00",
    "visit_slot_2": "2026-07-16 11:30:00",
    "visit_slot_3": "2026-07-17 14:00:00",
    "visit_instructions": "Bring Aadhar Card & Original Receipts"
  },
  "logs": [
    { "new_status": "Visit Scheduled", "remarks": "Visit scheduled by BDO." }
  ]
}</div>
    </div>

    <!-- VILLAGER SUBMIT GET -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method get">GET</span>
            <span class="path">/villager/submit-possession</span>
        </div>
        <div class="desc">Fetches BDO offered visit slot details for the villager before choosing one.</div>
        
        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "application": {
    "id": 5,
    "visit_slot_1": "2026-07-15 10:00:00",
    "visit_slot_2": "2026-07-16 11:30:00",
    "visit_slot_3": "2026-07-17 14:00:00"
  }
}</div>
    </div>

    <!-- VILLAGER SUBMIT POST -->
    <div class="endpoint-card">
        <div class="endpoint-header">
            <span class="auth-badge">Bearer Token</span>
            <span class="method post">POST</span>
            <span class="path">/villager/submit-possession</span>
        </div>
        <div class="desc">Submits the final selected visit slot selected by the villager.</div>
        
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>selected_slot</td>
                    <td>string</td>
                    <td>Yes</td>
                    <td>The selected slot date-time string (e.g. "2026-07-15 10:00:00")</td>
                </tr>
            </tbody>
        </table>

        <div class="response-title"><strong>Response (200 OK):</strong></div>
        <div class="code-block">{
  "success": true,
  "message": "You have successfully selected the visit slot: 15 Jul 2026 - 10:00 AM."
}</div>
    </div>

</body>
</html>
';

$pdf = Pdf::loadHtml($html)->setPaper('a4');
$pdf->save(__DIR__ . '/../MMGAY_Physical_Possession_APIs_Doc.pdf');

echo "PDF generated successfully!\n";
