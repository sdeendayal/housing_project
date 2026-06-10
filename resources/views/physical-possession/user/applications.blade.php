@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'My Physical Possession Applications',
    'activeNav' => 'pp-applications',
])

@section('content')
<div class="citizen-card">
    <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
        <h2 class="text-[11px] font-extrabold text-slate-800">All Applications</h2>
        @unless($ppHasApplication)
        <a href="{{ route('pp.user.apply') }}" class="btn-v2-primary btn-v2-sm no-underline">
            <span class="material-symbols-outlined text-[14px]">add</span>
            New Application
        </a>
        @endunless
    </div>
    <div class="p-0">
        @if($applications->count())
            <div class="overflow-x-auto">
                <table class="w-full text-[11px]">
                    <thead>
                        <tr class="bg-indigo-600 text-white text-left">
                            <th class="px-3 py-2 font-bold">Application ID</th>
                            <th class="px-3 py-2 font-bold">Slip ID</th>
                            <th class="px-3 py-2 font-bold">District</th>
                            <th class="px-3 py-2 font-bold">Date</th>
                            <th class="px-3 py-2 font-bold">Status</th>
                            <th class="px-3 py-2 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                        @php
                            $statusClass = match($app->status) {
                                'approved' => 'bg-emerald-100 text-emerald-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="px-3 py-2 font-bold text-indigo-700">{{ $app->application_number }}</td>
                            <td class="px-3 py-2 text-slate-500">{{ $app->slip_id }}</td>
                            <td class="px-3 py-2">{{ $app->district_name ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $app->created_at->format('d M Y') }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $statusClass }}">{{ $app->status }}</span>
                            </td>
                            <td class="px-3 py-2 text-right whitespace-nowrap">
                                <a href="{{ route('pp.user.application.show', $app) }}" class="text-indigo-600 font-bold no-underline hover:underline">View Details</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center">
                <span class="material-symbols-outlined text-[32px] text-slate-300">folder_open</span>
                <p class="text-[11px] text-slate-500 mt-2 mb-3">No applications found.</p>
                <a href="{{ route('pp.user.apply') }}" class="btn-v2-primary btn-v2-sm no-underline">Apply Now</a>
            </div>
        @endif
    </div>
</div>
@endsection
