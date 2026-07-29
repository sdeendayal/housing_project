@php
    $host = request()->getHost();
    if ($host === '127.0.0.1' || $host === 'localhost') {
        $localIp = gethostbyname(gethostname());
        $port = request()->getPort();
        $portSuffix = $port ? ':' . $port : '';
        $verifyUrl = 'http://' . $localIp . $portSuffix . '/physical-possession/allotment/verify/' . $letter['application_number'];
    } else {
        $verifyUrl = request()->getSchemeAndHttpHost() . '/physical-possession/allotment/verify/' . $letter['application_number'];
    }
    $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data='.urlencode($verifyUrl);
@endphp
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 portrait; margin: 0.35in 0.35in; }
        body { font-family: 'noto sans devanagari', DejaVu Sans, sans-serif; color: #111; line-height: 1.45; margin: 0; padding: 0; }
        .container {
            border: 5px solid orange;
            padding: 12px 15px;
            margin-bottom: 10px;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .logo { width: 90px; margin-bottom: 6px; }
        h2 { font-size: 16px; margin: 4px 0; font-weight: bold; }
        h3 { font-size: 18px; color: #d97706; margin: 6px 0; font-weight: bold; text-align: center; }
        h4.badge {
            display: inline-block;
            background: #198754;
            color: #fff;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            margin: 4px 0;
        }
        p, th, td, li { font-size: 12.5px; text-align: left; }
        .intro { font-size: 12.5px; margin: 6px 0 10px; text-align: center; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data-table th, table.data-table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data-table th {
            color: rgb(0, 112, 192);
            font-weight: bold;
            width: 42%;
            background: #f8f9fa;
        }
        .qr-section { margin-top: 10px; }
        .qr-section td { border: none !important; vertical-align: middle; }
        .qr-img { width: 80px; height: 80px; border: 1px solid #ddd; }
        .footer-note { font-size: 11.5px; margin-top: 8px; }
        .terms-note { color: #dc3545; }
        .page-break { page-break-before: always; }
        .terms-container { padding-top: 10px; }
        .terms-title { font-size: 17px; text-align: center; margin: 0 0 10px; font-weight: bold; }
        .numbered-list { counter-reset: list-counter; list-style: none; padding-left: 0; margin: 0; }
        .numbered-list li {
            list-style: none;
            position: relative;
            padding-left: 1.8em;
            margin-bottom: 6px;
            font-size: 12.5px;
            line-height: 1.35;
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
                    <img src="{{ $qrImageUrl }}" class="qr-img" alt="QR" />
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
