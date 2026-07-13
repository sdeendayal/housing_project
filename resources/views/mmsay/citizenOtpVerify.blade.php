<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@extends('layouts.auth')
@section('title', 'Verify OTP')
@section('content')

<style>
   body.page-citizen-login {
      font-family: 'Inter', system-ui, sans-serif;
   }
   body.page-citizen-login header .max-w-7xl {
      padding-top: 0.375rem !important;
      padding-bottom: 0.375rem !important;
   }
   body.page-citizen-login header img {
      height: 2.25rem !important;
      width: 2.25rem !important;
   }
   body.page-citizen-login header h1 {
      font-size: 0.9375rem !important;
      font-weight: 600 !important;
      line-height: 1.2 !important;
   }
   body.page-citizen-login header p {
      font-size: 0.6875rem !important;
   }
   body.page-citizen-login main.flex-grow {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 0;
   }
   body.page-citizen-login footer {
      padding-top: 0.5rem !important;
      padding-bottom: 0.5rem !important;
   }
   body.page-citizen-login footer .flex.flex-col {
      gap: 0.25rem !important;
   }
   body.page-citizen-login footer p.text-base {
      font-size: 0.75rem !important;
   }
   body.page-citizen-login footer p.text-sm {
      font-size: 0.6875rem !important;
      margin-top: 0.125rem !important;
   }
   body.page-citizen-login footer .flex.justify-center.items-center.gap-6.mt-4 {
      display: none !important;
   }

   .cl-page {
      flex: 1;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.75rem 1rem;
      background:
         linear-gradient(rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.65)),
         url('{{ asset('images/citizen-login/sehri_bg.png') }}') center / cover no-repeat;
   }
   @media (min-width: 768px) {
      .cl-page {
         justify-content: flex-start;
         padding-left: 8%;
      }
   }
   .cl-shell {
      width: 100%;
      max-width: 22rem;
      margin: 0 auto;
   }
   @media (min-width: 768px) {
      .cl-shell {
         margin: 0;
      }
   }
   .cl-card {
      background: #fff;
      border-radius: 0.75rem;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.18);
      overflow: hidden;
   }
   .cl-brand {
      display: flex;
      align-items: center;
      gap: 0.625rem;
      padding: 0.75rem 1rem;
      background: linear-gradient(135deg, #1e40af 0%, #0f172a 100%);
      color: #fff;
   }
   .cl-brand__icon {
      width: 2rem;
      height: 2rem;
      border-radius: 0.5rem;
      background: rgba(255, 255, 255, 0.15);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
   }
   .cl-brand__icon .material-symbols-outlined {
      font-size: 1.125rem;
   }
   .cl-brand__title {
      font-size: 0.875rem;
      font-weight: 600;
      line-height: 1.2;
      margin: 0;
   }
   .cl-brand__sub {
      font-size: 0.6875rem;
      opacity: 0.85;
      margin: 0.125rem 0 0;
   }
   .cl-body {
      padding: 1rem;
   }
   .cl-head {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.875rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid #eef2f6;
   }
   .cl-head__icon {
      width: 2rem;
      height: 2rem;
      border-radius: 0.5rem;
      background: #eff6ff;
      border: 1px solid #dbeafe;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
   }
   .cl-head__icon .material-symbols-outlined {
      font-size: 1.125rem;
      color: #1e40af;
   }
   .cl-head__title {
      font-size: 0.9375rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      line-height: 1.2;
   }
   .cl-head__sub {
      font-size: 0.6875rem;
      color: #64748b;
      margin: 0.125rem 0 0;
   }
   .cl-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 0.375rem;
      margin-bottom: 0.875rem;
   }
   .cl-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.625rem;
      font-weight: 500;
      color: #475569;
      background: #f1f5f9;
      border: 1px solid #e2e8f0;
      border-radius: 999px;
      padding: 0.2rem 0.5rem;
   }
   .cl-tag .material-symbols-outlined {
      font-size: 0.75rem;
      color: #2563eb;
   }
   .cl-field {
      margin-bottom: 0.625rem;
   }
   .cl-label {
      display: block;
      font-size: 0.75rem;
      font-weight: 600;
      color: #334155;
      margin-bottom: 0.25rem;
   }
   .cl-label .req {
      color: #dc2626;
   }
   .cl-mobile {
      display: flex;
      height: 2.25rem;
      border: 1px solid #cbd5e1;
      border-radius: 0.5rem;
      overflow: hidden;
      background: #f8fafc;
      transition: border-color 0.2s, box-shadow 0.2s;
   }
   .cl-mobile:focus-within {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
      background: #fff;
   }
   .cl-mobile--readonly {
      background: #f1f5f9;
      opacity: 0.95;
   }
   .cl-mobile--readonly:focus-within {
      border-color: #cbd5e1;
      box-shadow: none;
      background: #f1f5f9;
   }
   .cl-mobile__code {
      width: 2.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 600;
      color: #0f172a;
      background: #e2e8f0;
      flex-shrink: 0;
   }
   .cl-mobile__input,
   .cl-otp__input {
      flex: 1;
      min-width: 0;
      border: none;
      background: transparent;
      padding: 0 0.625rem;
      font-size: 0.8125rem;
      outline: none;
      height: 100%;
   }
   .cl-otp__input {
      letter-spacing: 0.35rem;
      font-weight: 600;
      text-align: center;
   }
   .cl-otp {
      display: flex;
      height: 2.25rem;
      border: 1px solid #cbd5e1;
      border-radius: 0.5rem;
      overflow: hidden;
      background: #f8fafc;
      transition: border-color 0.2s, box-shadow 0.2s;
   }
   .cl-otp:focus-within {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
      background: #fff;
   }
   .cl-btn {
      width: 100%;
      height: 2.375rem;
      margin-top: 0.25rem;
      border: none;
      border-radius: 0.5rem;
      background: #2c3c6b;
      color: #fff;
      font-size: 0.8125rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.375rem;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 14px rgba(44, 60, 107, 0.28);
   }
   .cl-btn:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(44, 60, 107, 0.35);
   }
   .cl-btn:disabled,
   .cl-btn.is-loading {
      opacity: 0.9;
      pointer-events: none;
      transform: none;
   }
   .cl-btn .btn-label-loading {
      display: none;
      align-items: center;
      gap: 0.5rem;
   }
   .cl-btn.is-loading .btn-label-default {
      display: none;
   }
   .cl-btn.is-loading .btn-label-loading {
      display: inline-flex;
   }
   .cl-btn .btn-spinner {
      width: 1rem;
      height: 1rem;
      border: 2px solid rgba(255, 255, 255, 0.35);
      border-top-color: #fff;
      border-radius: 50%;
      animation: cl-spin 0.7s linear infinite;
   }
   .cl-btn .material-symbols-outlined {
      font-size: 1rem;
   }
   .cl-btn-resend {
      width: 100%;
      height: 2.25rem;
      margin-top: 0.5rem;
      border: none;
      border-radius: 0.5rem;
      background: linear-gradient(135deg, #16a34a, #15803d);
      color: #fff;
      font-size: 0.8125rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.375rem;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
   }
   .cl-btn-resend:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(22, 163, 74, 0.32);
   }
   .cl-btn-resend:disabled,
   .cl-btn-resend.is-loading {
      opacity: 0.92;
      pointer-events: none;
      transform: none;
   }
   .cl-btn-resend .btn-label-loading {
      display: none;
      align-items: center;
      gap: 0.5rem;
   }
   .cl-btn-resend.is-loading .btn-label-default {
      display: none;
   }
   .cl-btn-resend.is-loading .btn-label-loading {
      display: inline-flex;
   }
   .cl-btn-resend .btn-spinner {
      width: 1rem;
      height: 1rem;
      border: 2px solid rgba(255, 255, 255, 0.35);
      border-top-color: #fff;
      border-radius: 50%;
      animation: cl-spin 0.7s linear infinite;
   }
   .cl-link-wrap {
      margin-top: 0.75rem;
      text-align: center;
   }
   .cl-link {
      font-size: 0.75rem;
      font-weight: 500;
      color: #2563eb;
      text-decoration: none;
   }
   .cl-link:hover {
      color: #1d4ed8;
      text-decoration: underline;
   }
   @keyframes cl-spin {
      to { transform: rotate(360deg); }
   }
   .citizen-page-loader {
      position: fixed;
      inset: 0;
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
   }
   .citizen-page-loader[hidden] {
      display: none !important;
   }
   .citizen-page-loader__backdrop {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, 0.45);
      backdrop-filter: blur(3px);
   }
   .citizen-page-loader__panel {
      position: relative;
      z-index: 1;
      background: #fff;
      border-radius: 0.625rem;
      padding: 1rem 1.25rem;
      text-align: center;
      box-shadow: 0 12px 32px rgba(15, 23, 42, 0.18);
      min-width: 10rem;
   }
   .citizen-page-loader__spinner {
      width: 1.75rem;
      height: 1.75rem;
      margin: 0 auto 0.5rem;
      border: 2px solid #e2e8f0;
      border-top-color: #2c3c6b;
      border-radius: 50%;
      animation: cl-spin 0.8s linear infinite;
   }
   .citizen-page-loader__text {
      margin: 0;
      font-size: 0.75rem;
      font-weight: 600;
      color: #0f172a;
   }
   @media (min-width: 576px) {
      .cl-shell {
         max-width: 24rem;
      }
   }
