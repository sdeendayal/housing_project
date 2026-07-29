<?php $__env->startSection('title', 'MMSAY Department Dashboard'); ?>

<?php $__env->startSection('content'); ?>

    <main class="ml-52 pt-20 px-6 pb-6 min-h-screen bg-slate-50">

        <!-- Header -->
        <div class="max-w-7xl mx-auto mb-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-2xl font-bold text-slate-800">
                    EMI Status & Payment Summary
                </h2>
                <p class="text-slate-500 mt-1">
                    Monthly EMI Installment Status & Payment Summary
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border">

            <h2 class="text-lg font-semibold mb-4">Registration Summary</h2>

            <div class="grid grid-cols-3 gap-6">

                <div>
                    <p class="text-gray-500">Total Plot Cost</p>
                    <h2 class="text-xl font-bold">₹ <?php echo e(number_format($flatCost)); ?></h2>
                </div>

                <div>
                    <p class="text-green-600">Received Amount</p>
                    <h2 class="text-xl font-bold text-green-600">
                        ₹ <?php echo e(number_format($ReceivedAmount)); ?>

                    </h2>
                </div>

                <div>
                    <p class="text-red-600">Pending Amount</p>
                    <h2 class="text-xl font-bold text-red-600">
                        ₹ <?php echo e(number_format($BalanceAmount)); ?>

                    </h2>
                </div>

            </div>

        </div>

        

        <div class="bg-white p-6 rounded-2xl shadow border mt-6">

            <h3 class="font-semibold mb-4">Property Details</h3>

            <div class="grid grid-cols-2 gap-4 text-sm">

                <div><b>Asset ID:</b> <?php echo e($property->AssetId); ?></div>
                <div><b>Asset Name:</b> <?php echo e($property->AssetName ?? '-'); ?></div>
                <div><b>Plot Cost:</b> ₹ <?php echo e(number_format($property->FlatCost)); ?></div>
                <div>
                    <b>Location:</b>
                    <?php echo e($property->sector ?? '-'); ?>,
                    <?php echo e($property->city ?? '-'); ?>,
                    <?php echo e($property->district ?? '-'); ?>

                </div>

            </div>

        </div>

        <div class="bg-white p-6 rounded-2xl shadow border mt-6">

            <h3 class="font-semibold mb-4">Personal Details</h3>

            <div class="grid grid-cols-2 gap-4 text-sm">

                <div><b>Name:</b> <?php echo e($property->PrivatePurchaserName ?? '-'); ?></div>
                <div><b>Mobile:</b> <?php echo e($property->MobileNo ?? '-'); ?></div>
                <div><b>Application No:</b> <?php echo e($property->ApplicationNo ?? '-'); ?></div>
                

            </div>

        </div>

        <div class="bg-white p-6 rounded-2xl shadow border mt-6">

            <h3 class="font-semibold mb-4">EMI Ledger Statement</h3>

            <table class="w-full text-sm border">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Due Date</th>
                        <th class="p-3 text-left">EMI</th>
                        <th class="p-3 text-left">Paid</th>
                        <th class="p-3 text-left">Balance</th>
                        <th class="p-3 text-left">Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php $__currentLoopData = $ledger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-t">

                            <td class="p-3"><?php echo e($row['no']); ?></td>
                            <td class="p-3"><?php echo e($row['due']); ?></td>

                            <td class="p-3">₹<?php echo e(number_format($row['emi'])); ?></td>
                            <td class="p-3 text-green-600">₹<?php echo e(number_format($row['paid'])); ?></td>
                            <td class="p-3 text-red-600">₹<?php echo e(number_format($row['balance'])); ?></td>

                            <td class="p-3">

                                <?php if($row['status'] == 'Paid'): ?>
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Paid</span>
                                <?php elseif($row['status'] == 'Partial'): ?>
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">Partial</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">Unpaid</span>
                                <?php endif; ?>

                            </td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </tbody>

            </table>
        </div>
    </main>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/emiStatus.blade.php ENDPATH**/ ?>