@extends('layouts.mmgayBdoAuth')
@section('title', 'MMGAY BDO Dashboard')
@section('page_header', 'Dashboard')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex-1">


    <!-- Header Banner - Denser & Modern Gradient -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#0f2027] via-[#203a43] to-[#2c5364] shadow-md mb-4 py-4 px-6 border border-slate-700/10">
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
        <div class="relative flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                    <span class="material-symbols-outlined text-white text-xl">location_on</span>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight">
                        {{ strtoupper($bdo->district_name ?? 'Haryana') }} District BDO Panel
                    </h2>
                    <p class="text-[10px] text-slate-300 font-semibold uppercase mt-0.5">Block Development Officer • Mukhyamantri Gramin Awas Yojana</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md border border-white/15 rounded-lg px-3 py-1.5 shadow-sm text-xs font-bold">
                <span class="material-symbols-outlined text-sm">calendar_month</span>
                <span>{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Stats Row - Tighter & More Densely Aligned -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2.5 mb-4">
        <!-- Total Eligible -->
        <a href="{{ route('mmgay.bdo.possession-applications') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-slate-300 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Total Eligible</p>
                <h2 class="text-lg font-extrabold text-slate-700 mt-0.5">{{ $stats['total_eligible'] }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-slate-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-slate-600 text-base">groups</span>
            </div>
        </a>

        <!-- Schedule Pending -->
        <a href="{{ route('mmgay.bdo.eligibility-list') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-blue-200 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Schedule Pending</p>
                <h2 class="text-lg font-extrabold text-blue-700 mt-0.5">{{ $stats['not_scheduled'] }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-700 text-base">pending_actions</span>
            </div>
        </a>

        <!-- Citizen Confirmation Pending -->
        <a href="{{ route('mmgay.bdo.possession-applications', ['status' => 'Visit Scheduled']) }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-orange-200 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Awaiting Citizen</p>
                <h2 class="text-lg font-extrabold text-orange-700 mt-0.5">{{ $stats['awaiting_citizen'] }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-orange-700 text-base">contact_support</span>
            </div>
        </a>

        <!-- Field Visit Pending -->
        <a href="{{ route('mmgay.bdo.possession-applications', ['status' => 'Slot Selected']) }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-indigo-200 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Field Visit Pending</p>
                <h2 class="text-lg font-extrabold text-indigo-700 mt-0.5">{{ $stats['awaiting_coordinates'] }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-indigo-700 text-base">location_on</span>
            </div>
        </a>

        <!-- E-Possession Pending -->
        <a href="{{ route('mmgay.bdo.possession-applications', ['status' => 'Site Verified']) }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-amber-200 transition font-semibold">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">E-Possession Pending</p>
                <h2 class="text-lg font-extrabold text-amber-700 mt-0.5">{{ $stats['awaiting_bdo_doc'] }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-amber-700 text-base">description</span>
            </div>
        </a>

        <!-- Verified -->
        <a href="{{ route('mmgay.bdo.possession-applications', ['status' => 'Verified']) }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-emerald-200 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Verified</p>
                <h2 class="text-lg font-extrabold text-emerald-700 mt-0.5">{{ $stats['verified'] }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-emerald-700 text-base">verified</span>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Activity Logs Table - Denser spacing -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-slate-100">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-lg">article</span>
                    Recent Activity log
                </h3>
                <a href="{{ route('mmgay.bdo.possession-applications') }}" class="text-[10px] text-blue-600 font-bold hover:underline">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                            <th class="px-3 py-2 text-left">Sr.No.</th>
                            <th class="px-3 py-2 text-left">App Number</th>
                            <th class="px-3 py-2 text-left">Applicant</th>
                            <th class="px-3 py-2 text-left">Mobile</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentApplications as $app)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-3 py-1.5 font-bold text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-3 py-1.5 font-bold text-slate-800">{{ $app->application_number }}</td>
                                <td class="px-3 py-1.5 text-slate-700 font-medium">{{ $app->applicant_name }}</td>
                                <td class="px-3 py-1.5 font-mono text-slate-500 text-[11px]">{{ $app->mobile }}</td>
                                <td class="px-3 py-1.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase
                                        @if($app->physical_possession_status === 'Verified') bg-emerald-50 text-emerald-700 border border-emerald-100
                                        @elseif($app->physical_possession_status === 'Visit Scheduled') bg-orange-50 text-orange-700 border border-orange-100
                                        @elseif($app->physical_possession_status === 'Slot Selected') bg-indigo-50 text-indigo-700 border border-indigo-100
                                        @elseif($app->physical_possession_status === 'Rejected') bg-rose-50 text-rose-700 border border-rose-100
                                        @else bg-blue-50 text-blue-700 border border-blue-100
                                        @endif">
                                        {{ $app->physical_possession_status }}
                                    </span>
                                </td>
                                <td class="px-3 py-1.5 text-center">
                                    @if($app->physical_possession_status === 'Eligible for Physical Possession')
                                        <a href="{{ route('mmgay.bdo.schedule-form', $app->secure_id) }}" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-2 py-1 rounded-md font-extrabold transition">
                                            <span class="material-symbols-outlined text-[12px] font-bold">calendar_month</span> Schedule
                                        </a>
                                    @else
                                        <a href="{{ route('mmgay.bdo.verify-form', $app->secure_id) }}" class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] px-2 py-1 rounded-md font-extrabold transition">
                                            <span class="material-symbols-outlined text-[12px]">visibility</span> Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-slate-400 font-semibold">No recent applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Access panel - Compact & Clean -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 pb-2.5 border-b border-slate-100 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-blue-600 text-lg">bolt</span>
                Quick Access Actions
            </h3>
            <div class="space-y-2.5">
                <a href="{{ route('mmgay.bdo.eligibility-list') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-[#f8fafc] hover:bg-slate-100/80 transition font-bold text-xs text-slate-700 shadow-sm">
                    <span class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-blue-600 bg-blue-50 p-1.5 rounded-lg text-lg">event_available</span>
                        Awaiting Visit Schedule
                    </span>
                    <span class="material-symbols-outlined text-slate-400 text-lg">chevron_right</span>
                </a>
                <a href="{{ route('mmgay.bdo.possession-applications') }}?status=Slot+Selected" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-[#f8fafc] hover:bg-slate-100/80 transition font-bold text-xs text-slate-700 shadow-sm">
                    <span class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-indigo-600 bg-indigo-50 p-1.5 rounded-lg text-lg">rule</span>
                        Verify Slot Submissions
                    </span>
                    <span class="material-symbols-outlined text-slate-400 text-lg">chevron_right</span>
                </a>
                <a href="{{ route('mmgay.bdo.possession-applications') }}?status=Verified" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-[#f8fafc] hover:bg-slate-100/80 transition font-bold text-xs text-slate-700 shadow-sm">
                    <span class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-emerald-600 bg-emerald-50 p-1.5 rounded-lg text-lg">download</span>
                        Possession Reports List
                    </span>
                    <span class="material-symbols-outlined text-slate-400 text-lg">chevron_right</span>
                </a>
            </div>
        </div>
    </div>
</main>
@endsection
