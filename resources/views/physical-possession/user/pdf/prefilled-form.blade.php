<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 55px 60px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #111;
            line-height: 1.55;
        }
        .to-block { margin-bottom: 28px; }
        .to-block p { margin: 0 0 4px 0; }
        .subject {
            margin: 24px 0 22px 0;
            font-weight: normal;
        }
        .subject-line {
            border-bottom: 1px solid #111;
            display: inline-block;
            min-width: 48px;
            padding: 0 3px 1px 3px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .body-text {
            text-align: justify;
            margin-bottom: 36px;
        }
        .closing { margin-top: 28px; }
        .closing-center {
            text-align: center;
            margin-bottom: 42px;
        }
        .signature-block {
            text-align: right;
            margin-top: 18px;
        }
        .signature-name {
            margin-top: 48px;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px solid #111;
            display: inline-block;
            min-width: 180px;
            padding-top: 4px;
        }
        .meta {
            margin-top: 40px;
            font-size: 9px;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="to-block">
        <p><strong>To</strong></p>
        <p>Estate Officer / JE</p>
        <p>HSVP</p>
        <p><strong>{{ $profile['office_location'] }}</strong></p>
    </div>

    <p class="subject">
        Subject:- For issue the <strong>POSSESSION CERTIFICATE</strong> of Plot no
        <span class="subject-line">{{ $profile['plot_no'] }}</span>
        sector
        <span class="subject-line">{{ $profile['sector'] }}</span>
        urban estate
        <span class="subject-line">{{ $profile['urban_estate'] }}</span>.
    </p>

    <p><strong>Respected Sir/Madam,</strong></p>

    <p class="body-text">
        I/We the allottee/re-allottee the plot no
        <span class="subject-line">{{ $profile['plot_no'] }}</span>
        sector
        <span class="subject-line">{{ $profile['sector'] }}</span>
        urban estate
        <span class="subject-line">{{ $profile['urban_estate'] }}</span>.
        I/We want to request you kindly issue me/us the possession certificate of my/our above said plot no as soon as possible.
    </p>

    <div class="closing">
        <p class="closing-center"><strong>Thanking you</strong></p>
        <div class="signature-block">
            <p style="margin:0;"><strong>Yours sincerely</strong></p>
            <p class="signature-name">{{ strtoupper($profile['name']) }}</p>
        </div>
    </div>

    <div class="meta">
        Generated on {{ now()->format('d M Y') }} |
        Mobile: {{ $profile['mobile'] }} |
        @if($profile['application_no']) Application No: {{ $profile['application_no'] }} @endif
    </div>
</body>
</html>
