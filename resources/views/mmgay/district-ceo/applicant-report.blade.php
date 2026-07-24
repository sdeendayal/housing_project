@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-50">

    <div class="mx-auto max-w-[1800px] px-4 py-6 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-center">

            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Applicant Report
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $phase === 'all' ? 'All Phases' : 'Phase '.$phase }}
                    applicant details
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">

                    <span class="material-symbols-outlined text-[19px]">
                        print
                    </span>

                    Print
                </button>

            </div>
        </div>

        {{-- Filters --}}
        <form method="GET"
            action="{{ route('district.dashboard.applicants') }}"
            class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">

                {{-- Phase --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Phase
                    </label>

                    <select name="phase"
                        id="phase_filter"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="all" @selected($phase === 'all')>
                            All Phases
                        </option>

                        @foreach ([1, 2, 3] as $phaseOption)
                            <option value="{{ $phaseOption }}"
                                @selected((string) $phase === (string) $phaseOption)>
                                Phase {{ $phaseOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Village --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Village
                    </label>

                    <select name="village_id"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">All Villages</option>

                        @foreach ($villages as $village)
                            <option value="{{ $village->VillageId }}"
                                @selected((int) $villageId === (int) $village->VillageId)>

                                {{ $village->VillageName }}

                                @if ($phase === 'all')
                                    (Phase {{ $village->phase }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Status
                    </label>

                    <select name="status"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">All Statuses</option>

                        <option value="allotted" @selected($status === 'allotted')>
                            Allotted
                        </option>

                        <option value="approved_paid" @selected($status === 'approved_paid')>
                            Approved & Paid
                        </option>

                        <option value="approved_unpaid" @selected($status === 'approved_unpaid')>
                            Approved & Unpaid
                        </option>

                        <option value="pending" @selected($status === 'pending')>
                            Yet to be Approved
                        </option>

                        <option value="rejected" @selected($status === 'rejected')>
                            Rejected
                        </option>

                        <option value="cancelled" @selected($status === 'cancelled')>
                            Cancelled
                        </option>

                        <option value="registry_done" @selected($status === 'registry_done')>
                            Registry Done
                        </option>

                        <option value="registry_pending" @selected($status === 'registry_pending')>
                            Registry Pending
                        </option>
                    </select>
                </div>

                {{-- Caste --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Caste
                    </label>

                    <select name="caste"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">All Categories</option>

                        @foreach (['SC', 'Ghumantu', 'Widow', 'General', 'Others'] as $casteOption)
                            <option value="{{ $casteOption }}"
                                @selected($caste === $casteOption)>
                                {{ $casteOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Search
                    </label>

                    <input type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Name, mobile, registration..."
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Per Page --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Per Page
                    </label>

                    <select name="per_page"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        @foreach ([25, 50, 100, 200] as $size)
                            <option value="{{ $size }}"
                                @selected($perPage === $size)>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-4 flex flex-wrap justify-end gap-2">

                <a href="{{ route('district.dashboard.applicants', ['phase' => 'all']) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">

                    <span class="material-symbols-outlined text-[18px]">
                        restart_alt
                    </span>

                    Reset
                </a>

                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">

                    <span class="material-symbols-outlined text-[18px]">
                        filter_alt
                    </span>

                    Apply Filters
                </button>

            </div>
        </form>

        {{-- Result Information --}}
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">

            <p class="text-sm text-slate-600">
                Showing
                <span class="font-semibold text-slate-900">
                    {{ number_format($applicants->firstItem() ?? 0) }}
                </span>
                to
                <span class="font-semibold text-slate-900">
                    {{ number_format($applicants->lastItem() ?? 0) }}
                </span>
                of
                <span class="font-semibold text-slate-900">
                    {{ number_format($applicants->total()) }}
                </span>
                applicants
            </p>

        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-[2600px] w-full border-collapse text-sm">

                    <thead class="bg-slate-800 text-xs uppercase tracking-wide text-white">

                        <tr>
                            <th class="px-4 py-4 text-center">#</th>
                            <th class="px-4 py-4 text-left">Registration No.</th>
                            <th class="px-4 py-4 text-left">Applicant Name</th>
                            <th class="px-4 py-4 text-left">Father/Husband</th>
                            <th class="px-4 py-4 text-center">Gender</th>
                            <th class="px-4 py-4 text-center">Caste</th>
                            <th class="px-4 py-4 text-left">Mobile</th>
                            <th class="px-4 py-4 text-left">PPP ID</th>
                            <th class="px-4 py-4 text-left">Member ID</th>
                            <th class="px-4 py-4 text-center">Phase</th>
                            <th class="px-4 py-4 text-left">Village</th>
                            <th class="px-4 py-4 text-center">Plot No.</th>
                            <th class="px-4 py-4 text-center">Allotment</th>
                            <th class="px-4 py-4 text-center">Status</th>
                            <th class="px-4 py-4 text-center">Registry</th>
                            <th class="px-4 py-4 text-left">Remarks</th>
                            <th class="px-4 py-4 text-left">Address</th>
                            <th class="px-4 py-4 text-center">Created Date</th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @forelse ($applicants as $applicant)

                            @php
                                $statusClasses = match ($applicant->ApplicantStatus) {
                                    'Approved & Paid' =>
                                        'bg-emerald-100 text-emerald-700',

                                    'Approved & Unpaid' =>
                                        'bg-cyan-100 text-cyan-700',

                                    'Rejected' =>
                                        'bg-red-100 text-red-700',

                                    'Cancelled' =>
                                        'bg-slate-200 text-slate-700',

                                    default =>
                                        'bg-amber-100 text-amber-700',
                                };

                                $registryClasses =
                                    $applicant->RegistryStatus === 'Registry Done'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : (
                                            $applicant->RegistryStatus === 'Registry Pending'
                                                ? 'bg-orange-100 text-orange-700'
                                                : 'bg-slate-100 text-slate-600'
                                        );
                            @endphp

                            <tr class="transition hover:bg-blue-50">

                                <td class="px-4 py-3 text-center text-slate-500">
                                    {{ $applicants->firstItem() + $loop->index }}
                                </td>

                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $applicant->RegistrationNo ?: '—' }}
                                </td>

                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    {{ $applicant->OwnerName ?: '—' }}
                                </td>

                                <td class="px-4 py-3 text-slate-700">
                                    {{ $applicant->FatherHusbandName ?: '—' }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $applicant->Gender ?: '—' }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $applicant->Caste ?: 'Others' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $applicant->MobileNo ?: '—' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $applicant->PPPId ?: '—' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $applicant->MemberId ?: '—' }}
                                </td>

                                <td class="px-4 py-3 text-center font-medium">
                                    Phase {{ $applicant->Phase }}
                                </td>

                                <td class="px-4 py-3 font-medium">
                                    {{ $applicant->VillageName }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $applicant->FlatNo ?: '—' }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $applicant->AllotmentStatus }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ $applicant->ApplicantStatus }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $registryClasses }}">
                                        {{ $applicant->RegistryStatus }}
                                    </span>
                                </td>

                                <td class="max-w-[320px] px-4 py-3 text-slate-700">
                                    <div class="line-clamp-2"
                                        title="{{ $applicant->StatusRemark }}">
                                        {{ $applicant->StatusRemark }}
                                    </div>
                                </td>

                                <td class="max-w-[320px] px-4 py-3 text-slate-600">
                                    <div class="line-clamp-2"
                                        title="{{ $applicant->OwnerAddress }}">
                                        {{ $applicant->OwnerAddress ?: '—' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center text-slate-600">
                                    {{ $applicant->CreatedDate
                                        ? \Carbon\Carbon::parse($applicant->CreatedDate)->format('d-m-Y')
                                        : '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="18"
                                    class="px-6 py-16 text-center text-slate-500">

                                    No applicant records found.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($applicants->hasPages())
                <div class="border-t border-slate-200 bg-white px-5 py-4">
                    {{ $applicants->links() }}
                </div>
            @endif

        </div>

    </div>

</div>

<style>
    @media print {
        form,
        button,
        nav {
            display: none !important;
        }

        body {
            background: #ffffff !important;
        }

        table {
            min-width: 100% !important;
            font-size: 9px !important;
        }

        th,
        td {
            padding: 4px !important;
        }
    }
</style>

@endsection