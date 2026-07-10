@extends('layouts.mmgayCEOAuth')
@section('title', 'District CEO | Physical Possession Dashboard')

@section('content')

    <main class="ml-[260px] mt-16 min-h-screen bg-slate-100">

        <div class="p-6">

            <!-- Page Header -->
            <div
                class="w-full rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-blue-700 via-indigo-700 to-cyan-600">

                <div class="px-6 py-4 flex items-center justify-between">

                    <!-- Left -->
                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">

                            <span class="material-symbols-outlined text-white text-2xl">
                                home_work
                            </span>

                        </div>

                        <div>

                            <h1 class="text-xl font-bold text-white">
                                Physical Possession Dashboard
                            </h1>

                            <div class="flex items-center gap-2 text-sm text-blue-100">

                                <a href="{{ route('district.dashboard') }}" class="hover:text-white transition">

                                    Dashboard

                                </a>

                                <span>/</span>

                                <span class="font-medium text-white">

                                    Physical Possession

                                </span>

                            </div>

                        </div>

                    </div>

                    <!-- Right -->
                    <div class="hidden lg:flex items-center gap-4">

                        <!-- District -->
                        <div class="text-right">

                            <p class="text-[10px] uppercase tracking-wider text-blue-200">
                                District
                            </p>

                            <p class="text-sm font-semibold text-white">
                                {{ auth()->user()->district_name }}
                            </p>

                        </div>

                        <div class="h-8 w-px bg-white/30"></div>

                        <!-- Date -->
                        <div class="text-right">

                            <p class="text-[10px] uppercase tracking-wider text-blue-200">
                                Today
                            </p>

                            <p class="text-sm font-semibold text-white">
                                {{ now()->format('d M Y') }}
                            </p>

                        </div>

                        <!-- Panel Badge -->
                        <span
                            class="inline-flex items-center gap-2 bg-white/20 backdrop-blur border border-white/20 text-white px-3 py-1.5 rounded-full text-xs font-semibold">

                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>

                            District CEO Panel

                        </span>

                    </div>

                </div>

            </div>

            <!-- Space -->
            <div class="h-6"></div>

            <!-- ===========================
                                                                        Statistics Cards
                                                                    =========================== -->

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

                <!-- Total Applications -->
                <div
                    class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="p-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                                    Total Applications
                                </p>

                                <h2 class="text-3xl font-bold text-slate-800 mt-2">
                                    {{ number_format($totalApplications) }}
                                </h2>

                            </div>

                            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">

                                <span class="material-symbols-outlined text-indigo-600 text-2xl">
                                    description
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Visit Scheduled -->
                <div
                    class="bg-white rounded-xl border border-orange-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="p-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                                    Visit Scheduled
                                </p>

                                <h2 class="text-3xl font-bold text-orange-600 mt-2">
                                    {{ number_format($visitScheduled) }}
                                </h2>

                            </div>

                            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">

                                <span class="material-symbols-outlined text-orange-600 text-2xl">
                                    event_available
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Slot Selected -->
                <div
                    class="bg-white rounded-xl border border-blue-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="p-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                                    Slot Selected
                                </p>

                                <h2 class="text-3xl font-bold text-blue-600 mt-2">
                                    {{ number_format($slotSelected) }}
                                </h2>

                            </div>

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">

                                <span class="material-symbols-outlined text-blue-600 text-2xl">
                                    schedule
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Site Verified -->
                <div
                    class="bg-white rounded-xl border border-purple-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="p-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                                    Site Verified
                                </p>

                                <h2 class="text-3xl font-bold text-purple-600 mt-2">
                                    {{ number_format($siteVerified) }}
                                </h2>

                            </div>

                            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">

                                <span class="material-symbols-outlined text-purple-600 text-2xl">
                                    verified_user
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Verified -->
                <div
                    class="bg-white rounded-xl border border-emerald-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="p-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                                    Verified
                                </p>

                                <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                                    {{ number_format($verified) }}
                                </h2>

                            </div>

                            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">

                                <span class="material-symbols-outlined text-emerald-600 text-2xl">
                                    task_alt
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="h-6"></div>

            <!-- ===============================
                                                                    Status Analytics
                                                                ================================ -->

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- Progress Section -->
                <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm">

                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">

                        <div>
                            <h2 class="text-lg font-bold text-slate-800">
                                Physical Possession Progress
                            </h2>

                            <p class="text-sm text-slate-500">
                                Current application workflow
                            </p>
                        </div>

                        <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">
                            Live Status
                        </span>

                    </div>

                    <div class="p-5 space-y-5">

                        {{-- Visit Scheduled --}}
                        <div>

                            <div class="flex justify-between items-center mb-2">

                                <div class="flex items-center gap-2">

                                    <span class="material-symbols-outlined text-orange-500 text-[20px]">
                                        event_available
                                    </span>

                                    <span class="font-semibold text-slate-700">
                                        Visit Scheduled
                                    </span>

                                </div>

                                <div class="text-right">

                                    <span class="font-bold text-orange-600">
                                        {{ $visitScheduled }}
                                    </span>

                                    <span class="text-xs text-slate-400">
                                        ({{ $totalApplications ? round(($visitScheduled / $totalApplications) * 100) : 0 }}%)
                                    </span>

                                </div>

                            </div>

                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">

                                <div class="h-full rounded-full bg-orange-500"
                                    style="width:{{ $totalApplications ? ($visitScheduled / $totalApplications) * 100 : 0 }}%">
                                </div>

                            </div>

                        </div>


                        {{-- Slot Selected --}}
                        <div>

                            <div class="flex justify-between items-center mb-2">

                                <div class="flex items-center gap-2">

                                    <span class="material-symbols-outlined text-blue-500 text-[20px]">
                                        schedule
                                    </span>

                                    <span class="font-semibold text-slate-700">
                                        Slot Selected
                                    </span>

                                </div>

                                <div>

                                    <span class="font-bold text-blue-600">
                                        {{ $slotSelected }}
                                    </span>

                                    <span class="text-xs text-slate-400">
                                        ({{ $totalApplications ? round(($slotSelected / $totalApplications) * 100) : 0 }}%)
                                    </span>

                                </div>

                            </div>

                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">

                                <div class="h-full rounded-full bg-blue-500"
                                    style="width:{{ $totalApplications ? ($slotSelected / $totalApplications) * 100 : 0 }}%">
                                </div>

                            </div>

                        </div>


                        {{-- Site Verified --}}
                        <div>

                            <div class="flex justify-between items-center mb-2">

                                <div class="flex items-center gap-2">

                                    <span class="material-symbols-outlined text-purple-500 text-[20px]">
                                        verified_user
                                    </span>

                                    <span class="font-semibold text-slate-700">
                                        Site Verified
                                    </span>

                                </div>

                                <div>

                                    <span class="font-bold text-purple-600">
                                        {{ $siteVerified }}
                                    </span>

                                    <span class="text-xs text-slate-400">
                                        ({{ $totalApplications ? round(($siteVerified / $totalApplications) * 100) : 0 }}%)
                                    </span>

                                </div>

                            </div>

                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">

                                <div class="h-full rounded-full bg-purple-500"
                                    style="width:{{ $totalApplications ? ($siteVerified / $totalApplications) * 100 : 0 }}%">
                                </div>

                            </div>

                        </div>


                        {{-- Verified --}}
                        <div>

                            <div class="flex justify-between items-center mb-2">

                                <div class="flex items-center gap-2">

                                    <span class="material-symbols-outlined text-emerald-500 text-[20px]">
                                        task_alt
                                    </span>

                                    <span class="font-semibold text-slate-700">
                                        Verified
                                    </span>

                                </div>

                                <div>

                                    <span class="font-bold text-emerald-600">
                                        {{ $verified }}
                                    </span>

                                    <span class="text-xs text-slate-400">
                                        ({{ $totalApplications ? round(($verified / $totalApplications) * 100) : 0 }}%)
                                    </span>

                                </div>

                            </div>

                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">

                                <div class="h-full rounded-full bg-emerald-500"
                                    style="width:{{ $totalApplications ? ($verified / $totalApplications) * 100 : 0 }}%">
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Status Summary -->

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                    <div class="px-5 py-4 border-b border-slate-200">

                        <h2 class="font-bold text-slate-800">
                            Status Summary
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            Overall possession statistics
                        </p>

                    </div>

                    <div class="p-5 space-y-3">

                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">

                            <span>Total Applications</span>

                            <span class="font-bold text-slate-800">
                                {{ $totalApplications }}
                            </span>

                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-orange-50">

                            <span class="text-orange-700">
                                Visit Scheduled
                            </span>

                            <span class="font-bold text-orange-700">
                                {{ $visitScheduled }}
                            </span>

                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50">

                            <span class="text-blue-700">
                                Slot Selected
                            </span>

                            <span class="font-bold text-blue-700">
                                {{ $slotSelected }}
                            </span>

                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-purple-50">

                            <span class="text-purple-700">
                                Site Verified
                            </span>

                            <span class="font-bold text-purple-700">
                                {{ $siteVerified }}
                            </span>

                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50">

                            <span class="text-emerald-700">
                                Verified
                            </span>

                            <span class="font-bold text-emerald-700">
                                {{ $verified }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

            <div class="h-6"></div>

            <!-- =======================================
                                                                    Recent Applications
                                                            ======================================= -->

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <div
                    class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">

                    <!-- Left -->
                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">

                            <span class="material-symbols-outlined text-indigo-600 text-[20px]">
                                folder_shared
                            </span>

                        </div>

                        <div>

                            <h2 class="text-lg font-bold text-slate-800 leading-none">
                                Recent Applications
                            </h2>

                            <p class="text-xs text-slate-500 mt-1">
                                Latest Physical Possession Applications
                            </p>

                        </div>

                    </div>

                    <!-- Right -->
                    <div class="flex items-center gap-2">

                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">

                            <span class="material-symbols-outlined text-[16px]">
                                inventory_2
                            </span>

                            {{ $recentApplications->count() }} Records

                        </span>

                    </div>

                </div>
                <!-- ===========================================================
                                                            Filters Section
                                                        =========================================================== -->

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-200">

                        <div class="flex items-center gap-2">

                            <span class="material-symbols-outlined text-indigo-600 text-[22px]">
                                filter_alt
                            </span>

                            <div>

                                <h2 class="text-lg font-bold text-slate-800">
                                    Search & Filters
                                </h2>

                                <p class="text-xs text-slate-500">
                                    Search applications using filters
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Form -->

                    <form method="GET" action="{{ route('district.possession.dashboard') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4 p-5 items-end">

                            <!-- Application -->

                            <div>

                                <label class="block mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                    Application No
                                </label>

                                <input type="text" name="application_number"
                                    value="{{ request('application_number') }}" placeholder="APP0001"
                                    class="w-full h-11 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            <!-- Mobile -->

                            <div>

                                <label class="block mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                    Mobile
                                </label>

                                <input type="text" name="mobile" value="{{ request('mobile') }}"
                                    placeholder="98XXXXXXXX"
                                    class="w-full h-11 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            <!-- Status -->

                            <div>

                                <label class="block mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                    Status
                                </label>

                                <select name="status"
                                    class="w-full h-11 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                                    <option value="">All Status</option>

                                    <option value="Visit Scheduled"
                                        {{ request('status') == 'Visit Scheduled' ? 'selected' : '' }}>
                                        Visit Scheduled
                                    </option>

                                    <option value="Slot Selected"
                                        {{ request('status') == 'Slot Selected' ? 'selected' : '' }}>
                                        Slot Selected
                                    </option>

                                    <option value="Site Verified"
                                        {{ request('status') == 'Site Verified' ? 'selected' : '' }}>
                                        Site Verified
                                    </option>

                                    <option value="Verified" {{ request('status') == 'Verified' ? 'selected' : '' }}>
                                        Verified
                                    </option>

                                </select>

                            </div>

                            <!-- From -->

                            <div>

                                <label class="block mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                    From Date
                                </label>

                                <input type="date" name="from_date" value="{{ request('from_date') }}"
                                    class="w-full h-11 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            <!-- To -->

                            <div>

                                <label class="block mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                    To Date
                                </label>

                                <input type="date" name="to_date" value="{{ request('to_date') }}"
                                    class="w-full h-11 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            <!-- Buttons -->

                            <div class="flex items-center gap-2">

                                <button type="submit"
                                    class="h-11 w-11 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center transition shadow-sm">

                                    <span class="material-symbols-outlined text-[20px]">
                                        search
                                    </span>

                                </button>

                                <a href="{{ route('district.possession.dashboard') }}"
                                    class="h-11 w-11 rounded-xl bg-slate-200 hover:bg-slate-300 flex items-center justify-center transition">

                                    <span class="material-symbols-outlined text-[20px]">
                                        refresh
                                    </span>

                                </a>

                                <a href="#"
                                    class="h-11 w-11 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition shadow-sm">

                                    <span class="material-symbols-outlined text-[20px]">
                                        download
                                    </span>

                                </a>

                            </div>

                        </div>

                    </form>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-100">

                            <tr>

                                <th
                                    class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-600 w-14">
                                    #
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-600">
                                    Application No
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-600">
                                    Applicant
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-600">
                                    Mobile
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-600">
                                    Status
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-600 w-36">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">

                            @forelse($recentApplications as $row)
                                <tr class="hover:bg-indigo-50 transition duration-200">

                                    <!-- Sr -->

                                    <td class="px-5 py-3">

                                        <span
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-700">

                                            {{ $loop->iteration }}

                                        </span>

                                    </td>

                                    <!-- Application -->

                                    <td class="px-5 py-3">

                                        <div class="font-semibold text-indigo-700">

                                            {{ $row->application_number }}

                                        </div>

                                    </td>

                                    <!-- Applicant -->

                                    <td class="px-5 py-3">

                                        <div class="font-semibold text-slate-800">

                                            {{ $row->applicant_name }}

                                        </div>

                                    </td>

                                    <!-- Mobile -->

                                    <td class="px-5 py-3 text-slate-600">

                                        {{ $row->mobile }}

                                    </td>

                                    <!-- Status -->

                                    <td class="px-5 py-3 text-center">

                                        @switch($row->physical_possession_status)
                                            @case('Visit Scheduled')
                                                <span
                                                    class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">

                                                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>

                                                    Visit Scheduled

                                                </span>
                                            @break

                                            @case('Slot Selected')
                                                <span
                                                    class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">

                                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>

                                                    Slot Selected

                                                </span>
                                            @break

                                            @case('Site Verified')
                                                <span
                                                    class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">

                                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>

                                                    Site Verified

                                                </span>
                                            @break

                                            @case('Verified')
                                                <span
                                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">

                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                                                    Verified

                                                </span>
                                            @break

                                            @default
                                                <span
                                                    class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">

                                                    {{ $row->physical_possession_status }}

                                                </span>
                                        @endswitch

                                    </td>

                                    <!-- Action -->

                                    <td class="px-5 py-3 text-center">

                                        <a href="{{ route('district.possession.view', $row->secure_id) }}"
                                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">

                                            <span class="material-symbols-outlined text-[18px]">
                                                visibility
                                            </span>

                                            View
                                        </a>

                                    </td>

                                </tr>

                                @empty

                                    <tr>

                                        <td colspan="6" class="py-16 text-center">

                                            <div class="flex flex-col items-center">

                                                <span class="material-symbols-outlined text-6xl text-slate-300">

                                                    inbox

                                                </span>

                                                <h4 class="mt-3 text-lg font-semibold text-slate-600">

                                                    No Applications Found

                                                </h4>

                                                <p class="mt-1 text-sm text-slate-400">

                                                    There are no physical possession applications available.

                                                </p>

                                            </div>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="h-6"></div>

                </div>

        </main>

    @endsection
