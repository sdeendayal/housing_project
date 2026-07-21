@extends('layouts.mmgayAdmin')

@section('title', 'Registry Report')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

        {{-- Page Header --}}
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-800">
                        Registry Report
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        OwnerMaster और Registry के mobile matched records देखें।
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">

                    <div class="inline-flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 px-5 py-2.5">

                        <span class="material-symbols-outlined text-[22px] text-blue-700">
                            description
                        </span>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                Total Matched
                            </p>

                            <p class="text-lg font-bold text-blue-800">
                                {{ number_format($totalRegistrations ?? $registrations->total()) }}
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <form method="GET" action="{{ route('admin.registration') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">

                {{-- Search --}}
                <div class="md:col-span-2 xl:col-span-4">

                    <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Search Registry Record
                    </label>

                    <div class="relative">

                        <span
                            class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">
                            search
                        </span>

                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Owner name, mobile, registry number, token..."
                            class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100">

                    </div>

                </div>

                {{-- Apply --}}
                <div class="flex items-end">

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">

                        <span class="material-symbols-outlined text-[20px]">
                            filter_alt
                        </span>

                        Apply Filter
                    </button>

                </div>

                {{-- Reset --}}
                <div class="flex items-end">

                    <a href="{{ route('admin.registration') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">

                        <span class="material-symbols-outlined text-[20px]">
                            restart_alt
                        </span>

                        Reset
                    </a>

                </div>

            </form>

        </div>

        {{-- Search Summary --}}
        @if (request()->filled('search'))
            <div class="mb-6 rounded-2xl border border-orange-200 bg-orange-50 px-5 py-4">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-3">

                        <span
                            class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-orange-700">
                            manage_search
                        </span>

                        <div>
                            <p class="text-sm font-semibold text-orange-900">
                                Search result for:
                                <span class="font-bold">
                                    “{{ request('search') }}”
                                </span>
                            </p>

                            <p class="mt-0.5 text-xs text-orange-700">
                                {{ number_format($registrations->total()) }} matching records मिले।
                            </p>
                        </div>

                    </div>

                    <a href="{{ route('admin.registration') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-orange-700 hover:text-orange-900">

                        <span class="material-symbols-outlined text-[18px]">
                            close
                        </span>

                        Clear Search
                    </a>

                </div>

            </div>
        @endif

        {{-- Table Card --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Table Header --}}
            <div
                class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="text-lg font-bold text-slate-800">
                        Registry Records
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Showing {{ $registrations->firstItem() ?? 0 }}
                        to {{ $registrations->lastItem() ?? 0 }}
                        of {{ number_format($registrations->total()) }} records
                    </p>

                </div>

                <div class="flex items-center gap-2 text-xs text-slate-500">

                    <span class="material-symbols-outlined text-[18px]">
                        info
                    </span>

                    Duplicate mobile पर एक OwnerMaster record लिया गया है।

                </div>

            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Sr. No.
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Application
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Owner Details
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Registry Party
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Mobile
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Registry
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Token / Khewat
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Area
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Location
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Phase
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($registrations as $registration)
                            <tr class="transition hover:bg-slate-50">

                                {{-- Serial --}}
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">

                                    {{ ($registrations->firstItem() ?? 1) + $loop->index }}

                                </td>

                                {{-- Application --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->RegistrationNo ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Owner ID: {{ $registration->OwnerId ?? '-' }}
                                    </p>

                                </td>

                                {{-- Owner --}}
                                <td class="min-w-[230px] px-4 py-4">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->OwnerName ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $registration->FatherHusbandName ?? '-' }}
                                    </p>

                                    @if (!empty($registration->PPPId))
                                        <p class="mt-1 text-xs text-slate-400">
                                            PPP ID: {{ $registration->PPPId }}
                                        </p>
                                    @endif

                                </td>

                                {{-- Registry Party --}}
                                <td class="min-w-[220px] px-4 py-4">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->SecondParty ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Seller: {{ $registration->FirstParty ?? '-' }}
                                    </p>

                                </td>

                                {{-- Mobile --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    <div class="flex flex-col gap-1">

                                        <span
                                            class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-green-50 px-2.5 py-1 text-sm font-semibold text-green-700">

                                            <span class="material-symbols-outlined text-[16px]">
                                                call
                                            </span>

                                            {{ $registration->SecondPartyMobile ?? '-' }}

                                        </span>

                                        @if (!empty($registration->MobileNo) && $registration->MobileNo != $registration->SecondPartyMobile)
                                            <span class="text-xs text-slate-500">
                                                Owner: {{ $registration->MobileNo }}
                                            </span>
                                        @endif

                                    </div>

                                </td>

                                {{-- Registry --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->RegistaryNumber ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">

                                        @if (!empty($registration->RegistaryDate))
                                            {{ \Carbon\Carbon::parse($registration->RegistaryDate)->format('d-m-Y') }}
                                        @else
                                            -
                                        @endif

                                    </p>

                                </td>

                                {{-- Token/Khewat --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    <p class="text-sm font-medium text-slate-700">
                                        Token: {{ $registration->Token ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Khewat: {{ $registration->Khewat ?? '-' }}
                                    </p>

                                </td>

                                {{-- Area --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->TransferArea ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Total: {{ $registration->TotalArea ?? '-' }}
                                    </p>

                                    @if (!empty($registration->Bhag))
                                        <p class="mt-1 text-xs text-slate-400">
                                            Bhag: {{ $registration->Bhag }}
                                        </p>
                                    @endif

                                </td>

                                {{-- Location --}}
                                <td class="min-w-[230px] px-4 py-4">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $registration->Village ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $registration->TehsilName ?? '-' }},
                                        {{ $registration->District ?? '-' }}
                                    </p>

                                </td>

                                {{-- Phase --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    @if (!empty($registration->Phase))
                                        <span
                                            class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">

                                            {{ $registration->Phase }}

                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            -
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10" class="px-6 py-16 text-center">

                                    <div
                                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">

                                        <span class="material-symbols-outlined text-[34px] text-slate-400">
                                            folder_off
                                        </span>

                                    </div>

                                    <h3 class="mt-4 font-bold text-slate-700">
                                        No Registry Records Found
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Search filter बदलकर दोबारा प्रयास करें।
                                    </p>

                                    @if (request()->filled('search'))
                                        <a href="{{ route('admin.registration') }}"
                                            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-orange-700">

                                            <span class="material-symbols-outlined text-[18px]">
                                                restart_alt
                                            </span>

                                            Reset Search
                                        </a>
                                    @endif

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @if ($registrations->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">

                    {{ $registrations->onEachSide(1)->links('pagination::tailwind') }}

                </div>
            @endif

        </div>

    </main>

@endsection
