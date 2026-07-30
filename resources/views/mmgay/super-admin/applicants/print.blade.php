<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicants Report - Print View</title>
    <style>
        @page {
            margin: 18px 16px;
            size: A4 landscape;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #1e293b;
            background: #fff;
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

        @media print {
            body {
                background: none;
                color: #000;
                padding-top: 0;
            }
            thead {
                display: table-header-group;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body style="padding-top: 0;">

    <div class="no-print" style="position: sticky; top: 0; background: #2563eb; color: #fff; padding: 12px 20px; text-align: center; font-family: system-ui, -apple-system, sans-serif; font-size: 13px; font-weight: bold; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); z-index: 99999; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <span>Report loaded successfully (Total: {{ number_format($applicants->count()) }} records). Chrome PDF generation may take a few seconds.</span>
        <div>
            <button onclick="window.print()" style="background: #fff; color: #2563eb; border: none; padding: 6px 16px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-right: 10px; font-size: 12px; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">Print PDF</button>
            <button onclick="window.close()" style="background: #ef4444; color: #fff; border: none; padding: 6px 16px; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 12px; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">Close</button>
        </div>
    </div>

    <div style="padding: 10px;">
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
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.print();
            }, 1500);
        });
    </script>
</body>
</html>
