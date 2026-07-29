
<?php $__env->startSection('title', 'Physical Possession Not Eligible'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $filters = [
        'search' => request('search', ''),
        'district_id' => request('district_id'),
        'city_id' => request('city_id'),
        'sector_id' => request('sector_id'),
    ];
?>
<main class="ml-52 min-h-screen bg-slate-50 px-5 pb-6 pt-20">
    <div class="mx-auto max-w-[1800px] space-y-4">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-900">Physical Possession Not Eligible</h1>
                    <p class="mt-0.5 text-xs text-slate-500">
                        Candidates with total received amount below ₹60,000
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('physical.possession.not-eligible.csv', request()->query())); ?>"
                        class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-700">
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        Excel CSV
                    </a>
                    <a href="<?php echo e(route('physical.possession.not-eligible.print', request()->query())); ?>" target="_blank"
                        class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-slate-800 px-3 text-xs font-semibold text-white hover:bg-slate-900">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        Print
                    </a>
                </div>
            </div>

            <form method="GET" action="<?php echo e(route('physical.possession.not-eligible')); ?>"
                class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-12">

                <input type="search" name="search" value="<?php echo e($filters['search']); ?>"
                    placeholder="Asset, applicant, mobile..."
                    class="h-10 rounded-lg border border-slate-200 px-3 text-xs outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 xl:col-span-4">

                <select name="district_id" id="district_id"
                    onchange="document.getElementById('city_id').value=''; document.getElementById('sector_id').value=''; this.form.submit();"
                    class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 xl:col-span-2">
                    <option value="">All Districts</option>
                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($district->DistrictId); ?>" <?php if($filters['district_id'] == $district->DistrictId): echo 'selected'; endif; ?>>
                            <?php echo e($district->DistrictName); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="city_id" id="city_id"
                    onchange="document.getElementById('sector_id').value=''; this.form.submit();"
                    <?php if(!$filters['district_id']): echo 'disabled'; endif; ?>
                    class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 xl:col-span-2">
                    <option value="">
                        <?php echo e($filters['district_id'] ? 'All Cities' : 'Select district first'); ?>

                    </option>
                    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($city->CityId); ?>" <?php if($filters['city_id'] == $city->CityId): echo 'selected'; endif; ?>>
                            <?php echo e($city->CityName); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="sector_id" id="sector_id"
                    <?php if(!$filters['city_id']): echo 'disabled'; endif; ?>
                    class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 xl:col-span-2">
                    <option value="">
                        <?php echo e($filters['city_id'] ? 'All Villages / Sectors' : 'Select city first'); ?>

                    </option>
                    <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sector->SectorId); ?>" <?php if($filters['sector_id'] == $sector->SectorId): echo 'selected'; endif; ?>>
                            <?php echo e($sector->SectorName); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <div class="flex gap-2 xl:col-span-2">
                    <button class="flex h-10 flex-1 items-center justify-center rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    </button>
                    <a href="<?php echo e(route('physical.possession.not-eligible')); ?>"
                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Not Eligible Candidates</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">
                        <?php echo e(number_format($applications->total())); ?> filtered records
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1120px] text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">ID / Application</th>
                            <th class="px-4 py-3">Applicant</th>
                            <th class="px-4 py-3">Property</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3 text-right">Total Cost</th>
                            <th class="px-4 py-3 text-right">Received</th>
                            <th class="px-4 py-3 text-right">Pending</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800">Asset #<?php echo e($application->asset_id); ?></p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        Purchaser App:
                                        <?php echo e(data_get($application, 'purchaser_application_number')
                                            ?: data_get($application, 'application_number')
                                            ?: '-'); ?>

                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800"><?php echo e($application->applicant_name ?: '-'); ?></p>
                                    <p class="mt-0.5 text-[10px] text-slate-400"><?php echo e($application->mobile ?: '-'); ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-700"><?php echo e($application->asset_name ?: '-'); ?></p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        Asset #<?php echo e($application->asset_id); ?> · <?php echo e($application->asset_size); ?> <?php echo e($application->asset_unit); ?>

                                    </p>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?php echo e($application->district_name ?: '-'); ?>

                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        <?php echo e($application->city_name); ?> / <?php echo e($application->sector_name); ?>

                                    </p>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-700">
                                    ₹<?php echo e(number_format($application->flat_cost ?? 0, 2)); ?>

                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="font-bold text-emerald-600">
                                        ₹<?php echo e(number_format($application->received_amount ?? 0, 2)); ?>

                                    </p>
                                    <p class="mt-0.5 text-[9px] text-slate-400">Initial + cash receipts</p>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-rose-600">
                                    ₹<?php echo e(number_format($application->pending_amount ?? 0, 2)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center text-sm text-slate-400">
                                    No not-eligible candidates found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($applications->hasPages()): ?>
                <div
                    class="flex flex-col gap-3 border-t border-slate-100 bg-white px-4 py-3
                    sm:flex-row sm:items-center sm:justify-between">

                    <p class="text-xs text-slate-500">
                        Showing
                        <span class="font-semibold text-slate-700">
                            <?php echo e(number_format($applications->firstItem())); ?>

                        </span>
                        to
                        <span class="font-semibold text-slate-700">
                            <?php echo e(number_format($applications->lastItem())); ?>

                        </span>
                        of
                        <span class="font-semibold text-slate-700">
                            <?php echo e(number_format($applications->total())); ?>

                        </span>
                        records
                    </p>

                    <nav class="flex flex-wrap items-center gap-1" aria-label="Pagination">
                        <?php if($applications->onFirstPage()): ?>
                            <span
                                class="inline-flex h-8 cursor-not-allowed items-center gap-1 rounded-lg
                                border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium text-slate-300">
                                <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                                <span class="hidden sm:inline">Previous</span>
                            </span>
                        <?php else: ?>
                            <a href="<?php echo e($applications->previousPageUrl()); ?>"
                                class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200
                                bg-white px-2.5 text-xs font-medium text-slate-600 transition
                                hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                                <span class="hidden sm:inline">Previous</span>
                            </a>
                        <?php endif; ?>

                        <?php if($applications->currentPage() > 3): ?>
                            <a href="<?php echo e($applications->url(1)); ?>"
                                class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg
                                border border-slate-200 bg-white px-2 text-xs font-medium text-slate-600
                                transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                1
                            </a>

                            <?php if($applications->currentPage() > 4): ?>
                                <span class="inline-flex h-8 min-w-8 items-center justify-center text-xs text-slate-400">
                                    …
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php $__currentLoopData = $applications->getUrlRange(
                            max(1, $applications->currentPage() - 2),
                            min($applications->lastPage(), $applications->currentPage() + 2)
                        ); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == $applications->currentPage()): ?>
                                <span aria-current="page"
                                    class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg
                                    bg-indigo-600 px-2 text-xs font-semibold text-white shadow-sm">
                                    <?php echo e($page); ?>

                                </span>
                            <?php else: ?>
                                <a href="<?php echo e($url); ?>"
                                    class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg
                                    border border-slate-200 bg-white px-2 text-xs font-medium text-slate-600
                                    transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                    <?php echo e($page); ?>

                                </a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if($applications->currentPage() < $applications->lastPage() - 2): ?>
                            <?php if($applications->currentPage() < $applications->lastPage() - 3): ?>
                                <span class="inline-flex h-8 min-w-8 items-center justify-center text-xs text-slate-400">
                                    …
                                </span>
                            <?php endif; ?>

                            <a href="<?php echo e($applications->url($applications->lastPage())); ?>"
                                class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg
                                border border-slate-200 bg-white px-2 text-xs font-medium text-slate-600
                                transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                <?php echo e($applications->lastPage()); ?>

                            </a>
                        <?php endif; ?>

                        <?php if($applications->hasMorePages()): ?>
                            <a href="<?php echo e($applications->nextPageUrl()); ?>"
                                class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200
                                bg-white px-2.5 text-xs font-medium text-slate-600 transition
                                hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                                <span class="hidden sm:inline">Next</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </a>
                        <?php else: ?>
                            <span
                                class="inline-flex h-8 cursor-not-allowed items-center gap-1 rounded-lg
                                border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium text-slate-300">
                                <span class="hidden sm:inline">Next</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </span>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/physicalPossessionNotEligible.blade.php ENDPATH**/ ?>