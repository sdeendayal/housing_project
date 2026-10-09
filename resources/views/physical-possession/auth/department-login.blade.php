@extends('physical-possession.layouts.auth-login', ['loginType' => 'officer'])

@section('title', 'Department Officer Login')

@section('authHeading', 'Department Officer Login')
@section('authSubheading', 'Department officers only — mobile & captcha to receive OTP')

@section('loginForm')
<form method="POST" action="{{ route('pp.department.login.send-otp') }}" id="ppDepartmentSendOtpForm" data-pp-loading>
    @csrf

    <div class="field">
        <label class="form-label pp-auth-label" for="ppDepartmentMobileInput">Mobile number <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">+91</span>
            <input type="text" name="mobile" id="ppDepartmentMobileInput" class="form-control @error('mobile') is-invalid @enderror"
                   value="{{ old('mobile') }}" placeholder="Enter 10-digit mobile" maxlength="10"
                   inputmode="numeric" autocomplete="tel">
        </div>
        @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="field">
        <label class="form-label pp-auth-label" for="ppDepartmentCaptchaInput">Captcha <span class="text-danger">*</span></label>
        <div class="pp-captcha-row">
            <div class="pp-captcha-box" id="ppDepartmentCaptchaText">{{ $captcha }}</div>
            <button type="button" class="btn pp-captcha-refresh" onclick="ppRefreshDepartmentCaptcha(this)" title="Refresh captcha">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        <input type="text" name="captcha" id="ppDepartmentCaptchaInput" class="form-control @error('captcha') is-invalid @enderror"
               placeholder="Enter captcha code" autocomplete="off">
        @error('captcha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="pp-auth-btn pp-auth-btn--officer" id="ppDepartmentSendOtpBtn">
        <i class="bi bi-send me-1"></i> Send OTP
    </button>
</form>
@endsection

@push('scripts')
<script>
function ppRefreshDepartmentCaptcha(btn) {
    btn.disabled = true;
    fetch('{{ url('/refresh-captcha') }}', {
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
@endpush
