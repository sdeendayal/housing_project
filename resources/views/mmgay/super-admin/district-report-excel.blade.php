<table>
    <thead>
        <tr>
            <th>District</th>
            <th>Villages</th>
            <th>Applicants</th>
            <th>Allotted</th>
            <th>Approved Paid</th>
            <th>Approved Unpaid</th>
            <th>Yet to be Approved</th>
            <th>Rejected</th>
            <th>Allotment Cancelled</th>
        </tr>
    </thead>


    <tbody>

        @foreach ($report as $row)
            <tr>

                <td>
                    {{ $row->DistrictName }}
                </td>

                <td>
                    {{ $row->VillagesWithPlots }}
                </td>

                <td>
                    {{ $row->RegisteredBeneficiaries }}
                </td>

                <td>
                    {{ $row->AllottedBeneficiaries }}
                </td>

                <td>
                    {{ $row->ApprovedPaid }}
                </td>

                <td>
                    {{ $row->ApprovedUnpaid }}
                </td>

                <td>
                    {{ $row->PendingApprovalPayment }}
                </td>

                <td>
                    {{ $row->Rejected }}
                </td>

                <td>
                    {{ $row->AllotmentCancelled }}
                </td>

            </tr>
        @endforeach


    </tbody>
    <tfoot>
        <tr style="font-weight:bold;">
            <td>Gross Total</td>
            <td>{{ $grossTotal->VillagesWithPlots }}</td>
            <td>{{ $grossTotal->RegisteredBeneficiaries }}</td>
            <td>{{ $grossTotal->AllottedBeneficiaries }}</td>
            <td>{{ $grossTotal->ApprovedPaid }}</td>
            <td>{{ $grossTotal->ApprovedUnpaid }}</td>
            <td>{{ $grossTotal->PendingApprovalPayment }}</td>
            <td>{{ $grossTotal->Rejected }}</td>
            <td>{{ $grossTotal->AllotmentCancelled }}</td>
        </tr>
    </tfoot>

</table>
