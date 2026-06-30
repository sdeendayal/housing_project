@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Application Details',
    'activeNav' => 'pp-application-show',
])

@section('content')
@php
    $statusClass = match($application->status) {
        'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-red-100 text-red-700 border-red-200',
        'returned' => 'bg-blue-100 text-blue-700 border-blue-200',
        default => 'bg-amber-100 text-amber-700 border-amber-200',
    };
    $statusIcon = match($application->status) {
        'approved' => 'check_circle',
        'rejected' => 'cancel',
        'returned' => 'edit_document',
        default => 'hourglass_top',
    };
    $timelineDotClass = fn (string $status) => match(strtolower($status)) {
        'approved', 'verified' => 'bg-emerald-500 ring-4 ring-emerald-100',
        'rejected' => 'bg-red-500 ring-4 ring-red-100',
        'returned' => 'bg-blue-500 ring-4 ring-blue-100',
        'slot selected' => 'bg-indigo-500 ring-4 ring-indigo-100',
        'visit scheduled' => 'bg-amber-500 ring-4 ring-amber-100',
        default => 'bg-slate-500 ring-4 ring-slate-100',
    };
@endphp

<div class="space-y-2">
    {{-- Application summary + status + actions --}}
    <div class="citizen-card overflow-hidden">
        <div class="relative px-4 py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-500 to-violet-500">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_top_right,white,transparent_55%)]"></div>
            <div class="relative flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[9px] uppercase tracking-wider font-bold text-indigo-100 mb-1">Generated Application ID</p>
                    <p class="text-[17px] sm:text-[18px] font-extrabold text-white tracking-wide m-0 break-all">{{ $application->application_number }}</p>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $statusClass }} shrink-0">
                    <span class="material-symbols-outlined text-[14px]">{{ $statusIcon }}</span>
                    {{ $application->status }}
                </span>
            </div>
        </div>

        <div class="p-3 sm:p-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    </span>
                    <div class="min-w-0">
                        <p class="pp-detail-label">Slip ID</p>
                        <p class="text-[12px] font-bold text-slate-800 truncate">{{ $application->slip_id }}</p>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    </span>
                    <div class="min-w-0">
                        <p class="pp-detail-label">Submitted On</p>
                        <p class="text-[12px] font-bold text-slate-800">{{ $application->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            @if($application->statusLogs->isNotEmpty())
            <div class="rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 to-violet-50/40 p-3">
                <div class="flex items-center gap-1.5 mb-2.5">
                    <span class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[15px]">timeline</span>
                    </span>
                    <h3 class="text-[11px] font-extrabold text-slate-800 m-0">Status Timeline</h3>
                </div>
                <div class="space-y-0">
                    @foreach($application->statusLogs as $log)
                    <div class="flex gap-2.5 {{ !$loop->last ? 'pb-3' : '' }}">
                        <div class="flex flex-col items-center shrink-0 pt-0.5">
                            <span class="w-2.5 h-2.5 rounded-full {{ $timelineDotClass($log->new_status) }}"></span>
                            @if(!$loop->last)
                            <span class="w-px flex-1 min-h-[28px] bg-indigo-200 mt-1"></span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 {{ !$loop->last ? 'pb-0.5' : '' }}">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                <p class="text-[11px] font-bold text-slate-800 m-0 capitalize">{{ $log->new_status }}</p>
                                <span class="text-[10px] text-slate-400">·</span>
                                <p class="text-[10px] font-semibold text-indigo-600 m-0">{{ $log->created_at->format('d M Y') }}</p>
                            </div>
                            @if($log->remarks)
                            <p class="text-[10px] text-slate-600 m-0 mt-0.5 leading-relaxed">{{ str_ireplace(' by user', '', $log->remarks) }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($application->status === 'approved' && !$application->citizen_visit_date)
            <div class="rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50/60 p-4 mt-3">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[12px] font-extrabold text-slate-800 m-0 mb-1">Select Meeting Time Slot</h3>
                        <p class="text-[11px] text-slate-600 m-0 leading-relaxed mb-3">Your application is approved. Please select one of the following 3 options to schedule your visit to the office and download your visiting slip.</p>
                        
                        <form method="POST" action="{{ route('pp.user.application.select-slot', $application) }}" id="ppSelectSlotForm">
                            @csrf
                            
                            <div class="space-y-2 mb-3">
                                @if($application->visit_slot_1)
                                <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-amber-50/40 cursor-pointer transition">
                                    <input type="radio" name="selected_slot" value="{{ $application->visit_slot_1->toDateTimeString() }}" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" required>
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-bold text-slate-700 m-0">{{ $application->visit_slot_1->format('d M Y') }}</p>
                                        <p class="text-[10px] text-slate-500 m-0">{{ $application->visit_slot_1->format('h:i a') }} to {{ $application->visit_slot_1->copy()->addHour()->format('h:i a') }}</p>
                                    </div>
                                </label>
                                @endif

                                @if($application->visit_slot_2)
                                <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-amber-50/40 cursor-pointer transition">
                                    <input type="radio" name="selected_slot" value="{{ $application->visit_slot_2->toDateTimeString() }}" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-bold text-slate-700 m-0">{{ $application->visit_slot_2->format('d M Y') }}</p>
                                        <p class="text-[10px] text-slate-500 m-0">{{ $application->visit_slot_2->format('h:i a') }} to {{ $application->visit_slot_2->copy()->addHour()->format('h:i a') }}</p>
                                    </div>
                                </label>
                                @endif

                                @if($application->visit_slot_3)
                                <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-amber-50/40 cursor-pointer transition">
                                    <input type="radio" name="selected_slot" value="{{ $application->visit_slot_3->toDateTimeString() }}" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-bold text-slate-700 m-0">{{ $application->visit_slot_3->format('d M Y') }}</p>
                                        <p class="text-[10px] text-slate-500 m-0">{{ $application->visit_slot_3->format('h:i a') }} to {{ $application->visit_slot_3->copy()->addHour()->format('h:i a') }}</p>
                                    </div>
                                </label>
                                @endif
                            </div>

                            @if($application->visit_instructions)
                            <div class="bg-slate-50 border border-slate-100 rounded-lg p-2 mb-3">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-0.5">Instructions from Officer</p>
                                <p class="text-[10px] text-slate-600 m-0 leading-relaxed">{{ $application->visit_instructions }}</p>
                            </div>
                            @endif

                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-[11px] font-bold no-underline hover:bg-indigo-700 shadow-sm border-0 cursor-pointer">
                                <span class="material-symbols-outlined text-[15px]">check</span> Confirm Appointment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @if($application->citizen_visit_date)
            <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50/60 p-3 mt-3">
                <div class="flex items-start gap-2.5">
                    <span class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">event</span>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[11px] font-extrabold text-slate-800 m-0 mb-1">Meeting Schedule</h3>
                        <p class="text-[13px] font-extrabold text-blue-700 m-0">{{ $application->citizen_visit_date->format('d M Y') }}</p>
                        <p class="text-[12px] font-bold text-blue-600 m-0 mt-0.5">{{ $application->citizen_visit_date->format('h:i a') }} to {{ $application->citizen_visit_date->copy()->addHour()->format('h:i a') }}</p>
                        @if($application->visit_instructions)
                        <p class="text-[10px] text-slate-600 m-0 mt-1 leading-relaxed">{{ $application->visit_instructions }}</p>
                        @else
                        <p class="text-[10px] text-slate-500 m-0 mt-1">Please visit the Municipal Office on the above date with original documents.</p>
                        @endif
                        <div class="flex flex-wrap gap-2 mt-2.5">
                            <a href="{{ route('pp.user.visit-performa.download', $application) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-600 text-white text-[10px] font-bold no-underline hover:bg-blue-700">
                                <span class="material-symbols-outlined text-[14px]">download</span> Download Visiting Slip
                            </a>
                            <a href="{{ route('pp.user.visit-performa.print', $application) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-blue-200 bg-white text-[10px] font-bold text-blue-700 no-underline hover:bg-blue-50">
                                <span class="material-symbols-outlined text-[14px]">print</span> Print Visiting Slip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($application->status === 'returned')
            <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50/60 p-3 mt-3">
                <div class="flex items-start gap-2.5">
                    <span class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">edit_document</span>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[11px] font-extrabold text-slate-800 m-0 mb-1">Action Required — Correct Documents</h3>
                        <p class="text-[10px] text-slate-600 m-0 leading-relaxed">The officer has returned some documents. Please upload the corrected documents and resubmit your application.</p>
                        @if($application->remarks)
                        <p class="text-[10px] font-bold text-blue-800 m-0 mt-1">{{ $application->remarks }}</p>
                        @endif
                        <a href="{{ route('pp.user.application.correct', $application) }}"
                           class="inline-flex items-center gap-1 mt-2.5 px-3 py-1.5 rounded-lg bg-blue-600 text-white text-[10px] font-bold no-underline hover:bg-blue-700">
                            <span class="material-symbols-outlined text-[14px]">upload_file</span> Correct & Resubmit
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-slate-100">
                <a href="{{ route('pp.user.slip.print', $application) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-indigo-600 text-white text-[11px] font-bold no-underline hover:bg-indigo-700 shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">print</span> Print Slip
                </a>
                <a href="{{ route('pp.user.applications') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-700 no-underline hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back
                </a>
            </div>
        </div>
    </div>

    @if($application->possession_certificate || $application->plot_image || $application->latitude || $application->verified_at)
    <div class="citizen-card">
        <div class="px-3 py-2.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-indigo-600">task_alt</span>
                On-Site Verification Details (Site Engineer Actions)
            </h2>
            @if($application->verified_at)
            <span class="text-[9px] font-semibold text-slate-400">Verified on {{ \Carbon\Carbon::parse($application->verified_at)->format('d M Y, h:i A') }}</span>
            @endif
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <!-- Location Details -->
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-indigo-500">pin_drop</span>
                        Captured GPS Location
                    </p>
                    <div class="text-[11px] font-semibold text-slate-700">
                        @if($application->latitude && $application->longitude)
                        <div class="flex flex-wrap gap-x-3 gap-y-1">
                            <div><span class="text-slate-400">Latitude:</span> <strong class="font-mono text-slate-800">{{ $application->latitude }}</strong></div>
                            <div><span class="text-slate-400">Longitude:</span> <strong class="font-mono text-slate-800">{{ $application->longitude }}</strong></div>
                        </div>
                        <a href="https://maps.google.com/?q={{ $application->latitude }},{{ $application->longitude }}" target="_blank" class="inline-flex items-center gap-1 mt-1.5 text-[9px] font-bold text-indigo-600 no-underline hover:text-indigo-800">
                            <span class="material-symbols-outlined text-[12px]">open_in_new</span> View on Google Maps
                        </a>
                        @else
                        <span class="text-slate-400">—</span>
                        @endif
                    </div>
                </div>

                <!-- Verification Info -->
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-indigo-500">shield_person</span>
                        Verified By
                    </p>
                    <div class="text-[11px] font-semibold text-slate-700">
                        @if($application->verified_by)
                            @php
                                $verifier = \App\Models\User::find($application->verified_by);
                            @endphp
                            <strong>{{ $verifier?->name ?? 'Site Engineer' }}</strong>
                            <div class="text-[9px] text-slate-400 mt-0.5">District: {{ $application->district_name }}</div>
                        @else
                            <strong>Site Engineer</strong>
                            <div class="text-[9px] text-slate-400 mt-0.5">District: {{ $application->district_name }}</div>
                        @endif
                    </div>
                </div>

                @if($application->remarks)
                <div class="rounded-lg border border-slate-100 bg-slate-50/80 p-2.5 md:col-span-2">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-indigo-500">comment</span>
                        Verification Remarks / Comments
                    </p>
                    <p class="text-[11px] font-medium text-slate-700 m-0 leading-relaxed">{{ $application->remarks }}</p>
                </div>
                @endif
            </div>

            <!-- Uploaded outcomes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Plot Photo -->
                @if($application->plot_image)
                <div class="flex flex-col rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                    <div class="p-2.5 bg-slate-50 border-b border-slate-100 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-indigo-500">photo_camera</span>
                        <span class="text-[10px] font-bold text-slate-800">On-Site Plot Photo</span>
                    </div>
                    <div class="p-3 flex-1 flex flex-col items-center justify-center bg-slate-100/50">
                        <div class="w-full max-w-[200px] aspect-video rounded-lg overflow-hidden border shadow-sm bg-white mb-2.5">
                            <img src="{{ asset('storage/' . $application->plot_image) }}" alt="On-Site Plot Photo" class="w-full h-full object-cover">
                        </div>
                        <a href="{{ asset('storage/' . $application->plot_image) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-[10px] font-bold text-indigo-700 no-underline hover:bg-indigo-100">
                            <span class="material-symbols-outlined text-[14px]">open_in_new</span> Open Image
                        </a>
                    </div>
                </div>
                @endif

                <!-- Signed Possession Application -->
                @if($application->possession_certificate)
                <div class="flex flex-col rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                    <div class="p-2.5 bg-slate-50 border-b border-slate-100 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-indigo-500">description</span>
                        <span class="text-[10px] font-bold text-slate-800">Signed Application Form</span>
                    </div>
                    <div class="p-3 flex-1 flex flex-col items-center justify-center bg-slate-100/50">
                        <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-[24px]">picture_as_pdf</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-700 m-0 mb-3 text-center">Physical Possession Application (Citizen Signed)</p>
                        <a href="{{ asset('storage/' . $application->possession_certificate) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-[10px] font-bold text-red-700 no-underline hover:bg-red-100">
                            <span class="material-symbols-outlined text-[14px]">download</span> Download PDF
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Personal Details -->
    <div class="citizen-card">
        <div class="px-3 py-2.5 border-b border-slate-100 bg-slate-50 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px] text-indigo-600">person</span>
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Personal Details</h2>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Full Name</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->PrivatePurchaserName ?? $application->applicant_name }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Father's Name</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->PurchaserFatherName ?? $application->father_name ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Mobile Number</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->MobileNo ?? $application->mobile }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">PPP ID</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->PPPId ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Member ID</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->MemberID ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Allotment ID / Application No.</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->purchaser_app_no ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Caste Category</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->purchaser_category ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Marital Status</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->purchaser_marital_status ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Registration Date</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->purchaser_reg_date ? \Carbon\Carbon::parse($purchaser->purchaser_reg_date)->format('d M Y') : '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5 sm:col-span-2 md:col-span-3">
                    <p class="pp-detail-label">Correspondence Address</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->Address ?? $application->address ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Details -->
    <div class="citizen-card">
        <div class="px-3 py-2.5 border-b border-slate-100 bg-slate-50 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px] text-indigo-600">apartment</span>
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Property & Allotment Details</h2>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Asset Name / ID</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->AssetName ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Asset Size</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->AssetSize ?? '—' }} {{ $purchaser?->Unit ?? '' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Sector / Area</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->SectorName ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">District</p>
                    <p class="text-[12px] font-bold text-slate-800">{{ $purchaser?->DistrictName ?? $application->district_name ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Property Cost</p>
                    <p class="text-[12px] font-bold text-slate-800">₹ {{ number_format($purchaser?->FlatCost ?? $application->flat_cost ?? 0, 2) }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Total Amount Received</p>
                    <p class="text-[12px] font-bold text-success">₹ {{ number_format($totalReceived ?? $application->received_amount ?? 0, 2) }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-2.5">
                    <p class="pp-detail-label">Balance Outstanding</p>
                    <p class="text-[12px] font-bold text-danger">₹ {{ number_format($balanceAmount ?? $application->balance_amount ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="citizen-card">
        <div class="px-3 py-2 border-b border-slate-100 bg-slate-50">
            <h2 class="text-[11px] font-extrabold text-slate-800 m-0">Uploaded Documents ({{ $application->documents->count() }})</h2>
        </div>
        <div class="p-3">
            @if($application->documents->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                @foreach($application->documents as $doc)
                @php
                    $docViewUrl = route('pp.user.document.view', [$application, $doc]);
                    $isImage = str_starts_with($doc->mime_type ?? '', 'image/');
                    $isPdf = ($doc->mime_type ?? '') === 'application/pdf'
                        || str_ends_with(strtolower($doc->original_name ?? ''), '.pdf');
                    $docIcon = $doc->iconName();
                @endphp
                <div class="flex flex-col rounded-xl border border-slate-200 bg-white overflow-hidden hover:border-indigo-200 hover:shadow-sm transition">
                    <a href="{{ $docViewUrl }}" target="_blank" rel="noopener"
                       class="group relative block no-underline bg-gradient-to-b from-slate-50 to-slate-100/80">
                        <div class="relative mx-auto w-full max-w-[180px] pt-3 px-3">
                            <div class="relative aspect-[3/4] overflow-hidden rounded-t-md rounded-b-sm bg-white shadow-md ring-1 ring-slate-200/80">
                                @if($isImage)
                                <img src="{{ $docViewUrl }}" alt="{{ $doc->typeLabel() }}"
                                     class="h-full w-full object-cover object-top"
                                     loading="lazy">
                                @elseif($isPdf)
                                <iframe src="{{ $docViewUrl }}#toolbar=0&navpanes=0&view=FitH"
                                        title="{{ $doc->typeLabel() }} preview"
                                        class="h-full w-full border-0 pointer-events-none bg-white"
                                        loading="lazy"></iframe>
                                @else
                                <div class="flex h-full items-center justify-center bg-slate-50">
                                    <span class="material-symbols-outlined text-[32px] text-slate-300">description</span>
                                </div>
                                @endif
                                <div class="absolute inset-0 bg-indigo-900/0 transition group-hover:bg-indigo-900/10"></div>
                            </div>
                            @if($isPdf)
                            <span class="absolute top-5 right-5 inline-flex items-center gap-0.5 rounded-md bg-red-500 px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wide text-white shadow-sm">
                                PDF
                            </span>
                            @endif
                        </div>
                        <div class="px-2.5 pt-2.5 pb-2 text-center">
                            <div class="flex items-start justify-center gap-1 mb-1">
                                <span class="material-symbols-outlined text-[15px] text-indigo-500 shrink-0 mt-px">{{ $docIcon }}</span>
                                <p class="text-[10px] font-bold text-slate-800 m-0 leading-snug line-clamp-2">{{ $doc->typeLabel() }}</p>
                            </div>
                            <p class="text-[9px] text-slate-400 m-0 truncate">{{ $doc->original_name }}</p>
                        </div>
                    </a>
                    <div class="mt-auto border-t border-slate-100 p-2 space-y-1.5">
                        @if($doc->review_status === 'returned')
                        <div class="flex items-center justify-center gap-1 rounded-lg bg-red-50 border border-red-200 py-1 text-[9px] font-bold text-red-700">
                            <span class="material-symbols-outlined text-[13px]">error</span>
                            Returned — re-upload required
                        </div>
                        @if($doc->officer_remarks)
                        <p class="text-[9px] text-red-600 m-0 text-center">{{ $doc->officer_remarks }}</p>
                        @endif
                        @elseif($doc->is_verified)
                        <div class="flex items-center justify-center gap-1 rounded-lg bg-emerald-50 border border-emerald-200 py-1 text-[9px] font-bold text-emerald-700">
                            <span class="material-symbols-outlined text-[13px]">verified</span>
                            Verified{{ $doc->verified_at ? ' · '.$doc->verified_at->format('d M Y, h:i A') : '' }}
                        </div>
                        @endif
                        <a href="{{ $docViewUrl }}" target="_blank" rel="noopener"
                           class="flex w-full items-center justify-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 py-1.5 text-[10px] font-bold text-indigo-700 no-underline hover:bg-indigo-100">
                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                            View Document
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-4 text-center text-[11px] text-slate-500">No documents uploaded.</div>
            @endif
        </div>
    </div>
</div>
@endsection
