@extends('physical-possession.layouts.officer')

@section('page-title', 'Review Application')

@section('content')
<div class="row g-2">
    <div class="col-lg-8">
        <div class="pp-panel mb-2">
            <div class="pp-panel-head">
                <span>{{ $application->application_number }} <small class="text-muted fw-normal">{{ $application->slip_id }}</small></span>
                <span class="badge bg-{{ $application->statusBadgeClass() }}">{{ ucfirst($application->status) }}</span>
            </div>
            <div class="pp-panel-body">
                <div class="pp-detail-grid">
                    <div><div class="label">Applicant</div>{{ $application->applicant_name }}</div>
                    <div><div class="label">Father</div>{{ $application->father_name ?? '—' }}</div>
                    <div><div class="label">Mobile</div>{{ $application->mobile }}</div>
                    <div><div class="label">District</div>{{ $application->district_name ?? '—' }}</div>
                    <div><div class="label">Asset ID</div>{{ $application->asset_id ?? '—' }}</div>
                    <div><div class="label">Property</div>{{ $application->propertyRegistration?->AssetName ?? '—' }}</div>
                    <div class="col-span-2"><div class="label">Address</div>{{ $application->address ?? '—' }}</div>
                    <div class="col-span-2"><div class="label">Registration</div>{{ $application->registration_details ?? '—' }}</div>
                    <div><div class="label">Submitted</div>{{ $application->created_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <div class="pp-panel">
            <div class="pp-panel-head">Documents @if($application->status === 'pending')<small class="text-muted fw-normal">— select for send back</small>@endif</div>
            <div class="pp-panel-body p-0">
                @foreach($application->documents as $doc)
                <div class="d-flex justify-content-between align-items-start px-2 py-2 border-bottom small gap-2">
                    <div class="min-w-0 me-2 flex-grow-1 d-flex gap-2">
                        @if($application->status === 'pending')
                        <div class="pp-sendback-check shrink-0" data-sendback-only style="display:none">
                            <input class="form-check-input returned-doc-checkbox mt-1" type="checkbox"
                                   name="returned_documents[]" value="{{ $doc->id }}" form="ppDecideForm"
                                   id="return_doc_{{ $doc->id }}" {{ in_array($doc->id, old('returned_documents', [])) ? 'checked' : '' }}>
                        </div>
                        @endif
                        <div class="min-w-0">
                        <strong class="d-block" @if($application->status === 'pending') id="return_doc_label_{{ $doc->id }}" @endif>{{ $doc->typeLabel() }}</strong>
                        <span class="text-muted text-truncate d-block">{{ $doc->original_name }}</span>
                        @if($doc->review_status === 'returned')
                        <span class="badge bg-warning text-dark mt-1">Returned to applicant</span>
                        @elseif($doc->review_status === 'accepted')
                        <span class="badge bg-success mt-1">Accepted</span>
                        @endif
                        @if($doc->officer_remarks)
                        <span class="text-danger d-block mt-1"><em>Remark: {{ $doc->officer_remarks }}</em></span>
                        @endif
                        </div>
                    </div>
                    <a href="{{ route('pp.officer.document.download', [$application, $doc]) }}" class="btn btn-outline-primary pp-btn-sm-compact shrink-0">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
                @if($application->status === 'pending')
                <div class="px-2 pb-2 border-bottom pp-sendback-remark" data-sendback-only>
                    <input type="text" name="document_remarks[{{ $doc->id }}]" form="ppDecideForm"
                           class="form-control form-control-sm" placeholder="Optional remark for this document"
                           value="{{ old('document_remarks.'.$doc->id) }}">
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($application->status === 'pending')
        <div class="pp-panel mb-2">
            <div class="pp-panel-head">Officer Decision</div>
            <div class="pp-panel-body">
                <form method="POST" action="{{ route('pp.officer.application.decide', $application) }}" id="ppDecideForm">
                    @csrf

                    <label class="form-label small fw-semibold mb-2">Select Action <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <label class="pp-decision-option">
                            <input type="radio" name="decision" value="approved" class="d-none pp-decision-radio" {{ old('decision', 'approved') === 'approved' ? 'checked' : '' }}>
                            <span class="pp-decision-card approve">
                                <i class="bi bi-check-circle"></i> Approve
                            </span>
                        </label>
                        <label class="pp-decision-option">
                            <input type="radio" name="decision" value="sent_back" class="d-none pp-decision-radio" {{ old('decision') === 'sent_back' ? 'checked' : '' }}>
                            <span class="pp-decision-card sendback">
                                <i class="bi bi-arrow-return-left"></i> Send Back
                            </span>
                        </label>
                        <label class="pp-decision-option">
                            <input type="radio" name="decision" value="rejected" class="d-none pp-decision-radio" {{ old('decision') === 'rejected' ? 'checked' : '' }}>
                            <span class="pp-decision-card reject">
                                <i class="bi bi-x-circle"></i> Reject
                            </span>
                        </label>
                    </div>
                    @error('returned_documents')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                    @error('decision')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                    <label class="form-label small fw-semibold mb-1">Remarks <span class="text-danger">*</span></label>
                    <textarea name="remarks" class="form-control form-control-sm mb-2 @error('remarks') is-invalid @enderror" rows="3" placeholder="Enter remarks for your decision" required>{{ old('remarks') }}</textarea>
                    @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div id="ppVisitFields">
                        @php
                            $visitMin = now()->seconds(0)->minute(0);

                            // Old values for slots
                            $slotOldData = [];
                            foreach ([1, 2, 3] as $i) {
                                $visitOldValue = old("visit_slot_$i");
                                $visitOldDate = '';
                                $visitOldTime = '';
                                if ($visitOldValue) {
                                    try {
                                        $visitOldParsed = \Carbon\Carbon::parse($visitOldValue)->seconds(0)->minute(0);
                                        $visitOldDate = $visitOldParsed->format('Y-m-d');
                                        $visitOldTime = $visitOldParsed->format('H:i');
                                    } catch (\Throwable $e) {
                                        $visitOldDate = '';
                                        $visitOldTime = '';
                                    }
                                }
                                $slotOldData[$i] = ['date' => $visitOldDate, 'time' => $visitOldTime];
                            }
                        @endphp

                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="bi bi-info-circle-fill"></i> Please propose <strong>3 different visit slots</strong>. The citizen will choose one.
                        </div>

                        @foreach([1, 2, 3] as $i)
                        <div class="mb-3 border-bottom pb-2">
                            <label class="form-label small fw-semibold mb-1">Proposed Slot {{ $i }} <span class="text-danger">*</span></label>
                            <input type="hidden" name="visit_slot_{{ $i }}" id="ppCitizenVisitDate_{{ $i }}"
                                   value="{{ $slotOldData[$i]['date'] && $slotOldData[$i]['time'] ? $slotOldData[$i]['date'].'T'.$slotOldData[$i]['time'] : '' }}">
                            <div class="row g-2">
                                <div class="col-7">
                                    <input type="text" id="ppVisitDateOnly_{{ $i }}"
                                           class="form-control form-control-sm pp-visit-picker-slot @error('visit_slot_'.$i) is-invalid @enderror"
                                           value="{{ $slotOldData[$i]['date'] }}"
                                           placeholder="Select date"
                                           autocomplete="off"
                                           readonly>
                                </div>
                                <div class="col-5">
                                    <select id="ppVisitTimeSlot_{{ $i }}"
                                            class="form-select form-select-sm pp-visit-time-select @error('visit_slot_'.$i) is-invalid @enderror">
                                        <option value="">Time</option>
                                    </select>
                                </div>
                            </div>
                            @error('visit_slot_'.$i)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        @endforeach

                        <p class="text-muted small mb-2">Slots must be <strong>hourly</strong> (e.g. 09:00, 10:00). Allowed: <strong>09:00 AM – 05:00 PM</strong>. Max <strong>10 citizens per slot</strong> per district.</p>

                        <label class="form-label small fw-semibold mb-1">Visit Instructions <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="visit_instructions" class="form-control form-control-sm mb-2 @error('visit_instructions') is-invalid @enderror" rows="2" placeholder="e.g. Municipal Office, Rohtak — bring original ID and documents">{{ old('visit_instructions') }}</textarea>
                        @error('visit_instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn w-100 pp-btn-sm-compact" id="ppDecideSubmitBtn">Submit Decision</button>
                </form>
            </div>
        </div>
        @else
        <div class="pp-panel mb-2">
            <div class="pp-panel-head">Officer Action History</div>
            <div class="pp-panel-body small">
                @forelse($application->officerActions as $action)
                    <div class="{{ ! $loop->last ? 'border-bottom pb-2 mb-2' : '' }}">
                        <p class="mb-1"><strong>Action #{{ $loop->iteration }}:</strong>
                            <span class="badge bg-{{ match($action->action) { 'approved' => 'success', 'rejected' => 'danger', 'sent_back' => 'warning', default => 'secondary' } }}">
                                {{ $action->action === 'sent_back' ? 'Sent Back' : ucfirst($action->action) }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Asset ID:</strong> {{ $action->asset_id ?? '—' }}</p>
                        <p class="mb-1"><strong>Application No:</strong> {{ $action->application_number }}</p>
                        <p class="mb-1"><strong>Officer:</strong> {{ $action->officer?->name ?? '—' }}</p>
                        <p class="mb-1"><strong>Remarks:</strong> {{ $action->remarks }}</p>
                        @if($action->action === 'approved')
                            @if($action->visit_slot_1)
                            <p class="mb-1"><strong>Offered Slots:</strong></p>
                            <ul class="mb-1 ps-3">
                                <li>{{ $action->visit_slot_1->format('d M Y, h:i a') }} to {{ $action->visit_slot_1->copy()->addHour()->format('h:i a') }}</li>
                                <li>{{ $action->visit_slot_2 ? $action->visit_slot_2->format('d M Y, h:i a') . ' to ' . $action->visit_slot_2->copy()->addHour()->format('h:i a') : '—' }}</li>
                                <li>{{ $action->visit_slot_3 ? $action->visit_slot_3->format('d M Y, h:i a') . ' to ' . $action->visit_slot_3->copy()->addHour()->format('h:i a') : '—' }}</li>
                            </ul>
                            @endif
                            @if($application->citizen_visit_date)
                            <p class="mb-1"><strong>Confirmed Schedule:</strong> <span class="text-success fw-bold">{{ $application->citizen_visit_date->format('d M Y, h:i a') }} to {{ $application->citizen_visit_date->copy()->addHour()->format('h:i a') }}</span></p>
                            @else
                            <p class="mb-1"><strong>Confirmed Schedule:</strong> <span class="text-warning">Awaiting citizen slot selection</span></p>
                            @endif
                            @if($action->visit_instructions)
                            <p class="mb-1"><strong>Visit Instructions:</strong> {{ $action->visit_instructions }}</p>
                            @endif
                        @endif
                        <p class="mb-0"><strong>Action Date:</strong> {{ $action->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                @empty
                    <p class="mb-0 text-muted">No officer action recorded yet.</p>
                @endforelse
            </div>
        </div>
        @endif

        <div class="pp-panel">
            <div class="pp-panel-head">Timeline</div>
            <div class="pp-panel-body">
                <div class="pp-timeline">
                    @foreach($application->statusLogs as $log)
                    <div class="pp-timeline-item">
                        <strong>{{ ucfirst($log->new_status) }}</strong>
                        <div class="text-muted">{{ $log->created_at->format('d M Y') }}</div>
                        @if($log->remarks)<div>{{ str_ireplace(' by user', '', $log->remarks) }}</div>@endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.pp-decision-option { cursor: pointer; margin: 0; }
.pp-decision-card {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.5rem 0.4rem;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.78rem;
    font-weight: 700;
    color: #64748b;
    transition: all 0.15s;
}
.pp-decision-card.approve { color: #059669; }
.pp-decision-card.reject { color: #dc2626; }
.pp-decision-card.sendback { color: #d97706; }
.pp-decision-radio:checked + .pp-decision-card.approve {
    background: #ecfdf5;
    border-color: #059669;
    color: #047857;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
}
.pp-decision-radio:checked + .pp-decision-card.reject {
    background: #fef2f2;
    border-color: #dc2626;
    color: #b91c1c;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
}
.pp-decision-radio:checked + .pp-decision-card.sendback {
    background: #fffbeb;
    border-color: #d97706;
    color: #b45309;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.12);
}
.pp-sendback-remark { display: none; }
.pp-sendback-check .form-check-input { margin-top: 0.2rem; }
.pp-visit-picker.form-control[readonly] { background-color: #fff; cursor: pointer; }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
@if($application->status === 'pending')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
(function () {
    const form = document.getElementById('ppDecideForm');
    const submitBtn = document.getElementById('ppDecideSubmitBtn');
    const visitFields = document.getElementById('ppVisitFields');
    const visitMinValue = @json($visitMin->format('Y-m-d'));
    const slotOldData = @json($slotOldData);
    const radios = form?.querySelectorAll('.pp-decision-radio');
    const pickers = {};

    const sendBackRemarks = form?.querySelectorAll('.pp-sendback-remark');
    const sendBackChecks = document.querySelectorAll('[data-sendback-only]');

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function roundedMinTimeForToday() {
        const now = new Date();
        let hours = now.getHours() + 1;
        if (hours < 9) hours = 9;
        return pad(hours) + ':00';
    }

    function isSameDay(a, b) {
        return a.getFullYear() === b.getFullYear()
            && a.getMonth() === b.getMonth()
            && a.getDate() === b.getDate();
    }

    const visitTimeSlots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];

    function formatTimeLabel(value) {
        const parts = value.split(':');
        const hour24 = parseInt(parts[0], 10);
        const nextHour24 = hour24 + 1;
        
        const startPeriod = hour24 >= 12 ? 'pm' : 'am';
        const startHour12 = hour24 % 12 || 12;
        const startStr = pad(startHour12) + ':00 ' + startPeriod;
        
        const endPeriod = nextHour24 >= 12 ? 'pm' : 'am';
        const endHour12 = nextHour24 % 12 || 12;
        const endStr = pad(endHour12) + ':00 ' + endPeriod;
        
        return startStr + ' to ' + endStr;
    }

    function syncHiddenVisitDate(index) {
        const hiddenInput = document.getElementById('ppCitizenVisitDate_' + index);
        const dateInput = document.getElementById('ppVisitDateOnly_' + index);
        const timeSelect = document.getElementById('ppVisitTimeSlot_' + index);
        if (!hiddenInput || !dateInput || !timeSelect) return;
        const date = dateInput.value;
        const time = timeSelect.value;
        hiddenInput.value = (date && time) ? (date + 'T' + time) : '';
    }

    function minTimeForDate(dateStr) {
        if (!dateStr) {
            return '09:00';
        }
        const selected = new Date(dateStr + 'T00:00:00');
        if (Number.isNaN(selected.getTime()) || !isSameDay(selected, new Date())) {
            return '09:00';
        }
        return roundedMinTimeForToday();
    }

    const capacityCache = {};

    function loadCapacityAndPopulate(index, selectedDate, preferredTime) {
        const timeSelect = document.getElementById('ppVisitTimeSlot_' + index);
        if (!timeSelect) return;
        
        if (!selectedDate) {
            populateVisitTimeSlots(index, '', preferredTime, {});
            return;
        }

        if (capacityCache[selectedDate]) {
            populateVisitTimeSlots(index, selectedDate, preferredTime, capacityCache[selectedDate]);
            return;
        }

        timeSelect.innerHTML = '<option value="">Loading...</option>';
        timeSelect.disabled = true;

        fetch('{{ route("pp.officer.slots.capacity") }}?date=' + encodeURIComponent(selectedDate))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    capacityCache[selectedDate] = data.bookings || {};
                    populateVisitTimeSlots(index, selectedDate, preferredTime, capacityCache[selectedDate]);
                } else {
                    populateVisitTimeSlots(index, selectedDate, preferredTime, {});
                }
            })
            .catch(function (err) {
                console.error(err);
                populateVisitTimeSlots(index, selectedDate, preferredTime, {});
            })
            .finally(function () {
                timeSelect.disabled = false;
            });
    }

    function populateVisitTimeSlots(index, selectedDate, preferredTime, bookings) {
        const timeSelect = document.getElementById('ppVisitTimeSlot_' + index);
        if (!timeSelect) return;
        const minTime = minTimeForDate(selectedDate);
        const previous = preferredTime || timeSelect.value;
        timeSelect.innerHTML = '<option value="">Time</option>';

        bookings = bookings || {};

        visitTimeSlots.forEach(function (slot) {
            if (selectedDate && slot < minTime) {
                return;
            }
            const option = document.createElement('option');
            option.value = slot;
            
            const hour = parseInt(slot.split(':')[0], 10);
            const count = bookings[hour] || 0;
            
            let label = formatTimeLabel(slot);
            if (count > 0) {
                label += ' (' + count + '/10 booked)';
            }
            option.textContent = label;

            if (count >= 10) {
                option.disabled = true;
                option.textContent += ' [Full - Disabled]';
            }

            timeSelect.appendChild(option);
        });

        if (previous && Array.from(timeSelect.options).some(function (opt) { return opt.value === previous; })) {
            timeSelect.value = previous;
        } else {
            timeSelect.value = '';
        }

        syncHiddenVisitDate(index);
    }

    [1, 2, 3].forEach(function (i) {
        const dateInput = document.getElementById('ppVisitDateOnly_' + i);
        const timeSelect = document.getElementById('ppVisitTimeSlot_' + i);

        if (dateInput && typeof flatpickr !== 'undefined') {
            pickers[i] = flatpickr(dateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
                altInputClass: 'form-control form-control-sm pp-visit-picker-slot',
                minDate: visitMinValue,
                defaultDate: dateInput.value || null,
                disableMobile: true,
                allowInput: false,
                onChange: function (selectedDates) {
                    const dateStr = selectedDates[0]
                        ? selectedDates[0].getFullYear() + '-' + pad(selectedDates[0].getMonth() + 1) + '-' + pad(selectedDates[0].getDate())
                        : '';
                    loadCapacityAndPopulate(i, dateStr, '');
                },
            });
        }

        loadCapacityAndPopulate(i, dateInput?.value || '', slotOldData[i]?.time || '');
        timeSelect?.addEventListener('change', function () {
            syncHiddenVisitDate(i);
        });
    });

    function updateSubmitBtn() {
        const selected = form?.querySelector('.pp-decision-radio:checked');
        if (!submitBtn || !selected) return;
        const isApprove = selected.value === 'approved';
        const isSendBack = selected.value === 'sent_back';
        const isReject = selected.value === 'rejected';
        if (visitFields) {
            visitFields.style.display = isApprove ? '' : 'none';
        }
        [1, 2, 3].forEach(function (i) {
            const hiddenInput = document.getElementById('ppCitizenVisitDate_' + i);
            const timeSelect = document.getElementById('ppVisitTimeSlot_' + i);
            if (hiddenInput) {
                hiddenInput.required = isApprove;
            }
            if (timeSelect) {
                timeSelect.required = isApprove;
            }
            if (!isApprove) {
                pickers[i]?.clear();
                populateVisitTimeSlots(i, '', '');
            }
        });
        sendBackRemarks?.forEach(function (el) {
            el.style.display = isSendBack ? '' : 'none';
        });
        sendBackChecks?.forEach(function (el) {
            el.style.display = isSendBack ? '' : 'none';
            if (!isSendBack) {
                el.querySelectorAll('input[type="checkbox"]').forEach(function (cb) { cb.checked = false; });
            }
        });
        if (isApprove) {
            submitBtn.className = 'btn btn-success w-100 pp-btn-sm-compact';
            submitBtn.textContent = 'Submit — Approve';
        } else if (isSendBack) {
            submitBtn.className = 'btn btn-warning w-100 pp-btn-sm-compact text-dark';
            submitBtn.textContent = 'Submit — Send Back';
        } else {
            submitBtn.className = 'btn btn-danger w-100 pp-btn-sm-compact';
            submitBtn.textContent = 'Submit — Reject';
        }
    }

    function visitScheduleClientError(value) {
        if (!value) {
            return 'Please select date and time for all 3 meeting slots.';
        }
        if (!/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(value)) {
            return 'Please select a valid meeting date and time.';
        }
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) {
            return 'Please enter a valid meeting date and time.';
        }
        if (d.getSeconds() !== 0 || d.getMinutes() !== 0) {
            return 'Meeting time must be on the hour (e.g. 09:00, 10:00).';
        }
        const totalMinutes = (d.getHours() * 60) + d.getMinutes();
        if (totalMinutes < (9 * 60) || totalMinutes > (16 * 60)) {
            return 'Meeting time must be between 09:00 AM and 05:00 PM.';
        }
        if (d < new Date()) {
            return 'Meeting schedule cannot be in the past.';
        }
        return '';
    }

    radios?.forEach(function (radio) {
        radio.addEventListener('change', updateSubmitBtn);
    });
    updateSubmitBtn();

    let decideConfirmed = false;
    const ppLoading = document.getElementById('ppLoading');

    form?.addEventListener('submit', function (e) {
        if (decideConfirmed) {
            ppLoading?.classList.add('show');
            return;
        }

        e.preventDefault();
        ppLoading?.classList.remove('show');

        const selected = form.querySelector('.pp-decision-radio:checked');
        const remarks = form.querySelector('[name="remarks"]')?.value?.trim();

        if (!selected) {
            ppSwal({ icon: 'warning', title: 'Action Required', text: 'Please select Approve, Send Back, or Reject.' });
            return;
        }
        if (!remarks) {
            ppSwal({ icon: 'warning', title: 'Remarks Required', text: 'Please enter remarks before submitting.' });
            return;
        }
        const isApprove = selected.value === 'approved';
        const isSendBack = selected.value === 'sent_back';
        if (isApprove) {
            const slots = [];
            let err = '';
            for (let i = 1; i <= 3; i++) {
                const val = document.getElementById('ppCitizenVisitDate_' + i)?.value?.trim();
                const scheduleError = visitScheduleClientError(val);
                if (scheduleError) {
                    err = 'Slot ' + i + ': ' + scheduleError;
                    break;
                }
                slots.push(val);
            }
            if (err) {
                ppSwal({ icon: 'warning', title: 'Invalid Schedule', text: err });
                return;
            }
            if (new Set(slots).size !== 3) {
                ppSwal({ icon: 'warning', title: 'Duplicate Slots', text: 'All three proposed slots must be unique.' });
                return;
            }
        }
        if (isSendBack) {
            const checked = document.querySelectorAll('.returned-doc-checkbox:checked');
            if (!checked.length) {
                ppSwal({ icon: 'warning', title: 'Documents Required', text: 'Select at least one document to send back.' });
                return;
            }
        }
        const confirmTitle = isApprove ? 'Approve Application?' : (isSendBack ? 'Send Back for Correction?' : 'Reject Application?');
        const confirmText = isSendBack
            ? 'Citizen will re-upload selected documents and resubmit for your review.'
            : 'Please confirm your decision. This cannot be undone.';
        Swal.fire({
            icon: 'question',
            title: confirmTitle,
            text: confirmText,
            showCancelButton: true,
            confirmButtonText: isApprove ? 'Yes, Approve' : (isSendBack ? 'Yes, Send Back' : 'Yes, Reject'),
            cancelButtonText: 'Cancel',
            confirmButtonColor: isApprove ? '#059669' : (isSendBack ? '#d97706' : '#dc2626'),
            cancelButtonColor: '#64748b',
        }).then(function (result) {
            if (result.isConfirmed) {
                decideConfirmed = true;
                ppLoading?.classList.add('show');
                form.submit();
            } else {
                ppLoading?.classList.remove('show');
            }
        });
    });
})();
</script>
@endif
@endpush
