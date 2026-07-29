<?php $__env->startSection('title', 'MMSAY Department Dashboard'); ?>
<?php $__env->startSection('content'); ?>
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">

        <div class="max-w-6xl mx-auto space-y-4">

            <div class="flex items-center justify-between mb-4">

                <!-- LEFT: Title -->
                <h3 class="text-lg font-medium text-primary">
                    Lucky Draw
                </h3>

                <!-- RIGHT: Back Button -->
                <a href="<?php echo e(url('mmsay-department-draw')); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg shadow transition">
                    ⬅ Back
                </a>

            </div>

            <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">

                <!-- TOP INFO -->
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-bold text-gray-800">
                        District: <?php echo e($districtName); ?>

                    </h2>

                    <span class="px-3 py-1 text-sm bg-black text-white rounded-full">
                        Total Records: <?php echo e($totalRecords); ?>

                    </span>
                </div>

                <!-- TABLE -->
                <table class="w-full text-sm border border-gray-300">

                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="p-2 border text-left">Sr. No.</th>
                            <th class="p-2 border text-left">Asset ID</th>
                            <th class="p-2 border text-left">Name</th>
                            <th class="p-2 border text-left">Size</th>
                            <th class="p-2 border text-left">Unit</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-2 border"><?php echo e($loop->iteration); ?></td>
                                <td class="p-2 border"><?php echo e($row->AssetId); ?></td>
                                <td class="p-2 border"><?php echo e($row->AssetName); ?></td>
                                <td class="p-2 border"><?php echo e($row->AssetSize); ?></td>
                                <td class="p-2 border"><?php echo e($row->Unit); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>

                <!-- PAGINATION -->
                <div class="mt-4">
                    <?php echo e($data->links()); ?>

                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/departmentDrawDetails.blade.php ENDPATH**/ ?>