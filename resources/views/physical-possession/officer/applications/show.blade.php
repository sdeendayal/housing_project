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
            <div class="pp-panel-head">Documents</div>
            <div class="pp-panel-body p-0">
                @foreach($application->documents as $doc)
                <div class="d-flex justify-content-between align-items-center px-2 py-2 border-bottom small">
                    <div class="min-w-0 me-2">
                        <strong class="d-block">{{ $doc->typeLabel() }}</strong>
                        <span class="text-muted text-truncate d-block">{{ $doc->original_name }}</span>
                    </div>
                    <a href="{{ route('pp.officer.document.download', [$application, $doc]) }}" class="btn btn-outline-primary pp-btn-sm-compact shrink-0">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
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
                    <div class="d-flex gap-2 mb-3">
                        <label class="pp-decision-option flex-fill">
                            <input type="radio" name="decision" value="approved" class="d-none pp-decision-radio" {{ old('decision', 'approved') === 'approved' ? 'checked' : '' }}>
                            <span class="pp-decision-card approve">
                                <i class="bi bi-check-circle"></i> Approve
                            </span>
                        </label>
                        <label class="pp-decision-option flex-fill">
                            <input type="radio" name="decision" value="rejected" class="d-none pp-decision-radio" {{ old('decision') === 'rejected' ? 'checked' : '' }}>
                            <span class="pp-decision-card reject">
                                <i class="bi bi-x-circle"></i> Reject
                            </span>
                        </label>
                    </div>
                    @error('decision')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                    <label class="form-label small fw-semibold mb-1">Remarks <span class="text-danger">*</span></label>
                    <textarea name="remarks" class="form-control form-control-sm mb-2 @error('remarks') is-invalid @enderror" rows="3" placeholder="Enter remarks for your decision" required>{{ old('remarks') }}</textarea>
                    @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div id="ppVisitFields">
                        <label class="form-label small fw-semibold mb-1">Meeting Schedule (Date & Time) <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="citizen_visit_date"
                               class="form-control form-control-sm mb-2 @error('citizen_visit_date') is-invalid @enderror"
                               value="{{ old('citizen_visit_date') }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}">
                        @error('citizen_visit_date')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                        <p class="text-muted small mb-2">Citizen will be asked to visit the office at this date and time.</p>

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
                            <span class="badge bg-{{ $action->action === 'approved' ? 'success' : 'danger' }}">
                                {{ ucfirst($action->action) }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Asset ID:</strong> {{ $action->asset_id ?? '—' }}</p>
                        <p class="mb-1"><strong>Application No:</strong> {{ $action->application_number }}</p>
                        <p class="mb-1"><strong>Officer:</strong> {{ $action->officer?->name ?? '—' }}</p>
                        <p class="mb-1"><strong>Remarks:</strong> {{ $action->remarks }}</p>
                        @if($action->action === 'approved')
                            @if($action->citizen_visit_date)
                            <p class="mb-1"><strong>Meeting Schedule:</strong> {{ $action->citizen_visit_date->format('d M Y, h:i A') }}</p>
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
</style>
@endpush

@push('scripts')
@if($application->status === 'pending')
<script>
(function () {
    const form = document.getElementById('ppDecideForm');
    const submitBtn = document.getElementById('ppDecideSubmitBtn');
    const visitFields = document.getElementById('ppVisitFields');
    const visitDateInput = form?.querySelector('[name="citizen_visit_date"]');
    const radios = form?.querySelectorAll('.pp-decision-radio');

    function updateSubmitBtn() {
        const selected = form?.querySelector('.pp-decision-radio:checked');
        if (!submitBtn || !selected) return;
        const isApprove = selected.value === 'approved';
        if (visitFields) {
            visitFields.style.display = isApprove ? '' : 'none';
        }
        if (visitDateInput) {
            visitDateInput.required = isApprove;
            if (!isApprove) {
                visitDateInput.value = '';
            }
        }
        if (isApprove) {
            submitBtn.className = 'btn btn-success w-100 pp-btn-sm-compact';
            submitBtn.textContent = 'Submit — Approve';
        } else {
            submitBtn.className = 'btn btn-danger w-100 pp-btn-sm-compact';
            submitBtn.textContent = 'Submit — Reject';
        }
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
        const visitDate = form.querySelector('[name="citizen_visit_date"]')?.value?.trim();

        if (!selected) {
            ppSwal({ icon: 'warning', title: 'Action Required', text: 'Please select Approve or Reject.' });
            return;
        }
        if (!remarks) {
            ppSwal({ icon: 'warning', title: 'Remarks Required', text: 'Please enter remarks before submitting.' });
            return;
        }
        const isApprove = selected.value === 'approved';
        if (isApprove && !visitDate) {
            ppSwal({ icon: 'warning', title: 'Schedule Required', text: 'Please select the meeting date and time for approval.' });
            return;
        }
        Swal.fire({
            icon: 'question',
            title: isApprove ? 'Approve Application?' : 'Reject Application?',
            text: 'Please confirm your decision. This cannot be undone.',
            showCancelButton: true,
            confirmButtonText: isApprove ? 'Yes, Approve' : 'Yes, Reject',
            cancelButtonText: 'Cancel',
            confirmButtonColor: isApprove ? '#059669' : '#dc2626',
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
