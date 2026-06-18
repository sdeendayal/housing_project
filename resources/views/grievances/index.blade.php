@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'My Grievances',
    'activeNav' => 'grievances',
    'displayName' => $displayName,
    'applicationId' => $applicationId,
])

@section('content')
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">My Grievances</h2>
            <a href="{{ route('citizen.grievances.create') }}" class="btn-v2-primary btn-v2-sm no-underline">
                <span class="material-symbols-outlined text-[14px]">add</span>
                New Grievance
            </a>
        </div>

        <div class="p-3">
            @if ($grievances->isEmpty())
                <p class="text-[11px] text-slate-500 m-0">No grievances submitted yet.</p>
            @else
                <div class="overflow-x-auto rounded-lg border border-slate-100">
                    <table class="w-full text-[10px] text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-2 py-1.5 font-bold text-slate-500">S.No.</th>
                                <th class="px-2 py-1.5 font-bold text-slate-500">Application ID</th>
                                <th class="px-2 py-1.5 font-bold text-slate-500">Name</th>
                                <th class="px-2 py-1.5 font-bold text-slate-500">Mobile</th>
                                <th class="px-2 py-1.5 font-bold text-slate-500">Subject</th>
                                <th class="px-2 py-1.5 font-bold text-slate-500">Status</th>
                                <th class="px-2 py-1.5 font-bold text-slate-500 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($grievances as $index => $grievance)
                                @php
                                    $statusClass = match ($grievance->grievance_status) {
                                        'Resolved' => 'bg-emerald-100 text-emerald-700',
                                        'In Progress' => 'bg-indigo-100 text-indigo-700',
                                        default => 'bg-amber-100 text-amber-700',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-2 py-1.5 text-slate-700">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1.5 text-slate-700 break-all">{{ $grievance->application_id }}</td>
                                    <td class="px-2 py-1.5 text-slate-800 font-semibold">{{ $grievance->applicant_name }}</td>
                                    <td class="px-2 py-1.5 text-slate-700 whitespace-nowrap">{{ $grievance->mobile_number }}</td>
                                    <td class="px-2 py-1.5 text-slate-800">{{ $grievance->grievance_subject }}</td>
                                    <td class="px-2 py-1.5">
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold {{ $statusClass }}">
                                            {{ $grievance->grievance_status }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-1.5 text-center">
                                        <a href="{{ route('citizen.grievances.show', $grievance) }}"
                                           class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded border border-indigo-200 bg-indigo-50 text-[9px] font-bold text-indigo-700 no-underline hover:bg-indigo-100">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
