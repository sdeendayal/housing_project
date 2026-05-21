@extends('layouts.auth')

@section('title', 'MMSAY Login')

@section('content')

<main class="flex-grow flex items-center justify-center px-4 py-4 relative overflow-hidden">

    <!-- Background -->
    <div class="absolute inset-0 z-0 bg-slate-100"></div>

    <div
        class="relative z-10 w-full max-w-6xl bg-white rounded-[28px] overflow-hidden shadow-2xl border border-slate-200 grid grid-cols-1 lg:grid-cols-2 min-h-[560px]">

        <!-- LEFT SIDE -->
        <div
            class="relative bg-gradient-to-br from-[#003358] via-[#004a7c] to-[#003358] text-white px-10 py-10 flex flex-col justify-between overflow-hidden">

            <!-- Decorative -->
            <div
                class="absolute -top-20 -left-20 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="relative z-10 space-y-8">

                <!-- Logo + Title -->
                <div class="flex items-start gap-5">

                    <div
                        class="w-24 h-24 rounded-[28px] bg-white/10 border border-white/10 flex items-center justify-center shadow-lg">

                        <span class="material-symbols-outlined text-[48px] text-white">
                            person
                        </span>
                    </div>

                    <div class="pt-2">

                        <h1 class="text-[30px] font-bold leading-tight">
                            Haryana Housing
                        </h1>

                        <p class="text-[18px] text-slate-200 font-medium">
                            Citizen Portal
                        </p>

                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-4 max-w-md">

                    <p class="text-[14px] leading-7 text-slate-200">
                        Secure citizen login portal for accessing housing schemes,
                        applications and beneficiary services.
                    </p>

                </div>

                <!-- Features -->
                <div class="space-y-5 pt-2">

                    <!-- Secure Login -->
                    <div class="flex items-center gap-4">

                        <div
                            class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center">

                            <span class="material-symbols-outlined text-green-300 text-[28px]">
                                verified_user
                            </span>
                        </div>

                        <div>
                            <h3 class="font-semibold text-[15px]">
                                Secure Login
                            </h3>

                            <p class="text-slate-300 text-xs">
                                Protected citizen access
                            </p>
                        </div>

                    </div>

                    <!-- OTP -->
                    <div class="flex items-center gap-4">

                        <div
                            class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center">

                            <span class="material-symbols-outlined text-yellow-300 text-[28px]">
                                sms
                            </span>
                        </div>

                        <div>
                            <h3 class="font-semibold text-[15px]">
                                OTP Based Login
                            </h3>

                            <p class="text-slate-300 text-xs">
                                Mobile OTP verification system
                            </p>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <!-- RIGHT SIDE LOGIN -->
        <div class="bg-slate-50 px-12 py-10 flex flex-col justify-center">

            <!-- Header -->
            <div class="flex items-center gap-4 mb-8">

                <div
                    class="w-20 h-20 rounded-[24px] bg-blue-100 flex items-center justify-center shadow-sm">

                    <span class="material-symbols-outlined text-[36px] text-blue-800">
                        account_circle
                    </span>
                </div>

                <div>

                    <h2 class="text-[26px] font-bold text-slate-800 leading-tight">
                        Citizen Login
                    </h2>

                    <p class="text-slate-500 text-[14px]">
                        Official secure citizen portal
                    </p>

                </div>

            </div>

            <form action="/mmsay.citizen.dashboard" method="GET"
                class="space-y-5">

                @csrf

                <!-- Mobile -->
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Mobile Number
                    </label>

                    <div class="flex">

                        <span
                            class="px-4 flex items-center bg-slate-200 border border-slate-300 border-r-0 rounded-l-xl text-slate-700 h-[48px]">
                            +91
                        </span>

                        <input type="text"
                            name="mobile"
                            required
                            placeholder="Enter Mobile Number"
                            class="w-full h-[48px] rounded-r-xl border border-slate-300 bg-white px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                </div>

                <!-- OTP -->
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        OTP Verification
                    </label>

                    <div class="flex gap-3">

                        <input type="text"
                            name="otp"
                            required
                            placeholder="Enter OTP"
                            class="flex-1 h-[48px] rounded-xl border border-slate-300 bg-white px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none">

                        <button type="button"
                            class="px-5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium text-sm whitespace-nowrap">
                            Send OTP
                        </button>

                    </div>

                </div>

                <!-- Captcha -->
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Captcha Verification
                    </label>

                    <div class="flex gap-3">

                        <div
                            class="h-[48px] min-w-[130px] px-5 rounded-xl bg-slate-200 border border-slate-300 flex items-center justify-center text-[20px] font-bold tracking-[4px] text-slate-700 italic select-none">
                            7XK92
                        </div>

                        <input type="text"
                            placeholder="Enter Captcha"
                            class="flex-1 h-[48px] rounded-xl border border-slate-300 bg-white px-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none">

                    </div>

                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full h-[48px] rounded-xl bg-[#003358] hover:bg-[#004a7c] text-white font-semibold text-sm transition-all shadow-md flex items-center justify-center gap-2">

                    <span>Verify & Login</span>

                    <span class="material-symbols-outlined text-[18px]">
                        arrow_forward
                    </span>

                </button>

            </form>

        </div>

    </div>

</main>
@endsection