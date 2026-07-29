

<?php $__env->startSection('title', 'Allotment Report'); ?>

<?php $__env->startSection('content'); ?>

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

        
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-800">
                        Allotment Report
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        View, search and export allotment records with filters.
                    </p>
                </div>

                
                <div class="flex flex-wrap items-center gap-3">

                    
                    <button type="button"
                        class="allotment-download-btn inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                        data-download-type="excel"
                        data-download-url="<?php echo e(route(
                            'admin.allotment.export.excel',
                            request()->only(['phase', 'district_id', 'block_id', 'village_id', 'search', 'status']),
                        )); ?>">
                        <span class="material-symbols-outlined text-[20px]">
                            table_view
                        </span>

                        Download Excel
                    </button>

                    <button type="button"
                        class="allotment-download-btn inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
                        data-download-type="pdf"
                        data-download-url="<?php echo e(route(
                            'admin.allotment.export.pdf',
                            request()->only(['phase', 'district_id', 'block_id', 'village_id', 'search', 'status']),
                        )); ?>">
                        <span class="material-symbols-outlined text-[20px]">
                            picture_as_pdf
                        </span>

                        Download PDF
                    </button>

                </div>

            </div>
        </div>

        
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <form method="GET" action="<?php echo e(route('admin.allotment.report')); ?>"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">

                <div>
                    <label for="phase" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Phase
                    </label>

                    <select name="phase" id="phase"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100">

                        <option value="">All Phases</option>

                        <?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($phase); ?>" <?php if(request('phase') == $phase): echo 'selected'; endif; ?>>
                                <?php echo e($phase); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>
                </div>

                <div>
                    <label for="district_id"
                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        District
                    </label>

                    <select name="district_id" id="district_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100">

                        <option value="">All Districts</option>

                        <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($district->DistrictId); ?>" <?php if(request('district_id') == $district->DistrictId): echo 'selected'; endif; ?>>
                                <?php echo e($district->DistrictName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>
                </div>

                <div>
                    <label for="block_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Block
                    </label>

                    <select name="block_id" id="block_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100">

                        <option value="">All Blocks</option>

                        <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($block->BlockId); ?>" <?php if(request('block_id') == $block->BlockId): echo 'selected'; endif; ?>>
                                <?php echo e($block->BlockName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>
                </div>

                <div>
                    <label for="village_id"
                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Village
                    </label>

                    <select name="village_id" id="village_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100">

                        <option value="">All Villages</option>

                        <?php $__currentLoopData = $villages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $village): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($village->VillageId); ?>" <?php if(request('village_id') == $village->VillageId): echo 'selected'; endif; ?>>
                                <?php echo e($village->VillageName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>
                </div>

                <div>
                    <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Search
                    </label>

                    <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>"
                        placeholder="Name, mobile, application..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100">
                </div>

                <div class="flex items-end gap-2">

                    <button type="submit"
                        class="inline-flex flex-1 items-center justify-center rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700">
                        Apply
                    </button>

                    <a href="<?php echo e(route('admin.allotment.report')); ?>"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">

                        <span class="material-symbols-outlined text-[20px]">
                            restart_alt
                        </span>

                        Reset
                    </a>

                </div>

                <?php if(request('status')): ?>
                    <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                <?php endif; ?>

            </form>
        </div>

        
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">

            <a href="<?php echo e(route('admin.allotment.report', request()->except(['status', 'page']))); ?>"
                class="rounded-2xl border p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md
                <?php echo e(!request('status') ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100' : 'border-slate-200 bg-white'); ?>">

                <p class="text-xs font-semibold uppercase text-slate-500">Total</p>

                <h3 class="mt-2 text-2xl font-bold text-slate-800">
                    <?php echo e(number_format($summary->Total ?? 0)); ?>

                </h3>
            </a>

            <a href="<?php echo e(route('admin.allotment.report', array_merge(request()->except('page'), ['status' => 'approved_paid']))); ?>"
                class="rounded-2xl border p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md
                <?php echo e(request('status') === 'approved_paid'
                    ? 'border-green-500 bg-green-50 ring-2 ring-green-100'
                    : 'border-slate-200 bg-white'); ?>">

                <p class="text-xs font-semibold uppercase text-slate-500">
                    Approved & Paid
                </p>

                <h3 class="mt-2 text-2xl font-bold text-green-700">
                    <?php echo e(number_format($summary->ApprovedPaid ?? 0)); ?>

                </h3>
            </a>

            <a href="<?php echo e(route('admin.allotment.report', array_merge(request()->except('page'), ['status' => 'approved_unpaid']))); ?>"
                class="rounded-2xl border p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md
                <?php echo e(request('status') === 'approved_unpaid'
                    ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-100'
                    : 'border-slate-200 bg-white'); ?>">

                <p class="text-xs font-semibold uppercase text-slate-500">
                    Approved & Unpaid
                </p>

                <h3 class="mt-2 text-2xl font-bold text-amber-700">
                    <?php echo e(number_format($summary->ApprovedUnpaid ?? 0)); ?>

                </h3>
            </a>

            <a href="<?php echo e(route('admin.allotment.report', array_merge(request()->except('page'), ['status' => 'pending']))); ?>"
                class="rounded-2xl border p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md
                <?php echo e(request('status') === 'pending'
                    ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100'
                    : 'border-slate-200 bg-white'); ?>">

                <p class="text-xs font-semibold uppercase text-slate-500">
                    Yet to be Approved
                </p>

                <h3 class="mt-2 text-2xl font-bold text-indigo-700">
                    <?php echo e(number_format($summary->PendingApproval ?? 0)); ?>

                </h3>
            </a>

            <a href="<?php echo e(route('admin.allotment.report', array_merge(request()->except('page'), ['status' => 'rejected']))); ?>"
                class="rounded-2xl border p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md
                <?php echo e(request('status') === 'rejected'
                    ? 'border-red-500 bg-red-50 ring-2 ring-red-100'
                    : 'border-slate-200 bg-white'); ?>">

                <p class="text-xs font-semibold uppercase text-slate-500">
                    Rejected
                </p>

                <h3 class="mt-2 text-2xl font-bold text-red-700">
                    <?php echo e(number_format($summary->Rejected ?? 0)); ?>

                </h3>
            </a>

            <a href="<?php echo e(route('admin.allotment.report', array_merge(request()->except('page'), ['status' => 'cancelled']))); ?>"
                class="rounded-2xl border p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md
                <?php echo e(request('status') === 'cancelled'
                    ? 'border-slate-500 bg-slate-100 ring-2 ring-slate-200'
                    : 'border-slate-200 bg-white'); ?>">

                <p class="text-xs font-semibold uppercase text-slate-500">
                    Cancelled
                </p>

                <h3 class="mt-2 text-2xl font-bold text-slate-700">
                    <?php echo e(number_format($summary->Cancelled ?? 0)); ?>

                </h3>
            </a>

        </div>

        
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        Allotment Records
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Showing <?php echo e($allotments->firstItem() ?? 0); ?>

                        to <?php echo e($allotments->lastItem() ?? 0); ?>

                        of <?php echo e(number_format($allotments->total())); ?> records
                    </p>
                </div>

                <?php if(request('status')): ?>
                    <a href="<?php echo e(route('admin.allotment.report', request()->except(['status', 'page']))); ?>"
                        class="rounded-xl border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-700 hover:bg-orange-100">
                        Clear Status
                    </a>
                <?php endif; ?>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Sr. No.
                            </th>
                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Application
                            </th>
                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Applicant
                            </th>
                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Mobile
                            </th>
                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Location
                            </th>
                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Phase
                            </th>
                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Plot
                            </th>
                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        <?php $__empty_1 = true; $__currentLoopData = $allotments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allotment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                if ((int) $allotment->IsAllotmentCancelled === 1) {
                                    $statusText = 'Cancelled';
                                    $statusClass = 'bg-slate-100 text-slate-700';
                                } elseif ((int) $allotment->IsRejected === 1) {
                                    $statusText = 'Rejected';
                                    $statusClass = 'bg-red-100 text-red-700';
                                } elseif ((int) $allotment->IsApproved === 1 && (int) $allotment->IsPaid === 1) {
                                    $statusText = 'Approved & Paid';
                                    $statusClass = 'bg-green-100 text-green-700';
                                } elseif ((int) $allotment->IsApproved === 1) {
                                    $statusText = 'Approved & Unpaid';
                                    $statusClass = 'bg-amber-100 text-amber-700';
                                } else {
                                    $statusText = 'Yet to be Approved';
                                    $statusClass = 'bg-indigo-100 text-indigo-700';
                                }
                            ?>

                            <tr class="transition hover:bg-slate-50">

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                    <?php echo e(($allotments->firstItem() ?? 1) + $loop->index); ?>

                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <p class="text-sm font-semibold text-slate-800">
                                        <?php echo e($allotment->RegistrationNo ?? '-'); ?>

                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Owner ID: <?php echo e($allotment->OwnerId ?? '-'); ?>

                                    </p>
                                </td>

                                <td class="min-w-[220px] px-4 py-4">
                                    <p class="text-sm font-semibold text-slate-800">
                                        <?php echo e($allotment->OwnerName ?? '-'); ?>

                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        <?php echo e($allotment->FatherHusbandName ?? '-'); ?>

                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                    <?php echo e($allotment->MobileNo ?? '-'); ?>

                                </td>

                                <td class="min-w-[220px] px-4 py-4">
                                    <p class="text-sm font-medium text-slate-700">
                                        <?php echo e($allotment->VillageName ?? '-'); ?>

                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        <?php echo e($allotment->BlockName ?? '-'); ?>,
                                        <?php echo e($allotment->DistrictName ?? '-'); ?>

                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                    <?php echo e($allotment->Phase ?? '-'); ?>

                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <p class="text-sm font-semibold text-slate-800">
                                        <?php echo e($allotment->FlatNo ?? '-'); ?>

                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Flat ID: <?php echo e($allotment->FlatId ?? '-'); ?>

                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?php echo e($statusClass); ?>">
                                        <?php echo e($statusText); ?>

                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                            <tr>
                                <td colspan="8" class="px-6 py-14 text-center">
                                    <h3 class="font-bold text-slate-700">
                                        No allotment records found
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Filters change karke dobara try karein.
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>

            </div>

            <?php if($allotments->hasPages()): ?>
                <div class="border-t border-slate-200 px-5 py-4">
                    <?php echo e($allotments->onEachSide(1)->links('pagination::tailwind')); ?>

                </div>
            <?php endif; ?>

        </div>

    </main>
    <div id="downloadModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">

        <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50">
                <svg class="h-9 w-9 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">

                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4">
                    </circle>

                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                    </path>
                </svg>
            </div>

            <h3 class="mt-5 text-lg font-bold text-slate-800">
                The report is generating.
            </h3>

            <p id="downloadMessage" class="mt-2 text-sm text-slate-500">
                Please wait...
            </p>

            <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full w-2/3 animate-pulse rounded-full bg-blue-600"></div>
            </div>

            <p class="mt-4 text-xs text-slate-400">
                Don't close the page until the download is complete.
            </p>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmgayAdmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmgay/super-admin/allotment-report.blade.php ENDPATH**/ ?>