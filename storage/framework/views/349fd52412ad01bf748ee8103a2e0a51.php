<?php
    $isActive = fn(array|string $patterns) => collect((array) $patterns)->contains(
        fn($pattern) => request()->is($pattern),
    );

    $menuClass = function (array|string $patterns) use ($isActive) {
        return $isActive($patterns)
            ? 'border-indigo-100 bg-indigo-50 text-indigo-600 shadow-sm'
            : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    };

    $iconClass = function (array|string $patterns) use ($isActive) {
        return $isActive($patterns)
            ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
            : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600';
    };

    $cmsRoutes = [
        'mmsay-department-add-banner*',
        'mmsay-department-add-news*',
        'add-news*',
        'upload-notice*',
        'manage-notice*',
        'upload-tender*',
        'manage-tender*',
    ];

    $cmsIsActive = $isActive($cmsRoutes);

    $dashboardFilters = array_filter(
        request()->only(['district_id', 'city_id', 'sector_id']),
        fn($value) => $value !== null && $value !== '',
    );

    $filteredUrl = fn(string $path) => url($path) .
        ($dashboardFilters ? '?' . http_build_query($dashboardFilters) : '');
?>

<?php
    $propertyView = request('property_view', 'registration');

    $propertyUrl = function (string $view) use ($dashboardFilters) {
        return url('mmsay-department-property-registration') .
            '?' .
            http_build_query(
                array_merge($dashboardFilters, [
                    'property_view' => $view,
                ]),
            );
    };

    $propertyMenuClass = function (string $view) use ($propertyView) {
        return request()->is('mmsay-department-property-registration*') && $propertyView === $view
            ? 'border-indigo-100 bg-indigo-50 text-indigo-600 shadow-sm'
            : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    };

    $propertyIconClass = function (string $view) use ($propertyView) {
        return request()->is('mmsay-department-property-registration*') && $propertyView === $view
            ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
            : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600';
    };
