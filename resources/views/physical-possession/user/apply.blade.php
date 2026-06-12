@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Apply for Physical Possession',
    'activeNav' => 'pp-apply',
])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Applicant Details <span class="text-slate-400 font-normal">(auto-filled)</span></h2>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                    <p class="pp-detail-label">Name</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $profile['name'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                    <p class="pp-detail-label">Father</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $profile['father_name'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                    <p class="pp-detail-label">Mobile</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $profile['mobile'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                    <p class="pp-detail-label">District</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $profile['district'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5 sm:col-span-2">
                    <p class="pp-detail-label">Address</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $profile['address'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5 sm:col-span-2">
                    <p class="pp-detail-label">Registration</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $profile['registration_details'] }}</p>
                </div>
            </div>
            <a href="{{ route('pp.user.view-form') }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 text-[11px] font-bold text-emerald-700 no-underline hover:bg-emerald-100 w-full justify-center">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                View Possession Certificate Form
            </a>
            <p class="text-[10px] text-slate-500 mt-2 mb-0">Review the form, download, print, sign and upload required documents while applying.</p>
        </div>
    </div>

    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800">Upload Documents</h2>
        </div>
        <div class="p-3">
            <form method="POST" action="{{ route('pp.user.apply.submit') }}" enctype="multipart/form-data" id="ppApplyForm">
                @csrf

                @foreach(\App\Models\PhysicalPossessionDocument::applyFormFields() as $field => $meta)
                <div class="mb-2.5">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">
                        {{ $loop->iteration }}. {{ $meta['label'] }}
                        @if($meta['required'])
                            <span class="text-red-500">*</span>
                        @else
                            <span class="text-slate-400 font-normal">(if applicable)</span>
                        @endif
                    </label>
                    <div class="pp-upload-zone" id="zone_{{ $field }}">
                        <span class="material-symbols-outlined text-indigo-500 text-[20px]">cloud_upload</span>
                        <p class="text-[10px] font-semibold text-slate-600 m-0 mt-1">Drag & drop or click to select</p>
                        <p class="text-[9px] text-slate-400 m-0">PDF, JPG, JPEG, PNG — Max 10 MB</p>
                    </div>
                    <input type="file" name="{{ $field }}" id="input_{{ $field }}" class="hidden"
                           accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                           @if($meta['required']) data-required="1" @endif>
                    <div id="preview_{{ $field }}"></div>
                    @error($field)<p class="text-[10px] text-red-600 mt-1 mb-0">{{ $message }}</p>@enderror
                </div>
                @endforeach

                <button type="submit" class="btn-v2-primary w-full justify-center no-underline border-0 cursor-pointer" id="ppApplySubmitBtn">
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
@foreach(\App\Models\PhysicalPossessionDocument::applyFormFields() as $field => $meta)
    ppInitUploadZone('zone_{{ $field }}', 'input_{{ $field }}', 'preview_{{ $field }}');
@endforeach
</script>
@endpush
