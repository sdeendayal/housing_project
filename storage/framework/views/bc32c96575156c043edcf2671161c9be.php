<?php $__env->startSection('title', 'MMSAY Department Property Registration'); ?>

<?php $__env->startSection('content'); ?>
    <main class="ml-52 min-h-screen px-5 pb-5 pt-20">
        <div class="mx-auto max-w-container-max space-y-md">

            
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-primary">
                        Assets List
                    </h2>

                    <p class="mt-0.5 text-sm text-on-surface-variant">
                        View property, purchaser and payment information.
                    </p>
                </div>

                <a href="<?php echo e(url('mmsay-department-dashboard')); ?>"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[17px]">
                        arrow_back
                    </span>
                    Dashboard
                </a>
            </div>

            
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                
                <div class="w-full border-b border-slate-100 bg-white p-4">

                    
                    <div class="mb-3">
                        <h3 class="text-base font-semibold text-slate-800">
                            Property Registration
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-400">
                            <?php echo e(number_format($properties->total())); ?> records found
                        </p>
                    </div>

                    
                    <form method="GET" action="<?php echo e(url()->current()); ?>"
                        class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-12">

                        
                        <div class="relative sm:col-span-2 lg:col-span-3">
                            <span
                                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[17px] text-slate-400">
                                search
                            </span>

                            <input type="search" name="search" value="<?php echo e($search ?? ''); ?>"
                                placeholder="Asset, purchaser, mobile..."
                                class="h-10 w-full rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-3 text-xs text-slate-700 outline-none transition
                       placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        </div>

                        
                        <div class="relative lg:col-span-2">
                            <select name="district_id" id="district_id"
                                class="h-10 w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 pr-8 text-xs text-slate-700 outline-none transition
                       focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                                <option value="">All Districts</option>

                                <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($district->DistrictId); ?>" <?php if(($districtId ?? null) == $district->DistrictId): echo 'selected'; endif; ?>>
                                        <?php echo e($district->DistrictName); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[17px] text-slate-400">
                                expand_more
                            </span>
                        </div>

                        
                        <div class="relative lg:col-span-2">
                            <select name="city_id" id="city_id"
                                class="h-10 w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 pr-8 text-xs text-slate-700 outline-none transition
                       focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                                <option value="">All Cities</option>

                                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($city->CityId); ?>" <?php if(($cityId ?? null) == $city->CityId): echo 'selected'; endif; ?>>
                                        <?php echo e($city->CityName); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[17px] text-slate-400">
                                expand_more
                            </span>
                        </div>

                        
                        <div class="relative lg:col-span-2">
                            <select name="sector_id" id="sector_id"
                                class="h-10 w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 pr-8 text-xs text-slate-700 outline-none transition
                       focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">

                                <option value="">All Sectors</option>

                                <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sector->SectorId); ?>" <?php if(($sectorId ?? null) == $sector->SectorId): echo 'selected'; endif; ?>>
                                        <?php echo e($sector->SectorName); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[17px] text-slate-400">
                                expand_more
                            </span>
                        </div>

                        
                        <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-3">

                            <button type="submit"
                                class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-lg bg-indigo-600 px-3 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                <span class="material-symbols-outlined text-[16px]">
                                    filter_alt
                                </span>
                                Filter
                            </button>

                            <a href="<?php echo e(url()->current()); ?>" title="Reset"
                                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-500">
                                <span class="material-symbols-outlined text-[17px]">
                                    restart_alt
                                </span>
                            </a>

                            <a href="<?php echo e(route('properties.export.csv', request()->query())); ?>" title="Download CSV"
                                class="inline-flex h-10 items-center justify-center gap-1.5 rounded-lg bg-teal-600 px-3 text-xs font-semibold text-white transition hover:bg-teal-700">
                                <span class="material-symbols-outlined text-[16px]">
                                    download
                                </span>
                                <span class="hidden xl:inline">CSV</span>
                            </a>

                            <a href="<?php echo e(route('properties.records.print', request()->query())); ?>" target="_blank"
                                title="Print records"
                                class="inline-flex h-10 items-center justify-center gap-1.5 rounded-lg bg-slate-700 px-3 text-xs font-semibold text-white transition hover:bg-slate-800">
                                <span class="material-symbols-outlined text-[16px]">
                                    print
                                </span>
                                <span class="hidden xl:inline">Print</span>
                            </a>
                        </div>
                    </form>
                </div>

                
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1150px] text-left text-xs">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-3">Asset</th>
                                <th class="px-4 py-3">Property</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3">Purchaser</th>
                                <th class="px-4 py-3">Mobile</th>
                                <th class="px-4 py-3 text-right">Total Cost</th>
                                <th class="px-4 py-3 text-right">Received</th>
                                <th class="px-4 py-3 text-right">Pending</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <?php $__empty_1 = true; $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="transition hover:bg-indigo-50/40">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-slate-800">
                                            #<?php echo e($item->AssetId); ?>

                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            App: <?php echo e($item->application_number ?? '-'); ?>

                                        </p>
                                    </td>

                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-700">
                                            <?php echo e($item->AssetName); ?>

                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            <?php echo e($item->AssetSize); ?> <?php echo e($item->Unit); ?>

                                        </p>
                                    </td>

                                    <td class="px-4 py-3">
                                        <p class="text-slate-700">
                                            <?php echo e($item->district ?? '-'); ?>

                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            <?php echo e($item->city ?? '-'); ?> /
                                            <?php echo e($item->sector ?? '-'); ?>

                                        </p>
                                    </td>

                                    <td class="px-4 py-3 font-medium text-slate-700">
                                        <?php echo e($item->purchaser_name ?? 'Not allotted'); ?>

                                    </td>

                                    <td class="px-4 py-3 text-slate-600">
                                        <?php echo e($item->mobile ?? '-'); ?>

                                    </td>

                                    <td class="px-4 py-3 text-right font-medium text-slate-700">
                                        ₹<?php echo e(number_format($item->FlatCost ?? 0, 2)); ?>

                                    </td>

                                    <td class="px-4 py-3 text-right font-semibold text-emerald-600">
                                        ₹<?php echo e(number_format($item->total_received ?? 0, 2)); ?>

                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <span
                                            class="<?php echo e(($item->pending_amount ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600'); ?> font-semibold">
                                            ₹<?php echo e(number_format($item->pending_amount ?? 0, 2)); ?>

                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="<?php echo e(route('properties.show', $item->AssetId)); ?>"
                                                class="inline-flex h-8 items-center gap-1 rounded-lg bg-indigo-50 px-3 text-[11px] font-semibold text-indigo-600 transition hover:bg-indigo-600 hover:text-white">
                                                <span class="material-symbols-outlined text-[16px]">
                                                    visibility
                                                </span>
                                                Details
                                            </a>

                                            <a href="<?php echo e(route('properties.print', $item->AssetId)); ?>" target="_blank"
                                                title="Print statement"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-700 hover:text-white">
                                                <span class="material-symbols-outlined text-[16px]">
                                                    print
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-sm text-slate-400">
                                        No property records found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <?php if($properties->hasPages()): ?>
                    <div
                        class="flex flex-col gap-3 border-t border-slate-100 bg-white px-4 py-3 sm:flex-row sm:items-center sm:justify-between">

                        
                        <p class="text-xs text-slate-500">
                            Showing
                            <span class="font-semibold text-slate-700">
                                <?php echo e(number_format($properties->firstItem())); ?>

                            </span>
                            to
                            <span class="font-semibold text-slate-700">
                                <?php echo e(number_format($properties->lastItem())); ?>

                            </span>
                            of
                            <span class="font-semibold text-slate-700">
                                <?php echo e(number_format($properties->total())); ?>

                            </span>
                            records
                        </p>

                        
                        <nav class="flex items-center gap-1" aria-label="Pagination">

                            
                            <?php if($properties->onFirstPage()): ?>
                                <span
                                    class="inline-flex h-8 cursor-not-allowed items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium text-slate-300">

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>

                                    Previous
                                </span>
                            <?php else: ?>
                                <a href="<?php echo e($properties->previousPageUrl()); ?>"
                                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-medium text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>

                                    Previous
                                </a>
                            <?php endif; ?>

                            
                            <?php $__currentLoopData = $properties->getUrlRange(max(1, $properties->currentPage() - 2), min($properties->lastPage(), $properties->currentPage() + 2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($page == $properties->currentPage()): ?>
                                    <span aria-current="page"
                                        class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-indigo-600 px-2 text-xs font-semibold text-white shadow-sm">

                                        <?php echo e($page); ?>

                                    </span>
                                <?php else: ?>
                                    <a href="<?php echo e($url); ?>"
                                        class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg border border-slate-200 bg-white px-2 text-xs font-medium text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                        <?php echo e($page); ?>

                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            
                            <?php if($properties->hasMorePages()): ?>
                                <a href="<?php echo e($properties->nextPageUrl()); ?>"
                                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-medium text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                    Next

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </a>
                            <?php else: ?>
                                <span
                                    class="inline-flex h-8 cursor-not-allowed items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium text-slate-300">

                                    Next

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </span>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/departmentPropertyRegistration.blade.php ENDPATH**/ ?>