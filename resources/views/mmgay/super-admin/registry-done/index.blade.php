@extends('layouts.mmgayAdmin')

@section('title', 'Registry Done')

@section('content')

    <style>
        .rd-page {
            min-height: calc(100vh - 64px);
            background:
                radial-gradient(circle at 92% 4%, rgba(99, 102, 241, .08), transparent 24rem),
                radial-gradient(circle at 5% 30%, rgba(249, 115, 22, .05), transparent 22rem),
                #f1f5f9;
        }

        .rd-card {
            border: 1px solid #e2e8f0;
            background: rgba(255, 255, 255, .96);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, .055);
        }

        .rd-header {
            position: relative;
            overflow: hidden;
        }

        .rd-header:after {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            right: -90px;
            top: -120px;
            border-radius: 999px;
            background: rgba(79, 70, 229, .06);
            pointer-events: none;
        }

        .rd-title-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
            flex: 0 0 auto;
        }

        .rd-filter {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 15px;
            padding: 12px;
            transition: .18s ease;
        }

        .rd-filter:focus-within {
            border-color: #c7d2fe;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, .08);
        }

        .rd-filter label {
            display: block;
            margin-bottom: 7px;
            font-size: 10px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #64748b;
        }

        .rd-control {
            width: 100%;
            height: 42px;
            border: 0;
            outline: 0;
            background: transparent;
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
        }

        .rd-control::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .rd-filter-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef2ff;
            color: #4f46e5;
            flex: 0 0 auto;
        }

        .rd-filter-icon svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .rd-select-row,
        .rd-search-row {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .rd-btn {
            height: 46px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 18px;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
        }

        .rd-btn:hover {
            transform: translateY(-1px);
        }

        .rd-btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #ea580c, #f97316);
            box-shadow: 0 7px 18px rgba(234, 88, 12, .18);
        }

        .rd-btn-secondary {
            color: #334155;
            background: #fff;
            border: 1px solid #cbd5e1;
        }

        .rd-btn-print {
            color: #fff;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            box-shadow: 0 7px 18px rgba(220, 38, 38, .16);
        }

        .rd-summary {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .rd-summary-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #10b981;
            box-shadow: 0 0 0 4px #d1fae5;
        }

        .rd-table-wrap {
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .rd-table {
            width: 100%;
            min-width: 980px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .rd-table th {
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

        .rd-table td {
            padding: 14px 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            font-size: 12px;
        }

        .rd-table tbody tr {
            transition: background .15s ease;
        }

        .rd-table tbody tr:hover {
            background: #fafbff;
        }

        .rd-number {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #f1f5f9;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
        }

        .rd-avatar {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 900;
        }

        .rd-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 12px;
            border-radius: 10px;
            background: #eef2ff;
            border: 1px solid #e0e7ff;
            color: #4338ca;
            font-size: 11px;
            font-weight: 800;
            transition: .16s ease;
        }

        .rd-action:hover {
            background: #e0e7ff;
            transform: translateY(-1px);
        }

        .rd-empty {
            padding: 64px 20px;
            text-align: center;
        }

        .rd-empty-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 17px;
            background: #f1f5f9;
            color: #94a3b8;
        }

        .rd-pagination {
            border-top: 1px solid #e2e8f0;
            background: #fff;
        }

        @media (max-width: 900px) {
            .rd-main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 16px !important;
            }
        }

        @media print {
            .rd-no-print {
                display: none !important;
            }

            .rd-page {
                background: #fff !important;
            }

            .rd-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .rd-table {
                min-width: 0 !important;
            }

            .rd-table th,
            .rd-table td {
                padding: 8px !important;
            }
        }

        /* =========================================================
       Registry Done - Select Dropdown
       ========================================================= */

        .rd-select-row {
            position: relative;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .rd-select-row .rd-control {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 32px;
            cursor: pointer;
        }

        .rd-select-arrow {
            position: absolute;
            right: 2px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            color: #64748b;
            pointer-events: none;
            transition: .18s ease;
        }

        .rd-select-arrow svg {
            width: 17px;
            height: 17px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .rd-filter:focus-within .rd-select-arrow {
            color: #4f46e5;
        }

        .rd-select-row select:hover+.rd-select-arrow {
            color: #4f46e5;
        }


        /* Better dropdown text */

        .rd-select-row select option {
            padding: 10px;
            font-weight: 600;
            color: #334155;
        }


        /* Search icon alignment */

        .rd-search-row {
            position: relative;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .rd-search-row .rd-control {
            padding-right: 5px;
        }


        /* Make controls feel clickable */

        .rd-control {
            transition: color .15s ease;
        }

        .rd-control:hover {
            color: #312e81;
        }
    </style>

    <main class="rd-page rd-main min-h-screen p-5 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

        {{-- Header --}}
        <section class="rd-card rd-header mb-5 p-6 lg:p-7">
            <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <div class="rd-title-icon">
                        <span class="material-symbols-outlined text-[25px]">task_alt</span>
                    </div>

                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                                Registry Done
                            </h1>
                            <span
                                class="rounded-full bg-emerald-50 px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-wider text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                Completed
                            </span>
                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            Verified applicants whose registry process has been completed.
                        </p>
                    </div>
                </div>

                <button type="button" onclick="window.print()" class="rd-btn rd-btn-print rd-no-print">
                    <span class="material-symbols-outlined text-[19px]">print</span>
                    Print List
                </button>
            </div>
        </section>

        {{-- Filters --}}
        <section class="rd-card rd-no-print mb-5 p-5 lg:p-6">

            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-900">
                        Find Registry Records
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Use the filters below to narrow down the completed registry list.
                    </p>
                </div>

                <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Live filters
                </div>
            </div>


            <form method="GET" action="{{ route('superadmin.registry_done.index') }}"
                class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-6">


                {{-- PHASE --}}
                <div class="rd-filter">

                    <label for="phase">
                        Phase
                    </label>

                    <div class="rd-select-row">

                        <span class="rd-filter-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M4 6h16M7 12h10M10 18h4" />
                            </svg>
                        </span>

                        <select name="phase" id="phase" class="rd-control">

                            <option value="">
                                All Phases
                            </option>

                            @foreach ($phases as $phase)
                                <option value="{{ $phase }}" @selected(request('phase') == $phase)>
                                    Phase {{ $phase }}
                                </option>
                            @endforeach

                        </select>

                        {{-- Dropdown indicator --}}
                        <span class="rd-select-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </span>

                    </div>

                </div>



                {{-- DISTRICT --}}
                <div class="rd-filter">

                    <label for="district_id">
                        District
                    </label>

                    <div class="rd-select-row">

                        <span class="rd-filter-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M4 21h16" />
                                <path d="M6 21V8l6-4 6 4v13" />
                                <path d="M9 11h1M14 11h1M9 15h1M14 15h1" />
                            </svg>
                        </span>

                        <select name="district_id" id="district_id" class="rd-control">

                            <option value="">
                                All Districts
                            </option>

                            @foreach ($districts as $district)
                                <option value="{{ $district->DistrictId }}" @selected(request('district_id') == $district->DistrictId)>
                                    {{ $district->DistrictName }}
                                </option>
                            @endforeach

                        </select>

                        {{-- Dropdown indicator --}}
                        <span class="rd-select-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </span>

                    </div>

                </div>



                {{-- BLOCK --}}
                <div class="rd-filter">

                    <label for="block_id">
                        Block
                    </label>

                    <div class="rd-select-row">

                        <span class="rd-filter-icon">
                            <svg viewBox="0 0 24 24">

                                <rect x="4" y="4" width="6" height="6" />
                                <rect x="14" y="4" width="6" height="6" />
                                <rect x="4" y="14" width="6" height="6" />
                                <rect x="14" y="14" width="6" height="6" />

                            </svg>
                        </span>

                        <select name="block_id" id="block_id" class="rd-control">

                            <option value="">
                                All Blocks
                            </option>

                            @foreach ($blocks as $block)
                                <option value="{{ $block->BlockId }}" @selected(request('block_id') == $block->BlockId)>
                                    {{ $block->BlockName }}
                                </option>
                            @endforeach

                        </select>

                        {{-- Dropdown indicator --}}
                        <span class="rd-select-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </span>

                    </div>

                </div>



                {{-- VILLAGE --}}
                <div class="rd-filter">

                    <label for="village_id">
                        Village
                    </label>

                    <div class="rd-select-row">

                        <span class="rd-filter-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 10.5 12 4l9 6.5" />
                                <path d="M5.5 9.5V21h13V9.5" />
                                <path d="M9.5 21v-6h5v6" />
                            </svg>
                        </span>

                        <select name="village_id" id="village_id" class="rd-control">

                            <option value="">
                                All Villages
                            </option>

                            @foreach ($villages as $village)
                                <option value="{{ $village->VillageId }}" @selected(request('village_id') == $village->VillageId)>
                                    {{ $village->VillageName }}
                                </option>
                            @endforeach

                        </select>

                        {{-- Dropdown indicator --}}
                        <span class="rd-select-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </span>

                    </div>

                </div>



                {{-- SEARCH --}}
                <div class="rd-filter">

                    <label for="search">
                        Search
                    </label>

                    <div class="rd-search-row">

                        <span class="rd-filter-icon">
                            <svg viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-4-4" />
                            </svg>
                        </span>

                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Name, mobile, application..." class="rd-control">

                    </div>

                </div>



                {{-- BUTTONS --}}
                <div class="flex items-end gap-2">

                    <button type="submit" class="rd-btn rd-btn-primary flex-1">

                        <span class="material-symbols-outlined text-[18px]">
                            search
                        </span>

                        Apply

                    </button>


                    <a href="{{ route('superadmin.registry_done.index') }}" class="rd-btn rd-btn-secondary">

                        <span class="material-symbols-outlined text-[18px]">
                            restart_alt
                        </span>

                        Reset

                    </a>

                </div>

            </form>

        </section>

        {{-- Results --}}
        <section class="rd-card overflow-hidden">

            <div
                class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-black text-slate-900">
                            Completed Applicants
                        </h2>
                        <span
                            class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-extrabold text-indigo-700 ring-1 ring-inset ring-indigo-100">
                            Registry Done
                        </span>
                    </div>

                    <p class="mt-1 text-xs text-slate-500">
                        Showing
                        <span class="font-bold text-slate-700">{{ $registryDone->firstItem() ?? 0 }}</span>
                        –
                        <span class="font-bold text-slate-700">{{ $registryDone->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-bold text-slate-700">{{ number_format($registryDone->total()) }}</span>
                        records
                    </p>
                </div>

                <div class="rd-summary">
                    <span class="rd-summary-dot"></span>
                    <div>
                        <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Total completed
                        </div>
                        <div class="text-sm font-black text-slate-800">{{ number_format($registryDone->total()) }}</div>
                    </div>
                </div>
            </div>

            <div class="rd-table-wrap">
                <table class="rd-table">
                    <thead>
                        <tr>
                            <th class="w-[60px]">#</th>
                            <th>Application</th>
                            <th>Applicant</th>
                            <th>Mobile</th>
                            <th>Location</th>
                            <th>Phase</th>
                            <th>Flat</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($registryDone as $row)
                            @php
                                $secureId = hash('sha256', $row->OwnerId . '-' . $row->MobileNo);
                                $initial = strtoupper(substr(trim($row->OwnerName ?? 'A'), 0, 1));
                            @endphp

                            <tr>
                                <td>
                                    <span class="rd-number">
                                        {{ ($registryDone->firstItem() ?? 1) + $loop->index }}
                                    </span>
                                </td>

                                <td>
                                    <div class="font-extrabold text-slate-800">
                                        {{ $row->RegistrationNo ?? '-' }}
                                    </div>
                                    <div class="mt-1 text-[10px] font-medium text-slate-400">
                                        Owner ID:
                                        <span class="font-bold text-slate-500">{{ $row->OwnerId ?? '-' }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="rd-avatar">{{ $initial }}</span>
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
                                    <span
                                        class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-extrabold text-slate-600">
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

                                <td class="text-center">
                                    <a href="{{ route('superadmin.registry_done.show', $secureId) }}" class="rd-action">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="rd-empty">
                                        <div class="rd-empty-icon">
                                            <span class="material-symbols-outlined text-[28px]">inventory_2</span>
                                        </div>
                                        <h3 class="mt-4 text-sm font-black text-slate-700">
                                            No Registry Done records found
                                        </h3>
                                        <p class="mx-auto mt-1 max-w-md text-xs text-slate-400">
                                            Try changing the phase, district, block, village, or search criteria.
                                        </p>
                                        <a href="{{ route('superadmin.registry_done.index') }}"
                                            class="rd-btn rd-btn-secondary rd-no-print mt-5">
                                            <span class="material-symbols-outlined text-[17px]">restart_alt</span>
                                            Clear Filters
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($registryDone->hasPages())
                <div class="rd-pagination rd-no-print px-5 py-4 sm:px-6">
                    {{ $registryDone->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            @endif

        </section>

    </main>


    {{-- Dependent Filters --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const phase = document.getElementById('phase');
            const district = document.getElementById('district_id');
            const block = document.getElementById('block_id');
            const village = document.getElementById('village_id');

            const optionsUrl = @json(route('superadmin.registry_done.options'));

            function setLoading(select, text) {
                select.innerHTML = '';
                select.add(new Option(text, ''));
            }

            function loadOptions(type, params, select, placeholder) {

                const url = new URL(optionsUrl, window.location.origin);

                url.searchParams.set('type', type);

                Object.keys(params).forEach(function(key) {

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

                        data.forEach(function(item) {
                            select.add(new Option(item.name, item.id));
                        });

                    });
            }


            phase.addEventListener('change', function() {

                setLoading(district, 'Loading districts...');
                setLoading(block, 'All Blocks');
                setLoading(village, 'All Villages');

                loadOptions(
                    'districts', {
                        phase: phase.value
                    },
                    district,
                    'All Districts'
                );

            });


            district.addEventListener('change', function() {

                setLoading(block, 'Loading blocks...');
                setLoading(village, 'All Villages');

                loadOptions(
                    'blocks', {
                        phase: phase.value,
                        district_id: district.value
                    },
                    block,
                    'All Blocks'
                );

            });


            block.addEventListener('change', function() {

                setLoading(village, 'Loading villages...');

                loadOptions(
                    'villages', {
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
