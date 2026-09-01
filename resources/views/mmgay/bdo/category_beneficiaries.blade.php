@extends('layouts.mmgayBdoAuth')
@section('title', 'Category Wise Beneficiaries')
@section('page_header', 'Category Report')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-6 flex-grow flex flex-col gap-5">

    <!-- Compact Block Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-xl px-4 py-2.5 text-white shadow-sm relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-indigo-500/10 blur-2xl"></div>
        <div class="absolute -right-20 -bottom-20 w-44 h-44 rounded-full bg-blue-500/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-2.5">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-400/30">
                        {{ $districtName ? $districtName . ' District' : 'MMGAY' }}
                    </span>
                    <span class="text-[10px] text-slate-400">•</span>
                    <span class="text-[8.5px] uppercase font-bold text-slate-300 tracking-wider">Beneficiary Classification</span>
                </div>
                <h1 class="text-xs md:text-sm font-extrabold uppercase tracking-wide mt-0.5 text-white leading-tight">
                    {{ $blockName }} Block — Category Wise Beneficiaries
                </h1>
                <p class="text-[9px] text-slate-300 font-medium mt-0.5 max-w-2xl leading-tight">
                    Verified physical possession beneficiaries classified into Ghumantu, Widow, Scheduled Caste (SC), and Others as registered in Registry & Owner records.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('mmgay.bdo.category-beneficiaries.export.csv', request()->query()) }}" 
                   class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-sm transition-all">
                    <span class="material-symbols-outlined text-[13px]">download</span>
                    Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Category KPI Summary Cards (Interactive quick filters) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        
        <!-- 1. Total All -->
        @php
            $isAllActive = empty($selectedCategory) || $selectedCategory === 'all';
        @endphp
        <a href="{{ route('mmgay.bdo.category-beneficiaries', array_merge(request()->except(['category', 'page']), ['category' => 'all'])) }}"
           class="p-4 rounded-xl border transition-all duration-200 flex flex-col justify-between {{ $isAllActive ? 'bg-blue-600 text-white border-blue-600 shadow-md ring-2 ring-blue-300' : 'bg-white text-slate-800 border-slate-200 hover:border-blue-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-[9px] font-black uppercase tracking-wider {{ $isAllActive ? 'text-blue-100' : 'text-slate-400' }}">Total Beneficiaries</span>
                <span class="material-symbols-outlined text-lg {{ $isAllActive ? 'text-blue-200' : 'text-blue-600' }}">groups</span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black">{{ number_format($stats['total']) }}</h3>
                <span class="text-[8.5px] font-semibold {{ $isAllActive ? 'text-blue-100' : 'text-slate-500' }}">All Eligible</span>
            </div>
        </a>

        <!-- 2. Ghumantu -->
        @php
            $isGhumantuActive = $selectedCategory === 'ghumantu';
        @endphp
        <a href="{{ route('mmgay.bdo.category-beneficiaries', array_merge(request()->except(['category', 'page']), ['category' => 'ghumantu'])) }}"
           class="p-4 rounded-xl border transition-all duration-200 flex flex-col justify-between {{ $isGhumantuActive ? 'bg-purple-700 text-white border-purple-700 shadow-md ring-2 ring-purple-300' : 'bg-white text-slate-800 border-slate-200 hover:border-purple-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-[9px] font-black uppercase tracking-wider {{ $isGhumantuActive ? 'text-purple-100' : 'text-purple-600' }}">Ghumantu</span>
                <span class="material-symbols-outlined text-lg {{ $isGhumantuActive ? 'text-purple-200' : 'text-purple-600' }}">commute</span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black">{{ number_format($stats['ghumantu']) }}</h3>
                <span class="text-[8.5px] font-semibold {{ $isGhumantuActive ? 'text-purple-100' : 'text-purple-600' }}">Ghumantu Jati</span>
            </div>
        </a>

        <!-- 3. Widow -->
        @php
            $isWidowActive = $selectedCategory === 'widow';
        @endphp
        <a href="{{ route('mmgay.bdo.category-beneficiaries', array_merge(request()->except(['category', 'page']), ['category' => 'widow'])) }}"
           class="p-4 rounded-xl border transition-all duration-200 flex flex-col justify-between {{ $isWidowActive ? 'bg-rose-600 text-white border-rose-600 shadow-md ring-2 ring-rose-300' : 'bg-white text-slate-800 border-slate-200 hover:border-rose-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-[9px] font-black uppercase tracking-wider {{ $isWidowActive ? 'text-rose-100' : 'text-rose-600' }}">Widow</span>
                <span class="material-symbols-outlined text-lg {{ $isWidowActive ? 'text-rose-200' : 'text-rose-600' }}">female</span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black">{{ number_format($stats['widow']) }}</h3>
                <span class="text-[8.5px] font-semibold {{ $isWidowActive ? 'text-rose-100' : 'text-rose-600' }}">Widow Beneficiaries</span>
            </div>
        </a>

        <!-- 4. Scheduled Caste (SC) -->
        @php
            $isScActive = $selectedCategory === 'sc';
        @endphp
        <a href="{{ route('mmgay.bdo.category-beneficiaries', array_merge(request()->except(['category', 'page']), ['category' => 'sc'])) }}"
           class="p-4 rounded-xl border transition-all duration-200 flex flex-col justify-between {{ $isScActive ? 'bg-indigo-700 text-white border-indigo-700 shadow-md ring-2 ring-indigo-300' : 'bg-white text-slate-800 border-slate-200 hover:border-indigo-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-[9px] font-black uppercase tracking-wider {{ $isScActive ? 'text-indigo-100' : 'text-indigo-600' }}">Scheduled Caste (SC)</span>
                <span class="material-symbols-outlined text-lg {{ $isScActive ? 'text-indigo-200' : 'text-indigo-600' }}">diversity_1</span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black">{{ number_format($stats['sc']) }}</h3>
                <span class="text-[8.5px] font-semibold {{ $isScActive ? 'text-indigo-100' : 'text-indigo-600' }}">SC Category</span>
            </div>
        </a>

        <!-- 5. Others -->
        @php
            $isOthersActive = $selectedCategory === 'others';
        @endphp
        <a href="{{ route('mmgay.bdo.category-beneficiaries', array_merge(request()->except(['category', 'page']), ['category' => 'others'])) }}"
           class="p-4 rounded-xl border transition-all duration-200 flex flex-col justify-between {{ $isOthersActive ? 'bg-slate-800 text-white border-slate-800 shadow-md ring-2 ring-slate-400' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-400 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-[9px] font-black uppercase tracking-wider {{ $isOthersActive ? 'text-slate-300' : 'text-slate-500' }}">Others</span>
                <span class="material-symbols-outlined text-lg {{ $isOthersActive ? 'text-slate-300' : 'text-slate-600' }}">person_outline</span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black">{{ number_format($stats['others']) }}</h3>
                <span class="text-[8.5px] font-semibold {{ $isOthersActive ? 'text-slate-300' : 'text-slate-500' }}">General / Remaining</span>
            </div>
        </a>

    </div>

    <!-- Village-Wise Breakdown Table in this Block -->
    @if(isset($villageBreakdown) && $villageBreakdown->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600 text-base">holiday_village</span>
                <h2 class="text-xs font-black uppercase tracking-wider text-slate-700 m-0">
                    Village-Wise Category Breakdown ({{ $blockName }} Block)
                </h2>
            </div>
            <span class="text-[9.5px] font-bold text-slate-500">
                {{ $villageBreakdown->count() }} {{ Str::plural('Village', $villageBreakdown->count()) }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-[11px] text-left">
                <thead class="bg-slate-100 text-slate-600 font-bold uppercase tracking-wider text-[9px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2.5">#</th>
                        <th class="px-4 py-2.5">Village Name</th>
                        <th class="px-4 py-2.5 text-center">Total</th>
                        <th class="px-4 py-2.5 text-center text-purple-700">Ghumantu</th>
                        <th class="px-4 py-2.5 text-center text-rose-700">Widow</th>
                        <th class="px-4 py-2.5 text-center text-indigo-700">SC</th>
                        <th class="px-4 py-2.5 text-center text-slate-600">Others</th>
                        <th class="px-4 py-2.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($villageBreakdown as $vb)
                    <tr class="hover:bg-slate-50 transition-colors {{ $selectedVillageId == $vb->VillageId ? 'bg-blue-50/60 font-semibold' : '' }}">
                        <td class="px-4 py-2 text-slate-400 text-[10px]">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 font-bold text-slate-800 uppercase flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-slate-400 text-sm">home_work</span>
                            {{ $vb->VillageName }}
                        </td>
                        <td class="px-4 py-2 text-center font-black text-slate-800">{{ $vb->total }}</td>
                        <td class="px-4 py-2 text-center font-bold text-purple-700">
                            {{ $vb->ghumantu > 0 ? $vb->ghumantu : '—' }}
                        </td>
                        <td class="px-4 py-2 text-center font-bold text-rose-700">
                            {{ $vb->widow > 0 ? $vb->widow : '—' }}
                        </td>
                        <td class="px-4 py-2 text-center font-bold text-indigo-700">
                            {{ $vb->sc > 0 ? $vb->sc : '—' }}
                        </td>
                        <td class="px-4 py-2 text-center font-bold text-slate-600">
                            {{ $vb->others > 0 ? $vb->others : '—' }}
                        </td>
                        <td class="px-4 py-2 text-center whitespace-nowrap">
                            @if($selectedVillageId == $vb->VillageId)
                                <a href="{{ route('mmgay.bdo.category-beneficiaries', array_merge(request()->except(['village_id', 'page']))) }}"
                                   class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-700 hover:bg-slate-300 transition">
                                    <span class="material-symbols-outlined text-[11px]">close</span> Clear
                                </a>
                            @else
                                <a href="{{ route('mmgay.bdo.category-beneficiaries', array_merge(request()->except(['village_id', 'page']), ['village_id' => $vb->VillageId])) }}"
                                   class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-600 hover:text-white transition">
                                    <span class="material-symbols-outlined text-[11px]">filter_alt</span> Filter
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Main Content Card: Filter Toolbar & Beneficiaries Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        
        <!-- Filter Toolbar -->
        <div class="p-4 border-b border-slate-200 bg-slate-50/50">
            <form action="{{ route('mmgay.bdo.category-beneficiaries') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3">
                
                <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[280px]">
                    <!-- Category Dropdown -->
                    <div class="w-44">
                        <label class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider mb-1">Category</label>
                        <select name="category" onchange="this.form.submit()"
                                class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>All Categories</option>
                            <option value="ghumantu" {{ $selectedCategory === 'ghumantu' ? 'selected' : '' }}>Ghumantu</option>
                            <option value="widow" {{ $selectedCategory === 'widow' ? 'selected' : '' }}>Widow</option>
                            <option value="sc" {{ $selectedCategory === 'sc' ? 'selected' : '' }}>Scheduled Caste (SC)</option>
                            <option value="others" {{ $selectedCategory === 'others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>

                    <!-- Village Dropdown -->
                    <div class="w-48">
                        <label class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider mb-1">Village</label>
                        <select name="village_id" onchange="this.form.submit()"
                                class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">All Villages ({{ $villages->count() }})</option>
                            @foreach($villages as $v)
                                <option value="{{ $v->VillageId }}" {{ $selectedVillageId == $v->VillageId ? 'selected' : '' }}>
                                    {{ $v->VillageName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Phase Filter -->
                    @if($phases->isNotEmpty())
                    <div class="w-36">
                        <label class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider mb-1">Phase</label>
                        <select name="phase" onchange="this.form.submit()"
                                class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">All Phases</option>
                            @foreach($phases as $p)
                                <option value="{{ $p }}" {{ $selectedPhase == $p ? 'selected' : '' }}>
                                    Phase {{ $p }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Search Input -->
                    <div class="flex-1 min-w-[200px] max-w-sm">
                        <label class="block text-[8.5px] font-black uppercase text-slate-500 tracking-wider mb-1">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Search by Name, Mobile, PPP, Reg ID..."
                                   class="w-full bg-white border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <span class="material-symbols-outlined text-slate-400 text-sm absolute left-2.5 top-2">search</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 self-end">
                    <button type="submit"
                            class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">filter_list</span> Apply
                    </button>
                    <a href="{{ route('mmgay.bdo.category-beneficiaries') }}"
                       class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                        Reset
                    </a>
                </div>

            </form>
        </div>

        <!-- Table Info Header -->
        <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between text-xs text-slate-600">
            <div>
                Showing <strong class="text-slate-800">{{ $beneficiaries->total() }}</strong> beneficiaries
                @if($selectedCategory !== 'all')
                    in category <span class="uppercase font-bold text-blue-700">[{{ $selectedCategory }}]</span>
                @endif
                @if($selectedVillageId)
                    @php
                        $vObj = $villages->firstWhere('VillageId', $selectedVillageId);
                    @endphp
                    in village <span class="uppercase font-bold text-indigo-700">[{{ $vObj->VillageName ?? '' }}]</span>
                @endif
            </div>
            <div class="text-[10px] text-slate-400 font-semibold">
                Page {{ $beneficiaries->currentPage() }} of {{ $beneficiaries->lastPage() }}
            </div>
        </div>

        <!-- Beneficiaries Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-[11px] text-left">
                <thead class="bg-slate-100 text-slate-600 font-bold uppercase tracking-wider text-[9px] border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2.5 text-center">Sr.</th>
                        <th class="px-3 py-2.5">Registration ID</th>
                        <th class="px-3 py-2.5">Beneficiary Details</th>
                        <th class="px-3 py-2.5">Contact / PPP</th>
                        <th class="px-3 py-2.5">Category</th>
                        <th class="px-3 py-2.5">Village / Block</th>
                        <th class="px-3 py-2.5">Plot / Flat No</th>
                        <th class="px-3 py-2.5 text-center">Possession Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($beneficiaries as $ben)
                    @php
                        // Category badge styling & label
                        $casteVal = $ben->Caste ?? '';
                        $categoryBadgeClass = match($casteVal) {
                            'Ghumantu' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'Widow' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'SC' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200'
                        };
                        $categoryLabel = match($casteVal) {
                            'Ghumantu' => 'Ghumantu',
                            'Widow' => 'Widow',
                            'SC' => 'Scheduled Caste (SC)',
                            default => 'Others (' . ($casteVal ?: 'General') . ')'
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-3 py-2 text-center text-slate-400 text-[10px]">
                            {{ ($beneficiaries->currentPage() - 1) * $beneficiaries->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-3 py-2 font-bold text-slate-800 font-mono text-[10px] whitespace-nowrap">
                            {{ $ben->RegistrationNo ?: '—' }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="font-extrabold text-slate-900 uppercase">{{ $ben->OwnerName }}</div>
                            <div class="text-[9.5px] text-slate-500">S/o, W/o: {{ $ben->FatherHusbandName ?: '—' }}</div>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <div class="font-semibold text-slate-700">{{ $ben->MobileNo ?: '—' }}</div>
                            @if($ben->PPPId)
                                <div class="text-[9.5px] text-slate-400 font-mono">PPP: {{ $ben->PPPId }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9.5px] font-bold border {{ $categoryBadgeClass }}">
                                {{ $categoryLabel }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <div class="font-bold text-slate-800 uppercase">{{ $ben->VillageName }}</div>
                            <div class="text-[9.5px] text-slate-400">{{ $ben->BlockName }}</div>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap font-bold text-slate-800">
                            {{ $ben->FlatNo ?: '—' }}
                        </td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            @php
                                $status = $ben->possession_status;
                                $statusClass = match($status) {
                                    'Verified' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Slot Selected' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'Visit Scheduled' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Site Verified' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200'
                                };
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold border {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400 font-semibold">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">search_off</span>
                            No beneficiaries found matching the selected filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($beneficiaries->hasPages())
        <div class="p-3 border-t border-slate-200 bg-slate-50/60 flex items-center justify-center">
            {{ $beneficiaries->links('partials.compact-pagination') }}
        </div>
        @endif

    </div>

</main>
@endsection
