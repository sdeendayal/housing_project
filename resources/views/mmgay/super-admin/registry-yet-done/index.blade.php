@extends('layouts.mmgayAdmin')

@section('title', 'Registry Yet To Be Done')

@section('content')

<style>
    .ryt-page {
        min-height: calc(100vh - 64px);
        background:
            radial-gradient(circle at 94% 3%, rgba(245,158,11,.08), transparent 25rem),
            radial-gradient(circle at 5% 35%, rgba(59,130,246,.05), transparent 22rem),
            #f1f5f9;
    }

    .ryt-card {
        border: 1px solid #e2e8f0;
        background: rgba(255,255,255,.97);
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(15,23,42,.055);
    }

    .ryt-header {
        position: relative;
        overflow: hidden;
    }

    .ryt-header:after {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        right: -110px;
        top: -160px;
        border-radius: 999px;
        background: rgba(245,158,11,.07);
        pointer-events: none;
    }

    .ryt-title-icon {
        width: 52px;
        height: 52px;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        color: #d97706;
        background: #fffbeb;
        border: 1px solid #fde68a;
    }

    .ryt-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .ryt-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #f59e0b;
        box-shadow: 0 0 0 3px #fef3c7;
    }

    .ryt-filter {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 15px;
        padding: 12px;
        transition: .18s ease;
    }

    .ryt-filter:focus-within {
        border-color: #cbd5e1;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(245,158,11,.08);
    }

    .ryt-filter label {
        display: block;
        margin-bottom: 7px;
        font-size: 10px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .ryt-row {
        display: flex;
        align-items: center;
        gap: 9px;
        position: relative;
    }

    .ryt-icon {
        width: 34px;
        height: 34px;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #ffedd5;
    }

    .ryt-icon svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ryt-control {
        width: 100%;
        height: 42px;
        border: 0;
        outline: 0;
        background: transparent;
        color: #0f172a;
        font-size: 13px;
        font-weight: 600;
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
    }

    .ryt-control::placeholder {
        color: #94a3b8;
        font-weight: 500;
    }

    .ryt-arrow {
        position: absolute;
        right: 1px;
        top: 50%;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        pointer-events: none;
    }

    .ryt-arrow svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ryt-control:hover + .ryt-arrow {
        color: #d97706;
    }

    .ryt-btn {
        height: 46px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
        transition: .16s ease;
    }

    .ryt-btn:hover {
        transform: translateY(-1px);
    }

    .ryt-apply {
        color: #fff;
        background: linear-gradient(135deg, #ea580c, #f97316);
        box-shadow: 0 7px 18px rgba(234,88,12,.17);
    }

    .ryt-reset {
        color: #334155;
        background: #fff;
        border: 1px solid #cbd5e1;
    }

    .ryt-print {
        color: #fff;
        background: linear-gradient(135deg, #dc2626, #ef4444);
        box-shadow: 0 7px 18px rgba(220,38,38,.15);
    }

    .ryt-csv {
        color: #fff;
        background: linear-gradient(135deg, #047857, #10b981);
        box-shadow: 0 7px 18px rgba(16,185,129,.15);
    }

    .ryt-summary {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 12px;
        background: #fffbeb;
        border: 1px solid #fde68a;
    }

    .ryt-summary-dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: #f59e0b;
        box-shadow: 0 0 0 4px #fef3c7;
    }

    .ryt-table-wrap {
        overflow-x: auto;
        scrollbar-width: thin;
    }

    .ryt-table {
        width: 100%;
        min-width: 1000px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .ryt-table th {
        padding: 13px 15px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 9px;
        line-height: 1.2;
        font-weight: 900;
        letter-spacing: .07em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    .ryt-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
        font-size: 12px;
    }

    .ryt-table tbody tr:hover {
        background: #fffdf7;
    }

    .ryt-number {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
    }

    .ryt-avatar {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #fff7ed;
        color: #c2410c;
        font-size: 12px;
        font-weight: 900;
    }

    .ryt-pending {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 999px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #c2410c;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .ryt-empty {
        padding: 64px 20px;
        text-align: center;
    }

    .ryt-empty-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #f8fafc;
        color: #94a3b8;
    }

    @media (max-width: 900px) {
        .ryt-main {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 16px !important;
        }
    }

    @media print {
        .ryt-no-print {
            display: none !important;
        }

        .ryt-page {
            background: #fff !important;
        }

        .ryt-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        .ryt-table {
            min-width: 0 !important;
        }
    }
</style>

<main class="ryt-page ryt-main min-h-screen p-5 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

    {{-- Header --}}
    <section class="ryt-card ryt-header mb-5 p-6 lg:p-7">

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-4">

                <div class="ryt-title-icon">
                    <span class="material-symbols-outlined text-[27px]">
                        pending_actions
                    </span>
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2">

                        <h1 class="text-2xl font-black tracking-tight text-slate-900">
                            Registry Yet To Be Done
                        </h1>

                        <span class="ryt-status">
                            <span class="ryt-status-dot"></span>
                            Pending
                        </span>

                    </div>

                    <p class="mt-1 text-sm text-slate-500">
                        Applicants whose registry is still pending.
                    </p>
                </div>

            </div>


            <div class="ryt-no-print flex flex-wrap gap-2">

                <a href="{{ route('superadmin.registry_yet_done.print', request()->query()) }}"
                    target="_blank"
                    class="ryt-btn ryt-print">

                    <span class="material-symbols-outlined text-[18px]">
                        print
                    </span>

                    Print
                </a>

                <a href="{{ route('superadmin.registry_yet_done.csv', request()->query()) }}"
                    class="ryt-btn ryt-csv">

                    <span class="material-symbols-outlined text-[18px]">
                        download
                    </span>

                    CSV
                </a>

            </div>

        </div>

    </section>


    {{-- Filters --}}
    <section class="ryt-card ryt-no-print mb-5 p-5 lg:p-6">

        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-base font-black text-slate-900">
                    Filter Pending Registry
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Filter the list by phase, district, block, village or applicant details.
                </p>
            </div>

            <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                Pending records
            </div>

        </div>


        <form method="GET"
            action="{{ route('superadmin.registry_yet_done.index') }}"
            class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-6">


            {{-- Phase --}}
            <div class="ryt-filter">

                <label for="phase">Phase</label>

                <div class="ryt-row">

                    <span class="ryt-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 6h16M7 12h10M10 18h4"/>
                        </svg>
                    </span>

                    <select name="phase" id="phase" class="ryt-control">
                        <option value="">All Phases</option>

                        @foreach ($phases as $phase)
                            <option value="{{ $phase }}"
                                @selected(request('phase') == $phase)>
                                Phase {{ $phase }}
                            </option>
                        @endforeach
                    </select>

                    <span class="ryt-arrow">
                        <svg viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </span>

                </div>

            </div>


            {{-- District --}}
            <div class="ryt-filter">

                <label for="district_id">District</label>

                <div class="ryt-row">

                    <span class="ryt-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 21h16M6 21V8l6-4 6 4v13"/>
                            <path d="M9 11h1M14 11h1M9 15h1M14 15h1"/>
                        </svg>
                    </span>

                    <select name="district_id" id="district_id" class="ryt-control">
                        <option value="">All Districts</option>

                        @foreach ($districts as $district)
                            <option value="{{ $district->DistrictId }}"
                                @selected(request('district_id') == $district->DistrictId)>
                                {{ $district->DistrictName }}
                            </option>
                        @endforeach
                    </select>

                    <span class="ryt-arrow">
                        <svg viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </span>

                </div>

            </div>


            {{-- Block --}}
            <div class="ryt-filter">

                <label for="block_id">Block</label>

                <div class="ryt-row">

                    <span class="ryt-icon">
                        <svg viewBox="0 0 24 24">
                            <rect x="4" y="4" width="6" height="6"/>
                            <rect x="14" y="4" width="6" height="6"/>
                            <rect x="4" y="14" width="6" height="6"/>
                            <rect x="14" y="14" width="6" height="6"/>
                        </svg>
                    </span>

                    <select name="block_id" id="block_id" class="ryt-control">
                        <option value="">All Blocks</option>

                        @foreach ($blocks as $block)
                            <option value="{{ $block->BlockId }}"
                                @selected(request('block_id') == $block->BlockId)>
                                {{ $block->BlockName }}
                            </option>
                        @endforeach
                    </select>

                    <span class="ryt-arrow">
                        <svg viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </span>

                </div>

            </div>


            {{-- Village --}}
            <div class="ryt-filter">

                <label for="village_id">Village</label>

                <div class="ryt-row">

                    <span class="ryt-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 10.5 12 4l9 6.5"/>
                            <path d="M5.5 9.5V21h13V9.5"/>
                            <path d="M9.5 21v-6h5v6"/>
                        </svg>
                    </span>

                    <select name="village_id" id="village_id" class="ryt-control">
                        <option value="">All Villages</option>

                        @foreach ($villages as $village)
                            <option value="{{ $village->VillageId }}"
                                @selected(request('village_id') == $village->VillageId)>
                                {{ $village->VillageName }}
                            </option>
                        @endforeach
                    </select>

                    <span class="ryt-arrow">
                        <svg viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </span>

                </div>

            </div>


            {{-- Search --}}
            <div class="ryt-filter">

                <label for="search">Search</label>

                <div class="ryt-row">

                    <span class="ryt-icon">
                        <svg viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-4-4"/>
                        </svg>
                    </span>

                    <input type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Name, mobile, application..."
                        class="ryt-control cursor-text">

                </div>

            </div>


            {{-- Buttons --}}
            <div class="flex items-end gap-2">

                <button type="submit" class="ryt-btn ryt-apply flex-1">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    Apply
                </button>

                <a href="{{ route('superadmin.registry_yet_done.index') }}"
                    class="ryt-btn ryt-reset">

                    <span class="material-symbols-outlined text-[18px]">
                        restart_alt
                    </span>

                    Reset
                </a>

            </div>

        </form>

    </section>


    {{-- Results --}}
    <section class="ryt-card overflow-hidden">

        <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <div class="flex items-center gap-2">

                    <h2 class="text-lg font-black text-slate-900">
                        Registry Pending Applicants
                    </h2>

                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-extrabold text-amber-700 ring-1 ring-inset ring-amber-100">
                        Yet To Be Done
                    </span>

                </div>

                <p class="mt-1 text-xs text-slate-500">
                    Showing
                    <span class="font-bold text-slate-700">{{ $registryYetDone->firstItem() ?? 0 }}</span>
                    –
                    <span class="font-bold text-slate-700">{{ $registryYetDone->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-bold text-slate-700">{{ number_format($registryYetDone->total()) }}</span>
                    records
                </p>

            </div>


            <div class="ryt-summary">

                <span class="ryt-summary-dot"></span>

                <div>
                    <div class="text-[9px] font-extrabold uppercase tracking-wider text-amber-600">
                        Registry pending
                    </div>

                    <div class="text-sm font-black text-slate-800">
                        {{ number_format($registryYetDone->total()) }}
                    </div>
                </div>

            </div>

        </div>


        <div class="ryt-table-wrap">

            <table class="ryt-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Application</th>
                        <th>Applicant</th>
                        <th>Mobile</th>
                        <th>Location</th>
                        <th>Phase</th>
                        <th>Flat</th>
                        <th>Status</th>
                    </tr>
                </thead>


                <tbody>

                    @forelse ($registryYetDone as $row)

                        @php
                            $initial = strtoupper(substr(trim($row->OwnerName ?? 'A'), 0, 1));
                        @endphp

                        <tr>

                            <td>
                                <span class="ryt-number">
                                    {{ ($registryYetDone->firstItem() ?? 1) + $loop->index }}
                                </span>
                            </td>


                            <td>
                                <div class="font-extrabold text-slate-800">
                                    {{ $row->RegistrationNo ?? '-' }}
                                </div>

                                <div class="mt-1 text-[10px] text-slate-400">
                                    Owner ID:
                                    <span class="font-bold text-slate-500">
                                        {{ $row->OwnerId ?? '-' }}
                                    </span>
                                </div>
                            </td>


                            <td>

                                <div class="flex items-center gap-3">

                                    <span class="ryt-avatar">
                                        {{ $initial }}
                                    </span>

                                    <div class="min-w-0">

                                        <div class="truncate font-extrabold text-slate-800">
                                            {{ $row->OwnerName ?? '-' }}
                                        </div>

                                        <div class="mt-1 truncate text-[10px] text-slate-400">
                                            {{ $row->FatherHusbandName ?? '-' }}
                                        </div>

                                    </div>

                                </div>

                            </td>


                            <td>
                                <div class="font-bold text-slate-700">
                                    {{ $row->MobileNo ?? '-' }}
                                </div>
                            </td>


                            <td>

                                <div class="font-bold text-slate-700">
                                    {{ $row->VillageName ?? '-' }}
                                </div>

                                <div class="mt-1 text-[10px] text-slate-400">
                                    {{ $row->BlockName ?? '-' }}
                                    <span class="mx-1">•</span>
                                    {{ $row->DistrictName ?? '-' }}
                                </div>

                            </td>


                            <td>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-extrabold text-slate-600">
                                    {{ $row->Phase ?? '-' }}
                                </span>
                            </td>


                            <td>

                                <div class="font-extrabold text-slate-800">
                                    {{ $row->FlatNo ?? '-' }}
                                </div>

                                <div class="mt-1 text-[10px] text-slate-400">
                                    ID: {{ $row->FlatId ?? '-' }}
                                </div>

                            </td>


                            <td>

                                <span class="ryt-pending">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Pending
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8">

                                <div class="ryt-empty">

                                    <div class="ryt-empty-icon">
                                        <span class="material-symbols-outlined text-[30px]">
                                            task_alt
                                        </span>
                                    </div>

                                    <h3 class="mt-4 text-sm font-black text-slate-700">
                                        No pending registry records found
                                    </h3>

                                    <p class="mx-auto mt-1 max-w-md text-xs text-slate-400">
                                        All matching applicants may already have their registry completed.
                                    </p>

                                    <a href="{{ route('superadmin.registry_yet_done.index') }}"
                                        class="ryt-btn ryt-reset ryt-no-print mt-5">

                                        <span class="material-symbols-outlined text-[17px]">
                                            restart_alt
                                        </span>

                                        Clear Filters
                                    </a>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($registryYetDone->hasPages())

            <div class="ryt-no-print border-t border-slate-200 px-5 py-4 sm:px-6">
                {{ $registryYetDone->onEachSide(1)->links('pagination::tailwind') }}
            </div>

        @endif

    </section>

</main>


{{-- Dependent Filters --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const phase = document.getElementById('phase');
    const district = document.getElementById('district_id');
    const block = document.getElementById('block_id');
    const village = document.getElementById('village_id');

    const optionsUrl = @json(route('superadmin.registry_yet_done.options'));

    function setLoading(select, text) {
        select.innerHTML = '';
        select.add(new Option(text, ''));
    }

    function loadOptions(type, params, select, placeholder) {

        const url = new URL(optionsUrl, window.location.origin);

        url.searchParams.set('type', type);

        Object.keys(params).forEach(function (key) {
            if (params[key]) {
                url.searchParams.set(key, params[key]);
            }
        });

        setLoading(select, 'Loading...');

        return fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {

            select.innerHTML = '';
            select.add(new Option(placeholder, ''));

            data.forEach(function (item) {
                select.add(new Option(item.name, item.id));
            });

        });
    }


    phase.addEventListener('change', function () {

        setLoading(district, 'Loading districts...');
        setLoading(block, 'All Blocks');
        setLoading(village, 'All Villages');

        loadOptions(
            'districts',
            {
                phase: phase.value
            },
            district,
            'All Districts'
        );

    });


    district.addEventListener('change', function () {

        setLoading(block, 'Loading blocks...');
        setLoading(village, 'All Villages');

        loadOptions(
            'blocks',
            {
                phase: phase.value,
                district_id: district.value
            },
            block,
            'All Blocks'
        );

    });


    block.addEventListener('change', function () {

        setLoading(village, 'Loading villages...');

        loadOptions(
            'villages',
            {
                phase: phase.value,
                district_id: district.value,
                block_id: block.value
            },
            village,
            'All Villages'
        );

    });

});
</script>

@endsection
