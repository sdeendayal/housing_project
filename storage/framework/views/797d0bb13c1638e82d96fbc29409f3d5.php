<aside class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-lg z-40 bg-surface-container shadow-none border-r border-outline-variant">
    <!-- Logo/Header -->
    <div class="px-md mb-xl flex items-center gap-sm">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-on-primary">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">holiday_village</span>
        </div>
        <div>
            <h1 class="text-headline-md font-headline-md text-on-surface font-bold leading-tight">MMGAY BDPO</h1>
            <p class="text-[10px] uppercase tracking-wider text-on-surface-variant font-bold">Possession Portal</p>
        </div>
    </div>
    <!-- Navigation Links -->
    <nav class="flex-grow px-sm space-y-1.5 overflow-y-auto">
        <!-- 1. Dashboard -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'dashboard') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.dashboard')); ?>">
            <span class="material-symbols-outlined text-base">dashboard</span>
            <span class="font-label-md text-label-md text-xs">Dashboard</span>
        </a>

        <div class="h-[1px] bg-outline-variant/60 my-2 mx-md"></div>

        <!-- 2. Schedule Pending -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'schedule_pending') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.eligibility-list')); ?>">
            <span class="material-symbols-outlined text-base">pending_actions</span>
            <span class="font-label-md text-label-md text-xs">Schedule Pending</span>
        </a>

        <!-- 3. Awaiting Citizen -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'awaiting_citizen') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.possession-applications', ['status' => 'Visit Scheduled'])); ?>">
            <span class="material-symbols-outlined text-base">contact_support</span>
            <span class="font-label-md text-label-md text-xs">Awaiting Citizen</span>
        </a>

        <!-- 4. Field Visit Pending -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'field_visit_pending') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.possession-applications', ['status' => 'Slot Selected'])); ?>">
            <span class="material-symbols-outlined text-base">location_on</span>
            <span class="font-label-md text-label-md text-xs">Field Visit Pending</span>
        </a>

        <!-- 5. E-Possession Pending -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'epossession_pending') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.possession-applications', ['status' => 'Site Verified'])); ?>">
            <span class="material-symbols-outlined text-base">description</span>
            <span class="font-label-md text-label-md text-xs">Possession Pending</span>
        </a>

        <!-- 6. Verified -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'verified') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.possession-applications', ['status' => 'Verified'])); ?>">
            <span class="material-symbols-outlined text-base">verified</span>
            <span class="font-label-md text-label-md text-xs">Verified / Completed</span>
        </a>

        <!-- 7. Phase Analytics -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'phase_report') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.phase-report')); ?>">
            <span class="material-symbols-outlined text-base">analytics</span>
            <span class="font-label-md text-label-md text-xs">Phase Analytics</span>
        </a>

        <!-- Villages Report -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'villages_report') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.villages-report')); ?>">
            <span class="material-symbols-outlined text-base">holiday_village</span>
            <span class="font-label-md text-label-md text-xs">Villages List</span>
        </a>

        <!-- 8. Site Development -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'site_development') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.site-development')); ?>">
            <span class="material-symbols-outlined text-base">engineering</span>
            <span class="font-label-md text-label-md text-xs">Site Development</span>
        </a>

        <!-- Owner Status Report -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'owner_status_report') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.owner-status-report')); ?>">
            <span class="material-symbols-outlined text-base">assignment</span>
            <span class="font-label-md text-label-md text-xs">Owner Status Report</span>
        </a>

        <!-- HFA API Test Tool -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'hfa_api_test') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.hfa-api-test')); ?>">
            <span class="material-symbols-outlined text-base">api</span>
            <span class="font-label-md text-label-md text-xs">HFA API Test Tool</span>
        </a>

        <!-- 9. Profile -->
        <a class="flex items-center gap-md rounded-lg px-md py-2 transition-all duration-205 ease-in-out <?php echo e((isset($activeMenu) && $activeMenu === 'profile') ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold' : 'text-on-surface-variant hover:bg-surface-variant'); ?>"
            href="<?php echo e(route('mmgay.bdo.profile')); ?>">
            <span class="material-symbols-outlined text-base">account_circle</span>
            <span class="font-label-md text-label-md text-xs">Profile</span>
        </a>
    </nav>
    <!-- Footer / Support -->
    <div class="mt-auto px-md pt-lg border-t border-outline-variant pb-md">
        <div class="mb-4 px-2">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Active Location</p>
            <p class="text-[11px] font-bold text-slate-700 uppercase mt-1.5">
                Dist: <span class="text-primary font-extrabold"><?php echo e(Auth::user()->district_name ?? 'N/A'); ?></span> 
                <span class="text-slate-300 mx-1.5">|</span> 
                Block: <span class="text-primary font-extrabold"><?php echo e(Auth::user()->block_name ?? 'ALL'); ?></span>
            </p>
        </div>
        <form action="<?php echo e(route('mmgay.bdo.logout')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full flex items-center gap-md px-md py-sm rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 font-semibold text-xs">
                <span class="material-symbols-outlined text-base">logout</span>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Top Navbar Header mapping -->
<header class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-lg bg-surface-container-lowest shadow-sm border-b border-outline-variant">
    <div class="flex items-center gap-md">
        <h2 class="text-headline-md font-headline-md font-bold text-primary"><?php echo $__env->yieldContent('page_header', 'BDPO Portal'); ?></h2>
        <div class="h-6 w-[1px] bg-outline-variant"></div>
        <span class="text-xs text-slate-500 font-medium">Mukhyamantri Gramin Awas Yojana</span>
    </div>
    <div class="flex items-center gap-md">
        <a href="<?php echo e(route('mmgay.bdo.profile')); ?>" class="flex items-center gap-sm pl-md hover:opacity-80 transition-all duration-200">
            <div class="text-right border-r pr-3 border-outline-variant mr-1">
                <p class="text-body-md font-body-md font-bold text-on-surface"><?php echo e(Auth::user()->name); ?></p>
                <p class="text-[10px] text-on-surface-variant font-semibold">BDPO Officer</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200 shadow-sm">
                <?php echo e(substr(Auth::user()->name, 0, 2)); ?>

            </div>
        </a>
    </div>
</header>
<?php /**PATH E:\sports\housing_project\resources\views/mmgay/bdo/bdoSidebar.blade.php ENDPATH**/ ?>