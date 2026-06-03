<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@extends('layouts.auth')
@section('title', 'Verify OTP')
@section('content')

<div class="body-div">
   <div class="main-wrapper">
      <div class="premium-login-card">
         <div class="row g-0">
            <!-- LEFT SECTION -->
            <div class="col-lg-5">
               <div class="left-panel h-100 d-flex flex-column justify-content-center">
                  <div>
                     <div class="d-flex align-items-start gap-4">
                        <div class="logo-box">
                           <span class="material-symbols-outlined">apartment</span>
                        </div>
                        <div>
                           <div class="portal-heading">Haryana Housing</div>
                           <div class="portal-subtitle">Citizen Portal</div>
                        </div>
                     </div>
                     <div class="description">
                        Enter the OTP sent to your registered mobile number
                        to complete secure citizen login.
                     </div>
                  </div>
                  <div>
                     <div class="feature-card">
                        <div class="feature-icon">
                           <span class="material-symbols-outlined">sms</span>
                        </div>
                        <div>
                           <div class="feature-title">OTP Verification</div>
                           <div class="feature-text">Valid for 10 minutes</div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- RIGHT SECTION — Step 2: OTP Verification -->
            <div class="col-lg-7">
               <div class="right-panel h-100 d-flex flex-column justify-content-center">

                  <div class="login-top">
                     <div class="login-icon">
                        <span class="material-symbols-outlined">lock</span>
                     </div>
                     <div>
                        <div class="login-title">Verify OTP</div>
                        <div class="login-subtitle">Enter the 6-digit OTP sent to your mobile</div>
                     </div>
                  </div>

                  <!-- Display mobile number (read-only) -->
                  <div class="mb-3">
                     <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                     <div class="input-group-custom d-flex">
                        <div class="country-code">+91</div>
                        <input
                           type="text"
                           class="form-control premium-input"
                           value="{{ $mobile }}"
                           readonly
                           disabled>
                     </div>
                  </div>

                  <!-- Verify OTP form -->
                  <form id="verifyOtpForm" action="{{ route('citizen.login.verify') }}" method="POST" class="mb-2">
                     @csrf

                     <div class="mb-1">
                        <label class="form-label">OTP <span class="text-danger">*</span></label>
                        <div class="input-group-custom d-flex">
                           <input
                              type="text"
                              class="form-control premium-input"
                              id="otpInput"
                              name="otp"
                              inputmode="numeric"
                              autocomplete="one-time-code"
                              placeholder="Enter 6-digit OTP"
                              maxlength="6"
                              value="{{ old('otp') }}"
                              required
                              autofocus>
                        </div>
                     </div>

                     <button type="submit" class="login-btn" id="verifyOtpBtn">
                        <span class="btn-label-default">
                           Verify OTP
                           <span class="material-symbols-outlined align-middle ms-2">arrow_forward</span>
                        </span>
                        <span class="btn-label-loading">
                           <span class="btn-spinner" aria-hidden="true"></span>
                           Verifying...
                        </span>
                     </button>
                  </form>

                  <!-- Resend OTP form -->
                  <form id="resendOtpForm" action="{{ route('citizen.login.resend-otp') }}" method="POST">
                     @csrf
                     <button type="submit" class="otp-btn w-100" id="resendOtpBtn">
                        <span class="btn-label-default">Resend OTP</span>
                        <span class="btn-label-loading">
                           <span class="btn-spinner" aria-hidden="true"></span>
                           Sending OTP...
                        </span>
                     </button>
                  </form>

                  <p class="mt-3 text-sm">
                     <a href="{{ route('citizen.login') }}">Change mobile number</a>
                  </p>
               </div>
            </div>
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
