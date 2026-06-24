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

<div class="pp-allotment-letter-wrap">
    <div class="pp-allotment-container">
        <div class="text-center">
            <img src="{{ asset('Haryana_emblem.png') }}" width="120" alt="Government of Haryana" class="pp-allotment-logo-img" />
            <h2 class="pp-allotment-dept-title">हाउसिंग फॉर ऑल विभाग, हरियाणा</h2>
            <h4 class="pp-allotment-green-badge">आंबटन पत्र</h4>
            <h3 class="pp-allotment-scheme-title">मुख्य मंत्री शहरी आवास योजना</h3>
            <p class="pp-allotment-intro-text">
                हरियाणा सरकार लाभार्थी को एक लाख रुपये की अदायगी पर एक मरला (30 वर्ग गज) आवासीय प्लॉट प्रदान करने की स्वीकृति प्रदान करती है।
            </p>
        </div>

        <table class="pp-allotment-data-table table table-striped table-bordered">
            <tr>
                <th>पंजीकरण संख्या</th>
                <td>{{ $letter['application_number'] }}</td>
            </tr>
            <tr>
                <th>परिवार पहचान पत्र संख्या</th>
                <td>{{ $letter['family_id'] ?: '—' }}</td>
            </tr>
            <tr>
                <th>लाभार्थी का पूरा नाम</th>
                <td>{{ $letter['beneficiary_name'] }}</td>
            </tr>
            <tr>
                <th>पिता/पति का नाम</th>
                <td>{{ $letter['father_name'] }}</td>
            </tr>
            <tr>
                <th>प्लॉट संख्या</th>
                <td>{{ $letter['plot'] }}</td>
            </tr>
            <tr>
                <th>सेक्टर</th>
                <td>{{ $letter['sector'] }}</td>
            </tr>
            <tr>
                <th>नगर</th>
                <td>{{ $letter['town_name'] ?: '—' }}</td>
            </tr>
            <tr>
                <th>जिला</th>
                <td>{{ $letter['district_name'] ?: '—' }}</td>
            </tr>
        </table>

        <div class="pp-allotment-qr-section">
            <div class="pp-allotment-qr-box">
                <img src="{{ $qrImageUrl }}" alt="QR Code" width="100" height="100" />
            </div>
            <p class="pp-allotment-qr-label">कृपया QR कोड स्कैन करें और विवरण सत्यापित करें</p>
        </div>

        <p class="pp-allotment-sign-note">
            पीपीपी सिस्टम द्वारा सत्यापित डेटा, हस्ताक्षर की आवश्यकता नहीं है
            <br /><small class="pp-allotment-terms-star">*नियम और शर्तें लागू</small>
        </p>
    </div>

    <div class="pp-allotment-container pp-allotment-terms-container">
        @include('partials.physical-possession.allotment-letter-terms')
    </div>
</div>
