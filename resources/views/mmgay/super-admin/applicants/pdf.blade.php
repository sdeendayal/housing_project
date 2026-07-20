<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Applicants Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1e293b;
        }

        h2 {
            margin: 0 0 5px;
            text-align: center;
        }

        .summary {
            margin-bottom: 15px;
            text-align: center;
            color: #475569;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 4px;
        }

        th {
            background-color: #2563eb;
            color: #ffffff;
            font-size: 8px;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Applicants Report</h2>

    <div class="summary">
        Total Applicants: {{ number_format($applicants->count()) }}
        |
        Generated On: {{ now()->format('d-m-Y h:i A') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Application No.</th>
                <th>Applicant Name</th>
                <th>Father / Husband</th>
                <th>Mobile</th>
                <th>PPP ID</th>
                <th>Village</th>
                <th>Phase</th>
                <th>Flat No.</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($applicants as $index => $applicant)
                <tr>
                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $applicant->RegistrationNo ?? '-' }}
                    </td>

                    <td>
                        {{ $applicant->OwnerName ?? '-' }}
                    </td>

                    <td>
                        {{ $applicant->FatherHusbandName ?? '-' }}
                    </td>

                    <td>
                        {{ $applicant->MobileNo ?? '-' }}
                    </td>

                    <td>
                        {{ $applicant->PPPId ?? '-' }}
                    </td>

                    <td>
                        {{ $applicant->VillageName ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $applicant->Phase ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $applicant->FlatNo ?? '-' }}
                    </td>

                    <td>
                        {{ $applicant->ApplicantStatus ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">
                        No applicants found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>