<?php $__env->startSection('title', 'Verify OTP'); ?>

<?php $__env->startSection('authHeading', 'Verify OTP'); ?>
<?php $__env->startSection('authSubheading'); ?>
    OTP sent to +91 <?php echo e($mobile); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('loginForm'); ?>
<?php if($usesFixedOtp ?? false): ?>
<div class="alert alert-info py-1 px-2 small mb-2">
    Local environment: use OTP <strong>111111</strong>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('pp.department.login.verify')); ?>" id="ppDepartmentVerifyOtpForm" data-pp-loading>
    <?php echo csrf_field(); ?>
    <div class="field">
        <label class="form-label pp-auth-label" for="ppDepartmentOtpInput">Enter OTP <span class="text-danger">*</span></label>
        <input type="text" name="otp" id="ppDepartmentOtpInput" class="form-control text-center pp-otp-input <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="6-digit OTP" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
        <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <button type="submit" class="pp-auth-btn pp-auth-btn--officer">
        <i class="bi bi-shield-check me-1"></i> Verify & Sign In
    </button>
</form>

<div class="d-flex justify-content-between align-items-center small mt-2">
    <form method="POST" action="<?php echo e(route('pp.department.login.resend-otp')); ?>" class="m-0">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Resend OTP</button>
    </form>
    <a href="<?php echo e(route('pp.department.login')); ?>" class="text-muted text-decoration-none">Change mobile</a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const otpInput = document.getElementById('ppDepartmentOtpInput');
    const form = document.getElementById('ppDepartmentVerifyOtpForm');

    if (otpInput) {
        otpInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
        otpInput.focus();
    }

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const otp = otpInput ? otpInput.value.trim() : '';

        if (!otp || otp.length !== 6 || !/^\d{6}$/.test(otp)) {
            ppSwal({ icon: 'warning', title: 'Invalid OTP', text: 'OTP must be exactly 6 digits.' });
            otpInput && otpInput.focus();
            return;
        }

        document.getElementById('ppLoading')?.classList.add('show');
        form.submit();
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('physical-possession.layouts.auth-login', ['loginType' => 'officer'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/physical-possession/auth/department-otp-verify.blade.php ENDPATH**/ ?>