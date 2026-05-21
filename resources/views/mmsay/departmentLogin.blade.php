<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
   rel="stylesheet">
<!-- Icons -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@extends('layouts.auth')
@section('title', 'MMSAY Login')
@section('content')
<!-- <main class="flex-grow flex items-center justify-center px-4 py-4 bg-slate-100 overflow-hidden">
   <div class="w-full max-w-6xl bg-white rounded-[24px] shadow-2xl overflow-hidden grid md:grid-cols-2 min-h-[580px]">
     
      <div class="bg-gradient-to-br from-[#003c72] to-[#0a5ea8] p-8 text-white flex flex-col justify-between">
         <div>
          
            <div class="flex items-center gap-4 mb-8">
               <div
                  class="w-24 h-24 rounded-[24px] bg-white/10 backdrop-blur-md border border-white/10 flex items-center justify-center shadow-lg">
                  <span class="material-symbols-outlined text-[48px] text-white">
                  apartment
                  </span>
               </div>
               <div>
                  <h1 class="text-[34px] font-bold leading-tight">
                     Haryana Housing
                  </h1>
                  <p class="text-lg text-slate-200">
                     Department Portal
                  </p>
               </div>
            </div>
            
            <div class="space-y-2 text-[14px] text-slate-200 leading-6">
               <p>
                  Secure login portal for authorized Haryana Government Department officials.
               </p>
               <p>
                  Access housing schemes, applications and internal services.
               </p>
            </div>
           
            <div class="mt-10 space-y-5">
            
               <div class="flex items-start gap-3">
                  <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                     <span class="material-symbols-outlined text-green-300 text-[26px]">
                     verified_user
                     </span>
                  </div>
                  <div>
                     <h4 class="font-semibold text-xl">
                        Secure Login
                     </h4>
                     <p class="text-sm text-slate-300">
                        Protected access for officials
                     </p>
                  </div>
               </div>
              
               <div class="flex items-start gap-3">
                  <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                     <span class="material-symbols-outlined text-yellow-300 text-[26px]">
                     sms
                     </span>
                  </div>
                  <div>
                     <h4 class="font-semibold text-xl">
                        OTP Based Login
                     </h4>
                     <p class="text-sm text-slate-300">
                        Mobile OTP verification system
                     </p>
                  </div>
               </div>
            </div>
         </div>
      </div>
      
      <div class="bg-white px-10 py-8 flex items-center justify-center">
         <form action="/mmsay.department.dashboard" method="GET"
            class="w-full max-w-md">
            @csrf
          
            <div class="flex items-center gap-4 mb-7">
               <div
                  class="w-20 h-20 rounded-[22px] bg-slate-100 flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[38px] text-[#003c72]">
                  admin_panel_settings
                  </span>
               </div>
               <div>
                  <h2 class="text-2xl font-bold text-slate-800 leading-tight">
                     Department Login
                  </h2>
                  <p class="text-sm text-slate-500">
                     Official secure login portal
                  </p>
               </div>
            </div>
           
            <div class="mb-4">
               <label class="text-[13px] font-medium text-slate-700 block mb-2">
               Department ID
               </label>
               <input name="department_id" required
                  class="w-full h-[44px] rounded-xl border border-slate-300 px-4 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none"
                  placeholder="Enter Official Department ID"
                  type="text">
            </div>
            
            <div class="mb-4">
               <label class="text-[13px] font-medium text-slate-700 block mb-2">
               Registered Mobile Number
               </label>
               <div class="flex">
                  <span
                     class="h-[44px] px-4 flex items-center border border-r-0 border-slate-300 rounded-l-xl bg-slate-50 text-slate-600 text-sm">
                  +91
                  </span>
                  <input name="mobile" required
                     class="w-full h-[44px] rounded-r-xl border border-slate-300 px-4 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none"
                     placeholder="Enter Mobile Number"
                     type="text">
               </div>
            </div>
            
            <div class="mb-4">
               <label class="text-[13px] font-medium text-slate-700 block mb-2">
               OTP Verification
               </label>
               <div class="flex gap-2">
                  <input name="otp" required
                     class="w-full h-[44px] rounded-xl border border-slate-300 px-4 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none"
                     placeholder="Enter OTP"
                     type="text">
                  <button type="button"
                     class="h-[44px] px-4 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-medium whitespace-nowrap transition">
                  Send OTP
                  </button>
               </div>
            </div>
            
            <div class="mb-5">
               <label class="text-[13px] font-medium text-slate-700 block mb-2">
               Captcha Verification
               </label>
               <div class="flex gap-3">
                  <div
                     class="h-[44px] min-w-[120px] rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-base font-bold tracking-[3px] text-[#003c72]">
                     X7P9K
                  </div>
                  <input type="text"
                     class="w-full h-[44px] rounded-xl border border-slate-300 px-4 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none"
                     placeholder="Enter Captcha">
               </div>
            </div>
            
            <button type="submit"
               class="w-full h-[46px] rounded-xl bg-[#003c72] hover:bg-[#002d55] text-white text-sm font-semibold shadow-lg transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[18px]">
            lock
            </span>
            Verify & Secure Login
            </button>
         </form>
      </div>
   </div>
