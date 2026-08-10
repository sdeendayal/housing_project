@extends('physical-possession.layouts.officer')

@section('page-title', 'Draw Maps & Documents')

@section('content')
<div class="container-fluid px-2 py-2">
    <div class="row g-2">
        <div class="col-12">
            <!-- Compact Title Bar -->
            <div class="d-flex justify-content-between align-items-center py-2 px-3 bg-white shadow-sm border-start border-primary border-4" style="border-radius: 10px; margin-bottom: 8px;">
                <div class="min-w-0">
                    <h5 class="mb-0 font-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                        <i class="bi bi-map-fill text-primary"></i> 
                        <span class="text-truncate">Draw Maps & Layouts — {{ $officer->district_name }} District</span>
                    </h5>
                    <span class="text-muted text-truncate d-none d-sm-inline" style="font-size: 0.68rem;">Access layout maps and published lists for your assigned area</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2 py-1 rounded" style="font-size: 0.65rem; font-weight: 700;">
                        ACTIVE DISTRICT
                    </span>
                </div>
            </div>

            <!-- Documents Table Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                <div class="card-body p-2">
                    @if($documents->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" id="drawDocumentsTable" style="border-collapse: collapse; font-size: 0.78rem;">
                                <thead class="table-light text-secondary text-uppercase text-xs">
                                    <tr style="border-bottom: 1.5px solid #dee2e6;">
                                        <th class="text-center py-2" style="width: 45px; font-weight: 700; font-size: 0.68rem;">S.No</th>
                                        <th class="py-2" style="width: 60px; font-weight: 700; font-size: 0.68rem;">Code</th>
                                        <th class="py-2" style="font-weight: 700; font-size: 0.68rem;">Document Title</th>
                                        <th class="py-2" style="font-weight: 700; font-size: 0.68rem;">Sector</th>
                                        <th class="py-2" style="font-weight: 700; font-size: 0.68rem;">Location</th>
                                        <th class="text-center py-2" style="width: 90px; font-weight: 700; font-size: 0.68rem;">Plots</th>
                                        <th class="py-2" style="font-weight: 700; font-size: 0.68rem;">Original PDF Filename</th>
                                        <th class="text-center py-2" style="width: 110px; font-weight: 700; font-size: 0.68rem;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $index => $doc)
                                        <tr style="border-bottom: 1px solid #f1f5f9; height: 38px;">
                                            <td class="text-center py-1.5 text-muted font-semibold">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="py-1.5">
                                                <span class="badge bg-primary bg-opacity-10 text-primary font-bold px-2 py-1" style="font-size: 0.68rem; border-radius: 4px;">
                                                    {{ $doc->document_code }}
                                                </span>
                                            </td>
                                            <td class="py-1.5">
                                                <div class="font-bold text-dark" style="font-size: 0.78rem;">{{ $doc->title }}</div>
                                                <span class="text-muted d-block text-xs" style="margin-top: -1px;">{{ $doc->scheme }}</span>
                                            </td>
                                            <td class="py-1.5 font-semibold text-secondary">
                                                {{ $doc->sector_label }}
                                            </td>
                                            <td class="py-1.5 text-secondary">
                                                {{ $doc->location_label }}
                                            </td>
                                            <td class="text-center py-1.5">
                                                @if($doc->total_plots)
                                                    <span class="badge bg-dark bg-opacity-90 px-2 py-1 rounded-pill font-bold" style="font-size: 0.68rem;">
                                                        {{ $doc->total_plots }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-1.5 text-muted text-truncate text-xs" style="max-width: 180px;" title="{{ $doc->original_file_name }}">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                                {{ $doc->original_file_name }}
                                            </td>
                                            <td class="text-center py-1.5">
                                                <div class="d-inline-flex gap-1 justify-content-center align-items-center">
                                                    @if($doc->file_path)
                                                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1 font-semibold px-2 py-0.5 rounded" style="font-size: 0.68rem; line-height: 1.5;">
                                                            <i class="bi bi-eye"></i> View
                                                        </a>
                                                        <a href="{{ asset($doc->file_path) }}" download class="btn btn-xs btn-primary d-inline-flex align-items-center justify-content-center rounded" style="width: 22px; height: 22px;" title="Download PDF">
                                                            <i class="bi bi-download" style="font-size: 0.65rem;"></i>
                                                        </a>
                                                    @else
                                                        <button class="btn btn-xs btn-secondary disabled px-2 py-0.5 rounded" style="font-size: 0.68rem;" disabled>
                                                            No File
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- Compact Empty State -->
                        <div class="text-center py-4">
                            <div class="mb-2 text-muted">
                                <i class="bi bi-folder-x display-6 text-opacity-50"></i>
                            </div>
                            <h6 class="font-bold text-secondary mb-1">No Maps Published</h6>
                            <p class="text-muted mx-auto mb-0" style="font-size: 0.72rem; max-width: 320px;">
                                There are currently no property draw documents or layouts published for the <strong>{{ $officer->district_name }}</strong> district.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .font-semibold { font-weight: 600; }
    .font-bold { font-weight: 700; }
    .text-xs { font-size: 0.68rem !important; }
    
    /* Hover effects for table rows */
    #drawDocumentsTable tbody tr:hover {
        background-color: rgba(42, 82, 152, 0.03) !important;
    }
    
    /* Bootstrap btn-xs implementation */
    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.68rem;
        border-radius: 0.2rem;
    }
    
    /* Datatables Compact Styling Overrides */
    #drawDocumentsTable_wrapper .dataTables_filter {
        margin-bottom: 0.5rem;
        float: right;
    }
    #drawDocumentsTable_wrapper .dataTables_filter input {
        border-radius: 6px;
        padding: 0.2rem 0.5rem;
        border: 1px solid #ced4da;
        outline: none;
        font-size: 0.72rem;
        width: 180px;
    }
    #drawDocumentsTable_wrapper .dataTables_filter input:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.15rem rgba(42, 82, 152, 0.15);
    }
    #drawDocumentsTable_wrapper .dataTables_info,
    #drawDocumentsTable_wrapper .dataTables_paginate {
        font-size: 0.7rem;
        margin-top: 0.5rem;
    }
    .page-item.active .page-link {
        background-color: #2a5298 !important;
        border-color: #2a5298 !important;
    }
    .page-link {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.7rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#drawDocumentsTable').length) {
            $('#drawDocumentsTable').DataTable({
                pageLength: 20,
                dom: '<"d-flex justify-content-between align-items-center mb-1"f>rt<"d-flex justify-content-between align-items-center"ip>',
                language: {
                    search: "",
                    searchPlaceholder: "Search maps, layouts..."
                },
                columnDefs: [
                    { orderable: false, targets: [0, 6, 7] }
                ]
            });
        }
    });
</script>
@endpush
