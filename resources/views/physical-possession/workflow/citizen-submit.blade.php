@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Complete Possession Submission',
    'activeNav' => 'dashboard',
])

@section('content')
<div class="container py-4">
    <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 no-underline hover:text-slate-700 mb-3">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Back to Dashboard
    </a>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-3 mb-4 text-[11px] text-red-700">
            <p class="font-bold mb-1">Please correct the following errors:</p>
            <ul class="list-disc pl-4 m-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="citizen-card mb-4">
        <div class="px-3 py-2.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[12px] font-extrabold text-slate-800">Complete Physical Possession Submission</h2>
        </div>
        <div class="p-3">
            <div class="rounded-lg border p-3 bg-white mb-4">
                <h3 class="text-[11px] font-bold text-slate-700 mb-2">Property & Visit Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[10px] text-slate-600">
                    <div>
                        <span class="text-slate-400">Application No:</span> <strong>{{ $application->application_number }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400">Applicant:</span> <strong>{{ $application->applicant_name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400">Property:</span> <strong>{{ $application->asset_name ?? 'Plot/Unit' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400">District:</span> <strong>{{ $application->district_name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400">Slot 1 Option:</span> <strong>{{ $application->visit_slot_1 ? $application->visit_slot_1->format('d M Y - h:i A') : '—' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400">Slot 2 Option:</span> <strong>{{ $application->visit_slot_2 ? $application->visit_slot_2->format('d M Y - h:i A') : '—' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400">Slot 3 Option:</span> <strong>{{ $application->visit_slot_3 ? $application->visit_slot_3->format('d M Y - h:i A') : '—' }}</strong>
                    </div>
                    @if($application->visit_instructions)
                    <div class="md:col-span-2 mt-2 p-3.5 rounded-xl border border-indigo-100 bg-indigo-50/40">
                        <span class="text-[10px] uppercase font-bold text-indigo-700 flex items-center gap-1 mb-1">
                            <span class="material-symbols-outlined text-[15px]">info</span> Visit Instructions / दिशा-निर्देश
                        </span>
                        <p class="text-[10.5px] text-slate-700 m-0 leading-relaxed font-medium whitespace-pre-line">{{ $application->visit_instructions }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <form action="{{ route('pp.citizen.submit.post') }}" method="POST">
                @csrf

                <!-- Slot Selection Section -->
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-3 mb-4">
                    <h3 class="text-[11px] font-bold text-slate-800 mb-2">Select Your Preferred Meeting Slot</h3>
                    
                    <div class="mb-3 bg-white p-3 rounded-lg border border-slate-200">
                        <label class="text-[10px] text-slate-700 font-bold mb-1.5 block">Select Visit Slot <span class="text-red-500">*</span></label>
                        <select name="selected_slot" id="selected_slot" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-semibold text-slate-700 outline-none">
                            <option value="" disabled selected>-- Select visit slot --</option>
                            @if($application->visit_slot_1)
                                <option value="{{ $application->visit_slot_1->format('Y-m-d H:i:s') }}" {{ old('selected_slot') === $application->visit_slot_1->format('Y-m-d H:i:s') ? 'selected' : '' }}>
                                    Slot 1 Option ({{ $application->visit_slot_1->format('d M Y - h:i A') }})
                                </option>
                            @endif
                            @if($application->visit_slot_2)
                                <option value="{{ $application->visit_slot_2->format('Y-m-d H:i:s') }}" {{ old('selected_slot') === $application->visit_slot_2->format('Y-m-d H:i:s') ? 'selected' : '' }}>
                                    Slot 2 Option ({{ $application->visit_slot_2->format('d M Y - h:i A') }})
                                </option>
                            @endif
                            @if($application->visit_slot_3)
                                <option value="{{ $application->visit_slot_3->format('Y-m-d H:i:s') }}" {{ old('selected_slot') === $application->visit_slot_3->format('Y-m-d H:i:s') ? 'selected' : '' }}>
                                    Slot 3 Option ({{ $application->visit_slot_3->format('d M Y - h:i A') }})
                                </option>
                            @endif
                        </select>
                        <p class="text-[9px] text-slate-400 mt-1">Please select the most convenient visit slot for you. The District Officer will visit the plot site at the selected time.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('citizen.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-[11px] font-bold text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 no-underline">Cancel</a>
                    <button type="submit" id="btn-submit" class="btn-v2-primary btn-v2-sm cursor-pointer">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        Confirm Visit Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
