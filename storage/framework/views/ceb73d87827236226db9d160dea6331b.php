<?php $__env->startSection('title', 'Department Officer Login'); ?>

<?php $__env->startSection('authHeading', 'Department Officer Login'); ?>
<?php $__env->startSection('authSubheading', 'Department officers only — mobile & captcha to receive OTP'); ?>

<?php $__env->startSection('loginForm'); ?>
<form method="POST" action="<?php echo e(route('pp.department.login.send-otp')); ?>" id="ppDepartmentSendOtpForm" data-pp-loading>
    <?php echo csrf_field(); ?>

    <div class="field">
        <label class="form-label pp-auth-label" for="ppDepartmentMobileInput">Mobile number <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">+91</span>
            <input type="text" name="mobile" id="ppDepartmentMobileInput" class="form-control <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   value="<?php echo e(old('mobile', '9999900278')); ?>" placeholder="Enter 10-digit mobile" maxlength="10"
                   inputmode="numeric" autocomplete="tel">
        </div>
        <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="field">
        <label class="form-label pp-auth-label" for="ppDepartmentCaptchaInput">Captcha <span class="text-danger">*</span></label>
        <div class="pp-captcha-row">
            <div class="pp-captcha-box" id="ppDepartmentCaptchaText"><?php echo e($captcha); ?></div>
            <button type="button" class="btn pp-captcha-refresh" onclick="ppRefreshDepartmentCaptcha(this)" title="Refresh captcha">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        <input type="text" name="captcha" id="ppDepartmentCaptchaInput" class="form-control <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Enter captcha code" autocomplete="off">
        <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <button type="submit" class="pp-auth-btn pp-auth-btn--officer" id="ppDepartmentSendOtpBtn">
        <i class="bi bi-send me-1"></i> Send OTP
    </button>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function ppRefreshDepartmentCaptcha(btn) {
    btn.disabled = true;
    fetch('<?php echo e(url('/refresh-captcha')); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('ppDepartmentCaptchaText').textContent = data.captcha;
        document.getElementById('ppDepartmentCaptchaInput').value = '';
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}

(function () {
    const mobileInput = document.getElementById('ppDepartmentMobileInput');
    const captchaInput = document.getElementById('ppDepartmentCaptchaInput');
    const form = document.getElementById('ppDepartmentSendOtpForm');

    if (mobileInput) {
        mobileInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    }

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const mobile = mobileInput ? mobileInput.value.trim() : '';
        const captcha = captchaInput ? captchaInput.value.trim() : '';

        if (!mobile || mobile.length !== 10 || !/^[6-9]\d{9}$/.test(mobile)) {
            ppSwal({ icon: 'warning', title: 'Invalid Mobile', text: 'Enter a valid 10-digit Indian mobile number.' });
            mobileInput && mobileInput.focus();
            return;
        }

        if (!captcha) {
            ppSwal({ icon: 'warning', title: 'Captcha Required', text: 'Please enter the captcha code.' });
            captchaInput && captchaInput.focus();
            return;
        }

        document.getElementById('ppLoading')?.classList.add('show');
        form.submit();
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('physical-possession.layouts.auth-login', ['loginType' => 'officer'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/physical-possession/auth/department-login.blade.php ENDPATH**/ ?>