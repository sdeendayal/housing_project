@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Possession Certificate',
    'activeNav' => 'possession-certificate',
])

@section('content')
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Possession Certificate Request Form</h2>
            <a href="{{ route('pp.user.download-form') }}" class="citizen-download-link inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-indigo-200 bg-indigo-50 text-[10px] font-bold text-indigo-700 no-underline hover:bg-indigo-100" data-download-loader-text="Downloading possession form…">
                <span class="material-symbols-outlined text-[14px]">download</span>
                Download PDF
            </a>
        </div>
        <div class="p-3">
            <div class="pp-cert-preview-paper">
                @include('partials.physical-possession.prefilled-form-content')
            </div>
        </div>
    </div>
@endsection
