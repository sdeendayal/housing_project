@extends('layouts.mmgayAdmin')

@section('title', 'Applicants - Super Admin')

@section('content')

    <style>
        /* =========================================================
           APPLICANTS PAGE - VISUAL ONLY
           No route / query / filter / pagination logic changed
        ========================================================= */

        .applicants-page {
            background:
                radial-gradient(circle at 75% 0%, rgba(79,70,229,.055), transparent 24%),
                #f6f8fc !important;
        }

        .app-filter-card,
        .app-table-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 18px !important;
            background: rgba(255,255,255,.98) !important;
            box-shadow:
                0 10px 30px rgba(15,23,42,.055),
                0 1px 2px rgba(15,23,42,.03) !important;
        }

        .app-filter-head {
            background: linear-gradient(180deg,#ffffff 0%,#fbfdff 100%) !important;
        }

        .filter-with-icon {
            position: relative;
        }

        .filter-with-icon .filter-left-icon {
            position: absolute;
            left: 9px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: linear-gradient(135deg,#eff6ff,#eef2ff);
            color: #4f46e5;
            border: 1px solid #e0e7ff;
            pointer-events: none;
            z-index: 2;
        }

        .filter-with-icon .filter-left-icon svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .filter-with-icon select {
            height: 46px;
            padding-left: 48px !important;
            padding-right: 34px !important;
            border-color: #dbe3ef !important;
            border-radius: 12px !important;
            background-color: #fff !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            transition: .2s ease;
        }

        .filter-with-icon select:hover {
            border-color: #a5b4fc !important;
            background-color: #fafbff !important;
        }

        .filter-with-icon:focus-within .filter-left-icon {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg,#2563eb,#4f46e5);
            box-shadow: 0 5px 12px rgba(79,70,229,.18);
        }

        .filter-with-icon select:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99,102,241,.10) !important;
        }

        /* Table fits in one screen - horizontal scroll removed */
        .app-table-wrap {
            overflow-x: visible !important;
            width: 100%;
        }

        .app-table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed;
            font-size: 11px !important;
        }

        .app-table th {
            padding: 10px 7px !important;
            font-size: 9px !important;
            line-height: 1.15 !important;
            letter-spacing: .025em;
            white-space: normal !important;
            overflow-wrap: anywhere;
        }

        .app-table td {
            padding: 9px 7px !important;
            font-size: 11px !important;
            line-height: 1.25 !important;
            vertical-align: middle;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .app-table .applicant-avatar {
            width: 30px !important;
            height: 30px !important;
            min-width: 30px !important;
            font-size: 11px !important;
        }

        .app-table .phase-pill,
        .app-table .flat-pill,
        .app-table .status-pill {
            font-size: 10px !important;
            padding: 4px 7px !important;
            line-height: 1.15;
            min-width: 0 !important;
            white-space: normal !important;
        }

        .app-table .view-btn {
            padding: 6px !important;
        }

        /* Column widths tuned to 10-column layout */
        .app-table th:nth-child(1), .app-table td:nth-child(1) { width: 4%; }
        .app-table th:nth-child(2), .app-table td:nth-child(2) { width: 13%; }
        .app-table th:nth-child(3), .app-table td:nth-child(3) { width: 17%; }
        .app-table th:nth-child(4), .app-table td:nth-child(4) { width: 13%; }
        .app-table th:nth-child(5), .app-table td:nth-child(5) { width: 10%; }
        .app-table th:nth-child(6), .app-table td:nth-child(6) { width: 11%; }
        .app-table th:nth-child(7), .app-table td:nth-child(7) { width: 7%; }
        .app-table th:nth-child(8), .app-table td:nth-child(8) { width: 8%; }
        .app-table th:nth-child(9), .app-table td:nth-child(9) { width: 12%; }
        .app-table th:nth-child(10), .app-table td:nth-child(10) { width: 5%; }

        @media (max-width: 1280px) {
            .app-table {
                font-size: 10px !important;
            }

            .app-table td {
                font-size: 10px !important;
                padding: 8px 5px !important;
            }

            .app-table th {
                font-size: 8.5px !important;
                padding: 9px 5px !important;
            }
        }
    </style>


    <main class="applicants-page ml-[260px] min-h-screen w-[calc(100%-260px)] bg-slate-100 p-6 pt-20">
        @if (session('error'))
            <div
                class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <span class="material-symbols-outlined text-[20px]">
                    error
                </span>

                <span>
                    {{ session('error') }}
                </span>
            </div>
        @endif
        {{-- Filter Card --}}
        <div class="app-filter-card mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Header --}}
            <div
                class="app-filter-head flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Applicants Filters
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Search and filter allotted applicants.
                    </p>
                </div>

                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>

                    {{ number_format($applicants->total()) }} Applicants Found
                </div>
            </div>

            {{-- Filters Form --}}
            <form method="GET" action="{{ route('superadmin.applicants.index') }}" class="p-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-12">

                    {{-- Search --}}
                    <div class="lg:col-span-3">
                        <label for="search" class="mb-2 block text-sm font-semibold text-slate-700">
                            Search
                        </label>

                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">
                                search
                            </span>

                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="Name, mobile, application or plot..."
                                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>
                    </div>

                    {{-- Phase --}}
                    <div class="lg:col-span-2">
                        <label for="phase" class="mb-2 block text-sm font-semibold text-slate-700">
                            Phase
                        </label>

                        <div class="filter-with-icon">
                            <span class="filter-left-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                            </span>
                            <select id="phase" name="phase"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">All Phases</option>

                            <option value="1" {{ request('phase') == '1' ? 'selected' : '' }}>
                                Phase 1
                            </option>

                            <option value="2" {{ request('phase') == '2' ? 'selected' : '' }}>
                                Phase 2
                            </option>

                            <option value="3" {{ request('phase') == '3' ? 'selected' : '' }}>
                                Phase 3
                            </option>
                        </select>
                        </div>
                    </div>

                    {{-- Village --}}
                    <div class="lg:col-span-3">
                        <label for="village_id" class="mb-2 block text-sm font-semibold text-slate-700">
                            Village
                        </label>

                        <div class="filter-with-icon">
                            <span class="filter-left-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M3 10.5 12 4l9 6.5"/><path d="M5.5 9.5V21h13V9.5"/><path d="M9.5 21v-6h5v6"/></svg>
                            </span>
                            <select id="village_id" name="village_id"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">All Villages</option>

                            @foreach ($villages as $village)
                                <option value="{{ $village->VillageId }}"
                                    {{ request('village_id') == $village->VillageId ? 'selected' : '' }}>
                                    {{ $village->VillageName }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="lg:col-span-2">
                        <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">
                            Status
                        </label>

                        <div class="filter-with-icon">
                            <span class="filter-left-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M4 5h16"/><path d="M7 10h10"/><path d="M10 15h4"/><path d="M12 19h.01"/></svg>
                            </span>
                            <select id="status" name="status"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">All Statuses</option>

                            <option value="approved_paid" {{ request('status') === 'approved_paid' ? 'selected' : '' }}>
                                Approved & Paid
                            </option>

                            <option value="approved_unpaid"
                                {{ request('status') === 'approved_unpaid' ? 'selected' : '' }}>
                                Approved & Unpaid
                            </option>

                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                Yet to be Approved
                            </option>

                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>
                        </select>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-end gap-3 lg:col-span-2">
                        <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200">
                            <span class="material-symbols-outlined text-[19px]">
                                filter_alt
                            </span>

                            Apply
                        </button>

                        <a href="{{ route('superadmin.applicants.index') }}"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200">
                            <span class="material-symbols-outlined text-[19px]">
                                restart_alt
                            </span>


                        </a>
                    </div>
                </div>

                {{-- Active Filters --}}
                @if (request('search') || request('phase') || request('village_id') || request('status'))
                    @php
                        $selectedVillage = $villages->firstWhere('VillageId', request('village_id'));

                        $activeStatusLabels = [
                            'approved_paid' => 'Approved & Paid',
                            'approved_unpaid' => 'Approved & Unpaid',
                            'pending' => 'Yet to be Approved',
                            'rejected' => 'Rejected',
                            'cancelled' => 'Cancelled',
                        ];
                    @endphp

                    <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                        <span class="text-sm font-medium text-slate-500">
                            Active Filters:
                        </span>

                        @if (request('search'))
                            <span
                                class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                Search: {{ request('search') }}
                            </span>
                        @endif

                        @if (request('phase'))
                            <span
                                class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                Phase {{ request('phase') }}
                            </span>
                        @endif

                        @if (request('village_id'))
                            <span
                                class="inline-flex items-center rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                                {{ $selectedVillage->VillageName ?? 'Village' }}
                            </span>
                        @endif

                        @if (request('status'))
                            <span
                                class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                {{ $activeStatusLabels[request('status')] ?? request('status') }}
                            </span>
                        @endif
                    </div>
                @endif
            </form>

            {{-- Export Footer --}}
            {{-- Export Footer --}}
            <div
                class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex flex-wrap items-center gap-2">

                    {{-- CSV --}}
                    <a href="{{ route('superadmin.applicants.csv', request()->except('page')) }}"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-xl
           bg-emerald-600 px-4 text-sm font-semibold text-white
           shadow-sm transition hover:bg-emerald-700">

                        <span class="material-symbols-outlined">
                            table_view
                        </span>

                        Excel
                    </a>

                    {{-- Print --}}
                    <a href="{{ route('superadmin.applicants.print', request()->except('page')) }}" target="_blank"
                        rel="noopener"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-xl
                   border border-slate-300 bg-white px-4
                   text-sm font-semibold text-slate-700 shadow-sm
                   transition hover:-translate-y-0.5
                   hover:bg-slate-700 hover:text-white hover:shadow-md">

                        <span class="material-symbols-outlined">
                            picture_as_pdf
                        </span>

                        PDF
                    </a>

                </div>

                <div
                    class="inline-flex w-fit items-center rounded-xl bg-indigo-100 px-4 py-2.5 text-sm font-semibold text-indigo-700">
                    Total: {{ number_format($applicants->total()) }}
                </div>
            </div>

        </div>

        {{-- Applicants Table --}}
        <div class="app-table-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="text-lg font-semibold text-slate-800">
                        Allotted Applicants
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Applicants who have been assigned a plot.
                    </p>
                </div>

                <div class="rounded-xl bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700">
                    Total: {{ number_format($applicants->total()) }}
                </div>

            </div>

            <div class="app-table-wrap">

                <table class="app-table w-full text-sm">

                    <thead class="bg-blue-600 text-xs uppercase text-white">

                        <tr>
                            <th class="p-3 text-center">
                                #
                            </th>

                            <th class="p-3 text-left">
                                Application No.
                            </th>

                            <th class="p-3 text-left">
                                Applicant
                            </th>

                            <th class="p-3 text-left">
                                Father / Husband
                            </th>

                            <th class="p-3 text-left">
                                Mobile
                            </th>

                            <th class="p-3 text-left">
                                Village
                            </th>

                            <th class="p-3 text-center">
                                Phase
                            </th>

                            <th class="p-3 text-center">
                                Flat No.
                            </th>

                            <th class="p-3 text-center">
                                Status
                            </th>

                            <th class="p-3 text-center">
                                Action
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @forelse ($applicants as $applicant)
                            @php
                                $status = $applicant->ApplicantStatus ?? 'Allotted';

                                $statusClasses = match ($status) {
                                    'Approved & Paid' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',

                                    'Approved & Unpaid' => 'bg-amber-100 text-amber-700 ring-amber-200',

                                    'Yet to be Approved' => 'bg-orange-100 text-orange-700 ring-orange-200',

                                    'Rejected' => 'bg-red-100 text-red-700 ring-red-200',

                                    'Cancelled' => 'bg-slate-200 text-slate-700 ring-slate-300',

                                    default => 'bg-blue-100 text-blue-700 ring-blue-200',
                                };
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50">

                                <td class="p-3 text-center text-slate-500">
                                    {{ $applicants->firstItem() + $loop->index }}
                                </td>

                                <td class="p-3 font-medium text-slate-700">
                                    {{ $applicant->RegistrationNo ?? '-' }}
                                </td>

                                <td class="p-3">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="applicant-avatar flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                                            {{ strtoupper(substr($applicant->OwnerName ?? 'A', 0, 1)) }}
                                        </div>

                                        <div>
                                            <p class="font-semibold text-slate-800">
                                                {{ $applicant->OwnerName ?? '-' }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                Owner ID: {{ $applicant->OwnerId ?? '-' }}
                                            </p>
                                        </div>

                                    </div>

                                </td>

                                <td class="p-3 text-slate-600">
                                    {{ $applicant->FatherHusbandName ?? '-' }}
                                </td>

                                <td class="p-3">

                                    @if (!empty($applicant->MobileNo))
                                        <a href="tel:{{ $applicant->MobileNo }}"
                                            class="font-medium text-blue-600 hover:underline">
                                            {{ $applicant->MobileNo }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif

                                </td>

                                <td class="p-3 text-slate-700">
                                    {{ $applicant->VillageName ?? '-' }}
                                </td>

                                <td class="p-3 text-center">

                                    <span
                                        class="phase-pill inline-flex rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                        Phase {{ $applicant->Phase ?? '-' }}
                                    </span>

                                </td>

                                <td class="p-3 text-center">

                                    <span
                                        class="flat-pill inline-flex min-w-14 justify-center rounded-lg bg-blue-50 px-3 py-1.5 font-semibold text-blue-700">
                                        {{ $applicant->FlatNo ?? '-' }}
                                    </span>

                                </td>

                                <td class="p-3 text-center">

                                    <span
                                        class="status-pill inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClasses }}">
                                        {{ $status }}
                                    </span>

                                </td>

                                <td class="p-3 text-center">

                                    <a href="{{ route('superadmin.applicants.show', $applicant->secure_id) }}"
                                        class="view-btn inline-flex items-center justify-center rounded-lg border
           border-slate-300 bg-white p-2 text-slate-600 shadow-sm
           transition hover:border-indigo-300 hover:bg-indigo-50
           hover:text-indigo-700"
                                        title="View Applicant">

                                        <span class="material-symbols-outlined text-[19px]">
                                            visibility
                                        </span>
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="11" class="p-12 text-center">

                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                                        <span class="material-symbols-outlined text-3xl text-slate-400">
                                            person_search
                                        </span>
                                    </div>

                                    <h4 class="mt-4 font-semibold text-slate-700">
                                        No applicants found
                                    </h4>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Change the filters and try again.
                                    </p>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @if ($applicants->hasPages())
                <div class="border-t border-slate-200 bg-slate-50 px-5 py-4">
                    {{ $applicants->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            @endif

        </div>

    </main>
    <div id="downloadModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 px-4 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">

            <div class="mx-auto mb-4 h-14 w-14 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"></div>

            <h3 class="text-lg font-bold text-slate-800">
                Download Preparing
            </h3>

            <p id="downloadMessage" class="mt-2 text-sm text-slate-500">
                Please wait...
            </p>
        </div>
    </div>

@endsection
