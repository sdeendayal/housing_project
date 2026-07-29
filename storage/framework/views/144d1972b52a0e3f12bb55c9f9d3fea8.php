<?php $__env->startSection('title', 'Possession Applications'); ?>
<?php $__env->startSection('page_header', 'Applications List'); ?>

<?php $__env->startSection('content'); ?>
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex-1">


    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <!-- Search and Filter Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-lg">list_alt</span>
                    Possession Applications
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5 font-semibold uppercase">Manage all scheduled, selected, verified and rejected applications.</p>
            </div>
            <!-- Search & Status filters - Compact -->
            <form action="<?php echo e(route('mmgay.bdo.possession-applications')); ?>" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="status" class="border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 w-36">
                    <option value="">All Statuses</option>
                    <option value="Visit Scheduled" <?php echo e($status === 'Visit Scheduled' ? 'selected' : ''); ?>>Visit Scheduled</option>
                    <option value="Slot Selected" <?php echo e($status === 'Slot Selected' ? 'selected' : ''); ?>>Slot Selected</option>
                    <option value="Verified" <?php echo e($status === 'Verified' ? 'selected' : ''); ?>>Verified</option>
                    <option value="Rejected" <?php echo e($status === 'Rejected' ? 'selected' : ''); ?>>Rejected</option>
                </select>
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search name, mobile, reg..." class="border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 w-44">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] font-bold">search</span> Filter
                </button>
                <?php if($search || $status): ?>
                    <a href="<?php echo e(route('mmgay.bdo.possession-applications')); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table - High Density spacing -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                        <th class="px-3 py-2 text-left">Sr.No.</th>
                        <th class="px-3 py-2 text-left">App Number</th>
                        <th class="px-3 py-2 text-left">Applicant Name</th>
                        <th class="px-3 py-2 text-left">Phase</th>
                        <th class="px-3 py-2 text-left">Mobile No</th>
                        <th class="px-3 py-2 text-left">Scheduled visit date</th>
                        <th class="px-3 py-2 text-left">Confirmed date</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-3 py-1.5 font-bold text-slate-400"><?php echo e($loop->iteration + ($applications->currentPage() - 1) * $applications->perPage()); ?></td>
                            <td class="px-3 py-1.5 font-bold text-slate-800"><?php echo e($app->application_number); ?></td>
                            <td class="px-3 py-1.5 text-slate-700 font-medium">
                                <?php echo e($app->applicant_name); ?>

                            </td>
                            <td class="px-3 py-1.5">
                                <?php if($app->owner_phase): ?>
                                    <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-extrabold px-2 py-0.5 rounded whitespace-nowrap">Phase <?php echo e($app->owner_phase); ?></span>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-1.5 font-mono text-slate-500 text-[11px]"><?php echo e($app->mobile); ?></td>
                            <td class="px-3 py-1.5 text-slate-500 text-[11px]">
                                <?php if($app->visit_slot_1): ?>
                                    <?php echo e(Carbon\Carbon::parse($app->visit_slot_1)->format('d M Y')); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-1.5 text-slate-500 text-[11px]">
                                <?php if($app->citizen_visit_date && $app->physical_possession_status === 'Slot Selected'): ?>
                                    <span class="font-bold text-indigo-600"><?php echo e(Carbon\Carbon::parse($app->citizen_visit_date)->format('d M Y, h:i A')); ?></span>
                                <?php elseif($app->physical_possession_status === 'Verified'): ?>
                                    <span class="text-emerald-600 font-medium"><?php echo e($app->possession_date ? Carbon\Carbon::parse($app->possession_date)->format('d M Y') : 'Completed'); ?></span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-1.5">
                                <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase
                                    <?php if($app->physical_possession_status === 'Verified'): ?> bg-emerald-50 text-emerald-700 border border-emerald-100
                                    <?php elseif($app->physical_possession_status === 'Visit Scheduled'): ?> bg-orange-50 text-orange-700 border border-orange-100
                                    <?php elseif($app->physical_possession_status === 'Slot Selected'): ?> bg-indigo-50 text-indigo-700 border border-indigo-100
                                    <?php elseif($app->physical_possession_status === 'Site Verified'): ?> bg-blue-50 text-blue-700 border border-blue-100
                                    <?php elseif($app->physical_possession_status === 'Rejected'): ?> bg-rose-50 text-rose-700 border border-rose-100
                                    <?php else: ?> bg-slate-50 text-slate-700 border border-slate-100
                                    <?php endif; ?>">
                                    <?php echo e($app->physical_possession_status); ?>

                                </span>
                            </td>
                            <td class="px-3 py-1.5 text-center">
                                <?php if(in_array($app->physical_possession_status, ['Slot Selected', 'Site Verified', 'Visit Scheduled'])): ?>
                                    <a href="<?php echo e(route('mmgay.bdo.verify-form', $app->secure_id)); ?>" class="inline-flex items-center gap-1 bg-[#10b981] hover:bg-[#059669] text-white text-[10px] px-2.5 py-1 rounded-md font-extrabold transition">
                                        <span class="material-symbols-outlined text-[13px] font-bold">assignment_turned_in</span> Action / Verify
                                    </a>
                                <?php elseif($app->physical_possession_status === 'Verified'): ?>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="<?php echo e(route('mmgay.bdo.verify-form', $app->secure_id)); ?>" class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] px-2.5 py-1 rounded-md font-extrabold border border-blue-200 transition">
                                            <span class="material-symbols-outlined text-[13px] font-bold">visibility</span> View Details
                                        </a>
                                        <a href="<?php echo e(route('mmgay.bdo.download-certificate', $app->secure_id)); ?>?inline=1" target="_blank" class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] px-2.5 py-1 rounded-md font-extrabold border border-slate-200 transition">
                                            <span class="material-symbols-outlined text-[13px] font-bold">picture_as_pdf</span> View PDF
                                        </a>
                                    </div>
                                <?php elseif($app->physical_possession_status === 'Rejected'): ?>
                                    <a href="<?php echo e(route('mmgay.bdo.verify-form', $app->secure_id)); ?>" class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] px-2.5 py-1 rounded-md font-extrabold border border-blue-200 transition">
                                        <span class="material-symbols-outlined text-[13px] font-bold">visibility</span> View Details
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="px-3 py-6 text-center text-slate-400 font-semibold">No applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <?php echo e($applications->links('partials.compact-pagination')); ?>

        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmgayBdoAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\sports\housing_project\resources\views/mmgay/bdo/applications.blade.php ENDPATH**/ ?>