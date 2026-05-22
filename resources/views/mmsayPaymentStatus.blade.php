<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8" />
      <meta content="width=device-width, initial-scale=1.0" name="viewport" />
      <title>MMSAY Citizen Dashboard - Haryana Housing For All</title>
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap"
         rel="stylesheet" />
      <link
         href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
         rel="stylesheet" />
      <link
         href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
         rel="stylesheet" />
      <script id="tailwind-config">
         tailwind.config = {
             darkMode: "class",
             theme: {
                 extend: {
                     "colors": {
                         "on-secondary-fixed-variant": "#005312",
                         "primary": "#003358",
                         "on-primary-fixed-variant": "#00497b",
                         "on-primary": "#ffffff",
                         "on-tertiary-fixed": "#0f1d25",
                         "on-error": "#ffffff",
                         "secondary-container": "#a0f399",
                         "surface-container": "#eeeeee",
                         "on-secondary-fixed": "#002204",
                         "surface-tint": "#296195",
                         "inverse-on-surface": "#f1f1f1",
                         "primary-fixed": "#d0e4ff",
                         "on-surface": "#1a1c1c",
                         "success-green": "#2e7d32",
                         "on-surface-variant": "#42474f",
                         "on-secondary-container": "#217128",
                         "on-tertiary-container": "#a9b8c2",
                         "secondary-fixed-dim": "#88d982",
                         "inverse-surface": "#2f3131",
                         "surface-bright": "#f9f9f9",
                         "outline": "#727780",
                         "on-error-container": "#93000a",
                         "surface": "#f9f9f9",
                         "outline-variant": "#c1c7d0",
                         "on-background": "#1a1c1c",
                         "secondary": "#1b6d24",
                         "primary-container": "#004a7c",
                         "status-blue": "#004a7c",
                         "tertiary-fixed": "#d6e5ef",
                         "on-primary-fixed": "#001d35",
                         "background": "#f9f9f9",
                         "surface-variant": "#e2e2e2",
                         "error-container": "#ffdad6",
                         "secondary-fixed": "#a3f69c",
                         "on-primary-container": "#87baf3",
                         "tertiary-container": "#3b4952",
                         "surface-container-low": "#f3f3f3",
                         "tertiary-fixed-dim": "#bac9d3",
                         "surface-container-highest": "#e2e2e2",
                         "on-tertiary": "#ffffff",
                         "tertiary": "#25333b",
                         "glass-surface": "rgba(255, 255, 255, 0.7)",
                         "bg-subtle": "#f8f9fa",
                         "on-secondary": "#ffffff",
                         "on-tertiary-fixed-variant": "#3b4951",
                         "primary-fixed-dim": "#9ccaff",
                         "border-gray": "#e0e0e0",
                         "inverse-primary": "#9ccaff",
                         "surface-dim": "#dadada",
                         "error": "#ba1a1a",
                         "surface-container-high": "#e8e8e8",
                         "surface-container-lowest": "#ffffff"
                     },
                     "borderRadius": {
                         "DEFAULT": "0.125rem",
                         "lg": "0.25rem",
                         "xl": "0.5rem",
                         "full": "0.75rem"
                     },
                     "spacing": {
                         "container-max": "1280px",
                         "stack-lg": "32px",
                         "margin-mobile": "16px",
                         "gutter": "24px",
                         "stack-sm": "8px",
                         "stack-md": "16px"
                     },
                     "fontFamily": {
                         "headline-lg": [
                             "Inter"
                         ],
                         "body-lg": [
                             "Inter"
                         ],
                         "label-sm": [
                             "Inter"
                         ],
                         "body-md": [
                             "Inter"
                         ],
                         "body-sm": [
                             "Inter"
                         ],
                         "headline-md": [
                             "Inter"
                         ],
                         "headline-xl": [
                             "Inter"
                         ],
                         "label-md": [
                             "Inter"
                         ]
                     },
                     "fontSize": {
                         "headline-lg": [
                             "32px",
                             {
                                 "lineHeight": "40px",
                                 "letterSpacing": "-0.01em",
                                 "fontWeight": "700"
                             }
                         ],
                         "body-lg": [
                             "18px",
                             {
                                 "lineHeight": "28px",
                                 "fontWeight": "400"
                             }
                         ],
                         "label-sm": [
                             "12px",
                             {
                                 "lineHeight": "16px",
                                 "fontWeight": "500"
                             }
                         ],
                         "body-md": [
                             "16px",
                             {
                                 "lineHeight": "24px",
                                 "fontWeight": "400"
                             }
                         ],
                         "body-sm": [
                             "14px",
                             {
                                 "lineHeight": "20px",
                                 "fontWeight": "400"
                             }
                         ],
                         "headline-md": [
                             "24px",
                             {
                                 "lineHeight": "32px",
                                 "fontWeight": "600"
                             }
                         ],
                         "headline-xl": [
                             "40px",
                             {
                                 "lineHeight": "48px",
                                 "letterSpacing": "-0.02em",
                                 "fontWeight": "700"
                             }
                         ],
                         "label-md": [
                             "14px",
                             {
                                 "lineHeight": "16px",
                                 "fontWeight": "600"
                             }
                         ]
                     }
                 },
             },
         }
      </script>
      <style>
         body {
         font-family: 'Inter', sans-serif;
         font-size: 14px;
         }
         .material-symbols-outlined {
         font-size: 20px;
         }
      </style>
   </head>
   <body class="bg-background text-on-background min-h-screen flex flex-col md:flex-row">
      <!-- SideNavBar -->
      <nav id="sidebar"
         class="fixed md:flex flex-col bg-surface text-primary font-label-md text-label-md border-r border-dotted border-outline-variant left-0 top-0 h-full w-[260px] z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
         <!-- Premium Logo Section -->
         <div
            class="relative overflow-hidden px-5 py-5 border-b border-gray-200/70 bg-gradient-to-br from-white via-[#f8fbff] to-[#eef4ff]">
            <!-- Decorative Blur -->
            <div
               class="absolute -top-10 -right-10 w-28 h-28 bg-blue-200/30 rounded-full blur-3xl">
            </div>
            <div
               class="absolute -bottom-10 -left-10 w-24 h-24 bg-indigo-200/20 rounded-full blur-3xl">
            </div>
            <!-- Content -->
            <div class="relative z-10 flex items-center gap-3">
               <!-- Logo -->
               <div
                  class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0B5CAD] to-[#003358] flex items-center justify-center shadow-lg shadow-blue-200/40 border border-white/30 shrink-0">
                  <img alt="Haryana State Emblem"
                     class="w-8 h-8 object-contain drop-shadow-sm"
                     src="Haryana_emblem.png" />
               </div>
               <!-- Text -->
               <div>
                  <h1
                     class="text-sm font-extrabold leading-tight text-[#0B2C4D] tracking-wide">
                     Department of Housing For All
                  </h1>
                  <div class="flex items-center gap-1 mt-1">
                     <span
                        class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse">
                     </span>
                     <p class="text-[11px] font-medium text-gray-500 tracking-wide">
                        Government of Haryana
                     </p>
                  </div>
               </div>
            </div>
         </div>
         <!-- Menu -->
         <div class="flex-1 overflow-y-auto px-margin-mobile py-4">
            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
               href="/mmsay.citizen.dashboard">
            <span class="material-symbols-outlined">
            dashboard
            </span>
            Dashboard
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 bg-primary-fixed text-on-primary-fixed border-l-4 border-primary font-bold hover:bg-surface-container-high transition-all"
               href="/mmsay-payment-status">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
            payments
            </span>
            Payment Status
            </a>
            {{-- <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
               href="#">
            <span class="material-symbols-outlined">
            description
            </span>
            Statutory Docs
            </a> --}}
            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
               href="#">
            <span class="material-symbols-outlined">
            bolt
            </span>
            Quick Services
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
               href="#">
            <span class="material-symbols-outlined">
            track_changes
            </span>
            Application Status
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT mb-2 text-on-surface-variant hover:bg-surface-container-high transition-all"
               href="#">
            <span class="material-symbols-outlined">
            support_agent
            </span>
            Grievances
            </a>
         </div>
      </nav>
      <!-- Mobile Overlay -->
      <div id="sidebarOverlay"
         class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>
      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col md:ml-[260px] min-h-screen">
         <!-- TopAppBar -->
         <header
            class="bg-primary text-on-primary font-headline-md text-headline-md font-label-md text-label-md docked full-width top-0 sticky backdrop-blur-md bg-opacity-90 border-b border-outline-variant shadow-sm z-50">
            <div
               class="flex justify-between items-center w-full px-margin-mobile md:px-gutter max-w-container-max mx-auto h-16">
               <div class="flex items-center gap-4">
                  <!-- Mobile Menu Button -->
                  <button id="menuToggle"
                     class="md:hidden text-on-primary hover:bg-on-primary-fixed-variant/20 p-2 rounded-full transition-colors">
                  <span class="material-symbols-outlined">menu</span>
                  </button>
                  <span class="text-lg md:text-lg font-semibold text-on-primary"> Citizen Dashboard</span>
               </div>
               <div class="flex items-center gap-2">
                  <a href="/mmsay-profile"
                     class="flex items-center gap-2 bg-[#0B5CAD] hover:bg-[#084B8A] text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200">
                  <span class="material-symbols-outlined text-[20px]">
                  account_circle
                  </span>
                  <span class="text-xs font-medium">
                  Profile
                  </span>
                  </a>
                  <a href="/mmsay-login"
                     class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-200">
                  <span class="material-symbols-outlined text-[20px]">
                  logout
                  </span>
                  <span class="text-xs font-medium">
                  Logout
                  </span>
                  </a>
               </div>
            </div>
         </header>
         <!-- Main Canvas -->
         <main class="flex-1 p-margin-mobile md:p-gutter max-w-container-max mx-auto w-full mt-16 md:mt-0 pb-stack-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
               <!-- Purchase Date -->
               <div
                  class="bg-gradient-to-br from-white to-gray-50 border border-white/40 rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-300/30 hover:-translate-y-2 transition-all duration-500 backdrop-blur-md">
                  <p class="text-sm font-medium text-gray-500 mb-2">
                     Purchase Date
                  </p>
                  <h4 class="text-xl font-bold text-[#0B3B66]">
                     12 Oct 2023
                  </h4>
                  <div class="mt-2 text-[#0B5CAD]">
                     <span class="material-symbols-outlined text-[28px]">
                     calendar_today
                     </span>
                  </div>
               </div>
               <!-- Total Paid -->
               <div
                  class="bg-gradient-to-br from-white to-gray-50 border border-white/40 rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-300/30 hover:-translate-y-2 transition-all duration-500 backdrop-blur-md">
                  <p class="text-sm font-medium text-gray-500 mb-2">
                     Total Paid Amount
                  </p>
                  <h4 class="text-xl font-bold text-green-700">
                     ₹ 14,50,000
                  </h4>
                  <div class="mt-2 w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                     <div class="bg-green-700 h-full w-[65%] rounded-full"></div>
                  </div>
               </div>
               <!-- Outstanding -->
               <div
                  class="bg-gradient-to-br from-white to-gray-50 border border-white/40 rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-300/30 hover:-translate-y-2 transition-all duration-500 backdrop-blur-md">
                  <p class="text-sm font-medium text-gray-500 mb-2">
                     Total Outstanding
                  </p>
                  <h4 class="text-xl font-bold text-red-600">
                     ₹ 7,25,000
                  </h4>
                  <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                     <span class="material-symbols-outlined text-[18px]">
                     warning
                     </span>
                     Due in 15 days
                  </div>
               </div>
               <!-- Status -->
               <div
                  class="bg-gradient-to-br from-white to-gray-50 border border-white/40 rounded-2xl p-4 shadow-xl hover:shadow-2xl hover:shadow-indigo-300/30 hover:-translate-y-2 transition-all duration-500 backdrop-blur-md">
                  <p class="text-sm font-medium text-gray-500 mb-2">
                     Flat/Plot Status
                  </p>
                  <h4 class="text-xl font-bold text-[#0B3B66]">
                     Allotted
                  </h4>
                  <div class="mt-2">
                     <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                     Category A-1
                     </span>
                  </div>
               </div>
            </div>
            <div class="lg:col-span-8 flex flex-col gap-6">
               <!-- Modern Premium Payment Card -->
               <div
                  class="relative overflow-hidden rounded-3xl bg-white border border-[#E8EEF5] shadow-[0_15px_40px_rgba(15,23,42,0.08)] mb-6">
                  <!-- Top Glow -->
                  <div
                     class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#7F5AF0] via-[#2CB67D] to-[#FF8906]">
                  </div>
                  <!-- Decorative Shapes -->
                  <div
                     class="absolute -top-20 -right-16 w-52 h-52 bg-purple-100 rounded-full blur-3xl opacity-50">
                  </div>
                  <div
                     class="absolute -bottom-20 -left-16 w-52 h-52 bg-cyan-100 rounded-full blur-3xl opacity-50">
                  </div>
                  <!-- Header -->
                  <div
                     class="relative z-10 px-5 md:px-7 py-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                     <!-- User Info -->
                     <div class="flex items-center gap-4">
                        <div
                           class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#7F5AF0] to-[#6246EA] text-white flex items-center justify-center shadow-lg shrink-0">
                           <span class="material-symbols-outlined text-[28px]">
                           account_balance_wallet
                           </span>
                        </div>
                        <div>
                           <p class="text-xs uppercase tracking-[2px] text-gray-400 font-semibold">
                              Payment Overview
                           </p>
                           <h2 class="text-lg md:text-xl font-extrabold text-gray-800">
                              Hello, TEST123
                           </h2>
                           <p class="text-sm text-gray-500 mt-1">
                              Your payment details are updated today
                           </p>
                        </div>
                     </div>
                  </div>
                  <!-- Action Row -->
                  <div
                     class="relative z-10 px-5 md:px-7 pb-5 flex flex-col xl:flex-row gap-5">
                     <!-- Left Main Box -->
                     <div
                        class="flex-1 rounded-2xl border border-[#EEF2F7] bg-[#FCFCFD] p-5">
                        <!-- Top Details -->
                        <div
                           class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                           <div>
                              <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-1">
                                 Application No.
                              </p>
                              <h4 class="text-sm font-bold text-gray-800">
                                 TEST12322
                              </h4>
                           </div>
                           <div>
                              <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-1">
                                 Asset Name
                              </p>
                              <h4 class="text-sm font-bold text-gray-800">
                                 Flat A-12
                              </h4>
                           </div>
                           <div>
                              <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-1">
                                 Due Date
                              </p>
                              <h4 class="text-sm font-bold text-[#E63946]">
                                 01-05-2026
                              </h4>
                           </div>
                        </div>
                        <!-- Payment Type -->
                        <div>
                           <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-3">
                              Select Payment Type
                           </p>
                           <div class="w-full overflow-x-auto pb-2">
                              <!-- Main Row -->
                              <div class="flex flex-nowrap items-stretch gap-3 min-w-max">
                                 <!-- Minimum Due -->
                                 <label
                                    class="min-w-[160px] h-[75px] cursor-pointer flex items-center gap-2 px-4 rounded-xl border-2 border-[#7F5AF0] bg-[#F5F3FF] transition-all duration-300">
                                    <input checked type="radio" name="pay"
                                       class="accent-[#7F5AF0] w-4 h-4 shrink-0">
                                    <div>
                                       <p class="text-[10px] font-semibold uppercase tracking-wide text-[#6246EA]">
                                          Minimum Due
                                       </p>
                                       <h5 class="text-base font-bold text-gray-900 mt-1">
                                          ₹ 1,389
                                       </h5>
                                    </div>
                                 </label>
                                 <!-- Total Due -->
                                 <label
                                    class="min-w-[160px] h-[75px] cursor-pointer flex items-center gap-2 px-4 rounded-xl border border-gray-200 bg-white hover:border-[#2CB67D] transition-all duration-300">
                                    <input type="radio" name="pay"
                                       class="accent-[#2CB67D] w-4 h-4 shrink-0">
                                    <div>
                                       <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">
                                          Total Due
                                       </p>
                                       <h5 class="text-base font-bold text-gray-900 mt-1">
                                          ₹ 50,004
                                       </h5>
                                    </div>
                                 </label>
                                 <!-- Other Amount -->
                                 <label
                                    class="min-w-[160px] h-[75px] cursor-pointer flex items-center gap-2 px-4 rounded-xl border border-gray-200 bg-white hover:border-[#FF8906] transition-all duration-300">
                                    <input type="radio" name="pay" id="customRadio"
                                       class="accent-[#FF8906] w-4 h-4 shrink-0">
                                    <div>
                                       <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">
                                          Other Amount
                                       </p>
                                       <h5 class="text-base font-bold text-gray-900 mt-1">
                                          Custom
                                       </h5>
                                    </div>
                                 </label>
                                 <!-- Pay Button -->
                                 <button
                                    class="min-w-[160px] h-[75px] rounded-xl bg-gradient-to-r from-[#6246EA] to-[#7F5AF0] hover:scale-[1.02] text-white px-4 transition-all duration-300">
                                    <div class="flex flex-col items-center justify-center h-full">
                                       <span class="block text-sm font-bold">
                                       Pay Now
                                       </span>
                                       <span class="text-[10px] text-white/80">
                                       Secure Payment
                                       </span>
                                    </div>
                                 </button>
                              </div>
                              <!-- Custom Amount Input -->
                              <div id="customAmountBox" class="hidden mt-4 max-w-xs">
                                 <div class="bg-white border border-orange-200 rounded-xl p-3">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                                    Enter Custom Amount
                                    </label>
                                    <div class="relative">
                                       <span
                                          class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-sm">
                                       ₹
                                       </span>
                                       <input type="number"
                                          placeholder="Enter amount"
                                          class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-orange-100 focus:border-[#FF8906] outline-none text-sm font-semibold">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- Alert -->
                        <div
                           class="mt-5 flex items-start gap-3 rounded-2xl border border-[#FFE5E5] bg-[#FFF5F5] px-4 py-3">
                           <div
                              class="w-9 h-9 rounded-xl bg-[#E63946] text-white flex items-center justify-center shrink-0">
                              <span class="material-symbols-outlined text-[18px]">
                              notification_important
                              </span>
                           </div>
                           <div>
                              <h4 class="text-sm font-bold text-[#B42318]">
                                 Payment Reminder
                              </h4>
                              <p class="text-xs text-[#B42318]/80 mt-1 leading-5">
                                 Please complete your payment before 01-05-2026
                                 to avoid late charges.
                              </p>
                           </div>
                        </div>
                     </div>
                     <!-- Right Actions -->
                     <div class="xl:w-[260px] flex flex-col gap-4">
                        <!-- Amount Card -->
                        <div
                           class="bg-gradient-to-br from-[#111827] to-[#1F2937] rounded-2xl px-5 py-4 shadow-xl min-w-[260px]">
                           <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">
                              Total Outstanding
                           </p>
                           <div class="flex items-center gap-2">
                              <span class="text-2xl font-black text-white">
                              ₹ 50,004
                              </span>
                              <span
                                 class="px-2 py-1 rounded-full bg-red-500/20 text-red-300 text-[10px] font-bold uppercase">
                              Due
                              </span>
                           </div>
                           <div class="mt-3 w-full bg-white/10 h-2 rounded-full overflow-hidden">
                              <div
                                 class="w-[72%] h-full bg-gradient-to-r from-[#FF8906] to-[#F25F4C] rounded-full">
                              </div>
                           </div>
                        </div>
                        <button
                           class="w-full rounded-2xl border border-[#D0D5DD] bg-white hover:bg-gray-50 px-5 py-4 transition-all">
                        <span class="block text-sm font-bold text-gray-800">
                        Download Letter
                        </span>
                        <span class="text-xs text-gray-500">
                        PDF Allotment Copy
                        </span>
                        </button>
                        <button
                           class="w-full rounded-2xl border border-[#FFE7C2] bg-[#FFF8EE] hover:bg-[#FFF2DD] px-5 py-4 transition-all">
                        <span class="block text-sm font-bold text-[#B54708]">
                        Raise Ticket
                        </span>
                        <span class="text-xs text-[#B54708]/80">
                        Need Help?
                        </span>
                        </button>
                     </div>
                  </div>
               </div>
               <!-- Payment History -->
               <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                  <!-- Header -->
                  <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                     <h3 class="text-lg font-bold text-[#0B3B66]">
                        Payment History
                     </h3>
                     <div class="flex gap-3">
                        <button class="p-2 rounded-lg hover:bg-gray-100 transition-all">
                        <span class="material-symbols-outlined text-gray-600">
                        filter_list
                        </span>
                        </button>
                        <button class="p-2 rounded-lg hover:bg-gray-100 transition-all">
                        <span class="material-symbols-outlined text-gray-600">
                        download
                        </span>
                        </button>
                     </div>
                  </div>
                  <!-- Table -->
                  <div class="overflow-x-auto">
                     <table class="w-full">
                        <thead class="bg-gray-50">
                           <tr>
                              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
                                 Receipt No.
                              </th>
                              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
                                 Date
                              </th>
                              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
                                 Amount
                              </th>
                              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">
                                 Status
                              </th>
                              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">
                                 Action
                              </th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                           <!-- Row -->
                           <tr class="hover:bg-gray-50 transition-all">
                              <td class="px-4 py-3 font-medium text-[#0B3B66]">
                                 RCPT/2023/1029
                              </td>
                              <td class="px-4 py-3 text-xs text-gray-600">
                                 12 Oct 2023
                              </td>
                              <td class="px-4 py-3 font-semibold text-green-700">
                                 ₹ 5,00,000
                              </td>
                              <td class="px-4 py-3">
                                 <span
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                 Paid
                                 </span>
                              </td>
                              <td class="px-4 py-3 text-right">
                                 <button
                                    class="inline-flex items-center gap-1 text-[#0B5CAD] hover:underline text-xs font-medium">
                                 <span class="material-symbols-outlined text-[18px]">
                                 download
                                 </span>
                                 Receipt
                                 </button>
                              </td>
                           </tr>
                           <!-- Row -->
                           <tr class="hover:bg-gray-50 transition-all">
                              <td class="px-4 py-3 font-medium text-[#0B3B66]">
                                 RCPT/2024/0245
                              </td>
                              <td class="px-4 py-3 text-xs text-gray-600">
                                 15 Jan 2024
                              </td>
                              <td class="px-4 py-3 font-semibold text-green-700">
                                 ₹ 4,50,000
                              </td>
                              <td class="px-4 py-3">
                                 <span
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                 Paid
                                 </span>
                              </td>
                              <td class="px-4 py-3 text-right">
                                 <button
                                    class="inline-flex items-center gap-1 text-[#0B5CAD] hover:underline text-xs font-medium">
                                 <span class="material-symbols-outlined text-[18px]">
                                 download
                                 </span>
                                 Receipt
                                 </button>
                              </td>
                           </tr>
                           <!-- Row -->
                           <tr class="hover:bg-gray-50 transition-all">
                              <td class="px-4 py-3 font-medium text-[#0B3B66]">
                                 RCPT/2024/0591
                              </td>
                              <td class="px-4 py-3 text-xs text-gray-600">
                                 20 May 2024
                              </td>
                              <td class="px-4 py-3 font-semibold text-green-700">
                                 ₹ 5,00,000
                              </td>
                              <td class="px-4 py-3">
                                 <span
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                 Paid
                                 </span>
                              </td>
                              <td class="px-4 py-3 text-right">
                                 <button
                                    class="inline-flex items-center gap-1 text-[#0B5CAD] hover:underline text-xs font-medium">
                                 <span class="material-symbols-outlined text-[18px]">
                                 download
                                 </span>
                                 Receipt
                                 </button>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
               <!-- Notifications -->
               <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 border-l-4 border-[#0B5CAD]">
                  <h3 class="text-lg font-bold text-[#0B3B66] mb-5">
                     System Notifications
                  </h3>
                  <div class="flex gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 items-start">
                     <div class="w-10 h-10 rounded-full bg-[#0B5CAD] text-white flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">
                        info
                        </span>
                     </div>
                     <div>
                        <p class="font-semibold text-[#0B3B66]">
                           Allotment Certificate Available
                        </p>
                        <p class="text-xs text-gray-600 mt-1 leading-6">
                           Your allotment certificate for Sector 12 project is now available in the downloads
                           section.
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                           2 hours ago
                        </p>
                     </div>
                  </div>
               </div>
            </div>
         </main>
         <!-- Premium Compact Footer -->
         <footer
            class="relative overflow-hidden bg-gradient-to-r from-[#0B2C4D] via-[#123B63] to-[#0B2C4D] text-white border-t border-white/10 mt-auto">
            <!-- Blur Effect -->
            <div
               class="absolute -top-10 right-0 w-40 h-40 bg-blue-400/10 rounded-full blur-3xl">
            </div>
            <div
               class="max-w-[1280px] mx-auto px-4 md:px-6 py-3 relative z-10">
               <div
                  class="flex flex-col md:flex-row items-center justify-between gap-3">
                  <!-- Left Side -->
                  <div class="text-center md:text-left">
                     <h4 class="text-sm font-semibold tracking-wide text-white">
                        Department of Housing For All
                     </h4>
                     <p class="text-[11px] text-blue-100 mt-1 leading-5">
                        Government of Haryana, India
                     </p>
                  </div>
                  <!-- Center -->
                  <div
                     class="hidden md:block w-px h-10 bg-white/10">
                  </div>
                  <!-- Right Side -->
                  <div class="text-center md:text-right leading-5">
                     <p class="text-[11px] text-blue-100">
                        Designed & Developed by
                        <span class="font-semibold text-white">
                        CRID Haryana
                        </span>
                     </p>
                     <p class="text-[11px] text-blue-100">
                        © 2026 All Rights Reserved
                     </p>
                  </div>
               </div>
            </div>
         </footer>
      </div>
   </body>
   <script>
      const menuToggle = document.getElementById('menuToggle');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      
      menuToggle.addEventListener('click', () => {
          sidebar.classList.toggle('-translate-x-full');
          overlay.classList.toggle('hidden');
      });
      
      overlay.addEventListener('click', () => {
          sidebar.classList.add('-translate-x-full');
          overlay.classList.add('hidden');
      });
      
      
      
      
      const customRadio = document.getElementById('customRadio');
      const customAmountBox = document.getElementById('customAmountBox');
      const allRadios = document.querySelectorAll('input[name="pay"]');
      
      allRadios.forEach(radio => {
      
          radio.addEventListener('change', function () {
      
              if (customRadio.checked) {
      
                  customAmountBox.classList.remove('hidden');
      
              } else {
      
                  customAmountBox.classList.add('hidden');
      
              }
      
          });
      
      });
      
      
      
      
   </script>
</html>