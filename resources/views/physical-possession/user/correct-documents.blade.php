@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Correct Documents',
    'activeNav' => 'pp-correct',
])

@section('content')
@php
    $possessionField = \App\Models\PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE;
    $allotmentField = \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER;
    $otpDocTypes = [$possessionField, $allotmentField];
    $fileDocuments = $returnedDocuments->whereNotIn('document_type', $otpDocTypes);
@endphp

<div class="max-w-4xl mx-auto space-y-2">
    <div class="citizen-card border-amber-200 bg-amber-50">
        <div class="p-3">
            <div class="flex items-start gap-2">
                <span class="material-symbols-outlined text-amber-600 text-[22px] shrink-0">warning</span>
                <div>
                    <h2 class="text-[12px] font-extrabold text-amber-900 m-0 mb-1">Documents Sent Back for Correction</h2>
                    <p class="text-[10px] text-amber-800 m-0 leading-relaxed">
                        Officer ne kuch documents wapas bheje hain. Neeche diye remarks dekh kar sahi documents dubara upload karein aur resubmit karein.
                    </p>
                    @if($application->remarks)
                    <p class="text-[10px] font-bold text-amber-900 m-0 mt-2">Officer remarks: {{ $application->remarks }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Re-upload Returned Documents</h2>
            <span class="text-[9px] font-semibold text-slate-400">{{ $application->application_number }}</span>
        </div>
        <div class="p-3">
            <form method="POST" action="{{ route('pp.user.application.resubmit', $application) }}" enctype="multipart/form-data" id="ppCorrectForm">
                @csrf

                <div class="space-y-3">
                    @foreach($returnedDocuments as $doc)
                    <div class="rounded-xl border border-red-200 bg-red-50/40 p-3">
                        <p class="text-[11px] font-extrabold text-slate-800 m-0 mb-1">{{ $doc->typeLabel() }}</p>
                        @if($doc->officer_remarks)
                        <p class="text-[10px] text-red-700 font-semibold m-0 mb-2">Officer remark: {{ $doc->officer_remarks }}</p>
                        @endif

                        @if($doc->document_type === $possessionField && $needsPossessionCert)
                        <div class="rounded-lg border border-indigo-100 bg-white p-2.5">
                            <p class="text-[10px] text-slate-600 m-0 mb-2">Possession certificate dubara OTP se verify karein.</p>
                            <div class="pp-cert-preview-paper mb-2" style="max-height:220px;overflow:auto;">
                                @include('partials.physical-possession.prefilled-form-content')
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" id="btnVerifyCertificate" class="pp-cert-verify-btn">
                                    <span class="material-symbols-outlined">verified_user</span> Re-verify Certificate
                                </button>
                                <span id="certVerifiedBadge" class="pp-cert-verified-pill hidden">
                                    <span class="material-symbols-outlined">verified</span>
                                    <span class="pp-cert-verified-pill-text">Verified</span>
                                </span>
                            </div>
                        </div>
                        @elseif($doc->document_type === $allotmentField && $needsAllotmentLetter)
                        <div class="rounded-lg border border-indigo-100 bg-white p-2.5">
                            <p class="text-[10px] text-slate-600 m-0 mb-2">Allotment letter dubara OTP se verify karein.</p>
                            @if(!empty($allotmentLetter))
                            <div class="pp-cert-preview-paper mb-2" style="max-height:220px;overflow:auto;">
                                @include('partials.physical-possession.allotment-letter-content', [
                                    'letter' => $allotmentLetter,
                                    'verifyUrl' => route('pp.allotment.verify', $allotmentLetter['application_number']),
                                ])
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" id="btnVerifyAllotment" class="pp-cert-verify-btn">
                                    <span class="material-symbols-outlined">verified_user</span> Re-verify Allotment Letter
                                </button>
                                <span id="allotmentVerifiedBadge" class="pp-cert-verified-pill hidden">
                                    <span class="material-symbols-outlined">verified</span>
                                    <span class="pp-cert-verified-pill-text">Verified</span>
                                </span>
                            </div>
                            @else
                            <div class="pp-upload-zone" id="zone_{{ $allotmentField }}">
                                <span class="material-symbols-outlined text-indigo-500 text-[22px]">cloud_upload</span>
                                <p class="text-[10px] font-semibold text-slate-600 m-0 mt-1">Upload allotment letter (PDF/JPG/PNG)</p>
                            </div>
                            <input type="file" name="{{ $allotmentField }}" id="input_{{ $allotmentField }}" class="hidden"
                                   accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                            <div id="preview_{{ $allotmentField }}"></div>
                            @endif
                        </div>
                        @else
                        <div class="pp-upload-zone" id="zone_{{ $doc->document_type }}">
                            <span class="material-symbols-outlined text-indigo-500 text-[22px]">cloud_upload</span>
                            <p class="text-[10px] font-semibold text-slate-600 m-0 mt-1">Upload corrected file (PDF/JPG/PNG)</p>
                        </div>
                        <input type="file" name="{{ $doc->document_type }}" id="input_{{ $doc->document_type }}" class="hidden"
                               accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                        <div id="preview_{{ $doc->document_type }}"></div>
                        @error($doc->document_type)<p class="text-[10px] text-red-600 mt-1 mb-0">{{ $message }}</p>@enderror
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap justify-between gap-2 mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('pp.user.application.show', $application) }}"
                       class="inline-flex items-center gap-1 px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-700 no-underline hover:bg-slate-50">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back
                    </a>
                    <button type="submit" class="btn-v2-primary inline-flex items-center gap-1.5 border-0 cursor-pointer px-5 py-2">
                        <span class="material-symbols-outlined text-[14px]">send</span>
                        Resubmit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('partials.mmsay.citizen.pp-upload-scripts')
