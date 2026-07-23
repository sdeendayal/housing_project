@extends('layouts.mmgayCEOAuth')

@section('title', 'Village Wise Report')

@section('content')

    <main class="mt-[68px] min-h-screen bg-slate-50 p-4 lg:ml-[230px] lg:w-[calc(100%-230px)] lg:p-5">

        {{-- Page Header --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100">

                        <span class="material-symbols-outlined text-[24px] text-blue-700">
                            table_view
                        </span>

                    </div>

                    <div>
                        <h1 class="text-xl font-bold text-slate-800">
                            Village Wise Consolidated Report
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $phase === 'all' ? 'All Phases' : 'Phase ' . $phase }}
                            village status report
                        </p>
                    </div>

                </div>

                <a href="{{ route('district.dashboard', ['phase' => $phase]) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">

                    <span class="material-symbols-outlined text-[19px]">
                        arrow_back
                    </span>

                    Back to Dashboard
                </a>

            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('district.dashboard.report', ['type' => 'villages']) }}"
                class="bg-slate-50/70 p-5">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">

                    {{-- Phase --}}
                    <div class="xl:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Phase
                        </label>

                        <select name="phase"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

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

                    </div>

                    {{-- Village --}}
                    <div class="xl:col-span-3">

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Village
                        </label>

                        <select name="village_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

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

                    </div>

                    {{-- Status --}}
                    <div class="xl:col-span-3">

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Status
                        </label>

                        <select name="status"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <option value="">
                                All Statuses
                            </option>

                            <option value="approved_paid" {{ $status === 'approved_paid' ? 'selected' : '' }}>
                                Approved & Paid
                            </option>

                            <option value="approved_unpaid" {{ $status === 'approved_unpaid' ? 'selected' : '' }}>
                                Approved & Unpaid
                            </option>

                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>
                                Yet to be Approved
                            </option>

                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                            <option value="registry_done" {{ $status === 'registry_done' ? 'selected' : '' }}>
                                Registry Done
                            </option>

                            <option value="registry_pending" {{ $status === 'registry_pending' ? 'selected' : '' }}>
                                Registry Yet to be Done
                            </option>

                        </select>

                    </div>

                    {{-- Caste --}}
                    <div class="xl:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Caste
                        </label>

                        <select name="caste"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <option value="">
                                All Castes
                            </option>

                            <option value="SC" {{ $caste === 'SC' ? 'selected' : '' }}>
                                SC
                            </option>

                            <option value="Ghumantu" {{ $caste === 'Ghumantu' ? 'selected' : '' }}>
                                Ghumantu
                            </option>

                            <option value="Widow" {{ $caste === 'Widow' ? 'selected' : '' }}>
                                Widow
                            </option>

                            <option value="General" {{ $caste === 'General' ? 'selected' : '' }}>
                                General
                            </option>

                            <option value="Others" {{ $caste === 'Others' ? 'selected' : '' }}>
                                Others
                            </option>

                        </select>

                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-end gap-2 xl:col-span-2">

                        <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">

                            <span class="material-symbols-outlined text-[19px]">
                                filter_alt
                            </span>

                            Apply
                        </button>

                        <a href="{{ route('district.dashboard.report', ['type' => 'villages']) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-600 transition hover:bg-slate-100"
                            title="Reset Filters">

                            <span class="material-symbols-outlined text-[19px]">
                                restart_alt
                            </span>

                        </a>

                    </div>

                </div>

            </form>

        </div>

        {{-- Summary Cards --}}
        {{-- <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Villages
                </p>

                <h3 class="mt-2 text-2xl font-bold text-blue-700">
                    {{ number_format($totals['totalVillages'] ?? 0) }}
                </h3>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Plots
                </p>

                <h3 class="mt-2 text-2xl font-bold text-emerald-700">
                    {{ number_format($totals['totalPlots'] ?? 0) }}
                </h3>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Applicants
                </p>

                <h3 class="mt-2 text-2xl font-bold text-indigo-700">
                    {{ number_format($totals['totalApplicants'] ?? 0) }}
                </h3>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Approved Paid
                </p>

                <h3 class="mt-2 text-2xl font-bold text-green-700">
                    {{ number_format($totals['approvedPaid'] ?? 0) }}
                </h3>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Allotment
                </p>

                <h3 class="mt-2 text-2xl font-bold text-amber-700">
                    {{ number_format($totals['totalAllotment'] ?? 0) }}
                </h3>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Possession
                </p>

                <h3 class="mt-2 text-2xl font-bold text-violet-700">
                    {{ number_format($totals['totalPossession'] ?? 0) }}
                </h3>
            </div>

        </div> --}}

        {{-- Report Table --}}
        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        Village Status Table
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        All dashboard statuses merged village-wise
                    </p>
                </div>

                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">

                    {{ number_format($reportData->count()) }} Records

                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-[2300px] w-full border-collapse text-sm">

                    <thead class="bg-slate-800 text-xs uppercase tracking-wide text-white">

                        <tr>

                            <th class="w-16 border-r border-slate-700 px-4 py-4 text-center">
                                #
                            </th>

                            <th class="w-24 border-r border-slate-700 px-4 py-4 text-center">
                                Phase
                            </th>

                            <th
                                class="sticky left-0 z-20 min-w-[220px] border-r border-slate-700 bg-slate-800 px-4 py-4 text-left">
                                Village
                            </th>

                            <th class="min-w-[100px] border-r border-slate-700 px-4 py-4 text-center">
                                Plots
                            </th>

                            <th class="min-w-[120px] border-r border-slate-700 px-4 py-4 text-center">
                                Applicants
                            </th>

                            <th class="min-w-[120px] border-r border-slate-700 px-4 py-4 text-center">
                                Allotment
                            </th>

                            <th class="min-w-[145px] border-r border-slate-700 px-4 py-4 text-center">
                                Approved & Paid
                            </th>

                            <th class="min-w-[155px] border-r border-slate-700 px-4 py-4 text-center">
                                Approved & Unpaid
                            </th>

                            <th class="min-w-[165px] border-r border-slate-700 px-4 py-4 text-center">
                                Yet to be Approved
                            </th>

                            <th class="min-w-[110px] border-r border-slate-700 px-4 py-4 text-center">
                                Rejected
                            </th>

                            <th class="min-w-[110px] border-r border-slate-700 px-4 py-4 text-center">
                                Cancelled
                            </th>

                            <th class="min-w-[160px] border-r border-slate-700 px-4 py-4 text-center">
                                Registry to be done
                            </th>

                            <th class="min-w-[125px] border-r border-slate-700 px-4 py-4 text-center">
                                Registry Done
                            </th>

                            <th class="min-w-[180px] border-r border-slate-700 px-4 py-4 text-center">
                                Registry yet to be done
                            </th>

                            <th class="min-w-[175px] border-r border-slate-700 px-4 py-4 text-center">
                                Possession to be Given
                            </th>

                            <th class="min-w-[90px] border-r border-slate-700 px-4 py-4 text-center">
                                SC
                            </th>

                            <th class="min-w-[110px] border-r border-slate-700 px-4 py-4 text-center">
                                Ghumantu
                            </th>

                            <th class="min-w-[90px] border-r border-slate-700 px-4 py-4 text-center">
                                Widow
                            </th>

                            <th class="min-w-[90px] px-4 py-4 text-center">
                                Others
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">

                        @forelse ($reportData as $row)
                            <tr class="transition hover:bg-blue-50">

                                <td class="border-r border-slate-200 px-4 py-3 text-center text-slate-500">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-medium text-slate-700">
                                    Phase {{ $row->Phase }}
                                </td>

                                <td
                                    class="sticky left-0 z-10 border-r border-slate-200 bg-white px-4 py-3 font-semibold text-slate-800 transition group-hover:bg-blue-50">
                                    {{ $row->VillageName ?? '-' }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center">
                                    {{ number_format($row->TotalPlots ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-indigo-700">
                                    {{ number_format($row->TotalApplicants ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-blue-700">
                                    {{ number_format($row->TotalAllotment ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-emerald-700">
                                    {{ number_format($row->ApprovedPaid ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-cyan-700">
                                    {{ number_format($row->ApprovedUnpaid ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-amber-700">
                                    {{ number_format($row->PendingApproval ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-rose-700">
                                    {{ number_format($row->Rejected ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-slate-700">
                                    {{ number_format($row->Cancelled ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-blue-700">
                                    {{ number_format($row->TotalAllotment ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-emerald-700">
                                    {{ number_format($row->RegistryDone ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-orange-700">
                                    {{ number_format($row->RegistryPending ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center font-semibold text-violet-700">
                                    {{ number_format($row->Possession ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center">
                                    {{ number_format($row->SC ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center">
                                    {{ number_format($row->Ghumantu ?? 0) }}
                                </td>

                                <td class="border-r border-slate-200 px-4 py-3 text-center">
                                    {{ number_format($row->Widow ?? 0) }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ number_format($row->Others ?? 0) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="19" class="px-6 py-14 text-center">

                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">

                                        <span class="material-symbols-outlined text-[30px] text-slate-400">
                                            folder_off
                                        </span>

                                    </div>

                                    <p class="mt-3 font-semibold text-slate-700">
                                        No village records found
                                    </p>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                    @if ($reportData->isNotEmpty())
                        <tfoot class="border-t-2 border-slate-300 bg-slate-100 font-bold text-slate-800">

                            <tr>

                                <td colspan="3"
                                    class="sticky left-0 z-20 border-r border-slate-300 bg-slate-100 px-4 py-4 text-left">
                                    Grand Total
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center">
                                    {{ number_format($totals['totalPlots'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center">
                                    {{ number_format($totals['totalApplicants'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-blue-700">
                                    {{ number_format($totals['totalAllotment'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-emerald-700">
                                    {{ number_format($totals['approvedPaid'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-cyan-700">
                                    {{ number_format($totals['approvedUnpaid'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-amber-700">
                                    {{ number_format($totals['pending'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-rose-700">
                                    {{ number_format($totals['rejected'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center">
                                    {{ number_format($totals['cancelled'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-blue-700">
                                    {{ number_format($totals['totalAllotment'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-emerald-700">
                                    {{ number_format($totals['registryDone'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-orange-700">
                                    {{ number_format($totals['registryPending'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center text-violet-700">
                                    {{ number_format($totals['totalPossession'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center">
                                    {{ number_format($totals['sc'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center">
                                    {{ number_format($totals['ghumantu'] ?? 0) }}
                                </td>

                                <td class="border-r border-slate-300 px-4 py-4 text-center">
                                    {{ number_format($totals['widow'] ?? 0) }}
                                </td>

                                <td class="px-4 py-4 text-center">
                                    {{ number_format($totals['others'] ?? 0) }}
                                </td>

                            </tr>

                        </tfoot>
                    @endif

                </table>

            </div>

        </section>

    </main>

@endsection
