@extends('physical-possession.layouts.officer')

@section('title', 'Physical Possession Applications')
@section('page-title', 'Possession Applications')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card premium-card border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">All Applications</h5>
                    <p class="text-muted small mb-0">List of physical possession applications currently under verification or scheduled.</p>
                </div>
                <form action="{{ route('pp.officer.possession-applications') }}" method="GET" class="d-flex gap-2 align-items-center">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Name, Mobile, Application No..." value="{{ $search }}">
                    </div>
                    <button type="submit" class="btn btn-primary px-4 btn-action">Search</button>
                    @if($search)
                        <a href="{{ route('pp.officer.possession-applications') }}" class="btn btn-outline-secondary btn-action">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 pp-applications-table">
                    <thead class="table-light text-uppercase text-muted">
                        <tr>
                            <th class="ps-3" style="width: 60px;">S.No.</th>
                            <th>Physical Possession Application No.</th>
                            <th>Applicant</th>
                            <th>District</th>
                            <th>Meeting Date & Slot</th>
                            <th>Workflow Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr>
                                <td class="ps-3 fw-semibold text-muted">
                                    {{ ($applications->currentPage() - 1) * $applications->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $app->application_number }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $app->applicant_name }}</div>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $app->mobile }}</small>
                                </td>
                                <td>
                                    <span class="text-slate-700">{{ $app->district_name }}</span>
                                </td>
                                <td>
                                    @if(in_array($app->physical_possession_status, ['Slot Selected', 'Physical Possession Submitted', 'Verified', 'Rejected']))
                                        <div class="fw-bold text-success">
                                            @if(strtotime($app->meeting_slot))
                                                {{ \Carbon\Carbon::parse($app->meeting_slot)->format('d M Y') }}
                                                <div class="small fw-normal text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($app->meeting_slot)->format('h:i A') }}</div>
                                            @else
                                                {{ $app->meeting_slot }}
                                            @endif
                                        </div>
                                    @elseif($app->physical_possession_status === 'Visit Scheduled')
                                        <div class="fw-semibold text-dark">
                                            <span class="text-muted small d-block">Pending citizen choice</span>
                                            <button type="button" class="btn btn-outline-primary btn-sm py-0.5 px-2 mt-1 rounded-pill d-inline-flex align-items-center gap-1.5 hover:-translate-y-px transition-transform cursor-pointer" 
                                                    style="font-size: 0.68rem; font-weight: 600;"
                                                    onclick="showSlotsModal('{{ $app->application_number }}', '{{ $app->visit_slot_1 ? $app->visit_slot_1->format('d M Y - h:i A') : '—' }}', '{{ $app->visit_slot_2 ? $app->visit_slot_2->format('d M Y - h:i A') : '—' }}', '{{ $app->visit_slot_3 ? $app->visit_slot_3->format('d M Y - h:i A') : '—' }}')">
                                                <i class="bi bi-calendar3"></i> 3 Slots Offered
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted small">Not Scheduled</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match ($app->physical_possession_status) {
                                            'Eligible for Physical Possession' => 'bg-info text-info bg-opacity-10 border border-info border-opacity-20',
                                            'Visit Scheduled' => 'bg-warning text-warning-emphasis bg-opacity-10 border border-warning border-opacity-20',
                                            'Slot Selected' => 'bg-primary text-white border border-primary',
                                            'Physical Possession Submitted' => 'bg-primary text-white border border-primary',
                                            'Verified' => 'bg-success text-white border border-success',
                                            'Rejected' => 'bg-danger text-white border border-danger',
                                            default => 'bg-secondary text-white border border-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2.5 py-1.5 rounded-3 fs-8">
                                        {{ $app->physical_possession_status }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    @if(in_array($app->physical_possession_status, ['Slot Selected', 'Physical Possession Submitted']))
                                        <a href="{{ route('pp.officer.verify-form', $app->secure_id) }}" class="btn btn-primary btn-action text-nowrap rounded-pill shadow-sm">
                                            <i class="bi bi-shield-check me-1"></i>Perform Visit
                                        </a>
                                    @elseif($app->physical_possession_status === 'Eligible for Physical Possession')
                                        <a href="{{ route('pp.officer.schedule-form', $app->secure_id) }}" class="btn btn-primary btn-action text-nowrap rounded-pill shadow-sm">
                                            <i class="bi bi-calendar-plus me-1"></i>Schedule Visit
                                        </a>
                                    @else
                                        <a href="{{ route('pp.officer.verify-form', $app->secure_id) }}" class="btn btn-outline-secondary btn-action text-nowrap rounded-pill">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-1 mb-3 d-block text-slate-300"></i>
                                    No possession applications found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .premium-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
    }
    .pp-applications-table th {
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 10px 12px !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .pp-applications-table td {
        font-size: 0.78rem !important;
        padding: 10px 12px !important;
        vertical-align: middle !important;
    }
    .pp-applications-table tr:hover {
        background-color: rgba(30, 64, 175, 0.02) !important;
    }
    .btn-action {
        font-size: 0.7rem !important;
        padding: 5px 12px !important;
        font-weight: 600 !important;
        letter-spacing: 0.2px;
        transition: all 0.2s ease;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(30, 64, 175, 0.15);
    }
    /* Pagination responsiveness */
    .pagination {
        margin-bottom: 0 !important;
        gap: 2px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .page-link {
        font-size: 0.75rem !important;
        padding: 5px 10px !important;
        border-radius: 6px !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
    }
    .page-item.active .page-link {
        background-color: var(--pp-primary) !important;
        border-color: var(--pp-primary) !important;
        color: #fff !important;
    }
    .page-link:hover {
        background-color: #f1f5f9 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function parseSlot(slotStr) {
        if (!slotStr || slotStr === '—') return null;
        try {
            const parts = slotStr.split(' - ');
            const dateParts = parts[0].trim().split(' ');
            return {
                day: dateParts[0],
                month: dateParts[1],
                year: dateParts[2],
                time: parts[1].trim()
            };
        } catch(e) {
            return null;
        }
    }

    function makeSlotHtml(slotData, optionNum, themeColor) {
        if (!slotData) return '';
        const borderStyle = `border: 1px solid #eef2f6; background: #fafbfe;`;
        const blockHeaderColor = `background: ${themeColor}; color: #fff;`;
        
        return `
            <div class="d-flex align-items-center gap-3 p-2.5 rounded-3 mb-2" style="${borderStyle}">
                <!-- Calendar Block -->
                <div class="bg-white rounded-3 overflow-hidden text-center shadow-sm" style="width: 52px; height: 56px; border: 1px solid #e2e8f0; flex-shrink: 0;">
                    <div style="${blockHeaderColor} font-size: 8px; font-weight: 800; padding: 2px 0; text-transform: uppercase; letter-spacing: 0.5px;">Slot ${optionNum}</div>
                    <div style="font-size: 18px; font-weight: 800; color: #1e293b; line-height: 1.1; padding-top: 3px;">${slotData.day}</div>
                    <div style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-top: -1px;">${slotData.month}</div>
                </div>
                <!-- Time & Day Details -->
                <div class="text-start flex-grow-1">
                    <span class="badge bg-light text-secondary px-2 py-0.5 rounded-pill fw-bold uppercase tracking-wider mb-1" style="font-size: 0.58rem; letter-spacing: 0.3px;">Proposed Time</span>
                    <h6 class="fw-extrabold text-slate-800 m-0" style="font-size: 0.88rem; line-height: 1.2;">${slotData.time}</h6>
                    <p class="text-slate-400 m-0 mt-0.5" style="font-size: 0.65rem; font-weight: 500;">Year: ${slotData.year}</p>
                </div>
            </div>
        `;
    }

    function showSlotsModal(appNo, slot1, slot2, slot3) {
        const s1 = parseSlot(slot1);
        const s2 = parseSlot(slot2);
        const s3 = parseSlot(slot3);

        let slotsHtml = '';
        if (s1) slotsHtml += makeSlotHtml(s1, 1, '#4f46e5');
        if (s2) slotsHtml += makeSlotHtml(s2, 2, '#7c3aed');
        if (s3) slotsHtml += makeSlotHtml(s3, 3, '#2563eb');

        Swal.fire({
            title: '<div class="text-indigo-950 fw-bold fs-5 mb-1"><i class="bi bi-calendar3 me-2 text-indigo-600"></i>Offered Visit Slots</div><div class="text-muted fw-normal" style="font-size: 0.72rem; letter-spacing: 0.2px;">Physical Possession Application No: ' + appNo + '</div>',
            html: `
                <div class="text-center mt-2 px-1">
                    <p class="text-muted mb-3" style="font-size: 0.78rem; font-weight: 500; line-height: 1.4;">The District Officer has proposed the following three visit slots for on-site physical verification:</p>
                    <div class="d-flex flex-column">
                        ${slotsHtml}
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Dismiss',
            confirmButtonColor: '#4f46e5',
            customClass: {
                popup: 'rounded-4 border-0 p-3 shadow-lg'
            }
        });
    }
</script>
@endpush
@endsection
