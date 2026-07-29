@extends('layouts.mmsayDepartmentAuth')
@section('title', 'Possession Application Details')

@section('content')
    @php
        /*
         * The controller SQL is the single source of truth. This keeps dashboard
         * cards, listing rows and the detail page on exactly the same status.
         */
        $currentStatus = $workflowStatus ?: 'awaiting_schedule';

        $statusMeta = [
            'awaiting_schedule' => [
                'label' => 'Awaiting Schedule',
                'icon' => 'calendar_clock',
                'badge' => 'border-amber-200 bg-amber-50 text-amber-700',
            ],
            'scheduled' => [
                'label' => 'Visit Scheduled',
                'icon' => 'event_available',
                'badge' => 'border-blue-200 bg-blue-50 text-blue-700',
            ],
            'pending_verification' => [
                'label' => 'Pending Verification',
                'icon' => 'fact_check',
                'badge' => 'border-orange-200 bg-orange-50 text-orange-700',
            ],
            'visit_missed' => [
                'label' => 'Visit Missed',
                'icon' => 'person_cancel',
                'badge' => 'border-red-200 bg-red-50 text-red-700',
            ],
            'visit_rescheduled' => [
                'label' => 'Visit Rescheduled',
                'icon' => 'event_repeat',
                'badge' => 'border-cyan-200 bg-cyan-50 text-cyan-700',
            ],
            'possession_pending' => [
                'label' => 'Possession Pending',
                'icon' => 'key_off',
                'badge' => 'border-rose-200 bg-rose-50 text-rose-700',
            ],
            'verified' => [
                'label' => 'Verified',
                'icon' => 'verified',
                'badge' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            ],
        ];

        $statusOrder = [
            'awaiting_schedule' => 1,
            'scheduled' => 2,
            'visit_missed' => 2,
            'visit_rescheduled' => 2,
            'pending_verification' => 3,
            'possession_pending' => 4,
            'verified' => 5,
        ];

        $currentRank = $statusOrder[$currentStatus] ?? 1;
        $currentMeta = $statusMeta[$currentStatus] ?? $statusMeta['awaiting_schedule'];
        $hasCitizenSelectedSlot =
            ($application->citizen_visit_date ?? null) &&
            in_array(
                $currentStatus,
                ['pending_verification', 'visit_missed', 'visit_rescheduled', 'possession_pending', 'verified'],
                true,
            );

        $workflowSteps = [
            ['key' => 'awaiting_schedule', 'label' => 'Awaiting Schedule', 'icon' => 'calendar_clock'],
            ['key' => 'scheduled', 'label' => 'Scheduled', 'icon' => 'event_available'],
            ['key' => 'pending_verification', 'label' => 'Pending Verification', 'icon' => 'fact_check'],
            ['key' => 'possession_pending', 'label' => 'Possession Pending', 'icon' => 'key'],
            ['key' => 'verified', 'label' => 'Verified', 'icon' => 'verified'],
        ];

        $formatDate = function ($value, $withTime = false) {
            if (!$value) {
                return '-';
            }

            try {
                return \Carbon\Carbon::parse($value)->format($withTime ? 'd M Y, h:i A' : 'd M Y');
            } catch (\Throwable $e) {
                return $value;
            }
        };

        $documentUrl = function ($path) {
            if (!$path) {
                return null;
            }

            return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
                ? $path
                : asset('storage/' . ltrim($path, '/'));
        };

        $timeline = collect();
        $siteEngineerName = ($application->district_name ?? 'District') . ' Site Engineer';
        $processedByName =
            $application->verified_by_name ??
            ($application->site_engineer_name ?? ($application->verified_by ?? null ? $siteEngineerName : null));

        if (
            ($application->visit_slot_1 ?? null) ||
            ($application->visit_slot_2 ?? null) ||
            ($application->visit_slot_3 ?? null)
        ) {
            $offeredSlots = collect([
                $application->visit_slot_1 ?? null,
                $application->visit_slot_2 ?? null,
                $application->visit_slot_3 ?? null,
            ])
                ->filter()
                ->values()
                ->map(fn($slot, $index) => 'Slot ' . ($index + 1) . ': ' . $formatDate($slot, true))
                ->implode(', ');

            $timeline->push([
                'title' => 'Visit Scheduled',
                'actor' => $siteEngineerName,
                'description' => 'Visit scheduled by Site Engineer. Offered slots: ' . $offeredSlots,
                'date' => $application->created_at ?? null,
                'icon' => 'event',
                'color' => 'bg-amber-500',
            ]);
        }

        if (
            ($application->citizen_visit_date ?? null) &&
            in_array(
                $currentStatus,
                ['pending_verification', 'visit_missed', 'visit_rescheduled', 'possession_pending', 'verified'],
                true,
            )
        ) {
            $timeline->push([
                'title' => 'Slot Selected',
                'actor' => $application->applicant_name ?? 'Citizen',
                'description' =>
                    'Visit slot selected by Citizen: ' . $formatDate($application->citizen_visit_date, true),
                'date' => $application->updated_at ?? null,
                'icon' => 'event_available',
                'color' => 'bg-violet-500',
            ]);
        }

        if ($currentStatus === 'visit_missed') {
            $timeline->push([
                'title' => 'Citizen Absent',
                'actor' => $siteEngineerName,
                'description' => 'Citizen did not attend the selected possession visit.',
                'date' => $application->updated_at ?? null,
                'icon' => 'person_cancel',
                'color' => 'bg-red-500',
            ]);
        }

        if ($currentStatus === 'visit_rescheduled') {
            $timeline->push([
                'title' => 'Visit Rescheduled',
                'actor' => $siteEngineerName,
                'description' => 'A new possession visit date/slot was assigned.',
                'date' => $application->updated_at ?? null,
                'icon' => 'event_repeat',
                'color' => 'bg-cyan-500',
            ]);
        }

        if (
            in_array($currentStatus, ['possession_pending', 'verified'], true) ||
            ($application->image_capture_datetime ?? null) ||
            ($application->plot_image ?? null) ||
            (($application->latitude ?? null) && ($application->longitude ?? null))
        ) {
            $timeline->push([
                'title' => 'Site Verified',
                'actor' => $siteEngineerName,
                'description' => 'Site verification details (GPS, Photo with Applicant) submitted by Site Engineer.',
                'date' => $application->image_capture_datetime ?? ($application->updated_at ?? null),
                'icon' => 'fact_check',
                'color' => 'bg-slate-500',
            ]);
        }

        if ($currentStatus === 'verified') {
            $timeline->push([
                'title' => 'Verified',
                'actor' => $siteEngineerName,
                'description' =>
                    'Final physical possession documents (Citizen Signed & Site Engineer file) uploaded and verified.',
                'date' => $application->verified_at ?? ($application->updated_at ?? null),
                'icon' => 'verified',
                'color' => 'bg-emerald-500',
            ]);
        }
    @endphp

    <main class="ml-52 min-h-screen bg-slate-50 px-5 pb-8 pt-20">
        <div class="mx-auto max-w-[1800px] space-y-4">

            {{-- Page header --}}
            <section
                class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm
                   sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-3">
                    <a href="{{ url()->previous() }}"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200
                           bg-white text-slate-500 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </a>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg font-bold text-slate-900">
                                Physical Possession Details
                            </h1>

                            <span
                                class="rounded-full border px-2.5 py-1 text-[9px] font-bold uppercase {{ $currentMeta['badge'] }}">
                                {{ $currentMeta['label'] }}
                            </span>
                        </div>

                        <p class="mt-1 truncate text-xs text-slate-500">
                            Asset #{{ $application->asset_id }} ·
                            {{ $application->asset_name ?: 'Property details unavailable' }}
                            @if ($application->physical_application_number ?? $application->possession_id)
                                · {{ $application->physical_application_number ?: $application->possession_id }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="rounded-xl bg-slate-50 px-3 py-2 text-right">
                        <p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Physical Application No.</p>
                        <p class="mt-0.5 text-xs font-bold text-slate-800">
                            {{ $application->physical_application_number ?: ($application->possession_id ?: '-') }}
                        </p>
                    </div>

                    <button type="button" onclick="window.print()"
                        class="inline-flex h-10 items-center gap-1.5 rounded-xl bg-slate-800 px-4 text-xs font-semibold text-white transition hover:bg-slate-900">
                        <span class="material-symbols-outlined text-[17px]">print</span>
                        Print
                    </button>
                </div>
            </section>

            @if (session('success'))
                <div
                    class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                    <p class="font-bold">Visit status could not be updated.</p>
                    <ul class="mt-1 list-disc space-y-0.5 pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Workflow progress --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-[19px] text-indigo-600">
                            account_tree
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">Possession Workflow</h2>
                            <p class="mt-0.5 text-[10px] text-slate-400">Complete application lifecycle</p>
                        </div>
                    </div>

                    <span class="text-[10px] font-semibold text-slate-500">
                        Step {{ $currentRank }} of 5
                    </span>
                </div>

                <div class="overflow-x-auto p-5">
                    <div class="relative grid min-w-[850px] grid-cols-5 gap-3">
                        <div class="absolute left-[10%] right-[10%] top-6 h-0.5 bg-slate-200"></div>

                        @foreach ($workflowSteps as $index => $step)
                            @php
                                $stepRank = $index + 1;
                                $isComplete = $stepRank < $currentRank;
                                $isCurrent = $stepRank === $currentRank;
                            @endphp

                            <div class="relative z-10 text-center">
                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full border-4 border-white text-[20px] shadow-sm
                                       {{ $isComplete
                                           ? 'bg-emerald-500 text-white'
                                           : ($isCurrent
                                               ? 'bg-indigo-600 text-white ring-4 ring-indigo-100'
                                               : 'bg-slate-100 text-slate-400') }}">
                                    <span class="material-symbols-outlined text-[20px]">
                                        {{ $isComplete ? 'check' : $step['icon'] }}
                                    </span>
                                </div>

                                <p
                                    class="mt-2 text-[10px] font-bold {{ $isCurrent ? 'text-indigo-600' : ($isComplete ? 'text-emerald-600' : 'text-slate-400') }}">
                                    {{ $step['label'] }}
                                </p>

                                <p class="mt-0.5 text-[9px] text-slate-400">
                                    {{ $isComplete ? 'Completed' : ($isCurrent ? 'Current status' : 'Pending') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            @if (in_array($currentStatus, ['visit_missed', 'visit_rescheduled'], true))
                <section
                    class="flex items-start gap-3 rounded-2xl border p-4
                       {{ $currentStatus === 'visit_missed' ? 'border-red-200 bg-red-50' : 'border-cyan-200 bg-cyan-50' }}">
                    <span
                        class="material-symbols-outlined flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-[20px]
                           {{ $currentStatus === 'visit_missed' ? 'bg-red-100 text-red-600' : 'bg-cyan-100 text-cyan-600' }}">
                        {{ $currentStatus === 'visit_missed' ? 'person_cancel' : 'event_repeat' }}
                    </span>

                    <div>
                        <h3
                            class="text-sm font-bold {{ $currentStatus === 'visit_missed' ? 'text-red-800' : 'text-cyan-800' }}">
                            {{ $currentStatus === 'visit_missed' ? 'Citizen Did Not Attend' : 'Possession Visit Rescheduled' }}
                        </h3>
                        <p
                            class="mt-1 text-xs leading-relaxed {{ $currentStatus === 'visit_missed' ? 'text-red-600' : 'text-cyan-600' }}">
                            {{ $currentStatus === 'visit_missed'
                                ? 'The selected visit was missed. Update the application with a new visit date/slot when rescheduling.'
                                : 'A new visit date/slot has been assigned. The application will move to Pending Verification after citizen confirmation.' }}
                        </p>
                    </div>
                </section>
            @endif

            {{-- Payment summary --}}
            <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">
                @foreach ([['Property Cost', $flatCost, 'payments', 'border-slate-200', 'bg-slate-100 text-slate-600', 'text-slate-800'], ['Initial Received', $initialReceived, 'account_balance_wallet', 'border-indigo-100', 'bg-indigo-50 text-indigo-600', 'text-indigo-600'], ['Cash Receipts', $receiptTotal, 'receipt_long', 'border-cyan-100', 'bg-cyan-50 text-cyan-600', 'text-cyan-600'], ['Total Received', $totalReceived, 'verified', 'border-emerald-100', 'bg-emerald-50 text-emerald-600', 'text-emerald-600']] as [$label, $value, $icon, $border, $iconClass, $valueClass])
                    <div class="rounded-xl border bg-white p-4 shadow-sm {{ $border }}">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ $label }}
                                </p>
                                <p class="mt-2 text-xl font-bold {{ $valueClass }}">₹{{ number_format($value, 2) }}</p>
                            </div>
                            <span
                                class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-lg text-[18px] {{ $iconClass }}">
                                {{ $icon }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </section>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-12">
                {{-- Main information --}}
                <div class="space-y-4 xl:col-span-7">

                    {{-- Applicant and property --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                            <span
                                class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-[19px] text-indigo-600">
                                badge
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800">Applicant & Property Details</h2>
                                <p class="mt-0.5 text-[10px] text-slate-400">Personal, allotment and location information
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 divide-y divide-slate-100 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
                            <div class="p-5">
                                <p
                                    class="mb-4 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-indigo-600">
                                    <span class="material-symbols-outlined text-[16px]">person</span>
                                    Applicant Information
                                </p>

                                <dl class="grid grid-cols-2 gap-x-5 gap-y-4">
                                    @foreach ([
            'Applicant Name' => $application->applicant_name,
            'Father Name' => $application->father_name,
            'Mobile Number' => $application->mobile,
            'Purchaser Application No.' => $application->purchaser_application_number,
            'Physical Application No.' => $application->physical_application_number,
            'MMSAY Application' => $application->mmsay_application_no,
            'PPP ID' => $application->ppp_id,
            'Member ID' => $application->member_id,
            'Possession ID' => $application->possession_id,
        ] as $label => $value)
                                        <div>
                                            <dt class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                                {{ $label }}</dt>
                                            <dd class="mt-1 break-words text-xs font-semibold text-slate-800">
                                                {{ $value ?: '-' }}</dd>
                                        </div>
                                    @endforeach

                                    <div class="col-span-2 rounded-xl bg-slate-50 p-3">
                                        <dt class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">Address
                                        </dt>
                                        <dd class="mt-1 text-xs font-medium leading-relaxed text-slate-700">
                                            {{ $application->address ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="p-5">
                                <p
                                    class="mb-4 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-violet-600">
                                    <span class="material-symbols-outlined text-[16px]">home_work</span>
                                    Property Information
                                </p>

                                <dl class="grid grid-cols-2 gap-x-5 gap-y-4">
                                    @foreach ([
            'Asset ID' => '#' . $application->asset_id,
            'Property Name' => $application->asset_name,
            'Property Size' => trim(($application->asset_size ?? '') . ' ' . ($application->asset_unit ?? '')),
            'Auction ID' => $application->property_auction_id ?? null,
            'District' => $application->district_name,
            'City' => $application->city_name,
            'Village / Sector' => $application->sector_name,
        ] as $label => $value)
                                        <div>
                                            <dt class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                                {{ $label }}</dt>
                                            <dd class="mt-1 break-words text-xs font-semibold text-slate-800">
                                                {{ $value ?: '-' }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                    </section>

                    {{-- Read-only visit schedule --}}
                    @if (
                        ($application->visit_slot_1 ?? null) ||
                            ($application->visit_slot_2 ?? null) ||
                            ($application->visit_slot_3 ?? null))
                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                                <span
                                    class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-[19px] text-blue-600">
                                    calendar_month
                                </span>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-800">Visit Schedule</h2>
                                    <p class="mt-0.5 text-[10px] text-slate-400">Offered slots and citizen selection</p>
                                </div>
                            </div>

                            <div class="p-5">
                                @if ($hasCitizenSelectedSlot)
                                    <div
                                        class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-300 bg-emerald-50 p-4">
                                        <span
                                            class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                            event_available
                                        </span>
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700">
                                                Confirmed Visit Slot
                                            </p>
                                            <p class="mt-1 text-sm font-bold text-emerald-800">
                                                {{ $formatDate($application->citizen_visit_date, true) }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="mb-4 flex items-center gap-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                                        <span
                                            class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-lg bg-white text-slate-400">
                                            event_busy
                                        </span>
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                                Confirmed Visit Slot
                                            </p>
                                            <p class="mt-1 text-sm font-bold text-slate-600">
                                                Slot Not Selected Yet
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                    {{ $hasCitizenSelectedSlot ? 'Alternate Visit Options' : 'Offered Visit Options' }}
                                </p>

                                <div class="flex flex-wrap gap-2">
                                    @foreach ([['Slot 1', $application->visit_slot_1 ?? null], ['Slot 2', $application->visit_slot_2 ?? null], ['Slot 3', $application->visit_slot_3 ?? null]] as [$label, $slot])
                                        @if ($slot)
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700">
                                                <span
                                                    class="material-symbols-outlined text-[15px] text-blue-600">event</span>
                                                {{ $label }}: {{ $formatDate($slot, true) }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="mt-3 rounded-xl bg-slate-50 p-3">
                                    <p class="flex items-center gap-1.5 text-[9px] font-bold uppercase text-slate-500">
                                        <span class="material-symbols-outlined text-[15px]">info</span>
                                        Visit Instructions
                                    </p>
                                    <p class="mt-1.5 text-xs leading-relaxed text-slate-700">
                                        {{ $application->visit_instructions ?: 'No visit instructions added.' }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    @endif

                    {{-- Timeline --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                            <span
                                class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-[19px] text-violet-600">
                                history
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800">Status Timeline</h2>
                                <p class="mt-0.5 text-[10px] text-slate-400">Recorded workflow activity</p>
                            </div>
                        </div>

                        <div class="p-5">
                            <div
                                class="relative space-y-5 before:absolute before:bottom-3 before:left-[17px] before:top-3 before:w-px before:bg-slate-200">
                                @foreach ($timeline as $event)
                                    <div class="relative flex gap-3">
                                        <span
                                            class="material-symbols-outlined z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-[17px] text-white shadow-sm {{ $event['color'] }}">
                                            {{ $event['icon'] }}
                                        </span>
                                        <div class="min-w-0 flex-1 rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                                            <div
                                                class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                                <p class="text-xs font-bold text-slate-800">
                                                    {{ $event['title'] }}
                                                    @if ($event['actor'] ?? null)
                                                        <span class="font-normal text-slate-500">
                                                            (by {{ $event['actor'] }})
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="text-[9px] font-medium text-slate-400">
                                                    {{ $formatDate($event['date'], true) }}</p>
                                            </div>
                                            <p class="mt-1 text-[10px] leading-relaxed text-slate-500">
                                                {{ $event['description'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    {{-- Verification outcome --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                            <span
                                class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-[19px] text-emerald-600">
                                shield_person
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800">Verification & Approval Outcome</h2>
                                <p class="mt-0.5 text-[10px] text-slate-400">Processing and remarks information</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2">
                            @foreach ([['Verified By', $processedByName, 'person_check'], ['Verified At', $formatDate($application->verified_at ?? null, true), 'event_available']] as [$label, $value, $icon])
                                <div
                                    class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
                                    <span
                                        class="material-symbols-outlined flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-[19px] text-emerald-700">
                                        {{ $icon }}
                                    </span>

                                    <div class="min-w-0">
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                            {{ $label }}
                                        </p>
                                        <p class="mt-1 break-words text-xs font-bold text-slate-800">
                                            {{ $value ?: '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:col-span-2">
                                <p
                                    class="flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wider text-slate-500">
                                    <span class="material-symbols-outlined text-[15px]">notes</span>
                                    Verification Remarks
                                </p>
                                <p class="mt-2 text-xs leading-relaxed text-slate-700">
                                    {{ $application->remarks ?? null ?: 'No remarks provided.' }}
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Right information column --}}
                <div class="space-y-4 xl:col-span-5">

                    {{-- GPS --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                            <span
                                class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-[19px] text-blue-600">
                                location_on
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800">GPS Map Location</h2>
                                <p class="mt-0.5 text-[10px] text-slate-400">Captured site coordinates</p>
                            </div>
                        </div>

                        @if (($application->latitude ?? null) && ($application->longitude ?? null))
                            <div class="p-5">
                                <div class="rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 p-5 text-center">
                                    <span class="material-symbols-outlined text-4xl text-blue-600">distance</span>
                                    <p class="mt-2 text-xs font-bold text-slate-800">
                                        {{ $application->latitude }}, {{ $application->longitude }}
                                    </p>
                                    <p class="mt-1 text-[10px] text-slate-500">
                                        Captured: {{ $formatDate($application->image_capture_datetime ?? null, true) }}
                                    </p>
                                    <a href="https://www.google.com/maps?q={{ $application->latitude }},{{ $application->longitude }}"
                                        target="_blank"
                                        class="mt-3 inline-flex h-9 items-center gap-1.5 rounded-lg bg-blue-600 px-4 text-[10px] font-bold text-white hover:bg-blue-700">
                                        <span class="material-symbols-outlined text-[16px]">map</span>
                                        Open in Google Maps
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="p-5">
                                <div
                                    class="flex min-h-32 flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 text-center">
                                    <span class="material-symbols-outlined text-3xl text-slate-300">location_off</span>
                                    <p class="mt-2 text-xs font-semibold text-slate-500">GPS coordinates not captured</p>
                                </div>
                            </div>
                        @endif
                    </section>

                    {{-- Plot image --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                            <span
                                class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-[19px] text-cyan-600">
                                add_photo_alternate
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800">Plot Site Photo</h2>
                                <p class="mt-0.5 text-[10px] text-slate-400">Site engineer capture</p>
                            </div>
                        </div>

                        <div class="p-5">
                            @if ($application->plot_image ?? null)
                                <a href="{{ $documentUrl($application->plot_image) }}" target="_blank"
                                    class="group block overflow-hidden rounded-xl border border-slate-200">
                                    <img src="{{ $documentUrl($application->plot_image) }}" alt="Plot site"
                                        class="h-56 w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                                    <div class="flex items-center justify-between border-t border-slate-100 px-3 py-2">
                                        <span class="text-[10px] font-semibold text-slate-600">View full-size image</span>
                                        <span
                                            class="material-symbols-outlined text-[16px] text-indigo-500">open_in_new</span>
                                    </div>
                                </a>
                            @else
                                <div
                                    class="flex min-h-44 flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 text-center">
                                    <span
                                        class="material-symbols-outlined text-4xl text-slate-300">image_not_supported</span>
                                    <p class="mt-2 text-xs font-semibold text-slate-500">Plot photo not uploaded</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Documents --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                            <span
                                class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-[19px] text-violet-600">
                                folder_open
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800">Possession Documents</h2>
                                <p class="mt-0.5 text-[10px] text-slate-400">Uploaded application and verification files
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3 p-5">
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <div
                                    class="rounded-xl border p-3 {{ $application->possession_certificate ?? null ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }}">
                                    <p class="text-[9px] font-bold uppercase text-slate-500">Signed Application</p>
                                    <p
                                        class="mt-1 text-xs font-bold {{ $application->possession_certificate ?? null ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $application->possession_certificate ?? null ? 'Uploaded' : 'Pending' }}
                                    </p>
                                </div>

                                <div
                                    class="rounded-xl border p-3 {{ $application->site_engineer_file ?? null ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }}">
                                    <p class="text-[9px] font-bold uppercase text-slate-500">Final Possession Letter</p>
                                    <p
                                        class="mt-1 text-xs font-bold {{ $application->site_engineer_file ?? null ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $application->site_engineer_file ?? null ? 'Uploaded' : 'Pending' }}
                                    </p>
                                </div>
                            </div>

                            @foreach ([['Registration Details', $application->registration_details ?? null, 'description', 'indigo'], ['Physical Possession Application (Signed)', $application->possession_certificate ?? null, 'workspace_premium', 'emerald'], ['Final Possession Letter', $application->site_engineer_file ?? null, 'engineering', 'orange']] as [$label, $file, $icon, $color])
                                @php
                                    $documentStyles = [
                                        'indigo' => 'bg-indigo-50 text-indigo-600',
                                        'emerald' => 'bg-emerald-50 text-emerald-600',
                                        'orange' => 'bg-orange-50 text-orange-600',
                                    ];
                                @endphp

                                @if ($file)
                                    <a href="{{ $documentUrl($file) }}" target="_blank"
                                        class="group flex items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-indigo-200 hover:bg-indigo-50/40">
                                        <span
                                            class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-xl text-[19px] {{ $documentStyles[$color] }}">
                                            {{ $icon }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-xs font-bold text-slate-800">{{ $label }}</p>
                                            <p class="mt-0.5 text-[9px] text-emerald-600">Document available</p>
                                        </div>
                                        <span
                                            class="material-symbols-outlined text-[17px] text-slate-300 group-hover:text-indigo-500">open_in_new</span>
                                    </a>
                                @else
                                    {{-- <div
                                        class="flex items-center gap-3 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3">
                                        <span
                                            class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-xl bg-white text-[19px] text-slate-300">
                                            {{ $icon }}
                                        </span>
                                        <div>
                                            <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
                                            <p class="mt-0.5 text-[9px] text-slate-400">Not uploaded</p>
                                        </div>
                                    </div> --}}
                                @endif
                            @endforeach
                        </div>
                    </section>
                </div>
            </section>

            {{-- Cash receipt history --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div
                    class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-[19px] text-emerald-600">
                            receipt_long
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">Cash Receipt History</h2>
                            <p class="mt-0.5 text-[10px] text-slate-400">{{ $cashReceipts->count() }} receipt records</p>
                        </div>
                    </div>

                    <span class="rounded-lg bg-emerald-50 px-3 py-1.5 text-[10px] font-bold text-emerald-700">
                        Receipt Total: ₹{{ number_format($receiptTotal, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[750px] text-left">
                        <thead class="bg-slate-50 text-[9px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3">S.No.</th>
                                <th class="px-5 py-3">Receipt ID</th>
                                <th class="px-5 py-3">Receipt Number</th>
                                <th class="px-5 py-3">Payment Date</th>
                                <th class="px-5 py-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($cashReceipts as $index => $receipt)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-3 text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3 font-semibold text-slate-700">#{{ $receipt->id }}</td>
                                    <td class="px-5 py-3 font-medium text-slate-800">{{ $receipt->receipt_number ?: '-' }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">{{ $formatDate($receipt->created_date) }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-emerald-600">
                                        ₹{{ number_format($receipt->total_paid_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <span class="material-symbols-outlined text-3xl text-slate-300">receipt_long</span>
                                        <p class="mt-2 text-xs font-semibold text-slate-500">No cash receipts found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        @media print {

            html,
            body {
                width: 100% !important;
                margin: 0 !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            aside,
            header,
            button,
            .visit-update-panel {
                display: none !important;
            }

            main {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                background: white !important;
                overflow: visible !important;
            }

            main>div {
                width: 100% !important;
                max-width: none !important;
            }

            section {
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: none !important;
            }

            .overflow-x-auto,
            .overflow-hidden {
                overflow: visible !important;
            }

            table {
                width: 100% !important;
                min-width: 0 !important;
                table-layout: fixed;
                font-size: 8px !important;
            }

            th,
            td {
                padding: 5px !important;
                overflow-wrap: anywhere;
            }

            img {
                max-height: 150px !important;
                object-fit: contain !important;
            }
        }
    </style>
@endsection
