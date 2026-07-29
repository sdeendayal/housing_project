<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Property Statement - <?php echo e($property->AssetId); ?>

    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef2f7;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 18px;
            background: #172033;
            color: #fff;
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
            min-height: 34px;
            padding: 7px 14px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 7px;
            background: #fff;
            color: #172033;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button-secondary {
            background: transparent;
            color: #fff;
        }

        .sheet {
            width: min(1500px, calc(100% - 32px));
            margin: 18px auto;
            padding: 22px;
            border: 1px solid #dbe3ed;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .report-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 14px;
            border-bottom: 2px solid #172033;
        }

        .report-title {
            margin: 0;
            font-size: 20px;
            line-height: 1.25;
        }

        .muted {
            color: #64748b;
        }

        .small {
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin-top: 12px;
        }

        .summary-card {
            padding: 10px;
            border: 1px solid #dbe3ed;
            border-radius: 7px;
            background: #f8fafc;
        }

        .summary-label {
            color: #64748b;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .summary-value {
            margin-top: 5px;
            font-size: 17px;
            font-weight: 800;
        }

        .green {
            color: #059669;
        }

        .red {
            color: #e11d48;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .section {
            margin-top: 14px;
            page-break-inside: auto;
        }

        .section-card {
            border: 1px solid #dbe3ed;
            border-radius: 7px;
            overflow: hidden;
        }

        .section-title {
            margin: 0;
            padding: 8px 10px;
            border-bottom: 1px solid #dbe3ed;
            background: #f1f5f9;
            font-size: 11px;
            font-weight: 800;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px 18px;
            padding: 10px;
        }

        .detail-label {
            color: #64748b;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .detail-value {
            margin-top: 2px;
            font-size: 10px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th {
            padding: 6px 5px;
            border: 1px solid #cbd5e1;
            background: #eaf0f6;
            color: #475569;
            font-size: 7px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            padding: 5px;
            border: 1px solid #dbe3ed;
            font-size: 8px;
            line-height: 1.35;
            vertical-align: top;
            word-break: break-word;
        }

        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 999px;
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-paid {
            background: #dcfce7;
            color: #047857;
        }

        .status-partial {
            background: #fef3c7;
            color: #b45309;
        }

        .status-pending {
            background: #ffe4e6;
            color: #be123c;
        }

        .receipt-line + .receipt-line {
            margin-top: 2px;
        }

        .footer {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #cbd5e1;
            color: #64748b;
            font-size: 8px;
        }

        .signature {
            min-width: 180px;
            padding-top: 24px;
            border-top: 1px solid #172033;
            text-align: center;
        }

        @page {
            size: A4 landscape;
            margin: 9mm;
        }

        @media print {
            body {
                background: #fff;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .sheet {
                width: 100%;
                margin: 0;
                padding: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .section-card {
                break-inside: auto;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar no-print">
        <div>
            <strong>Property Statement</strong>
            <span style="margin-left: 8px; color: #cbd5e1;">
                Asset #<?php echo e($property->AssetId); ?>

            </span>
        </div>

        <div class="toolbar-actions">
            <button type="button" class="button button-secondary" onclick="window.close()">
                Close
            </button>

            <button type="button" class="button" onclick="window.print()">
                Print Statement
            </button>
        </div>
    </div>

    <main class="sheet">
        <header class="report-header">
            <div>
                <h1 class="report-title">
                    MMSAY Property Payment Statement
                </h1>

                <p class="muted" style="margin: 5px 0 0;">
                    Property, purchaser, cash-receipt and FIFO EMI allocation details
                </p>
            </div>

            <div class="text-right">
                <strong>Asset #<?php echo e($property->AssetId); ?></strong>
                <div class="muted small" style="margin-top: 3px;">
                    Generated: <?php echo e(now()->format('d-m-Y H:i')); ?>

                </div>
            </div>
        </header>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Property Cost</div>
                <div class="summary-value">
                    ₹<?php echo e(number_format($flatCost ?? 0, 2)); ?>

                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">Initial Received</div>
                <div class="summary-value">
                    ₹<?php echo e(number_format($openingReceivedAmount ?? 0, 2)); ?>

                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">Total Received</div>
                <div class="summary-value green">
                    ₹<?php echo e(number_format($totalReceived ?? 0, 2)); ?>

                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">Pending Amount</div>
                <div class="summary-value <?php echo e(($pendingAmount ?? 0) > 0 ? 'red' : 'green'); ?>">
                    ₹<?php echo e(number_format($pendingAmount ?? 0, 2)); ?>

                </div>
            </div>
        </div>

        <div class="info-grid">
            <section class="section-card">
                <h2 class="section-title">Property Information</h2>

                <div class="details">
                    <div>
                        <div class="detail-label">Asset</div>
                        <div class="detail-value">
                            <?php echo e($property->AssetName ?? '-'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="detail-label">Size</div>
                        <div class="detail-value">
                            <?php echo e($property->AssetSize ?? '-'); ?> <?php echo e($property->Unit ?? ''); ?>

                        </div>
                    </div>

                    <div>
                        <div class="detail-label">District / City</div>
                        <div class="detail-value">
                            <?php echo e($property->DistrictName ?? '-'); ?> /
                            <?php echo e($property->CityName ?? '-'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="detail-label">Sector</div>
                        <div class="detail-value">
                            <?php echo e($property->SectorName ?? '-'); ?>

                        </div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Purchaser Information</h2>

                <div class="details">
                    <div>
                        <div class="detail-label">Purchaser</div>
                        <div class="detail-value">
                            <?php echo e($property->PrivatePurchaserName ?? 'Not allotted'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="detail-label">Father Name</div>
                        <div class="detail-value">
                            <?php echo e($property->PurchaserFatherName ?? '-'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="detail-label">Mobile</div>
                        <div class="detail-value">
                            <?php echo e($property->MobileNo ?? '-'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="detail-label">Application</div>
                        <div class="detail-value">
                            <?php echo e($property->ApplicationNo ?? '-'); ?>

                        </div>
                    </div>
                </div>
            </section>
        </div>

        <section class="section section-card">
            <h2 class="section-title">
                Cash Receipts (<?php echo e(number_format($cashReceipts->count())); ?>)
            </h2>

            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">S. No.</th>
                        <th style="width: 45%;">Receipt Number</th>
                        <th style="width: 22%;">Receipt Date</th>
                        <th class="text-right" style="width: 25%;">Amount</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $cashReceipts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receipt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($receipt->receipt_number ?? '-'); ?></td>
                            <td>
                                <?php echo e($receipt->created_date
                                    ? \Carbon\Carbon::parse($receipt->created_date)->format('d-m-Y')
                                    : '-'); ?>

                            </td>
                            <td class="text-right">
                                ₹<?php echo e(number_format($receipt->total_paid_amount ?? 0, 2)); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" style="padding: 14px; text-align: center;">
                                No cash receipts found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Receipt Total</th>
                        <th class="text-right">
                            ₹<?php echo e(number_format($receiptTotal ?? 0, 2)); ?>

                        </th>
                    </tr>
                </tfoot>
            </table>
        </section>

        <section class="section section-card">
            <h2 class="section-title">
                FIFO EMI Allocation -
                <?php echo e(number_format($paidEmiCount ?? 0)); ?> Paid,
                <?php echo e(number_format($partiallyPaidEmiCount ?? 0)); ?> Partial,
                <?php echo e(number_format($pendingEmiCount ?? 0)); ?> Pending
                (₹<?php echo e(number_format($emiPendingAmount ?? 0, 2)); ?>)
            </h2>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">EMI</th>
                        <th style="width: 9%;">Due Date</th>
                        <th class="text-right" style="width: 10%;">Payable</th>
                        <th class="text-right" style="width: 10%;">Allocated</th>
                        <th class="text-right" style="width: 9%;">Pending</th>
                        <th style="width: 9%;">Cleared On</th>
                        <th style="width: 38%;">Receipt Allocation</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $emiDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $status = $emi->payment_status ?? 'pending';
                        ?>

                        <tr>
                            <td><?php echo e($emi->InstallmentNumber); ?></td>

                            <td>
                                <?php echo e($emi->DueDate
                                    ? \Carbon\Carbon::parse($emi->DueDate)->format('d-m-Y')
                                    : '-'); ?>

                            </td>

                            <td class="text-right">
                                ₹<?php echo e(number_format($emi->installment_payable ?? 0, 2)); ?>

                            </td>

                            <td class="text-right green">
                                <strong>
                                    ₹<?php echo e(number_format($emi->allocated_payment ?? 0, 2)); ?>

                                </strong>
                            </td>

                            <td class="text-right <?php echo e(($emi->installment_pending ?? 0) > 0 ? 'red' : ''); ?>">
                                ₹<?php echo e(number_format($emi->installment_pending ?? 0, 2)); ?>

                            </td>

                            <td>
                                <?php echo e($emi->actual_payment_date
                                    ? \Carbon\Carbon::parse($emi->actual_payment_date)->format('d-m-Y')
                                    : '-'); ?>

                            </td>

                            <td>
                                <?php $__empty_2 = true; $__currentLoopData = $emi->receipt_allocations ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allocation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <div class="receipt-line">
                                        <?php echo e($allocation['receipt_number'] ?: '-'); ?>

                                        · ₹<?php echo e(number_format($allocation['allocated_amount'], 2)); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="status
                                    <?php echo e($status === 'paid'
                                        ? 'status-paid'
                                        : ($status === 'partial'
                                            ? 'status-partial'
                                            : 'status-pending')); ?>">
                                    <?php echo e(ucfirst($status)); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" style="padding: 14px; text-align: center;">
                                No EMI records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <footer class="footer">
            <div>
                This statement applies cash receipts to the oldest unpaid EMI first (FIFO).
                Non-EMI component: ₹<?php echo e(number_format($nonEmiComponent ?? 0, 2)); ?>.
                It is system generated.
            </div>

            <div class="signature">
                Authorized Signatory
            </div>
        </footer>
    </main>
</body>
</html><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/propertyStatementPrint.blade.php ENDPATH**/ ?>