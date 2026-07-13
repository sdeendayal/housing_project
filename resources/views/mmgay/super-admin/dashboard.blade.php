@extends('layouts.mmgayAdmin')

@section('title', 'Super Admin Dashboard')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

        {{-- 📊 KPI CARDS WRAPPER --}}
        <div class="w-full bg-[#111827] rounded-2xl p-4 sm:p-5 shadow-xl mb-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-7 gap-4 w-full">
                <!-- Districts -->
                <a href="{{ route('superadmin.districts') }}"
                    class="block bg-[#1F2937] rounded-xl p-4 text-center border border-gray-700 shadow transition-all duration-300 hover:shadow-blue-500/20 hover:-translate-y-1">
                    <div class="mx-auto w-10 h-10 flex items-center justify-center rounded-full bg-blue-500/20 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <p class="text-xs text-gray-400">Districts</p>
                    <h2 class="text-2xl font-bold text-blue-400">{{ $summary->TotalDistricts }}</h2>
                </a>

                <!-- Villages -->
                <a href="{{ route('superadmin.all-villages') }}"
                    class="block bg-[#1F2937] rounded-xl p-4 text-center border border-gray-700 shadow transition-all duration-300 hover:shadow-green-500/20 hover:-translate-y-1">
                    <div class="mx-auto w-10 h-10 flex items-center justify-center rounded-full bg-green-500/20 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 4l9 5.75V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.75z" /></svg>
                    </div>
                    <p class="text-xs text-gray-400">Villages</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-green-400">{{ $summary->TotalVillages }}</h2>
                </a>

                <!-- Beneficiaries -->
                <a href="{{ route('superadmin.beneficiaries.index') }}"
                    class="block bg-[#1F2937] rounded-xl p-4 text-center border border-gray-700 shadow transition-all duration-300 hover:shadow-indigo-500/20 hover:-translate-y-1">
                    <div class="mx-auto w-10 h-10 flex items-center justify-center rounded-full bg-indigo-500/20 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-3.13a4 4 0 10-8 0 4 4 0 008 0z" /></svg>
                    </div>
                    <p class="text-xs text-gray-400">Beneficiaries</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-indigo-400">{{ $summary->TotalBeneficiaries }}</h2>
                </a>

                <!-- Allotment -->
                <a href="{{ route('superadmin.allotment.index') }}"
                    class="block bg-[#1F2937] rounded-xl p-4 text-center border border-gray-700 shadow transition-all duration-300 hover:shadow-orange-500/20 hover:-translate-y-1">
                    <div class="mx-auto w-10 h-10 flex items-center justify-center rounded-full bg-orange-500/20 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" /></svg>
                    </div>
                    <p class="text-xs text-gray-400">Allotment</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-orange-400">{{ $summary->TotalAllotment }}</h2>
                </a>

                <!-- Assigned Flats -->
                <a href="{{ route('superadmin.assigned.flats') }}"
                    class="block bg-[#1F2937] rounded-xl p-4 text-center border border-gray-700 shadow transition-all duration-300 hover:shadow-purple-500/20 hover:-translate-y-1 cursor-pointer">
                    <div class="mx-auto w-10 h-10 flex items-center justify-center rounded-full bg-purple-500/20 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 20h14M12 3l9 7-9 7-9-7 9-7z" /></svg>
                    </div>
                    <p class="text-xs text-gray-400">Assigned Flats</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-purple-400">{{ $summary->TotalAssignedFlats }}</h2>
                </a>

                <!-- Paid -->
                <a href="{{ route('superadmin.paid.beneficiaries') }}"
                    class="block bg-[#1F2937] rounded-xl p-4 text-center border border-gray-700 shadow transition-all duration-300 hover:shadow-emerald-500/20 hover:-translate-y-1 cursor-pointer">
                    <div class="mx-auto w-10 h-10 flex items-center justify-center rounded-full bg-emerald-500/20 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-xs text-gray-400">Paid</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-emerald-400">{{ $summary->TotalPaid }}</h2>
                </a>

                <!-- Not Paid -->
                <div class="bg-[#1F2937] rounded-xl p-4 text-center border border-gray-700 shadow transition-all duration-300 hover:shadow-red-500/20 hover:-translate-y-1">
                    <div class="mx-auto w-10 h-10 flex items-center justify-center rounded-full bg-red-500/20 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </div>
                    <p class="text-xs text-gray-400">Not Paid</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-red-400">{{ $summary->TotalNotPaid }}</h2>
                </div>
            </div>
        </div>

        {{-- 📉 ANALYSIS SECTION --}}
        <div class="w-full bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden mb-8">
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gradient-to-r from-slate-50 to-gray-100">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Paid vs Allotment Analysis</h2>
                    <p class="text-xs text-gray-500">Registration & Payment Summary</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">Live Statistics</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-5">
                    <!-- Allotment -->
                    <div class="group bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-2xl p-5 text-center hover:-translate-y-1 hover:shadow-lg transition">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M9 8h6M7 3h10l2 2v16H5V5l2-2z" /></svg>
                        </div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Allotment</p>
                        <h2 class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($summary->TotalAllotment) }}</h2>
                    </div>

                    <!-- Paid -->
                    <div class="group bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 rounded-2xl p-5 text-center hover:-translate-y-1 hover:shadow-lg transition">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" /><circle cx="12" cy="12" r="9" /></svg>
                        </div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Paid</p>
                        <h2 class="mt-2 text-3xl font-bold text-emerald-600">{{ number_format($summary->TotalPaid) }}</h2>
                    </div>

                    <!-- Pending -->
                    <div class="group bg-gradient-to-br from-red-50 to-white border border-red-100 rounded-2xl p-5 text-center hover:-translate-y-1 hover:shadow-lg transition">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4" /><circle cx="12" cy="17" r="1" /><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18A2 2 0 003.55 21h16.9a2 2 0 001.73-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                        </div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Pending</p>
                        <h2 class="mt-2 text-3xl font-bold text-red-600">{{ number_format($summary->TotalAllotment - $summary->TotalPaid) }}</h2>
                    </div>

                    <!-- Registration -->
                    <div class="group bg-gradient-to-br from-violet-50 to-white border border-violet-100 rounded-2xl p-5 text-center hover:-translate-y-1 hover:shadow-lg transition">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-violet-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="5" y="3" width="14" height="18" rx="2" /><path d="M9 8h6M9 12h6M9 16h4" /></svg>
                        </div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Registration</p>
                        <h2 class="mt-2 text-3xl font-bold text-violet-600">{{ number_format($registration->TotalRegistration) }}</h2>
                    </div>

                    <!-- Matched -->
                    <div class="group bg-gradient-to-br from-cyan-50 to-white border border-cyan-100 rounded-2xl p-5 text-center hover:-translate-y-1 hover:shadow-lg transition">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-cyan-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Matched</p>
                        <h2 class="mt-2 text-3xl font-bold text-cyan-600">{{ number_format($registration->Matched) }}</h2>
                    </div>

                    <!-- Unmatched -->
                    <div class="group bg-gradient-to-br from-amber-50 to-white border border-amber-100 rounded-2xl p-5 text-center hover:-translate-y-1 hover:shadow-lg transition">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12" /></svg>
                        </div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Unmatched</p>
                        <h2 class="mt-2 text-3xl font-bold text-amber-600">{{ number_format($registration->UnMatched) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        

    </main>

@endsection