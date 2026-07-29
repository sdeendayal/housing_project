<?php $__env->startSection('title', 'MMSAY - Lucky Draw'); ?>

<?php $__env->startSection('content'); ?>
    <main class="ml-52 min-h-screen bg-slate-50 px-5 pb-6 pt-20">
        <div class="mx-auto max-w-7xl space-y-4">

            
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">

                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <span class="material-symbols-outlined">
                                casino
                            </span>
                        </div>

                        <div>
                            <h1 class="text-lg font-bold text-slate-900">
                                Lucky Draw
                            </h1>

                            <p class="mt-0.5 text-xs text-slate-500">
                                District-wise registered property summary
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('department.draw.csv', request()->query())); ?>"
                            class="inline-flex h-10 items-center gap-1.5 rounded-xl bg-emerald-600 px-4 text-xs font-semibold text-white transition hover:bg-emerald-700">

                            <span class="material-symbols-outlined text-[17px]">
                                download
                            </span>

                            Excel CSV
                        </a>

                        <a href="<?php echo e(route('department.draw.print', request()->query())); ?>"
                            target="_blank"
                            class="inline-flex h-10 items-center gap-1.5 rounded-xl bg-slate-800 px-4 text-xs font-semibold text-white transition hover:bg-slate-900">

                            <span class="material-symbols-outlined text-[17px]">
                                print
                            </span>

                            Print
                        </a>
                    </div>
                </div>

                <form method="GET"
                    action="<?php echo e(route('department.draw.index')); ?>"
                    class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 lg:grid-cols-12">

                    <div class="relative lg:col-span-5">
                        <select name="district_id"
                            class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 pr-10 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                            <option value="">All Districts</option>

                            <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($district->DistrictId); ?>"
                                    <?php if(($districtId ?? null) == $district->DistrictId): echo 'selected'; endif; ?>>

                                    <?php echo e($district->DistrictName); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <span
                            class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                            expand_more
                        </span>
                    </div>

                    <div class="relative lg:col-span-3">
                        <select name="sort_order"
                            class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 pr-10 text-sm text-slate-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                            <option value="desc" <?php if(($sortOrder ?? 'desc') === 'desc'): echo 'selected'; endif; ?>>
                                Highest Assets First
                            </option>

                            <option value="asc" <?php if(($sortOrder ?? 'desc') === 'asc'): echo 'selected'; endif; ?>>
                                Lowest Assets First
                            </option>
                        </select>

                        <span
                            class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                            swap_vert
                        </span>
                    </div>

                    <div class="flex gap-2 sm:col-span-2 lg:col-span-4">
                        <button type="submit"
                            class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white transition hover:bg-indigo-700">

                            <span class="material-symbols-outlined text-[18px]">
                                filter_alt
                            </span>

                            Apply Filter
                        </button>

                        <a href="<?php echo e(route('department.draw.index')); ?>"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-red-500">

                            <span class="material-symbols-outlined text-[18px]">
                                restart_alt
                            </span>
                        </a>
                    </div>
                </form>
            </section>

            
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-indigo-100 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Districts
                    </p>

                    <p class="mt-2 text-2xl font-bold text-indigo-600">
                        <?php echo e(number_format($drawDistricts->count())); ?>

                    </p>
                </div>

                <div class="rounded-xl border border-orange-100 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Total Registered Assets
                    </p>

                    <p class="mt-2 text-2xl font-bold text-orange-600">
                        <?php echo e(number_format($grandTotal)); ?>

                    </p>
                </div>
            </div>

            
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-bold text-slate-800">
                        District-wise Draw Summary
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Click a district to view its filtered properties
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[750px] text-left text-xs">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                <th class="w-20 px-5 py-3 text-center">S.No.</th>
                                <th class="px-5 py-3">District</th>
                                <th class="px-5 py-3 text-center">Total Assets</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <?php $__empty_1 = true; $__currentLoopData = $drawDistricts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $registrationUrl =
                                        url('mmsay-department-property-registration')
                                        . '?'
                                        . http_build_query([
                                            'property_view' => 'registration',
                                            'district_id' => $district->DistrictId,
                                        ]);
                                ?>

                                <tr class="transition hover:bg-indigo-50/30">
                                    <td class="px-5 py-4 text-center font-medium text-slate-500">
                                        <?php echo e($loop->iteration); ?>

                                    </td>

                                    <td class="px-5 py-4">
                                        <a href="<?php echo e($registrationUrl); ?>"
                                            class="inline-flex items-center gap-2 font-semibold text-slate-800 transition hover:text-indigo-600">

                                            <span
                                                class="material-symbols-outlined text-[18px] text-indigo-500">
                                                location_on
                                            </span>

                                            <?php echo e($district->DistrictName); ?>

                                        </a>
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="inline-flex min-w-16 justify-center rounded-full bg-orange-50 px-3 py-1.5 font-bold text-orange-600">

                                            <?php echo e(number_format($district->total_assets)); ?>

                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-right">
                                        <a href="<?php echo e($registrationUrl); ?>"
                                            class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-indigo-50 px-3 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100">

                                            <span class="material-symbols-outlined text-[16px]">
                                                visibility
                                            </span>

                                            View Properties
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center text-slate-500">
                                        No district assets found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                        <tfoot class="border-t border-slate-200 bg-slate-50">
                            <tr>
                                <td colspan="2" class="px-5 py-4 font-bold text-slate-700">
                                    Grand Total
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex min-w-20 justify-center rounded-full bg-slate-900 px-3 py-1.5 font-bold text-white">

                                        <?php echo e(number_format($grandTotal)); ?>

                                    </span>
                                </td>

                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/departmentDraw.blade.php ENDPATH**/ ?>