@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Grievance Details',
    'activeNav' => 'grievances',
    'displayName' => $displayName,
    'applicationId' => $applicationId,
])

@section('content')
    @php
        $statusClass = match ($grievance->grievance_status) {
            'Resolved' => 'bg-emerald-100 text-emerald-700',
            'In Progress' => 'bg-indigo-100 text-indigo-700',
            default => 'bg-amber-100 text-amber-700',
        };
    @endphp

    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Grievance Details</h2>
            <a href="{{ route('citizen.grievances.index') }}" class="text-[10px] font-bold text-indigo-600 no-underline hover:text-indigo-800">
                ← Back to list
            </a>
        </div>

        <div class="p-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application ID</p>
                    <p class="text-[12px] font-bold text-slate-800 break-all">{{ $grievance->application_id }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Applicant Name</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $grievance->applicant_name }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Mobile Number</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $grievance->mobile_number }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Asset ID</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $grievance->asset_id ?? '—' }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">District ID</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $grievance->district_id ?? '—' }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">District</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $grievance->district ?? '—' }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Status</p>
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClass }}">
                        {{ $grievance->grievance_status }}
                    </span>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Subject</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $grievance->grievance_subject }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Description</p>
                    <p class="text-[12px] font-medium text-slate-800 leading-relaxed whitespace-pre-line">{{ $grievance->grievance_description }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50 sm:col-span-2">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Admin Remarks</p>
                    <p class="text-[12px] font-medium text-slate-800 leading-relaxed whitespace-pre-line">
                        {{ $grievance->admin_remarks ?: '—' }}
                    </p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Submitted On</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $grievance->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50">
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Last Updated</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $grievance->updated_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