?>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-52 flex-col border-r border-slate-200 bg-white shadow-[4px_0_20px_rgba(15,23,42,0.04)]">

    
    <div class="flex h-20 shrink-0 items-center gap-3 border-b border-slate-100 px-4">

        <div
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-200">

            <img src="/Haryana_emblem.png" alt="Haryana State Emblem" class="h-7 w-7 object-contain brightness-0 invert" />
        </div>

        <div class="min-w-0 leading-tight">
            <h1 class="text-[13px] font-bold text-slate-800">
                Housing For All
            </h1>

            <p class="mt-1 text-[9px] font-bold uppercase tracking-wider text-indigo-600">
                Department Portal
            </p>
        </div>
    </div>

    
    <nav class="sidebar-scroll flex-1 overflow-y-auto px-3 py-4">

        <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
            Overview
        </p>

        
        <a href="<?php echo e(url('mmsay-department-dashboard')); ?>"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
                   <?php echo e($menuClass('mmsay-department-dashboard*')); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       <?php echo e($iconClass('mmsay-department-dashboard*')); ?>">
                dashboard
            </span>

            <span class="min-w-0 flex-1 whitespace-normal leading-tight">
                Dashboard
            </span>

            <?php if($isActive('mmsay-department-dashboard*')): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        <p class="mb-2 mt-5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
            Property
        </p>

        
        <a href="<?php echo e($propertyUrl('registration')); ?>"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
           <?php echo e($propertyMenuClass('registration')); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
               <?php echo e($propertyIconClass('registration')); ?>">
                app_registration
            </span>

            <span class="min-w-0 flex-1 whitespace-normal leading-tight">
                Property Registration
            </span>

            <?php if(request()->is('mmsay-department-property-registration*') && $propertyView === 'registration'): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        
        <a href="<?php echo e($filteredUrl('mmsay-department-draw')); ?>"
            class="group mb-1 flex items-center gap-2 rounded-xl border px-2.5 py-2.5 text-[12px] font-medium transition-all duration-200
                   <?php echo e($menuClass('mmsay-department-draw*')); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       <?php echo e($iconClass('mmsay-department-draw*')); ?>">
                casino
            </span>

            <span class="min-w-0 flex-1 whitespace-normal leading-tight">
                Draw
            </span>

            <?php if($isActive('mmsay-department-draw*')): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        
        <a href="<?php echo e($propertyUrl('allotted')); ?>"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
           <?php echo e($propertyMenuClass('allotted')); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
               <?php echo e($propertyIconClass('allotted')); ?>">
                location_city
            </span>

            <span class="min-w-0 flex-1 whitespace-normal leading-tight">
                Allotted Properties
            </span>

            <?php if(request()->is('mmsay-department-property-registration*') && $propertyView === 'allotted'): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        


        
        <a href="<?php echo e(url('mmsay-department-property-emi-calculation')); ?>"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
                   <?php echo e($menuClass('mmsay-department-property-emi-calculation*')); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       <?php echo e($iconClass('mmsay-department-property-emi-calculation*')); ?>">
                calculate
            </span>

            <span class="min-w-0 flex-1 leading-tight">
                EMI Calculation
            </span>

            <?php if($isActive('mmsay-department-property-emi-calculation*')): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        
        <a href="<?php echo e(url('mmsay-department-add-district-officer')); ?>"
            class="group mb-1 flex items-center gap-3 rounded-xl border px-3 py-2.5 text-[12px] font-medium transition-all duration-200
                   <?php echo e($menuClass('mmsay-department-add-district-officer*')); ?>">

            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       <?php echo e($iconClass('mmsay-department-add-district-officer*')); ?>">
                engineering
            </span>

            <span class="min-w-0 flex-1 whitespace-normal leading-tight">
                Site Engineer
            </span>

            <?php if($isActive('mmsay-department-add-district-officer*')): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        <p class="mb-2 mt-5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
            Payment Reports
        </p>

        
        <a href="<?php echo e($filteredUrl('full-paid-properties')); ?>"
            class="group mb-1 flex items-center gap-2 rounded-xl border px-2.5 py-2.5 text-[12px] font-medium transition-all duration-200
                   <?php echo e($menuClass('full-paid-properties*')); ?>">
            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       <?php echo e($iconClass('full-paid-properties*')); ?>">
                task_alt
            </span>
            <span class="min-w-0 flex-1 whitespace-normal leading-tight">Full Payment</span>
            <?php if($isActive('full-paid-properties*')): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        
        <a href="<?php echo e($filteredUrl('partial-paid-properties')); ?>"
            class="group mb-1 flex items-center gap-2 rounded-xl border px-2.5 py-2.5 text-[12px] font-medium transition-all duration-200
                   <?php echo e($menuClass(['partial-paid-properties*', 'pending-properties*'])); ?>">
            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       <?php echo e($iconClass(['partial-paid-properties*', 'pending-properties*'])); ?>">
                pending_actions
            </span>
            <span class="min-w-0 flex-1 whitespace-normal leading-tight">Partial Payment</span>
            <?php if($isActive(['partial-paid-properties*', 'pending-properties*'])): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        <p class="mb-2 mt-5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
            Possession
        </p>

        
        <a href="<?php echo e(route('physical.possession.index', $dashboardFilters)); ?>"
            class="group mb-1 flex items-center gap-2 rounded-xl border px-2.5 py-2.5 text-[12px] font-medium transition-all duration-200
                   <?php echo e($menuClass('mmsay-department-physical-possession')); ?>">
            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       <?php echo e($iconClass('mmsay-department-physical-possession')); ?>">
                real_estate_agent
            </span>
            <span class="min-w-0 flex-1 whitespace-normal leading-tight">
                Possession Eligible
            </span>
            <?php if($isActive('mmsay-department-physical-possession')): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        
        <a href="<?php echo e(route('physical.possession.not-eligible', $dashboardFilters)); ?>"
            class="group mb-1 flex items-center gap-2 rounded-xl border px-2.5 py-2.5 text-[12px] font-medium transition-all duration-200
                   <?php echo e($menuClass('mmsay-department-physical-possession/not-eligible*')); ?>">
            <span
                class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                       <?php echo e($iconClass('mmsay-department-physical-possession/not-eligible*')); ?>">
                block
            </span>
            <span class="min-w-0 flex-1 whitespace-normal leading-tight">
                Possession Not Eligible
            </span>
            <?php if($isActive('mmsay-department-physical-possession/not-eligible*')): ?>
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600"></span>
            <?php endif; ?>
        </a>

        <p class="mb-2 mt-5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
            Management
        </p>

        
        <div x-data="{ cmsOpen: <?php echo e($cmsIsActive ? 'true' : 'false'); ?> }">

            <button type="button" @click="cmsOpen = !cmsOpen"
                class="group flex w-full items-center gap-3 rounded-xl border px-3 py-2.5 text-left text-[12px] font-medium transition-all duration-200
                       <?php echo e($cmsIsActive
                           ? 'border-indigo-100 bg-indigo-50 text-indigo-600 shadow-sm'
                           : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">

                <span
                    class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[18px] transition
                           <?php echo e($cmsIsActive
                               ? 'bg-indigo-600 text-white'
                               : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600'); ?>">
                    edit_square
                </span>

                <span class="min-w-0 flex-1 whitespace-normal leading-tight">
                    CMS Management
                </span>

                <span class="material-symbols-outlined text-[17px] transition-transform duration-200"
                    :class="{ 'rotate-180': cmsOpen }">
                    expand_more
                </span>
            </button>

            
            <div x-cloak x-show="cmsOpen" x-collapse class="ml-4 mt-1.5 border-l border-slate-200 pl-3">

                <a href="<?php echo e(url('mmsay-department-add-banner')); ?>"
                    class="mb-1 flex items-center gap-2 rounded-lg px-3 py-2 text-[11px] font-medium transition
                           <?php echo e($isActive('mmsay-department-add-banner*')
                               ? 'bg-indigo-50 text-indigo-600'
                               : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'); ?>">

                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    Add Banner
                </a>

                <a href="<?php echo e(url('mmsay-department-add-news')); ?>"
                    class="mb-1 flex items-center gap-2 rounded-lg px-3 py-2 text-[11px] font-medium transition
                           <?php echo e($isActive(['mmsay-department-add-news*', 'add-news*'])
                               ? 'bg-indigo-50 text-indigo-600'
                               : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'); ?>">

                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    Add News
                </a>
            </div>
        </div>
    </nav>

    
    <div class="shrink-0 border-t border-slate-100 bg-slate-50/60 p-3">

        
        <div class="mb-3 flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm">

            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 text-xs font-bold text-white shadow-sm">

                <?php echo e(strtoupper(substr(auth()->user()->name ?? 'D', 0, 1))); ?>

            </div>

            <div class="min-w-0 leading-tight">
                <p class="truncate text-xs font-semibold text-slate-800">
                    <?php echo e(auth()->user()->name ?? 'Department Admin'); ?>

                </p>

                <p class="mt-0.5 truncate text-[9px] text-slate-500">
                    MMSAY Management Portal
                </p>
            </div>
        </div>

        
        <a href="<?php echo e(route('logout')); ?>"
            class="flex h-10 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-100">

            <span class="material-symbols-outlined text-[18px]">
                logout
            </span>

            Logout
        </a>
    </div>
</aside>
<?php /**PATH D:\xampp\htdocs\housing-project\resources\views/partials/mmsay/department/sideNavBar.blade.php ENDPATH**/ ?>