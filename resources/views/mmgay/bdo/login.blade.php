<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@extends('layouts.auth')
@section('title', 'MMGAY BDPO Login')
@section('content')

<style>
   body.bg-bg-subtle {
      font-family: 'Inter', system-ui, sans-serif;
      overflow: hidden !important;
   }
   body.bg-bg-subtle header .max-w-7xl {
      padding-top: 0.25rem !important;
      padding-bottom: 0.25rem !important;
   }
   body.bg-bg-subtle header img {
      height: 1.75rem !important;
      width: 1.75rem !important;
   }
   body.bg-bg-subtle header h1 {
      font-size: 0.85rem !important;
      font-weight: 600 !important;
      line-height: 1.2 !important;
   }
   body.bg-bg-subtle header p {
      font-size: 0.625rem !important;
   }
   body.bg-bg-subtle main.flex-grow {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 0;
      height: calc(100vh - 100px) !important;
      overflow: hidden !important;
   }
   body.bg-bg-subtle footer {
      padding-top: 0.35rem !important;
      padding-bottom: 0.35rem !important;
   }
   body.bg-bg-subtle footer .flex.flex-col {
      gap: 0.15rem !important;
   }
   body.bg-bg-subtle footer p.text-base {
      font-size: 0.7rem !important;
   }
   body.bg-bg-subtle footer p.text-sm {
      font-size: 0.625rem !important;
      margin-top: 0 !important;
   }
   body.bg-bg-subtle footer .flex.justify-center.items-center.gap-6.mt-4,
   body.bg-bg-subtle footer .flex.justify-center.items-center.gap-6 {
      display: none !important;
   }

    .cl-page {
       flex: 1;
       width: 100%;
       height: 100% !important;
       display: flex;
       align-items: center;
       justify-content: center;
       padding: 0.5rem 1rem;
       background:
          linear-gradient(135deg, rgba(30, 88, 188, 0.3) 0%, rgba(15, 23, 42, 0.55) 100%),
          url('{{ asset('images/citizen-login/mmgay_rural_house.jpg') }}') center / cover no-repeat;
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
      display: none !important;
   }
   .cl-brand__icon {
      width: 1.75rem;
      height: 1.75rem;
      border-radius: 0.4rem;
      background: rgba(255, 255, 255, 0.15);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
   }
   .cl-brand__icon .material-symbols-outlined {
      font-size: 1rem;
   }
   .cl-brand__title {
      font-size: 0.8125rem;
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
      padding: 0.65rem 0.8rem;
   }
   .cl-head {
      display: flex;
      align-items: center;
      gap: 0.45rem;
      margin-bottom: 0.35rem;
      padding-bottom: 0.35rem;
      border-bottom: 1px solid #eef2f6;
   }
   .cl-head__icon {
      width: 1.75rem;
      height: 1.75rem;
      border-radius: 0.4rem;
      background: #eff6ff;
      border: 1px solid #dbeafe;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
   }
   .cl-head__icon .material-symbols-outlined {
      font-size: 1rem;
      color: #0058bc;
   }
   .cl-head__title {
      font-size: 0.875rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      line-height: 1.2;
   }
   .cl-head__sub {
      font-size: 0.65rem;
      color: #64748b;
      margin: 0.1rem 0 0;
   }
   .cl-tags {
      display: none !important;
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
      color: #0058bc;
   }
   .cl-field {
      margin-bottom: 0.45rem;
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
   
   .cl-input-container {
      display: flex;
      align-items: center;
      height: 2.25rem;
      border: 1px solid #cbd5e1;
      border-radius: 0.5rem;
      overflow: hidden;
      background: #f8fafc;
      transition: border-color 0.2s, box-shadow 0.2s;
   }
   .cl-input-container:focus-within {
      border-color: #0058bc;
      box-shadow: 0 0 0 3px rgba(0, 88, 188, 0.12);
      background: #fff;
   }
   .cl-input__icon {
      width: 2.25rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      color: #64748b;
      flex-shrink: 0;
   }
   .cl-input__field {
      flex: 1;
      min-width: 0;
      border: none;
      background: transparent;
      padding: 0 0.625rem 0 0;
      font-size: 0.8125rem;
      outline: none;
      height: 100%;
   }
   .cl-input__field:focus {
      outline: none;
      box-shadow: none;
      background: transparent;
   }

   .cl-captcha {
      display: grid;
      grid-template-columns: 1fr auto;
      grid-template-rows: auto auto;
      gap: 0.375rem;
      align-items: center;
   }
   .cl-captcha__box {
      grid-column: 1;
      height: 2.25rem;
      border-radius: 0.5rem;
      background: #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      font-weight: 700;
      font-style: italic;
      letter-spacing: 0.2rem;
      color: #0f172a;
   }
   .cl-captcha__refresh {
      grid-column: 2;
      grid-row: 1;
      width: 2.25rem;
      height: 2.25rem;
      border: 1px solid #93c5fd;
      border-radius: 0.5rem;
      background: linear-gradient(135deg, #dbeafe, #bfdbfe);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.2s;
   }
   .cl-captcha__refresh:hover {
      background: linear-gradient(135deg, #bfdbfe, #93c5fd);
   }
   .cl-captcha__refresh span {
      font-size: 1rem;
      color: #0058bc;
   }
   .cl-captcha__input-wrap {
      grid-column: 1 / -1;
      display: flex;
      height: 2.25rem;
      border: 1px solid #cbd5e1;
      border-radius: 0.5rem;
      background: #f8fafc;
      overflow: hidden;
      transition: border-color 0.2s, box-shadow 0.2s;
   }
   .cl-captcha__input-wrap:focus-within {
      border-color: #0058bc;
      box-shadow: 0 0 0 3px rgba(0, 88, 188, 0.12);
      background: #fff;
   }
   .cl-captcha__input {
      flex: 1;
      min-width: 0;
      border: none;
      background: transparent;
      padding: 0 0.625rem;
      font-size: 0.8125rem;
      outline: none;
      height: 100%;
   }
   .cl-captcha__input:focus {
      outline: none;
      box-shadow: none;
      background: transparent;
   }
   .cl-btn {
      width: 100%;
      height: 2.15rem;
      margin-top: 0;
      border: none;
      border-radius: 0.5rem;
      background: #0058bc;
      color: #fff;
      font-size: 0.8125rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.375rem;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 14px rgba(0, 88, 188, 0.28);
   }
   .cl-btn:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(0, 88, 188, 0.35);
   }
</style>

<div class="cl-page">
   <div class="cl-shell">
      <div class="cl-card">
         <div class="cl-brand">
            <div class="cl-brand__icon">
               <span class="material-symbols-outlined">apartment</span>
            </div>
             <div>
                <p class="cl-brand__title">Housing For All (MMGAY)</p>
             </div>
         </div>

         <div class="cl-body">
            <div class="cl-head">
               <div class="cl-head__icon">
                  <span class="material-symbols-outlined">gavel</span>
               </div>
               <div>
                  <p class="cl-head__title">MMGAY BDPO Login</p>
                  <p class="cl-head__sub">Block Development & Panchayat Officer</p>
               </div>
            </div>

            <div class="cl-tags">
               <span class="cl-tag"><span class="material-symbols-outlined">verified_user</span> Secure</span>
               <span class="cl-tag"><span class="material-symbols-outlined">shield</span> Protected</span>
            </div>

            @if (session('error'))
                <div class="alert alert-danger py-2 px-3 rounded-lg text-xs font-semibold mb-3">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success py-2 px-3 rounded-lg text-xs font-semibold mb-3">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('mmgay.bdo.login.submit') }}">
               @csrf

               {{-- Username/Email --}}
               <div class="cl-field">
                  <label class="cl-label" for="emailInput">BDPO Email Address <span class="req">*</span></label>
                  <div class="cl-input-container">
                     <span class="material-symbols-outlined cl-input__icon">mail</span>
                     <input
                        type="email"
                        class="cl-input__field"
                        id="emailInput"
                        name="email"
                        placeholder="bdpobhiwani@gmail.com"
                        value="{{ old('email', 'bdpobhiwani@gmail.com') }}"
                        required>
                  </div>
                  @error('email')
                      <p class="text-danger text-xs mt-1">{{ $message }}</p>
                  @enderror
               </div>

               {{-- Password --}}
               <div class="cl-field">
                  <label class="cl-label" for="password">Password <span class="req">*</span></label>
                  <div class="cl-input-container">
                     <span class="material-symbols-outlined cl-input__icon">lock</span>
                     <input
                        type="password"
                        class="cl-input__field"
                        id="password"
                        name="password"
                        value="123456"
                        placeholder="••••••••"
                        required>
                     <button type="button" id="togglePassword" class="text-slate-400 hover:text-slate-600 mr-2 flex items-center">
                        <span id="eyeIcon" class="material-symbols-outlined text-[18px]">visibility</span>
                     </button>
                  </div>
                  @error('password')
                      <p class="text-danger text-xs mt-1">{{ $message }}</p>
                  @enderror
               </div>

               {{-- Captcha --}}
               <div class="cl-field">
                  <label class="cl-label" for="captchaInput">Security Verification <span class="req">*</span></label>
                  <div class="cl-captcha">
                     <div class="cl-captcha__box" id="captchaBox">{{ $captcha }}</div>
                     <button
                        type="button"
                        id="refreshCaptcha"
                        class="cl-captcha__refresh">
                        <span class="material-symbols-outlined">refresh</span>
                     </button>
                     <div class="cl-captcha__input-wrap">
                        <input
                           type="text"
                           class="cl-captcha__input"
                           id="captchaInput"
                           name="captcha"
                           placeholder="Enter captcha"
                           required>
                     </div>
                  </div>
                  @error('captcha')
                      <p class="text-danger text-xs mt-1">{{ $message }}</p>
                  @enderror
               </div>

               <button type="submit" class="cl-btn" id="loginBtn">
                  Login
                  <span class="material-symbols-outlined">arrow_forward</span>
               </button>
            </form>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400 mb-1">Are you a regular officer?</p>
                <a href="{{ route('mmgay.login') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#0058bc] hover:underline">
                    <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                    Go to Officer Login
                </a>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    }

    const refreshCaptcha = document.querySelector('#refreshCaptcha');
    const captchaBox = document.querySelector('#captchaBox');

    if (refreshCaptcha) {
        refreshCaptcha.addEventListener('click', function() {
            fetch('{{ route("mmgay.bdo.refresh.captcha") }}')
                .then(response => response.json())
                .then(data => {
                    captchaBox.textContent = data.captcha;
                });
        });
    }
</script>
@endsection
