@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Payment Status',
    'activeNav' => 'payments',
])

@section('content')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2">
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
                     â‚¹ 14,50,000
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
                     â‚¹ 7,25,000
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
                                          â‚¹ 1,389
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
                                          â‚¹ 50,004
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
                                       â‚¹
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
                              â‚¹ 50,004
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
                                 â‚¹ 5,00,000
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
                                 â‚¹ 4,50,000
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
                                 â‚¹ 5,00,000
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
@endsection
