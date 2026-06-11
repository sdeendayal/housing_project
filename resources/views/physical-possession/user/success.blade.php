@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Application Submitted',
    'activeNav' => 'pp-application-show',
])

@section('content')
@php
    $statusClass = match($application->status) {
        'approved' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-red-100 text-red-700',
        default => 'bg-amber-100 text-amber-700',
    };
@endphp

<div class="citizen-card">
    <div class="px-3 py-2 border-b border-emerald-100 bg-emerald-50 flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
        <h2 class="text-[11px] font-extrabold text-emerald-800 m-0">Application Submitted Successfully</h2>
    </div>
    <div class="p-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 mb-3">
            <div class="rounded-lg border border-indigo-100 bg-indigo-50/60 p-2.5 sm:col-span-2 lg:col-span-3">
                <p class="pp-detail-label">Generated Application ID</p>
                <p class="text-[14px] font-extrabold text-indigo-700">{{ $application->application_number }}</p>
            </div>
            <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                <p class="pp-detail-label">Slip ID</p>
                <p class="text-[12px] font-bold text-slate-800">{{ $application->slip_id }}</p>
            </div>
            <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                <p class="pp-detail-label">Applicant</p>
                <p class="text-[12px] font-bold text-slate-800">{{ $application->applicant_name }}</p>
            </div>
            <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                <p class="pp-detail-label">District</p>
                <p class="text-[12px] font-bold text-slate-800">{{ $application->district_name ?? '—' }}</p>
            </div>
            <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                <p class="pp-detail-label">Submitted</p>
                <p class="text-[12px] font-bold text-slate-800">{{ $application->created_at->format('d M Y') }}</p>
            </div>
            <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                <p class="pp-detail-label">Status</p>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $statusClass }}">{{ $application->status }}</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('pp.user.application.show', $application) }}" class="btn-v2-primary btn-v2-sm no-underline">
                <span class="material-symbols-outlined text-[14px]">visibility</span> View Application & Documents
            </a>
            <a href="{{ route('pp.user.slip.print', $application) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-700 no-underline hover:bg-slate-100">
                <span class="material-symbols-outlined text-[16px]">print</span> Print Slip
            </a>
            <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-[11px] font-bold text-indigo-700 no-underline hover:bg-indigo-100">
                <span class="material-symbols-outlined text-[16px]">dashboard</span> Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
