@extends('layouts.mmgayAdmin')

@section('title', 'Applicants - Super Admin')

@section('content')

    <main class="ml-[260px] min-h-screen w-[calc(100%-260px)] bg-slate-100 p-6 pt-20">
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
        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Header --}}
            <div
                class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
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

                    {{-- Village --}}
                    <div class="lg:col-span-3">
                        <label for="village_id" class="mb-2 block text-sm font-semibold text-slate-700">
                            Village
                        </label>

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

                    {{-- Status --}}
                    <div class="lg:col-span-2">
                        <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">
                            Status
                        </label>

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
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

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

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1400px] text-sm">

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
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
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
                                        class="inline-flex rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                        Phase {{ $applicant->Phase ?? '-' }}
                                    </span>

                                </td>

                                <td class="p-3 text-center">

                                    <span
                                        class="inline-flex min-w-14 justify-center rounded-lg bg-blue-50 px-3 py-1.5 font-semibold text-blue-700">
                                        {{ $applicant->FlatNo ?? '-' }}
                                    </span>

                                </td>

                                <td class="p-3 text-center">

                                    <span
                                        class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClasses }}">
                                        {{ $status }}
                                    </span>

                                </td>

                                <td class="p-3 text-center">

                                    <a href="{{ route('superadmin.applicants.show', $applicant->secure_id) }}"
                                        class="inline-flex items-center justify-center rounded-lg border
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
