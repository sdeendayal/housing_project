@extends('layouts.app')

@section('title', 'About Us - Organisation Chart')

@section('content')

    {{-- Main Content --}}
    <main class="lg:col-span-2">
        <section class="bg-[#eef2f7] py-10 overflow-x-auto">
            <div class="min-w-[1250px] flex flex-col items-center text-[12px]">
                <div class="text-center mb-10">
                    <div
                        class="inline-flex items-center gap-2 bg-blue-100 text-civic-blue px-4 py-1.5 rounded-full text-xs font-semibold mb-3">
                        <span class="material-symbols-outlined text-[16px]">
                            account_tree
                        </span>
                        Organisation Chart
                    </div>
                </div>

                <!-- Department -->
                <div
                    class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl shadow-md px-10 py-3 w-[420px] text-center whitespace-nowrap">

                    <h2 class="font-semibold text-[18px]">
                        Department of Housing For All
                    </h2>

                </div>

                <!-- Line -->
                <div class="w-[2px] h-8 bg-red-500"></div>
                <!-- Minister -->
                <!-- Minister -->
                <div
                    class="bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-2xl shadow-lg px-5 py-4 w-[420px] text-center whitespace-nowrap">

                    <h3 class="font-semibold text-[14px]">
                        Minister-In-Charge - Dr. Kamal Gupta
                    </h3>

                </div>

                <!-- Line -->
                <div class="w-[2px] h-8 bg-blue-500"></div>
                <!-- Secretary -->
                <div
                    class="bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-2xl shadow-lg px-5 py-4 w-[520px] text-center whitespace-nowrap">

                    <h3 class="font-semibold text-[14px]">
                        Administrative Secretary - Dr. Raja Sekhar Vundru, I.A.S.
                    </h3>

                </div>

                <!-- Connector -->
                <div class="relative w-[650px] h-[70px]">

                    <!-- center line -->
                    <div class="absolute left-1/2 top-0 w-[2px] h-[30px] bg-blue-500"></div>

                    <!-- horizontal -->
                    <div class="absolute top-[30px] left-[90px] w-[470px] h-[2px] bg-blue-500"></div>

                    <!-- vertical left -->
                    <div class="absolute left-[90px] top-[30px] w-[2px] h-[40px] bg-blue-500"></div>

                    <!-- vertical right -->
                    <div class="absolute right-[90px] top-[30px] w-[2px] h-[40px] bg-blue-500"></div>
                </div>

                <!-- Main Branches -->
                <div class="flex gap-28">

                    <!-- LEFT SIDE -->
                    <div class="flex flex-col items-center">

                        <!-- Heading -->
                        <div
                            class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-xl shadow-md px-8 py-3 w-[290px] text-center font-semibold text-[13px]">
                            Directorate of Housing For All
                        </div>

                        <div class="w-[2px] h-6 bg-blue-500"></div>

                        <!-- DG -->
                        <div class="bg-white border border-blue-100 rounded-2xl shadow-md px-5 py-4 text-center w-[290px]">
                            <h4 class="font-semibold text-[14px] text-blue-900">
                                Director General
                            </h4>
                            <p class="text-slate-600 text-[12px] mt-1">
                                Sh. Ajit Balaji Joshi, I.A.S
                            </p>
                        </div>

                        <div class="w-[2px] h-6 bg-blue-500"></div>

                        <!-- Joint Director -->
                        <div class="bg-white border border-blue-100 rounded-2xl shadow-md px-5 py-4 text-center w-[290px]">
                            <h4 class="font-semibold text-[14px] text-blue-900">
                                Joint Director
                            </h4>
                            <p class="text-slate-600 text-[12px] mt-1">
                                Sh. Rakesh Sandhu, H.C.S.
                            </p>
                        </div>

                        <!-- Bottom connector -->
                        <div class="relative w-[320px] h-[50px]">

                            <div class="absolute left-1/2 top-0 w-[2px] h-[20px] bg-blue-500"></div>

                            <div class="absolute top-[20px] left-[35px] w-[250px] h-[2px] bg-blue-500"></div>

                            <div class="absolute left-[35px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                            <div class="absolute left-1/2 top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                            <div class="absolute right-[35px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>

                        </div>

                        <!-- Officers -->
                        <div class="flex gap-4">

                            <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[110px] text-center">
                                <h5 class="font-semibold text-[13px] text-blue-900">
                                    ATP
                                </h5>
                                <p class="text-[11px] text-slate-600 mt-1">
                                    Sh. Aman Godara
                                </p>
                            </div>

                            <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[110px] text-center">
                                <h5 class="font-semibold text-[13px] text-blue-900">
                                    Coordinator
                                </h5>
                                <p class="text-[11px] text-slate-600 mt-1">
                                    Sh. Devender
                                </p>
                            </div>

                            <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[110px] text-center">
                                <h5 class="font-semibold text-[13px] text-blue-900">
                                    A.O/Supdt.
                                </h5>
                                <p class="text-[11px] text-slate-600 mt-1">
                                    Sh. Dev Kant Sharma
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="flex flex-col items-center">

                        <div
                            class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-xl shadow-md px-8 py-3 w-[290px] text-center font-semibold text-[13px]">
                            Housing Board Haryana
                        </div>

                        <div class="w-[2px] h-6 bg-blue-500"></div>

                        <div class="bg-white border border-blue-100 rounded-2xl shadow-md px-5 py-4 text-center w-[290px]">
                            <h4 class="font-semibold text-[14px] text-blue-900">
                                Chief Administrator
                            </h4>
                            <p class="text-slate-600 text-[12px] mt-1">
                                Sh. Ajit Balaji Joshi, I.A.S
                            </p>
                        </div>

                        <div class="w-[2px] h-6 bg-blue-500"></div>

                        <div class="bg-white border border-blue-100 rounded-2xl shadow-md px-5 py-4 text-center w-[290px]">
                            <h4 class="font-semibold text-[14px] text-blue-900">
                                Secretary
                            </h4>
                            <p class="text-slate-600 text-[12px] mt-1">
                                Sh. Rakesh Sandhu, H.C.S.
                            </p>
                        </div>

                        <!-- Bottom connector -->
                        <div class="relative w-[470px] h-[50px]">

                            <div class="absolute left-1/2 top-0 w-[2px] h-[20px] bg-blue-500"></div>

                            <div class="absolute top-[20px] left-[25px] w-[420px] h-[2px] bg-blue-500"></div>

                            <div class="absolute left-[25px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                            <div class="absolute left-[155px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                            <div class="absolute right-[155px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>
                            <div class="absolute right-[25px] top-[20px] w-[2px] h-[30px] bg-blue-500"></div>

                        </div>

                        <!-- Officers -->
                        <div class="flex gap-3">

                            <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[105px] text-center">
                                <h5 class="font-semibold text-[13px] text-blue-900">Chief Engineer</h5>
                                <p class="text-[11px] text-slate-600">Sh. Kabul Singh</p>
                            </div>

                            <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[105px] text-center">
                                <h5 class="font-semibold text-[13px] text-blue-900">CRO (PM)</h5>
                                <p class="text-[11px] text-slate-600">Sh. Lalit</p>
                            </div>

                            <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[105px] text-center">
                                <h5 class="font-semibold text-[13px] text-blue-900">CAO</h5>
                                <p class="text-[11px] text-slate-600">Sh. Chander Mohan</p>
                            </div>

                            <div class="bg-white border border-blue-100 rounded-xl shadow p-3 w-[105px] text-center">
                                <h5 class="font-semibold text-[13px] text-blue-900">STP</h5>
                                <p class="text-[11px] text-slate-600">Sh. Satish Punia</p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </section>
    </main>
    {{-- Right Sidebar --}}
    @include('partials.rightSidebar')
@endsection