</style>

<script>document.body.classList.add('page-citizen-login');</script>

<div class="cl-page">
   <div class="cl-shell">
      <div class="cl-card">
         <div class="cl-brand">
            <div class="cl-brand__icon">
               <span class="material-symbols-outlined">apartment</span>
            </div>
            <div>
               <p class="cl-brand__title">Housing For All</p>
               <p class="cl-brand__sub">Citizen Portal</p>
            </div>
         </div>

         <div class="cl-body">
            <div class="cl-head">
               <div class="cl-head__icon">
                  <span class="material-symbols-outlined">lock</span>
               </div>
               <div>
                  <p class="cl-head__title">Verify OTP</p>
                  <p class="cl-head__sub">Enter the 6-digit OTP sent to your mobile</p>
               </div>
            </div>

            <div class="cl-tags">
               <span class="cl-tag"><span class="material-symbols-outlined">sms</span> OTP Verification</span>
               <span class="cl-tag"><span class="material-symbols-outlined">schedule</span> Valid 10 min</span>
            </div>

            @if($usesFixedOtp ?? false)
            <div class="alert alert-info py-1 px-2 small mb-2">
               Test / local account: use OTP <strong>111111</strong>
            </div>
            @endif

            <div class="cl-field">
               <label class="cl-label">Mobile Number <span class="req">*</span></label>
               <div class="cl-mobile cl-mobile--readonly">
                  <span class="cl-mobile__code">+91</span>
                  <input
                     type="text"
                     class="cl-mobile__input"
                     value="{{ $mobile }}"
                     readonly
                     disabled
                     aria-readonly="true">
               </div>
            </div>

            <form id="verifyOtpForm" action="{{ route('citizen.login.verify') }}" method="POST">
               @csrf

               <div class="cl-field">
                  <label class="cl-label" for="otpInput">OTP <span class="req">*</span></label>
                  <div class="cl-otp">
                     <input
                        type="text"
                        class="cl-otp__input"
                        id="otpInput"
                        name="otp"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="······"
                        maxlength="6"
                        value="{{ old('otp') }}"
                        required
                        autofocus>
                  </div>
               </div>

               <button type="submit" class="cl-btn" id="verifyOtpBtn">
                  <span class="btn-label-default">
                     Verify OTP
                     <span class="material-symbols-outlined">arrow_forward</span>
                  </span>
                  <span class="btn-label-loading">
                     <span class="btn-spinner" aria-hidden="true"></span>
                     Verifying...
                  </span>
               </button>
            </form>

            <form id="resendOtpForm" action="{{ route('citizen.login.resend-otp') }}" method="POST">
               @csrf
               <button type="submit" class="cl-btn-resend" id="resendOtpBtn">
                  <span class="btn-label-default">Resend OTP</span>
                  <span class="btn-label-loading">
                     <span class="btn-spinner" aria-hidden="true"></span>
                     Sending OTP...
                  </span>
               </button>
            </form>

            <p class="cl-link-wrap">
               <a href="{{ route('citizen.login') }}" class="cl-link">Change mobile number</a>
            </p>
         </div>
      </div>
   </div>
