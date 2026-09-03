<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Registry Details - Print</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #111827;
            margin: 30px;
        }

        h1 {
            margin-bottom: 5px;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 25px;
        }

        .section {
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .value {
            font-size: 14px;
            font-weight: bold;
        }

        .map {
            margin-top: 15px;
            width: 100%;
            max-height: 700px;
        }

        @media print {

            body {
                margin: 10px;
            }

        }

    </style>

</head>

<body onload="window.print()">

    <h1>Registry Details</h1>

    <div class="subtitle">
        Registry completed applicant
    </div>


    <div class="section">

        <div class="section-title">
            Applicant Information
        </div>

        <div class="grid">

            <div>
                <div class="label">Applicant Name</div>
                <div class="value">
                    {{ $owner->OwnerName ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Father / Husband</div>
                <div class="value">
                    {{ $owner->FatherHusbandName ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Mobile</div>
                <div class="value">
                    {{ $owner->MobileNo ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Registration No.</div>
                <div class="value">
                    {{ $owner->RegistrationNo ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Owner ID</div>
                <div class="value">
                    {{ $owner->OwnerId ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Flat ID</div>
                <div class="value">
                    {{ $owner->FlatId ?? '-' }}
                </div>
            </div>

        </div>

    </div>


    <div class="section">

        <div class="section-title">
            Location
        </div>

        <div class="grid">

            <div>
                <div class="label">Phase</div>
                <div class="value">
                    {{ $owner->Phase ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">District</div>
                <div class="value">
                    {{ $owner->DistrictName ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Block</div>
                <div class="value">
                    {{ $owner->BlockName ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Village</div>
                <div class="value">
                    {{ $owner->VillageName ?? '-' }}
                </div>
            </div>

        </div>

    </div>


    <div class="section">

        <div class="section-title">
            Registry Information
        </div>

        <div class="grid">

            <div>
                <div class="label">Registry Number</div>
                <div class="value">
                    {{ $registry->RegistaryNumber ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Registry Date</div>
                <div class="value">
                    {{ $registry->RegistaryDate ?? '-' }}
                </div>
            </div>

            <div>
                <div class="label">Registry ID</div>
                <div class="value">
                    {{ $registry->id ?? '-' }}
                </div>
            </div>

        </div>

    </div>


    @if (!empty($owner->PdfFile))

        <div class="section">

            <div class="section-title">
                Village Map
            </div>

            <p>
                Village: {{ $owner->VillageName ?? '-' }}
            </p>

            <p>
                Map File: {{ $owner->PdfFile }}
            </p>

        </div>

    @endif

</body>

</html>