<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Allotment Letter Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    @include('partials.mmsay.citizen.styles')
    <style>
        body { background: #f1f5f9; padding: 15px; font-family: 'noto sans devanagari', Arial, sans-serif; }
        .pp-allotment-verify-container { max-width: 800px; margin: 0 auto; }
        .verification-success-alert {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #047857;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="pp-allotment-verify-container">
        <div class="verification-success-alert">
            ✓ यह आंबटन पत्र पीपीपी (PPP) सिस्टम द्वारा डिजिटल रूप से सत्यापित और वैध है।
        </div>
        
        <div class="pp-cert-preview-paper pp-allotment-preview-paper">
            @include('partials.physical-possession.allotment-letter-content', [
                'letter' => $letter,
            ])
        </div>
    </div>
</body>
</html>
