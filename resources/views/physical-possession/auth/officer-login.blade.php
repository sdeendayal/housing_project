@extends('physical-possession.layouts.auth-login', ['loginType' => 'officer'])

@section('title', 'District Officer Login')

@section('loginForm')
<form method="POST" action="{{ route('pp.officer.login.send-otp') }}" id="ppOfficerSendOtpForm" data-pp-loading>
    @csrf

    <div class="field">
        <label class="form-label pp-auth-label" for="ppOfficerMobileInput">Mobile number <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">+91</span>
            <input type="text" name="mobile" id="ppOfficerMobileInput" class="form-control @error('mobile') is-invalid @enderror"
                   value="{{ old('mobile') }}" placeholder="Enter 10-digit mobile" maxlength="10"
                   inputmode="numeric" autocomplete="tel">
        </div>
        @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="field">
        <label class="form-label pp-auth-label" for="ppOfficerCaptchaInput">Captcha <span class="text-danger">*</span></label>
        <div class="pp-captcha-row">
            <div class="pp-captcha-box" id="ppOfficerCaptchaText">{{ $captcha }}</div>
            <button type="button" class="btn pp-captcha-refresh" onclick="ppRefreshOfficerCaptcha(this)" title="Refresh captcha">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        <input type="text" name="captcha" id="ppOfficerCaptchaInput" class="form-control @error('captcha') is-invalid @enderror"
               placeholder="Enter captcha code" autocomplete="off">
        @error('captcha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="pp-auth-btn pp-auth-btn--officer" id="ppOfficerSendOtpBtn">
        <i class="bi bi-send me-1"></i> Send OTP
    </button>
</form>
@endsection

@push('scripts')
<script>
function ppRefreshOfficerCaptcha(btn) {
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
        document.getElementById('ppOfficerCaptchaText').textContent = data.captcha;
        document.getElementById('ppOfficerCaptchaInput').value = '';
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}

(function () {
    const mobileInput = document.getElementById('ppOfficerMobileInput');
    const captchaInput = document.getElementById('ppOfficerCaptchaInput');
    const form = document.getElementById('ppOfficerSendOtpForm');

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
