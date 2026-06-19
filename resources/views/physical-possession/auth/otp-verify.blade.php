@extends('physical-possession.layouts.auth-login', ['loginType' => 'user'])

@section('title', 'Verify OTP')

@section('authHeading', 'Verify OTP')
@section('authSubheading')
    OTP sent to +91 {{ $mobile }}
@endsection

@section('loginForm')
@if(\App\Services\OtpVerificationService::usesFixedTestOtp('', ''))
<div class="alert alert-info py-1 px-2 small mb-2">
    Local environment: use OTP <strong>111111</strong>
</div>
@endif

<form method="POST" action="{{ route('pp.user.login.verify') }}" id="ppVerifyOtpForm" data-pp-loading>
    @csrf
    <div class="field">
        <label class="form-label pp-auth-label" for="ppOtpInput">Enter OTP <span class="text-danger">*</span></label>
        <input type="text" name="otp" id="ppOtpInput" class="form-control text-center pp-otp-input @error('otp') is-invalid @enderror"
               placeholder="6-digit OTP" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
        @error('otp')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="pp-auth-btn">
        <i class="bi bi-shield-check me-1"></i> Verify & Sign In
    </button>
</form>

<div class="d-flex justify-content-between align-items-center small mt-2">
    <form method="POST" action="{{ route('pp.user.login.resend-otp') }}" class="m-0">
        @csrf
        <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Resend OTP</button>
    </form>
    <a href="{{ route('pp.user.login') }}" class="text-muted text-decoration-none">Change mobile</a>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const otpInput = document.getElementById('ppOtpInput');
    const form = document.getElementById('ppVerifyOtpForm');

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

        if (!otp) {
            ppSwal({ icon: 'warning', title: 'OTP Required', text: 'Please enter the 6-digit OTP sent to your mobile.' });
            otpInput && otpInput.focus();
            return;
        }

        if (otp.length !== 6) {
            ppSwal({ icon: 'warning', title: 'Invalid OTP', text: 'OTP must be exactly 6 digits.' });
            otpInput && otpInput.focus();
            return;
        }

        if (!/^\d{6}$/.test(otp)) {
            ppSwal({ icon: 'warning', title: 'Invalid OTP', text: 'OTP must contain numbers only.' });
            otpInput && otpInput.focus();
            return;
        }

        document.getElementById('ppLoading')?.classList.add('show');
        form.submit();
    });
})();
</script>
@endpush
