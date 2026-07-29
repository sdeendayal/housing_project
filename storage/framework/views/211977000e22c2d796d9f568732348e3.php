<header
    class="fixed left-52 right-0 top-0 z-40 flex h-16 items-center justify-between
           border-b border-white/15 bg-gradient-to-r from-indigo-600 via-blue-600 to-blue-500
           px-6 text-white shadow-sm">

    
    <div class="flex min-w-0 items-center gap-3">
        <div
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                   bg-white/15 ring-1 ring-white/15">
            <span class="material-symbols-outlined text-[25px]">
                dashboard
            </span>
        </div>

        <div class="min-w-0 leading-tight">
            <h2 class="truncate text-xl font-bold tracking-tight">
                Department Dashboard
            </h2>

            <p class="mt-0.5 truncate text-[11px] font-medium text-white/75">
                MMSAY Monitoring System
            </p>
        </div>
    </div>

    
    <div class="flex items-center gap-3">        
        

        
        <div
            class="hidden h-11 items-center gap-2 rounded-xl border border-white/20
                   bg-white/10 px-4 text-sm font-semibold backdrop-blur-sm lg:flex">

            <span class="material-symbols-outlined text-[20px] text-amber-300">
                calendar_month
            </span>

            <span><?php echo e(now()->format('d M Y')); ?></span>
        </div>

        
        <div
            class="flex h-12 cursor-pointer items-center gap-3 rounded-xl border border-white/20
                   bg-white/10 px-3 backdrop-blur-sm transition hover:bg-white/20">

            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                       bg-white text-sm font-bold text-indigo-600 shadow-sm">
                <?php echo e(strtoupper(substr(auth()->user()->name ?? 'A', 0, 1))); ?>

            </div>

            <div class="hidden min-w-0 leading-tight sm:block">
                <p class="max-w-36 truncate text-sm font-semibold">
                    <?php echo e(auth()->user()->name ?? 'Department Admin'); ?>

                </p>

                <p class="mt-0.5 text-[11px] font-medium text-white/70">
                    MMSAY Portal
                </p>
            </div>
        </div>

    </div>
</header>
<?php /**PATH D:\xampp\htdocs\housing-project\resources\views/partials/mmsay/department/topHeader.blade.php ENDPATH**/ ?>