@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Allotment Letter',
    'activeNav' => 'allotment-letter',
])

@section('content')
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2 flex-wrap">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Allotment Letter</h2>
            @if (!empty($letter))
            <a href="{{ route('citizen.allotment-letter.download') }}" class="citizen-download-link inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-indigo-200 bg-indigo-50 text-[10px] font-bold text-indigo-700 no-underline hover:bg-indigo-100" data-download-loader-text="Downloading allotment letter…">
                <span class="material-symbols-outlined text-[14px]">download</span>
                Download PDF
            </a>
            @endif
        </div>
        <div class="p-3">
            @if (!empty($letter))
                <div class="pp-cert-preview-paper pp-allotment-preview-paper">
                    @include('partials.physical-possession.allotment-letter-content', [
                        'letter' => $letter,
                        'verifyUrl' => $verifyUrl,
                    ])
                </div>
            @else
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-center">
                    <span class="material-symbols-outlined text-amber-600 text-[28px]">description</span>
                    <p class="text-[11px] font-bold text-amber-800 m-0 mt-2">Allotment letter data not found for your account.</p>
                    <p class="text-[10px] text-amber-700/80 m-0 mt-1">Please contact your estate office if you believe this is an error.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
