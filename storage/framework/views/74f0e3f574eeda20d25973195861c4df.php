<aside
    class="fixed left-0 top-0 h-full w-[260px] flex flex-col z-40 bg-white border-r border-slate-200 shadow-sm transition-all duration-300">

    <div class="px-6 h-20 flex items-center gap-3 border-b border-slate-100">
        <div
            class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-indigo-200">
            <span class="material-symbols-outlined font-icon text-xl">home_work</span>
        </div>
        <div>
            <h1 class="text-base font-bold text-slate-800 leading-tight">MMGAY Admin</h1>
            <p class="text-[10px] uppercase tracking-wider text-indigo-600 font-bold">Management Portal</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">

        
        <a href="<?php echo e(route('admin.dashboard')); ?>"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
        <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 shadow-sm font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:pl-4'); ?>">

            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="text-[13px] font-medium">Dashboard</span>

        </a>

        
        <div class="px-3 pt-3 pb-1">
            <p class="text-[10px] uppercase tracking-[2px] text-slate-400 font-bold">
                Master
            </p>
        </div>
        <a href="<?php echo e(route('admin.district.report')); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
        <?php echo e(request()->routeIs('admin.district.report')
            ? 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-100 shadow-sm'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
            <span
                class="material-symbols-outlined flex h-8 w-8 items-center justify-center rounded-lg text-[20px]
            <?php echo e(request()->routeIs('admin.district.report')
                ? 'bg-indigo-600 text-white'
                : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700'); ?>">
                assessment
            </span>

            <span class="flex-1 font-medium">
                District Report
            </span>

            <?php if(request()->routeIs('admin.district.report')): ?>
                <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        <a href="<?php echo e(route('admin.village.report')); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
        <?php echo e(request()->routeIs('admin.village.report*')
            ? 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-100 shadow-sm'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
            <?php echo e(request()->routeIs('admin.village.report*')
                ? 'bg-indigo-600 text-white'
                : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                holiday_village
            </span>

            <span class="flex-1 truncate font-medium">
                Village Report
            </span>

            <?php if(request()->routeIs('admin.village.report*')): ?>
                <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        <a href="<?php echo e(route('superadmin.applicants.index')); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('superadmin.applicants*')
        ? 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('superadmin.applicants*')
            ? 'bg-indigo-600 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                group
            </span>

            <span class="flex-1 truncate font-medium">
                Applicants
            </span>

            <?php if(request()->routeIs('superadmin.applicants*')): ?>
                <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
            <?php endif; ?>

        </a>

        <div class="px-3 pt-4 pb-1">
            <p class="text-[10px] uppercase tracking-[2px] text-slate-400 font-bold">
                Beneficiary
            </p>
        </div>

        
        <a href="<?php echo e(route('admin.allotment.report')); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.allotment.report') && !request('status')
        ? 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.allotment.report') && !request('status')
            ? 'bg-indigo-600 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                home_work
            </span>

            <span class="flex-1 truncate font-medium">
                Allotment Report
            </span>

            <?php if(request()->routeIs('admin.allotment.report') && !request('status')): ?>
                <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>


        
        <a href="<?php echo e(route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['status' => 'approved_paid']),
        )); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'approved_paid'
        ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'approved_paid'
            ? 'bg-green-600 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                verified
            </span>

            <span class="flex-1 truncate font-medium">
                Approved &amp; Paid
            </span>

            <?php if(request()->routeIs('admin.allotment.report') && request('status') === 'approved_paid'): ?>
                <span class="h-2 w-2 rounded-full bg-green-600"></span>
            <?php endif; ?>
        </a>


        
        <a href="<?php echo e(route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['status' => 'approved_unpaid']),
        )); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'approved_unpaid'
        ? 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'approved_unpaid'
            ? 'bg-yellow-500 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                payments
            </span>

            <span class="flex-1 truncate font-medium">
                Approved &amp; Unpaid
            </span>

            <?php if(request()->routeIs('admin.allotment.report') && request('status') === 'approved_unpaid'): ?>
                <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
            <?php endif; ?>
        </a>


        
        <a href="<?php echo e(route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['status' => 'pending']),
        )); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'pending'
        ? 'bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'pending'
            ? 'bg-orange-500 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                pending_actions
            </span>

            <span class="flex-1 truncate font-medium">
                Yet to be Approved
            </span>

            <?php if(request()->routeIs('admin.allotment.report') && request('status') === 'pending'): ?>
                <span class="h-2 w-2 rounded-full bg-orange-500"></span>
            <?php endif; ?>
        </a>


        
        <a href="<?php echo e(route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['status' => 'rejected']),
        )); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'rejected'
        ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'rejected'
            ? 'bg-red-600 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                cancel
            </span>

            <span class="flex-1 truncate font-medium">
                Rejected
            </span>

            <?php if(request()->routeIs('admin.allotment.report') && request('status') === 'rejected'): ?>
                <span class="h-2 w-2 rounded-full bg-red-600"></span>
            <?php endif; ?>
        </a>


        
        <a href="<?php echo e(route(
            'admin.allotment.report',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['status' => 'cancelled']),
        )); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'cancelled'
        ? 'bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-200 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.allotment.report') && request('status') === 'cancelled'
            ? 'bg-slate-700 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                block
            </span>

            <span class="flex-1 truncate font-medium">
                Cancelled
            </span>

            <?php if(request()->routeIs('admin.allotment.report') && request('status') === 'cancelled'): ?>
                <span class="h-2 w-2 rounded-full bg-slate-700"></span>
            <?php endif; ?>
        </a>

        <div class="px-3 pt-4 pb-1">
            <p class="text-[10px] uppercase tracking-[2px] text-slate-400 font-bold">
                Registration
            </p>
        </div>

        
        <a href="<?php echo e(route(
            'admin.registration',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['type' => 'all']),
        )); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.registration') && request('type', 'all') == 'all'
        ? 'bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.registration') && request('type', 'all') == 'all'
            ? 'bg-violet-600 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                description
            </span>

            <span class="flex-1 truncate font-medium">
                Total Registration
            </span>

            <?php if(request()->routeIs('admin.registration') && request('type', 'all') == 'all'): ?>
                <span class="h-2 w-2 rounded-full bg-violet-600"></span>
            <?php endif; ?>
        </a>


        
        <a href="<?php echo e(route(
            'admin.registration',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['type' => 'matched']),
        )); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.registration') && request('type') == 'matched'
        ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.registration') && request('type') == 'matched'
            ? 'bg-green-600 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                verified
            </span>

            <span class="flex-1 truncate font-medium">
                Matched
            </span>

            <?php if(request()->routeIs('admin.registration') && request('type') == 'matched'): ?>
                <span class="h-2 w-2 rounded-full bg-green-600"></span>
            <?php endif; ?>
        </a>


        
        <a href="<?php echo e(route(
            'admin.registration',
            array_merge(request()->only(['phase', 'district_id', 'block_id', 'village_id']), ['type' => 'unmatched']),
        )); ?>"
            class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-200
    <?php echo e(request()->routeIs('admin.registration') && request('type') == 'unmatched'
        ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-100 shadow-sm'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[20px]
        <?php echo e(request()->routeIs('admin.registration') && request('type') == 'unmatched'
            ? 'bg-red-600 text-white'
            : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'); ?>">
                cancel
            </span>

            <span class="flex-1 truncate font-medium">
                Unmatched
            </span>

            <?php if(request()->routeIs('admin.registration') && request('type') == 'unmatched'): ?>
                <span class="h-2 w-2 rounded-full bg-red-600"></span>
            <?php endif; ?>
        </a>

          

    </nav>

    <!-- Bottom Profile & Logout -->

    <!-- Bottom Profile & Logout -->

    <div class="border-t border-slate-200 bg-gradient-to-r from-slate-50 to-white p-3">

        <!-- Profile -->

        <div class="flex items-center gap-2.5 mb-3 p-2.5 rounded-xl bg-white border border-slate-200 shadow-sm">

            <div
                class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 flex items-center justify-center text-white text-sm font-bold shadow">

                SA

            </div>

            <div>

                <h4 class="text-[12px] font-semibold text-slate-800 tracking-wide">
                    Super Admin
                </h4>

                <p class="text-[10px] text-slate-500">
                    MMGAY Management Portal
                </p>

            </div>

        </div>

        <!-- Logout -->

        <form action="<?php echo e(route('mmgay.logout')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
            bg-red-50 border border-red-200 text-red-600 text-[12px] font-semibold
            hover:bg-red-600 hover:text-white hover:border-red-600
            transition-all duration-300">

                <span class="material-symbols-outlined text-[18px]">
                    logout
                </span>

                Logout

            </button>

        </form>

    </div>

</aside>

<header
    class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 bg-gradient-to-r from-indigo-700 to-blue-600 shadow-lg">

    <div class="flex items-center justify-between h-full px-6">

        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-10h8V3h-8v8z" />
                </svg>
            </div>

            <div>
                <h2 class="text-xl font-bold text-white">
                    Super Admin Dashboard
                </h2>
                <p class="text-xs text-blue-100">
                    MMGAY Monitoring System
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4">

            <div
                class="hidden md:flex items-center gap-2 bg-white/15 backdrop-blur px-4 py-2 rounded-xl border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-300" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10m-12 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium text-white">
                    <?php echo e(date('d M Y')); ?>

                </span>
            </div>

            <div class="flex items-center gap-2 bg-white/15 backdrop-blur px-4 py-2 rounded-xl border border-white/20">
                <div class="w-9 h-9 rounded-full bg-white text-indigo-700 flex items-center justify-center font-bold">
                    A
                </div>
                <div class="hidden lg:block text-left">
                    <p class="text-sm font-semibold text-white">
                        Super Admin
                    </p>
                    <p class="text-xs text-blue-100">
                        MMGAY Portal
                    </p>
                </div>
            </div>

        </div>

    </div>
</header>
<?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmgay/super-admin/mmgayadminSidebar.blade.php ENDPATH**/ ?>