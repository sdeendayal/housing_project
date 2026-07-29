<?php $__env->startSection('title', 'MMSAY Department Dashboard'); ?>
<?php $__env->startSection('content'); ?>

    <?php if(session('success')): ?>
        <div id="successToast" class="success-toast">
            <span class="material-symbols-outlined me-2">
                check_circle
            </span>

            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">
            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50">
                            <span class="material-symbols-outlined text-violet-600">
                                filter_alt
                            </span>
                        </div>

                        <div>
                            <h2 class="text-sm font-bold text-slate-800">
                                Dashboard Filters
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Filter dashboard data by location
                            </p>
                        </div>
                    </div>

                    <?php if($districtId || $cityId || $sectorId): ?>
                        <div
                            class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600">
                            <span class="material-symbols-outlined text-[16px]">
                                check_circle
                            </span>
                            Filter applied
                        </div>
                    <?php endif; ?>
                </div>

                
                <form method="GET" action="<?php echo e(url()->current()); ?>" id="dashboardFilterForm" class="p-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

                        
                        <div>
                            <label for="district_id"
                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                                District
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                                    location_city
                                </span>

                                <select name="district_id" id="district_id"
                                    class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-9 text-sm font-medium text-slate-700 outline-none transition
                               hover:border-violet-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-100">
                                    <option value="">All Districts</option>

                                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($district->DistrictId); ?>" <?php if((string) $districtId === (string) $district->DistrictId): echo 'selected'; endif; ?>>
                                            <?php echo e($district->DistrictName); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <span
                                    class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                    expand_more
                                </span>
                            </div>
                        </div>

                        
                        <div>
                            <label for="city_id"
                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Block/Town
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                                    apartment
                                </span>

                                <select name="city_id" id="city_id" <?php if(!$districtId): echo 'disabled'; endif; ?>
                                    class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-9 text-sm font-medium text-slate-700 outline-none transition
                               hover:border-violet-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-100
                               disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-100 disabled:text-slate-400">
                                    <option value="">
                                        <?php echo e($districtId ? 'All Cities' : 'Select district first'); ?>

                                    </option>

                                    <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($city->CityId); ?>" <?php if((string) $cityId === (string) $city->CityId): echo 'selected'; endif; ?>>
                                            <?php echo e($city->CityName); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <span
                                    class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                    expand_more
                                </span>
                            </div>
                        </div>

                        
                        <div>
                            <label for="sector_id"
                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Village/Ward
                            </label>

                            <div class="relative">
                                <span
                                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                                    grid_view
                                </span>

                                <select name="sector_id" id="sector_id" <?php if(!$cityId): echo 'disabled'; endif; ?>
                                    class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-9 text-sm font-medium text-slate-700 outline-none transition
                               hover:border-violet-300 focus:border-violet-500 focus:bg-white focus:ring-4 focus:ring-violet-100
                               disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-100 disabled:text-slate-400">
                                    <option value="">
                                        <?php echo e($cityId ? 'All Sectors' : 'Select city first'); ?>

                                    </option>

                                    <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($sector->SectorId); ?>" <?php if((string) $sectorId === (string) $sector->SectorId): echo 'selected'; endif; ?>>
                                            <?php echo e($sector->SectorName); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <span
                                    class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                    expand_more
                                </span>
                            </div>
                        </div>

                        
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 text-sm font-semibold text-white shadow-sm transition
                           hover:bg-violet-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-violet-200">
                                <span class="material-symbols-outlined text-[19px]">
                                    filter_alt
                                </span>
                                Apply
                            </button>

                            <a href="<?php echo e(url()->current()); ?>" title="Reset filters"
                                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition
                           hover:border-red-200 hover:bg-red-50 hover:text-red-500 focus:outline-none focus:ring-4 focus:ring-red-100">
                                <span class="material-symbols-outlined text-[20px]">
                                    restart_alt
                                </span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            

            <!-- Bento Grid - Summary Metrics -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12">

                
                <a href="<?php echo e(url('mmsay-department-property-registration') .
                    '?' .
                    http_build_query(
                        array_filter([
                            'district_id' => $districtId,
                            'city_id' => $cityId,
                            'sector_id' => $sectorId,
                        ]),
                    )); ?>"
                    class="group rounded-xl border border-indigo-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md lg:col-span-2">

                    <div class="flex items-start justify-between">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 transition group-hover:bg-indigo-100">
                            <span class="material-symbols-outlined text-[20px]">
                                person_add
                            </span>
                        </div>

                        <span
                            class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-400">
                            arrow_outward
                        </span>
                    </div>

                    <p class="mt-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Registration
                    </p>

                    <h3 class="mt-1 text-2xl font-bold leading-none text-slate-800">
                        <?php echo e(number_format($totalApplications ?? 0)); ?>

                    </h3>

                    <p class="mt-2 text-[11px] text-slate-400">
                        Total properties
                    </p>
                </a>

                
                <a href="<?php echo e(url('mmsay-department-draw')); ?>"
                    class="group rounded-xl border border-emerald-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md lg:col-span-2">

                    <div class="flex items-start justify-between">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 transition group-hover:bg-emerald-100">
                            <span class="material-symbols-outlined text-[20px]">
                                casino
                            </span>
                        </div>

                        <span
                            class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-emerald-400">
                            arrow_outward
                        </span>
                    </div>

                    <p class="mt-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Draw
                    </p>

                    <h3 class="mt-1 text-2xl font-bold leading-none text-emerald-600">
                        <?php echo e(number_format($allottedUnits ?? 0)); ?>

                    </h3>

                    <p class="mt-2 text-[11px] text-slate-400">
                        Draw process
                    </p>
                </a>

                
                <a href="<?php echo e(url('mmsay-department-property-registration')); ?>?<?php echo e(http_build_query(
                    array_filter([
                        'property_view' => 'allotted',
                        'district_id' => $districtId ?? null,
                        'city_id' => $cityId ?? null,
                        'sector_id' => $sectorId ?? null,
                    ]),
                )); ?>"
                    class="group rounded-xl border border-orange-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-md lg:col-span-2">

                    <div class="flex items-start justify-between">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-50 text-orange-500 transition group-hover:bg-orange-100">
                            <span class="material-symbols-outlined text-[20px]">
                                apartment
                            </span>
                        </div>

                        <span
                            class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-orange-400">
                            arrow_outward
                        </span>
                    </div>

                    <p class="mt-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Allotted
                    </p>

                    <h3 class="mt-1 text-2xl font-bold leading-none text-orange-600">
                        <?php echo e(number_format($allottedUnits ?? 0)); ?>

                    </h3>

                    <p class="mt-2 text-[11px] text-slate-400">
                        Plot / flat assigned
                    </p>
                </a>

                
                <div class="rounded-xl border border-amber-100 bg-white p-4 shadow-sm lg:col-span-2">

                    <div class="flex items-center justify-between">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-500">
                            <span class="material-symbols-outlined text-[20px]">
                                payments
                            </span>
                        </div>

                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            EMI Payment Status
                        </p>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2">

                        
                        <a href="<?php echo e(url('full-paid-properties') .
                            '?' .
                            http_build_query(
                                array_filter([
                                    'district_id' => $districtId,
                                    'city_id' => $cityId,
                                    'sector_id' => $sectorId,
                                ]),
                            )); ?>"
                            class="rounded-lg bg-emerald-50 px-2 py-2.5 text-center transition hover:bg-emerald-100">
                            <p class="text-[9px] font-medium uppercase text-slate-500">
                                Full Payment
                            </p>

                            <p class="mt-1 text-lg font-bold leading-none text-emerald-600">
                                <?php echo e(number_format($paymentStats->total_paid_properties ?? 0)); ?>

                            </p>
                        </a>

                        
                        <a href="<?php echo e(url('partial-paid-properties') .
                            '?' .
                            http_build_query(
                                array_filter([
                                    'district_id' => $districtId,
                                    'city_id' => $cityId,
                                    'sector_id' => $sectorId,
                                ]),
                            )); ?>"
                            class="rounded-lg bg-amber-50 px-2 py-2.5 text-center transition hover:bg-amber-100">
                            <p class="text-[9px] font-medium uppercase text-slate-500">
                                Partial Payment
                            </p>

                            <p class="mt-1 text-lg font-bold leading-none text-amber-600">
                                <?php echo e(number_format($paymentStats->pending_properties ?? 0)); ?>

                            </p>
                        </a>
                    </div>
                </div>

                
                <div
                    class="overflow-hidden rounded-xl border border-violet-100 bg-white shadow-sm sm:col-span-2 lg:col-span-4">

                    
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                                <span class="material-symbols-outlined text-[20px]">
                                    real_estate_agent
                                </span>
                            </div>

                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-600">
                                    Physical Possession
                                </p>

                                <p class="mt-0.5 text-[10px] text-slate-400">
                                    Payment eligibility
                                </p>
                            </div>
                        </div>

                        <span class="rounded-full bg-violet-50 px-2 py-1 text-[9px] font-semibold text-violet-600">
                            ₹60,000 minimum
                        </span>
                    </div>

                    
                    <div class="grid grid-cols-2 divide-x divide-slate-100">

                        <a href="<?php echo e(route(
                            'physical.possession.index',
                            array_filter([
                                'district_id' => $districtId ?? null,
                                'city_id' => $cityId ?? null,
                                'sector_id' => $sectorId ?? null,
                            ]),
                        )); ?>"
                            class="group px-4 py-3 transition hover:bg-emerald-50/60">

                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[17px] text-emerald-600">
                                    task_alt
                                </span>

                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                    Eligible
                                </p>
                            </div>

                            <div class="mt-2 flex items-end justify-between gap-2">
                                <div>
                                    <h3 class="text-2xl font-bold leading-none text-emerald-600">
                                        <?php echo e(number_format($eligiblePhysicalPossession ?? 0)); ?>

                                    </h3>

                                    <p class="mt-1.5 text-[10px] leading-tight text-slate-400">
                                        ₹60,000 or more
                                    </p>
                                </div>

                                <span
                                    class="material-symbols-outlined text-[17px] text-slate-300 transition
                   group-hover:translate-x-0.5 group-hover:text-emerald-500">
                                    arrow_forward
                                </span>
                            </div>
                        </a>

                        <a href="<?php echo e(route(
                            'physical.possession.not-eligible',
                            array_filter([
                                'district_id' => $districtId ?? null,
                                'city_id' => $cityId ?? null,
                                'sector_id' => $sectorId ?? null,
                            ]),
                        )); ?>"
                            class="group px-4 py-3 transition hover:bg-rose-50/60">

                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[17px] text-rose-500">
                                    pending_actions
                                </span>

                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                    Not Eligible
                                </p>
                            </div>

                            <div class="mt-2 flex items-end justify-between gap-2">
                                <div>
                                    <h3 class="text-2xl font-bold leading-none text-rose-600">
                                        <?php echo e(number_format($notEligiblePhysicalPossession ?? 0)); ?>

                                    </h3>

                                    <p class="mt-1.5 text-[10px] leading-tight text-slate-400">
                                        Below ₹60,000
                                    </p>
                                </div>

                                <span
                                    class="material-symbols-outlined text-[17px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-rose-500">
                                    arrow_forward
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <span class="material-symbols-outlined text-[20px]">
                                real_estate_agent
                            </span>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-800">
                                Latest Scheduled Possession Applications
                            </h3>
                            <p class="mt-0.5 text-xs text-slate-400">
                                Latest 10 applications with a citizen-selected visit slot
                            </p>
                        </div>
                    </div>

                    <a href="<?php echo e(route(
                        'physical.possession.index',
                        array_filter([
                            'district_id' => $districtId ?? null,
                            'city_id' => $cityId ?? null,
                            'sector_id' => $sectorId ?? null,
                        ]),
                    )); ?>"
                        class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-violet-100 bg-violet-50 px-3 text-xs font-semibold text-violet-600 transition hover:bg-violet-100">
                        View All
                        <span class="material-symbols-outlined text-[16px]">
                            arrow_forward
                        </span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-left text-xs">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                <th class="px-5 py-3">Application</th>
                                <th class="px-5 py-3">Applicant</th>
                                <th class="px-5 py-3">Property</th>
                                <th class="px-5 py-3">Location</th>
                                <th class="px-5 py-3 text-right">Received</th>
                                <th class="px-5 py-3">Schedule</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <?php $__empty_1 = true; $__currentLoopData = $latestPhysicalApplications ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $currentStatus =
                                        $application->physical_possession_status ?: $application->status ?: 'Pending';

                                    $statusClass = match (strtolower($currentStatus)) {
                                        'verified', 'approved' => 'bg-emerald-50 text-emerald-600',
                                        'site verified' => 'bg-cyan-50 text-cyan-600',
                                        'slot selected' => 'bg-orange-50 text-orange-600',
                                        'visit scheduled' => 'bg-blue-50 text-blue-600',
                                        default => 'bg-amber-50 text-amber-600',
                                    };
                                ?>

                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="px-5 py-3.5">
                                        <p class="font-bold text-slate-800">
                                            <?php echo e($application->application_number ?: 'Application #' . $application->id); ?>

                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            Asset #<?php echo e($application->asset_id ?: '-'); ?>

                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-800">
                                            <?php echo e($application->applicant_name ?: '-'); ?>

                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            <?php echo e($application->mobile ?: '-'); ?>

                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-slate-700">
                                            <?php echo e($application->asset_name ?: '-'); ?>

                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            <?php echo e($application->asset_size ?: '-'); ?>

                                            <?php echo e($application->asset_unit ?: ''); ?>

                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-slate-700">
                                            <?php echo e($application->district_name ?: '-'); ?>

                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            <?php echo e(collect([$application->city_name, $application->sector_name])->filter()->implode(' / ') ?:
                                                '-'); ?>

                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5 text-right">
                                        <p class="font-bold text-emerald-600">
                                            ₹<?php echo e(number_format((float) ($application->received_amount ?? 0), 2)); ?>

                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <?php if($application->citizen_visit_date): ?>
                                            <p class="font-medium text-slate-700">
                                                <?php echo e(\Illuminate\Support\Carbon::parse($application->citizen_visit_date)->format('d M Y, h:i A')); ?>

                                            </p>
                                        <?php else: ?>
                                            <span class="text-slate-400">Not selected</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide <?php echo e($statusClass); ?>">
                                            <?php echo e($currentStatus); ?>

                                        </span>
                                    </td>

                                    <td class="px-5 py-3.5 text-right">
                                        <a href="<?php echo e(url('mmsay-department-physical-possession/' . $application->asset_id . '/view')); ?>"
                                            class="inline-flex h-8 items-center justify-center gap-1 rounded-lg bg-indigo-50 px-3 text-[10px] font-semibold text-indigo-600 transition hover:bg-indigo-100">

                                            <span class="material-symbols-outlined text-[15px]">
                                                visibility
                                            </span>

                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="px-5 py-10 text-center">
                                        <span class="material-symbols-outlined text-3xl text-slate-300">
                                            inbox
                                        </span>
                                        <p class="mt-2 text-xs font-medium text-slate-500">
                                            No physical possession applications found.
                                        </p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/departmentDashboard.blade.php ENDPATH**/ ?>