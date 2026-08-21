@extends('physical-possession.layouts.auth-login', ['loginType' => 'officer'])

@section('title', 'DTP Officer Login')

@section('authHeading', 'DTP Officer Login')
@section('authSubheading', 'MMGAY District Town Planners — email & password login')

@section('loginForm')
<form method="POST" action="{{ route('pp.dtp.login.submit') }}" id="ppDtpLoginForm" data-pp-loading>
    @csrf

    <div class="field">
        <label class="form-label pp-auth-label" for="ppDtpEmailInput">Email Address <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="ppDtpEmailInput" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', 'dtp@gmail.com') }}" placeholder="Enter DTP email address" required>
        </div>
        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="field">
        <label class="form-label pp-auth-label" for="ppDtpPasswordInput">Password <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="ppDtpPasswordInput" class="form-control @error('password') is-invalid @enderror"
                   value="password" placeholder="Enter password" required>
        </div>
        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="field">
        <label class="form-label pp-auth-label" for="ppDtpCaptchaInput">Captcha <span class="text-danger">*</span></label>
        <div class="pp-captcha-row">
            <div class="pp-captcha-box" id="ppDtpCaptchaText">{{ $captcha }}</div>
            <button type="button" class="btn pp-captcha-refresh" onclick="ppRefreshDtpCaptcha(this)" title="Refresh captcha">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        <input type="text" name="captcha" id="ppDtpCaptchaInput" class="form-control @error('captcha') is-invalid @enderror"
               placeholder="Enter captcha code" autocomplete="off" required>
        @error('captcha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="pp-auth-btn pp-auth-btn--officer" id="ppDtpLoginBtn">
        <i class="bi bi-box-arrow-in-right me-1"></i> Log In
    </button>
</form>
@endsection

@push('scripts')
<script>
function ppRefreshDtpCaptcha(btn) {
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
        document.getElementById('ppDtpCaptchaText').textContent = data.captcha;
        document.getElementById('ppDtpCaptchaInput').value = '';
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}

(function () {
    const emailInput = document.getElementById('ppDtpEmailInput');
    const passwordInput = document.getElementById('ppDtpPasswordInput');
    const captchaInput = document.getElementById('ppDtpCaptchaInput');
    const form = document.getElementById('ppDtpLoginForm');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const email = emailInput ? emailInput.value.trim() : '';
        const password = passwordInput ? passwordInput.value.trim() : '';
        const captcha = captchaInput ? captchaInput.value.trim() : '';

        if (!email) {
            ppSwal({ icon: 'warning', title: 'Email Required', text: 'Please enter your email address.' });
            emailInput && emailInput.focus();
            return;
        }

        if (!password) {
            ppSwal({ icon: 'warning', title: 'Password Required', text: 'Please enter your account password.' });
            passwordInput && passwordInput.focus();
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
