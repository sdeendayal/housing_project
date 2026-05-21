@extends('layouts.app')

@section('title', 'Help & Support')

@section('content')

    {{-- Main Content --}}
    <main class="lg:col-span-2">
        <section class="bg-[#eef3f9] py-14">
            <div class="max-w-7xl mx-auto px-4">

                <!-- Heading -->
                <div class="text-center mb-10">

                    <h2 class="text-[42px] font-bold text-[#0b3c74]">
                        Get in Touch
                    </h2>

                    <div class="flex justify-center items-center gap-3 mt-3">
                        <div class="w-16 h-[2px] bg-[#0f75c8] rounded"></div>
                        <div class="w-3 h-3 bg-[#0f75c8] rounded-full"></div>
                        <div class="w-16 h-[2px] bg-[#0f75c8] rounded"></div>
                    </div>

                    <p class="text-slate-600 text-[15px] mt-4">
                        We are here to help you
                    </p>

                </div>

                <!-- Cards -->
                <div class="bg-white rounded-[35px] shadow-lg border border-slate-200 p-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">

                        <!-- Address -->
                        <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                            <div class="flex justify-center mb-5">
                                <div class="contact-icon">
                                    <span class="material-symbols-outlined">
                                        location_on
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                                Address
                            </h3>

                            <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                            <p class="text-[13px] text-slate-600 leading-7">
                                C-15, Awas Bhawan,<br>
                                Sector 6,<br>
                                Panchkula, Haryana
                            </p>

                        </div>

                        <!-- Email -->
                        <!-- Email -->
                        <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                            <div class="flex justify-center mb-5">
                                <div class="contact-icon">
                                    <span class="material-symbols-outlined">
                                        mail
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                                Email ID
                            </h3>

                            <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                            <p class="text-[13px] text-slate-600 leading-6 mb-5">
                                admin-hfa[at]<br>
                                hry[dot]gov[dot]in
                            </p>

                            <!-- Button -->
                            <a href="mailto:admin-hfa@hry.gov.in"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white px-5 py-2 rounded-full text-[13px] font-medium shadow-md hover:scale-105 transition duration-300">

                                <span class="material-symbols-outlined text-[18px]">
                                    send
                                </span>

                                Email Us
                            </a>

                        </div>

                        <!-- Phone 1 -->
                        <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                            <div class="flex justify-center mb-5">
                                <div class="contact-icon">
                                    <span class="material-symbols-outlined">
                                        call
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                                Phone
                            </h3>

                            <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                            <p class="text-[16px] font-medium text-slate-700 mb-5">
                                0172-2585852
                            </p>

                            <a href="tel:01722585852"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white px-5 py-2 rounded-full text-[13px] font-medium shadow-md hover:scale-105 transition duration-300">

                                <span class="material-symbols-outlined text-[18px]">
                                    call
                                </span>

                                Call Now
                            </a>

                        </div>

                        <!-- Phone 2 -->
                        <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                            <div class="flex justify-center mb-5">
                                <div class="contact-icon">
                                    <span class="material-symbols-outlined">
                                        call
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                                Phone
                            </h3>

                            <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                            <p class="text-[16px] font-medium text-slate-700 mb-5">
                                0172-2568687
                            </p>

                            <a href="tel:01722568687"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white px-5 py-2 rounded-full text-[13px] font-medium shadow-md hover:scale-105 transition duration-300">

                                <span class="material-symbols-outlined text-[18px]">
                                    call
                                </span>

                                Call Now
                            </a>

                        </div>

                        <!-- Phone 3 -->
                        <!-- Phone 3 -->
                        <div class="contact-card bg-white border border-slate-200 rounded-[28px] p-6 text-center">

                            <div class="flex justify-center mb-5">
                                <div class="contact-icon">
                                    <span class="material-symbols-outlined">
                                        call
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-[18px] font-semibold text-[#0b3c74]">
                                Phone
                            </h3>

                            <div class="w-10 h-[2px] bg-[#0f75c8] mx-auto my-3"></div>

                            <p class="text-[16px] font-medium text-slate-700 mb-5">
                                0172-2567233
                            </p>

                            <a href="tel:01722567233"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white px-5 py-2 rounded-full text-[13px] font-medium shadow-md hover:scale-105 transition duration-300">

                                <span class="material-symbols-outlined text-[18px]">
                                    call
                                </span>

                                Call Now
                            </a>

                        </div>

                    </div>

                </div>
            </div>
        </section>
    </main>
    {{-- Right Sidebar --}}
    @include('partials.rightSidebar')
@endsection
