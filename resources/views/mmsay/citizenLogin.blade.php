<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@extends('layouts.auth')
@section('title', 'MMSAY Login')
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
                        Secure citizen login portal for accessing housing
                        schemes, applications and beneficiary services.
                     </div>
                  </div>
                  <div>
                     <div class="feature-card">
                        <div class="feature-icon">
                           <span class="material-symbols-outlined">verified_user</span>
                        </div>
                        <div>
                           <div class="feature-title">Secure Login</div>
                           <div class="feature-text">Protected citizen access</div>
                        </div>
                     </div>
                     <div class="feature-card">
                        <div class="feature-icon">
                           <span class="material-symbols-outlined">sms</span>
                        </div>
                        <div>
                           <div class="feature-title">OTP Verification</div>
                           <div class="feature-text">Mobile OTP verification system</div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- RIGHT SECTION — Step 1: Mobile + Captcha -->
            <div class="col-lg-7">
               <div class="right-panel h-100 d-flex flex-column justify-content-center">

                  <div class="login-top">
                     <div class="login-icon">
                        <span class="material-symbols-outlined">account_circle</span>
                     </div>
                     <div>
                        <div class="login-title">Citizen Login</div>
                        <div class="login-subtitle">Enter mobile number to receive OTP</div>
                     </div>
                  </div>

                  <form id="sendOtpForm" action="{{ route('citizen.login.send-otp') }}" method="POST">
                     @csrf

                     <div class="mb-1">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <div class="input-group-custom d-flex">
                           <div class="country-code">+91</div>
                           <input
                              type="text"
                              class="form-control premium-input"
                              id="mobileInput"
                              name="mobile"
                              inputmode="numeric"
                              autocomplete="tel"
                              placeholder="Enter Mobile Number"
                              maxlength="10"
                              value="{{ old('mobile') }}"
                              required>
                        </div>
                     </div>

                     <div class="mb-1">
                        <label class="form-label">Captcha Verification <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-3">
                           <div class="captcha-box" id="captchaText">{{ $captcha }}</div>
                           <div
                              role="button"
                              tabindex="0"
                              aria-label="Refresh captcha"
                              onclick="refreshCaptcha(this)"
                              onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();refreshCaptcha(this);}"
                              class="login-icon-new captcha-refresh-btn">
                              <span class="material-symbols-outlined">refresh</span>
                           </div>
                           <div class="input-group-custom flex-grow-1 d-flex">
                              <input
                                 type="text"
                                 class="form-control premium-input"
                                 id="captchaInput"
                                 name="captcha"
                                 placeholder="Enter Captcha"
                                 required>
                           </div>
                        </div>
                     </div>

                     <button type="submit" class="login-btn" id="sendOtpBtn">
                        <span class="btn-label-default">
                           Send OTP
                           <span class="material-symbols-outlined align-middle ms-2">arrow_forward</span>
                        </span>
                        <span class="btn-label-loading">
                           <span class="btn-spinner" aria-hidden="true"></span>
                           Sending OTP...
                        </span>
                     </button>
                  </form>
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
      <p class="citizen-page-loader__text">Sending OTP, please wait...</p>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
   var form = document.getElementById('sendOtpForm');
   var btn = document.getElementById('sendOtpBtn');
   var loader = document.getElementById('citizenPageLoader');
   var mobileInput = document.getElementById('mobileInput');
   var captchaInput = document.getElementById('captchaInput');
   var mobilePattern = /^[6-9]\d{9}$/;

   function sanitizeMobile(value) {
      var digits = String(value || '').replace(/\D/g, '');
      if (digits.length > 10) {
         digits = digits.slice(-10);
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

   function bindMobileSanitizer(input) {
      if (!input) {
         return;
      }

      input.addEventListener('input', function () {
         var cleaned = sanitizeMobile(input.value);
         if (input.value !== cleaned) {
            input.value = cleaned;
         }
      });

      input.addEventListener('paste', function (e) {
         e.preventDefault();
         var pasted = (e.clipboardData || window.clipboardData).getData('text');
         input.value = sanitizeMobile(pasted);
      });
   }

   function showPageLoader() {
      btn.classList.add('is-loading');
      btn.disabled = true;
      loader.removeAttribute('hidden');
      document.body.style.overflow = 'hidden';
   }

   bindMobileSanitizer(mobileInput);

   if (mobileInput && mobileInput.value) {
      mobileInput.value = sanitizeMobile(mobileInput.value);
   }

   if (!form || !btn || !loader) {
      return;
   }

   form.addEventListener('submit', function (e) {
      e.preventDefault();

      var mobile = sanitizeMobile(mobileInput ? mobileInput.value : '');
      var captcha = captchaInput ? captchaInput.value.trim() : '';

      if (mobileInput) {
         mobileInput.value = mobile;
      }

      if (!mobile) {
         showValidationAlert('Mobile Required', 'Please enter your 10-digit mobile number.');
         mobileInput && mobileInput.focus();
         return;
      }

      if (mobile.length !== 10) {
         showValidationAlert('Invalid Mobile', 'Mobile number must be exactly 10 digits (numbers only).');
         mobileInput && mobileInput.focus();
         return;
      }

      if (!mobilePattern.test(mobile)) {
         showValidationAlert('Invalid Mobile', 'Enter a valid Indian mobile number starting with 6, 7, 8, or 9.');
         mobileInput && mobileInput.focus();
         return;
      }

      if (!captcha) {
         showValidationAlert('Captcha Required', 'Please enter the captcha shown above.');
         captchaInput && captchaInput.focus();
         return;
      }

      showPageLoader();
      form.submit();
   });

   @if ($errors->any())
      showErrorAlert('Validation Error', @json($errors->first()));
   @elseif (session('error'))
      showErrorAlert('Unable to Send OTP', @json(session('error')));
   @endif
})();
</script>
@endsection
