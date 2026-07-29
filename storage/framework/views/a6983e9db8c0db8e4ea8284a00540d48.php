<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Allotment Report</title>

    <style>
        @page {
            margin: 18px 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1e293b;
        }

        .header {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #334155;
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
            font-size: 18px;
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

        .filter-box {
            margin-bottom: 12px;
            padding: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .filter-title {
            margin-bottom: 5px;
            font-weight: bold;
            color: #334155;
        }

        .filter-item {
            display: inline-block;
            margin-right: 12px;
            margin-bottom: 3px;
        }

        .warning {
            margin-bottom: 10px;
            padding: 7px;
            border: 1px solid #f59e0b;
            background: #fffbeb;
            color: #92400e;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.report th {
            padding: 6px 4px;
            border: 1px solid #94a3b8;
            background: #e2e8f0;
            color: #0f172a;
            font-size: 7px;
            text-align: left;
        }

        table.report td {
            padding: 5px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            word-wrap: break-word;
        }

        table.report tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .status {
            font-weight: bold;
        }

        .footer {
            margin-top: 10px;
            text-align: right;
            font-size: 7px;
            color: #64748b;
        }

        .w-sr {
            width: 4%;
        }

        .w-application {
            width: 10%;
        }

        .w-name {
            width: 13%;
        }

        .w-mobile {
            width: 8%;
        }

        .w-location {
            width: 18%;
        }

        .w-phase {
            width: 6%;
        }

        .w-plot {
            width: 8%;
        }

        .w-status {
            width: 12%;
        }
    </style>
</head>

<body>

    <?php
        $statusLabels = [
            'approved_paid' => 'Approved & Paid',
            'approved_unpaid' => 'Approved & Unpaid',
            'pending' => 'Yet to be Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];
    ?>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="title">Allotment Report</h1>

                    <div class="subtitle">
                        MMGAY Super Admin Dashboard
                    </div>
                </td>

                <td class="date">
                    Generated:
                    <?php echo e(now()->format('d-m-Y h:i A')); ?>

                </td>
            </tr>
        </table>
    </div>

    <div class="filter-box">
        <div class="filter-title">Applied Filters</div>

        <span class="filter-item">
            Phase:
            <strong><?php echo e($filters['phase'] ?: 'All'); ?></strong>
        </span>

        <span class="filter-item">
            District ID:
            <strong><?php echo e($filters['district_id'] ?: 'All'); ?></strong>
        </span>

        <span class="filter-item">
            Block ID:
            <strong><?php echo e($filters['block_id'] ?: 'All'); ?></strong>
        </span>

        <span class="filter-item">
            Village ID:
            <strong><?php echo e($filters['village_id'] ?: 'All'); ?></strong>
        </span>

        <span class="filter-item">
            Status:
            <strong>
                <?php echo e($statusLabels[$filters['status']] ?? 'All'); ?>

            </strong>
        </span>

        <span class="filter-item">
            Search:
            <strong><?php echo e($filters['search'] ?: 'None'); ?></strong>
        </span>
    </div>

    <?php if($totalRecords > $pdfLimit): ?>
        <div class="warning">
            Total filtered records: <?php echo e(number_format($totalRecords)); ?>.
            PDF performance ke liye first
            <?php echo e(number_format($pdfLimit)); ?> records include kiye gaye hain.
            Complete records Excel export me available hain.
        </div>
    <?php endif; ?>

    <table class="report">
        <thead>
            <tr>
                <th class="w-sr">Sr.</th>
                <th class="w-application">Application No.</th>
                <th class="w-name">Applicant</th>
                <th class="w-mobile">Mobile</th>
                <th class="w-location">Location</th>
                <th class="w-phase">Phase</th>
                <th class="w-plot">Plot</th>
                <th class="w-status">Status</th>
            </tr>
        </thead>

        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allotment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                <?php
                    if ((int) ($allotment->IsAllotmentCancelled ?? 0) === 1) {
                        $status = 'Cancelled';
                    } elseif ((int) ($allotment->IsRejected ?? 0) === 1) {
                        $status = 'Rejected';
                    } elseif (
                        (int) ($allotment->IsApproved ?? 0) === 1 &&
                        (int) ($allotment->IsPaid ?? 0) === 1
                    ) {
                        $status = 'Approved & Paid';
                    } elseif ((int) ($allotment->IsApproved ?? 0) === 1) {
                        $status = 'Approved & Unpaid';
                    } else {
                        $status = 'Yet to be Approved';
                    }
                ?>

                <tr>
                    <td class="text-center">
                        <?php echo e($loop->iteration); ?>

                    </td>

                    <td>
                        <?php echo e($allotment->RegistrationNo ?? '-'); ?>

                    </td>

                    <td>
                        <strong>
                            <?php echo e($allotment->OwnerName ?? '-'); ?>

                        </strong>

                        <br>

                        <?php echo e($allotment->FatherHusbandName ?? '-'); ?>

                    </td>

                    <td>
                        <?php echo e($allotment->MobileNo ?? '-'); ?>

                    </td>

                    <td>
                        <?php echo e($allotment->VillageName ?? '-'); ?>,
                        <?php echo e($allotment->BlockName ?? '-'); ?>,
                        <?php echo e($allotment->DistrictName ?? '-'); ?>

                    </td>

                    <td>
                        <?php echo e($allotment->Phase ?? '-'); ?>

                    </td>

                    <td>
                        <?php echo e($allotment->FlatNo ?? '-'); ?>

                    </td>

                    <td class="status">
                        <?php echo e($status); ?>

                    </td>
                </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                <tr>
                    <td colspan="8" class="text-center">
                        No allotment records found.
                    </td>
                </tr>

            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Total displayed records:
        <?php echo e(number_format($allotments->count())); ?>

    </div>

</body>

</html><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmgay/super-admin/allotment/pdf.blade.php ENDPATH**/ ?>