@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Apply for Physical Possession',
    'activeNav' => 'pp-apply',
])

@section('content')
@php
    $formFields = \App\Models\PhysicalPossessionDocument::applyFormFields();
    $possessionField = \App\Models\PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE;
    $possessionMeta = $formFields[$possessionField];
    $otherFields = collect($formFields)->except($possessionField);
    $docIndex = 2;
@endphp

<div class="max-w-4xl mx-auto">
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Upload Documents</h2>
            <span class="text-[9px] font-semibold text-slate-400">PDF, JPG, PNG — Max 10 MB</span>
        </div>
        <div class="p-3">
            <form method="POST" action="{{ route('pp.user.apply.submit') }}" enctype="multipart/form-data" id="ppApplyForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- Step workflow: Possession Certificate --}}
                    <div class="md:col-span-2 pp-cert-workflow">
                        <div class="pp-cert-workflow-head">
                            <p class="text-[11px] font-extrabold text-indigo-900 m-0 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">assignment</span>
                                1. {{ $possessionMeta['label'] }}
                                <span class="text-red-500">*</span>
                            </p>
                            <p class="text-[10px] text-indigo-700/80 m-0 mt-0.5">Follow the 3 steps below — download here, upload the signed copy in Step 3.</p>
                        </div>

                        <div class="pp-cert-steps">
                            {{-- Step 1: Download --}}
                            <div class="pp-cert-step pp-cert-step-download">
                                <span class="pp-step-badge pp-step-badge-blue">
                                    <span class="material-symbols-outlined text-[12px]">looks_one</span>
                                    Step 1
                                </span>
                                <p class="pp-cert-step-title">View &amp; Download Form</p>
                                <p class="pp-cert-step-desc">Open the pre-filled possession certificate form and download it to your device.</p>
                                <div class="flex flex-col sm:flex-row gap-1.5 mt-auto">
                                    <a href="{{ route('pp.user.view-form') }}" target="_blank" rel="noopener"
                                       class="pp-cert-action-btn pp-cert-action-view flex-1">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        View Form
                                    </a>
                                    <a href="{{ route('pp.user.download-form') }}"
                                       class="pp-cert-action-btn pp-cert-action-download flex-1">
                                        <span class="material-symbols-outlined text-[16px]">download</span>
                                        Download
                                    </a>
                                </div>
                            </div>

                            <div class="pp-cert-arrow" aria-hidden="true">
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </div>
                            <div class="pp-cert-arrow-mobile" aria-hidden="true">
                                <span class="material-symbols-outlined">arrow_downward</span>
                            </div>

                            {{-- Step 2: Sign --}}
                            <div class="pp-cert-step pp-cert-step-sign">
                                <span class="pp-step-badge pp-step-badge-amber">
                                    <span class="material-symbols-outlined text-[12px]">looks_two</span>
                                    Step 2
                                </span>
                                <p class="pp-cert-step-title">Print &amp; Sign</p>
                                <p class="pp-cert-step-desc">Print the downloaded form, sign it by hand, and keep the signed copy ready to upload.</p>
                                <div class="flex items-center gap-1.5 mt-auto text-amber-700">
                                    <span class="material-symbols-outlined text-[18px]">draw</span>
                                    <span class="text-[10px] font-bold">Signature required</span>
                                </div>
                            </div>

                            <div class="pp-cert-arrow" aria-hidden="true">
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </div>
                            <div class="pp-cert-arrow-mobile" aria-hidden="true">
                                <span class="material-symbols-outlined">arrow_downward</span>
                            </div>

                            {{-- Step 3: Upload --}}
                            <div class="pp-cert-step pp-cert-step-upload">
                                <span class="pp-step-badge pp-step-badge-green">
                                    <span class="material-symbols-outlined text-[12px]">looks_3</span>
                                    Step 3
                                </span>
                                <p class="pp-cert-step-title">Upload Signed Copy Here</p>
                                <p class="pp-cert-step-desc">Upload the same form after signing — only in this box.</p>
                                <div class="pp-upload-zone pp-upload-zone-signed flex-1 mt-1" id="zone_{{ $possessionField }}">
                                    <span class="material-symbols-outlined text-emerald-600 text-[26px]">upload_file</span>
                                    <p class="pp-upload-here-label">Upload signed document here</p>
                                    <p class="text-[9px] text-emerald-700/70 m-0">Click or drag &amp; drop</p>
                                </div>
                                <input type="file" name="{{ $possessionField }}" id="input_{{ $possessionField }}" class="hidden"
                                       accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                                       data-required="1">
                                <div id="preview_{{ $possessionField }}"></div>
                                @error($possessionField)<p class="text-[10px] text-red-600 mt-1 mb-0">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Other documents — 2 per row --}}
                    @foreach($otherFields as $field => $meta)
                    <div>
                        <div class="rounded-xl border border-slate-100 bg-white p-2.5 h-full flex flex-col">
                            <label class="text-[11px] font-bold text-slate-800 mb-1.5">
                                {{ $docIndex }}. {{ $meta['label'] }}
                                @if($meta['required'])
                                    <span class="text-red-500">*</span>
                                @else
                                    <span class="text-slate-400 font-normal text-[10px]">(if applicable)</span>
                                @endif
                            </label>
                            <div class="pp-upload-zone flex-1" id="zone_{{ $field }}">
                                <span class="material-symbols-outlined text-indigo-500 text-[22px]">cloud_upload</span>
                                <p class="text-[10px] font-semibold text-slate-600 m-0 mt-1">Drag & drop or click to select</p>
                                <p class="text-[9px] text-slate-400 m-0">PDF, JPG, JPEG, PNG</p>
                            </div>
                            <input type="file" name="{{ $field }}" id="input_{{ $field }}" class="hidden"
                                   accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                                   @if($meta['required']) data-required="1" @endif>
                            <div id="preview_{{ $field }}"></div>
                            @error($field)<p class="text-[10px] text-red-600 mt-1 mb-0">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    @php $docIndex++; @endphp
                    @endforeach
                </div>

                <button type="submit" class="btn-v2-primary w-full justify-center no-underline border-0 cursor-pointer mt-3" id="ppApplySubmitBtn">
                    <span class="material-symbols-outlined text-[14px]">send</span>
                    Submit Application
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.mmsay.citizen.pp-upload-scripts')
@include('partials.mmsay.citizen.pp-apply-validation')
<script>
    ppInitUploadZone('zone_{{ $possessionField }}', 'input_{{ $possessionField }}', 'preview_{{ $possessionField }}');
@foreach($otherFields as $field => $meta)
    ppInitUploadZone('zone_{{ $field }}', 'input_{{ $field }}', 'preview_{{ $field }}');
@endforeach
</script>
@endpush
