@extends('layouts.mmgayCEOAuth')

@section('title', 'Possession Applications')

@section('content')
    <main class="mt-[68px] min-h-screen bg-slate-50 p-4 lg:ml-[230px] lg:w-[calc(100%-230px)] lg:p-5">

        @php
            $commonParams = array_filter(
                [
                    'phase' => $phase ?? 'all',
                    'village_id' => $villageId ?? null,
                ],
                static fn($value) => $value !== null && $value !== '',
            );

            $cards = [
                [
                    'filter' => 'all',
                    'label' => 'Total Eligible',
                    'count' => $counts['all'] ?? 0,
                    'icon' => 'groups',
                    'text' => 'text-slate-800',
                    'iconBox' => 'bg-slate-100 text-slate-600',
                ],
                [
                    'filter' => 'schedule_pending',
                    'label' => 'Schedule Pending',
                    'count' => $counts['schedule_pending'] ?? 0,
                    'icon' => 'pending_actions',
                    'text' => 'text-blue-700',
                    'iconBox' => 'bg-blue-50 text-blue-600',
                ],
                [
                    'filter' => 'awaiting_citizen',
                    'label' => 'Confirmation Pending from Citizen',
                    'count' => $counts['awaiting_citizen'] ?? 0,
                    'icon' => 'contact_support',
                    'text' => 'text-orange-700',
                    'iconBox' => 'bg-orange-50 text-orange-600',
                ],
                [
                    'filter' => 'field_visit_pending',
                    'label' => 'Physical/Site Visit Pending',
                    'count' => $counts['field_visit_pending'] ?? 0,
                    'icon' => 'location_on',
                    'text' => 'text-indigo-700',
                    'iconBox' => 'bg-indigo-50 text-indigo-600',
                ],
                [
                    'filter' => 'possession_pending',
                    'label' => 'Document Verification',
                    'count' => $counts['possession_pending'] ?? 0,
                    'icon' => 'description',
                    'text' => 'text-amber-700',
                    'iconBox' => 'bg-amber-50 text-amber-600',
                ],
                [
                    'filter' => 'verified',
                    'label' => 'Pessession Given',
                    'count' => $counts['verified'] ?? 0,
                    'icon' => 'verified',
                    'text' => 'text-emerald-700',
                    'iconBox' => 'bg-emerald-50 text-emerald-600',
                ],
            ];
        @endphp

        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">
                    Possession Applications
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $filterLabels[$filter] ?? 'Total Eligible' }}
                </p>
            </div>

            <a href="{{ route('district.dashboard', ['phase' => $phase, 'village_id' => $villageId]) }}"
                class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">
                <span class="material-symbols-outlined text-[18px]">
                    arrow_back
                </span>
                Back to Dashboard
            </a>
        </div>

        @php
            $exportParams = array_filter(
                [
                    'filter' => $filter,
                    'phase' => $phase,
                    'village_id' => $villageId,
                ],
                static fn($value) => $value !== null && $value !== '',
            );

            $printParams = array_merge($exportParams, [
                'print' => 1,
                'per_page' => 200,
            ]);
        @endphp

        <section class="no-print mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div
                class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        Filter Possession Applications
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Filter records by phase and village
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">

                    <a href="{{ route('district.possession.export.csv', $exportParams) }}"
                        class="inline-flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        <span class="material-symbols-outlined text-[18px]">
                            download
                        </span>

                        CSV
                    </a>

                    <a href="{{ route('district.possession.list', array_merge(['filter' => $filter], $printParams)) }}"
                        target="_blank"
                        class="inline-flex h-10 items-center gap-2 rounded-xl bg-slate-800 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900">
                        <span class="material-symbols-outlined text-[18px]">
                            print
                        </span>

                        Print
                    </a>

                </div>
            </div>

            <form method="GET" action="{{ route('district.possession.list', ['filter' => $filter]) }}" class="p-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-[1fr_1.5fr_150px_auto]">

                    {{-- Phase --}}
                    <div>
                        <label for="possessionPhase"
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            Phase
                        </label>

                        <div class="relative">
                            <span
                                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                layers
                            </span>

                            <select id="possessionPhase" name="phase"
                                class="h-11 w-full appearance-none rounded-xl border border-slate-300 bg-white pl-10 pr-10 text-sm font-medium text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                <option value="all" {{ $phase === 'all' ? 'selected' : '' }}>
                                    All Phases
                                </option>

                                <option value="1" {{ (string) $phase === '1' ? 'selected' : '' }}>
                                    Phase 1
                                </option>

                                <option value="2" {{ (string) $phase === '2' ? 'selected' : '' }}>
                                    Phase 2
                                </option>

                                <option value="3" {{ (string) $phase === '3' ? 'selected' : '' }}>
                                    Phase 3
                                </option>
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                expand_more
                            </span>
                        </div>
                    </div>

                    {{-- Village --}}
                    <div>
                        <label for="possessionVillage"
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            Village
                        </label>

                        <div class="relative">
                            <span
                                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                holiday_village
                            </span>

                            <select id="possessionVillage" name="village_id"
                                class="h-11 w-full appearance-none rounded-xl border border-slate-300 bg-white pl-10 pr-10 text-sm font-medium text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                <option value="">
                                    All Villages
                                </option>

                                @foreach ($villages as $village)
                                    <option value="{{ $village->VillageId }}"
                                        {{ (string) $villageId === (string) $village->VillageId ? 'selected' : '' }}>
                                        {{ $village->VillageName }}

                                        @if ($phase === 'all')
                                            (Phase {{ $village->phase }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                expand_more
                            </span>
                        </div>
                    </div>

                    {{-- Per Page --}}
                    <div>
                        <label for="possessionPerPage"
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            Per Page
                        </label>

                        <div class="relative">
                            <select id="possessionPerPage" name="per_page"
                                class="h-11 w-full appearance-none rounded-xl border border-slate-300 bg-white px-3 pr-9 text-sm font-medium text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                @foreach ([20, 50, 100, 200] as $pageSize)
                                    <option value="{{ $pageSize }}"
                                        {{ (int) $perPage === $pageSize ? 'selected' : '' }}>
                                        {{ $pageSize }}
                                    </option>
                                @endforeach
                            </select>

                            <span
                                class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">
                                expand_more
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="inline-flex h-11 min-w-[110px] flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:ring-4 focus:ring-blue-200">
                            <span class="material-symbols-outlined text-[18px]">
                                filter_alt
                            </span>

                            Apply
                        </button>

                        <a href="{{ route('district.possession.list', [
                            'filter' => $filter,
                            'phase' => 'all',
                        ]) }}"
                            title="Reset Filters"
                            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                            <span class="material-symbols-outlined text-[20px]">
                                restart_alt
                            </span>
                        </a>
                    </div>

                </div>
            </form>

            <div
                class="flex flex-col gap-2 border-t border-slate-200 bg-slate-50/70 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="material-symbols-outlined text-[17px] text-blue-500">
                        info
                    </span>

                    Export uses the selected status, phase and village filters.
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-600">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">
                        {{ $filterLabels[$filter] ?? 'Total Eligible' }}
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1">
                        {{ $phase === 'all' ? 'All Phases' : 'Phase ' . $phase }}
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1">
                        @if ($villageId)
                            {{ optional($villages->firstWhere('VillageId', $villageId))->VillageName ?? 'Selected Village' }}
                        @else
                            All Villages
                        @endif
                    </span>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
            @foreach ($cards as $card)
                <a href="{{ route('district.possession.list', array_merge(['filter' => $card['filter']], $commonParams)) }}"
                    class="rounded-2xl border bg-white px-4 py-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
                        {{ $filter === $card['filter'] ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                {{ $card['label'] }}
                            </p>

                            <p class="mt-2 text-2xl font-bold {{ $card['text'] }}">
                                {{ number_format($card['count']) }}
                            </p>
                        </div>

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $card['iconBox'] }}">
                            <span class="material-symbols-outlined text-[21px]">
                                {{ $card['icon'] }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </section>

        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-bold text-slate-800">
                    {{ $filterLabels[$filter] ?? 'Applications' }}
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Only Registry Matched beneficiaries included in “Possession to be given” are shown.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">Applicant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">Application No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">
                                Registry No.
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">Village / Phase</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">Possession Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">Visit / Meeting</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($applications as $application)
                            @php
                                $physicalStatus = trim((string) ($application->physical_possession_status ?? ''));

                                $statusClass = match (strtolower($physicalStatus)) {
                                    'verified' => 'bg-emerald-100 text-emerald-700',
                                    'visit scheduled' => 'bg-orange-100 text-orange-700',
                                    'slot selected' => 'bg-indigo-100 text-indigo-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp

                            <tr class="hover:bg-blue-50/60">
                                <td class="whitespace-nowrap px-4 py-3 text-slate-500">
                                    {{ $applications->firstItem() + $loop->index }}
                                </td>

                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-800">
                                        {{ $application->OwnerName ?? '-' }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ $application->MobileNo ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-700">
                                        {{ $application->application_number ?? 'Not Created' }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Owner ID: {{ $application->OwnerId }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-700">
                                        {{ $application->RegistaryNumber ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-700">
                                        {{ $application->VillageName ?? '-' }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Phase {{ $application->Phase ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                        {{ $physicalStatus !== '' ? $physicalStatus : 'Schedule Pending' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $application->meeting_slot ?? ($application->citizen_visit_date ?? ($application->possession_date ?? '-')) }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    @if ($application->secure_id)
                                        <a href="{{ route('district.possession.show', $application->secure_id) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                            <span class="material-symbols-outlined text-[16px]">
                                                visibility
                                            </span>

                                            View
                                        </a>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">
                                            Not Scheduled
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center text-slate-500">
                                    No possession applications found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($applications->hasPages())
                <div
                    class="no-print flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    {{-- Result Information --}}
                    <p class="text-sm text-slate-600">
                        Showing

                        <span class="font-bold text-slate-800">
                            {{ $applications->firstItem() ?? 0 }}
                        </span>

                        to

                        <span class="font-bold text-slate-800">
                            {{ $applications->lastItem() ?? 0 }}
                        </span>

                        of

                        <span class="font-bold text-slate-800">
                            {{ number_format($applications->total()) }}
                        </span>

                        results
                    </p>

                    {{-- Pagination --}}
                    <nav class="flex flex-wrap items-center gap-1.5" aria-label="Pagination">
                        {{-- Previous --}}
                        @if ($applications->onFirstPage())
                            <span
                                class="inline-flex h-9 items-center gap-1 rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-400">
                                <span class="material-symbols-outlined text-[18px]">
                                    chevron_left
                                </span>

                                Previous
                            </span>
                        @else
                            <a href="{{ $applications->previousPageUrl() }}"
                                class="inline-flex h-9 items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                <span class="material-symbols-outlined text-[18px]">
                                    chevron_left
                                </span>

                                Previous
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $applications->currentPage();
                            $lastPage = $applications->lastPage();

                            $startPage = max(1, $currentPage - 2);

                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @if ($startPage > 1)
                            <a href="{{ $applications->url(1) }}"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                1
                            </a>

                            @if ($startPage > 2)
                                <span
                                    class="inline-flex h-9 min-w-9 items-center justify-center text-sm font-semibold text-slate-400">
                                    ...
                                </span>
                            @endif
                        @endif

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            @if ($page === $currentPage)
                                <span
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-blue-600 bg-blue-600 px-3 text-sm font-bold text-white shadow-sm"
                                    aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $applications->url($page) }}"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span
                                    class="inline-flex h-9 min-w-9 items-center justify-center text-sm font-semibold text-slate-400">
                                    ...
                                </span>
                            @endif

                            <a href="{{ $applications->url($lastPage) }}"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                {{ $lastPage }}
                            </a>
                        @endif

                        {{-- Next --}}
                        @if ($applications->hasMorePages())
                            <a href="{{ $applications->nextPageUrl() }}"
                                class="inline-flex h-9 items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                Next

                                <span class="material-symbols-outlined text-[18px]">
                                    chevron_right
                                </span>
                            </a>
                        @else
                            <span
                                class="inline-flex h-9 items-center gap-1 rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-400">
                                Next

                                <span class="material-symbols-outlined text-[18px]">
                                    chevron_right
                                </span>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </section>
    </main>

    @if ($isPrint)
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 400);
            });
        </script>
    @endif

    <style>
        @media print {

            header,
            aside,
            nav,
            .no-print {
                display: none !important;
            }

            body {
                background: #ffffff !important;
            }

            main {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            table {
                width: 100% !important;
                font-size: 11px !important;
            }

            thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
            }

            a {
                color: inherit !important;
                text-decoration: none !important;
            }
        }
    </style>
@endsection
