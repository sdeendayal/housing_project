@extends('layouts.mmgayBdoAuth')
@section('title', 'Schedule Physical Possession')
@section('page_header', 'Schedule Visit')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex-1">
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Sidebar Beneficiary Profile Details - High Density -->
        <div class="md:col-span-1 bg-white rounded-xl shadow-sm border border-slate-100 p-4 self-start">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-2.5 pb-2 border-b border-slate-100 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-blue-600 text-lg">person</span>
                Beneficiary Profile
            </h3>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Name</span>
                    <span class="font-extrabold text-slate-800 block">{{ $owner->OwnerName }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Father / Husband</span>
                    <span class="font-medium text-slate-700 block">{{ $owner->FatherHusbandName ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Mobile Number</span>
                    <span class="font-mono text-slate-700 block">{{ $owner->MobileNo }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Registration No</span>
                    <span class="font-bold text-slate-700 block">{{ $owner->RegistrationNo }}</span>
                </div>
                <div class="border-t border-slate-100 pt-2">
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Flat No & Address</span>
                    <span class="text-slate-600 leading-tight block">{{ $owner->FlatNo ?? '—' }}, {{ $owner->OwnerAddress ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Block & Village</span>
                    <span class="text-slate-600 block">{{ $owner->BlockName ?? '—' }}, {{ $owner->VillageName ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">District</span>
                    <span class="text-slate-800 font-bold block">{{ $owner->DistrictName }}</span>
                </div>
                <div class="border-t border-slate-100 pt-2">
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Family ID (PPP ID)</span>
                    <span class="font-mono text-slate-800 block font-bold">{{ $owner->PPPId ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Caste / Category</span>
                    <span class="text-slate-700 block font-semibold">{{ $owner->Caste ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[9px] block">Payment Status</span>
                    @if($owner->IsPaid)
                        @if($owner->IsPaymentApproved)
                            <span class="inline-block mt-0.5 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-extrabold border border-emerald-100">Paid & Approved</span>
                        @else
                            <span class="inline-block mt-0.5 text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded font-extrabold border border-amber-100">Paid (Awaiting Approval)</span>
                        @endif
                    @else
                        <span class="inline-block mt-0.5 text-xs text-rose-700 bg-rose-50 px-2 py-0.5 rounded font-extrabold border border-rose-100 font-bold">Unpaid / Payment Pending</span>
                    @endif
                </div>
                @if($owner->Remarks || $owner->DCRemarks)
                    <div class="border-t border-slate-100 pt-2">
                        <span class="text-slate-400 font-bold uppercase text-[9px] block">Office / DC Remarks</span>
                        <p class="text-slate-600 leading-normal block mt-0.5 italic">
                            {{ $owner->DCRemarks ?? $owner->Remarks }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Scheduling Form - High Density -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-2.5 pb-2 border-b border-slate-100 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-blue-600 text-lg">schedule</span>
                Set Visit Options (3 Slots Required)
            </h3>
            <p class="text-[10px] text-slate-400 uppercase font-semibold mb-3">Provide 3 distinct available options. The applicant will choose one from these.</p>

            <!-- Slot Policy Alert -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-4 text-xs font-semibold text-blue-800 leading-normal flex items-start gap-2">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">info</span>
                <div>
                    <p class="font-extrabold uppercase text-[9px] tracking-wider text-blue-700">Slot Scheduling Policy & Rules</p>
                    <ul class="list-disc pl-4 mt-1 space-y-0.5 text-[10px] text-blue-800/90 font-medium">
                        <li>Only **future dates** (from tomorrow onwards) are allowed. Past and current dates are disabled in the calendar.</li>
                        <li>Visits must be scheduled on the hour (e.g. 09:00 AM, 10:00 AM) between **09:00 AM and 05:00 PM**.</li>
                        <li>**Capacity Restriction:** Max **10 citizens** can be approved within any 1-hour slot in your block.</li>
                    </ul>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-rose-50 text-rose-800 border border-rose-100 px-3.5 py-2 rounded-xl mb-4 text-xs font-semibold">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('mmgay.bdo.schedule-save', $secureId) }}" method="POST" class="space-y-4">
                @csrf

                <!-- Slot 1 - Row Layout for Density -->
                <div class="bg-[#f8fafc] border border-slate-100 rounded-xl p-3">
                    <h4 class="text-[10px] uppercase font-bold text-slate-500 mb-2 flex items-center gap-1">
                        <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-extrabold text-[9px]">1</span>
                        Slot Option 1
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="slot_date_1" id="slot_date_1" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('slot_date_1', $application->visit_slot_1 ? date('Y-m-d', strtotime($application->visit_slot_1)) : '') }}" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Time <span class="text-rose-500">*</span></label>
                            <select name="slot_time_1" id="slot_time_1" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none" required>
                                <option value="">Select Time</option>
                                @for($hour = 9; $hour <= 16; $hour++)
                                    @php
                                        $t1 = sprintf('%02d:00', $hour);
                                        $label = date('h:i A', strtotime($t1));
                                        $selected = old('slot_time_1', $application->visit_slot_1 ? date('H:i', strtotime($application->visit_slot_1)) : '') === $t1 ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $t1 }}" {{ $selected }}>{{ $label }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Slot 2 -->
                <div class="bg-[#f8fafc] border border-slate-100 rounded-xl p-3">
                    <h4 class="text-[10px] uppercase font-bold text-slate-500 mb-2 flex items-center gap-1">
                        <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-extrabold text-[9px]">2</span>
                        Slot Option 2
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="slot_date_2" id="slot_date_2" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('slot_date_2', $application->visit_slot_2 ? date('Y-m-d', strtotime($application->visit_slot_2)) : '') }}" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Time <span class="text-rose-500">*</span></label>
                            <select name="slot_time_2" id="slot_time_2" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none" required>
                                <option value="">Select Time</option>
                                @for($hour = 9; $hour <= 16; $hour++)
                                    @php
                                        $t2 = sprintf('%02d:00', $hour);
                                        $label = date('h:i A', strtotime($t2));
                                        $selected = old('slot_time_2', $application->visit_slot_2 ? date('H:i', strtotime($application->visit_slot_2)) : '') === $t2 ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $t2 }}" {{ $selected }}>{{ $label }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Slot 3 -->
                <div class="bg-[#f8fafc] border border-slate-100 rounded-xl p-3">
                    <h4 class="text-[10px] uppercase font-bold text-slate-500 mb-2 flex items-center gap-1">
                        <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-extrabold text-[9px]">3</span>
                        Slot Option 3
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="slot_date_3" id="slot_date_3" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('slot_date_3', $application->visit_slot_3 ? date('Y-m-d', strtotime($application->visit_slot_3)) : '') }}" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Time <span class="text-rose-500">*</span></label>
                            <select name="slot_time_3" id="slot_time_3" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none" required>
                                <option value="">Select Time</option>
                                @for($hour = 9; $hour <= 16; $hour++)
                                    @php
                                        $t3 = sprintf('%02d:00', $hour);
                                        $label = date('h:i A', strtotime($t3));
                                        $selected = old('slot_time_3', $application->visit_slot_3 ? date('H:i', strtotime($application->visit_slot_3)) : '') === $t3 ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $t3 }}" {{ $selected }}>{{ $label }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Visit Instructions / Remarks (Optional)</label>
                    <textarea name="visit_instructions" rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none placeholder:text-slate-300" placeholder="e.g. Please bring original ID documents for physical verification at the flat site.">{{ old('visit_instructions', $application->visit_instructions) }}</textarea>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('mmgay.bdo.eligibility-list') }}" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-bold text-slate-500 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-bold flex items-center gap-1 shadow">
                        <span class="material-symbols-outlined text-[16px]">done</span> Confirm Schedule
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    // Capacity real-time alerts
    (function() {
        const dateFields = [1, 2, 3];
        dateFields.forEach(num => {
            const dateInput = document.getElementById('slot_date_' + num);
            const timeSelect = document.getElementById('slot_time_' + num);
            
            function checkCapacity() {
                const date = dateInput.value;
                const time = timeSelect.value;
                if (!date || !time) return;

                // Call AJAX capacity check API
                fetch('{{ route("mmgay.bdo.schedule.capacity-check") }}?date=' + date + '&exclude_id={{ $application->id }}')
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            const hr = parseInt(time.split(':')[0]);
                            const count = d.counts[hr] || 0;
                            
                            // Remove previous badge if any
                            const parent = timeSelect.parentElement;
                            let badge = parent.querySelector('.capacity-badge');
                            if (badge) badge.remove();

                            badge = document.createElement('span');
                            badge.className = 'capacity-badge text-[9px] font-bold block mt-1 px-1.5 py-0.5 rounded';
                            
                            if (count >= 10) {
                                badge.className += ' bg-rose-50 text-rose-700';
                                badge.textContent = '❌ Slot Full (' + count + '/10 scheduled)';
                            } else if (count >= 7) {
                                badge.className += ' bg-amber-50 text-amber-700';
                                badge.textContent = '⚠️ Filling Fast (' + count + '/10 scheduled)';
                            } else {
                                badge.className += ' bg-emerald-50 text-emerald-700';
                                badge.textContent = '✅ Available (' + count + '/10 scheduled)';
                            }
                            parent.appendChild(badge);
                        }
                    });
            }

            dateInput.addEventListener('change', checkCapacity);
            timeSelect.addEventListener('change', checkCapacity);
        });

        // Form Submission Loader
        const form = document.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        submitBtn.addEventListener('click', function(e) {
            // Trigger native HTML5 validation report
            if (!form.reportValidity()) {
                // If form is invalid, browser shows validation bubbles, keep button enabled
                return;
            }
            
            // If form is valid, prevent default, disable button, show spinner, and submit
            e.preventDefault();
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            submitBtn.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-white inline-block mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Confirming...
            `;
            
            Swal.fire({
                title: 'Submitting Slots...',
                text: 'Please wait, setting up the 3 slot choices.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            form.submit();
        });
    })();
</script>
@endsection
