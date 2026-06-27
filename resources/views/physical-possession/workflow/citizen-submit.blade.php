@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Complete Possession Submission',
    'activeNav' => 'dashboard',
])

@section('content')
<div class="container max-w-[960px] py-4">
    <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 no-underline hover:text-slate-700 mb-3 transition-colors">
        <span class="material-symbols-outlined text-[15px]">arrow_back</span>
        Back to Dashboard
    </a>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-3 mb-4 text-[11px] text-red-700 shadow-sm">
            <p class="font-bold mb-1 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-red-600">error</span>
                Please correct the following errors:
            </p>
            <ul class="list-disc pl-4 m-0 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bento bento-white overflow-hidden mb-4">
        <!-- Header Banner -->
        <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-indigo-50/80 via-white to-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                </div>
                <div>
                    <h2 class="text-xs font-extrabold text-slate-800 tracking-tight">Complete Physical Possession Submission</h2>
                    <p class="text-[9px] text-slate-500 font-medium mt-0.5">Please review the proposed slots and confirm your preferred choice.</p>
                </div>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200/50 shrink-0">
                Action Required
            </span>
        </div>

        <div class="p-4">
            <!-- Property Details Bento Box -->
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-3.5 mb-4">
                <h3 class="text-[10px] uppercase font-extrabold text-slate-700 tracking-wider mb-2.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px] text-indigo-500">info_i</span>
                    Property & Allotment Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-[10px]">
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 shadow-sm">
                        <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[8.5px]">Application No</span>
                        <strong class="text-slate-800 text-[10.5px] font-extrabold">{{ $application->application_number }}</strong>
                    </div>
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 shadow-sm">
                        <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[8.5px]">Applicant Name</span>
                        <strong class="text-slate-800 text-[10.5px] font-extrabold">{{ $application->applicant_name }}</strong>
                    </div>
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 shadow-sm">
                        <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[8.5px]">Property Name/ID</span>
                        <strong class="text-slate-800 text-[10.5px] font-extrabold">{{ $application->asset_name ?? 'Plot/Unit' }}</strong>
                    </div>
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200/60 shadow-sm">
                        <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[8.5px]">District</span>
                        <strong class="text-slate-800 text-[10.5px] font-extrabold">{{ $application->district_name }}</strong>
                    </div>
                </div>

                @if($application->visit_instructions)
                <div class="mt-3 p-3 rounded-xl border border-indigo-100/80 bg-indigo-50/30">
                    <span class="text-[9px] uppercase font-extrabold text-indigo-700 flex items-center gap-1.5 mb-1 tracking-wider">
                        <span class="material-symbols-outlined text-[14px]">info</span> Visit Instructions / दिशा-निर्देश
                    </span>
                    <p class="text-[10.5px] text-slate-700 m-0 leading-relaxed font-medium whitespace-pre-line">{{ $application->visit_instructions }}</p>
                </div>
                @endif
            </div>

            <!-- Slot Selection Form -->
            <form action="{{ route('pp.citizen.submit.post') }}" method="POST">
                @csrf

                <!-- Custom Slot Card Selection -->
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-3.5 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-[10px] uppercase font-extrabold text-slate-800 tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-indigo-500">ads_click</span>
                            Select Your Preferred Meeting Slot / स्लॉट का चयन करें <span class="text-red-500">*</span>
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-[8.5px] font-extrabold bg-indigo-100 text-indigo-700 uppercase tracking-wider">
                            Choose 1 Option
                        </span>
                    </div>

                    <!-- Highlighted Callout Box -->
                    <div class="bg-gradient-to-r from-indigo-50 to-indigo-50/30 border border-indigo-100 text-indigo-800 text-[10px] font-bold px-3 py-2 rounded-xl mb-3.5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-indigo-600 animate-bounce">pan_tool_alt</span>
                        <span>कृपया नीचे दिए गए 3 विकल्पों में से किसी एक पर क्लिक करके स्लॉट चुनें (Please click on any of the 3 cards below to choose your slot):</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- Slot 1 -->
                        @if($application->visit_slot_1)
                            <div class="relative">
                                <input type="radio" name="selected_slot" id="slot_opt_1" value="{{ $application->visit_slot_1->format('Y-m-d H:i:s') }}" class="slot-radio absolute opacity-0 w-0 h-0" required {{ old('selected_slot') === $application->visit_slot_1->format('Y-m-d H:i:s') ? 'checked' : '' }}>
                                <label for="slot_opt_1" class="slot-card cursor-pointer block border-2 border-slate-200 rounded-xl p-3 bg-white hover:bg-slate-50/50 hover:border-slate-300 transition-all select-none shadow-sm relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="slot-icon w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                            </div>
                                            <div>
                                                <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider block">Option 1</span>
                                                <span class="text-[11px] font-extrabold text-slate-800 block mt-0.5">{{ $application->visit_slot_1->format('d M Y') }}</span>
                                                <span class="text-[9.5px] font-semibold text-slate-500 block mt-0.5">{{ $application->visit_slot_1->format('h:i A') }} ({{ $application->visit_slot_1->format('l') }})</span>
                                            </div>
                                        </div>
                                        <div class="slot-check w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all"></div>
                                    </div>
                                </label>
                            </div>
                        @endif

                        <!-- Slot 2 -->
                        @if($application->visit_slot_2)
                            <div class="relative">
                                <input type="radio" name="selected_slot" id="slot_opt_2" value="{{ $application->visit_slot_2->format('Y-m-d H:i:s') }}" class="slot-radio absolute opacity-0 w-0 h-0" required {{ old('selected_slot') === $application->visit_slot_2->format('Y-m-d H:i:s') ? 'checked' : '' }}>
                                <label for="slot_opt_2" class="slot-card cursor-pointer block border-2 border-slate-200 rounded-xl p-3 bg-white hover:bg-slate-50/50 hover:border-slate-300 transition-all select-none shadow-sm relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="slot-icon w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                            </div>
                                            <div>
                                                <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider block">Option 2</span>
                                                <span class="text-[11px] font-extrabold text-slate-800 block mt-0.5">{{ $application->visit_slot_2->format('d M Y') }}</span>
                                                <span class="text-[9.5px] font-semibold text-slate-500 block mt-0.5">{{ $application->visit_slot_2->format('h:i A') }} ({{ $application->visit_slot_2->format('l') }})</span>
                                            </div>
                                        </div>
                                        <div class="slot-check w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all"></div>
                                    </div>
                                </label>
                            </div>
                        @endif

                        <!-- Slot 3 -->
                        @if($application->visit_slot_3)
                            <div class="relative">
                                <input type="radio" name="selected_slot" id="slot_opt_3" value="{{ $application->visit_slot_3->format('Y-m-d H:i:s') }}" class="slot-radio absolute opacity-0 w-0 h-0" required {{ old('selected_slot') === $application->visit_slot_3->format('Y-m-d H:i:s') ? 'checked' : '' }}>
                                <label for="slot_opt_3" class="slot-card cursor-pointer block border-2 border-slate-200 rounded-xl p-3 bg-white hover:bg-slate-50/50 hover:border-slate-300 transition-all select-none shadow-sm relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="slot-icon w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                            </div>
                                            <div>
                                                <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider block">Option 3</span>
                                                <span class="text-[11px] font-extrabold text-slate-800 block mt-0.5">{{ $application->visit_slot_3->format('d M Y') }}</span>
                                                <span class="text-[9.5px] font-semibold text-slate-500 block mt-0.5">{{ $application->visit_slot_3->format('h:i A') }} ({{ $application->visit_slot_3->format('l') }})</span>
                                            </div>
                                        </div>
                                        <div class="slot-check w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all"></div>
                                    </div>
                                </label>
                            </div>
                        @endif
                    </div>
                    <p class="text-[9px] text-slate-400 mt-3 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px] text-slate-300">info_outline</span>
                        Note: The District Officer will conduct the site physical verification visit on the slot date and time you confirm above.
                    </p>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-3 mt-4">
                    <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-[11px] font-bold text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 no-underline transition-colors">Cancel</a>
                    <button type="submit" id="btn-submit" class="btn-v2-primary btn-v2-sm cursor-pointer flex items-center gap-1 shadow-sm transition-all hover:-translate-y-px hover:shadow-md">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        Confirm Visit Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling state for the radio choice cards */
    .slot-card {
        border: 2px solid #cbd5e1 !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .slot-card:hover {
        border-color: #818cf8 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.1) !important;
        background-color: #fafbfe !important;
    }
    .slot-card:hover .slot-icon {
        background-color: #e0e7ff !important;
        color: #4f46e5 !important;
    }
    .slot-card:hover .slot-check {
        border-color: #4f46e5 !important;
    }
    .slot-check {
        width: 18px !important;
        height: 18px !important;
        border: 2px solid #cbd5e1 !important;
        background-color: #fff !important;
        border-radius: 50% !important;
        transition: all 0.15s ease !important;
    }
    .slot-radio:checked + .slot-card {
        border-color: #4f46e5 !important;
        background-color: rgba(99, 102, 241, 0.04) !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
    }
    .slot-radio:checked + .slot-card .slot-icon {
        background-color: #ede9fe !important;
        color: #4f46e5 !important;
    }
    .slot-radio:checked + .slot-card .slot-check {
        border-color: #4f46e5 !important;
        background-color: #4f46e5 !important;
    }
    .slot-radio:checked + .slot-card .slot-check::after {
        content: '✓';
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        display: block;
        text-align: center;
        line-height: 14px;
    }
</style>
@endpush
