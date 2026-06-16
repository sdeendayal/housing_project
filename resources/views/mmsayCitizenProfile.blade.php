@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'My Profile',
    'activeNav' => 'profile',
])

@section('content')
    <div class="citizen-card">
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-3 py-2.5 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-[22px]">account_circle</span>
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-extrabold text-white truncate">{{ $fullName }}</h2>
                    <p class="text-[10px] text-white/70 truncate">App No: {{ $applicationId }}</p>
                </div>
            </div>
            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $accountStatus === 'Active' ? 'bg-emerald-500/20 text-emerald-100 border-emerald-300/30' : 'bg-red-500/20 text-red-100 border-red-300/30' }}">
                {{ $accountStatus }}
            </span>
        </div>

        <div class="p-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Full Name</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $fullName }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Father Name</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $fatherName }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Mobile</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $mobile }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">{{ $idLabel }}</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $idValue }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Category</p>
                    <span class="inline-block bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $category }}</span>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">District</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $district }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application No.</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $applicationNo }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Residential Address</p>
                    <p class="text-[12px] font-medium text-slate-800 leading-relaxed">{{ $address }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