</main> -->
<div class="body-div">
   <div class="main-wrapper">
      <div class="premium-login-card">
         <div class="row g-0">
            <!-- LEFT SECTION -->
            <div class="col-lg-5">
               <div class="left-panel h-100 d-flex flex-column justify-content-center">
                  <div>
                     <!-- LOGO -->
                     <div class="d-flex align-items-start gap-4">
                        <div class="logo-box">
                           <span class="material-symbols-outlined">
                           apartment
                           </span>
                        </div>
                        <div>
                           <div class="portal-heading">
                              Haryana Housing
                           </div>
                           <div class="portal-subtitle">
                              Department Portal
                           </div>
                        </div>
                     </div>
                     <!-- DESCRIPTION -->
                     <div class="description">
                        Secure login portal for authorized Haryana Government Department officials.
                        Access housing schemes, applications and internal services.
                     </div>
                     <!-- IMAGE -->
                     <!-- <div class="housing-image">
                        <img
                            src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1200&auto=format&fit=crop"
                            alt="Housing">
                        
                        </div> -->
                  </div>
                  <!-- FEATURES -->
                  <div>
                     <div class="feature-card">
                        <div class="feature-icon">
                           <span class="material-symbols-outlined">
                           verified_user
                           </span>
                        </div>
                        <div>
                           <div class="feature-title">
                              Secure Login
                           </div>
                           <div class="feature-text">
                              Protected citizen access
                           </div>
                        </div>
                     </div>
                     <div class="feature-card">
                        <div class="feature-icon">
                           <span class="material-symbols-outlined">
                           sms
                           </span>
                        </div>
                        <div>
                           <div class="feature-title">
                              OTP Verification
                           </div>
                           <div class="feature-text">
                              Mobile OTP verification system
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- RIGHT SECTION -->
            <div class="col-lg-7">
               <div class="right-panel h-100 d-flex flex-column justify-content-center">
                  <!-- TOP -->
                  <div class="login-top">
                     <div class="login-icon">
                        <span class="material-symbols-outlined">
                        admin_panel_settings
                        </span>
                     </div>
                     <div>
                        <div class="login-title">
                           Department Login
                        </div>
                        <div class="login-subtitle">
                           Official secure login portal
                        </div>
                     </div>
                  </div>
                  <!-- FORM -->
                  <form action="/mmsay.citizen.dashboard" method="GET">
                     @csrf
                     <!-- MOBILE -->
                     <div class="1">
                        <label class="form-label">
                        Department ID
                        </label>
                        <div class="input-group-custom d-flex">
                           <input
                              type="text"
                              class="form-control premium-input"
                              name="id"
                              placeholder="Enter Official Department ID"
                              required
                              >
                        </div>
                     </div>
                     <!-- MOBILE -->
                     <div class="mb-1">
                        <label class="form-label">
                        Mobile Number
                        </label>
                        <div class="input-group-custom d-flex">
                           <div class="country-code">
                              +91
                           </div>
                           <input
                              type="text"
                              class="form-control premium-input"
                              name="mobile"
                              placeholder="Enter Mobile Number"
                              required
                              >
                        </div>
                     </div>
                     <!-- OTP -->
                     <div class="mb-1">
                        <label class="form-label">
                        OTP Verification
                        </label>
                        <div class="otp-wrapper">
                           <div class="input-group-custom flex-grow-1 d-flex">
                              <input
                                 type="text"
                                 class="form-control premium-input"
                                 name="otp"
                                 placeholder="Enter OTP"
                                 required
                                 >
                           </div>
                          
                           <button type="button" class="otp-btn">
                           Send OTP
                           </button>
                        </div>
                     </div>
                     <!-- CAPTCHA -->
                     <div class="mb-1">
                        <label class="form-label">
                        Captcha Verification
                        </label>
                        <div class="d-flex  align-items-center gap-3">
                           <div class="captcha-box">
                              7XK92
                           </div>
                            <div class="login-icon-new rotating-icon">
                              <span class="material-symbols-outlined  ">
                              refresh
                              </span>
                           </div>
                           <div class="input-group-custom flex-grow-1 d-flex">
                              <input
                                 type="text"
                                 class="form-control premium-input"
                                 placeholder="Enter Captcha"
                                 >
                           </div>
                        </div>
                     </div>
                     <!-- BUTTON -->
                     <button type="submit" class="login-btn">
                     Verify & Secure Login
                     <span class="material-symbols-outlined align-middle ms-2">
                     arrow_forward
                     </span>
                     </button>
                  </form>
                  <!-- FOOTER -->
                  <!-- <div class="footer-text">
                     © 2026 Haryana Housing Department • All Rights Reserved
                     
                     </div> -->
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection