<?php $__env->startSection('title', 'Eligible Applicants List'); ?>
<?php $__env->startSection('page-title', 'Possession Eligibility List'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card premium-card border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Eligible Applicants</h5>
                    <p class="text-muted small mb-0">Applicants whose total payments are at least ₹60,000 (auto-aggregated from cash receipts and installment ledger).</p>
                </div>
                <form action="<?php echo e(route('pp.officer.eligibility-list')); ?>" method="GET" class="d-flex gap-2 align-items-center">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Name, Mobile, Application No..." value="<?php echo e($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary px-4 btn-schedule">Search</button>
                    <?php if($search): ?>
                        <a href="<?php echo e(route('pp.officer.eligibility-list')); ?>" class="btn btn-outline-secondary btn-schedule">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 pp-eligibility-table">
                    <thead class="table-light text-uppercase text-muted">
                        <tr>
                            <th class="ps-3" style="width: 60px;">S.No.</th>
                            <th>Application No.</th>
                            <th>Applicant Details</th>
                            <th>Property Details</th>
                            <th>Total Paid</th>
                            <th>Payment Status</th>
                            <th>Possession Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $purchasers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-3 fw-semibold text-muted">
                                    <?php echo e(($purchasers->currentPage() - 1) * $purchasers->perPage() + $loop->iteration); ?>

                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-0.5"><?php echo e($p->ApplicationNo ?? '—'); ?></div>
                                    <small class="text-muted text-uppercase tracking-wider font-monospace fs-8">PPP ID: <?php echo e($p->PPPId ?? '—'); ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?php echo e($p->PrivatePurchaserName); ?></div>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i><?php echo e($p->MobileNo); ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-slate-700"><?php echo e($p->AssetName); ?></div>
                                    <small class="text-muted">Size: <?php echo e($p->AssetSize); ?> <?php echo e($p->Unit); ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-success">₹ <?php echo e(number_format($p->total_paid, 2)); ?></div>
                                    <small class="text-muted">Cost: ₹ <?php echo e(number_format($p->FlatCost, 2)); ?></small>
                                </td>
                                <td>
                                    <?php if($p->total_paid >= $p->FlatCost && $p->FlatCost > 0): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1.5 rounded-pill fs-8">
                                            <i class="bi bi-check-circle-fill me-1"></i>Fully Paid
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-20 px-2.5 py-1.5 rounded-pill fs-8">
                                            <i class="bi bi-cash-stack me-1"></i>Partially Paid
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($p->physical_possession_status): ?>
                                        <?php
                                            $badgeClass = match ($p->physical_possession_status) {
                                                'Eligible for Physical Possession' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-20',
                                                'Visit Scheduled' => 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-20',
                                                'Slot Selected' => 'bg-primary text-white border border-primary',
                                                'Physical Possession Submitted' => 'bg-primary text-white border border-primary',
                                                'Site Verified' => 'bg-info text-white border border-info shadow-sm',
                                                'Verified' => 'bg-success text-white border border-success shadow-sm',
                                                'Rejected' => 'bg-danger text-white border border-danger shadow-sm',
                                                default => 'bg-secondary text-white border border-secondary'
                                            };
                                        ?>
                                        <span class="badge <?php echo e($badgeClass); ?> px-2.5 py-1.5 rounded-3 fs-8">
                                            <?php echo e($p->physical_possession_status); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic">Not Initiated</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                     <?php if(!$p->application_secure_id): ?>
                                         <button class="btn btn-outline-secondary btn-schedule text-nowrap rounded-pill" disabled>
                                             <i class="bi bi-slash-circle me-1"></i>Not Initiated
                                         </button>
                                     <?php elseif($p->physical_possession_status === 'Eligible for Physical Possession'): ?>
                                         <a href="<?php echo e(route('pp.officer.schedule-form', $p->application_secure_id)); ?>" class="btn btn-primary btn-schedule text-nowrap rounded-pill shadow-sm">
                                             <i class="bi bi-calendar-plus me-1"></i>Schedule Visit
                                         </a>
                                     <?php elseif($p->physical_possession_status === 'Visit Scheduled'): ?>
                                         <a href="<?php echo e(route('pp.officer.schedule-form', $p->application_secure_id)); ?>" class="btn btn-outline-secondary btn-schedule text-nowrap rounded-pill">
                                             <i class="bi bi-pencil-square me-1"></i>Update Schedule
                                         </a>
                                     <?php elseif($p->physical_possession_status === 'Slot Selected'): ?>
                                         <a href="<?php echo e(route('pp.officer.verify-form', $p->application_secure_id)); ?>" class="btn btn-success btn-schedule text-nowrap rounded-pill text-white shadow-sm">
                                             <i class="bi bi-shield-check me-1"></i>Perform Visit
                                         </a>
                                     <?php elseif($p->physical_possession_status === 'Site Verified'): ?>
                                         <a href="<?php echo e(route('pp.officer.verify-form', $p->application_secure_id)); ?>" class="btn btn-info btn-schedule text-nowrap rounded-pill text-white shadow-sm">
                                             <i class="bi bi-file-earmark-arrow-up me-1"></i>E-Verify
                                         </a>
                                     <?php else: ?>
                                         <a href="<?php echo e(route('pp.officer.verify-form', $p->application_secure_id)); ?>" class="btn btn-outline-secondary btn-schedule text-nowrap rounded-pill">
                                             <i class="bi bi-eye me-1"></i>View Details
                                         </a>
                                     <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 mb-3 d-block text-slate-300"></i>
                                    No eligible applicants found in your district.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($purchasers->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($purchasers->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .premium-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
    }
    .pp-eligibility-table th {
        font-size: 0.68rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.3px !important;
        padding: 8px 8px !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .pp-eligibility-table td {
        font-size: 0.72rem !important;
        padding: 8px 8px !important;
        vertical-align: middle !important;
    }
    .pp-eligibility-table tr:hover {
        background-color: rgba(30, 64, 175, 0.02) !important;
    }
    .btn-schedule {
        font-size: 0.68rem !important;
        padding: 4px 10px !important;
        font-weight: 600 !important;
        letter-spacing: 0.2px;
        transition: all 0.2s ease;
    }
    .btn-schedule:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(30, 64, 175, 0.15);
    }
    /* Pagination responsiveness */
    .pagination {
        margin-bottom: 0 !important;
        gap: 2px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .page-link {
        font-size: 0.75rem !important;
        padding: 5px 10px !important;
        border-radius: 6px !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
    }
    .page-item.active .page-link {
        background-color: var(--pp-primary) !important;
        border-color: var(--pp-primary) !important;
        color: #fff !important;
    }
    .page-link:hover {
        background-color: #f1f5f9 !important;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('physical-possession.layouts.officer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/physical-possession/workflow/officer-eligibility.blade.php ENDPATH**/ ?>