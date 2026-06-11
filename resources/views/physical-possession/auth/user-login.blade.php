@extends('physical-possession.layouts.auth-login', ['loginType' => 'user'])

@section('title', 'Login')

@section('authHeading', 'Login')
@section('authSubheading', 'Citizen or District Officer — mobile & captcha to receive OTP')

@section('loginForm')
<form method="POST" action="{{ route('pp.user.login.send-otp') }}" id="ppSendOtpForm" data-pp-loading>
    @csrf

    <div class="field">
        <label class="form-label pp-auth-label" for="ppMobileInput">Mobile number <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">+91</span>
            <input type="text" name="mobile" id="ppMobileInput" class="form-control @error('mobile') is-invalid @enderror"
                   value="{{ old('mobile') }}" placeholder="Enter 10-digit mobile" maxlength="10"
                   inputmode="numeric" autocomplete="tel">
        </div>
        @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="field">
        <label class="form-label pp-auth-label" for="ppCaptchaInput">Captcha <span class="text-danger">*</span></label>
        <div class="pp-captcha-row">
            <div class="pp-captcha-box" id="ppCaptchaText">{{ $captcha }}</div>
            <button type="button" class="btn pp-captcha-refresh" onclick="ppRefreshCaptcha(this)" title="Refresh captcha">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        <input type="text" name="captcha" id="ppCaptchaInput" class="form-control @error('captcha') is-invalid @enderror"
               placeholder="Enter captcha code" autocomplete="off">
        @error('captcha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="pp-auth-btn" id="ppSendOtpBtn">
        <i class="bi bi-send me-1"></i> Send OTP
    </button>
</form>
@endsection

@push('scripts')
<script>
function ppRefreshCaptcha(btn) {
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
        document.getElementById('ppCaptchaText').textContent = data.captcha;
        document.getElementById('ppCaptchaInput').value = '';
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}

(function () {
    const mobileInput = document.getElementById('ppMobileInput');
    const captchaInput = document.getElementById('ppCaptchaInput');
    const form = document.getElementById('ppSendOtpForm');
    const mobilePattern = /^[6-9]\d{9}$/;

    function sanitizeMobile(value) {
        let digits = String(value || '').replace(/\D/g, '');
        if (digits.length > 10) digits = digits.slice(-10);
        return digits;
    }

    if (mobileInput) {
        mobileInput.addEventListener('input', function () {
            this.value = sanitizeMobile(this.value);
        });
        mobileInput.addEventListener('paste', function (e) {
            e.preventDefault();
            this.value = sanitizeMobile((e.clipboardData || window.clipboardData).getData('text'));
        });
    }

    if (captchaInput) {
        captchaInput.addEventListener('input', function () {
            this.value = this.value.replace(/\s/g, '');
        });
    }

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const mobile = sanitizeMobile(mobileInput ? mobileInput.value : '');
        const captcha = captchaInput ? captchaInput.value.trim() : '';

        if (mobileInput) mobileInput.value = mobile;

        if (!mobile) {
            ppSwal({ icon: 'warning', title: 'Mobile Required', text: 'Please enter your 10-digit mobile number.' });
            mobileInput && mobileInput.focus();
            return;
        }

        if (mobile.length !== 10) {
            ppSwal({ icon: 'warning', title: 'Invalid Mobile', text: 'Mobile number must be exactly 10 digits.' });
            mobileInput && mobileInput.focus();
            return;
        }

        if (!mobilePattern.test(mobile)) {
            ppSwal({ icon: 'warning', title: 'Invalid Mobile', text: 'Enter a valid Indian mobile number starting with 6, 7, 8, or 9.' });
            mobileInput && mobileInput.focus();
            return;
        }

        if (!captcha) {
            ppSwal({ icon: 'warning', title: 'Captcha Required', text: 'Please enter the captcha shown above.' });
            captchaInput && captchaInput.focus();
            return;
        }

        if (!/^\d+$/.test(captcha)) {
            ppSwal({ icon: 'warning', title: 'Invalid Captcha', text: 'Captcha must contain numbers only.' });
            captchaInput && captchaInput.focus();
            return;
        }

        document.getElementById('ppLoading')?.classList.add('show');
        form.submit();
    });
})();
</script>
@endpush
