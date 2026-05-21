@extends('layouts.auth')

@section('title', 'MMSAY Login')

@section('content')

<main class="flex-grow flex items-center justify-center px-4 py-4 bg-slate-100 overflow-hidden">

    <div class="w-full max-w-6xl bg-white rounded-[24px] shadow-2xl overflow-hidden grid md:grid-cols-2 min-h-[580px]">

        <!-- LEFT SIDE -->
        <div class="bg-gradient-to-br from-[#003c72] to-[#0a5ea8] p-8 text-white flex flex-col justify-between">

            <div>

                <!-- Logo + Title -->
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

                <!-- Description -->
                <div class="space-y-2 text-[14px] text-slate-200 leading-6">

                    <p>
                        Secure login portal for authorized Haryana Government Department officials.
                    </p>

                    <p>
                        Access housing schemes, applications and internal services.
                    </p>

                </div>

                <!-- Features -->
                <div class="mt-10 space-y-5">

                    <!-- Secure Login -->
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

                    <!-- OTP Login -->
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

        <!-- RIGHT SIDE -->
        <div class="bg-white px-10 py-8 flex items-center justify-center">

            <form action="/mmsay.department.dashboard" method="GET"
                class="w-full max-w-md">

                @csrf

                <!-- Header -->
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

                <!-- Department ID -->
                <div class="mb-4">

                    <label class="text-[13px] font-medium text-slate-700 block mb-2">
                        Department ID
                    </label>

                    <input name="department_id" required
                        class="w-full h-[44px] rounded-xl border border-slate-300 px-4 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none"
                        placeholder="Enter Official Department ID"
                        type="text">

                </div>

                <!-- Mobile -->
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

                <!-- OTP -->
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

                <!-- CAPTCHA -->
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

                <!-- Button -->
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

</main>
@endsection
