@extends('layouts.mmgayAdmin')

@section('title', 'Possession Applications')

@section('content')
    <main class="ml-[260px] min-h-screen w-[calc(100%-260px)] bg-slate-100 p-6 pt-20">

        @php
            $commonParams = array_filter(
                [
                    'phase' => $phase,
                    'district_id' => $districtId,
                    'block_id' => $blockId,
                    'village_id' => $villageId,
                    'per_page' => $perPage,
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
                    'filter' => 'document_verification',
                    'label' => 'Document Verification',
                    'count' => $counts['document_verification'] ?? 0,
                    'icon' => 'description',
                    'text' => 'text-amber-700',
                    'iconBox' => 'bg-amber-50 text-amber-600',
                ],
                [
                    'filter' => 'verified',
                    'label' => 'Possession Given',
                    'count' => $counts['verified'] ?? 0,
                    'icon' => 'verified',
                    'text' => 'text-emerald-700',
                    'iconBox' => 'bg-emerald-50 text-emerald-600',
                ],
            ];

            $exportParams = array_merge(['filter' => $filter], $commonParams);
        @endphp

        <section class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div
                class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-slate-800">
                        Filter Possession Applications
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Filter records by phase, district, block and village
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.possession.export.csv', $exportParams) }}"
                        class="inline-flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        <span class="material-symbols-outlined text-[18px]">
                            download
                        </span>

                        CSV
                    </a>

                    <a href="{{ route('admin.possession.print', array_merge(['filter' => $filter], $commonParams)) }}"
                        target="_blank"
                        class="inline-flex h-10 items-center gap-2 rounded-xl bg-slate-800 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900">
                        <span class="material-symbols-outlined text-[18px]">
                            print
                        </span>

                        Print
                    </a>
                </div>
            </div>

            <form id="possessionFilterForm" method="GET" action="{{ route('admin.possession.list', ['filter' => $filter]) }}" class="p-4">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-12">

                    {{-- Phase --}}
                    <div class="xl:col-span-2">
                        <label for="possessionPhase"
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            Phase
                        </label>

                        <select id="possessionPhase" name="phase"
                            class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">
                                All Phases
                            </option>

                            @foreach ([1, 2, 3, 4] as $phaseOption)
                                <option value="{{ $phaseOption }}"
                                    {{ (string) $phase === (string) $phaseOption ? 'selected' : '' }}>
                                    Phase {{ $phaseOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- District --}}
                    <div class="xl:col-span-3">
                        <label for="possessionDistrict"
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            District
                        </label>

                        <select id="possessionDistrict" name="district_id"
                            class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">
                                All Districts
                            </option>

                            @foreach ($districts as $district)
                                <option value="{{ $district->DistrictId }}"
                                    {{ (string) $districtId === (string) $district->DistrictId ? 'selected' : '' }}>
                                    {{ $district->DistrictName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Block --}}
                    <div class="xl:col-span-2">
                        <label for="possessionBlock"
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            Block
                        </label>

                        <select id="possessionBlock" name="block_id"
                            class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">
                                All Blocks
                            </option>

                            @foreach ($blocks as $block)
                                <option value="{{ $block->BlockId }}"
                                    {{ (string) $blockId === (string) $block->BlockId ? 'selected' : '' }}>
                                    {{ $block->BlockName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Village --}}
                    <div class="xl:col-span-2">
                        <label for="possessionVillage"
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            Village
                        </label>

                        <select id="possessionVillage" name="village_id"
                            class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">
                                All Villages
                            </option>

                            @foreach ($villages as $village)
                                <option value="{{ $village->VillageId }}"
                                    {{ (string) $villageId === (string) $village->VillageId ? 'selected' : '' }}>
                                    {{ $village->VillageName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Per Page --}}
                    <div class="xl:col-span-1">
                        <label for="possessionPerPage"
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            Per Page
                        </label>

                        <select id="possessionPerPage" name="per_page"
                            class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            @foreach ([20, 50, 100, 200] as $size)
                                <option value="{{ $size }}" {{ (int) $perPage === $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end gap-2 xl:col-span-2">
                        <button id="possessionApplyButton" type="submit"
                            class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                            <span class="material-symbols-outlined text-[18px]">
                                filter_alt
                            </span>

                            Apply
                        </button>

                        <a href="{{ route('admin.possession.list', ['filter' => $filter]) }}" title="Reset filters"
                            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-600 transition hover:bg-red-50 hover:text-red-600">
                            <span class="material-symbols-outlined text-[20px]">
                                restart_alt
                            </span>
                        </a>
                    </div>

                </div>
            </form>

            {{-- Active Filters --}}
            <div
                class="flex flex-col gap-2 border-t border-slate-200 bg-slate-50/70 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="material-symbols-outlined text-[17px] text-blue-500">
                        info
                    </span>

                    Export uses the selected status, phase, district, block and village filters.
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">

                    <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">
                        {{ $filterLabels[$filter] ?? 'Total Eligible' }}
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                        {{ $phase ? 'Phase ' . $phase : 'All Phases' }}
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                        @if ($districtId)
                            {{ optional($districts->firstWhere('DistrictId', $districtId))->DistrictName ?? 'Selected District' }}
                        @else
                            All Districts
                        @endif
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                        @if ($blockId)
                            {{ optional($blocks->firstWhere('BlockId', $blockId))->BlockName ?? 'Selected Block' }}
                        @else
                            All Blocks
                        @endif
                    </span>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
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
                <a href="{{ route('admin.possession.list', array_merge(['filter' => $card['filter']], $commonParams)) }}"
                    class="rounded-2xl border bg-white px-4 py-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
                {{ $filter === $card['filter'] ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200' }}">
                    <div class="flex items-center justify-between gap-3">

                        <div>
                            <p class="text-xs font-semibold uppercase leading-5 tracking-wide text-slate-400">
                                {{ $card['label'] }}
                            </p>

                            <p class="mt-2 text-2xl font-bold {{ $card['text'] }}">
                                {{ number_format($card['count']) }}
                            </p>
                        </div>

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $card['iconBox'] }}">
                            <span class="material-symbols-outlined text-[22px]">
                                {{ $card['icon'] }}
                            </span>
                        </div>

                    </div>
                </a>
            @endforeach
        </section>

        <section style="margin-top: 20px;" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-bold text-slate-800">
                    {{ $filterLabels[$filter] ?? 'Applications' }}
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">

                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Applicant</th>
                            <th class="px-4 py-3 text-left">Application No.</th>
                            <th class="px-4 py-3 text-left">Village / Phase</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Visit / Meeting</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($applications as $application)
                            @php
                                $status = trim((string) ($application->physical_possession_status ?? ''));
                            @endphp

                            <tr class="hover:bg-blue-50/60">

                                <td class="px-4 py-3 text-slate-500">
                                    {{ $applications->firstItem() + $loop->index }}
                                </td>

                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-800">
                                        {{ $application->OwnerName ?? '-' }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $application->MobileNo ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-700">
                                        {{ $application->application_number ?? 'Not Created' }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Owner ID: {{ $application->OwnerId }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-700">
                                        {{ $application->VillageName ?? '-' }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Phase {{ $application->Phase ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                        {{ $status !== '' ? $status : 'Schedule Pending' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $application->meeting_slot ?? ($application->citizen_visit_date ?? ($application->possession_date ?? '-')) }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($application->secure_id)
                                        <a href="{{ route('admin.possession.show', $application->secure_id) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
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
                <div class="border-t border-slate-200 bg-white px-6 py-4">
                    <div class="flex flex-col items-center justify-between gap-4 md:flex-row">

                        <div class="text-sm text-slate-600">
                            
                        </div>

                        {{ $applications->onEachSide(1)->links('pagination::tailwind') }}

                    </div>
                </div>
            @endif

        </section>

    </main>
@endsection
