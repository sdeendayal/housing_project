@extends('layouts.mmgayBdoAuth')
@section('title', 'MMGAY BDPO Owner Status Report')
@section('page_header', 'Owner Status Report')

@section('content')
<main class="ml-[260px] min-h-screen bg-[#f3f6fc] p-4 flex-1">
    <!-- Spacer to clear fixed top navbar -->
    <div style="height: 80px;" class="w-full shrink-0"></div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 rounded text-green-700 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded text-red-700 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-sm">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#1e3c72] to-[#2a5298] shadow-md mb-4 py-4 px-6 border border-slate-700/10">
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
        <div class="relative flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                    <span class="material-symbols-outlined text-white text-xl">assignment</span>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight">
                        {{ strtoupper($bdo->block_name ?? $bdo->district_name ?? 'Haryana') }} Block Beneficiary Status Report
                    </h2>
                    <p class="text-[10px] text-slate-300 font-semibold uppercase mt-0.5">Allotment Status Drilldown & Export • MMGAY</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('mmgay.bdo.owner-status-report.export.csv', ['status' => $activeTab, 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
                   class="flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-[11px] font-extrabold px-3 py-1.5 rounded-lg shadow-sm transition">
                    <span class="material-symbols-outlined text-sm">download</span>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('mmgay.bdo.owner-status-report.export.pdf', ['status' => $activeTab, 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
                   class="flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white text-[11px] font-extrabold px-3 py-1.5 rounded-lg shadow-sm transition">
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Status Tabs / Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
        <!-- Approved + Paid -->
        <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'approved_paid', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
           class="bg-white rounded-xl shadow-sm border {{ $activeTab === 'approved_paid' ? 'border-green-500 bg-green-50/20' : 'border-slate-100' }} p-3 flex items-center justify-between hover:shadow transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Approved + Paid</p>
                <h2 class="text-base font-extrabold text-green-700 mt-0.5">{{ $counts->approved_paid }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg {{ $activeTab === 'approved_paid' ? 'bg-green-100 text-green-700' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base">payments</span>
            </div>
        </a>

        <!-- Approved + Unpaid -->
        <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'approved_unpaid', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
           class="bg-white rounded-xl shadow-sm border {{ $activeTab === 'approved_unpaid' ? 'border-amber-500 bg-amber-50/20' : 'border-slate-100' }} p-3 flex items-center justify-between hover:shadow transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Approved + Unpaid</p>
                <h2 class="text-base font-extrabold text-amber-700 mt-0.5">{{ $counts->approved_unpaid }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg {{ $activeTab === 'approved_unpaid' ? 'bg-amber-100 text-amber-700' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base">money_off</span>
            </div>
        </a>

        <!-- Yet To Be Done -->
        <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'yet_to_be_done', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
           class="bg-white rounded-xl shadow-sm border {{ $activeTab === 'yet_to_be_done' ? 'border-indigo-500 bg-indigo-50/20' : 'border-slate-100' }} p-3 flex items-center justify-between hover:shadow transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Yet To Be Done</p>
                <h2 class="text-base font-extrabold text-indigo-700 mt-0.5">{{ $counts->yet_to_be_done }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg {{ $activeTab === 'yet_to_be_done' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base">pending</span>
            </div>
        </a>

        <!-- Rejected -->
        <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'rejected', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
           class="bg-white rounded-xl shadow-sm border {{ $activeTab === 'rejected' ? 'border-red-500 bg-red-50/20' : 'border-slate-100' }} p-3 flex items-center justify-between hover:shadow transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Rejected</p>
                <h2 class="text-base font-extrabold text-red-700 mt-0.5">{{ $counts->rejected }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg {{ $activeTab === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base">cancel</span>
            </div>
        </a>

        <!-- Cancelled -->
        <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'cancelled', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
           class="bg-white rounded-xl shadow-sm border {{ $activeTab === 'cancelled' ? 'border-slate-800 bg-slate-100' : 'border-slate-100' }} p-3 flex items-center justify-between hover:shadow transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Cancelled</p>
                <h2 class="text-base font-extrabold text-slate-800 mt-0.5">{{ $counts->cancelled }}</h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg {{ $activeTab === 'cancelled' ? 'bg-slate-800 text-white' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base">delete_forever</span>
            </div>
        </a>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-4">
        <form method="GET" action="{{ route('mmgay.bdo.owner-status-report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <input type="hidden" name="status" value="{{ $activeTab }}">
            
            <!-- Phase Filter -->
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Select Phase</label>
                <select name="phase" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-lg p-2 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
                    <option value="">All Phases</option>
                    @foreach($phases as $p)
                        <option value="{{ $p }}" {{ $selectedPhase == $p ? 'selected' : '' }}>Phase {{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Village Filter -->
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Select Village</label>
                <select name="village_id" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-lg p-2 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
                    <option value="">All Villages</option>
                    @foreach($villages as $v)
                        <option value="{{ $v->VillageId }}" {{ $selectedVillageId == $v->VillageId ? 'selected' : '' }}>{{ $v->VillageName }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Search Beneficiary</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Name, Mobile, Reg No..." 
                       class="w-full bg-slate-50 border border-slate-200 text-xs rounded-lg p-2 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 rounded-lg transition shadow-sm">
                    Filter
                </button>
                <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => $activeTab]) }}" 
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2 px-3 rounded-lg text-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Beneficiary List Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 py-3 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full 
                    @if($activeTab === 'approved_paid') bg-green-500
                    @elseif($activeTab === 'approved_unpaid') bg-amber-500
                    @elseif($activeTab === 'yet_to_be_done') bg-indigo-500
                    @elseif($activeTab === 'rejected') bg-red-500
                    @else bg-slate-800
                    @endif"></span>
                <span>List of Beneficiaries ({{ ucwords(str_replace('_', ' ', $activeTab)) }})</span>
            </h3>
            <span class="text-[10px] text-slate-400 font-bold">Showing {{ $owners->firstItem() ?? 0 }}-{{ $owners->lastItem() ?? 0 }} of {{ $owners->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                        <th class="px-3 py-2 text-center w-12">Sr.No.</th>
                        <th class="px-3 py-2">Reg Number</th>
                        <th class="px-3 py-2">Applicant Name</th>
                        <th class="px-3 py-2">Father/Husband</th>
                        <th class="px-3 py-2">Mobile</th>
                        <th class="px-3 py-2">Phase</th>
                        <th class="px-3 py-2">Village</th>
                        <th class="px-3 py-2">Flat No.</th>
                        <th class="px-3 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($owners as $owner)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-3 py-1.5 text-center font-bold text-slate-400">
                                {{ $loop->iteration + ($owners->currentPage() - 1) * $owners->perPage() }}
                            </td>
                            <td class="px-3 py-1.5 font-bold text-slate-800">
                                {{ $owner->RegistrationNo }}
                            </td>
                            <td class="px-3 py-1.5 font-semibold text-slate-700">
                                {{ $owner->OwnerName }}
                            </td>
                            <td class="px-3 py-1.5 text-slate-500">
                                {{ $owner->FatherHusbandName ?? '—' }}
                            </td>
                            <td class="px-3 py-1.5 font-mono text-slate-500">
                                {{ $owner->MobileNo }}
                            </td>
                            <td class="px-3 py-1.5">
                                <span class="bg-blue-50 border border-blue-100 text-blue-700 text-[10px] font-extrabold px-2 py-0.5 rounded whitespace-nowrap">Phase {{ $owner->Phase }}</span>
                            </td>
                            <td class="px-3 py-1.5 text-slate-600 font-semibold">
                                {{ $owner->VillageName }}
                            </td>
                            <td class="px-3 py-1.5 font-mono text-slate-500">
                                {{ $owner->FlatNo ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-1.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase
                                    @if($activeTab === 'approved_paid') bg-green-50 text-green-700 border border-green-100
                                    @elseif($activeTab === 'approved_unpaid') bg-amber-50 text-amber-700 border border-amber-100
                                    @elseif($activeTab === 'yet_to_be_done') bg-indigo-50 text-indigo-700 border border-indigo-100
                                    @elseif($activeTab === 'rejected') bg-red-50 text-red-700 border border-red-100
                                    @else bg-slate-800 text-white
                                    @endif">
                                    {{ str_replace('_', ' ', $activeTab) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-6 text-center text-slate-400 font-medium">
                                <span class="material-symbols-outlined text-2xl block mb-1 text-slate-300">folder_open</span>
                                No beneficiaries found under this status.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($owners->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/30">
                {{ $owners->links('partials.compact-pagination') }}
            </div>
        @endif
    </div>
</main>
@endsection
