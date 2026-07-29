<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Partial Paid Properties</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font: 9px Arial, sans-serif; }
        .toolbar { display: flex; justify-content: space-between; margin-bottom: 12px; }
        .toolbar a, .toolbar button { border: 0; border-radius: 6px; padding: 8px 12px; color: #fff; background: #334155; text-decoration: none; }
        h1 { margin: 0; font-size: 18px; }
        p { margin: 3px 0 0; color: #64748b; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #dbe2ea; vertical-align: top; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 8px; }
        .right { text-align: right; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h1>Partial Paid Properties</h1>
            <p><?php echo e($properties->count()); ?> records in this print chunk</p>
        </div>
        <div>
            <?php if($hasMore): ?>
                <a href="<?php echo e(request()->fullUrlWithQuery(['after_id' => $nextAfterId])); ?>">Next 500 Records</a>
            <?php endif; ?>
            <button onclick="window.print()">Print / Save PDF</button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No.</th><th>Asset</th><th>Application</th><th>Applicant</th>
                <th>Mobile</th><th>Property</th><th>Location</th>
                <th class="right">Flat Cost</th><th class="right">Total Paid</th>
                <th class="right">Pending</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td>#<?php echo e($row->asset_id); ?></td>
                    <td><?php echo e($row->application_number ?: '-'); ?></td>
                    <td><?php echo e($row->applicant_name ?: 'Not allotted'); ?></td>
                    <td><?php echo e($row->mobile ?: '-'); ?></td>
                    <td><?php echo e($row->asset_name ?: '-'); ?><br><?php echo e($row->asset_size); ?> <?php echo e($row->asset_unit); ?></td>
                    <td><?php echo e($row->district_name ?: '-'); ?><br><?php echo e($row->city_name ?: '-'); ?> / <?php echo e($row->sector_name ?: '-'); ?></td>
                    <td class="right">₹<?php echo e(number_format($row->flat_cost, 2)); ?></td>
                    <td class="right">₹<?php echo e(number_format($row->total_paid, 2)); ?></td>
                    <td class="right">₹<?php echo e(number_format($row->pending_amount, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/pendingPropertiesPrint.blade.php ENDPATH**/ ?>