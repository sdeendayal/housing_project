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

    <!-- Overall Block Statistics -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider">Overall Block Statistics</h3>
                <p class="text-[9px] text-slate-450 font-bold uppercase tracking-wider mt-0.5">Live statistics of MMGAY beneficiaries in this Block</p>
            </div>
            <span class="text-[9px] bg-blue-50 border border-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-extrabold uppercase tracking-wider">Live</span>
        </div>        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Block Card -->
            <a href="{{ route('mmgay.bdo.dashboard') }}" class="flex items-center p-3 bg-gradient-to-r from-blue-50/50 to-white border border-slate-100 rounded-xl hover:shadow-sm transition">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mr-3 text-blue-600 shrink-0">
                    <span class="material-symbols-outlined text-lg">location_city</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] text-slate-400 uppercase font-black tracking-wider truncate">Block</p>
                    <h3 class="text-sm font-extrabold text-slate-800 truncate">{{ strtoupper($blockName) }}</h3>
                </div>
            </a>

            <!-- Villages Card -->
            <a href="{{ route('mmgay.bdo.villages-report') }}" class="flex items-center p-3 bg-gradient-to-r from-green-50/50 to-white border border-slate-100 rounded-xl hover:shadow-sm transition">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mr-3 text-green-600 shrink-0">
                    <span class="material-symbols-outlined text-lg">home</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] text-slate-400 uppercase font-black tracking-wider truncate">Villages</p>
                    <h3 class="text-sm font-extrabold text-slate-800">{{ number_format($totalVillagesCount) }}</h3>
                </div>
            </a>

            <!-- Applicants Card -->
            <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'total']) }}" class="flex items-center p-3 bg-gradient-to-r from-indigo-50/50 to-white border border-slate-100 rounded-xl hover:shadow-sm transition">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mr-3 text-indigo-600 shrink-0">
                    <span class="material-symbols-outlined text-lg">groups</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] text-slate-400 uppercase font-black tracking-wider truncate">Applicants</p>
                    <h3 class="text-sm font-extrabold text-slate-800">{{ number_format($totalApplicantsCount) }}</h3>
                </div>
            </a>

            <!-- Allotted Card -->
            <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'total']) }}" class="flex items-center p-3 bg-gradient-to-r from-orange-50/50 to-white border border-slate-100 rounded-xl hover:shadow-sm transition">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center mr-3 text-orange-600 shrink-0">
                    <span class="material-symbols-outlined text-lg">cottage</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] text-slate-400 uppercase font-black tracking-wider truncate">Allotted</p>
                    <h3 class="text-sm font-extrabold text-slate-800">{{ number_format($totalAllottedCount) }}</h3>
                </div>
            </a>
        </div>
    </div>

    <!-- Allotment Status -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider">Allotment Status</h3>
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Status of Allotted Beneficiaries</p>
            </div>
            <span class="text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-extrabold uppercase tracking-wider">
                {{ number_format($grossTotal) }} Records
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
            <!-- Total -->
            <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'total', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
               class="flex items-center p-2.5 bg-white border {{ $activeTab === 'total' ? 'border-blue-500 bg-blue-50/10' : 'border-slate-100' }} rounded-xl hover:shadow-sm transition">
                <div class="w-8 h-8 rounded-lg {{ $activeTab === 'total' ? 'bg-blue-100 text-blue-600' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center mr-2.5 shrink-0">
                    <span class="material-symbols-outlined text-base">format_list_bulleted</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[9px] uppercase text-slate-400 font-black tracking-wider leading-tight">Total</p>
                    <h3 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($grossTotal) }}</h3>
                </div>
            </a>

            <!-- Approved + Paid -->
            <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'approved_paid', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
               class="flex items-center p-2.5 bg-white border {{ $activeTab === 'approved_paid' ? 'border-green-500 bg-green-50/10' : 'border-slate-100' }} rounded-xl hover:shadow-sm transition">
                <div class="w-8 h-8 rounded-lg {{ $activeTab === 'approved_paid' ? 'bg-green-100 text-green-700' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center mr-2.5 shrink-0">
                    <span class="material-symbols-outlined text-base">payments</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[9px] uppercase text-slate-400 font-black tracking-wider leading-tight">Approved + Paid</p>
                    <h3 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($counts->approved_paid) }}</h3>
                </div>
            </a>

            <!-- Approved + Unpaid -->
            <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'approved_unpaid', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
               class="flex items-center p-2.5 bg-white border {{ $activeTab === 'approved_unpaid' ? 'border-amber-500 bg-amber-50/10' : 'border-slate-100' }} rounded-xl hover:shadow-sm transition">
                <div class="w-8 h-8 rounded-lg {{ $activeTab === 'approved_unpaid' ? 'bg-amber-100 text-amber-700' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center mr-2.5 shrink-0">
                    <span class="material-symbols-outlined text-base">money_off</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[9px] uppercase text-slate-400 font-black tracking-wider leading-tight">Approved + Unpaid</p>
                    <h3 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($counts->approved_unpaid) }}</h3>
                </div>
            </a>

            <!-- Yet to be Approved -->
            <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'yet_to_be_done', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
               class="flex items-center p-2.5 bg-white border {{ $activeTab === 'yet_to_be_done' ? 'border-indigo-500 bg-indigo-50/10' : 'border-slate-100' }} rounded-xl hover:shadow-sm transition">
                <div class="w-8 h-8 rounded-lg {{ $activeTab === 'yet_to_be_done' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center mr-2.5 shrink-0">
                    <span class="material-symbols-outlined text-base">pending</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[8px] uppercase text-slate-400 font-black tracking-wider leading-tight">Yet to Approve</p>
                    <h3 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($counts->yet_to_be_done) }}</h3>
                </div>
            </a>

            <!-- Rejected -->
            <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'rejected', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
               class="flex items-center p-2.5 bg-white border {{ $activeTab === 'rejected' ? 'border-red-500 bg-red-50/10' : 'border-slate-100' }} rounded-xl hover:shadow-sm transition">
                <div class="w-8 h-8 rounded-lg {{ $activeTab === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center mr-2.5 shrink-0">
                    <span class="material-symbols-outlined text-base">cancel</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[9px] uppercase text-slate-400 font-black tracking-wider leading-tight">Rejected</p>
                    <h3 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($counts->rejected) }}</h3>
                </div>
            </a>

            <!-- Cancelled -->
            <a href="{{ route('mmgay.bdo.owner-status-report', ['status' => 'cancelled', 'phase' => $selectedPhase, 'village_id' => $selectedVillageId, 'search' => $search]) }}" 
               class="flex items-center p-2.5 bg-white border {{ $activeTab === 'cancelled' ? 'border-slate-800 bg-slate-100' : 'border-slate-100' }} rounded-xl hover:shadow-sm transition">
                <div class="w-8 h-8 rounded-lg {{ $activeTab === 'cancelled' ? 'bg-slate-800 text-white' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center mr-2.5 shrink-0">
                    <span class="material-symbols-outlined text-base">delete_forever</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[9px] uppercase text-slate-400 font-black tracking-wider leading-tight">Cancelled</p>
                    <h3 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($counts->cancelled) }}</h3>
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
                <select name="phase" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-lg p-2 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
                    <option value="">All Phases</option>
                    @foreach($phases as $p)
                        <option value="{{ $p }}" {{ $selectedPhase == $p ? 'selected' : '' }}>Phase {{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Village Filter -->
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Select Village</label>
                <select name="village_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 text-xs rounded-lg p-2 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
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
                        <th class="px-3 py-2 text-center w-20">Actions</th>
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
                                @php
                                    $statusText = 'YET TO BE APPROVED';
                                    $statusClass = 'bg-indigo-50 text-indigo-700 border border-indigo-100';
                                    if ($owner->IsAllotmentCancelled ?? 0) {
                                        $statusText = 'CANCELLED';
                                        $statusClass = 'bg-slate-800 text-white';
                                    } elseif ($owner->IsRejected ?? 0) {
                                        $statusText = 'REJECTED';
                                        $statusClass = 'bg-red-50 text-red-700 border border-red-100';
                                    } elseif ($owner->IsApproved ?? 0) {
                                        if ($owner->IsPaid ?? 0) {
                                            if ($owner->registry_matched ?? 0) {
                                                $statusText = 'APPROVED & PAID (Registry Matched)';
                                                $statusClass = 'bg-green-50 text-green-700 border border-green-100';
                                            } else {
                                                $statusText = 'APPROVED & PAID (Registry Pending)';
                                                $statusClass = 'bg-cyan-50 text-cyan-700 border border-cyan-100';
                                            }
                                        } else {
                                            $statusText = 'APPROVED & UNPAID';
                                            $statusClass = 'bg-amber-50 text-amber-700 border border-amber-100';
                                        }
                                    }
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5 text-center">
                                <button onclick="openViewModal(this)" 
                                        data-name="{{ $owner->OwnerName }}"
                                        data-reg="{{ $owner->RegistrationNo }}"
                                        data-father="{{ $owner->FatherHusbandName ?? 'N/A' }}"
                                        data-mobile="{{ $owner->MobileNo }}"
                                        data-phase="Phase {{ $owner->Phase }}"
                                        data-village="{{ $owner->VillageName }}"
                                        data-flat="{{ $owner->FlatNo ?? 'N/A' }}"
                                        data-status="{{ $statusText }}"
                                        data-status-class="{{ $statusClass }}"
                                        class="bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold shadow-sm transition">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 py-6 text-center text-slate-400 font-medium">
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

    <!-- View Beneficiary Profile Modal -->
    <div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl border border-slate-150 max-w-lg w-full overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalCard">
            <!-- Modal Header -->
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600 text-lg">account_circle</span>
                    <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider">Beneficiary Profile</h3>
                </div>
                <button onclick="closeViewModal()" class="w-6 h-6 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                
                <!-- Profile Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h4 id="modalName" class="text-sm font-extrabold text-slate-800">-</h4>
                        <p id="modalReg" class="text-[10px] text-slate-400 font-mono mt-0.5">-</p>
                    </div>
                    <span id="modalStatus" class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase">-</span>
                </div>

                <!-- Personal & Location Info -->
                <div>
                    <h5 class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-2">Personal & Allotment Info</h5>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Father / Husband</span>
                            <span id="modalFather" class="font-semibold text-slate-700">-</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Mobile Number</span>
                            <span id="modalMobile" class="font-mono font-bold text-slate-700">-</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Village (Phase)</span>
                            <span id="modalVillage" class="font-semibold text-slate-700">-</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Flat Number</span>
                            <span id="modalFlat" class="font-mono font-bold text-slate-700">-</span>
                        </div>
                    </div>
                </div>

                <!-- Registry Details Section -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Land Registry matching details</h5>
                        <span id="registryStatusBadge" class="text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded">Checking...</span>
                    </div>

                    <!-- Loader Spinner -->
                    <div id="registryLoader" class="py-6 flex items-center justify-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <span class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></span>
                        <span>Fetching registry...</span>
                    </div>

                    <!-- No registry message -->
                    <div id="registryEmpty" class="hidden p-4 bg-amber-50/50 border border-amber-100 rounded-xl text-center text-slate-500">
                        <span class="material-symbols-outlined text-amber-500 text-lg block mb-1">warning</span>
                        <p class="text-[10px] font-bold uppercase tracking-wide">No property registration details found for this mobile number in local database</p>
                    </div>

                    <!-- Registry Information Panel -->
                    <div id="registryData" class="hidden grid grid-cols-2 gap-3 text-xs">
                        <div class="col-span-2 p-2.5 bg-emerald-50/20 border border-emerald-100 rounded-lg">
                            <span class="block text-[8px] font-black text-emerald-600 uppercase tracking-wider mb-0.5">Second Party / Owner in Land Record</span>
                            <span id="regSecondParty" class="font-bold text-slate-800">-</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Token ID</span>
                            <span id="regToken" class="font-mono text-[10px] font-bold text-slate-700 break-all">-</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Registry Number</span>
                            <span id="regNumber" class="font-mono font-bold text-slate-700">-</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Khewat Number</span>
                            <span id="regKhewat" class="font-mono font-bold text-slate-700">-</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Area Transferred</span>
                            <span id="regArea" class="font-bold text-slate-700">-</span>
                        </div>
                        <div class="col-span-2 p-2.5 bg-slate-50 border border-slate-100 rounded-lg">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Registry Date</span>
                            <span id="regDate" class="font-semibold text-slate-700">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button onclick="closeViewModal()" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold py-1.5 px-4 rounded-lg shadow-sm transition">
                    Close Details
                </button>
            </div>
        </div>
    </div>

    <script>
        function openViewModal(button) {
            // 1. Populate basic owner info from data attributes
            document.getElementById('modalName').innerText = button.getAttribute('data-name');
            document.getElementById('modalReg').innerText = button.getAttribute('data-reg');
            document.getElementById('modalFather').innerText = button.getAttribute('data-father');
            document.getElementById('modalMobile').innerText = button.getAttribute('data-mobile');
            document.getElementById('modalVillage').innerText = button.getAttribute('data-village') + ' (' + button.getAttribute('data-phase') + ')';
            document.getElementById('modalFlat').innerText = button.getAttribute('data-flat');
            
            const statusElement = document.getElementById('modalStatus');
            statusElement.innerText = button.getAttribute('data-status');
            statusElement.className = "px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase " + button.getAttribute('data-status-class');

            // 2. Open Modal Animation
            const modal = document.getElementById('viewModal');
            const card = document.getElementById('modalCard');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 50);

            // 3. Reset registry panel to loading state
            const loader = document.getElementById('registryLoader');
            const emptyState = document.getElementById('registryEmpty');
            const dataPanel = document.getElementById('registryData');
            const regStatusBadge = document.getElementById('registryStatusBadge');

            loader.classList.remove('hidden');
            emptyState.classList.add('hidden');
            dataPanel.classList.add('hidden');
            
            regStatusBadge.innerText = 'Checking...';
            regStatusBadge.className = 'text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-500';

            // 4. Hit AJAX route to get registry details
            const mobile = button.getAttribute('data-mobile');
            fetch('{{ url("/mmgay/bdo/owner-registry-details") }}/' + mobile)
                .then(res => res.json())
                .then(data => {
                    loader.classList.add('hidden');
                    
                    if (data.success && data.registry) {
                        const reg = data.registry;
                        
                        // Populate fields
                        document.getElementById('regSecondParty').innerText = reg.SecondParty || 'N/A';
                        document.getElementById('regToken').innerText = reg.Token || 'N/A';
                        document.getElementById('regNumber').innerText = reg.RegistaryNumber || 'N/A';
                        document.getElementById('regKhewat').innerText = reg.Khewat || 'N/A';
                        document.getElementById('regArea').innerText = (reg.TransferArea || '0') + ' units (Bhag: ' + (reg.Bhag || 'N/A') + ')';
                        
                        // Format Date
                        let formattedDate = 'N/A';
                        if (reg.RegistaryDate) {
                            try {
                                const dateObj = new Date(reg.RegistaryDate);
                                formattedDate = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                            } catch (e) {
                                formattedDate = reg.RegistaryDate;
                            }
                        }
                        document.getElementById('regDate').innerText = formattedDate;

                        // Show data panel
                        dataPanel.classList.remove('hidden');
                        regStatusBadge.innerText = 'Matched';
                        regStatusBadge.className = 'text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800';
                    } else {
                        // Show empty state
                        emptyState.classList.remove('hidden');
                        regStatusBadge.innerText = 'Unmatched';
                        regStatusBadge.className = 'text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-red-100 text-red-800';
                    }
                })
                .catch(err => {
                    loader.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                    regStatusBadge.innerText = 'Error';
                    regStatusBadge.className = 'text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-red-100 text-red-800';
                    console.error(err);
                });
        }

        function closeViewModal() {
            const modal = document.getElementById('viewModal');
            const card = document.getElementById('modalCard');
            
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
    </script>
</main>
@endsection
