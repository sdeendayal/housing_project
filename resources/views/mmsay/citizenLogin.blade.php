
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
                              Citizen Portal
                           </div>
                        </div>
                     </div>
                     <!-- DESCRIPTION -->
                     <div class="description">
                        Secure citizen login portal for accessing housing
                        schemes, applications and beneficiary services.
                        Modern digital infrastructure with protected
                        verification and smart citizen access.
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
                        account_circle
                        </span>
                     </div>
                     <div>
                        <div class="login-title">
                           Citizen Login
                        </div>
                        <div class="login-subtitle">
                           Official secure citizen portal
                        </div>
                     </div>
                  </div>
                  <!-- FORM -->
                  <form action="/mmsay.citizen.dashboard" method="GET">
                     @csrf
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
                     Verify & Login
                     <span class="material-symbols-outlined align-middle ms-2">
                     arrow_forward
                     </span>
                     </button>
                  </form>                 
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection