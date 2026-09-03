@extends('physical-possession.layouts.officer')

@section('title', 'Verify Physical Possession Submission')
@section('page-title', 'Verify Submission')

@section('content')
<div class="container-fluid pt-2 pb-3">
    <a href="{{ request()->input('from') === 'caste' ? route('pp.officer.caste-eligibility') : route('pp.officer.possession-applications') }}" class="btn btn-link text-decoration-none text-muted mb-1 ps-0" style="font-size: 0.85rem;">
        <i class="bi bi-arrow-left me-1"></i>Back to {{ request()->input('from') === 'caste' ? 'Caste Eligibility' : 'Applications' }}
    </a>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">Please correct the following errors:</h6>
            <ul class="mb-0 ps-3" style="font-size: 0.8rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(in_array($application->physical_possession_status, ['Slot Selected', 'Physical Possession Submitted', 'Site Verified']))
        <!-- Perform Verification Flow (Active Submission) -->
        <form id="verificationForm" action="{{ route('pp.officer.verify-save', $application->secure_id) }}{{ request()->input('from') ? '?from='.request()->input('from') : '' }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Default status to Verified (automatic site verification) -->
            <input type="hidden" name="status" value="Verified">

            <div class="row g-3">
                <!-- Left: Application Details & Timeline -->
                <div class="col-lg-6">
                    <!-- Details Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-header bg-white border-0 pt-3 px-3 pb-1">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Application & Property Details</h6>
                        </div>
                        <div class="card-body px-3 pb-3 pt-1">
                            <!-- Section 1: Applicant Info -->
                            <div class="mb-2">
                                <div class="fw-bold text-primary mb-1.5" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
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
                                                    'Site Verified' => 'bg-info text-white border border-info',
                                                    'Verified' => 'bg-success text-white border border-success',
                                                    'Rejected' => 'bg-danger text-white border border-danger',
                                                    default => 'bg-secondary text-white border border-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-2 py-1 rounded-2 text-wrap text-start d-inline-block" style="font-size: 0.65rem; white-space: normal; line-height: 1.25;">
                                                {{ \App\Models\PhysicalPossessionApplication::getDisplayStatus($application->physical_possession_status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Applicant Name</label>
                                        <div class="fw-semibold">{{ $application->applicant_name }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Father's Name</label>
                                        <div class="fw-semibold text-slate-700">{{ $application->father_name ?? '—' }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Mobile Number</label>
                                        <div class="fw-semibold">{{ $application->mobile }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Caste Category</label>
                                        <div class="fw-semibold">{{ $property?->purchaser_category ?? '—' }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Marital Status</label>
                                        <div class="fw-semibold text-slate-700">{{ $property?->purchaser_marital_status ?? '—' }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">PPP (Family ID)</label>
                                        <div class="fw-semibold font-monospace">{{ $property?->purchaser_ppp_id ?? '—' }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Member ID</label>
                                        <div class="fw-semibold font-monospace">{{ $property?->purchaser_member_id ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-2 border-slate-100">

                            <!-- Section 2: Property Info -->
                            <div class="mb-2">
                                <div class="fw-bold text-primary mb-1.5" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="bi bi-house-door me-1"></i>Property & Allotment Details
                                </div>
                                <div class="row g-2 text-slate-800" style="font-size: 0.8rem;">
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Property Name/Code</label>
                                        <div class="fw-bold">{{ $property->AssetName ?? $application->asset_name ?? '—' }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Property Size</label>
                                        <div class="fw-semibold">{{ $property->AssetSize ?? $application->asset_size }} {{ $property->Unit ?? $application->asset_unit }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Allotment No</label>
                                        <div class="fw-semibold">{{ $property?->purchaser_app_no ?? '—' }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Allotment Date</label>
                                        <div class="fw-semibold">
                                            {{ $property?->purchaser_reg_date ? \Carbon\Carbon::parse($property->purchaser_reg_date)->format('d M Y') : '—' }}
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Sector / Ward</label>
                                        <div class="fw-semibold text-slate-700">{{ $property->SectorName ?? '—' }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">City / Town</label>
                                        <div class="fw-semibold text-slate-700">{{ $property->CityName ?? '—' }}</div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">District</label>
                                        <div class="fw-semibold text-slate-700">{{ $property->DistrictName ?? $application->district_name }}</div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-2 border-slate-100">

                            <!-- Section 3: Financial Details -->
                            <div class="mb-2">
                                <div class="fw-bold text-primary mb-1.5" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="bi bi-currency-rupee me-1"></i>Financial Details
                                </div>
                                <div class="row g-2 text-slate-800" style="font-size: 0.8rem;">
                                    <div class="col-4">
                                        <label class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Property Cost</label>
                                        <div class="fw-bold">@indianCurrency($property->FlatCost ?? $application->flat_cost)</div>
                                    </div>
                                    <div class="col-4">
                                        <label class="text-success mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Received Amount</label>
                                        <div class="fw-bold text-success" title="Initial Deposit: @indianCurrency($initialDeposit) + Installments: @indianCurrency($installmentPaid)">@indianCurrency($totalReceived)</div>
                                    </div>
                                    <div class="col-4">
                                        <label class="text-danger mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Balance Amount</label>
                                        <div class="fw-bold text-danger">@indianCurrency($balanceAmount)</div>
                                    </div>
                                </div>
                            </div>

                            @if($application->meeting_slot || $application->visit_instructions || $application->visit_slot_1)
                            <hr class="my-2 border-slate-100">
                            @endif

                            @if($application->meeting_slot)
                            <div class="col-12 mt-2 mb-2">
                                <div class="p-2 rounded-3 border border-success border-opacity-20 d-flex align-items-center gap-2" style="background-color: rgba(25, 135, 84, 0.03);">
                                    <i class="bi bi-calendar-check-fill text-success fs-5"></i>
                                    <div>
                                        <label class="text-success mb-0 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Confirmed Visit Slot</label>
                                        <div class="fw-bold text-success" style="font-size: 0.85rem;">
                                            @if(strtotime($application->meeting_slot))
                                                {{ \Carbon\Carbon::parse($application->meeting_slot)->format('d M Y - h:i A') }}
                                            @else
                                                {{ $application->meeting_slot }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($application->visit_slot_1 || $application->visit_slot_2 || $application->visit_slot_3)
                            <div class="mt-2 mb-2">
                                <label class="text-muted mb-1 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Alternate Visit Options</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2" style="font-size: 0.68rem; font-weight: 500;">
                                        Option 1: {{ $application->visit_slot_1 ? $application->visit_slot_1->format('d M Y - h:i A') : '—' }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2" style="font-size: 0.68rem; font-weight: 500;">
                                        Option 2: {{ $application->visit_slot_2 ? $application->visit_slot_2->format('d M Y - h:i A') : '—' }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2" style="font-size: 0.68rem; font-weight: 500;">
                                        Option 3: {{ $application->visit_slot_3 ? $application->visit_slot_3->format('d M Y - h:i A') : '—' }}
                                    </span>
                                </div>
                            </div>
                            @endif

                            @if($application->visit_instructions)
                            <div class="col-12 mt-2">
                                <div class="p-2 bg-light rounded-3 border border-slate-200">
                                    <label class="text-muted mb-0.5 block fw-bold text-primary" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;"><i class="bi bi-info-circle-fill me-1"></i>Visit Instructions</label>
                                    <div class="text-slate-700 whitespace-pre-line" style="font-size: 0.78rem; line-height: 1.3;">{{ $application->visit_instructions }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Timeline Card -->
                    @if($application->statusLogs->isNotEmpty())
                        <div class="card border-0 shadow-sm rounded-4 mb-3">
                            <div class="card-header bg-white border-0 pt-3 px-3">
                                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Status Timeline</h6>
                            </div>
                            <div class="card-body px-3 pb-3 pt-1">
                                <div class="position-relative" style="font-size: 0.8rem;">
                                    @foreach($application->statusLogs as $log)
                                        @php
                                            $dotColor = match(strtolower($log->new_status)) {
                                                'approved', 'verified' => '#198754',
                                                'rejected' => '#dc3545',
                                                'returned' => '#0dcaf0',
                                                'slot selected' => '#6f42c1',
                                                'visit scheduled' => '#ffc107',
                                                default => '#6c757d'
                                            };
                                        @endphp
                                        <div class="d-flex gap-3 mb-3 position-relative">
                                            <div class="d-flex flex-column align-items-center shrink-0">
                                                <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; margin-top: 5px; background-color: {{ $dotColor }}; border: 2px solid #fff; box-shadow: 0 0 0 2px {{ $dotColor }}33; z-index: 2;"></span>
                                                @if(!$loop->last)
                                                    <div class="vr bg-secondary opacity-20 flex-grow-1 my-1" style="width: 2px; min-height: 25px; z-index: 1;"></div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-0.5">
                                                    <span class="fw-bold text-dark text-capitalize" style="font-size: 0.8rem;">
                                                        {{ $log->new_status }}
                                                        @if($log->changer)
                                                            <small class="text-muted fw-normal" style="text-transform: none;">
                                                                (by {{ $log->changer->name }})
                                                            </small>
                                                        @endif
                                                    </span>
                                                    <span class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                                                </div>
                                                @if($log->remarks)
                                                    <p class="text-muted mb-0" style="font-size: 0.78rem; line-height: 1.3;">{{ $log->remarks }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- E-Possession Details Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-header bg-white border-0 pt-3 px-3 pb-1">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check text-primary me-2"></i>E-Possession Status</h6>
                        </div>
                        <div class="card-body px-3 pb-3 pt-1">
                            <div class="row g-2 text-slate-800" style="font-size: 0.8rem;">
                                <div class="col-12">
                                    <span class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Signed Application</span>
                                    @if($application->possession_certificate)
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="text-success fw-bold" style="font-size: 0.76rem;"><i class="bi bi-check-circle-fill me-1"></i>Uploaded</span>
                                            <a href="{{ asset('storage/' . $application->possession_certificate) }}" target="_blank" class="btn btn-xs btn-outline-danger py-0.5 px-2 rounded-pill font-semibold" style="font-size: 0.68rem; padding: 1px 8px;">
                                                <i class="bi bi-file-pdf"></i> View
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted d-block mt-1" style="font-size: 0.76rem;"><i class="bi bi-dash-circle me-1"></i>Pending</span>
                                    @endif
                                </div>
                                <div class="col-12 mt-2.5">
                                    <span class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Final Possession Letter</span>
                                    @if($application->site_engineer_file)
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="text-success fw-bold" style="font-size: 0.76rem;"><i class="bi bi-check-circle-fill me-1"></i>Uploaded</span>
                                            <a href="{{ asset('storage/' . $application->site_engineer_file) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0.5 px-2 rounded-pill font-semibold" style="font-size: 0.68rem; padding: 1px 8px;">
                                                <i class="bi bi-file-earmark-arrow-down"></i> View
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted d-block mt-1" style="font-size: 0.76rem;"><i class="bi bi-dash-circle me-1"></i>Pending</span>
                                    @endif
                                </div>
                                <div class="col-12 mt-2.5 border-top pt-2">
                                    <span class="text-muted mb-0.5 block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Overall E-Verify Status</span>
                                    @if($application->possession_certificate && $application->site_engineer_file)
                                        <span class="badge bg-success text-white rounded-2 px-2 py-1 mt-1" style="font-size: 0.68rem;">Completed</span>
                                    @else
                                        <span class="badge bg-warning text-warning-emphasis bg-opacity-10 border border-warning border-opacity-20 rounded-2 px-2 py-1 mt-1" style="font-size: 0.68rem;">Pending Final Submission</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- On-Site Verification Steps Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-header bg-white border-0 pt-3 px-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Verification Steps</h6>
                        </div>
                        <div class="card-body px-3 pb-3 pt-2">
                            <div class="text-muted" style="font-size: 0.78rem; line-height: 1.4;">
                                @if($application->physical_possession_status === 'Site Verified')
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1.5 d-flex gap-2">
                                            <i class="bi bi-1-circle-fill text-primary"></i>
                                            <span>Download prefilled application & possession letter format.</span>
                                        </li>
                                        <li class="mb-1.5 d-flex gap-2">
                                            <i class="bi bi-2-circle-fill text-primary"></i>
                                            <span>Obtain citizen/applicant physical signature.</span>
                                        </li>
                                        <li class="mb-1.5 d-flex gap-2">
                                            <i class="bi bi-3-circle-fill text-primary"></i>
                                            <span>Upload the signed PDF & Final Possession Letter.</span>
                                        </li>
                                        <li class="d-flex gap-2">
                                            <i class="bi bi-4-circle-fill text-primary"></i>
                                            <span>Click "Submit Final E-Possession" to complete.</span>
                                        </li>
                                    </ul>
                                @else
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1.5 d-flex gap-2">
                                            <i class="bi bi-1-circle-fill text-primary"></i>
                                            <span>Visit the site with the applicant.</span>
                                        </li>
                                        <li class="mb-1.5 d-flex gap-2">
                                            <i class="bi bi-2-circle-fill text-primary"></i>
                                            <span>Click "Capture GPS Location" at the plot location.</span>
                                        </li>
                                        <li class="mb-1.5 d-flex gap-2">
                                            <i class="bi bi-3-circle-fill text-primary"></i>
                                            <span>Upload Plot Photo with Applicant.</span>
                                        </li>
                                        <li class="mb-1.5 d-flex gap-2">
                                            <i class="bi bi-4-circle-fill text-primary"></i>
                                            <span>Add on-site remarks/comments.</span>
                                        </li>
                                        <li class="d-flex gap-2">
                                            <i class="bi bi-5-circle-fill text-primary"></i>
                                            <span>Click "Verify & Submit Site Verification" to proceed.</span>
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Submission Form Card -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-body px-3 pb-3 pt-3">
                            @if($application->physical_possession_status === 'Site Verified')
                                <!-- Step 2: E-Possession Verification -->
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold small mb-1.5"><i class="bi bi-geo-alt-fill text-primary"></i> Captured Site Verification Details</label>
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="row g-2 mb-2" style="font-size: 0.8rem;">
                                            <div class="col-6">
                                                <span class="text-muted block mb-0.5" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Latitude</span>
                                                <div class="font-monospace fw-bold text-slate-800">{{ $application->latitude }}</div>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted block mb-0.5" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Longitude</span>
                                                <div class="font-monospace fw-bold text-slate-800">{{ $application->longitude }}</div>
                                            </div>
                                        </div>
                                        @if($application->plot_image)
                                            <div class="mb-2">
                                                <span class="text-muted block mb-1" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Plot Photo with Applicant</span>
                                                <div class="border rounded-2 p-1 bg-white text-center">
                                                    <img src="{{ asset('storage/' . $application->plot_image) }}" class="img-fluid rounded-2" style="max-height: 180px; object-fit: contain;">
                                                </div>
                                            </div>
                                        @endif
                                        @if($application->remarks)
                                            <div>
                                                <span class="text-muted block mb-0.5" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Verification Remarks</span>
                                                <div class="text-slate-700 bg-white p-2 rounded-2 border" style="font-size: 0.76rem; line-height: 1.35;">{{ $application->remarks }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <hr class="my-3 border-slate-100">

                                <!-- 1. Physical Possession Application (Signed) -->
                                <div class="mb-3 p-3 bg-light rounded-3 border">
                                    <div class="d-flex flex-wrap flex-sm-nowrap align-items-center justify-content-between gap-2 mb-2">
                                        <div>
                                            <label class="form-label text-dark fw-bold small mb-0 d-block" style="font-size: 0.82rem;">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>1. Upload Physical Possession Application (Signed) <span class="text-danger">*</span>
                                            </label>
                                            <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.25;">
                                                Physical Possession Application PDF &bull; Download, get signed by citizen, and upload below
                                            </small>
                                        </div>
                                        <div class="d-flex gap-1.5 shrink-0">
                                            <a href="{{ route('pp.officer.download-certificate', ['application' => $application->secure_id, 'inline' => 1]) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 py-1 px-2.5 rounded-pill fw-bold" style="font-size: 0.76rem;">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="{{ route('pp.officer.download-certificate', $application->secure_id) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded-pill fw-bold" style="font-size: 0.76rem;">
                                                <i class="bi bi-file-earmark-arrow-down"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                    <input type="file" name="possession_certificate" id="possession_certificate" required accept="application/pdf" class="form-control form-control-sm bg-white" style="font-size: 0.78rem;">
                                    <div class="form-text text-muted mb-0 mt-1" style="font-size: 0.66rem;">PDF only (Max file size 500 KB)</div>
                                </div>

                                <!-- 2. Final Possession Letter -->
                                <div class="mb-3 p-3 bg-light rounded-3 border">
                                    <div class="d-flex flex-wrap flex-sm-nowrap align-items-center justify-content-between gap-2 mb-2">
                                        <div>
                                            <label class="form-label text-dark fw-bold small mb-0 d-block" style="font-size: 0.82rem;">
                                                <i class="bi bi-file-earmark-text-fill text-primary me-1"></i>2. Upload Final Possession Letter <span class="text-danger">*</span>
                                            </label>
                                            <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.25;">
                                                Housing_For_All_Possession_Letter_Hindi_Unicode &bull; Download letter format, complete/sign, and upload below
                                            </small>
                                        </div>
                                        <div class="d-flex gap-1.5 shrink-0">
                                            <a href="{{ route('pp.officer.download-possession-letter-template', ['inline' => 1]) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 py-1 px-2.5 rounded-pill fw-bold" style="font-size: 0.76rem;">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="{{ route('pp.officer.download-possession-letter-template') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded-pill fw-bold" style="font-size: 0.76rem;">
                                                <i class="bi bi-file-earmark-arrow-down"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                    <input type="file" name="site_engineer_file" id="site_engineer_file" required accept="application/pdf" class="form-control form-control-sm bg-white" style="font-size: 0.78rem;">
                                    <div class="form-text text-muted mb-0 mt-1" style="font-size: 0.66rem;">PDF only (Max file size 500 KB)</div>
                                </div>

                                <hr class="my-3 border-slate-100">

                                <button type="submit" class="btn btn-sm btn-primary w-100 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">
                                    <i class="bi bi-check-circle me-1"></i>Submit Final E-Possession
                                </button>
                            @else
                                <!-- Step 1: Active Site Verification -->
                                <!-- 1. GPS Location Capture -->
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold small mb-1.5">1. Capture GPS Coordinates <span class="text-danger">*</span></label>
                                    
                                    <div id="location-container" class="rounded-3 border border-secondary border-opacity-25 bg-light p-2 mb-2 d-flex align-items-center gap-2">
                                        <div class="text-secondary">
                                            <i id="location-icon" class="bi bi-geo-fill fs-5 text-secondary"></i>
                                        </div>
                                        <div id="location-status" class="text-dark m-0" style="font-size: 0.76rem;">
                                            Location not captured. Click "Capture GPS Location" below.
                                        </div>
                                    </div>

                                    <button type="button" id="btn-get-location" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 py-1 px-3 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                        <i class="bi bi-geo-alt"></i>
                                        Capture GPS Location
                                    </button>

                                    <div class="row g-2 mt-2">
                                        <div class="col-6">
                                            <label class="text-muted uppercase tracking-wider mb-0.5 block" style="font-size: 0.68rem; font-weight: 600;">Latitude <span class="text-danger">*</span></label>
                                            <input type="text" name="latitude" id="latitude" readonly required class="form-control form-control-sm bg-light text-dark font-monospace" style="font-size: 0.78rem;" placeholder="Latitude">
                                        </div>
                                        <div class="col-6">
                                            <label class="text-muted uppercase tracking-wider mb-0.5 block" style="font-size: 0.68rem; font-weight: 600;">Longitude <span class="text-danger">*</span></label>
                                            <input type="text" name="longitude" id="longitude" readonly required class="form-control form-control-sm bg-light text-dark font-monospace" style="font-size: 0.78rem;" placeholder="Longitude">
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3 border-slate-100">

                                <!-- 2. Upload Plot Photo with Applicant -->
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold small mb-1">2. Upload plot photo with applicant <span class="text-danger">*</span></label>
                                    <input type="file" name="plot_image" id="plot_image" disabled required accept="image/jpeg,image/jpg,image/png" class="form-control form-control-sm" style="font-size: 0.78rem;">
                                    <div class="form-text text-muted" style="font-size: 0.65rem;">PNG, JPG, JPEG (Max file size 500 KB)</div>
                                </div>

                                <hr class="my-3 border-slate-100">

                                <!-- Remarks (Verification Remarks) -->
                                <div class="mb-3">
                                    <label for="remarks" class="form-label text-dark fw-semibold small mb-1">3. Verification Remarks / Comments <span class="text-danger">*</span></label>
                                    <textarea name="remarks" id="remarks" required rows="3" class="form-control form-control-sm" placeholder="Add verification remarks here..." style="font-size: 0.8rem;"></textarea>
                                    <div class="form-text text-muted" style="font-size: 0.68rem;">Provide details of physical matching, plot dimensions, or checklist checks.</div>
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary w-100 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">
                                    <i class="bi bi-check-circle me-1"></i>Verify & Submit Site Verification
                                </button>
                                <button type="button" id="btn-citizen-absent" class="btn btn-sm btn-outline-danger w-100 py-2 rounded-pill fw-bold mt-3" style="font-size: 0.85rem;">
                                    <i class="bi bi-person-x me-1"></i>Citizen Absent / Reschedule
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <!-- Completed/Read-only flow -->
        <div class="row g-4">
            <!-- Left: Application Details & Timeline & Outcome -->
            <div class="col-lg-6">
                <!-- Details Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Application & Property Details</h5>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <!-- Section 1: Applicant Info -->
                        <div class="mb-3">
                            <div class="fw-bold text-primary mb-2" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-person me-1"></i>Applicant Information
                            </div>
                            <div class="row g-3 text-slate-800" style="font-size: 0.85rem;">
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Physical Possession Application No.</label>
                                    <div class="fw-bold text-slate-800">{{ $application->application_number }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Workflow Status</label>
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
                                        <span class="badge {{ $badgeClass }} px-2.5 py-1.2 rounded-2 text-wrap text-start d-inline-block" style="font-size: 0.72rem; white-space: normal; line-height: 1.25;">
                                            {{ \App\Models\PhysicalPossessionApplication::getDisplayStatus($application->physical_possession_status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Applicant Name</label>
                                    <div class="fw-semibold">{{ $application->applicant_name }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Father's Name</label>
                                    <div class="fw-semibold text-slate-700">{{ $application->father_name ?? '—' }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Mobile Number</label>
                                    <div class="fw-semibold">{{ $application->mobile }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Caste Category</label>
                                    <div class="fw-semibold">{{ $property?->purchaser_category ?? '—' }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Marital Status</label>
                                    <div class="fw-semibold text-slate-700">{{ $property?->purchaser_marital_status ?? '—' }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">PPP (Family ID)</label>
                                    <div class="fw-semibold font-monospace">{{ $property?->purchaser_ppp_id ?? '—' }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Member ID</label>
                                    <div class="fw-semibold font-monospace">{{ $property?->purchaser_member_id ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3 border-slate-100">

                        <!-- Section 2: Property Info -->
                        <div class="mb-3">
                            <div class="fw-bold text-primary mb-2" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-house-door me-1"></i>Property & Allotment Details
                            </div>
                            <div class="row g-3 text-slate-800" style="font-size: 0.85rem;">
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Property Name/Code</label>
                                    <div class="fw-bold">{{ $property->AssetName ?? $application->asset_name ?? '—' }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Property Size</label>
                                    <div class="fw-semibold">{{ $property->AssetSize ?? $application->asset_size }} {{ $property->Unit ?? $application->asset_unit }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Allotment No</label>
                                    <div class="fw-semibold">{{ $property?->purchaser_app_no ?? '—' }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Allotment Date</label>
                                    <div class="fw-semibold">
                                        {{ $property?->purchaser_reg_date ? \Carbon\Carbon::parse($property->purchaser_reg_date)->format('d M Y') : '—' }}
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Sector / Ward</label>
                                    <div class="fw-semibold text-slate-700">{{ $property->SectorName ?? '—' }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">City / Town</label>
                                    <div class="fw-semibold text-slate-700">{{ $property->CityName ?? '—' }}</div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">District</label>
                                    <div class="fw-semibold text-slate-700">{{ $property->DistrictName ?? $application->district_name }}</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3 border-slate-100">

                        <!-- Section 3: Financial Details -->
                        <div class="mb-3">
                            <div class="fw-bold text-primary mb-2" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-currency-rupee me-1"></i>Financial Details
                            </div>
                            <div class="row g-3 text-slate-800" style="font-size: 0.85rem;">
                                <div class="col-4">
                                    <label class="text-muted small uppercase tracking-wider mb-1 block">Property Cost</label>
                                    <div class="fw-bold">@indianCurrency($property->FlatCost ?? $application->flat_cost)</div>
                                </div>
                                <div class="col-4">
                                    <label class="text-success small uppercase tracking-wider mb-1 block">Received Amount</label>
                                    <div class="fw-bold text-success" title="Initial Deposit: @indianCurrency($initialDeposit) + Installments: @indianCurrency($installmentPaid)">@indianCurrency($totalReceived)</div>
                                </div>
                                <div class="col-4">
                                    <label class="text-danger small uppercase tracking-wider mb-1 block">Balance Amount</label>
                                    <div class="fw-bold text-danger">@indianCurrency($balanceAmount)</div>
                                </div>
                            </div>
                        </div>

                        @if(in_array($application->physical_possession_status, ['Physical Possession Submitted', 'Verified', 'Rejected']))
                        <hr class="my-3 border-slate-100">
                        <div class="col-sm-12">
                            <label class="text-muted small uppercase tracking-wider mb-1 block text-success fw-semibold">Confirmed Visit Slot</label>
                            <div class="fw-bold text-success fs-6">
                                <i class="bi bi-calendar-check-fill me-1"></i>
                                @if(strtotime($application->meeting_slot))
                                    {{ \Carbon\Carbon::parse($application->meeting_slot)->format('d M Y - h:i A') }}
                                @else
                                    {{ $application->meeting_slot }}
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($application->visit_slot_1 || $application->visit_slot_2 || $application->visit_slot_3)
                        <div class="mt-3 mb-2">
                            <label class="text-muted small uppercase tracking-wider mb-1 block">Alternate Visit Options</label>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-2" style="font-size: 0.72rem; font-weight: 500;">
                                    Option 1: {{ $application->visit_slot_1 ? $application->visit_slot_1->format('d M Y - h:i A') : '—' }}
                                </span>
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-2" style="font-size: 0.72rem; font-weight: 500;">
                                    Option 2: {{ $application->visit_slot_2 ? $application->visit_slot_2->format('d M Y - h:i A') : '—' }}
                                </span>
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-2" style="font-size: 0.72rem; font-weight: 500;">
                                    Option 3: {{ $application->visit_slot_3 ? $application->visit_slot_3->format('d M Y - h:i A') : '—' }}
                                </span>
                            </div>
                        </div>
                        @endif

                        @if($application->visit_instructions)
                        <div class="col-12 mt-2">
                            <div class="p-3 bg-light rounded-3 border border-slate-200">
                                <label class="text-muted small uppercase tracking-wider mb-1 block fw-bold text-primary"><i class="bi bi-info-circle-fill me-1"></i>Visit Instructions</label>
                                <div class="text-slate-700 small whitespace-pre-line">{{ $application->visit_instructions }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Status Timeline Card -->
                @if($application->statusLogs->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Status Timeline</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="position-relative">
                                @foreach($application->statusLogs as $log)
                                    <div class="d-flex gap-3 mb-3 position-relative">
                                        <div class="d-flex flex-column align-items-center shrink-0">
                                            @php
                                                $dotClass = match(strtolower($log->new_status)) {
                                                    'approved', 'verified' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    'returned' => 'bg-info',
                                                    'slot selected' => 'bg-indigo text-white',
                                                    'visit scheduled' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="rounded-circle {{ $dotClass }} d-inline-block" style="width: 10px; height: 10px; margin-top: 5px; z-index: 2;"></span>
                                            @if(!$loop->last)
                                                <div class="vr bg-secondary opacity-20 flex-grow-1 my-1" style="width: 2px; min-height: 25px; z-index: 1;"></div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-dark small text-capitalize">
                                                    {{ $log->new_status }}
                                                    @if($log->changer)
                                                        <small class="text-muted fw-normal" style="text-transform: none;">
                                                            (by {{ $log->changer->name }})
                                                        </small>
                                                    @endif
                                                </span>
                                                <span class="text-muted font-monospace fs-8">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                                            </div>
                                            @if($log->remarks)
                                                <p class="text-muted small mb-0">{{ $log->remarks }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Completed Verification info -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-success border-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-fill-check text-success me-2"></i>Verification Outcome</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3 text-slate-700">
                            <div class="col-sm-6">
                                <span class="text-muted small">Processed By:</span>
                                <div class="fw-bold">{{ $application->verifiedByUser ? $application->verifiedByUser->name : 'System' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small">Processed At:</span>
                                <div class="fw-bold">{{ $application->verified_at ? $application->verified_at->format('d M Y, h:i A') : '—' }}</div>
                            </div>
                            <div class="col-12">
                                <span class="text-muted small block">Remarks:</span>
                                <div class="bg-light p-3 rounded-3 text-break italic small">"{{ $application->remarks ?? 'No remarks provided.' }}"</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Maps & Upload Previews -->
            <div class="col-lg-6">
                <!-- GPS & Map Coordinates -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-primary me-2"></i>GPS Map Location</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($application->latitude && $application->longitude)
                            <div class="bg-light rounded-3 overflow-hidden border mb-3">
                                <iframe src="https://maps.google.com/maps?q={{ $application->latitude }},{{ $application->longitude }}&z=16&output=embed" width="100%" height="280" frameborder="0" style="border:0;" allowfullscreen></iframe>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-slate-600">
                                    <i class="bi bi-pin-angle me-1 text-primary"></i><strong>Lat:</strong> {{ $application->latitude }} · <strong>Lng:</strong> {{ $application->longitude }}
                                </div>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $application->latitude }},{{ $application->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Open Google Maps
                                </a>
                            </div>
                            @if($application->image_capture_datetime)
                                <div class="text-muted small font-monospace"><i class="bi bi-clock me-1"></i>Captured At: {{ $application->image_capture_datetime->format('d M Y, h:i A') }}</div>
                            @endif
                        @else
                            <div class="text-center text-muted py-4">No GPS coordinate details captured.</div>
                        @endif
                    </div>
                </div>

                <!-- Plot Image Preview Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-image text-primary me-2"></i>Plot Site Photo</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($application->plot_image)
                            <div class="border rounded-3 p-2 bg-light mb-3 text-center">
                                <img src="{{ asset('storage/' . $application->plot_image) }}" class="img-fluid rounded-3 border" style="max-height: 350px; object-fit: contain;">
                            </div>
                            <div class="text-center">
                                <a href="{{ asset('storage/' . $application->plot_image) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    <i class="bi bi-arrows-fullscreen me-1"></i>View Full Image
                                </a>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">No plot photo uploaded.</div>
                        @endif
                    </div>
                </div>

                <!-- Possession Certificate preview -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-check text-primary me-2"></i>Physical Possession Application (Signed)</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($application->possession_certificate)
                            @php
                                $isPdf = str_ends_with(strtolower($application->possession_certificate), '.pdf');
                            @endphp
                            @if ($isPdf)
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-file-earmark-pdf-fill fs-2 text-danger"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Physical Possession Application PDF (Signed)</h6>
                                            <small class="text-muted">Signed document uploaded by site engineer</small>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $application->possession_certificate) }}" target="_blank" class="btn btn-danger btn-sm px-3 rounded-pill">
                                        <i class="bi bi-file-pdf me-1"></i>View PDF
                                    </a>
                                </div>
                            @else
                                <div class="border rounded-3 p-2 bg-light mb-3 text-center">
                                    <img src="{{ asset('storage/' . $application->possession_certificate) }}" class="img-fluid rounded-3 border" style="max-height: 280px; object-fit: contain;">
                                </div>
                                <div class="text-center">
                                    <a href="{{ asset('storage/' . $application->possession_certificate) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                                        <i class="bi bi-arrows-fullscreen me-1"></i>View Full Document Image
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted py-4">No possession certificate document uploaded.</div>
                        @endif
                    </div>
                </div>

                <!-- Final Possession Letter preview -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-check text-primary me-2"></i>Final Possession Letter</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($application->site_engineer_file)
                            @php
                                $isPdf = str_ends_with(strtolower($application->site_engineer_file), '.pdf');
                            @endphp
                            @if ($isPdf)
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-file-earmark-pdf-fill fs-2 text-primary"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Final Possession Letter PDF</h6>
                                            <small class="text-muted">Document uploaded by Site Engineer</small>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $application->site_engineer_file) }}" target="_blank" class="btn btn-primary btn-sm px-3 rounded-pill">
                                        <i class="bi bi-file-pdf me-1"></i>View PDF
                                    </a>
                                </div>
                            @else
                                <div class="border rounded-3 p-2 bg-light mb-3 text-center">
                                    <img src="{{ asset('storage/' . $application->site_engineer_file) }}" class="img-fluid rounded-3 border" style="max-height: 280px; object-fit: contain;">
                                </div>
                                <div class="text-center">
                                    <a href="{{ asset('storage/' . $application->site_engineer_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                                        <i class="bi bi-arrows-fullscreen me-1"></i>View Full Document Image
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted py-4">No final possession letter uploaded.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnGetLocation = document.getElementById('btn-get-location');
        const statusText = document.getElementById('location-status');
        const statusIcon = document.getElementById('location-icon');
        const statusContainer = document.getElementById('location-container');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const plotImageInput = document.getElementById('plot_image');

        if (btnGetLocation) {
            btnGetLocation.addEventListener('click', function() {
                statusText.innerText = "Requesting GPS coordinates from your device...";
                statusIcon.className = "bi bi-hourglass-split text-warning animate-spin";
                statusContainer.style.borderColor = "#ffeeba";
                statusContainer.style.backgroundColor = "#fff3cd";

                if (!navigator.geolocation) {
                    statusText.innerText = "Browser Error: Geolocation is not supported by your browser.";
                    statusIcon.className = "bi bi-x-circle-fill text-danger";
                    statusContainer.style.borderColor = "#f5c6cb";
                    statusContainer.style.backgroundColor = "#f8d7da";
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        latitudeInput.value = lat;
                        longitudeInput.value = lng;

                        statusText.innerHTML = `<strong>Location captured successfully!</strong><br>Latitude: ${lat} · Longitude: ${lng}`;
                        statusIcon.className = "bi bi-geo-alt-fill text-success fs-4";
                        statusContainer.style.borderColor = "#c3e6cb";
                        statusContainer.style.backgroundColor = "#d4edda";
                        
                        // Enable photo input
                        if (plotImageInput) {
                            plotImageInput.removeAttribute('disabled');
                        }
                    },
                    function(error) {
                        let errorMsg = "Unable to retrieve your location. ";
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg += "Permission denied. Please grant location access in your browser settings.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg += "Location details unavailable from network/GPS sensors.";
                                break;
                            case error.TIMEOUT:
                                errorMsg += "Request timed out. Please check GPS signal strength.";
                                break;
                            default:
                                errorMsg += "An unknown location error occurred.";
                        }
                        statusText.innerText = errorMsg;
                        statusIcon.className = "bi bi-exclamation-triangle-fill text-danger fs-4";
                        statusContainer.style.borderColor = "#f5c6cb";
                        statusContainer.style.backgroundColor = "#f8d7da";
                        
                        latitudeInput.value = '';
                        longitudeInput.value = '';
                        if (plotImageInput) {
                            plotImageInput.setAttribute('disabled', 'disabled');
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        }

        // File selection instant validations
        const possessionCertificateInput = document.getElementById('possession_certificate');

        if (plotImageInput) {
            plotImageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileName = file.name.toLowerCase();
                    const isImage = file.type.startsWith('image/') || fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png');
                    const maxSize = 500 * 1024; // 500KB

                    if (!isImage) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Format',
                            text: 'Please upload only image files (JPG, JPEG, PNG) for the Plot Photo.',
                            confirmButtonColor: '#3085d6'
                        });
                        this.value = ''; // clear select
                        return;
                    }

                    if (file.size > maxSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: 'Plot Photo size must not exceed 500 KB.',
                            confirmButtonColor: '#3085d6'
                        });
                        this.value = '';
                        return;
                    }
                }
            });
        }

        if (possessionCertificateInput) {
            possessionCertificateInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileName = file.name.toLowerCase();
                    const isPdf = file.type === 'application/pdf' || fileName.endsWith('.pdf');
                    const maxSize = 2 * 1024 * 1024; // 2MB

                    if (!isPdf) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Format',
                            text: 'Please upload only PDF files for the Physical Possession Application.',
                            confirmButtonColor: '#3085d6'
                        });
                        this.value = ''; // clear select
                        return;
                    }

                    if (file.size > maxSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: 'Physical Possession Application size must not exceed 500 KB.',
                            confirmButtonColor: '#3085d6'
                        });
                        this.value = '';
                        return;
                    }
                }
            });
        }

        const siteEngineerFileInput = document.getElementById('site_engineer_file');
        if (siteEngineerFileInput) {
            siteEngineerFileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileName = file.name.toLowerCase();
                    const isAllowed = file.type === 'application/pdf' || fileName.endsWith('.pdf');
                    const maxSize = 500 * 1024; // 500KB

                    if (!isAllowed) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Format',
                            text: 'Please upload only PDF file for the Final Possession Letter.',
                            confirmButtonColor: '#3085d6'
                        });
                        this.value = ''; // clear select
                        return;
                    }

                    if (file.size > maxSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: 'Final Possession Letter size must not exceed 500 KB.',
                            confirmButtonColor: '#3085d6'
                        });
                        this.value = '';
                        return;
                    }
                }
            });
        }

        // Form submit frontend validation
        const verifyForm = document.getElementById('verificationForm');
        if (verifyForm) {
            verifyForm.addEventListener('submit', function(e) {
                const actionInput = verifyForm.querySelector('input[name="action"]');
                if (actionInput && actionInput.value === 'reschedule') {
                    return true;
                }

                const isStep2 = document.getElementById('site_engineer_file') !== null;

                if (isStep2) {
                    const certInput = document.getElementById('possession_certificate');
                    const siteInput = document.getElementById('site_engineer_file');
                    const certFile = certInput ? certInput.files.length : 0;
                    const siteFile = siteInput ? siteInput.files.length : 0;

                    if (!certFile) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Signed Application Required',
                            text: 'Please upload the Physical Possession Application (Signed).',
                            confirmButtonColor: '#3085d6'
                        });
                        return false;
                    }

                    if (!siteFile) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Final Possession Letter Required',
                            text: 'Please upload the Final Possession Letter.',
                            confirmButtonColor: '#3085d6'
                        });
                        return false;
                    }
                } else {
                    const lat = latitudeInput.value.trim();
                    const lng = longitudeInput.value.trim();
                    const plotImg = plotImageInput.files.length;

                    if (!lat || !lng) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Location Required',
                            text: 'Please capture GPS location coordinates before submitting.',
                            confirmButtonColor: '#3085d6'
                        });
                        return false;
                    }

                    if (!plotImg) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Plot Photo Required',
                            text: 'Please upload the Plot Photo with Applicant.',
                            confirmButtonColor: '#3085d6'
                        });
                        return false;
                    }

                    const remarks = document.getElementById('remarks').value.trim();
                    if (!remarks) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Remarks Required',
                            text: 'Please enter verification remarks/comments.',
                            confirmButtonColor: '#3085d6'
                        });
                        return false;
                    }
                }

                // Show loading state and let submit proceed
                const submitBtn = verifyForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...';
                    setTimeout(() => {
                        submitBtn.disabled = true;
                    }, 10);
                }
            });
        }

        const btnCitizenAbsent = document.getElementById('btn-citizen-absent');
        if (btnCitizenAbsent && verifyForm) {
            btnCitizenAbsent.addEventListener('click', function() {
                Swal.fire({
                    title: 'Citizen Absent / Reschedule?',
                    text: "Are you sure the citizen was absent? This will reset their scheduled visit slot and return the application to the scheduling pool so you can offer new slot dates.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Reset & Reschedule',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'reschedule';
                        verifyForm.appendChild(actionInput);

                        btnCitizenAbsent.disabled = true;
                        btnCitizenAbsent.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Rescheduling...';

                        verifyForm.submit();
                    }
                });
            });
        }
    });
</script>
@endsection
