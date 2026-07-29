<?php $__env->startSection('title', 'MMSAY Department Dashboard'); ?>

<?php $__env->startSection('content'); ?>

    <main class="ml-52 pt-20 px-6 pb-6 min-h-screen bg-slate-50">

        <!-- Header -->
        <div class="max-w-7xl mx-auto mb-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-2xl font-bold text-slate-800">
                    EMI Payments
                </h2>
                <p class="text-slate-500 mt-1">
                    Monthly Installment Status & Payment Summary
                </p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-600 to-blue-600">
                    <h3 class="text-white font-semibold text-lg">
                        Property EMI Records
                    </h3>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

                    <!-- Table Wrapper -->
                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[1000px] text-sm">

                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700">
                                <tr>
                                    <th class="px-5 py-3 text-left font-semibold">District</th>
                                    <th class="px-5 py-3 text-left font-semibold">Purchaser</th>
                                    <th class="px-5 py-3 text-left font-semibold">Mobile</th>
                                    <th class="px-5 py-3 text-right font-semibold">Plot Cost</th>
                                    <th class="px-5 py-3 text-right font-semibold">Received</th>
                                    <th class="px-5 py-3 text-right font-semibold">Balance</th>
                                    <th class="px-5 py-3 text-center font-semibold">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                <?php $__empty_1 = true; $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-slate-50 transition">

                                        <td class="px-5 py-3 text-slate-700">
                                            <?php echo e($property->district); ?>

                                        </td>

                                        <td class="px-5 py-3 text-slate-700">
                                            <?php echo e($property->PrivatePurchaserName); ?>

                                        </td>

                                        <td class="px-5 py-3 text-slate-700">
                                            <?php echo e($property->MobileNo); ?>

                                        </td>

                                        <td class="px-5 py-3 text-right text-slate-800 font-medium">
                                            ₹<?php echo e(number_format($property->FlatCost, 2)); ?>

                                        </td>

                                        <td class="px-5 py-3 text-right text-green-700 font-semibold">
                                            ₹<?php echo e(number_format($property->ReceivedAmount, 2)); ?>

                                        </td>

                                        <td class="px-5 py-3 text-right text-red-700 font-semibold">
                                            ₹<?php echo e(number_format($property->BalanceAmount, 2)); ?>

                                        </td>

                                        <td class="px-5 py-3 text-center">

                                            <a href="<?php echo e(route('mmsay.emi.status', ['assetId' => $property->AssetId])); ?>"
                                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium
          bg-slate-900 text-white rounded-md hover:bg-slate-800 transition">

                                                <span class="material-symbols-outlined text-sm">
                                                    account_balance_wallet
                                                </span>

                                                EMI Status
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                    <tr>
                                        <td colspan="7" class="text-center py-10 text-slate-500">
                                            No EMI records found.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                    <!-- Pagination -->
                    <div class="px-5 py-4 border-t border-slate-200 bg-slate-50 flex justify-end">
                        <div class="text-sm">
                            <?php echo e($properties->links()); ?>

                        </div>
                    </div>

                </div>

            </div>
        </div>

    </main>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/departmentEmiPayments.blade.php ENDPATH**/ ?>