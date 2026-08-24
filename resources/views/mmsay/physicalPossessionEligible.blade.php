@extends('layouts.mmsayDepartmentAuth')
@section('title', 'Physical Possession Workflow')

@section('content')
    <style>
        @property --possession-orbit-angle {
            syntax: '<angle>';
            initial-value: 0deg;
            inherits: false;
        }

        .possession-orbit-card {
            position: relative;
            isolation: isolate;
        }

        .possession-orbit-card::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 20;
            border-radius: inherit;
            padding: 2px;
            pointer-events: none;
            opacity: 0;
            background: conic-gradient(from var(--possession-orbit-angle),
                    #7c3aed,
                    #2563eb,
                    #06b6d4,
                    #10b981,
                    #f59e0b,
                    #f43f5e,
                    #7c3aed);
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            transition: opacity 180ms ease;
        }

        .possession-orbit-card:hover::before,
        .possession-orbit-card:focus-visible::before {
            opacity: 1;
            animation: possession-border-orbit 1.6s linear infinite;
        }

        @keyframes possession-border-orbit {
            to {
                --possession-orbit-angle: 360deg;
            }
        }

        #possessionCardTooltip {
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            max-width: 280px;
            transform: translate(-50%, calc(-100% - 14px)) scale(.96);
            border: 1px solid rgba(148, 163, 184, .28);
            border-radius: 10px;
            padding: 7px 11px;
            background: rgba(15, 23, 42, .94);
            box-shadow: 0 10px 28px rgba(15, 23, 42, .22);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            visibility: hidden;
            transition: opacity 140ms ease, transform 140ms ease, visibility 140ms ease;
        }

        #possessionCardTooltip::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -5px;
            width: 9px;
            height: 9px;
            background: rgba(15, 23, 42, .94);
            transform: translateX(-50%) rotate(45deg);
        }

        #possessionCardTooltip.is-visible {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, calc(-100% - 14px)) scale(1);
        }

        @media (prefers-reduced-motion: reduce) {
            .possession-orbit-card:hover::before,
            .possession-orbit-card:focus-visible::before {
                animation: none;
            }
        }
    </style>

    <main class="ml-52 min-h-screen bg-slate-50 px-5 pb-6 pt-20">
        <div class="mx-auto max-w-[1800px] space-y-4">

            {{-- Filters --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-slate-900">Physical Possession Workflow</h1>
                        <p class="mt-0.5 text-xs text-slate-500">
                            Schedule, verification and possession status management
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('physical.possession.csv', request()->query()) }}"
                            class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-700">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            Excel CSV
                        </a>

                        <a href="{{ route('physical.possession.print', request()->query()) }}" target="_blank"
                            class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-slate-800 px-3 text-xs font-semibold text-white hover:bg-slate-900">
                            <span class="material-symbols-outlined text-[16px]">print</span>
                            Print
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('physical.possession.index') }}"
                    class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-12">

                    <input type="search" name="search" value="{{ $filters['search'] }}"
                        placeholder="Asset, applicant, mobile..."
                        class="h-10 rounded-lg border border-slate-200 px-3 text-xs outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 xl:col-span-4">

                    <select name="district_id" id="district_id"
                        onchange="document.getElementById('city_id').value=''; document.getElementById('sector_id').value=''; this.form.submit();"
                        class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 xl:col-span-2">
                        <option value="">All Districts</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->DistrictId }}" @selected($filters['district_id'] == $district->DistrictId)>
                                {{ $district->DistrictName }}
                            </option>
                        @endforeach
                    </select>

                    <select name="city_id" id="city_id"
                        onchange="document.getElementById('sector_id').value=''; this.form.submit();"
                        @disabled(!$filters['district_id'])
                        class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 xl:col-span-2">
                        <option value="">
                            {{ $filters['district_id'] ? 'All Block/Town' : 'Select district first' }}
                        </option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->CityId }}" @selected($filters['city_id'] == $city->CityId)>
                                {{ $city->CityName }}
                            </option>
                        @endforeach
                    </select>

                    <select name="sector_id" id="sector_id" @disabled(!$filters['city_id'])
                        class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-xs outline-none focus:border-indigo-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 xl:col-span-2">
                        <option value="">
                            {{ $filters['city_id'] ? 'All Village/Ward' : 'Select city first' }}
                        </option>
                        @foreach ($sectors as $sector)
                            <option value="{{ $sector->SectorId }}" @selected($filters['sector_id'] == $sector->SectorId)>
                                {{ $sector->SectorName }}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2 xl:col-span-2">
                        <button
                            class="flex h-10 flex-1 items-center justify-center rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                            Apply
                        </button>
                        <a href="{{ route('physical.possession.index') }}"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">
                            <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                        </a>
                    </div>
                </form>
            </section>

            @php
                $cards = [
                    [
                        'key' => '',
                        'label' => 'Total Eligible',
                        'value' => $statusStats->total_records ?? 0,
                        'icon' => 'groups',
                        'icon_class' => 'bg-indigo-50 text-indigo-600',
                        'value_class' => 'text-indigo-600',
                        'active_class' => 'border-indigo-300 ring-2 ring-indigo-100',
                    ],
                    [
                        'key' => 'awaiting_schedule',
                        'label' => 'Awaiting Schedule',
                        'value' => $statusStats->awaiting_schedule ?? 0,
                        'icon' => 'calendar_clock',
                        'icon_class' => 'bg-amber-50 text-amber-600',
                        'value_class' => 'text-amber-600',
                        'active_class' => 'border-amber-300 ring-2 ring-amber-100',
                    ],
                    [
                        'key' => 'scheduled',
                        'label' => 'Confirmation Pending from Citizen',
                        'value' => $statusStats->scheduled ?? 0,
                        'icon' => 'event_available',
                        'icon_class' => 'bg-blue-50 text-blue-600',
                        'value_class' => 'text-blue-600',
                        'active_class' => 'border-blue-300 ring-2 ring-blue-100',
                    ],
                    [
                        'key' => 'pending_verification',
                        'label' => 'Physical/Site Visit Pending',
                        'value' => $statusStats->pending_verification ?? 0,
                        'icon' => 'fact_check',
                        'icon_class' => 'bg-orange-50 text-orange-600',
                        'value_class' => 'text-orange-600',
                        'active_class' => 'border-orange-300 ring-2 ring-orange-100',
                    ],
                    [
                        'key' => 'possession_pending',
                        'label' => 'Document Verification',
                        'value' => $statusStats->possession_pending ?? 0,
                        'icon' => 'key_off',
                        'icon_class' => 'bg-rose-50 text-rose-600',
                        'value_class' => 'text-rose-600',
                        'active_class' => 'border-rose-300 ring-2 ring-rose-100',
                    ],
                    [
                        'key' => 'verified',
                        'label' => 'Possession Given',
                        'value' => $statusStats->verified ?? 0,
                        'icon' => 'verified',
                        'icon_class' => 'bg-emerald-50 text-emerald-600',
                        'value_class' => 'text-emerald-600',
                        'active_class' => 'border-emerald-300 ring-2 ring-emerald-100',
                    ],
                ];
            @endphp

            {{-- Status cards --}}
            <section class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
                @foreach ($cards as $card)
                    @php
                        $query = array_filter(
                            array_merge(request()->except('page', 'status'), ['status' => $card['key']]),
                        );
                        $active = ($filters['status'] ?? '') === $card['key'];
                    @endphp

                    <a href="{{ route('physical.possession.index', $query) }}"
                        data-card-name="{{ $card['label'] }}"
                        class="possession-orbit-card group rounded-xl border bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
                           {{ $active ? $card['active_class'] : 'border-slate-200' }}">
                        <div class="flex items-start justify-between">
                            <span
                                class="material-symbols-outlined flex h-9 w-9 items-center justify-center rounded-lg
                                     text-[19px] {{ $card['icon_class'] }}">
                                {{ $card['icon'] }}
                            </span>
                            <span
                                class="material-symbols-outlined text-[16px] text-slate-300 group-hover:text-slate-500">arrow_outward</span>
                        </div>
                        <p class="mt-3 text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ $card['label'] }}
                        </p>
                        <p class="mt-1 text-2xl font-bold {{ $card['value_class'] }}">{{ number_format($card['value']) }}
                        </p>
                    </a>
                @endforeach
            </section>

            {{-- Table --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Possession Applications</h2>
                        <p class="mt-0.5 text-[11px] text-slate-500">
                            {{ number_format($applications->total()) }} filtered records
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1120px] text-left">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">ID / Application</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Property</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3 text-right">Received</th>
                                <th class="px-4 py-3">Schedule</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($applications as $application)
                                @php
                                    $statusStyles = [
                                        'awaiting_schedule' => 'bg-amber-50 text-amber-700',
                                        'scheduled' => 'bg-blue-50 text-blue-700',
                                        'pending_verification' => 'bg-orange-50 text-orange-700',
                                        'possession_pending' => 'bg-rose-50 text-rose-700',
                                        'verified' => 'bg-emerald-50 text-emerald-700',
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-slate-800">
                                            {{ $application->possession_id ?: 'Asset #' . $application->asset_id }}
                                        </p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            App: {{ $application->application_number ?: '-' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-slate-800">{{ $application->applicant_name ?: '-' }}
                                        </p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">{{ $application->mobile ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-700">{{ $application->asset_name ?: '-' }}</p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            Asset #{{ $application->asset_id }} · {{ $application->asset_size }}
                                            {{ $application->asset_unit }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $application->district_name ?: '-' }}
                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $application->city_name }} / {{ $application->sector_name }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <p class="font-bold text-emerald-600">
                                            ₹{{ number_format($application->received_amount ?? 0, 2) }}
                                        </p>
                                        <p class="mt-0.5 text-[9px] text-slate-400">
                                            Initial + cash receipts
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-slate-700">
                                            {{ $application->possession_date ? \Carbon\Carbon::parse($application->possession_date)->format('d-m-Y') : '-' }}
                                        </p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $application->meeting_slot ?: 'No slot' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full px-2.5 py-1 text-[9px] font-bold uppercase {{ $statusStyles[$application->workflow_status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ str_replace('_', ' ', $application->workflow_status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('physical.possession.show', ['assetId' => $application->asset_id]) }}"
                                            class="inline-flex h-8 items-center gap-1 rounded-lg bg-indigo-50 px-3 text-[11px] font-semibold text-indigo-600 hover:bg-indigo-100">
                                            <span class="material-symbols-outlined text-[15px]">visibility</span>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-16 text-center text-sm text-slate-400">No eligible
                                        records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($applications->hasPages())
                    <div
                        class="flex flex-col gap-3 border-t border-slate-100 bg-white px-4 py-3
               sm:flex-row sm:items-center sm:justify-between">

                        {{-- Record information --}}
                        <p class="text-xs text-slate-500">
                            Showing

                            <span class="font-semibold text-slate-700">
                                {{ number_format($applications->firstItem()) }}
                            </span>

                            to

                            <span class="font-semibold text-slate-700">
                                {{ number_format($applications->lastItem()) }}
                            </span>

                            of

                            <span class="font-semibold text-slate-700">
                                {{ number_format($applications->total()) }}
                            </span>

                            records
                        </p>

                        {{-- Pagination controls --}}
                        <nav class="flex flex-wrap items-center gap-1" aria-label="Pagination">

                            {{-- Previous --}}
                            @if ($applications->onFirstPage())
                                <span
                                    class="inline-flex h-8 cursor-not-allowed items-center gap-1 rounded-lg
                           border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium
                           text-slate-300">

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>

                                    <span class="hidden sm:inline">Previous</span>
                                </span>
                            @else
                                <a href="{{ $applications->previousPageUrl() }}"
                                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200
                           bg-white px-2.5 text-xs font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_left
                                    </span>

                                    <span class="hidden sm:inline">Previous</span>
                                </a>
                            @endif

                            {{-- First page and left dots --}}
                            @if ($applications->currentPage() > 3)
                                <a href="{{ $applications->url(1) }}"
                                    class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg
                           border border-slate-200 bg-white px-2 text-xs font-medium
                           text-slate-600 transition hover:border-indigo-200
                           hover:bg-indigo-50 hover:text-indigo-600">
                                    1
                                </a>

                                @if ($applications->currentPage() > 4)
                                    <span
                                        class="inline-flex h-8 min-w-8 items-center justify-center text-xs text-slate-400">
                                        …
                                    </span>
                                @endif
                            @endif

                            {{-- Nearby page numbers --}}
                            @foreach ($applications->getUrlRange(max(1, $applications->currentPage() - 2), min($applications->lastPage(), $applications->currentPage() + 2)) as $page => $url)
                                @if ($page == $applications->currentPage())
                                    <span aria-current="page"
                                        class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg
                               bg-indigo-600 px-2 text-xs font-semibold text-white shadow-sm">

                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg
                               border border-slate-200 bg-white px-2 text-xs font-medium
                               text-slate-600 transition hover:border-indigo-200
                               hover:bg-indigo-50 hover:text-indigo-600">

                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Right dots and last page --}}
                            @if ($applications->currentPage() < $applications->lastPage() - 2)
                                @if ($applications->currentPage() < $applications->lastPage() - 3)
                                    <span
                                        class="inline-flex h-8 min-w-8 items-center justify-center text-xs text-slate-400">
                                        …
                                    </span>
                                @endif

                                <a href="{{ $applications->url($applications->lastPage()) }}"
                                    class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg
                           border border-slate-200 bg-white px-2 text-xs font-medium
                           text-slate-600 transition hover:border-indigo-200
                           hover:bg-indigo-50 hover:text-indigo-600">

                                    {{ $applications->lastPage() }}
                                </a>
                            @endif

                            {{-- Next --}}
                            @if ($applications->hasMorePages())
                                <a href="{{ $applications->nextPageUrl() }}"
                                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200
                           bg-white px-2.5 text-xs font-medium text-slate-600 transition
                           hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                    <span class="hidden sm:inline">Next</span>

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </a>
                            @else
                                <span
                                    class="inline-flex h-8 cursor-not-allowed items-center gap-1 rounded-lg
                           border border-slate-200 bg-slate-50 px-2.5 text-xs font-medium
                           text-slate-300">

                                    <span class="hidden sm:inline">Next</span>

                                    <span class="material-symbols-outlined text-[16px]">
                                        chevron_right
                                    </span>
                                </span>
                            @endif
                        </nav>
                    </div>
                @endif
            </section>
        </div>
    </main>

    <div id="possessionCardTooltip" role="tooltip" aria-hidden="true"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tooltip = document.getElementById('possessionCardTooltip');
            const cards = document.querySelectorAll('.possession-orbit-card[data-card-name]');

            if (!tooltip || !cards.length) return;

            const placeTooltip = (event) => {
                const padding = 16;
                const halfWidth = tooltip.offsetWidth / 2;
                const x = Math.min(
                    window.innerWidth - halfWidth - padding,
                    Math.max(halfWidth + padding, event.clientX)
                );

                tooltip.style.left = `${x}px`;
                tooltip.style.top = `${Math.max(48, event.clientY)}px`;
            };

            const showTooltip = (card, event = null) => {
                tooltip.textContent = card.dataset.cardName || '';
                tooltip.classList.add('is-visible');
                tooltip.setAttribute('aria-hidden', 'false');

                if (event) {
                    placeTooltip(event);
                    return;
                }

                const rect = card.getBoundingClientRect();
                tooltip.style.left = `${rect.left + (rect.width / 2)}px`;
                tooltip.style.top = `${Math.max(48, rect.top)}px`;
            };

            const hideTooltip = () => {
                tooltip.classList.remove('is-visible');
                tooltip.setAttribute('aria-hidden', 'true');
            };

            cards.forEach((card) => {
                card.addEventListener('mouseenter', (event) => showTooltip(card, event));
                card.addEventListener('mousemove', placeTooltip);
                card.addEventListener('mouseleave', hideTooltip);
                card.addEventListener('focusin', () => showTooltip(card));
                card.addEventListener('focusout', hideTooltip);
            });
        });
    </script>
@endsection