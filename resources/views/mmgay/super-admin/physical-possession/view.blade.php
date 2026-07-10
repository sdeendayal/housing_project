@extends('layouts.mmgayAdmin')

@section('title', 'Physical Possession Application - Super Admin')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">
        <div class="p-6 bg-slate-50 min-h-screen">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Physical Possession Status</h1>
                    <p class="text-sm text-slate-500">View and manage physical possession handover details</p>
                </div>
                <a href="{{ url()->previous() }}"
                    class="flex items-center gap-2 px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 rounded-xl text-sm transition-all duration-200 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Back
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1 bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                        <span
                            class="material-symbols-outlined text-indigo-600 bg-indigo-50 p-2 rounded-xl text-[22px]">person</span>
                        <h2 class="text-md font-bold text-slate-800 uppercase tracking-wide">Beneficiary Details</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Name</label>
                            <span
                                class="text-sm font-semibold text-slate-800 block mt-0.5">{{ $application->OwnerName ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Father /
                                Husband</label>
                            <span
                                class="text-sm font-semibold text-slate-700 block mt-0.5">{{ $application->FatherHusbandName ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Mobile
                                Number</label>
                            <span
                                class="text-sm font-semibold text-slate-700 block mt-0.5">{{ $application->MobileNo ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Registration
                                No</label>
                            <span
                                class="text-sm font-bold text-slate-800 block mt-0.5 tracking-wide">{{ $application->RegistrationNo ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Flat No &
                                Address</label>
                            <span class="text-sm font-semibold text-slate-700 block mt-0.5 leading-relaxed">
                                {{ $application->Address ?? 'N/A' }}
                            </span>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Block &
                                Village</label>
                            <span class="text-sm font-semibold text-slate-700 block mt-0.5">
                                {{ $application->BlockName ?? 'N/A' }}, {{ $application->VillageName ?? 'N/A' }}
                            </span>
                        </div>

                        <div>
                            <label
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">District</label>
                            <span
                                class="text-sm font-bold text-slate-800 block mt-0.5">{{ $application->DistrictName ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Family ID
                                (PPP ID)</label>
                            <span
                                class="text-sm font-bold text-slate-800 block mt-0.5">{{ $application->PPPId ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Caste /
                                Category</label>
                            <span
                                class="text-sm font-semibold text-slate-700 block mt-0.5">{{ $application->Caste ?? 'N/A' }}</span>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Payment
                                Status</label>
                            <div class="mt-1">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Paid & Approved
                                </span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Office / DC
                                Remarks</label>
                            <span
                                class="text-sm italic text-slate-600 block mt-0.5 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                {{ $application->Remarks ?? 'OK' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">

                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                            <span class="material-symbols-outlined text-indigo-600 bg-indigo-50 p-2 rounded-xl">
                                assignment_turned_in
                            </span>

                            <h2 class="text-md font-bold uppercase">
                                Possession Status & Verification
                            </h2>

                        </div>


                        {{-- VISIT SCHEDULED --}}

                        @if ($application->physical_possession_status == 'Visit Scheduled')

                            <div class="bg-white border border-orange-200 rounded-2xl shadow-sm overflow-hidden">

                                <!-- Header -->
                                <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-5 py-3">
                                    <div class="flex items-center gap-2 text-white">
                                        <span class="material-symbols-outlined">
                                            event_available
                                        </span>

                                        <h5 class="font-semibold">
                                            Meeting Slot Options
                                        </h5>
                                    </div>
                                </div>

                                <!-- Body -->
                                <div class="p-5">

                                    <div class="grid md:grid-cols-3 gap-4">

                                        <!-- Slot 1 -->
                                        <div
                                            class="rounded-xl border border-slate-200 bg-slate-50 p-4 hover:border-orange-300 hover:bg-orange-50 transition">

                                            <div class="flex items-center gap-2 mb-2">

                                                <span
                                                    class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">

                                                    <span class="material-symbols-outlined text-orange-600 text-[18px]">

                                                        looks_one

                                                    </span>

                                                </span>

                                                <span class="font-semibold text-slate-700">
                                                    Slot 1
                                                </span>

                                            </div>

                                            <p class="text-sm text-slate-600">
                                                {{ $application->visit_slot_1
                                                    ? \Carbon\Carbon::parse($application->visit_slot_1)->format('d M Y, h:i A')
                                                    : 'Not Available' }}
                                            </p>

                                        </div>

                                        <!-- Slot 2 -->
                                        <div
                                            class="rounded-xl border border-slate-200 bg-slate-50 p-4 hover:border-blue-300 hover:bg-blue-50 transition">

                                            <div class="flex items-center gap-2 mb-2">

                                                <span
                                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">

                                                    <span class="material-symbols-outlined text-blue-600 text-[18px]">

                                                        looks_two

                                                    </span>

                                                </span>

                                                <span class="font-semibold text-slate-700">
                                                    Slot 2
                                                </span>

                                            </div>

                                            <p class="text-sm text-slate-600">
                                                {{ $application->visit_slot_2
                                                    ? \Carbon\Carbon::parse($application->visit_slot_2)->format('d M Y, h:i A')
                                                    : 'Not Available' }}
                                            </p>

                                        </div>

                                        <!-- Slot 3 -->
                                        <div
                                            class="rounded-xl border border-slate-200 bg-slate-50 p-4 hover:border-green-300 hover:bg-green-50 transition">

                                            <div class="flex items-center gap-2 mb-2">

                                                <span
                                                    class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">

                                                    <span class="material-symbols-outlined text-green-600 text-[18px]">

                                                        looks_3

                                                    </span>

                                                </span>

                                                <span class="font-semibold text-slate-700">
                                                    Slot 3
                                                </span>

                                            </div>

                                            <p class="text-sm text-slate-600">
                                                {{ $application->visit_slot_3
                                                    ? \Carbon\Carbon::parse($application->visit_slot_3)->format('d M Y, h:i A')
                                                    : 'Not Available' }}
                                            </p>

                                        </div>

                                    </div>

                                    @if ($application->visit_instructions)
                                        <div class="mt-5 rounded-xl border-l-4 border-orange-500 bg-orange-50 p-4">

                                            <div class="flex items-start gap-3">

                                                <span class="material-symbols-outlined text-orange-600">

                                                    info

                                                </span>

                                                <div>

                                                    <h6 class="font-semibold text-orange-700">
                                                        BDPO Instructions
                                                    </h6>

                                                    <p class="text-sm text-slate-600 mt-1">
                                                        {{ $application->visit_instructions }}
                                                    </p>

                                                </div>



                                            </div>

                                        </div>
                                        <br>
                                        <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-4">

                                            <div class="flex items-start gap-3">

                                                <span class="material-symbols-outlined text-blue-600">
                                                    admin_panel_settings
                                                </span>

                                                <div>

                                                    <h6 class="font-semibold text-blue-700">
                                                        Super Admin Monitoring
                                                    </h6>

                                                    <p class="text-sm text-slate-600 mt-1 leading-6">
                                                        The meeting slots have been assigned to the applicant.
                                                        The application is currently awaiting the applicant's confirmation.
                                                        Once a slot is selected, the status will automatically change to
                                                        <strong>Slot Selected</strong>, allowing the field verification
                                                        process
                                                        to continue. Super Admin can monitor the progress of this
                                                        application
                                                        at every stage.
                                                    </p>

                                                </div>

                                            </div>

                                        </div>
                                    @endif

                                </div>

                            </div>

                        @endif




                        {{-- SLOT SELECTED --}}

                        @if ($application->physical_possession_status == 'Slot Selected')

                            <div class="bg-white border border-blue-200 rounded-2xl shadow-sm overflow-hidden mt-5">

                                <!-- Header -->
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-5 py-3">
                                    <div class="flex items-center gap-2 text-white">
                                        <span class="material-symbols-outlined">
                                            event_available
                                        </span>

                                        <h5 class="font-semibold">
                                            Meeting Slot Confirmed
                                        </h5>
                                    </div>
                                </div>

                                <div class="p-5">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                        <!-- Selected Date -->
                                        <div class="bg-slate-50 border rounded-xl p-4">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">

                                                    <span class="material-symbols-outlined text-blue-600">
                                                        calendar_month
                                                    </span>

                                                </div>

                                                <div>

                                                    <p class="text-xs text-slate-500 uppercase">
                                                        Selected Visit Date
                                                    </p>

                                                    <h6 class="font-bold text-slate-800 mt-1">
                                                        {{ \Carbon\Carbon::parse($application->citizen_visit_date)->format('d M Y, h:i A') }}
                                                    </h6>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- Current Status -->
                                        <div class="bg-slate-50 border rounded-xl p-4">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">

                                                    <span class="material-symbols-outlined text-green-600">
                                                        verified
                                                    </span>

                                                </div>

                                                <div>

                                                    <p class="text-xs text-slate-500 uppercase">
                                                        Current Status
                                                    </p>

                                                    <span
                                                        class="inline-flex items-center mt-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">

                                                        Slot Selected

                                                    </span>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <!-- Instructions -->

                                    @if (!empty($application->visit_instructions))
                                        <div class="mt-6 border-l-4 border-blue-500 bg-blue-50 rounded-xl p-4">

                                            <div class="flex items-start gap-3">

                                                <span class="material-symbols-outlined text-blue-600">

                                                    info

                                                </span>

                                                <div>

                                                    <h6 class="font-semibold text-blue-700">
                                                        BDPO Instructions
                                                    </h6>

                                                    <p class="text-sm text-slate-600 mt-2 leading-6">
                                                        {{ $application->visit_instructions }}
                                                    </p>

                                                </div>

                                            </div>

                                        </div>
                                    @endif

                                </div>

                            </div>

                        @endif




                        {{-- SITE VERIFIED --}}

                        @if (
                            $application->physical_possession_status == 'Site Verified' ||
                                $application->physical_possession_status == 'Verified')

                            <div class="bg-white border border-sky-200 rounded-2xl shadow-sm overflow-hidden mt-5">

                                <!-- Header -->
                                <div class="bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-3">
                                    <div class="flex items-center gap-2 text-white">
                                        <span class="material-symbols-outlined">
                                            my_location
                                        </span>

                                        <h5 class="font-semibold">
                                            Captured Field Coordinates & Photo
                                        </h5>
                                    </div>
                                </div>

                                <div class="p-5">

                                    <!-- GPS Cards -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                        <div class="bg-slate-50 border rounded-xl p-4">

                                            <div class="flex items-center gap-2 mb-2">

                                                <span
                                                    class="w-9 h-9 rounded-full bg-sky-100 flex items-center justify-center">

                                                    <span class="material-symbols-outlined text-sky-600">
                                                        location_on
                                                    </span>

                                                </span>

                                                <span class="text-xs font-semibold text-slate-500">
                                                    GPS LATITUDE
                                                </span>

                                            </div>

                                            <p class="font-bold text-slate-800 break-all">
                                                {{ $application->latitude ?? 'N/A' }}
                                            </p>

                                        </div>

                                        <div class="bg-slate-50 border rounded-xl p-4">

                                            <div class="flex items-center gap-2 mb-2">

                                                <span
                                                    class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">

                                                    <span class="material-symbols-outlined text-indigo-600">
                                                        explore
                                                    </span>

                                                </span>

                                                <span class="text-xs font-semibold text-slate-500">
                                                    GPS LONGITUDE
                                                </span>

                                            </div>

                                            <p class="font-bold text-slate-800 break-all">
                                                {{ $application->longitude ?? 'N/A' }}
                                            </p>

                                        </div>

                                        <div class="bg-slate-50 border rounded-xl p-4">

                                            <div class="flex items-center gap-2 mb-2">

                                                <span
                                                    class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">

                                                    <span class="material-symbols-outlined text-green-600">
                                                        schedule
                                                    </span>

                                                </span>

                                                <span class="text-xs font-semibold text-slate-500">
                                                    CAPTURED DATETIME
                                                </span>

                                            </div>

                                            <p class="font-bold text-slate-800">
                                                {{ $application->image_capture_datetime ?? 'N/A' }}
                                            </p>

                                        </div>

                                    </div>

                                    <!-- Plot Image -->

                                    @if ($application->plot_image)
                                        <div class="mt-6">

                                            <h6 class="text-sm font-semibold text-slate-600 mb-3 flex items-center gap-2">

                                                <span class="material-symbols-outlined text-sky-600">
                                                    image
                                                </span>

                                                Plot Site Photo With Applicant

                                            </h6>

                                            <div
                                                class="border rounded-2xl overflow-hidden shadow hover:shadow-lg transition w-fit">

                                                <img src="{{ asset('storage/' . $application->plot_image) }}"
                                                    class="w-80 h-56 object-cover hover:scale-105 transition duration-300">

                                            </div>

                                        </div>
                                    @endif

                                </div>

                            </div>

                        @endif




                        {{-- VERIFIED --}}

                        @if ($application->physical_possession_status == 'Verified')
                            <div class="bg-green-50 border border-green-200 rounded-xl p-5 mt-5">

                                <h4 class="font-bold text-green-700 mb-4">

                                    Uploaded Verification Documents

                                </h4>

                                <div class="grid grid-cols-2 gap-5">

                                    <a target="_blank" href="{{ asset('storage/' . $application->site_engineer_file) }}"
                                        class="border rounded-xl p-4 hover:bg-white">

                                        📄 Signed Possession Report

                                    </a>

                                    <a target="_blank"
                                        href="{{ asset('storage/' . $application->possession_certificate) }}"
                                        class="border rounded-xl p-4 hover:bg-white">

                                        📄 Final Possession Letter

                                    </a>

                                </div>

                            </div>



                            <div class="bg-green-100 border border-green-300 rounded-xl p-5 mt-5">

                                <div class="flex items-center gap-3">

                                    <span class="material-symbols-outlined text-green-700">

                                        verified

                                    </span>

                                    <div>

                                        <h5 class="font-bold text-green-700">

                                            Verification Completed

                                        </h5>

                                        <p>

                                            This application has been successfully verified.

                                        </p>

                                    </div>

                                </div>

                            </div>
                        @endif

                    </div>

                    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-8 pb-4 border-b border-slate-100">
                            <span
                                class="material-symbols-outlined text-indigo-600 bg-indigo-50 p-2 rounded-xl text-[22px]">timeline</span>
                            <h2 class="text-md font-bold text-slate-800 uppercase tracking-wide">Application Progress
                                Timeline</h2>
                        </div>

                        <div class="relative border-l-2 border-indigo-100 ml-3 space-y-8 pb-4">
                            @forelse($timeline as $index => $log)
                                <div class="relative pl-6">
                                    <span
                                        class="absolute -left-[7px] top-1.5 w-3 h-3 rounded-full bg-indigo-600 ring-4 ring-indigo-50 shadow-sm"></span>

                                    <div class="flex justify-between items-start gap-4">
                                        <div>
                                            <span
                                                class="text-xs font-bold text-indigo-600 uppercase tracking-wider">{{ $log->status ?? 'STATUS CHANGED' }}</span>
                                            <p class="text-sm font-medium text-slate-600 mt-0.5">
                                                {{ $log->remarks ?? 'No description provided.' }}</p>
                                            <span
                                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wide block mt-1.5">
                                                Action By: {{ $log->action_by ?? 'BDPO OFFICER' }}
                                            </span>
                                        </div>
                                        <span class="text-xs font-medium text-slate-400 whitespace-nowrap pt-0.5">
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y - h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="relative pl-6">
                                    <span
                                        class="absolute -left-[7px] top-1.5 w-3 h-3 rounded-full bg-indigo-600 ring-4 ring-indigo-50 shadow-sm"></span>
                                    <div class="flex justify-between items-start gap-4">
                                        <div>
                                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">VISIT
                                                SCHEDULED</span>
                                            <p class="text-sm font-medium text-slate-600 mt-0.5">Visit scheduled by BDO.
                                                Offered slots: Slot 1: 09 Jul 2026 - 09:00 AM, Slot 2: 10 Jul 2026 - 10:00
                                                AM, Slot 3: 11 Jul 2026 - 11:00 AM</p>
                                            <span
                                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wide block mt-1.5">Action
                                                By: BDPO OFFICER</span>
                                        </div>
                                        <span class="text-xs font-medium text-slate-400 whitespace-nowrap pt-0.5">08 Jul
                                            2026 - 10:17 AM</span>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </main>
@endsection
