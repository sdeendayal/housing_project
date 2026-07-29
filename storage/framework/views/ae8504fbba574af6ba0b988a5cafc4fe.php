<?php if($paginator->hasPages()): ?>
    <?php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        
        // Show active page, 1 page on left, and 1 page on right
        $start = max($currentPage - 1, 1);
        $end = min($currentPage + 1, $lastPage);
        
        // Adjust range at edges to show 3 pages if possible
        if ($currentPage == 1) {
            $end = min(3, $lastPage);
        } elseif ($currentPage == $lastPage) {
            $start = max($lastPage - 2, 1);
        }
    ?>

    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col items-center justify-center gap-2 py-2 select-none">
        <!-- Text details: Showing X to Y of Z results -->
        <div class="text-[11px] text-slate-500 font-semibold text-center">
            Showing
            <span class="font-bold text-slate-800"><?php echo e($paginator->firstItem()); ?></span>
            to
            <span class="font-bold text-slate-800"><?php echo e($paginator->lastItem()); ?></span>
            of
            <span class="font-bold text-slate-800"><?php echo e($paginator->total()); ?></span>
            results
        </div>

        <!-- Controls: [ < ] [ 1 ] ... [ 5 ] [ 6 ] [ 7 ] ... [ 13 ] [ > ] -->
        <div class="inline-flex items-center gap-1">
            
            <?php if($paginator->onFirstPage()): ?>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 bg-slate-100 cursor-not-allowed border border-slate-200">
                    <span class="material-symbols-outlined text-[15px] font-black">chevron_left</span>
                </span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" 
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                   title="Previous Page">
                    <span class="material-symbols-outlined text-[15px] font-black">chevron_left</span>
                </a>
            <?php endif; ?>

            
            <?php if($start > 1): ?>
                <a href="<?php echo e($paginator->url(1)); ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold transition">1</a>
                <?php if($start > 2): ?>
                    <span class="text-slate-400 px-0.5 text-xs font-black">..</span>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php for($i = $start; $i <= $end; $i++): ?>
                <?php if($i == $currentPage): ?>
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-xs font-extrabold shadow-sm border border-blue-600">
                        <?php echo e($i); ?>

                    </span>
                <?php else: ?>
                    <a href="<?php echo e($paginator->url($i)); ?>" 
                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold transition">
                        <?php echo e($i); ?>

                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            
            <?php if($end < $lastPage): ?>
                <?php if($end < $lastPage - 1): ?>
                    <span class="text-slate-400 px-0.5 text-xs font-black">..</span>
                <?php endif; ?>
                <a href="<?php echo e($paginator->url($lastPage)); ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold transition"><?php echo e($lastPage); ?></a>
            <?php endif; ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" 
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                   title="Next Page">
                    <span class="material-symbols-outlined text-[15px] font-black">chevron_right</span>
                </a>
            <?php else: ?>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 bg-slate-100 cursor-not-allowed border border-slate-200">
                    <span class="material-symbols-outlined text-[15px] font-black">chevron_right</span>
                </span>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\housing-project\resources\views/partials/compact-pagination.blade.php ENDPATH**/ ?>