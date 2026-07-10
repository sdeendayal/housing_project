@extends('layouts.mmgayCEOAuth')

@section('title', 'Physical Possession Application - CEO')

@section('content')

    {{-- Yahan lg:pl-[272px] add kiya hai jo content ko sidebar ke piche jaane se rokega --}}
    <main class="min-h-screen bg-slate-100 p-4 md:p-6 lg:pl-[272px] pt-20 w-full overflow-x-hidden">

        <div class="max-w-full xl:max-w-[1400px] mx-auto">
            <br><br>
            {{-- Header --}}
            <div
                class="mb-6 bg-gradient-to-r from-blue-500/10 to-indigo-500/5 border border-blue-100 rounded-2xl p-4 md:p-5 flex flex-row items-center justify-between gap-4 shadow-xs">

                <div class="min-w-0 flex items-center gap-3">
                    {{-- Left side icon wrapper to enhance card look --}}
                    <div
                        class="hidden sm:flex p-2.5 bg-blue-500 text-white rounded-xl shadow-md shadow-blue-500/20 shrink-0">
                        <span class="material-symbols-outlined text-[22px]">
                            real_estate_agent
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h1 class="text-base md:text-xl font-bold text-slate-800 tracking-tight">
                            Physical Possession Status
                        </h1>
                        <p class="text-xs md:text-sm text-slate-500 mt-0.5 font-medium">
                            View physical possession handover details
                        </p>
                    </div>
                </div>

                {{-- Back Button --}}
                <a href="{{ url()->previous() }}"
                    class="flex items-center gap-1.5 px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 active:scale-95 text-slate-600 rounded-xl text-xs md:text-sm font-semibold shadow-sm transition-all duration-150 shrink-0">
                    <span class="material-symbols-outlined text-[18px]">
                        arrow_back
                    </span>
                    Back
                </a>
            </div>

            {{-- Main Layout Grid --}}
            <div class="flex flex-col lg:flex-row items-start gap-6 w-full">

                {{-- LEFT PANEL: BENEFICIARY CARD --}}
                <div
                    class="w-full lg:w-[320px] xl:w-[380px] bg-white border border-slate-200 rounded-2xl shadow-sm p-5 md:p-6 shrink-0">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                        <span class="material-symbols-outlined text-indigo-600 bg-indigo-50 p-2 rounded-xl">
                            person
                        </span>
                        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">
                            Beneficiary Details
                        </h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">
                                Name
                            </label>
                            <div class="font-bold text-slate-800 break-words text-base">
                                {{ $application->OwnerName ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">
                                Father / Husband
                            </label>
                            <div class="text-slate-700 break-words">
                                {{ $application->FatherHusbandName ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">
                                Mobile
                            </label>
                            <div class="text-slate-700">
                                {{ $application->MobileNo ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">
                                Registration No
                            </label>
                            <div class="font-bold text-indigo-600 tracking-wide text-sm break-all">
                                {{ $application->RegistrationNo ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">
                                District
                            </label>
                            <div class="text-slate-700">
                                {{ $application->DistrictName ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">
                                Village
                            </label>
                            <div class="text-slate-700 break-words">
                                {{ $application->VillageName ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT PANEL: STATUS & TIMELINE --}}
                <div class="w-full flex-1 min-w-0 space-y-6">

                    {{-- Card: Possession Status --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 md:p-6 min-w-0">
                        {{-- Card Header --}}
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                            <span class="material-symbols-outlined text-blue-600 bg-blue-50 p-2.5 rounded-xl shadow-xs">
                                assignment_turned_in
                            </span>
                            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                                Possession Status & Verification
                            </h2>
                        </div>

                        {{-- VISIT SCHEDULED --}}
                        @if ($application->physical_possession_status == 'Visit Scheduled')
                            <div class="border border-amber-200 bg-amber-50/20 rounded-2xl overflow-hidden shadow-xs">
                                <div
                                    class="bg-gradient-to-r from-orange-500 to-amber-500 p-4 text-white flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                                    <h4 class="font-bold text-xs md:text-sm tracking-wide uppercase">
                                        Meeting Slot Options
                                    </h4>
                                </div>

                                <div class="p-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    @foreach ([$application->visit_slot_1, $application->visit_slot_2, $application->visit_slot_3] as $key => $slot)
                                        <div
                                            class="bg-white border border-slate-200/80 rounded-xl p-4 shadow-xs flex flex-col justify-between min-w-0 hover:border-amber-300 transition-colors">
                                            <p class="font-bold text-[10px] text-slate-400 uppercase tracking-wider">
                                                Slot {{ $key + 1 }}
                                            </p>
                                            <p class="text-xs md:text-sm font-semibold text-slate-700 mt-2 break-words">
                                                {{ $slot ?? 'Not Available' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- SLOT SELECTED --}}
                        @if ($application->physical_possession_status == 'Slot Selected')
                            <div
                                class="bg-gradient-to-br from-blue-50/60 to-indigo-50/30 border border-blue-200 rounded-2xl p-5 shadow-xs">
                                <div class="flex items-center gap-2 text-blue-700 mb-3">
                                    <span class="material-symbols-outlined text-xl">check_circle</span>
                                    <h4 class="font-bold text-sm md:text-base tracking-wide">
                                        Meeting Slot Confirmed
                                    </h4>
                                </div>
                                <p
                                    class="mt-1 font-bold text-slate-800 text-base md:text-lg tracking-tight bg-white/80 border border-blue-100 px-4 py-2.5 rounded-xl inline-block shadow-2xs">
                                    {{ $application->citizen_visit_date }}
                                </p>

                                @if ($application->visit_instructions)
                                    <div
                                        class="mt-4 text-xs md:text-sm text-slate-600 bg-white p-4 rounded-xl border border-slate-100 w-full break-words shadow-2xs">
                                        <span
                                            class="font-bold text-slate-700 block mb-1 text-[11px] uppercase tracking-wider text-blue-600">
                                            Instructions from Authority:
                                        </span>
                                        {{ $application->visit_instructions }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- SITE VERIFIED / VERIFIED --}}
                        @if (in_array($application->physical_possession_status, ['Site Verified', 'Verified']))
                            <div class="border border-sky-100 bg-sky-50/20 rounded-2xl p-4 md:p-5 shadow-xs">
                                <h4
                                    class="font-bold text-sky-900 mb-4 text-xs md:text-sm uppercase tracking-wider flex items-center gap-2">
                                    <span
                                        class="material-symbols-outlined text-lg bg-sky-100 p-1 rounded-lg text-sky-700">pin_drop</span>
                                    Field Verification Details
                                </h4>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="bg-white border border-slate-100 p-4 rounded-xl shadow-2xs min-w-0">
                                        <span
                                            class="text-[10px] font-bold text-slate-400 block mb-1 uppercase tracking-wider">Latitude</span>
                                        <div class="font-bold text-slate-800 text-xs md:text-sm truncate">
                                            {{ $application->latitude ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="bg-white border border-slate-100 p-4 rounded-xl shadow-2xs min-w-0">
                                        <span
                                            class="text-[10px] font-bold text-slate-400 block mb-1 uppercase tracking-wider">Longitude</span>
                                        <div class="font-bold text-slate-800 text-xs md:text-sm truncate">
                                            {{ $application->longitude ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="bg-white border border-slate-100 p-4 rounded-xl shadow-2xs min-w-0">
                                        <span
                                            class="text-[10px] font-bold text-slate-400 block mb-1 uppercase tracking-wider">Capture
                                            Time</span>
                                        <div class="font-bold text-slate-800 text-xs md:text-sm break-words">
                                            {{ $application->image_capture_datetime ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                @if ($application->plot_image)
                                    <div
                                        class="mt-5 max-w-sm rounded-xl overflow-hidden border border-slate-200/80 shadow-sm bg-white p-2">
                                        <img src="{{ asset('storage/' . $application->plot_image) }}"
                                            class="w-full h-auto rounded-lg object-cover hover:scale-[1.02] transition-transform duration-200">
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- VERIFIED DOCUMENTS --}}
                        @if ($application->physical_possession_status == 'Verified')
                            <div
                                class="bg-gradient-to-br from-green-50/60 to-emerald-50/20 border border-green-200 rounded-2xl p-4 md:p-5 mt-5 shadow-xs">
                                <div class="flex items-center gap-2 text-green-700 mb-4">
                                    <span class="material-symbols-outlined text-xl">verified_user</span>
                                    <h4 class="font-bold text-sm md:text-base tracking-wide">
                                        Verification Completed
                                    </h4>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <a target="_blank" href="{{ asset('storage/' . $application->site_engineer_file) }}"
                                        class="flex items-center justify-between gap-3 bg-white hover:bg-green-50/40 border border-slate-200 hover:border-green-300 rounded-xl p-4 transition-all duration-150 text-xs md:text-sm font-semibold text-slate-700 shadow-2xs min-w-0 group">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span
                                                class="text-lg shrink-0 group-hover:scale-110 transition-transform">📄</span>
                                            <span class="truncate">Signed Possession Report</span>
                                        </div>
                                        <span
                                            class="material-symbols-outlined text-slate-400 text-[18px] group-hover:text-green-600 transition-colors">download</span>
                                    </a>

                                    <a target="_blank"
                                        href="{{ asset('storage/' . $application->possession_certificate) }}"
                                        class="flex items-center justify-between gap-3 bg-white hover:bg-green-50/40 border border-slate-200 hover:border-green-300 rounded-xl p-4 transition-all duration-150 text-xs md:text-sm font-semibold text-slate-700 shadow-2xs min-w-0 group">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span
                                                class="text-lg shrink-0 group-hover:scale-110 transition-transform">📄</span>
                                            <span class="truncate">Final Possession Letter</span>
                                        </div>
                                        <span
                                            class="material-symbols-outlined text-slate-400 text-[18px] group-hover:text-green-600 transition-colors">download</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Card: Timeline --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 md:p-6">
                        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-6">
                            Application Progress Timeline
                        </h2>

                        @if (isset($timeline) && count($timeline) > 0)
                            <div class="border-l-2 border-indigo-100 ml-3 space-y-6">
                                @foreach ($timeline as $log)
                                    <div class="relative pl-6">
                                        <div
                                            class="absolute -left-[7px] top-1.5 w-3 h-3 bg-indigo-500 rounded-full ring-4 ring-indigo-50">
                                        </div>

                                        <span class="font-bold text-indigo-600 text-sm tracking-wide block sm:inline">
                                            {{ $log->status ?? 'STATUS UPDATED' }}
                                        </span>

                                        <p class="text-slate-600 text-sm mt-1 break-words">
                                            {{ $log->remarks ?? 'No remarks available' }}
                                        </p>

                                        <span class="text-[11px] font-medium text-slate-400 block mt-1">
                                            {{ $log->created_at ?? '' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-400 text-sm">
                                No timeline logs found for this application.
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>

    </main>

@endsection
