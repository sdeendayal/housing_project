@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Allotted Property Details',
    'activeNav' => 'property-details',
])

@section('content')
    <div class="border border-slate-100 rounded-lg p-2.5 bg-white mb-2">
        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Application</p>
        <p class="text-[12px] font-bold text-slate-800 break-all">{{ $applicationId }}</p>
        <p class="text-[10px] text-slate-500 m-0 mt-0.5">Status: <span class="font-semibold text-slate-700">{{ $flatStatus }}</span></p>
    </div>

    @if ($hasProperty && count($propertyDetails) > 0)
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Allotted Property Details</h2>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @include('partials.mmsay.citizen.detail-grid', ['items' => $propertyDetails])
            </div>
        </div>
    </div>
    @else
    <div class="citizen-card p-4 text-center">
        <span class="material-symbols-outlined text-[32px] text-slate-300">home_work</span>
        <p class="text-[12px] font-bold text-slate-700 mt-2 mb-1">No property allotted yet</p>
        <p class="text-[10px] text-slate-500 m-0">Your allotted flat / plot details will appear here once available.</p>
    </div>
    @endif
@endsection