<script>
@foreach($fileDocuments as $doc)
    ppInitUploadZone('zone_{{ $doc->document_type }}', 'input_{{ $doc->document_type }}', 'preview_{{ $doc->document_type }}');
@endforeach
@if($needsAllotmentLetter && empty($allotmentLetter))
    ppInitUploadZone('zone_{{ $allotmentField }}', 'input_{{ $allotmentField }}', 'preview_{{ $allotmentField }}');
@endif

(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function ppOtpVerifyFlow(options) {
        var verifyBtn = options.verifyBtn;
        var verifyUrl = options.verifyUrl;
        var verifyBtnHtml = verifyBtn.innerHTML;
        Swal.fire({
            title: 'Enter OTP',
            input: 'text',
            inputAttributes: { maxlength: 6, autocapitalize: 'off', autocorrect: 'off' },
            showCancelButton: true,
            confirmButtonText: 'Verify',
            showLoaderOnConfirm: true,
            preConfirm: function (otp) {
                return fetch(verifyUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ otp: otp })
                }).then(function (r) { return r.json(); }).then(function (data) {
                    if (!data.success) throw new Error(data.message || 'Verification failed');
                    return data;
                });
            }
        }).then(function (result) {
            if (result.isConfirmed && options.onSuccess) options.onSuccess(result.value);
            verifyBtn.innerHTML = verifyBtnHtml;
            verifyBtn.disabled = false;
        });
        verifyBtn.disabled = true;
        verifyBtn.innerHTML = 'Sending OTP...';
        fetch(verifyUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({})
        });
    }

    @if($needsPossessionCert)
    document.getElementById('btnVerifyCertificate')?.addEventListener('click', function () {
        var btn = this;
        ppOtpVerifyFlow({
            verifyBtn: btn,
            verifyUrl: '{{ route('pp.user.certificate.verify') }}',
            onSuccess: function () {
                document.getElementById('certVerifiedBadge')?.classList.remove('hidden');
                btn.classList.add('hidden');
            }
        });
    });
    @endif

    @if($needsAllotmentLetter && !empty($allotmentLetter))
    document.getElementById('btnVerifyAllotment')?.addEventListener('click', function () {
        var btn = this;
        ppOtpVerifyFlow({
            verifyBtn: btn,
            verifyUrl: '{{ route('pp.user.allotment.verify') }}',
            onSuccess: function () {
                document.getElementById('allotmentVerifiedBadge')?.classList.remove('hidden');
                btn.classList.add('hidden');
            }
        });
    });
    @endif
})();
</script>
@endpush
