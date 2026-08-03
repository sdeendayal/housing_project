<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle ?? 'Registry Report' }} - Print View</title>
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
            font-size: 8px;
            color: #1e293b;
            background: #fff;
        }

        .header {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #334155;
            padding-left: 10px;
            padding-right: 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            padding: 0;
        }

        .title {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }

        .subtitle {
            margin-top: 4px;
            font-size: 9px;
            color: #64748b;
        }

        .date {
            text-align: right;
            font-size: 9px;
            color: #475569;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.report th {
            padding: 5px 3px;
            border: 1px solid #94a3b8;
            background: #e2e8f0;
            color: #0f172a;
            font-size: 8px;
            text-align: left;
        }

        table.report td {
            padding: 4px 3px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            word-wrap: break-word;
            font-size: 8px;
        }

        table.report tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8px;
            color: #64748b;
            font-weight: bold;
            padding-right: 10px;
        }

        .w-sr { width: 3%; }
        .w-app { width: 8%; }
        .w-owner { width: 14%; }
        .w-parties { width: 14%; }
        .w-mobile { width: 9%; }
        .w-registry { width: 9%; }
        .w-token { width: 9%; }
        .w-area { width: 9%; }
        .w-location { width: 15%; }
        .w-status { width: 10%; }

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
        <span>Report loaded successfully (Total: {{ number_format($registrations->count()) }} records). Chrome PDF generation may take a few seconds.</span>
        <div>
            <button onclick="window.print()" style="background: #fff; color: #2563eb; border: none; padding: 6px 16px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-right: 10px; font-size: 12px; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">Print PDF</button>
            <button onclick="window.close()" style="background: #ef4444; color: #fff; border: none; padding: 6px 16px; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 12px; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">Close</button>
        </div>
    </div>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="title">{{ $reportTitle ?? 'Registry Report' }}</h1>
                    <div class="subtitle">
                        MMGAY Super Admin Dashboard
                    </div>
                </td>
                <td class="date">
                    Generated: {{ now()->format('d-m-Y h:i A') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="padding-left: 10px; padding-right: 10px;">
        <table class="report">
            <thead>
                <tr>
                    <th class="w-sr">Sr.</th>
                    <th class="w-app">Application</th>
                    <th class="w-owner">Owner Details</th>
                    <th class="w-parties">Registry Parties</th>
                    <th class="w-mobile">Mobile</th>
                    <th class="w-registry">Registry No</th>
                    <th class="w-token">Token / Khewat</th>
                    <th class="w-area">Area</th>
                    <th class="w-location">Location</th>
                    <th class="w-status">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registrations as $index => $reg)
                    @php
                        $matchStatus = !empty($reg->matched_owner_id) ? 'Matched' : 'Unmatched';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $reg->RegistrationNo ?? '-' }}</strong>
                            @if(!empty($reg->OwnerId))
                                <br>Owner ID: {{ $reg->OwnerId }}
                            @endif
                        </td>
                        <td>
                            <strong>{{ $reg->OwnerName ?? 'Owner not matched' }}</strong>
                            <br>{{ $reg->FatherHusbandName ?? '-' }}
                            @if(!empty($reg->PPPId))
                                <br>PPP ID: {{ $reg->PPPId }}
                            @endif
                        </td>
                        <td>
                            First: {{ $reg->FirstParty ?? '-' }}
                            <br>Second: {{ $reg->SecondParty ?? '-' }}
                        </td>
                        <td>
                            Registry: {{ $reg->SecondPartyMobile ?? '-' }}
                            @if(!empty($reg->matched_owner_mobile))
                                <br>Owner: {{ $reg->matched_owner_mobile }}
                            @endif
                        </td>
                        <td>
                            No: {{ $reg->RegistaryNumber ?? '-' }}
                            <br>Date: {{ !empty($reg->RegistaryDate) ? date('d-m-Y', strtotime($reg->RegistaryDate)) : '-' }}
                        </td>
                        <td>
                            Token: {{ $reg->Token ?? '-' }}
                            <br>Khewat: {{ $reg->Khewat ?? '-' }}
                        </td>
                        <td>
                            Total: {{ $reg->TotalArea ?? '-' }}
                            <br>Transfer: {{ $reg->TransferArea ?? '-' }}
                            <br>Bhag: {{ $reg->Bhag ?? '-' }}
                        </td>
                        <td>
                            District: {{ $reg->District ?? '-' }}
                            <br>Tehsil: {{ $reg->TehsilName ?? '-' }}
                            <br>Village: {{ $reg->Village ?? '-' }}
                        </td>
                        <td class="text-center">
                            <strong>{{ $matchStatus }}</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No registry records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        Total Displayed Records: {{ number_format($registrations->count()) }}
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
