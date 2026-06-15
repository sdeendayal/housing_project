<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 portrait; margin: 0.75in 0.5in; }
        body { font-family: 'noto sans devanagari', DejaVu Sans, sans-serif; color: #111; line-height: 1.55; margin: 0; padding: 0; }
        .container {
            border: 5px solid orange;
            padding: 18px 20px;
            margin-bottom: 20px;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .logo { width: 110px; margin-bottom: 8px; }
        h2 { font-size: 18px; margin: 8px 0; font-weight: bold; }
        h3 { font-size: 20px; color: #d97706; margin: 10px 0; font-weight: bold; text-align: center; }
        h4.badge {
            display: inline-block;
            background: #198754;
            color: #fff;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            margin: 6px 0;
        }
        p, th, td, li { font-size: 14px; text-align: left; }
        .intro { font-size: 14px; margin: 12px 0 16px; text-align: center; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            vertical-align: top;
        }
        table.data-table th {
            color: rgb(0, 112, 192);
            font-weight: bold;
            width: 42%;
            background: #f8f9fa;
        }
        .qr-section { margin-top: 16px; }
        .qr-section td { border: none !important; vertical-align: middle; }
        .qr-img { width: 100px; height: 100px; border: 1px solid #ddd; }
        .footer-note { font-size: 13px; margin-top: 14px; }
        .terms-note { color: #dc3545; }
        .page-break { page-break-before: always; padding-top: 40px; }
        .terms-container { padding-top: 24px; }
        .terms-title { font-size: 20px; text-align: center; margin: 0 0 14px; font-weight: bold; }
        .numbered-list { counter-reset: list-counter; list-style: none; padding-left: 0; margin: 0; }
        .numbered-list li {
            list-style: none;
            position: relative;
            padding-left: 2em;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .numbered-list li::before {
            counter-increment: list-counter;
            content: counter(list-counter) ". ";
            position: absolute;
            left: 0;
            top: 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center">
            <img src="{{ public_path('Haryana_emblem.png') }}" class="logo" alt="Government of Haryana" />
            <h2>हाउसिंग फॉर ऑल विभाग, हरियाणा</h2>
            <h4 class="badge">आंबटन पत्र</h4>
            <h3>मुख्य मंत्री शहरी आवास योजना</h3>
            <p class="intro">हरियाणा सरकार लाभार्थी को एक लाख रुपये की अदायगी पर एक मरला (30 वर्ग गज) आवासीय प्लॉट प्रदान करने की स्वीकृति प्रदान करती है।</p>
        </div>

        <table class="data-table">
            <tr><th>पंजीकरण संख्या</th><td>{{ $letter['application_number'] }}</td></tr>
            <tr><th>परिवार पहचान पत्र संख्या</th><td>{{ $letter['family_id'] ?: '—' }}</td></tr>
            <tr><th>लाभार्थी का पूरा नाम</th><td>{{ $letter['beneficiary_name'] }}</td></tr>
            <tr><th>पिता/पति का नाम</th><td>{{ $letter['father_name'] }}</td></tr>
            <tr><th>प्लॉट संख्या</th><td>{{ $letter['plot'] }}</td></tr>
            <tr><th>सेक्टर</th><td>{{ $letter['sector'] }}</td></tr>
            <tr><th>नगर</th><td>{{ $letter['town_name'] ?: '—' }}</td></tr>
            <tr><th>जिला</th><td>{{ $letter['district_name'] ?: '—' }}</td></tr>
        </table>

        <table class="qr-section">
            <tr>
                <td style="width: 115px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($verifyUrl) }}" class="qr-img" alt="QR" />
                </td>
                <td>कृपया QR कोड स्कैन करें और विवरण सत्यापित करें</td>
            </tr>
        </table>

        <p class="text-end footer-note">
            पीपीपी सिस्टम द्वारा सत्यापित डेटा, हस्ताक्षर की आवश्यकता नहीं है<br />
            <small class="terms-note">*नियम और शर्तें लागू</small>
        </p>
    </div>

    <div class="page-break"></div>

    <div class="container terms-container">
        @include('partials.physical-possession.allotment-letter-terms-pdf')
    </div>
</body>
</html>