</div>

<div id="citizenPageLoader" class="citizen-page-loader" hidden aria-live="polite" aria-busy="true">
   <div class="citizen-page-loader__backdrop"></div>
   <div class="citizen-page-loader__panel">
      <div class="citizen-page-loader__spinner" aria-hidden="true"></div>
      <p class="citizen-page-loader__text" id="citizenPageLoaderText">Please wait...</p>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
   var form = document.getElementById('verifyOtpForm');
   var btn = document.getElementById('verifyOtpBtn');
   var resendForm = document.getElementById('resendOtpForm');
   var resendBtn = document.getElementById('resendOtpBtn');
   var loader = document.getElementById('citizenPageLoader');
   var loaderText = document.getElementById('citizenPageLoaderText');
   var otpInput = document.getElementById('otpInput');

   function showPageLoader(message) {
      if (!loader) {
         return;
      }
      if (loaderText) {
         loaderText.textContent = message;
      }
      loader.removeAttribute('hidden');
      document.body.style.overflow = 'hidden';
   }

   function hidePageLoader() {
      if (!loader) {
         return;
      }
      loader.setAttribute('hidden', '');
      document.body.style.overflow = '';
   }

   function resetResendButton() {
      if (resendBtn) {
         resendBtn.classList.remove('is-loading');
         resendBtn.disabled = false;
      }
      if (btn) {
         btn.classList.remove('is-loading');
         btn.disabled = false;
      }
      hidePageLoader();
   }

   function sanitizeOtp(value) {
      var digits = String(value || '').replace(/\D/g, '');
      if (digits.length > 6) {
         digits = digits.slice(0, 6);
      }
      return digits;
   }

   function showValidationAlert(title, text) {
      if (typeof Swal === 'undefined') {
         alert(text);
         return;
      }
      Swal.fire({
         icon: 'warning',
         title: title,
         text: text,
         confirmButtonColor: '#2c3c6b',
      });
   }

   function showErrorAlert(title, text) {
      if (typeof Swal === 'undefined') {
         alert(text);
         return;
      }
      Swal.fire({
         icon: 'error',
         title: title,
         text: text,
         confirmButtonColor: '#2c3c6b',
      });
   }

   if (otpInput) {
      otpInput.addEventListener('input', function () {
         var cleaned = sanitizeOtp(otpInput.value);
         if (otpInput.value !== cleaned) {
            otpInput.value = cleaned;
         }
      });

      otpInput.addEventListener('paste', function (e) {
         e.preventDefault();
         var pasted = (e.clipboardData || window.clipboardData).getData('text');
         otpInput.value = sanitizeOtp(pasted);
      });

      if (otpInput.value) {
         otpInput.value = sanitizeOtp(otpInput.value);
      }
   }

   if (form && btn && loader) {
      form.addEventListener('submit', function (e) {
         e.preventDefault();

         var otp = sanitizeOtp(otpInput ? otpInput.value : '');
         if (otpInput) {
            otpInput.value = otp;
         }

         if (!otp) {
            showValidationAlert('OTP Required', 'Please enter the 6-digit OTP sent to your mobile.');
            otpInput && otpInput.focus();
            return;
         }

         if (otp.length !== 6) {
            showValidationAlert('Invalid OTP', 'OTP must be exactly 6 digits (numbers only).');
            otpInput && otpInput.focus();
            return;
         }

         btn.classList.add('is-loading');
         btn.disabled = true;
         showPageLoader('Verifying OTP, please wait...');
         form.submit();
      });
   }

   if (resendForm && resendBtn) {
      resendForm.addEventListener('submit', function () {
         if (resendBtn.classList.contains('is-loading')) {
            return;
         }

         resendBtn.classList.add('is-loading');
         resendBtn.disabled = true;
         showPageLoader('Sending OTP to your mobile, please wait...');
         resendForm.submit();
      });
   }

   @if ($errors->any())
      resetResendButton();
      showErrorAlert('Validation Error', @json($errors->first()));
   @elseif (session('warning'))
      resetResendButton();
      showValidationAlert('Warning', @json(session('warning')));
   @elseif (session('error'))
      resetResendButton();
      showErrorAlert('Verification Failed', @json(session('error')));
   @elseif (session('success'))
      resetResendButton();
      if (typeof Swal !== 'undefined') {
         Swal.fire({
            icon: 'success',
            title: 'OTP Sent',
            text: @json(session('success')),
            confirmButtonColor: '#2c3c6b',
         });
      }
   @endif
})();
</script>
@endsection
