<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Allotment Letter Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background: #f1f5f9; padding: 20px; }
        th { color: rgb(0, 112, 192) !important; font-size: 16px; }
        td { font-size: 16px; }
        .container { border: 5px solid orange; max-width: 760px; margin: 0 auto; background: #fff; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h4 class="text-center text-success mb-3">आंबटन पत्र — सत्यापित विवरण</h4>
        <table class="table table-bordered table-striped">
            <tr><th>पंजीकरण संख्या</th><td>{{ $letter['application_number'] }}</td></tr>
            <tr><th>परिवार पहचान पत्र संख्या</th><td>{{ $letter['family_id'] ?: '—' }}</td></tr>
            <tr><th>लाभार्थी का नाम</th><td>{{ $letter['beneficiary_name'] }}</td></tr>
            <tr><th>पिता/पति का नाम</th><td>{{ $letter['father_name'] }}</td></tr>
            <tr><th>प्लॉट संख्या</th><td>{{ $letter['plot'] }}</td></tr>
            <tr><th>सेक्टर</th><td>{{ $letter['sector'] }}</td></tr>
            <tr><th>नगर</th><td>{{ $letter['town_name'] ?: '—' }}</td></tr>
            <tr><th>जिला</th><td>{{ $letter['district_name'] ?: '—' }}</td></tr>
        </table>
        <p class="text-center text-muted small mb-0">यह विवरण पीपीपी सिस्टम से सत्यापित है।</p>
    </div>
</body>
</html>
