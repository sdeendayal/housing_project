<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Physical Possession Records</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font-family: Arial, sans-serif; font-size: 9px; }
        .toolbar { display: flex; justify-content: space-between; margin-bottom: 12px; }
        .toolbar a, .toolbar button { border: 0; border-radius: 6px; padding: 8px 12px; color: white; background: #334155; text-decoration: none; cursor: pointer; }
        h1 { margin: 0; font-size: 18px; }
        p { margin: 3px 0 0; color: #64748b; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; color: #475569; }
        th, td { padding: 6px; border: 1px solid #dbe2ea; vertical-align: top; }
        .right { text-align: right; }
        .status { font-weight: bold; text-transform: capitalize; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h1>Physical Possession Records</h1>
            <p><?php echo e($applications->count()); ?> records in this print chunk</p>
        </div>
        <div>
            <?php if($hasMore): ?>
                <a href="<?php echo e(request()->fullUrlWithQuery(['after_id' => $nextAfterId])); ?>">Next 500 Records</a>
            <?php endif; ?>
            <button onclick="window.print()">Print This Chunk</button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No.</th><th>Possession ID</th><th>Asset</th>
                <th>Applicant</th><th>Mobile</th><th>Location</th>
                <th class="right">Received Amount</th><th>Schedule</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td>
                        <?php echo e(($row->physical_application_number ?? null)
                            ?: (($row->possession_id ?? null) ?: '-')); ?>

                    </td>
                    <td>
                        #<?php echo e($row->asset_id ?? '-'); ?><br>
                        <?php echo e($row->asset_name ?? '-'); ?>

                    </td>
                    <td>
                        <?php echo e($row->applicant_name ?? '-'); ?><br>
                        App: <?php echo e(($row->purchaser_application_number ?? null) ?: '-'); ?>

                    </td>
                    <td><?php echo e(($row->mobile ?? null) ?: '-'); ?></td>
                    <td>
                        <?php echo e(($row->district_name ?? null) ?: '-'); ?><br>
                        <?php echo e(($row->city_name ?? null) ?: '-'); ?> /
                        <?php echo e(($row->sector_name ?? null) ?: '-'); ?>

                    </td>
                    <td class="right">₹<?php echo e(number_format($row->received_amount ?? 0, 2)); ?></td>
                    <td>
                        <?php echo e(($row->possession_date ?? null) ?: '-'); ?><br>
                        <?php echo e(($row->meeting_slot ?? null) ?: '-'); ?>

                    </td>
                    <td class="status">
                        <?php echo e(str_replace('_', ' ', $row->workflow_status ?? 'awaiting_schedule')); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/physicalPossessionPrint.blade.php ENDPATH**/ ?>