@extends('layouts.mmgayBdoAuth')
@section('title', 'Villages List')
@section('page_header', 'Villages List')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-6 flex-grow flex flex-col gap-6">

    <!-- Premium Block Header Info -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-xl p-4 text-white shadow-sm relative overflow-hidden">
        <!-- Background decorative circles -->
        <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
        <div class="absolute -right-20 -bottom-20 w-48 h-48 rounded-full bg-white/5 blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <p class="text-[8px] uppercase font-black tracking-widest text-blue-200">Active Block</p>
                <h2 class="text-sm font-extrabold uppercase tracking-wide mt-0.5">{{ $blockName }} Block</h2>
                <p class="text-[10px] text-slate-200/80 font-medium mt-0.5">Select a village card to drill down and manage beneficiaries, approvals, and registry tracking.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md border border-white/20 px-3 py-1.5 rounded-lg shrink-0">
                <p class="text-[8px] uppercase font-black tracking-wider text-blue-200">Total Villages</p>
                <h3 class="text-sm font-black mt-0.5">{{ $villages->count() }} Villages</h3>
            </div>
        </div>
    </div>

    <!-- Interactive Villages Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
        @forelse($villages as $vil)
            <div class="group relative flex items-center p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <!-- Circular Serial Number Badge -->
                <div class="absolute top-4 right-4 text-[10px] font-black text-slate-350 bg-slate-50 px-2.5 py-0.5 rounded-full uppercase tracking-wider group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                    #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                </div>
                <!-- Stylized Icon Container -->
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 flex items-center justify-center text-blue-600 mr-4 group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <span class="material-symbols-outlined text-xl">holiday_village</span>
                </div>
                <!-- Village Details Link -->
                <a href="{{ route('mmgay.bdo.owner-status-report', ['village_id' => $vil->VillageId]) }}" class="min-w-0 flex-1">
                    <p class="text-[9px] uppercase text-slate-400 font-extrabold tracking-wider">Village</p>
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide group-hover:text-blue-700 transition-colors mt-0.5">{{ $vil->VillageName }}</h3>
                </a>
                <!-- Actions and Arrow Container -->
                <div class="flex items-center gap-3 z-10 ml-2">
                    @if(!empty($vil->map_pdf))
                        <a href="{{ asset('phase1_plans_gps_map/' . $vil->map_pdf) }}" target="_blank" 
                           class="inline-flex items-center gap-0.5 px-2 py-1 text-[8px] font-black text-red-650 hover:text-red-755 bg-red-50 hover:bg-red-100 border border-red-200 rounded transition-all shadow-sm hover:shadow" 
                           title="Open Village GPS Map PDF">
                            <span class="material-symbols-outlined text-[10px] font-black">picture_as_pdf</span>
                            <span>MAP PDF</span>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-0.5 px-2 py-1 text-[8px] font-extrabold text-slate-400 bg-slate-50 border border-slate-150 rounded cursor-default" title="Map not available">
                            <span class="material-symbols-outlined text-[10px] font-black">info</span>
                            <span>MAP NOT AVAILABLE</span>
                        </span>
                    @endif
                    <a href="{{ route('mmgay.bdo.owner-status-report', ['village_id' => $vil->VillageId]) }}" class="text-slate-300 group-hover:text-blue-600 transition-colors">
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white border border-slate-100 rounded-2xl text-slate-450 font-bold shadow-sm">
                <span class="material-symbols-outlined text-4xl block mb-2 text-slate-350">holiday_village</span>
                No villages found under this block.
            </div>
        @endforelse
    </div>

</main>
@endsection
