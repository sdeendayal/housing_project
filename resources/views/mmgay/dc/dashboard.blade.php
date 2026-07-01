@extends('layouts.mmgayDCAuth')

@section('title', 'DC Dashboard')

@section('content')

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        <!-- BEGIN: HeroBanner -->
        <section
            class="hero-gradient rounded-2xl p-5 mb-6 text-white relative overflow-hidden flex justify-between items-center"
            data-purpose="welcome-banner">

            <!-- Left Content -->
            <div class="relative z-10 max-w-xl">
                <h2 class="text-2xl font-semibold mb-1">
                    Welcome to MMGAY Portal
                </h2>

                <p class="text-sm text-blue-100 opacity-90 leading-snug">
                    Monitor and manage all scheme applications from your district efficiently. Track progress, review
                    submissions, and generate detailed reports.
                </p>
            </div>

            <!-- Logged-in User Box -->
            <div
                class="relative z-10 bg-white/20 backdrop-blur-md p-3 rounded-xl flex items-center space-x-3 border border-white/30">

                <div class="text-right">
                    <p class="text-xs font-semibold uppercase opacity-70">
                        Logged In User
                    </p>
                    <p class="text-sm font-bold">
                        {{ auth()->user()->name }}
                    </p>
                </div>

                <div class="w-10 h-10 bg-white text-blue-600 rounded-lg flex items-center justify-center">
                    <i class="w-5 h-5" data-lucide="user"></i>
                </div>
            </div>

            <!-- Decorative Icon -->
            <div class="absolute right-20 bottom-0 opacity-20 pointer-events-none">
                <i class="w-48 h-48" data-lucide="landmark"></i>
            </div>

        </section>
        <!-- END: HeroBanner -->
        <div class="flex gap-4 mb-6">

            <a href="{{ route('dc.dashboard', 1) }}"
                class="px-5 py-2 rounded-xl shadow font-semibold
        {{ $phase == 1 ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
                Phase 1
            </a>

            <a href="{{ route('dc.dashboard', 2) }}"
                class="px-5 py-2 rounded-xl shadow font-semibold
        {{ $phase == 2 ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
                Phase 2
            </a>

            <a href="{{ route('dc.dashboard', 3) }}"
                class="px-5 py-2 rounded-xl shadow font-semibold
        {{ $phase == 3 ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
                Phase 3
            </a>

        </div>
        <!-- BEGIN: MetricCards -->
        <section class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8" data-purpose="quick-metrics">
            <!-- Total Applications -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center">
                        <i class="w-5 h-5" data-lucide="file-text"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-400">Total Applications</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $total }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] text-slate-400 mb-1">All Time</p>
                    <canvas class="w-full h-8" id="sparkline-1"></canvas>
                </div>
            </div>
            <!-- Pending -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center">
                        <i class="w-5 h-5" data-lucide="clock"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-400">Pending</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $pending }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] text-emerald-500 flex items-center font-bold mb-1">14.47% <i class="w-3 h-3 ml-1"
                            data-lucide="trending-up"></i></p>
                    <canvas class="w-full h-8" id="sparkline-2"></canvas>
                </div>
            </div>
            <!-- Approved -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-lg flex items-center justify-center">
                        <i class="w-5 h-5" data-lucide="check-circle-2"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-400">Approved</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $approved }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] text-emerald-500 flex items-center font-bold mb-1">72.37% <i class="w-3 h-3 ml-1"
                            data-lucide="trending-up"></i></p>
                    <canvas class="w-full h-8" id="sparkline-3"></canvas>
                </div>
            </div>
            <!-- Rejected -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center">
                        <i class="w-5 h-5" data-lucide="alert-circle"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-400">Rejected</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $rejected }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] text-emerald-500 flex items-center font-bold mb-1">5.26% <i class="w-3 h-3 ml-1"
                            data-lucide="trending-up"></i></p>
                    <canvas class="w-full h-8" id="sparkline-4"></canvas>
                </div>
            </div>
            <!-- Paid -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-lg flex items-center justify-center">
                        <i class="w-5 h-5" data-lucide="wallet"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-400">Paid</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $paid }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] text-emerald-500 flex items-center font-bold mb-1">62.50% <i class="w-3 h-3 ml-1"
                            data-lucide="trending-up"></i></p>
                    <canvas class="w-full h-8" id="sparkline-5"></canvas>
                </div>
            </div>
            <!-- Reconsidered -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center">
                        <i class="w-5 h-5" data-lucide="refresh-cw"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-400">Reconsidered</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $reconsidered }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] text-emerald-500 flex items-center font-bold mb-1">4.61% <i class="w-3 h-3 ml-1"
                            data-lucide="trending-up"></i></p>
                    <canvas class="w-full h-8" id="sparkline-6"></canvas>
                </div>
            </div>
        </section>
        <!-- END: MetricCards -->

    </div>

@endsection
