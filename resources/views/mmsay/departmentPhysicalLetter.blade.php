@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY - Physical Possession Letter')
@section('content')

    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">

            <!-- Breadcrumbs -->
            <nav class="mb-6 flex items-center gap-2 text-body-sm font-body-sm text-text-secondary">
                <span class="material-symbols-outlined text-[18px]">home</span>
                <a class="hover:text-primary transition-colors" href="{{ route('mmsay.dashboard') }}">Dashboard</a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="text-primary font-semibold">Physical Possession Letter</span>
            </nav>
            <hr>

            {{-- <div class="bg-surface-container-lowest rounded-xl border border-surface-border shadow-sm p-4 mb-6">
                <div class="flex items-center gap-3">
                    <button
                        class="status-filter-btn px-6 py-1.5 rounded-lg font-label-md text-label-md transition-all bg-blue-600 text-white border border-blue-600 shadow-sm"
                        onclick="filterTable('all', this)">All</button>
                    <button
                        class="status-filter-btn px-6 py-1.5 rounded-lg font-label-md text-label-md transition-all border border-amber-400 text-amber-500 hover:bg-amber-50"
                        onclick="filterTable('pending', this)">Pending</button>
                    <button
                        class="status-filter-btn px-6 py-1.5 rounded-lg font-label-md text-label-md transition-all border border-green-600 text-green-700 hover:bg-green-50"
                        onclick="filterTable('approved', this)">Approved</button>
                    <button
                        class="status-filter-btn px-6 py-1.5 rounded-lg font-label-md text-label-md transition-all border border-red-500 text-red-600 hover:bg-red-50"
                        onclick="filterTable('rejected', this)">Rejected</button>
                </div>
            </div> --}}
            <!-- Dashboard Bento Grid Content -->
            <div class="bg-surface-container-lowest rounded-xl border border-surface-border shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse" id="officers-table">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-surface-border">
                                <th>#</th>
                                <th>Applicant Name</th>
                                <th>Application No</th>
                                <th>District</th>
                                <th>Mobile</th>
                                <th>
                                    <Portal>Plot</Portal> Cost
                                </th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($letters as $key => $row)
                                <tr class="hover:bg-gray-50 transition duration-150">

                                    <td class="px-6 py-4 text-sm font-medium text-gray-700">
                                        {{ $key + 1 }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-800">
                                            {{ $row->applicant_name }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $row->application_number }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $row->district_name }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $row->mobile }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-green-700">
                                            ₹{{ number_format($row->flat_cost, 2) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($row->status == 'approved')
                                            <span
                                                class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                ✓ Approved
                                            </span>
                                        @elseif($row->status == 'rejected')
                                            <span
                                                class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                ✕ Rejected
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                                ⏳ Pending
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        {{-- <a href="#"
                                            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">

                                            <span class="material-symbols-outlined text-[18px]">
                                                visibility
                                            </span>

                                            View
                                        </a> --}}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center">

                                        <div class="flex flex-col items-center gap-2">

                                            <span class="material-symbols-outlined text-5xl text-gray-300">
                                                inbox
                                            </span>

                                            <p class="text-gray-500 font-medium">
                                                No Records Found
                                            </p>

                                        </div>

                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                    <!-- Empty State -->
                    <div class="hidden p-20 flex flex-col items-center justify-center text-center" id="empty-state">
                        <p class="text-body-lg font-body-lg text-text-secondary">No applications in this category.</p>
                    </div>
                </div>
                <!-- Pagination / Footer -->

            </div>


        </div>
    </main>

@endsection
