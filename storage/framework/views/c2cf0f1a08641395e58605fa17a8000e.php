<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Property Registration Print
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            background: #f1f5f9;
            color: #1e293b;
            font-family: Arial, sans-serif;
        }

        .page-container {
            max-width: 1500px;
            margin: 0 auto;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: white;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 8px 14px;
            border: 0;
            border-radius: 7px;
            background: #475569;
            color: white;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .button:hover {
            background: #334155;
        }

        .button-primary {
            background: #4f46e5;
        }

        .button-primary:hover {
            background: #4338ca;
        }

        .button-disabled {
            pointer-events: none;
            opacity: 0.4;
        }

        .report {
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: white;
        }

        .report-header {
            margin-bottom: 14px;
            text-align: center;
        }

        .report-header h1 {
            margin: 0;
            font-size: 20px;
        }

        .report-meta {
            margin-top: 5px;
            color: #64748b;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9px;
        }

        thead {
            display: table-header-group;
        }

        th {
            padding: 6px 4px;
            border: 1px solid #cbd5e1;
            background: #eef2ff;
            color: #475569;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            word-break: break-word;
        }

        tr {
            page-break-inside: avoid;
        }

        .text-right {
            text-align: right;
        }

        .received {
            color: #059669;
            font-weight: 600;
        }

        .pending {
            color: #e11d48;
            font-weight: 600;
        }

        .empty {
            padding: 30px;
            text-align: center;
            color: #64748b;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .no-print {
                display: none !important;
            }

            .report {
                padding: 0;
                border: 0;
            }

            table {
                font-size: 7px;
            }

            th {
                background: #e2e8f0 !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="page-container">

        
        <div class="toolbar no-print">
            <div>
                <strong>
                    Property Records
                </strong>

                <div style="margin-top: 3px; color: #64748b; font-size: 11px;">
                    Chunk <?php echo e($properties->currentPage()); ?>

                    of <?php echo e($properties->lastPage()); ?>


                    · Records
                    <?php echo e(number_format($properties->firstItem() ?? 0)); ?>

                    –
                    <?php echo e(number_format($properties->lastItem() ?? 0)); ?>


                    of <?php echo e(number_format($properties->total())); ?>

                </div>
            </div>

            <div class="toolbar-actions">
                <?php if($properties->onFirstPage()): ?>
                    <span class="button button-disabled">
                        Previous Chunk
                    </span>
                <?php else: ?>
                    <a
                        href="<?php echo e($properties->previousPageUrl()); ?>"
                        class="button"
                    >
                        Previous Chunk
                    </a>
                <?php endif; ?>

                <button
                    type="button"
                    onclick="window.print()"
                    class="button button-primary"
                >
                    Print This Chunk
                </button>

                <?php if($properties->hasMorePages()): ?>
                    <a
                        href="<?php echo e($properties->nextPageUrl()); ?>"
                        class="button"
                    >
                        Next Chunk
                    </a>
                <?php else: ?>
                    <span class="button button-disabled">
                        Next Chunk
                    </span>
                <?php endif; ?>
            </div>
        </div>

        
        <section class="report">
            <div class="report-header">
                <h1>
                    Property Registration Report
                </h1>

                <div class="report-meta">
                    Total records:
                    <?php echo e(number_format($properties->total())); ?>


                    · Current chunk:
                    <?php echo e($properties->currentPage()); ?>

                    / <?php echo e($properties->lastPage()); ?>


                    · Generated:
                    <?php echo e(now()->format('d-m-Y H:i')); ?>

                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 6%;">Asset</th>
                        <th style="width: 12%;">Property</th>
                        <th style="width: 15%;">Location</th>
                        <th style="width: 9%;">Application</th>
                        <th style="width: 15%;">Purchaser</th>
                        <th style="width: 9%;">Mobile</th>
                        <th class="text-right" style="width: 11%;">
                            Cost
                        </th>
                        <th class="text-right" style="width: 11%;">
                            Received
                        </th>
                        <th class="text-right" style="width: 11%;">
                            Pending
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                #<?php echo e($item->AssetId); ?>

                            </td>

                            <td>
                                <strong>
                                    <?php echo e($item->AssetName); ?>

                                </strong>

                                <br>

                                <?php echo e($item->AssetSize); ?>

                                <?php echo e($item->Unit); ?>

                            </td>

                            <td>
                                <?php echo e($item->DistrictName ?? '-'); ?>


                                <br>

                                <?php echo e($item->CityName ?? '-'); ?>

                                /
                                <?php echo e($item->SectorName ?? '-'); ?>

                            </td>

                            <td>
                                <?php echo e($item->ApplicationNo ?? '-'); ?>

                            </td>

                            <td>
                                <?php echo e($item->PrivatePurchaserName ?? '-'); ?>

                            </td>

                            <td>
                                <?php echo e($item->MobileNo ?? '-'); ?>

                            </td>

                            <td class="text-right">
                                ₹<?php echo e(number_format($item->FlatCost ?? 0, 2)); ?>

                            </td>

                            <td class="text-right received">
                                ₹<?php echo e(number_format(
                                    $item->total_received ?? 0,
                                    2
                                )); ?>

                            </td>

                            <td class="text-right pending">
                                ₹<?php echo e(number_format(
                                    $item->pending_amount ?? 0,
                                    2
                                )); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="empty">
                                No property records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/exports/propertiesPrint.blade.php ENDPATH**/ ?>