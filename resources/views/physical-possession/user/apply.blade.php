@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Apply for Physical Possession',
    'activeNav' => 'pp-apply',
])

@section('content')
@php
    $formFields = \App\Models\PhysicalPossessionDocument::applyFormFields();
    $possessionField = \App\Models\PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE;
    $allotmentField = \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER;
    $possessionMeta = $formFields[$possessionField];
    $allotmentMeta = $formFields[$allotmentField];
    $remainingFields = collect($formFields)->except([$possessionField, $allotmentField]);
    $docIndex = 3;
    $allotmentVerifyUrl = !empty($allotmentLetter)
        ? route('pp.allotment.verify', $allotmentLetter['application_number'])
        : null;
@endphp

<div class="max-w-4xl mx-auto">
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Upload Documents</h2>
            <span class="text-[9px] font-semibold text-slate-400">All 5 documents required to submit</span>
        </div>
        <div class="p-3">
            @if(!empty($verifiedPossessionCert) || !empty($verifiedAllotmentLetter))
            <div class="mb-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">info</span>
                <p class="text-[10px] text-blue-800 m-0 font-semibold">Application in progress — verified documents are saved. Upload remaining documents and submit.</p>
            </div>
            @endif
            <form method="POST" action="{{ route('pp.user.apply.submit') }}" enctype="multipart/form-data" id="ppApplyForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- 1. Possession Certificate --}}
                    <div class="md:col-span-2" id="possessionCertSection">
                        <div class="pp-cert-preview-wrap">
                            <div class="pp-cert-preview-head">
                                <p class="text-[11px] font-extrabold text-indigo-900 m-0 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">assignment</span>
                                    1. {{ $possessionMeta['label'] }}
                                    <span class="text-red-500">*</span>
                                </p>
                                <p class="text-[10px] text-indigo-700/80 m-0 mt-0.5">Review and confirm via OTP.</p>
                            </div>
                            <div class="pp-cert-preview-paper">
                                @include('partials.physical-possession.prefilled-form-content')
                            </div>
                            <div class="pp-cert-action-bar {{ !empty($verifiedPossessionCert) ? 'pp-cert-action-bar--done' : '' }}" id="certActionBar">
                                <div class="pp-cert-action-hint {{ !empty($verifiedPossessionCert) ? 'hidden' : '' }}">
                                    <span class="pp-cert-action-hint-icon"><span class="material-symbols-outlined">shield_lock</span></span>
                                    <div class="min-w-0">
                                        <p class="pp-cert-action-hint-title">OTP Verification Required</p>
                                        <p class="pp-cert-action-hint-sub">Confirm with OTP sent to your mobile</p>
                                    </div>
                                </div>
                                <div id="certVerifiedBadge" class="pp-cert-verified-pill {{ empty($verifiedPossessionCert) ? 'hidden' : '' }}">
                                    <span class="material-symbols-outlined">verified</span>
                                    <div class="text-left">
                                        <p class="pp-cert-verified-pill-text">Verified</p>
                                        <p class="pp-cert-verified-pill-sub" id="certVerifiedAt">
                                            @if(!empty($verifiedPossessionCert))
                                                {{ $verifiedPossessionCert->verified_at->format('d M Y, h:i A') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <button type="button" id="btnVerifyCertificate" class="pp-cert-verify-btn {{ !empty($verifiedPossessionCert) ? 'hidden' : '' }}">
                                    <span class="material-symbols-outlined">verified_user</span> Verify
                                </button>
                                <input type="hidden" name="possession_certificate_verified" id="possessionCertificateVerified" value="{{ !empty($verifiedPossessionCert) ? '1' : '0' }}">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Allotment Letter --}}
                    <div class="md:col-span-2" id="allotmentLetterSection">
                        <div class="pp-cert-preview-wrap">
                            <div class="pp-cert-preview-head">
                                <p class="text-[11px] font-extrabold text-indigo-900 m-0 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">home_work</span>
                                    2. {{ $allotmentMeta['label'] }}
                                    <span class="text-red-500">*</span>
                                </p>
                                <p class="text-[10px] text-indigo-700/80 m-0 mt-0.5">आंबटन पत्र — डेटा सिस्टम से लोड होगा, OTP से verify करें।</p>
                            </div>

                            @if(!empty($allotmentLetter))
                            <div class="pp-cert-preview-paper pp-allotment-preview-paper">
                                @include('partials.physical-possession.allotment-letter-content', [
                                    'letter' => $allotmentLetter,
                                    'verifyUrl' => $allotmentVerifyUrl,
                                ])
                            </div>
                            <div class="pp-cert-action-bar {{ !empty($verifiedAllotmentLetter) ? 'pp-cert-action-bar--done' : '' }}" id="allotmentActionBar">
                                <div class="pp-cert-action-hint {{ !empty($verifiedAllotmentLetter) ? 'hidden' : '' }}">
                                    <span class="pp-cert-action-hint-icon"><span class="material-symbols-outlined">shield_lock</span></span>
                                    <div class="min-w-0">
                                        <p class="pp-cert-action-hint-title">OTP Verification Required</p>
                                        <p class="pp-cert-action-hint-sub">Allotment letter will be saved automatically</p>
                                    </div>
                                </div>
                                <div id="allotmentVerifiedBadge" class="pp-cert-verified-pill {{ empty($verifiedAllotmentLetter) ? 'hidden' : '' }}">
                                    <span class="material-symbols-outlined">verified</span>
                                    <div class="text-left">
                                        <p class="pp-cert-verified-pill-text">Verified</p>
                                        <p class="pp-cert-verified-pill-sub" id="allotmentVerifiedAt">
                                            @if(!empty($verifiedAllotmentLetter))
                                                {{ $verifiedAllotmentLetter->verified_at->format('d M Y, h:i A') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <button type="button" id="btnVerifyAllotment" class="pp-cert-verify-btn {{ !empty($verifiedAllotmentLetter) ? 'hidden' : '' }}">
                                    <span class="material-symbols-outlined">verified_user</span> Verify
                                </button>
                                <input type="hidden" name="allotment_letter_verified" id="allotmentLetterVerified" value="{{ !empty($verifiedAllotmentLetter) ? '1' : '0' }}">
                            </div>
                            @else
                            <div class="p-3">
                                <p class="text-[11px] text-amber-700 font-bold m-0 mb-2">Allotment data not found in system. Please upload manually.</p>
                                <div class="pp-upload-zone" id="zone_{{ $allotmentField }}">
                                    <span class="material-symbols-outlined text-indigo-500 text-[22px]">cloud_upload</span>
                                    <p class="text-[10px] font-semibold text-slate-600 m-0 mt-1">Upload allotment letter (PDF/JPG/PNG)</p>
                                </div>
                                <input type="file" name="{{ $allotmentField }}" id="input_{{ $allotmentField }}" class="hidden"
                                       accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" data-required="1">
                                <div id="preview_{{ $allotmentField }}"></div>
                                <input type="hidden" name="allotment_letter_verified" id="allotmentLetterVerified" value="0">
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Other documents --}}
                    @foreach($remainingFields as $field => $meta)
                    <div>
                        <div class="rounded-xl border border-slate-100 bg-white p-2.5 h-full flex flex-col">
                            <label class="text-[11px] font-bold text-slate-800 mb-1.5">
                                {{ $docIndex }}. {{ $meta['label'] }}
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="pp-upload-zone flex-1" id="zone_{{ $field }}">
                                <span class="material-symbols-outlined text-indigo-500 text-[22px]">cloud_upload</span>
                                <p class="text-[10px] font-semibold text-slate-600 m-0 mt-1">Drag & drop or click to select</p>
                                <p class="text-[9px] text-slate-400 m-0">PDF, JPG, JPEG, PNG</p>
                            </div>
                            <input type="file" name="{{ $field }}" id="input_{{ $field }}" class="hidden"
                                   accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                                   data-required="1">
                            <div id="preview_{{ $field }}"></div>
                            @error($field)<p class="text-[10px] text-red-600 mt-1 mb-0">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    @php $docIndex++; @endphp
                    @endforeach
                </div>

                <div class="flex justify-end mt-4 pt-3 border-t border-slate-100">
                    <button type="submit" class="btn-v2-primary inline-flex items-center gap-1.5 no-underline border-0 cursor-pointer px-5 py-2" id="ppApplySubmitBtn">
                        <span class="material-symbols-outlined text-[14px]">send</span>
                        Submit Application
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
@include('partials.mmsay.citizen.pp-apply-validation')
<script>
@foreach($remainingFields as $field => $meta)
    ppInitUploadZone('zone_{{ $field }}', 'input_{{ $field }}', 'preview_{{ $field }}');
