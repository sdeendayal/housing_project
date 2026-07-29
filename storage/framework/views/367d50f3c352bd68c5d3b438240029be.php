<?php $__env->startSection('bodyClass', 'pp-body-auth'); ?>

<?php
    $loginType = $loginType ?? 'user';
    $isOfficer = $loginType === 'officer';
    $bgImage = asset('images/citizen-login/sehri_bg.png');
?>

<?php $__env->startSection('content'); ?>
<div class="pp-auth-page">
    <div class="pp-auth-bg" aria-hidden="true">
        <img src="<?php echo e($bgImage); ?>" alt="">
    </div>

    <div class="pp-auth-inner">
        <div class="pp-auth-card <?php echo e($isOfficer ? 'pp-auth-card--officer' : ''); ?>">
            <header class="pp-auth-card__head">
                <div class="pp-auth-card__logo">
                    <img src="<?php echo e(asset('Haryana_emblem.png')); ?>" alt="Government of Haryana">
                </div>
                <p class="pp-auth-card__dept mb-0">Government of Haryana</p>
                <p class="pp-auth-card__scheme mb-0">Housing For All — Physical Possession Portal</p>
                <?php if (! ($isOfficer)): ?>
                    <span class="pp-auth-chip">New Portal</span>
                <?php endif; ?>
                <h1 class="pp-auth-card__title">
                    <?php echo $__env->yieldContent('authHeading', $isOfficer ? 'Officer Login' : 'User Login'); ?>
                </h1>
                <p class="pp-auth-card__hint mb-0">
                    <?php echo $__env->yieldContent('authSubheading', $isOfficer ? 'Department officer mobile & captcha to receive OTP' : 'Mobile number & captcha to receive OTP'); ?>
                </p>
            </header>

            <div class="pp-auth-card__body">
                <?php echo $__env->yieldContent('loginForm'); ?>
            </div>

            <footer class="pp-auth-card__foot">
                <div class="pp-auth-features">
                    <span><i class="bi bi-shield-check"></i> Secure</span>
                    <span><i class="bi bi-cloud-upload"></i> Upload</span>
                    <span><i class="bi bi-graph-up"></i> Track</span>
                </div>
                <a href="<?php echo e(route('pp.landing')); ?>" class="pp-auth-back">
                    <i class="bi bi-arrow-left"></i> Back to Home
                </a>
            </footer>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php echo $__env->make('physical-possession.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/physical-possession/layouts/auth-login.blade.php ENDPATH**/ ?>