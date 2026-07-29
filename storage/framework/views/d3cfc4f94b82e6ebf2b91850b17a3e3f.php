<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Owner Status Report - <?php echo e(ucwords(str_replace('_', ' ', $status))); ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #2a5298;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            color: #1e3c72;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 10px;
        }
        .meta-info {
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            font-size: 10px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .report-table th {
            background-color: #2a5298;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 6px 8px;
            border: 1px solid #2a5298;
            text-align: left;
        }
        .report-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved_paid {
            background-color: #def7ec;
            color: #03543f;
        }
        .status-approved_unpaid {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-yet_to_be_done {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .status-rejected {
            background-color: #fde8e8;
            color: #9b1c1c;
        }
        .status-cancelled {
            background-color: #374151;
            color: #ffffff;
        }
        .page-number:before {
            content: "Page " counter(page);
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MUKHYAMANTRI GRAMIN AWAS YOJANA (MMGAY)</h1>
        <p>BDO Beneficiary Status Report — Category: <strong><?php echo e(strtoupper(str_replace('_', ' ', $status))); ?></strong></p>
    </div>

    <div class="meta-info">
        <table class="meta-table">
            <tr>
                <td style="width: 15%;"><strong>District:</strong></td>
                <td style="width: 35%;"><?php echo e($bdo->district_name ?? 'Haryana'); ?></td>
                <td style="width: 15%;"><strong>Block:</strong></td>
                <td style="width: 35%;"><?php echo e($bdo->block_name ?? 'ALL'); ?></td>
            </tr>
            <tr>
                <td><strong>BDPO Name:</strong></td>
                <td><?php echo e($bdo->name); ?></td>
                <td><strong>Report Date:</strong></td>
                <td><?php echo e($report_date); ?></td>
            </tr>
            <tr>
                <td><strong>Total Records:</strong></td>
                <td><?php echo e(count($owners)); ?></td>
                <td><strong>Export Format:</strong></td>
                <td>PDF Document (Landscape)</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">SR NO.</th>
                <th style="width: 12%;">REGISTRATION NO.</th>
                <th style="width: 18%;">OWNER NAME</th>
                <th style="width: 18%;">FATHER/HUSBAND NAME</th>
                <th style="width: 10%;">MOBILE NO.</th>
                <th style="width: 7%;">PHASE</th>
                <th style="width: 15%;">VILLAGE NAME</th>
                <th style="width: 8%;">FLAT NO.</th>
                <th style="width: 7%; text-align: center;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $owners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #666;"><?php echo e($loop->iteration); ?></td>
                    <td style="font-weight: bold; color: #1e3c72;"><?php echo e($owner->RegistrationNo); ?></td>
                    <td style="font-weight: bold;"><?php echo e($owner->OwnerName); ?></td>
                    <td><?php echo e($owner->FatherHusbandName ?? 'N/A'); ?></td>
                    <td><?php echo e($owner->MobileNo); ?></td>
                    <td>Phase <?php echo e($owner->Phase); ?></td>
                    <td><?php echo e($owner->VillageName); ?></td>
                    <td><?php echo e($owner->FlatNo ?? 'N/A'); ?></td>
                    <td style="text-align: center;">
                        <span class="status-badge status-<?php echo e($status); ?>"><?php echo e(str_replace('_', ' ', $status)); ?></span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #999; padding: 20px;">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <span class="page-number"></span> | Generated automatically from MMGAY Possession Portal
    </div>
</body>
</html>
<?php /**PATH E:\sports\housing_project\resources\views/mmgay/bdo/pdf/owner-status-pdf.blade.php ENDPATH**/ ?>