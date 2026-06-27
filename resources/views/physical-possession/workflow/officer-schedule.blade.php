@extends('physical-possession.layouts.officer')

@section('title', 'Schedule Physical Possession Visit')
@section('page-title', 'Schedule Meeting & Visit')

@section('content')
<div class="container-fluid py-4">
    <a href="{{ route('pp.officer.eligibility-list') }}" class="btn btn-link text-decoration-none text-muted mb-3 ps-0">
        <i class="bi bi-arrow-left me-1"></i>Back to Eligibility List
    </a>

    <div class="row g-4">
        <!-- Applicant & Property Details Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi-file-earmark-text text-primary me-2"></i>Application & Property Details</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-1">
                    <!-- Section 1: Applicant Info -->
                    <div class="mb-3">
                        <div class="fw-bold text-primary mb-2" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-person me-1"></i>Applicant Information
                        </div>
                        <div class="row g-2 text-slate-800" style="font-size: 0.8rem;">
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Physical Possession Application No.</label>
                                <div class="fw-bold text-slate-800">{{ $application->application_number }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Workflow Status</label>
                                <div>
                                    @php
                                        $badgeClass = match ($application->physical_possession_status) {
                                            'Eligible for Physical Possession' => 'bg-info text-info bg-opacity-10 border border-info border-opacity-20',
                                            'Visit Scheduled' => 'bg-warning text-warning-emphasis bg-opacity-10 border border-warning border-opacity-20',
                                            'Slot Selected' => 'bg-primary text-white border border-primary',
                                            'Physical Possession Submitted' => 'bg-primary text-white border border-primary',
                                            'Verified' => 'bg-success text-white border border-success',
                                            'Rejected' => 'bg-danger text-white border border-danger',
                                            default => 'bg-secondary text-white border border-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2 py-1 rounded-2 text-wrap text-start d-inline-block" style="font-size: 0.65rem; white-space: normal; line-height: 1.25;">
                                        {{ $application->physical_possession_status }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Applicant Name</label>
                                <div class="fw-semibold text-slate-800">{{ $application->applicant_name }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Father's Name</label>
                                <div class="fw-semibold text-slate-700">{{ $application->father_name ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Mobile Number</label>
                                <div class="fw-semibold text-slate-800">{{ $application->mobile }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Caste Category</label>
                                <div class="fw-semibold text-slate-800">{{ $property?->purchaser_category ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Marital Status</label>
                                <div class="fw-semibold text-slate-700">{{ $property?->purchaser_marital_status ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">PPP (Family ID)</label>
                                <div class="fw-semibold text-slate-800 font-monospace">{{ $property?->purchaser_ppp_id ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Member ID</label>
                                <div class="fw-semibold text-slate-800 font-monospace">{{ $property?->purchaser_member_id ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2 border-slate-100">

                    <!-- Section 2: Property Info -->
                    <div class="mb-3">
                        <div class="fw-bold text-primary mb-2" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-house-door me-1"></i>Property & Allotment Details
                        </div>
                        <div class="row g-2 text-slate-800" style="font-size: 0.8rem;">
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Property Name/Code</label>
                                <div class="fw-bold text-slate-800">{{ $property?->AssetName ?? $application->asset_name ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Property Size</label>
                                <div class="fw-semibold text-slate-800">{{ $property?->AssetSize ?? $application->asset_size }} {{ $property?->Unit ?? $application->asset_unit }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Allotment No</label>
                                <div class="fw-semibold text-slate-800">{{ $property?->purchaser_app_no ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Allotment Date</label>
                                <div class="fw-semibold text-slate-800">
                                    {{ $property?->purchaser_reg_date ? \Carbon\Carbon::parse($property->purchaser_reg_date)->format('d M Y') : '—' }}
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Sector / Ward</label>
                                <div class="fw-semibold text-slate-700">{{ $property?->SectorName ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">City / Town</label>
                                <div class="fw-semibold text-slate-700">{{ $property?->CityName ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">District</label>
                                <div class="fw-semibold text-slate-700">{{ $property?->DistrictName ?? $application->district_name ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2 border-slate-100">

                    <!-- Section 3: Financial Details -->
                    <div class="mb-3">
                        <div class="fw-bold text-primary mb-2" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-currency-rupee me-1"></i>Financial Details
                        </div>
                        <div class="row g-2 text-slate-800" style="font-size: 0.8rem;">
                            <div class="col-4">
                                <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Property Cost</label>
                                <div class="fw-bold text-slate-800">₹{{ number_format($property?->FlatCost ?? $application->flat_cost ?? 0, 2) }}</div>
                            </div>
                            <div class="col-4">
                                <label class="text-success mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Received Amount</label>
                                <div class="fw-bold text-success" title="Initial Deposit: ₹{{ number_format($initialDeposit ?? 0, 2) }} + Installments: ₹{{ number_format($installmentPaid ?? 0, 2) }}">₹{{ number_format($totalReceived ?? 0, 2) }}</div>
                            </div>
                            <div class="col-4">
                                <label class="text-danger mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Balance Amount</label>
                                <div class="fw-bold text-danger">₹{{ number_format($balanceAmount ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Meeting Slots Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-3 px-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-check text-primary me-2"></i>Schedule Meeting</h5>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('pp.officer.schedule-save', $application->secure_id) }}" method="POST">
                        @csrf
                        
                        <div class="table-responsive mb-2">
                            <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.85rem;">
                                <thead>
                                    <tr class="text-muted border-bottom" style="font-size: 0.75rem;">
                                        <th class="pb-2" style="width: 20%;">Slot</th>
                                        <th class="pb-2" style="width: 45%;">Date <span class="text-danger">*</span></th>
                                        <th class="pb-2" style="width: 35%;">Time Slot <span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Slot 1 -->
                                    <tr class="align-top">
                                        <td class="pt-3 fw-bold text-dark">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-3 px-2 py-1">Slot 1</span>
                                        </td>
                                        <td class="pt-2">
                                            <input type="date" name="slot_date_1" id="slot_date_1" class="form-control form-control-sm rounded-3 @error('slot_date_1') is-invalid @enderror" value="{{ old('slot_date_1', $application && $application->visit_slot_1 ? $application->visit_slot_1->format('Y-m-d') : '') }}" required min="{{ date('Y-m-d') }}">
                                            @error('slot_date_1')
                                                <div class="invalid-feedback fs-9 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="pt-2">
                                            <select name="slot_time_1" id="slot_time_1" class="form-select form-select-sm rounded-3 @error('slot_time_1') is-invalid @enderror" required>
                                                <option value="" disabled selected>Time</option>
                                                @php
                                                    $t1 = old('slot_time_1', $application && $application->visit_slot_1 ? $application->visit_slot_1->format('H:i:s') : '');
                                                @endphp
                                                <option value="09:00:00" {{ $t1 === '09:00:00' ? 'selected' : '' }}>09:00 AM - 10:00 AM</option>
                                                <option value="10:00:00" {{ $t1 === '10:00:00' ? 'selected' : '' }}>10:00 AM - 11:00 AM</option>
                                                <option value="11:00:00" {{ $t1 === '11:00:00' ? 'selected' : '' }}>11:00 AM - 12:00 PM</option>
                                                <option value="12:00:00" {{ $t1 === '12:00:00' ? 'selected' : '' }}>12:00 PM - 01:00 PM</option>
                                                <option value="13:00:00" {{ $t1 === '13:00:00' ? 'selected' : '' }}>01:00 PM - 02:00 PM</option>
                                                <option value="14:00:00" {{ $t1 === '14:00:00' ? 'selected' : '' }}>02:00 PM - 03:00 PM</option>
                                                <option value="15:00:00" {{ $t1 === '15:00:00' ? 'selected' : '' }}>03:00 PM - 04:00 PM</option>
                                                <option value="16:00:00" {{ $t1 === '16:00:00' ? 'selected' : '' }}>04:00 PM - 05:00 PM</option>
                                            </select>
                                            @error('slot_time_1')
                                                <div class="invalid-feedback fs-9 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>

                                    <!-- Slot 2 -->
                                    <tr class="align-top">
                                        <td class="pt-3 fw-bold text-dark">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-3 px-2 py-1">Slot 2</span>
                                        </td>
                                        <td class="pt-2">
                                            <input type="date" name="slot_date_2" id="slot_date_2" class="form-control form-control-sm rounded-3 @error('slot_date_2') is-invalid @enderror" value="{{ old('slot_date_2', $application && $application->visit_slot_2 ? $application->visit_slot_2->format('Y-m-d') : '') }}" required min="{{ date('Y-m-d') }}">
                                            @error('slot_date_2')
                                                <div class="invalid-feedback fs-9 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="pt-2">
                                            <select name="slot_time_2" id="slot_time_2" class="form-select form-select-sm rounded-3 @error('slot_time_2') is-invalid @enderror" required>
                                                <option value="" disabled selected>Time</option>
                                                @php
                                                    $t2 = old('slot_time_2', $application && $application->visit_slot_2 ? $application->visit_slot_2->format('H:i:s') : '');
                                                @endphp
                                                <option value="09:00:00" {{ $t2 === '09:00:00' ? 'selected' : '' }}>09:00 AM - 10:00 AM</option>
                                                <option value="10:00:00" {{ $t2 === '10:00:00' ? 'selected' : '' }}>10:00 AM - 11:00 AM</option>
                                                <option value="11:00:00" {{ $t2 === '11:00:00' ? 'selected' : '' }}>11:00 AM - 12:00 PM</option>
                                                <option value="12:00:00" {{ $t2 === '12:00:00' ? 'selected' : '' }}>12:00 PM - 01:00 PM</option>
                                                <option value="13:00:00" {{ $t2 === '13:00:00' ? 'selected' : '' }}>01:00 PM - 02:00 PM</option>
                                                <option value="14:00:00" {{ $t2 === '14:00:00' ? 'selected' : '' }}>02:00 PM - 03:00 PM</option>
                                                <option value="15:00:00" {{ $t2 === '15:00:00' ? 'selected' : '' }}>03:00 PM - 04:00 PM</option>
                                                <option value="16:00:00" {{ $t2 === '16:00:00' ? 'selected' : '' }}>04:00 PM - 05:00 PM</option>
                                            </select>
                                            @error('slot_time_2')
                                                <div class="invalid-feedback fs-9 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>

                                    <!-- Slot 3 -->
                                    <tr class="align-top">
                                        <td class="pt-3 fw-bold text-dark">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-3 px-2 py-1">Slot 3</span>
                                        </td>
                                        <td class="pt-2">
                                            <input type="date" name="slot_date_3" id="slot_date_3" class="form-control form-control-sm rounded-3 @error('slot_date_3') is-invalid @enderror" value="{{ old('slot_date_3', $application && $application->visit_slot_3 ? $application->visit_slot_3->format('Y-m-d') : '') }}" required min="{{ date('Y-m-d') }}">
                                            @error('slot_date_3')
                                                <div class="invalid-feedback fs-9 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="pt-2">
                                            <select name="slot_time_3" id="slot_time_3" class="form-select form-select-sm rounded-3 @error('slot_time_3') is-invalid @enderror" required>
                                                <option value="" disabled selected>Time</option>
                                                @php
                                                    $t3 = old('slot_time_3', $application && $application->visit_slot_3 ? $application->visit_slot_3->format('H:i:s') : '');
                                                @endphp
                                                <option value="09:00:00" {{ $t3 === '09:00:00' ? 'selected' : '' }}>09:00 AM - 10:00 AM</option>
                                                <option value="10:00:00" {{ $t3 === '10:00:00' ? 'selected' : '' }}>10:00 AM - 11:00 AM</option>
                                                <option value="11:00:00" {{ $t3 === '11:00:00' ? 'selected' : '' }}>11:00 AM - 12:00 PM</option>
                                                <option value="12:00:00" {{ $t3 === '12:00:00' ? 'selected' : '' }}>12:00 PM - 01:00 PM</option>
                                                <option value="13:00:00" {{ $t3 === '13:00:00' ? 'selected' : '' }}>01:00 PM - 02:00 PM</option>
                                                <option value="14:00:00" {{ $t3 === '14:00:00' ? 'selected' : '' }}>02:00 PM - 03:00 PM</option>
                                                <option value="15:00:00" {{ $t3 === '15:00:00' ? 'selected' : '' }}>03:00 PM - 04:00 PM</option>
                                                <option value="16:00:00" {{ $t3 === '16:00:00' ? 'selected' : '' }}>04:00 PM - 05:00 PM</option>
                                            </select>
                                            @error('slot_time_3')
                                                <div class="invalid-feedback fs-9 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Instructions Textarea -->
                        <div class="mb-3">
                            <label for="visit_instructions" class="form-label text-dark fw-semibold small mb-1">Visit Instructions</label>
                            <textarea name="visit_instructions" id="visit_instructions" class="form-control form-control-sm rounded-3 @error('visit_instructions') is-invalid @enderror" rows="2" placeholder="Enter instructions for the citizen (e.g. documents to bring, precautions)...">{{ old('visit_instructions', $application ? $application->visit_instructions : '') }}</textarea>
                            @error('visit_instructions')
                                <div class="invalid-feedback fs-9 mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted small" style="font-size: 0.75rem;">These instructions will be displayed on the citizen's dashboard.</div>
                        </div>

                        <!-- Dynamic capacity & business hours guidelines card -->
                        <div class="alert alert-primary bg-primary bg-opacity-10 border border-primary border-opacity-20 rounded-3 p-2.5 mb-3" style="font-size: 0.75rem;">
                            <div class="fw-bold text-primary mb-1"><i class="bi bi-info-circle-fill me-1"></i> Guidelines / महत्वपूर्ण निर्देश:</div>
                            <ul class="ps-3 mb-0 text-muted leading-relaxed" style="font-size: 0.72rem;">
                                <li><strong>Hours / समय:</strong> Slots must be between <strong>09:00 AM and 05:00 PM</strong> only.</li>
                                <li><strong>Capacity / क्षमता:</strong> Max <strong>10 visits</strong> per 1-hour slot in this district. Full slots are disabled.</li>
                                <li><strong>Past slots / पिछला समय:</strong> Future slots only; past slots on today's date are disabled.</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('pp.officer.eligibility-list') }}" class="btn btn-sm btn-outline-secondary px-3 rounded-pill">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary px-3 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i>Save & Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const excludeId = {{ $application->id }};
    
    const slotTimes = [
        { value: '09:00:00', hour: 9, label: '09:00 AM - 10:00 AM' },
        { value: '10:00:00', hour: 10, label: '10:00 AM - 11:00 AM' },
        { value: '11:00:00', hour: 11, label: '11:00 AM - 12:00 PM' },
        { value: '12:00:00', hour: 12, label: '12:00 PM - 01:00 PM' },
        { value: '13:00:00', hour: 13, label: '01:00 PM - 02:00 PM' },
        { value: '14:00:00', hour: 14, label: '02:00 PM - 03:00 PM' },
        { value: '15:00:00', hour: 15, label: '03:00 PM - 04:00 PM' },
        { value: '16:00:00', hour: 16, label: '04:00 PM - 05:00 PM' }
    ];

    function getLocalTodayAndHour() {
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        return {
            todayStr: `${yyyy}-${mm}-${dd}`,
            currentHour: now.getHours()
        };
    }

    function updateTimeSlots(slotIndex) {
        const dateInput = document.getElementById('slot_date_' + slotIndex);
        const timeSelect = document.getElementById('slot_time_' + slotIndex);
        const selectedDate = dateInput.value;

        if (!selectedDate) {
            timeSelect.innerHTML = '<option value="" disabled selected>Time</option>';
            return;
        }

        // Fetch capacity counts for this date
        fetch(`{{ route('pp.officer.schedule.capacity-check') }}?date=${selectedDate}&exclude_id=${excludeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const counts = data.counts;
                    const { todayStr, currentHour } = getLocalTodayAndHour();
                    const isToday = (selectedDate === todayStr);
                    const isPastDate = (selectedDate < todayStr);

                    // Preserve selected value if it exists
                    const currentValue = timeSelect.value;
                    
                    let html = '<option value="" disabled selected>Time</option>';
                    
                    slotTimes.forEach(slot => {
                        let isDisabled = false;
                        let reason = '';
                        const bookingCount = counts[slot.hour] || 0;

                        // 1. Check if the slot is in the past
                        if (isPastDate) {
                            isDisabled = true;
                            reason = ' (Past)';
                        } else if (isToday && slot.hour <= currentHour) {
                            isDisabled = true;
                            reason = ' (Past)';
                        }
                        
                        // 2. Check if the slot is full
                        if (bookingCount >= 10) {
                            isDisabled = true;
                            reason = ' (Full - ' + bookingCount + '/10)';
                        } else if (bookingCount > 0) {
                            reason = ' (' + bookingCount + '/10 booked)';
                        }

                        const isSelected = (currentValue === slot.value) ? 'selected' : '';
                        const disabledAttr = isDisabled ? 'disabled' : '';
                        
                        html += `<option value="${slot.value}" ${isSelected} ${disabledAttr}>${slot.label}${reason}</option>`;
                    });
                    
                    timeSelect.innerHTML = html;
                }
            })
            .catch(err => {
                console.error("Error fetching slot capacities", err);
            });
    }

    [1, 2, 3].forEach(index => {
        const dateInput = document.getElementById('slot_date_' + index);
        if (dateInput) {
            dateInput.addEventListener('change', () => updateTimeSlots(index));
            // Trigger once on page load if date is prefilled
            if (dateInput.value) {
                updateTimeSlots(index);
            }
        }
    });
});
</script>
@endpush
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
