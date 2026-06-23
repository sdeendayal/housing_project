<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Possession Application Request Form — Preview</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Inter, sans-serif;
            background: #f1f5f9;
            color: #111;
            margin: 0;
            padding: 1rem;
        }
        .toolbar {
            max-width: 760px;
            margin: 0 auto 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            justify-content: space-between;
        }
        .toolbar h1 {
            font-size: 1rem;
            margin: 0;
            font-weight: 700;
        }
        .toolbar-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .btn-primary { background: #1e40af; color: #fff; }
        .btn-outline { background: #fff; color: #334155; border-color: #cbd5e1; }
        .form-paper {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            padding: 3rem 3.5rem;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.08);
            border-radius: 4px;
            line-height: 1.6;
            font-size: 14px;
        }
        .subject-line,
        .pp-form-line {
            border-bottom: 1px solid #111;
            display: inline-block;
            min-width: 48px;
            padding: 0 3px 1px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .body-text,
        .pp-form-body { text-align: justify; margin: 1.5rem 0 2rem; }
        .closing-center,
        .pp-form-thanks { text-align: center; margin: 1.5rem 0; }
        .signature-block,
        .pp-form-signature { text-align: right; margin-top: 1rem; }
        .signature-name,
        .pp-form-sign-name {
            margin-top: 3rem;
            font-weight: 700;
            text-transform: uppercase;
            border-top: 1px solid #111;
            display: inline-block;
            min-width: 180px;
            padding-top: 4px;
        }
        .meta,
        .pp-form-meta {
            margin-top: 2.5rem;
            font-size: 11px;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 0.75rem;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .form-paper { box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <h1>Possession Application Request Form — Preview</h1>
        <div class="toolbar-actions">
            <button type="button" class="btn btn-outline" onclick="window.close()">Close</button>
            <button type="button" class="btn btn-outline" onclick="window.print()">Print</button>
            <a href="{{ route('pp.user.download-form') }}" class="btn btn-primary">Download PDF</a>
        </div>
    </div>

    <div class="form-paper">
        @include('partials.physical-possession.prefilled-form-content')
    </div>
</body>
</html>
