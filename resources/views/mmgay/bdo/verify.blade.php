@extends('layouts.mmgayBdoAuth')
@section('title', 'Verify Physical Possession')
@section('page_header', 'Verify Possession')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex-1">
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Sidebar Beneficiary Details - High Density -->
        <div class="md:col-span-1 bg-white rounded-xl shadow-sm border border-slate-100 p-4 self-start">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-2.5 pb-2 border-b border-slate-100 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-blue-600 text-lg">person</span>
                Beneficiary details
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

        <!-- Verification/Action Card - High Density -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-2.5 pb-2 border-b border-slate-100 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-blue-600 text-lg">fact_check</span>
                Possession Status & Verification
            </h3>

            @if ($errors->any())
                <div class="bg-rose-50 text-rose-800 border border-rose-100 px-3.5 py-2 rounded-xl mb-4 text-xs font-semibold">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Current Visit slot chosen -->
            <div class="bg-[#f8fafc] border border-slate-100 rounded-xl p-3.5 mb-4 text-xs">
                <h4 class="text-[10px] uppercase font-bold text-slate-400 mb-1.5 block">Meeting Slot Option</h4>
                <div class="flex items-center gap-2 font-bold text-slate-800">
                    <span class="material-symbols-outlined text-indigo-600 text-lg">event_available</span>
                    @if(in_array($application->physical_possession_status, ['Slot Selected', 'Site Verified', 'Verified']))
                        <span class="text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 font-extrabold">
                            Confirmed by Applicant: {{ Carbon\Carbon::parse($application->citizen_visit_date)->format('d M Y, h:i A') }}
                        </span>
                    @else
                        <span class="text-slate-600">
                            Offered slot Options: {{ $application->meeting_slot }}
                        </span>
                    @endif
                </div>
                @if($application->visit_instructions)
                    <div class="mt-2 text-slate-500 bg-white p-2 rounded-lg border border-slate-100 leading-normal">
                        <strong>BDPO Instructions:</strong> {{ $application->visit_instructions }}
                    </div>
                @endif
            </div>

            <!-- Stage 1 Capture Result / Display if exists -->
            @if($application->latitude && $application->longitude)
                <div class="bg-[#f0f9ff] border border-blue-100 rounded-xl p-3.5 mb-4 text-xs">
                    <h4 class="text-[10px] uppercase font-bold text-blue-600 mb-1.5 block">Captured Field Coordinates & Photo</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-white p-2.5 rounded-lg border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[9px] block">GPS Latitude</span>
                            <span class="font-mono text-slate-800 block mt-0.5 font-bold">{{ $application->latitude }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[9px] block">GPS Longitude</span>
                            <span class="font-mono text-slate-800 block mt-0.5 font-bold">{{ $application->longitude }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[9px] block">Captured Datetime</span>
                            <span class="text-slate-700 block mt-0.5 font-semibold">{{ Carbon\Carbon::parse($application->image_capture_datetime)->format('d M Y - h:i A') }}</span>
                        </div>
                    </div>
                    @if($application->plot_image)
                        <div class="mt-2.5 border-t border-slate-200/50 pt-2.5">
                            <span class="text-slate-400 font-bold uppercase text-[9px] block mb-1">Plot Site Photo with Applicant</span>
                            <a href="{{ asset('storage/' . $application->plot_image) }}" target="_blank" class="inline-block relative rounded-lg overflow-hidden border border-slate-200 max-w-[180px] hover:opacity-90 transition">
                                <img src="{{ asset('storage/' . $application->plot_image) }}" class="w-full object-cover max-h-[100px]" alt="Plot Site Photo">
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Stage 2 Uploaded Documents Display -->
            @if($application->site_engineer_file || $application->possession_certificate)
                <div class="bg-emerald-50/50 border border-emerald-100 rounded-xl p-3.5 mb-4 text-xs">
                    <h4 class="text-[10px] uppercase font-bold text-emerald-800 mb-1.5 block">Uploaded Verification Documents</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @if($application->site_engineer_file)
                            <a href="{{ asset('storage/' . $application->site_engineer_file) }}" target="_blank" class="flex items-center gap-2 bg-white hover:bg-slate-50 transition p-2.5 rounded-lg border border-slate-200/60 font-bold text-slate-700">
                                <span class="material-symbols-outlined text-red-500 text-xl">picture_as_pdf</span>
                                <div>
                                    <span class="block text-[10px] text-slate-800">Signed Possession Report</span>
                                    <span class="block text-[8px] text-slate-400 font-semibold uppercase mt-0.5">Click to View PDF</span>
                                </div>
                            </a>
                        @endif
                        @if($application->possession_certificate)
                            <a href="{{ asset('storage/' . $application->possession_certificate) }}" target="_blank" class="flex items-center gap-2 bg-white hover:bg-slate-50 transition p-2.5 rounded-lg border border-slate-200/60 font-bold text-slate-700">
                                <span class="material-symbols-outlined text-red-500 text-xl">picture_as_pdf</span>
                                <div>
                                    <span class="block text-[10px] text-slate-800">Final Possession Letter</span>
                                    <span class="block text-[8px] text-slate-400 font-semibold uppercase mt-0.5">Click to View PDF</span>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Action Select Options or Awaiting Box -->
            @if($application->physical_possession_status === 'Visit Scheduled')
                <!-- Card 3: Awaiting Citizen Action -->
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 text-xs text-amber-800 font-semibold flex items-start gap-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-amber-600 text-lg">info</span>
                    <div>
                        <p class="font-bold">Awaiting Applicant Action</p>
                        <p class="text-[11px] text-amber-700/90 mt-0.5 leading-normal">
                            The offered schedule visit slots have been sent to the applicant. BDPO can verify or reschedule this application once the applicant selects one of the slots.
                        </p>
                    </div>
                </div>
            @elseif($application->physical_possession_status === 'Slot Selected')
                <!-- Card 4: Field Visit / Coordinates Capture (Stage 1) -->
                <form id="verify_form" action="{{ route('mmgay.bdo.verify-save', $application->secure_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                            <span class="text-xs font-bold text-slate-700">GPS Location Capture & Plot Photo</span>
                            <button type="button" onclick="getLocation()" class="text-[10px] bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-3 py-1.5 rounded-lg flex items-center gap-1 transition shadow-sm font-bold">
                                <span class="material-symbols-outlined text-sm">my_location</span> Capture Current GPS
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Latitude <span class="text-rose-500">*</span></label>
                                <input type="text" name="latitude" id="latitude" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 font-mono font-bold" readonly required placeholder="Awaiting GPS capture...">
                            </div>
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Longitude <span class="text-rose-500">*</span></label>
                                <input type="text" name="longitude" id="longitude" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 font-mono font-bold" readonly required placeholder="Awaiting GPS capture...">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Upload Plot Photo (with Applicant) <span class="text-rose-500">*</span></label>
                            <div class="border-2 border-dashed border-slate-200 rounded-lg p-4 bg-white flex flex-col items-center justify-center cursor-pointer hover:bg-slate-50/50 transition relative">
                                <input type="file" name="plot_image" id="plot_image" class="absolute inset-0 opacity-0 cursor-pointer" required accept=".png,.jpg,.jpeg" onchange="updatePlotFileName(this)">
                                <span class="material-symbols-outlined text-slate-400 text-2xl mb-1">photo_camera</span>
                                <span id="plot_file_picker_text" class="text-xs font-bold text-slate-600">Click to upload photo (JPG or PNG)</span>
                                <span class="text-[9px] text-slate-400 uppercase mt-0.5">Maximum size: 500 KB</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Verification Remarks / Comments <span class="text-rose-500">*</span></label>
                            <textarea name="remarks" id="remarks" rows="2" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 text-xs text-slate-700 leading-normal" required placeholder="Describe site condition, boundary pillars, applicant presence, etc..."></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('mmgay.bdo.possession-applications') }}" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-bold text-slate-500 hover:bg-slate-50">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-bold flex items-center gap-1 shadow">
                            <span class="material-symbols-outlined text-[16px]">save</span> Save Field Coordinates
                        </button>
                    </div>
                </form>
            @elseif($application->physical_possession_status === 'Site Verified')
                <!-- Card 5: E-Possession Report (Stage 2) -->
                <form action="{{ route('mmgay.bdo.verify-save', $application->secure_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 font-semibold">
                    @csrf
                    
                    <!-- Verify Upload Form Area -->
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 space-y-4">
                        <!-- Application Preview & Download -->
                        <div class="bg-white p-3 rounded-lg border border-slate-150 shadow-sm space-y-1.5">
                            <span class="text-xs font-bold text-slate-700 font-extrabold block">Application Preview & Print</span>
                            <p class="text-[10px] text-slate-400 leading-normal">
                                Click the button below to view and download the prefilled MMGAY E-Possession Certificate. BDPO must print this document, sign it, and upload the signed copy.
                            </p>
                            <a href="{{ route('mmgay.bdo.download-certificate', $application->secure_id) }}?inline=1" target="_blank" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-3.5 py-1.5 rounded-lg font-bold transition shadow-sm font-bold">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span> Download & View Prefilled Certificate PDF
                            </a>
                        </div>

                        <!-- Upload 1: Signed prefilled report -->
                        <div class="space-y-2 border-b border-slate-200/60 pb-3">
                            <div>
                                <span class="text-xs font-bold text-slate-700 font-extrabold block">1. Upload App Preview (Sign BDPO Also Required)</span>
                            </div>

                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Upload Prefilled Document signed by BDPO (PDF only) <span class="text-rose-500">*</span></label>
                                <div class="border-2 border-dashed border-slate-200 rounded-lg p-3.5 bg-white flex flex-col items-center justify-center cursor-pointer hover:bg-slate-50/50 transition relative">
                                    <input type="file" name="site_engineer_file" id="site_engineer_file" class="absolute inset-0 opacity-0 cursor-pointer" required accept=".pdf" onchange="updateFileName(this)">
                                    <span class="material-symbols-outlined text-slate-400 text-2xl mb-0.5">cloud_upload</span>
                                    <span id="file_picker_text" class="text-xs font-bold text-slate-600">Click to upload signed certificate (PDF only)</span>
                                    <span class="text-[9px] text-slate-400 uppercase mt-0.5 font-bold">Maximum size: 500 KB</span>
                                </div>
                            </div>
                        </div>

                        <!-- Upload 2: Final Possession Letter -->
                        <div class="space-y-2">
                            <div>
                                <span class="text-xs font-bold text-slate-700 font-extrabold block">2. Upload Final Possession Letter</span>
                            </div>

                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Upload Final Possession Letter (PDF only) <span class="text-rose-500">*</span></label>
                                <div class="border-2 border-dashed border-slate-200 rounded-lg p-3.5 bg-white flex flex-col items-center justify-center cursor-pointer hover:bg-slate-50/50 transition relative">
                                    <input type="file" name="possession_certificate" id="possession_certificate" class="absolute inset-0 opacity-0 cursor-pointer" required accept=".pdf" onchange="updateOfficialFileName(this)">
                                    <span class="material-symbols-outlined text-slate-400 text-2xl mb-0.5">cloud_upload</span>
                                    <span id="official_file_picker_text" class="text-xs font-bold text-slate-600">Click to upload Final Possession Letter (PDF only)</span>
                                    <span class="text-[9px] text-slate-400 uppercase mt-0.5 font-bold">Maximum size: 500 KB</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('mmgay.bdo.possession-applications') }}" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-bold text-slate-500 hover:bg-slate-50">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-bold flex items-center gap-1 shadow">
                            <span class="material-symbols-outlined text-[16px]">done_all</span> Submit Verification
                        </button>
                    </div>
                </form>
            @elseif($application->physical_possession_status === 'Verified')
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-xs text-emerald-800 font-semibold flex items-start gap-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                    <div>
                        <p class="font-bold">Verification Completed</p>
                        <p class="text-[11px] text-emerald-700/90 mt-0.5 leading-normal">
                            This physical possession application has been successfully verified and completed. No further action is required.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('mmgay.bdo.possession-applications') }}" class="inline-flex items-center gap-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded px-2.5 py-1 text-[10px] font-bold">
                                <span class="material-symbols-outlined text-[12px]">arrow_back</span> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            @elseif($application->physical_possession_status === 'Rejected')
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 text-xs text-rose-800 font-semibold flex items-start gap-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-rose-600 text-lg">cancel</span>
                    <div>
                        <p class="font-bold">Application Rejected</p>
                        <p class="text-[11px] text-rose-700/90 mt-0.5 leading-normal">
                            This physical possession application was rejected. Please review the timeline logs for remarks.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('mmgay.bdo.possession-applications') }}" class="inline-flex items-center gap-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded px-2.5 py-1 text-[10px] font-bold">
                                <span class="material-symbols-outlined text-[12px]">arrow_back</span> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timeline Progress Logs Section -->
            <div class="mt-4 border-t border-slate-100 pt-3">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-lg">timeline</span>
                    Application Progress Timeline
                </h3>
                <div class="space-y-3.5 pl-1.5">
                    @forelse($logs as $log)
                        <div class="relative pl-5 border-l-2 border-slate-200 last:border-l-0 pb-1 text-xs">
                            <span class="absolute -left-[5.5px] top-1.5 w-2.5 h-2.5 rounded-full bg-blue-500 border border-white"></span>
                            <div class="flex items-center justify-between font-bold text-slate-700 text-[10px]">
                                <span class="uppercase tracking-wider text-blue-600">
                                    {{ $log->new_status }}
                                </span>
                                <span class="text-slate-400 font-normal">
                                    {{ Carbon\Carbon::parse($log->created_at)->format('d M Y - h:i A') }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-normal">{{ $log->remarks }}</p>
                            <p class="text-[9px] text-slate-400 uppercase mt-0.5 font-bold tracking-wider">
                                Action By: {{ $log->changed_by_type === 'officer' ? 'BDPO Officer' : 'Applicant' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-slate-400 font-semibold text-[11px] py-1">No activity log found.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    function updatePlotFileName(input) {
        const textSpan = document.getElementById('plot_file_picker_text');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 500 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'The plot site photo must not exceed 500 KB. (Selected: ' + (file.size / 1024).toFixed(1) + ' KB)'
                });
                input.value = '';
                textSpan.textContent = 'Click to upload photo (JPG or PNG)';
                textSpan.style.color = '#475569';
                return;
            }
            textSpan.textContent = 'Selected: ' + file.name;
            textSpan.style.color = '#2563eb';
        } else {
            textSpan.textContent = 'Click to upload photo (JPG or PNG)';
            textSpan.style.color = '#475569';
        }
    }

    function updateFileName(input) {
        const textSpan = document.getElementById('file_picker_text');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a valid PDF document.'
                });
                input.value = '';
                textSpan.textContent = 'Click to upload report (PDF only)';
                textSpan.style.color = '#475569';
                return;
            }
            if (file.size > 500 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'The BDPO verification document must not exceed 500 KB. (Selected: ' + (file.size / 1024).toFixed(1) + ' KB)'
                });
                input.value = '';
                textSpan.textContent = 'Click to upload report (PDF only)';
                textSpan.style.color = '#475569';
                return;
            }
            textSpan.textContent = 'Selected: ' + file.name;
            textSpan.style.color = '#2563eb';
        } else {
            textSpan.textContent = 'Click to upload report (PDF only)';
            textSpan.style.color = '#475569';
        }
    }

    function updateOfficialFileName(input) {
        const textSpan = document.getElementById('official_file_picker_text');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a valid PDF document.'
                });
                input.value = '';
                textSpan.textContent = 'Click to upload Final Possession Letter (PDF only)';
                textSpan.style.color = '#475569';
                return;
            }
            if (file.size > 500 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'The Final Possession Letter must not exceed 500 KB. (Selected: ' + (file.size / 1024).toFixed(1) + ' KB)'
                });
                input.value = '';
                textSpan.textContent = 'Click to upload Final Possession Letter (PDF only)';
                textSpan.style.color = '#475569';
                return;
            }
            textSpan.textContent = 'Selected: ' + file.name;
            textSpan.style.color = '#2563eb';
        } else {
            textSpan.textContent = 'Click to upload Final Possession Letter (PDF only)';
            textSpan.style.color = '#475569';
        }
    }

    function getLocation() {
        if (navigator.geolocation) {
            Swal.fire({
                title: 'Fetching GPS coordinates...',
                text: 'Please allow location permission in your browser.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    Swal.fire({
                        icon: 'success',
                        title: 'Location Captured',
                        text: 'Latitude: ' + position.coords.latitude + ', Longitude: ' + position.coords.longitude,
                        confirmButtonColor: '#0058bc',
                        confirmButtonText: 'OK'
                    });
                },
                function(error) {
                    let errMsg = 'Unable to retrieve location coordinates.';
                    if (error.code === error.PERMISSION_DENIED) {
                        errMsg = 'Location access permission denied by browser. Please enable location permissions.';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'GPS Error',
                        text: errMsg
                    });
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Not Supported',
                text: 'Geolocation is not supported by your browser.'
            });
        }
    }

    // Form Submission Loader
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                Swal.fire({
                    title: 'Submitting Verification...',
                    text: 'Please wait, saving details.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        }
    });
</script>
@endsection