@endforeach
@if(empty($allotmentLetter))
    ppInitUploadZone('zone_{{ $allotmentField }}', 'input_{{ $allotmentField }}', 'preview_{{ $allotmentField }}');
@endif

    // Shared OTP popup helper with resend support
    function ppOtpVerifyFlow(options) {
        var verifyBtn = options.verifyBtn;
        var verifyUrl = options.verifyUrl;
        var csrfToken = options.csrfToken;
        var verifyBtnHtml = verifyBtn.html();
        var onSuccess = options.onSuccess;
        var loadingText = options.loadingText || 'Verifying...';
        var resendCooldown = 60;
        var resendTimer = null;

        function clearResendTimer() {
            if (resendTimer) {
                clearInterval(resendTimer);
                resendTimer = null;
            }
        }

        function startResendCountdown(seconds) {
            var remaining = seconds;
            var resendBtn = document.getElementById('swalResendOtpBtn');
            if (!resendBtn) return;

            resendBtn.disabled = true;
            resendBtn.textContent = 'Resend OTP in ' + remaining + 's';

            clearResendTimer();
            resendTimer = setInterval(function () {
                remaining -= 1;
                if (remaining <= 0) {
                    clearResendTimer();
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend OTP';
                    return;
                }
                resendBtn.textContent = 'Resend OTP in ' + remaining + 's';
            }, 1000);
        }

        function sendOtpRequest(isResend, onSent) {
            $.ajax({
                url: verifyUrl,
                type: 'POST',
                data: { _token: csrfToken, resend: isResend ? 1 : 0 },
                success: function (response) {
                    if (response.step === 'otp_sent') {
                        onSent(response);
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to send OTP.';
                    Swal.showValidationMessage(msg);
                    if (xhr.responseJSON && xhr.responseJSON.resend_after) {
                        startResendCountdown(xhr.responseJSON.resend_after);
                    }
                }
            });
        }

        function openOtpModal() {
            Swal.fire({
                title: 'Enter OTP',
                html: '<p class="text-[12px] text-slate-600 mb-2">OTP sent to your registered mobile number.</p>' +
                      '<input type="text" id="swalOtpInput" class="swal2-input" placeholder="Enter 6-digit OTP" maxlength="6" inputmode="numeric" style="font-size:18px;letter-spacing:4px;text-align:center;">' +
                      '<button type="button" id="swalResendOtpBtn" class="swal2-styled" style="background:#64748b;margin-top:8px;">Resend OTP</button>' +
                      @if(app()->environment('local'))
                      '<p class="text-[10px] text-amber-600 mt-1">Local: use OTP <strong>111111</strong></p>' +
                      @endif
                      '',
                showCancelButton: true,
                confirmButtonText: 'Verify OTP',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5',
                didOpen: function () {
                    startResendCountdown(resendCooldown);
                    var resendBtn = document.getElementById('swalResendOtpBtn');
                    if (resendBtn) {
                        resendBtn.addEventListener('click', function () {
                            if (resendBtn.disabled) return;
                            Swal.resetValidationMessage();
                            sendOtpRequest(true, function (response) {
                                startResendCountdown(response.resend_after || resendCooldown);
                                Swal.showValidationMessage('A new OTP has been sent.');
                                setTimeout(function () { Swal.resetValidationMessage(); }, 2500);
                            });
                        });
                    }
                },
                willClose: function () {
                    clearResendTimer();
                },
                preConfirm: function () {
                    var otp = document.getElementById('swalOtpInput').value.trim();
                    if (!otp || otp.length !== 6) {
                        Swal.showValidationMessage('Please enter a valid 6-digit OTP');
                        return false;
                    }
                    return otp;
                }
            }).then(function (result) {
                if (!result.isConfirmed) return;

                Swal.fire({ title: loadingText, allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

                $.ajax({
                    url: verifyUrl,
                    type: 'POST',
                    data: { _token: csrfToken, otp: result.value },
                    success: function (res) {
                        if (res.step === 'verified') {
                            onSuccess(res);
                            Swal.fire({ icon: 'success', title: 'Verified!', text: res.message, confirmButtonColor: '#4f46e5' });
                        }
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Verification failed.';
                        Swal.fire({ icon: 'error', title: 'Failed', text: msg, confirmButtonColor: '#dc2626' });
                    }
                });
            });
        }

        verifyBtn.on('click', function () {
            verifyBtn.prop('disabled', true).html('<span class="material-symbols-outlined">hourglass_top</span> Sending...');

            $.ajax({
                url: verifyUrl,
                type: 'POST',
                data: { _token: csrfToken, resend: 0 },
                success: function (response) {
                    verifyBtn.prop('disabled', false).html(verifyBtnHtml);
                    if (response.step === 'otp_sent') {
                        openOtpModal();
                    }
                },
                error: function (xhr) {
                    verifyBtn.prop('disabled', false).html(verifyBtnHtml);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to send OTP.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#dc2626' });
                }
            });
        });
    }

    $(function () {
        var csrfToken = @json(csrf_token());

        ppOtpVerifyFlow({
            verifyBtn: $('#btnVerifyCertificate'),
            verifyUrl: @json(route('pp.user.certificate.verify')),
            csrfToken: csrfToken,
            loadingText: 'Saving certificate...',
            onSuccess: function (res) {
                $('#possessionCertificateVerified').val('1');
                $('#certVerifiedAt').text(res.verified_at);
                $('#certVerifiedBadge').removeClass('hidden');
                $('#btnVerifyCertificate').addClass('hidden');
                $('.pp-cert-action-hint').first().addClass('hidden');
                $('#certActionBar').addClass('pp-cert-action-bar--done');
            }
        });

        @if(!empty($allotmentLetter))
        ppOtpVerifyFlow({
            verifyBtn: $('#btnVerifyAllotment'),
            verifyUrl: @json(route('pp.user.allotment.verify')),
            csrfToken: csrfToken,
            loadingText: 'Saving allotment letter...',
            onSuccess: function (res) {
                $('#allotmentLetterVerified').val('1');
                $('#allotmentVerifiedAt').text(res.verified_at);
                $('#allotmentVerifiedBadge').removeClass('hidden');
                $('#btnVerifyAllotment').addClass('hidden');
                $('#allotmentActionBar .pp-cert-action-hint').addClass('hidden');
                $('#allotmentActionBar').addClass('pp-cert-action-bar--done');
            }
        });
        @endif
    });
</script>
@endpush
